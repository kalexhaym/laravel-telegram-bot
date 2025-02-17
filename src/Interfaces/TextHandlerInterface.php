<?php

namespace Kalexhaym\LaravelTelegramBot\Interfaces;

use Kalexhaym\LaravelTelegramBot\Message;

interface TextHandlerInterface
{
    /**
     * @param Message $message
     *
     * @return void
     */
    public function execute(Message $message): void;
}
