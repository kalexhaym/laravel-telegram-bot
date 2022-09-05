<?php

namespace Kalexhaym\LaravelTelegramBot;

interface ExecutableCallback
{
    public function execute(array $message, Telegram $telegram, array $params = []);
}
