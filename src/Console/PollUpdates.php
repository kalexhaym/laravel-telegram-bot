<?php

namespace Kalexhaym\LaravelTelegramBot\Console;

use Illuminate\Console\Command;
use Kalexhaym\LaravelTelegramBot\Telegram;

/**
 * Class PollUpdates
 */
class PollUpdates extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:poll-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll updates';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $telegram = new Telegram();

        $telegram->pollUpdates();
    }
}
