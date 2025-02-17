<?php

namespace Kalexhaym\LaravelTelegramBot;

use Kalexhaym\LaravelTelegramBot\Interfaces\CallbackInterface;

abstract class Callback implements CallbackInterface
{
    /**
     * @var string
     */
    public string $callback;
}
