<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Supplier;
use DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplier = Supplier::all();
        return response()->json($supplier);
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
            'company_name' => 'required|unique:suppliers|max:255',
            'email' => 'required|unique:suppliers|max:255',
            'phone' => 'required|unique:suppliers',
            'address' => 'required|unique:suppliers',
            'vat_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $supplier = new Supplier();
        $supplier->company_name = $request->company_name;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->contact_person = $request->contact_person;
        $supplier->vat_number = $request->vat_number;
        $supplier->notes = $request->notes;
        $supplier->save();

        return response()->json([
            'message' => 'Supplier added successfully'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier = DB::table('suppliers')->where('id', $id)->first();
        return response()->json($supplier);
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
        $data['company_name'] = $request->company_name;
        $data['phone'] = $request->phone;
        $data['email'] = $request->email;
        $data['address'] = $request->address;
        $data['contact_person'] = $request->contact_person;
        $data['vat_number'] = $request->vat_number;
        $data['notes'] = $request->notes;

        $supplier = DB::table('suppliers')->where('id', $id)->update($data);
        return response()->json([
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('suppliers')->where('id', $id)->delete();
    }
}
