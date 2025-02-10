<?php

namespace Kalexhaym\LaravelTelegramBot;

interface Executable
{
    /**
     * @param array    $message
     * @param Telegram $telegram
     *
     * @return void
     */
    public function execute(array $message, Telegram $telegram): void;
}
