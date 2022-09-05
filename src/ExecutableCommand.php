<?php

namespace Kalexhaym\LaravelTelegramBot;

interface ExecutableCommand
{
    public function execute(array $message, Telegram $telegram);
}
