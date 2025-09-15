<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path('Support/helpers.php');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->runningInConsole()) {
            return;
        }

        // If the table isn’t there yet (first boot after deploy), bail out
        try {
            if (!Schema::hasTable('system_settings')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        // Map DB settings -> config (with ENV/config fallbacks in the helper)
        config([
            'services.simplepay.base'              => setting('simplepay_base', config('services.simplepay.base')),
            'services.simplepay.key'               => setting('simplepay_key',  config('services.simplepay.key')),
            'services.simplepay.client_id'         => (int) setting('simplepay_client_id', config('services.simplepay.client_id')),
            'services.simplepay.wave_ids.monthly'  => (int) setting('simplepay_wave_id_monthly', config('services.simplepay.wave_ids.monthly')),
            'services.simplepay.wave_ids.weekly'   => (int) setting('simplepay_wave_id_weekly',  config('services.simplepay.wave_ids.weekly')),

            'mail.mailers.smtp.host'     => setting('email_host',     config('mail.mailers.smtp.host')),
            'mail.mailers.smtp.port'     => (int) setting('email_port',     config('mail.mailers.smtp.port')),
            'mail.mailers.smtp.username' => setting('email_username', config('mail.mailers.smtp.username')),
            'mail.mailers.smtp.password' => setting('email_password', config('mail.mailers.smtp.password')),
            'mail.from.address'          => setting('email_from',     config('mail.from.address')),
            'app.name'                   => setting('company_name',   config('app.name')),
        ]);
    }
}
