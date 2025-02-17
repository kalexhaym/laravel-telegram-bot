<?php

namespace Kalexhaym\LaravelTelegramBot;

use Kalexhaym\LaravelTelegramBot\Interfaces\CommandInterface;

abstract class Command implements CommandInterface
{
    /**
     * @var string
     */
    public string $command;
}
