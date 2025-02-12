<?php

namespace Kalexhaym\LaravelTelegramBot;

use Kalexhaym\LaravelTelegramBot\Interfaces\ExecutableCallback;

abstract class Callback implements ExecutableCallback
{
    /**
     * @var string
     */
    public string $callback;
}
