<?php

namespace App\Services\Payroll;

use App\Model\Employee;
use Illuminate\Support\Carbon;

class EmployeeMapper
{
    public static function toSimplePay(Employee $e)
    {
        $appointment = self::dateOrDefault($e->start_date, date('Y-m-d'));
        $waveId      = self::resolveWaveId($e->pay_frequency);
        [$payMethod, $bankAccount] = self::mapPaymentMethodAndBank($e);

        // Identification (conditionally include the right fields)
        [$identType, $idNumber, $otherNumber, $passportCode] = self::mapIdentification($e);

        $payload = array_filter([
            'wave_id'              => $waveId,                          // REQUIRED
            'first_name'           => self::t($e->first_name),          // REQUIRED
            'last_name'            => self::t($e->last_name),           // REQUIRED
            'birthdate'            => self::dateOrNull($e->birthdate),  // REQUIRED (YYYY-MM-DD)
            'appointment_date'     => $appointment,                     // REQUIRED (YYYY-MM-DD)
            'identification_type'  => $identType,                       // REQUIRED
            'id_number'            => $idNumber,                        // if rsa_id or refugee
            'other_number'         => $otherNumber,                     // if passport or asylum_seeker
            'passport_code'        => $passportCode,                    // if passport
            'payment_method'       => $payMethod,                       // 'cash'|'cheque'|'eft_manual'
            'bank_account'         => $bankAccount,                     // required iff eft_manual
            'email'                => self::nullIfEmpty($e->email),
        ], function ($v) {
            return $v !== null && $v !== '';
        });

        return $payload;
    }

    public static function fromSimplePay(array $sp)
    {
        $first = self::dg($sp, 'employee.first_name', self::dg($sp, 'first_name'));
        $last  = self::dg($sp, 'employee.last_name',  self::dg($sp, 'last_name'));

        $out = [
            'first_name'     => self::t($first),
            'last_name'      => self::t($last),
            'email'          => self::nullIfEmpty(self::dg($sp, 'employee.email', self::dg($sp, 'email'))),
            'phone'          => self::nullIfEmpty(self::dg($sp, 'employee.mobile', self::dg($sp, 'mobile'))),
            'id_number'      => self::nullIfEmpty(self::dg($sp, 'employee.id_number', self::dg($sp, 'id_number'))),
            'birthdate'      => self::dateOrNull(self::dg($sp, 'employee.birthdate', self::dg($sp, 'birthdate'))),
            'start_date'     => self::dateOrNull(self::dg($sp, 'employee.appointment_date', self::dg($sp, 'appointment_date'))),
            'payment_method' => self::nullIfEmpty(self::dg($sp, 'employee.payment_method', self::dg($sp, 'payment_method'))),
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

    private static function resolveWaveId($payFrequency)
    {
        $freq = strtolower(trim((string) ($payFrequency ?: 'monthly')));
        if (!in_array($freq, ['monthly', 'weekly'], true)) $freq = 'monthly';

        $waveId = config('services.simplepay.wave_ids.' . $freq);
        if (!$waveId) {
            throw new \RuntimeException("Missing SIMPLEPAY_WAVE_ID for frequency '{$freq}'. Configure in .env.");
        }
        return (int) $waveId;
    }

    /**
     * Identification mapping respecting SimplePay rules.
     * Returns: [identification_type, id_number, other_number, passport_code]
     */
    private static function mapIdentification(Employee $e)
    {
        $id           = self::nullIfEmpty($e->id_number);
        $other        = self::nullIfEmpty($e->other_number ?? null);
        $passportCode = self::nullIfEmpty($e->passport_code ?? null);

        // Prefer explicit forms if your DB has them; otherwise infer
        if ($id && preg_match('/^\d{13}$/', $id)) {
            return ['rsa_id', $id, null, null];
        }
        if ($other) {
            // If you know it's passport, include passport_code (ISO) or SimplePay will reject
            return ['passport', null, $other, $passportCode];
        }

        // nothing available
        return ['none', null, null, null];
    }

    /**
     * Map local payment method and build required bank_account block when needed.
     * Local tokens → SimplePay tokens: cash|cheque|eft_manual
     */
    private static function mapPaymentMethodAndBank(Employee $e)
    {
        $raw = strtolower(trim((string) ($e->payment_method ?: 'cash')));
        $method = in_array($raw, ['cash', 'cheque', 'eft_manual'], true) ? $raw : 'cash';

        $bank = null;
        if ($method === 'eft_manual') {
            $bankId        = $e->bank_id ?? config('services.simplepay.defaults.bank_id');
            $accountNumber = self::nullIfEmpty($e->bank_account_number);
            $branchCode    = self::nullIfEmpty($e->bank_branch_code);
            $accountType   = self::nullIfEmpty($e->bank_account_type);
            $holderRel     = self::nullIfEmpty($e->bank_holder_relationship);
            $holderName    = self::nullIfEmpty($e->bank_holder_name);

            if ($bankId && $accountNumber && $branchCode) {
                $bank = array_filter([
                    'bank_id'             => (int) $bankId,
                    'account_number'      => $accountNumber,
                    'branch_code'         => $branchCode,
                    'account_type'        => $accountType, // "1","2","3","4","6"
                    'holder_relationship' => $holderRel,   // "1","2","3"
                    'holder_name'         => $holderName,
                ], function ($v) {
                    return $v !== null && $v !== '';
                });
            } else {
                // degrade to cash to avoid SimplePay 422
                $method = 'cash';
            }
        }

        return [$method, $bank];
    }

    // tiny data_get replacement
    private static function dg(array $arr, $path, $default = null)
    {
        $keys = explode('.', $path);
        foreach ($keys as $k) {
            if (!is_array($arr) || !array_key_exists($k, $arr)) return $default;
            $arr = $arr[$k];
        }
        return $arr;
    }
}
