<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\User;
use App\Role;
use App\Services\Payroll\EmployeeMapper;
use App\Services\SimplePayClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('per_page', 15));

        $employees = Employee::query()
            ->with(['role:id,name', 'user:id,name,email,role_id'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->input('search');
                $q->where(function ($qq) use ($s) {
                    $qq->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$s}%"])
                        ->orWhere('email', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%");
                });
            })
            ->when($request->filled('role_id'), fn($q) => $q->where('role_id', $request->integer('role_id')))
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        try {
            $validated = $request->validate([
                'first_name'            => ['required', 'string', 'max:255'],
                'last_name'             => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('employees', 'email'),
                    Rule::unique('users', 'email'),
                ],
                'phone'                 => ['required', 'string', 'max:50', Rule::unique('employees', 'phone')],
                'id_number'             => ['nullable', 'string', 'max:255'],
                'birthdate'             => ['nullable', 'date'],
                'start_date'            => ['required', 'date'],
                'pay_frequency'         => ['required', Rule::in(['monthly', 'fortnightly', 'weekly'])],

                // IMPORTANT: use SimplePay tokens (cash|cheque|eft_manual)
                'payment_method'        => ['required', Rule::in(['cash', 'cheque', 'eft_manual'])],

                'status'                => ['required', Rule::in(['active', 'terminated'])],
                'simplepay_employee_id' => ['nullable', 'string', 'max:255'],
                'external_reference'    => ['nullable', 'string', 'max:255'],
                'role_id'               => ['required', Rule::exists('roles', 'id')],

                // Bank fields (required only if eft_manual)
                'bank_id'                 => ['nullable', 'integer', 'required_if:payment_method,eft_manual'],
                'bank_account_number'     => ['nullable', 'string', 'min:4', 'required_if:payment_method,eft_manual'],
                'bank_branch_code'        => ['nullable', 'string', 'size:6', 'required_if:payment_method,eft_manual'],
                'bank_account_type'       => ['nullable', 'string', 'in:1,2,3,4,6'],
                'bank_holder_relationship' => ['nullable', 'string', 'in:1,2,3'],
                'bank_holder_name'        => ['nullable', 'string', 'max:255'],
            ]);

            $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

            return DB::transaction(function () use ($validated, $request, $fullName) {

                $user = User::create([
                    'name'     => $fullName,
                    'email'    => $validated['email'],
                    'password' => Hash::make('defaultPass'),
                    'role_id'  => $validated['role_id'],
                ]);

                $employee = Employee::create([
                    'first_name'            => $validated['first_name'],
                    'last_name'             => $validated['last_name'],
                    'email'                 => $validated['email'],
                    'phone'                 => $validated['phone'],
                    'id_number'             => $validated['id_number'] ?? null,
                    'birthdate'             => $validated['birthdate'] ?? null,
                    'start_date'            => $validated['start_date'], // <-- matches your DB
                    'pay_frequency'         => $validated['pay_frequency'],
                    'payment_method'        => $validated['payment_method'],
                    'status'                => $validated['status'],
                    'simplepay_employee_id' => $validated['simplepay_employee_id'] ?? null,
                    'external_reference'    => $validated['external_reference'] ?? null,
                    'role_id'               => $validated['role_id'],
                    'user_id'               => $user->id,
                    'bank_id'                 => $validated['bank_id'] ?? null,
                    'bank_account_type'       => $validated['bank_account_type'] ?? null,
                    'bank_account_number'     => $validated['bank_account_number'] ?? null,
                    'bank_branch_code'        => $validated['bank_branch_code'] ?? null,
                    'bank_holder_relationship' => $validated['bank_holder_relationship'] ?? null,
                    'bank_holder_name'        => $validated['bank_holder_name'] ?? null,
                ]);

                return response()->json([
                    'message'  => 'Employee created successfully',
                    'employee' => $employee->load(['role:id,name', 'user:id,name,email,role_id']),
                ], 201);
            });
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['message' => 'Validation failed', 'errors' => $ve->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Employee/User Creation Failed', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Server error. Could not create employee.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if ($id === 'me') {
            return $this->me($request);
        }

        $employee = Employee::with(['role:id,name', 'user:id,name,email,role_id'])->findOrFail($id);
        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, SimplePayClient $sp)
    {
        try {
            $employee = Employee::with('user')->findOrFail($id);

            $validated = $request->validate([
                'first_name'            => ['required', 'string', 'max:255'],
                'last_name'             => ['required', 'string', 'max:255'],
                'email'                 => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('employees', 'email')->ignore($employee->id),
                    Rule::unique('users', 'email')->ignore($employee->user_id),
                ],
                'phone'                 => ['required', 'string', 'max:50', Rule::unique('employees', 'phone')->ignore($employee->id)],
                'id_number'             => ['nullable', 'string', 'max:255'],
                'birthdate'             => ['nullable', 'date'],
                'start_date'            => ['required', 'date'],
                'pay_frequency'         => ['required', Rule::in(['monthly', 'fortnightly', 'weekly'])],
                'payment_method'        => ['required', Rule::in(['cash', 'cheque', 'eft_manual'])],
                'status'                => ['required', Rule::in(['active', 'terminated'])],
                'simplepay_employee_id' => ['nullable', 'string', 'max:255'],
                'external_reference'    => ['nullable', 'string', 'max:255'],
                'role_id'               => ['required', Rule::exists('roles', 'id')],
                'user_id'               => ['nullable', Rule::exists('users', 'id')],

                // bank fields if eft_manual
                'bank_id'                  => ['nullable', 'integer', 'required_if:payment_method,eft_manual'],
                'bank_account_number'      => ['nullable', 'string', 'min:4', 'required_if:payment_method,eft_manual'],
                'bank_branch_code'         => ['nullable', 'string', 'size:6', 'required_if:payment_method,eft_manual'],
                'bank_account_type'        => ['nullable', 'string', 'in:1,2,3,4,6'],
                'bank_holder_relationship' => ['nullable', 'string', 'in:1,2,3'],
                'bank_holder_name'         => ['nullable', 'string', 'max:255'],
            ]);

            $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

            // 1) Save locally (no external API inside the DB transaction)
            DB::transaction(function () use ($validated, $employee, $fullName) {
                if ($employee->user) {
                    $employee->user->update([
                        'name'    => $fullName,
                        'email'   => $validated['email'],
                        'role_id' => $validated['role_id'],
                    ]);
                } else {
                    $user = User::create([
                        'name'     => $fullName,
                        'email'    => $validated['email'],
                        'password' => Hash::make('defaultPass'),
                        'role_id'  => $validated['role_id'],
                    ]);
                    $employee->user_id = $user->id;
                }

                $employee->fill([
                    'first_name'            => $validated['first_name'],
                    'last_name'             => $validated['last_name'],
                    'email'                 => $validated['email'],
                    'phone'                 => $validated['phone'],
                    'id_number'             => $validated['id_number'] ?? null,
                    'birthdate'             => $validated['birthdate'] ?? null,
                    'start_date'            => $validated['start_date'],
                    'pay_frequency'         => $validated['pay_frequency'],
                    'payment_method'        => $validated['payment_method'],
                    'status'                => $validated['status'],
                    'simplepay_employee_id' => $validated['simplepay_employee_id'] ?? $employee->simplepay_employee_id,
                    'external_reference'    => $validated['external_reference'] ?? $employee->external_reference,
                    'role_id'               => $validated['role_id'],

                    'bank_id'                  => $validated['bank_id'] ?? null,
                    'bank_account_type'        => $validated['bank_account_type'] ?? null,
                    'bank_account_number'      => $validated['bank_account_number'] ?? null,
                    'bank_branch_code'         => $validated['bank_branch_code'] ?? null,
                    'bank_holder_relationship' => $validated['bank_holder_relationship'] ?? null,
                    'bank_holder_name'         => $validated['bank_holder_name'] ?? null,
                ])->save();
            });

            // 2) After local commit, try to sync to SimplePay
            $employee->refresh(); // get latest values
            $simplepay = ['synced' => false];

            try {
                $payload = EmployeeMapper::toSimplePay($employee);

                if ($employee->simplepay_employee_id) {
                    // update on SimplePay
                    $resp = $sp->updateEmployee((int) $employee->simplepay_employee_id, $payload);
                    $simplepay = ['synced' => true, 'action' => 'update', 'response' => $resp];
                } else {
                    // create on SimplePay
                    $clientId = (int) $sp->getPrimaryClientId();
                    $resp = $sp->createEmployee($clientId, $payload);

                    $spId = (int) (data_get($resp, 'employee.id') ?: data_get($resp, 'id'));
                    if ($spId) {
                        $employee->simplepay_employee_id = $spId;
                        $employee->save();
                    }
                    $simplepay = ['synced' => (bool) $spId, 'action' => 'create', 'response' => $resp];
                }
            } catch (\Throwable $se) {
                Log::warning('SimplePay auto-sync failed', [
                    'employee_id' => $employee->id,
                    'message'     => $se->getMessage(),
                ]);
                $simplepay = ['synced' => false, 'error' => $se->getMessage()];
                // We don't fail the whole update—local save already succeeded.
            }

            return response()->json([
                'message'   => 'Employee updated successfully',
                'employee'  => $employee->load(['role:id,name', 'user:id,name,email,role_id']),
                'simplepay' => $simplepay,
            ]);
        } catch (ValidationException $ve) {
            return response()->json(['message' => 'Validation failed', 'errors' => $ve->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Employee Update Failed', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Server error. Could not update employee.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete(); // will soft-delete if trait is used
            return response()->json(['message' => 'Employee deleted successfully']);
        } catch (\Throwable $e) {
            Log::error('Employee Delete Failed', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Server error. Could not delete employee.'], 500);
        }
    }

    public function self(Request $request)
    {
        $user = $request->user(); // requires auth:api (JWT) to be working
        $employee = Employee::with(['role:id,name', 'user:id,name,email,role_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json($employee);
    }
}
