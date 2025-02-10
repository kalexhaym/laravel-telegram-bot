<?php

namespace Kalexhaym\LaravelTelegramBot;

abstract class Command implements ExecutableCommand
{
    /**
     * @var string
     */
    public string $command;
}
