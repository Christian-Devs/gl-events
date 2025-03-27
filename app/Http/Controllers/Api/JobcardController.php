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
        $jobcard = Jobcard::all();
        return response()->json($jobcard);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'job_number' => 'required|max:255',
            'salesperson' => 'required|max:255',
            'stand_name' => 'required',
            'show_name' => 'required',
            'materials' => 'required|array',
            'total_amount' => 'required|numeric'
        ]);

        $jobcard = new Jobcard();
        $jobcard->job_number = $request->job_number;
        $jobcard->salesperson = $request->salesperson;
        $jobcard->stand_name = $request->stand_name;
        $jobcard->show_name = $request->show_name;
        $jobcard->materials = json_encode($request->materials);
        $jobcard->total_amount = $request->total_amount;
        $jobcard->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jobcard = DB::table('jobcards')->where('id', $id)->first();
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
        $data = array();
        $data['job_number'] = $request->job_number;
        $data['salesperson'] = $request->salesperson;
        $data['stand_name'] = $request->stand_name;
        $data['show_name'] = $request->show_name;
        $data['materials'] = $request->materials;
        $data['total_amount'] = $request->total_amount;

        $user = DB::table('jobcards')->where('id', $id)->update($data);
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
