<?php

namespace Kalexhaym\LaravelTelegramBot\Interfaces;

use Kalexhaym\LaravelTelegramBot\Telegram;

interface ExecutableCommand
{
    /**
     * @param array    $message
     * @param Telegram $telegram
     *
     * @return void
     */
    public function execute(array $message, Telegram $telegram): void;
}
