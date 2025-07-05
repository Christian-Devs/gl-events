<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = Employee::all();
        return response()->json($employee);
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
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|unique:employees',
                'joining_date' => 'required|date',
                'nid' => 'nullable|string',
                'role_id' => 'required|exists:roles,id',
            ]);

            // Create the User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('defaultPass'),
                'role_id' => $validated['role_id'],
            ]);

            // Handle image if provided
            $imagePath = null;
            if ($request->photo) {
                $position = strpos($request->photo, ';');
                $sub = substr($request->photo, 0, $position);
                $ext = explode('/', $sub)[1];
                $name = time() . '.' . $ext;
                $img = Image::make($request->photo)->resize(240, 200);
                $upload_path = 'backend/employee/';
                $imagePath = $upload_path . $name;
                $img->save($imagePath);
            }

            // Create the Employee
            $employee = Employee::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'nid' => $request->nid,
                'joining_date' => $validated['joining_date'],
                'photo' => $imagePath,
                'role_id' => $validated['role_id'],
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Employee created successfully',
                'employee' => $employee,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Employee/User Creation Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Server error. Could not create employee.',
            ], 500);
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
        $employee = DB::table('employees')->where('id', $id)->first();
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
        $data = array();
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['nid'] = $request->nid;
        $data['joining_date'] = $request->joining_date;
        $image = $request->newphoto;

        if ($image) {
            $position = strpos($image, ';');
            $sub = substr($image, 0, $position);
            $ext = explode('/', $sub)[1];

            $name = time() . '.' . $ext;
            $img = Image::make($image)->resize(240, 200);
            $upload_path = 'backend/employee/';
            $image_url = $upload_path . $name;
            $success = $img->save($image_url);

            if ($success) {
                $data['photo'] = $image_url;
                $img = DB::table('employees')->where('id', $id)->first();
                $image_path = $img->photo;
                $done = unlink($image_path);
                $user = DB::table('employees')->where('id', $id)->update($data);
            }
        } else {
            $oldphoto = $request->photo;
            $data['photo'] = $oldphoto;
            $user = DB::table('employees')->where('id', $id)->update($data);
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
        $employee = DB::table('employees')->where('id', $id)->first();
        $photo = $employee->photo;
        if ($photo) {
            unlink($photo);
            DB::table('employees')->where('id', $id)->delete();
        } else {
            DB::table('employees')->where('id', $id)->delete();
        }
    }
}
