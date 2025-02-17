<?php

return [

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
    | Commands List
    |--------------------------------------------------------------------------
    */

    'commands' => [
        \App\Telegram\StartCommand::class,

        \Kalexhaym\LaravelTelegramBot\Commands\MyChatIDCommand::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Callbacks List
    |--------------------------------------------------------------------------
    */

    'callbacks' => [
        \App\Telegram\StartCallback::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Text Handler
    |--------------------------------------------------------------------------
    */

    'text-handler' => Kalexhaym\LaravelTelegramBot\DefaultTextHandler::class,

    /*
    |--------------------------------------------------------------------------
    | Polls Handler
    |--------------------------------------------------------------------------
    */

    'polls-handler' => Kalexhaym\LaravelTelegramBot\DefaultPollsHandler::class,

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
    | Hook Configuration
    |--------------------------------------------------------------------------
    */

    'hook' => [
        // It is used in this form /{hook.uri}/{bot.token}
        'uri'          => env('TELEGRAM_HOOK_URI', 'telegram-hook'),

        'route-name'   => env('TELEGRAM_HOOK_ROUTE_NAME', 'telegram-hook'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Http Configuration
    |--------------------------------------------------------------------------
    */

    'debug' => [
        'http' => env('TELEGRAM_DEBUG_HTTP', false),

        // Force sending messages to this chat, bot must be a member of the chat
        'chat_id' => env('TELEGRAM_DEBUG_CHAT_ID'),
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
    | Api Configuration
    |--------------------------------------------------------------------------
    |
    */

    'api' => [
        'url' => env('TELEGRAM_API_URL', 'https://api.telegram.org/bot'),
    ],

];
