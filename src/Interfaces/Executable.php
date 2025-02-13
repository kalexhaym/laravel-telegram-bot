<?php

namespace Kalexhaym\LaravelTelegramBot\Interfaces;

use Kalexhaym\LaravelTelegramBot\Message;

interface Executable
{
    /**
     * @param Message $message
     *
     * @return void
     */
    public function execute(Message $message): void;
}
