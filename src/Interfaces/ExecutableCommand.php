<?php

namespace Kalexhaym\LaravelTelegramBot\Interfaces;

use Kalexhaym\LaravelTelegramBot\Message;

interface ExecutableCommand
{
    /**
     * @param Message $message
     *
     * @return void
     */
    public function execute(Message $message): void;
}
