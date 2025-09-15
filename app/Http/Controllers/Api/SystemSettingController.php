<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SystemSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = SystemSetting::all()->mapWithKeys(fn($row) => [
            $row->key => $row->is_secret ? null : $row->value
        ]);

        return response()->json([
            'company' => [
                'company_name' => $settings['company_name'] ?? '',
                'company_logo_url' => $settings['logo'] ?? '',
                'tax_rate' => $settings['tax_rate'] ?? '',
            ],
            'simplepay' => [
                'base' => $settings['simplepay_base'] ?? '',
                'key' => null,
                'client_id' => $settings['simplepay_client_id'] ?? '',
                'wave_monthly' => $settings['simplepay_wave_monthly'] ?? '',
                'wave_weekly' => $settings['simplepay_wave_weekly'] ?? '',
            ],
            'email' => [
                'host' => $settings['email_host'] ?? '',
                'port' => $settings['email_port'] ?? '',
                'username' => $settings['email_username'] ?? '',
                'password' => null,
                'from' => $settings['email_from'] ?? '',
            ],
            'numbering' => [
                'invoice_prefix' => $settings['invoice_prefix'] ?? 'INV',
                'quote_prefix' => $settings['quote_prefix'] ?? 'QT',
            ],
        ]);
    }

    public function public()
    {
        return response()->json([
            'company_name' => setting('company_name', config('app.name')),
            'company_logo_url' => setting('company_logo_url'),
        ]);
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
        $data = $request->validate([
            // company
            'company.company_name'     => 'required|string|max:255',
            'company.company_logo_url' => 'nullable|url|max:2000',
            'company.tax_rate'         => 'nullable|numeric|min:0|max:100',

            // simplepay
            'simplepay.base'        => 'required|url',
            'simplepay.key'         => 'nullable|string|max:2000',
            'simplepay.client_id'   => 'required|integer|min:1',
            'simplepay.wave_monthly' => 'required|integer|min:1',
            'simplepay.wave_weekly' => 'required|integer|min:1',

            // email
            'email.host'     => 'nullable|string|max:255',
            'email.port'     => 'nullable|integer|min:1',
            'email.username' => 'nullable|string|max:255',
            'email.password' => 'nullable|string|max:2000',
            'email.from'     => 'nullable|email',

            // numbering
            'numbering.invoice_prefix' => 'nullable|string|max:10',
            'numbering.quote_prefix'   => 'nullable|string|max:10',
        ]);

        $userId = optional($request->user())->id;

        $toSave = [
            // company
            ['key' => 'company_name', 'value' => $data['company']['company_name'] ?? null],
            ['key' => 'company_logo_url', 'value' => $data['company']['company_logo_url'] ?? null],
            ['key' => 'tax_rate', 'value' => $data['company']['tax_rate'] ?? null],

            // simplepay
            ['key' => 'simplepay_base', 'value' => $data['simplepay']['base'] ?? null],
            // key goes in with is_secret=true only if provided (don’t overwrite with null)
            ['key' => 'simplepay_client_id', 'value' => $data['simplepay']['client_id'] ?? null],
            ['key' => 'simplepay_wave_id_monthly', 'value' => $data['simplepay']['wave_monthly'] ?? null],
            ['key' => 'simplepay_wave_id_weekly', 'value' => $data['simplepay']['wave_weekly'] ?? null],

            // email
            ['key' => 'email_host', 'value' => $data['email']['host'] ?? null],
            ['key' => 'email_port', 'value' => $data['email']['port'] ?? null],
            ['key' => 'email_username', 'value' => $data['email']['username'] ?? null],
            ['key' => 'email_from', 'value' => $data['email']['from'] ?? null],

            // numbering
            ['key' => 'invoice_prefix', 'value' => $data['numbering']['invoice_prefix'] ?? null],
            ['key' => 'quote_prefix',   'value' => $data['numbering']['quote_prefix'] ?? null],
        ];

        foreach ($toSave as $row) {
            if (!is_null($row['value'])) {
                SystemSetting::updateOrCreate(
                    ['key' => $row['key']],
                    ['value' => $row['value'], 'updated_by' => $userId, 'is_secret' => false]
                );
            }
        }

        if (!empty($data['simplepay']['key'])) {
            SystemSetting::updateOrCreate(
                ['key' => 'simplepay_key'],
                ['value' => $data['simplepay']['key'], 'updated_by' => $userId, 'is_secret' => true]
            );
        }
        if (!empty($data['email']['password'])) {
            SystemSetting::updateOrCreate(
                ['key' => 'email_password'],
                ['value' => $data['email']['password'], 'updated_by' => $userId, 'is_secret' => true]
            );
        }

        Cache::forget('settings:all'); // bust cache so new values take effect
        // Optionally apply immediately in-process:
        app(\App\Providers\AppServiceProvider::class)->boot();

        return response()->json(['ok' => true]);
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
