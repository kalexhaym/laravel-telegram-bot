<?php

namespace Kalexhaym\LaravelTelegramBot\Commands;

use Illuminate\Http\Client\ConnectionException;
use Kalexhaym\LaravelTelegramBot\Command;
use Kalexhaym\LaravelTelegramBot\Message;

class MyChatIDCommand extends Command
{
    /**
     * @var string
     */
    public string $command = '/myChatID';

    /**
     * @param Message $message
     *
     * @throws ConnectionException
     *
     * @return void
     */
    public function execute(Message $message): void
    {
        $message->sendMessage("Your Chat ID is {$message->chat_id}");
    }
}
