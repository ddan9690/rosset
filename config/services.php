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
        'base_url' => env('APP_ENV') === 'local' 
            ? env('KCB_SANDBOX_BASE_URL', 'https://uat.buni.kcbgroup.com') 
            : env('KCB_PROD_BASE_URL'),

        'token_url' => env('APP_ENV') === 'local' 
            ? env('KCB_SANDBOX_TOKEN_URL', 'https://accounts.buni.kcbgroup.com/oauth2/token') 
            : env('KCB_PROD_TOKEN_URL'),

        'consumer_key' => env('APP_ENV') === 'local' 
            ? env('KCB_SANDBOX_CONSUMER_KEY', 'jwVBQb8ybAkDnbK_cReWRLzD5A4a') 
            : env('KCB_PROD_CONSUMER_KEY'),

        'consumer_secret' => env('APP_ENV') === 'local' 
            ? env('KCB_SANDBOX_CONSUMER_SECRET', 'zYFZvQg2IIOwfeZjGvVKCIvFweQkDDMwNo3Y8mvY4bsa') 
            : env('KCB_PROD_CONSUMER_SECRET'),

        'callback_url' => env('APP_ENV') === 'local' 
            ? env('KCB_SANDBOX_CALLBACK_URL') 
            : env('KCB_PROD_CALLBACK_URL'),

        'paybill_number' => env('APP_ENV') === 'local' 
            ? null 
            : env('KCB_PAYBILL_NUMBER', '522533'),

        'account_number' => env('KCB_ACCOUNT_NUMBER', '7936435'),
        'account_prefix' => env('KCB_ACCOUNT_PREFIX'),
        'pass_key' => env('KCB_PASS_KEY'),
    ],

];