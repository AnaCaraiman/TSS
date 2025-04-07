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

    'ms_auth' => [
        'url' => env('MS_AUTH_URL'),
    ],

    'ms_product' => [
        'url' => env('MS_PRODUCT_URL'),
    ],

    'ms_cart' => [
        'url' => env('MS_CART_URL'),
    ],

    'ms_catalog' => [
        'url' => env('MS_CATALOG_URL'),
    ],
    'ms_order' => [
        'url' => env('MS_ORDER_URL'),
    ],
    'ms_payment' => [
        'url' => env('MS_PAYMENT_URL'),
    ]



];
