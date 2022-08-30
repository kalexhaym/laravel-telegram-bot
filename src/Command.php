<?php

namespace Kalexhaym\LaravelTelegramBot;

abstract class Command implements Executable
{
    /**
     * @var string
     */
    protected $command;
}
