<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Jobcard;
use Illuminate\Http\Request;
use DB;

class JobcardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobcards = Jobcard::with('quote')->latest()->get();
        return response()->json($jobcards);
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
            'quote_id' => 'required|exists:quotes,id',
            'assigned_to' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $jobcard = Jobcard::create($validated);

        return response()->json([
            'message' => 'Job card created successfully',
            'jobcard' => $jobcard->load('quote'),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jobcard = Jobcard::with('quote')->findOrFail($id);
        return response()->json($jobcard);
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
        $jobcard = Jobcard::findOrFail($id);

        $validated = $request->validate([
            'assigned_to' => 'nullable|string|max:255',
            'status' => 'required|in:open,in-progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $jobcard->update($validated);

        return response()->json([
            'message' => 'Job card updated successfully',
            'jobcard' => $jobcard->fresh('quote'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('jobcards')->where('id', $id)->delete();
    }
}
