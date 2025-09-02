<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\Services\Payroll\EmployeeMapper;
use App\Services\SimplePayClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeSyncController extends Controller
{
    /**
     * POST /api/payroll/simplepay/sync-one
     * Body: { employee_id: int, client_id?: int }
     */
    public function syncOne(Request $request, SimplePayClient $sp)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'client_id'   => ['nullable', 'integer'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $clientId = isset($data['client_id'])
            ? (int) $data['client_id']
            : (int) $sp->getPrimaryClientId();

        $payload = EmployeeMapper::toSimplePay($employee);

        // SimplePay "create" usually requires these
        $required = ['wave_id', 'first_name', 'last_name', 'birthdate', 'appointment_date', 'identification_type', 'id_number', 'payment_method'];
        $missing = [];
        foreach ($required as $key) {
            if (!array_key_exists($key, $payload) || $payload[$key] === null || $payload[$key] === '') {
                $missing[] = $key;
            }
        }
        if ($missing) {
            return response()->json([
                'ok' => false,
                'error' => 'Missing required fields for SimplePay employee create',
                'fields' => $missing,
            ], 422);
        }

        try {
            $resp = null;

            DB::transaction(function () use (&$resp, $employee, $sp, $clientId, $payload) {
                if ($employee->simplepay_employee_id) {
                    // Update on SimplePay
                    $resp = $sp->updateEmployee((int) $employee->simplepay_employee_id, $payload);
                } else {
                    // Create on SimplePay
                    $resp = $sp->createEmployee($clientId, $payload);

                    // Accept either { employee: { id, external_reference, ... } } or flat { id, ... }
                    $spId = (int) (data_get($resp, 'employee.id') ?: data_get($resp, 'id'));
                    if ($spId) {
                        $employee->simplepay_employee_id = $spId;
                    }
                    $extRef = data_get($resp, 'employee.external_reference', data_get($resp, 'external_reference'));
                    if ($extRef && !$employee->external_reference) {
                        $employee->external_reference = (string) $extRef;
                    }

                    $employee->save();
                }
            });

            return response()->json([
                'ok'                     => true,
                'message'                => $employee->simplepay_employee_id ? 'Synced with SimplePay' : 'Linked to SimplePay',
                'simplepay_employee_id'  => (int) $employee->simplepay_employee_id,
                'simplepay_response'     => $resp,
            ]);
        } catch (\Throwable $e) {
            Log::error('SimplePay sync failed', [
                'employee_id' => $employee->id,
                'message'     => $e->getMessage(),
            ]);

            return response()->json([
                'ok'     => false,
                'error'  => 'SimplePay sync failed',
                'details' => $e->getMessage(),
            ], 422);
        }
    }
}
