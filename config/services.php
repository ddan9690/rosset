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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'kcb' => [
        'base_url' => env('KCB_BASE_URL'),
        'token_url' => env('KCB_TOKEN_URL'),

        'consumer_key' => env('KCB_CONSUMER_KEY'),
        'consumer_secret' => env('KCB_CONSUMER_SECRET'),

        'account_prefix' => env('KCB_ACCOUNT_PREFIX'),
        'paybill_number' => env('KCB_PAYBILL_NUMBER'),
        'pass_key' => env('KCB_PASS_KEY'),

        'callback_url' => env('KCB_CALLBACK_URL'),
    ],

];
