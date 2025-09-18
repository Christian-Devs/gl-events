<?php

$banks = json_decode(env('SIMPLEPAY_BANKS_JSON', '[]'), true) ?: [];

return [
    'banks' => array_map(fn($b) => [
        'id'   => (int) $b['id'],
        'name' => (string) $b['name'],
    ], $banks),
];
