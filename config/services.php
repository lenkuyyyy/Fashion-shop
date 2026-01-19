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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT'),
    ],
    // để sử dụng Google reCAPTCHA
    // 'twilio' => [
    //     'sid' => env('TWILIO_SID'),
    //     'token' => env('TWILIO_TOKEN'),
    //     'from' => env('TWILIO_FROM'),
    // ],
    // để sử dụng SMS service

    //api của giao hàng nhanh
    'ghn' => [
    'token'  => env('b23654b9-7514-11f0-a383-b6878025f060'),
    'url'    => env('GHN_API_URL', 'https://online-gateway.ghn.vn/shiip/public-api'),
    'shop_id'=> env('5939923'),
],
];
