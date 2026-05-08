<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Log Server Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the real-time log streaming server.
    | These values can be overridden by fetching from an external API.
    |
    */

    'server_url' => env('LOG_SERVER_URL', 'wss://logs.sparkcliks.com:3443'),

    'api_key' => env('LOG_SERVER_API_KEY', 'logapi_prod_8CAF333965B8290F2D0D8E44B263EA076C5DC5EDA9894EB6C2723B4326F9483B'),

    /*
    |--------------------------------------------------------------------------
    | External API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for fetching log credentials from an external API.
    | Set 'enabled' to true when you want to fetch credentials from API.
    |
    */

    'external_api' => [
        'enabled' => env('LOG_EXTERNAL_API_ENABLED', false),
        'url' => env('LOG_EXTERNAL_API_URL', 'https://api.example.com/v1/log-credentials'),
        'token' => env('LOG_EXTERNAL_API_TOKEN', ''),
        'timeout' => env('LOG_EXTERNAL_API_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Settings
    |--------------------------------------------------------------------------
    |
    | Additional settings for log viewer
    |
    */

    'settings' => [
        'max_lines' => env('LOG_MAX_LINES', 1000),
        'auto_scroll' => env('LOG_AUTO_SCROLL', true),
        'reconnect_attempts' => env('LOG_RECONNECT_ATTEMPTS', 5),
    ],

];
