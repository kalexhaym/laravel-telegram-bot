<?php

namespace Kalexhaym\LaravelTelegramBot;

use Kalexhaym\LaravelTelegramBot\Interfaces\ExecutableCommand;

abstract class Command implements ExecutableCommand
{
    /**
     * @var string
     */
    public string $command;
}
