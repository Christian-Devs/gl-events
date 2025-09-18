<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SimplePayClient;
use Illuminate\Support\Facades\Cache;

class MetaController extends Controller
{
    public function banks(SimplePayClient $sp)
    {
        $banks = Cache::remember('simplepay_banks', 60 * 60, function () use ($sp) {
            $raw = $sp->listBanks();

            return collect($raw)->map(function ($row) {
                $b = $row['bank'] ?? $row;
                return ['id' => (int)($b['id'] ?? 0), 'name' => (string)($b['name'] ?? '')];
            })->filter(fn($b) => $b['id'] > 0 && $b['name'] !== '')->values()->all();
        });
        return response()->json($banks);
    }
}
