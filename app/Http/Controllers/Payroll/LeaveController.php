<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\Services\SimplePayClient;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function types(Request $request, SimplePayClient $sp)
    {
        $clientId = $request->filled('client_id')
            ? (int) $request->input('client_id')
            : (int) $sp->getPrimaryClientId();

        try {
            $rows = $sp->listLeaveTypes($clientId);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Failed to load leave types', 'details' => $e->getMessage()], 422);
        }

        // Normalize to {id, name}
        $items = [];
        foreach ($rows as $row) {
            $id   = (int) data_get($row, 'leave_type.id', data_get($row, 'id'));
            $name = (string) data_get($row, 'leave_type.name', data_get($row, 'name'));
            if ($id && $name) $items[] = ['id' => $id, 'name' => $name];
        }

        return response()->json($items);
    }

    public function index(Request $request, SimplePayClient $sp)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        if (!$employee->simplepay_employee_id) {
            return response()->json([], 200); // nothing on SP yet
        }

        try {
            $rows = $sp->listEmployeeLeave((int) $employee->simplepay_employee_id);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Failed to load leave applications', 'details' => $e->getMessage()], 422);
        }

        // Normalize for the UI table
        $items = [];
        foreach ($rows as $row) {
            $lr = data_get($row, 'leave_application', $row);
            $items[] = [
                'id'          => (int) data_get($lr, 'id'),
                'type_name'   => (string) data_get($lr, 'leave_type.name'),
                'leave_type_id' => (int) data_get($lr, 'leave_type_id'),
                'start_date'  => (string) data_get($lr, 'start_date'),
                'end_date'    => (string) data_get($lr, 'end_date'),
                'units'       => (float) data_get($lr, 'units', 0),
                'status'      => (string) (data_get($lr, 'status') ?: 'pending'),
                'reason'      => (string) data_get($lr, 'reason', ''),
            ];
        }

        return response()->json($items);
    }

    /** POST /api/payroll/leave-applications  */
    public function store(Request $request, SimplePayClient $sp)
    {
        $data = $request->validate([
            'employee_id'   => ['required', 'integer', 'exists:employees,id'],
            'leave_type_id' => ['required', 'integer'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            'units'         => ['nullable', 'numeric', 'min:0'],
            'reason'        => ['nullable', 'string', 'max:1000'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        if (!$employee->simplepay_employee_id) {
            return response()->json(['ok' => false, 'error' => 'Employee not linked to SimplePay'], 422);
        }

        $payload = [
            'leave_type_id' => (int) $data['leave_type_id'],
            'start_date'    => (string) $data['start_date'],
            'end_date'      => (string) $data['end_date'],
            // SimplePay usually treats units as optional – if omitted it derives from dates
            // Include only if provided:
        ];
        if (array_key_exists('units', $data) && $data['units'] !== null) {
            $payload['units'] = (float) $data['units'];
        }
        if (!empty($data['reason'])) {
            $payload['reason'] = (string) $data['reason'];
        }

        try {
            $resp = $sp->createEmployeeLeave((int) $employee->simplepay_employee_id, $payload);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Failed to submit leave',
                'details' => $e->getMessage(),
            ], 422);
        }

        // Return normalized response
        $lr = data_get($resp, 'leave_application', $resp);
        return response()->json([
            'ok' => true,
            'leave_application' => [
                'id'          => (int) data_get($lr, 'id'),
                'leave_type_id' => (int) data_get($lr, 'leave_type_id'),
                'start_date'  => (string) data_get($lr, 'start_date'),
                'end_date'    => (string) data_get($lr, 'end_date'),
                'units'       => (float) data_get($lr, 'units', 0),
                'status'      => (string) (data_get($lr, 'status') ?: 'pending'),
                'reason'      => (string) data_get($lr, 'reason', ''),
            ]
        ]);
    }
}
