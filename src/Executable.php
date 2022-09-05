<?php

namespace Kalexhaym\LaravelTelegramBot;

interface Executable
{
    public function execute(array $message, Telegram $telegram);
}
