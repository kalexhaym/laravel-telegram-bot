<?php

namespace Kalexhaym\LaravelTelegramBot;

class DefaultTextHandler extends TextHandler
{
    /**
     * @param array    $message
     * @param Telegram $telegram
     *
     * @return void
     */
    public function execute(array $message, Telegram $telegram): void {}
}
