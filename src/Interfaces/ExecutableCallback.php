<?php

namespace Kalexhaym\LaravelTelegramBot\Interfaces;

use Kalexhaym\LaravelTelegramBot\Message;

interface ExecutableCallback
{
    /**
     * @param Message $message
     * @param array   $params
     *
     * @return void
     */
    public function execute(Message $message, array $params = []): void;
}
