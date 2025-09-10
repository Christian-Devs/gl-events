<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\Services\SimplePayClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayslipController extends Controller
{
    /**
     * GET /api/payroll/payslip
     * Query/body:
     *  - employee_id            (local DB id)         XOR
     *  - simplepay_employee_id  (external id)
     *  - client_id?             (optional; defaults via API)
     */
    public function show(Request $request, SimplePayClient $sp)
    {
        // Accept either local or external id
        $data = $request->validate([
            'employee_id'           => ['nullable', 'integer'],
            'simplepay_employee_id' => ['nullable', 'integer'],
            'client_id'             => ['nullable', 'integer'],
        ]);

        if (empty($data['employee_id']) && empty($data['simplepay_employee_id'])) {
            return response()->json([
                'ok' => false,
                'error' => 'Provide either employee_id (local) or simplepay_employee_id (external).'
            ], 422);
        }

        // Resolve SimplePay employee id
        $spEmployeeId = isset($data['simplepay_employee_id']) ? (int) $data['simplepay_employee_id'] : null;
        if (!$spEmployeeId) {
            $emp = Employee::find($data['employee_id']);
            if (!$emp) {
                return response()->json(['ok' => false, 'error' => 'Employee not found'], 404);
            }
            if (!$emp->simplepay_employee_id) {
                return response()->json(['ok' => false, 'error' => 'Employee not linked to SimplePay'], 422);
            }
            $spEmployeeId = (int) $emp->simplepay_employee_id;
        }

        $clientId = isset($data['client_id']) ? (int) $data['client_id'] : (int) $sp->getPrimaryClientId();

        // 1) latest run
        $runId = $sp->getLatestRunId($clientId);
        if (!$runId) {
            return response()->json(['ok' => false, 'error' => 'No payment runs found'], 404);
        }

        // 2) employee’s payslip in that run
        $payslipId = $sp->findPayslipIdForEmployee($runId, $spEmployeeId);
        if (!$payslipId) {
            return response()->json(['ok' => false, 'error' => 'Payslip not found for employee in latest run'], 404);
        }

        // 3) full payslip
        try {
            $full = $sp->getPayslip($payslipId);
        } catch (\Throwable $e) {
            Log::warning('Payslip fetch failed', ['payslip_id' => $payslipId, 'err' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'Failed to fetch payslip', 'details' => $e->getMessage()], 422);
        }

        // Normalize income/deductions like [["Tax (PAYE)", 2500], ...]
        $incomeRows = (array) data_get($full, 'payslip.data.income', []);
        $deductRows = (array) data_get($full, 'payslip.data.deduction', []);

        $income = [];
        foreach ($incomeRows as $row) {
            if (is_array($row) && count($row) >= 2) {
                $income[(string) $row[0]] = (float) $row[1];
            }
        }

        $deductions = [];
        foreach ($deductRows as $row) {
            if (is_array($row) && count($row) >= 2) {
                $deductions[(string) $row[0]] = (float) $row[1];
            }
        }

        $gross = 0.0;
        foreach ($income as $v) $gross += (float) $v;

        // Net total: prefer explicit key, fallback to any numeric in 'grand_total'
        $net = (float) data_get($full, 'payslip.data.net_pay', 0);
        if (!$net) {
            $grand = (array) data_get($full, 'payslip.data.grand_total', []);
            // Look for first numeric in nested arrays
            foreach ($grand as $item) {
                if (is_array($item)) {
                    foreach ($item as $val) {
                        if (is_numeric($val)) {
                            $net = (float) $val;
                            break 2;
                        }
                    }
                } elseif (is_numeric($item)) {
                    $net = (float) $item;
                    break;
                }
            }
        }

        return response()->json([
            'ok'         => true,
            'payslip_id' => $payslipId,
            'period'     => (string) data_get($full, 'payslip.date'),
            'gross'      => $gross,
            'net'        => $net,
            'income'     => $income,
            'deductions' => $deductions,
            'raw'        => $full, // optional; remove if you don’t want to expose
        ]);
    }

    public function list(Request $request, SimplePayClient $sp)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'client_id'   => ['nullable', 'integer'],
            'max_runs'    => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $clientId   = $request->filled('client_id') ? (int) $data['client_id'] : (int) $sp->getPrimaryClientId();
        $employeeId = (int) $data['employee_id'];
        $maxRuns    = (int) ($data['max_runs'] ?? 6);

        try {
            $items = $sp->listRecentPayslipsForEmployee($clientId, $employeeId, $maxRuns);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Failed to list payslips', 'details' => $e->getMessage()], 422);
        }

        return response()->json($items);
    }

    /**
     * Streams the official PDF payslip.
     * Route: GET /api/payroll/payslip/{id}/pdf
     * {id} is the SimplePay payslip id (not local employee id)
     */
    public function pdf(SimplePayClient $sp, $id)
    {
        try {
            $pdf = $sp->getPayslipPdf((int) $id);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Failed to fetch payslip PDF', 'details' => $e->getMessage()], 422);
        }

        return new StreamedResponse(function () use ($pdf) {
            echo $pdf;
        }, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=payslip-{$id}.pdf",
        ]);
    }
}
