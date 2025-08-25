<?php

namespace App\Services\Payroll;

use App\Model\Employee;
use Illuminate\Support\Carbon;

class EmployeeMapper
{
    /**
     * Local Employee -> SimplePay payload
     * @return array<string,mixed>
     */
    public static function toSimplePay(Employee $e)
    {
        $payload = [
            'first_name'          => self::t($e->first_name),
            'last_name'           => self::t($e->last_name),

            'email'               => self::nullIfEmpty($e->email),
            'mobile'              => self::nullIfEmpty($e->phone),

            'id_number'           => self::nullIfEmpty($e->id_number),
            'birthdate'           => self::dateOrNull($e->birthdate),

            // SimplePay often expects "appointment_date"
            'appointment_date'    => self::dateOrDefault($e->start_date, date('Y-m-d')),

            'payment_method'      => self::mapPaymentMethod($e->payment_method), // bank|cash
            'wave'                => self::mapWave($e->pay_frequency),           // monthly|fortnightly|weekly

            'external_reference'  => self::nullIfEmpty($e->external_reference) ?: (string) $e->id,
        ];

        // keep "0", strip only nulls
        return array_filter($payload, function ($v) {
            return $v !== null;
        });
    }

    /**
     * SimplePay -> local (optional reverse mapper)
     * @param array<string,mixed> $sp
     * @return array<string,mixed>
     */
    public static function fromSimplePay(array $sp)
    {
        $first = self::dg($sp, 'employee.first_name', self::dg($sp, 'first_name'));
        $last  = self::dg($sp, 'employee.last_name',  self::dg($sp, 'last_name'));

        $out = [
            'first_name'         => self::t($first),
            'last_name'          => self::t($last),
            'email'              => self::nullIfEmpty(self::dg($sp, 'employee.email', self::dg($sp, 'email'))),
            'phone'              => self::nullIfEmpty(self::dg($sp, 'employee.mobile', self::dg($sp, 'mobile'))),
            'id_number'          => self::nullIfEmpty(self::dg($sp, 'employee.id_number', self::dg($sp, 'id_number'))),
            'birthdate'          => self::dateOrNull(self::dg($sp, 'employee.birthdate', self::dg($sp, 'birthdate'))),
            'start_date'         => self::dateOrNull(self::dg($sp, 'employee.appointment_date', self::dg($sp, 'appointment_date'))),
            'payment_method'     => self::mapPaymentMethod(self::dg($sp, 'employee.payment_method', self::dg($sp, 'payment_method'))),
            'pay_frequency'      => self::mapWave(self::dg($sp, 'employee.wave', self::dg($sp, 'wave'))),
            'external_reference' => self::nullIfEmpty(self::dg($sp, 'employee.external_reference', self::dg($sp, 'external_reference'))),
        ];

        return array_filter($out, function ($v) {
            return $v !== null;
        });
    }

    /* ----------------- helpers ----------------- */

    private static function t($v)
    {
        return trim((string) $v);
    }

    private static function nullIfEmpty($v)
    {
        return (isset($v) && trim((string) $v) !== '') ? trim((string) $v) : null;
    }

    // PHP 7-safe: no nullable return type hints
    private static function dateOrNull($v)
    {
        if (!$v) return null;
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function dateOrDefault($v, $default)
    {
        try {
            return Carbon::parse($v ?: $default)->format('Y-m-d');
        } catch (\Throwable $e) {
            try {
                return Carbon::parse($default)->format('Y-m-d');
            } catch (\Throwable $e2) {
                return date('Y-m-d');
            }
        }
    }

    private static function mapPaymentMethod($v)
    {
        $v = strtolower(trim((string) $v));
        return in_array($v, ['bank', 'cash'], true) ? $v : 'bank';
    }

    private static function mapWave($v)
    {
        $v = strtolower(trim((string) $v));
        if (in_array($v, ['weekly', 'fortnightly', 'monthly'], true)) return $v;
        if ($v === 'biweekly') return 'fortnightly';
        return 'monthly';
    }

    // tiny data_get replacement (PHP 7-safe, no helpers needed)
    private static function dg(array $arr, $path, $default = null)
    {
        $keys = explode('.', $path);
        foreach ($keys as $k) {
            if (!is_array($arr) || !array_key_exists($k, $arr)) {
                return $default;
            }
            $arr = $arr[$k];
        }
        return $arr;
    }
}
