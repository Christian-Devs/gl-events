<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'simplepay' => [
        'base'       => env('SIMPLEPAY_BASE'),
        'key'        => env('SIMPLEPAY_KEY'),
        'client_id'  => env('SIMPLEPAY_CLIENT_ID'),
        'wave_ids'  => [
            'monthly'     => env('SIMPLEPAY_WAVE_ID_MONTHLY'),
            'weekly'      => env('SIMPLEPAY_WAVE_ID_WEEKLY'),
        ],
    ],

];
