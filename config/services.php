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

    'paystack' => [
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
    ],

    'flutterwave' => [
        'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
        'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
        'secret_hash' => env('FLUTTERWAVE_SECRET_HASH'),
    ],

    'cpalead' => [
        'api_key' => env('CPALEAD_API_KEY'),
        'base_url' => env('CPALEAD_BASE_URL', 'https://api.cpalead.com'),
        'advertiser_id' => env('CPALEAD_ADVERTISER_ID'),
        'default_cookie_duration' => env('CPALEAD_DEFAULT_COOKIE_DURATION', 30),
        'platform_spread_percentage' => env('CPALEAD_PLATFORM_SPREAD_PERCENTAGE', 10),
        'auto_approve' => env('CPALEAD_AUTO_APPROVE', true),
        'disable_missing_offers' => env('CPALEAD_DISABLE_MISSING_OFFERS', false),
    ],

    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
        'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com'),
        'default_model' => env('DEEPSEEK_DEFAULT_MODEL', 'deepseek-v4-flash'),
    ],

];
