<?php

namespace App\Services\Payroll;

use App\Model\Employee;
use Illuminate\Support\Carbon;

class EmployeeMapper
{
    public static function toSimplePay(Employee $e): array
    {
        // Map to SimplePay REQUIRED fields
        $payload = [
            'first_name' => trim((string) $e->first_name),
            'last_name' => trim((string) $e->last_name),
            'id_number' => self::nullIfEmpty($e->id_number), // SA ID / passport
            'birthdate' => self::dateOrNull($e->date_of_birth ?? $e->dob), // -> birthdate
            'appointment_date' => self::dateOrDefault($e->start_date ?? $e->hired_at, now()),
            'payment_method' => self::mapPaymentMethod($e->payment_method ?? null), // 'bank' | 'cash'
            'wave' => self::mapWave($e->pay_frequency ?? 'monthly'),     // 'monthly' | 'fortnightly' | 'weekly'
            // Optional but useful for recon:
            'external_reference' => (string) $e->id,
        ];

        // Remove only NULLs (keep "0")
        return array_filter($payload, static function ($v) {
            return $v !== null;
        });
    }

    private static function nullIfEmpty($v)
    {
        return (isset($v) && trim((string) $v) !== '') ? $v : null;
    }

    private static function dateOrNull($v): ?string
    {
        if (!$v)
            return null;
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function dateOrDefault($v, $default): string
    {
        try {
            return Carbon::parse($v ?: $default)->format('Y-m-d');
        } catch (\Throwable $e) {
            return Carbon::parse($default)->format('Y-m-d');
        }
    }

    // SimplePay accepts 'bank' or 'cash' (use 'bank' by default; adjust if you know otherwise)
    private static function mapPaymentMethod($v): string
    {
        $v = strtolower((string) $v);
        return in_array($v, ['bank', 'cash'], true) ? $v : 'bank';
    }

    // SimplePay calls the pay cycle "wave"
    private static function mapWave($v): string
    {
        $v = strtolower((string) $v);
        if (in_array($v, ['weekly', 'fortnightly', 'monthly'], true))
            return $v;
        if ($v === 'biweekly')
            return 'fortnightly';
        return 'monthly';
    }
}
