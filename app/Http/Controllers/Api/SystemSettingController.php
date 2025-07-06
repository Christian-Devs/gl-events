<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SystemSetting;

class SystemSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SystemSetting::first(); // or `settings()` helper
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $setting = SystemSetting::findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'currency' => 'nullable|string|max:10',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'footer_note' => 'nullable|string|max:1000',
            'logo' => 'nullable|string', // base64 or file path
        ]);

        // Handle base64 logo upload
        if ($request->logo && str_starts_with($request->logo, 'data:image')) {
            $image = $request->logo;
            $extension = explode('/', mime_content_type($image))[1];
            $name = 'logo_' . time() . '.' . $extension;
            $path = 'uploads/settings/' . $name;

            // Convert base64 to file
            $img = \Intervention\Image\ImageManagerStatic::make($image)->resize(240, 100);
            $img->save(public_path($path));

            // Delete old logo if exists
            if ($setting->logo && file_exists(public_path($setting->logo))) {
                unlink(public_path($setting->logo));
            }

            $validated['logo'] = $path;
        }

        $setting->update($validated);

        return response()->json([
            'message' => 'Settings updated successfully',
            'settings' => $setting,
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
        //
    }
}
