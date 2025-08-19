<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\Services\Payroll\EmployeeMapper;
use App\Services\SimplePayClient;
use Illuminate\Http\Request;

class EmployeeSyncController extends Controller
{
    public function syncOne(Request $request, SimplePayClient $sp)
    {

        // Basic validation (adjust table name if different)
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'client_id' => ['nullable', 'integer'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $clientId = isset($data['client_id']) ? (int) $data['client_id'] : (int) $sp->getPrimaryClientId();

        $payload = EmployeeMapper::toSimplePay($employee);

        $missing = [];
        foreach (['first_name', 'last_name', 'id_number', 'birthdate', 'appointment_date', 'payment_method', 'wave'] as $req) {
            if (!array_key_exists($req, $payload) || $payload[$req] === null || $payload[$req] === '') {
                $missing[] = $req;
            }
        }
        if ($missing) {
            return response()->json([
                'ok' => false,
                'error' => 'Missing required fields for SimplePay employee create',
                'details' => $missing,
            ], 422);
        }

        try {
            if ($employee->simplepay_employee_id) {
                $resp = $sp->updateEmployee($employee->simplepay_employee_id, $payload);
            } else {
                $resp = $sp->createEmployee($clientId, $payload);
                $employee->simplepay_employee_id = (int) data_get($resp, 'employee.id');
                $employee->save();
            }
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'SimplePay sync failed',
                'details' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'simplepay_employee_id' => $employee->simplepay_employee_id,
            'simplepay_response' => $resp,
        ]);
    }
}
