<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

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
                    $qq->where('name', 'like', "%{$s}%")
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
        try {
            $validated = $request->validate([
                'first_name'            => ['required', 'string', 'max:255'],
                'last_name'             => ['required', 'string', 'max:255'],
                'email'                 => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('employees', 'email'),
                    Rule::unique('users', 'email')->ignore($request->integer('user_id')),
                ],
                'phone'                 => ['required', 'string', 'max:50', Rule::unique('employees', 'phone')],
                'id_number'             => ['nullable', 'string', 'max:255'],
                'birthdate'             => ['nullable', 'date'],
                'start_date'            => ['required', 'date'],
                'pay_frequency'         => ['required', Rule::in(['monthly', 'fortnightly', 'weekly'])],
                'payment_method'        => ['required', Rule::in(['bank', 'cash'])],
                'status'                => ['required', Rule::in(['active', 'terminated'])],
                'simplepay_employee_id' => ['nullable', 'string', 'max:255'],
                'external_reference'    => ['nullable', 'string', 'max:255'],
                'role_id'               => ['required', Rule::exists('roles', 'id')],
                'user_id'               => ['nullable', Rule::exists('users', 'id')],
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
    public function show($id)
    {
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
    public function update(Request $request, $id)
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
                'phone'                 => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('employees', 'phone')->ignore($employee->id),
                ],
                'id_number'             => ['nullable', 'string', 'max:255'],
                'birthdate'             => ['nullable', 'date'],
                'start_date'            => ['required', 'date'],
                'pay_frequency'         => ['required', Rule::in(['monthly', 'fortnightly', 'weekly'])],
                'payment_method'        => ['required', Rule::in(['bank', 'cash'])],
                'status'                => ['required', Rule::in(['active', 'terminated'])],
                'simplepay_employee_id' => ['nullable', 'string', 'max:255'],
                'external_reference'    => ['nullable', 'string', 'max:255'],
                'role_id'               => ['required', Rule::exists('roles', 'id')],
                'user_id'               => ['nullable', Rule::exists('users', 'id')], // allow relink
            ]);

            $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

            return DB::transaction(function () use ($validated, $employee, $fullName) {
                // Sync user record (if present)
                if ($employee->user) {
                    $employee->user->update([
                        'name'    => $fullName,
                        'email'   => $validated['email'],
                        'role_id' => $validated['role_id'],
                    ]);
                } else {
                    // Edge case: employee had no user; create one
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
                    'start_date'            => $validated['start_date'], // <-- matches your DB
                    'pay_frequency'         => $validated['pay_frequency'],
                    'payment_method'        => $validated['payment_method'],
                    'status'                => $validated['status'],
                    'simplepay_employee_id' => $validated['simplepay_employee_id'] ?? null,
                    'external_reference'    => $validated['external_reference'] ?? null,
                    'role_id'               => $validated['role_id'],
                ])->save();

                return response()->json([
                    'message'  => 'Employee updated successfully',
                    'employee' => $employee->load(['role:id,name', 'user:id,name,email,role_id']),
                ]);
            });
        } catch (ValidationException $ve) {
            return response()->json(['message' => 'Validation failed', 'errors' => $ve->errors()], 422);
        } catch (Throwable $e) {
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
}
