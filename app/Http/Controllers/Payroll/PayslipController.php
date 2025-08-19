<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Services\SimplePayClient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayslipController extends Controller
{
    public function show(Request $request, SimplePayClient $sp)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer'],
            'client_id' => ['nullable', 'integer'],
        ]);

        $clientId = isset($data['client_id']) ? (int) $data['client_id'] : (int) $sp->getPrimaryClientId();
        $employeeId = (int) $data['employee_id'];

        // 1) latest run
        $runId = $sp->getLatestRunId($clientId);
        if (!$runId) {
            return response()->json(['ok' => false, 'error' => 'No payment runs found'], 404);
        }

        // 2) employee’s payslip in that run
        $payslipId = $sp->findPayslipIdForEmployee($runId, $employeeId);
        if (!$payslipId) {
            return response()->json(['ok' => false, 'error' => 'Payslip not found for employee in latest run'], 404);
        }

        // 3) full payslip
        try {
            $full = $sp->getPayslip($payslipId);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Failed to fetch payslip', 'details' => $e->getMessage()], 422);
        }

        // Normalize arrays like [["Tax (PAYE)", 2500], ...] into key=>value
        $deductions = [];
        foreach ((array) data_get($full, 'payslip.data.deduction', []) as $row) {
            if (is_array($row) && count($row) >= 2) {
                $deductions[(string) $row[0]] = (float) $row[1];
            }
        }
        $income = [];
        foreach ((array) data_get($full, 'payslip.data.income', []) as $row) {
            if (is_array($row) && count($row) >= 2) {
                $income[(string) $row[0]] = (float) $row[1];
            }
        }

        return response()->json([
            'ok' => true,
            'payslip_id' => $payslipId,
            'period' => (string) data_get($full, 'payslip.date'),
            'gross' => array_sum(array_values($income)),
            'net' => (float) data_get($full, 'payslip.data.grand_total.0.1'),
            'deductions' => $deductions, // e.g. "Tax (PAYE)", "UIF - Employee"
            'income' => $income,
        ]);
    }

    /**
     * Streams the official PDF payslip.
     * Route: /api/payroll/payslip/{id}/pdf
     */
    public function pdf(SimplePayClient $sp, int $id)
    {
        try {
            $pdf = $sp->getPayslipPdf($id);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Failed to fetch payslip PDF', 'details' => $e->getMessage()], 422);
        }

        return new StreamedResponse(function () use ($pdf) {
            echo $pdf;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=payslip-{$id}.pdf",
        ]);
    }
}
