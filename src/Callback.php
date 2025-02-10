<?php

namespace Kalexhaym\LaravelTelegramBot;

abstract class Callback implements ExecutableCallback
{
    /**
     * @var string
     */
    public $callback;
}
