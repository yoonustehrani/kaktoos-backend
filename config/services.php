<?php

use App\Payment\IranianCurrency;

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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'payment' => [
        'jibit' => [
            'apiKey' => env('JIBIT_APIKEY'),
            'secret' => env('JIBIT_SECRET'),
            'currency' => IranianCurrency::RIAL
        ]
    ],

    'parto' => [
        'username' => env('PARTO_USERNAME'),
        'password' => env('PARTO_PASSWORD'),
        'office_id' => env('PARTO_OFFICEID'),
        'timing' => [
            'flights_cache' => 60 * 2, // 2m
            'calendar_cache' => 60 // 1m
        ],
        'datetime_format' => 'Y-m-d\TH:i:s.uP',
        'endpoint' => 'https://apidemo.partocrs.com/api/',
        'testing' => env('PARTO_TESTING', false)
    ],

    'sms' => [
        'melli_payamak' => [
            'api_key' => env('MELLI_PAYAMAK_API_KEY'),
            'username' => env('MELLI_PAYAMAK_USERNAME'),
            'password' => env('MELLI_PAYAMAK_PASSWORD'),
            'from' => '50004001130151',
            'patterns' => [
                'login' => intval(env('MELLI_PAYAMAK_LOGIN_PATTERN'))
            ]
        ],
        'enabled' => env('SMS_ENABLED', false)
    ]

];
