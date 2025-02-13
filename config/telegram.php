<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Api Configuration
    |--------------------------------------------------------------------------
    |
    */

    'api' => [
        'url' => env('TELEGRAM_API_URL', 'https://api.telegram.org/bot'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bot Configuration
    |--------------------------------------------------------------------------
    |
    */

    'bot' => [
        'name'  => env('TELEGRAM_BOT_NAME'),
        'token' => env('TELEGRAM_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Long Polling Configuration
    |--------------------------------------------------------------------------
    */

    'poll' => [
        'sleep'   => env('TELEGRAM_POLL_SLEEP', 2),
        'limit'   => env('TELEGRAM_POLL_LIMIT', 100),
        'timeout' => env('TELEGRAM_POLL_TIMEOUT', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Http Configuration
    |--------------------------------------------------------------------------
    */

    'http' => [
        'debug' => env('TELEGRAM_HTTP_DEBUG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'key' => env('TELEGRAM_CACHE_KEY', 'telegram'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands List
    |--------------------------------------------------------------------------
    */

    'commands' => [
        \App\Telegram\TestCommand::class,
    ],

    'callbacks' => [
        \App\Telegram\TestCallback::class,
    ],

    'text-handler' => Kalexhaym\LaravelTelegramBot\DefaultTextHandler::class,

];
