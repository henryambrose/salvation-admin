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

    // External Image APIs for Saints
    'pixabay' => [
        'key' => env('PIXABAY_API_KEY'),
    ],

    'unsplash' => [
        'key' => env('UNSPLASH_ACCESS_KEY'),
    ],

    'google' => [
        'search_engine_id' => env('GOOGLE_CUSTOM_SEARCH_ENGINE_ID'),
        'api_key' => env('GOOGLE_CUSTOM_SEARCH_API_KEY'),
    ],

    // OpenAI API for Chat
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    // CatéGPT API for Catholic teachings
    'categpt' => [
        'key' => env('CATEGPT_API_KEY'),
        'base_url' => 'https://categpt.chat/api',
    ],

    'pexels' => [
        'key' => env('PEXELS_API_KEY'),
        'base_url' => 'https://api.pexels.com/v1/',
    ],

];
