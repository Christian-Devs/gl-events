<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Salary;
use App\Model\Employee;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            Salary::with('employee')->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);

        // Calculate net salary
        $net = $validated['basic_salary'] + ($validated['bonus'] ?? 0) - ($validated['deductions'] ?? 0);
        $validated['net_salary'] = $net;

        $salary = Salary::create($validated);

        return response()->json(['message' => 'Salary record created', 'salary' => $salary], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(
            Salary::with('employee')->findOrFail($id)
        );
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
        $salary = Salary::findOrFail($id);

        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);

        $net = $validated['basic_salary'] + ($validated['bonus'] ?? 0) - ($validated['deductions'] ?? 0);
        $validated['net_salary'] = $net;

        $salary->update($validated);

        return response()->json(['message' => 'Salary record updated', 'salary' => $salary]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Salary::findOrFail($id)->delete();
        return response()->json(['message' => 'Salary deleted']);
    }
}
