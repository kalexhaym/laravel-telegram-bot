<?php

namespace Kalexhaym\LaravelTelegramBot;

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
