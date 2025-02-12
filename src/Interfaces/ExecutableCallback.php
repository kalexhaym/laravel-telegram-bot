<?php

namespace Kalexhaym\LaravelTelegramBot\Interfaces;

use Kalexhaym\LaravelTelegramBot\Telegram;

interface ExecutableCallback
{
    /**
     * @param array    $message
     * @param Telegram $telegram
     * @param array    $params
     *
     * @return void
     */
    public function execute(array $message, Telegram $telegram, array $params = []): void;
}
