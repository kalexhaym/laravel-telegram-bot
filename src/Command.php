<?php

namespace Kalexhaym\LaravelTelegramBot;

abstract class Command implements ExecutableCommand
{
    /**
     * @var string
     */
    protected $command;
}
