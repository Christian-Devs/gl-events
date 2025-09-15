<?php

use App\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        // During console bootstrap (e.g., migrate, make:migration), avoid DB
        if (app()->runningInConsole()) {
            // Optional: if you mirror some settings to ENV, prefer that first
            // e.g. SIMPLEPAY_BASE maps to simplepay_base
            $envCandidate = 'SIMPLEPAY_' . Str::upper(str_replace('.', '_', $key));
            $fromEnv = env($envCandidate, null);
            return $fromEnv !== null ? $fromEnv : $default;
        }

        // If the table isn't created yet, bail out
        try {
            if (! Schema::hasTable('system_settings')) {
                return $default;
            }
        } catch (\Throwable $e) {
            // Schema connection might not be ready very early in boot
            return $default;
        }

        return Cache::remember("system_setting:{$key}", 300, function () use ($key, $default) {
            try {
                // Use your actual model namespace
                return SystemSetting::query()
                    ->where('key', $key)
                    ->value('value') ?? $default;
            } catch (\Throwable $e) {
                return $default;
            }
        });
    }
}

if (! function_exists('settings_set')) {
    function settings_set(string $key, $value): void
    {
        try {
            if (! app()->runningInConsole() && Schema::hasTable('system_settings')) {
                SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
                Cache::forget("system_setting:{$key}");
            }
        } catch (\Throwable $e) {
            // swallow during early boot
        }
    }
}
