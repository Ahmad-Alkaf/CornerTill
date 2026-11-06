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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => '/auth/github/callback',
    ],

    'twitter-oauth-2'=>[
        'client_id'=>env('X_CLIENT_ID'),
        'client_secret' => env('X_CLIENT_SECRET'),
        'redirect' => '/auth/x/callback',
    ],

    'google'=>[
        'client_id'=>env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => '/auth/google/callback',
    ],

    // Paddle product and price IDs of the subscription plans.
    // Used only when config('features.payments_enabled') is true.
    'paddle' => [
        'products' => [
            'basic' => env('PADDLE_PRODUCT_ID_BASIC'),
            'enhanced' => env('PADDLE_PRODUCT_ID_ENHANCED'),
            'advanced' => env('PADDLE_PRODUCT_ID_ADVANCED'),
        ],
        'prices' => [
            'basic' => [
                'monthly' => env('PADDLE_PRICE_ID_BASIC_MONTHLY'),
                'annually' => env('PADDLE_PRICE_ID_BASIC_ANNUALLY'),
            ],
            'enhanced' => [
                'monthly' => env('PADDLE_PRICE_ID_ENHANCED_MONTHLY'),
                'annually' => env('PADDLE_PRICE_ID_ENHANCED_ANNUALLY'),
            ],
            'advanced' => [
                'monthly' => env('PADDLE_PRICE_ID_ADVANCED_MONTHLY'),
                'annually' => env('PADDLE_PRICE_ID_ADVANCED_ANNUALLY'),
            ],
        ],
    ],

];
