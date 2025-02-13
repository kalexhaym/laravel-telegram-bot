<?php

namespace Kalexhaym\LaravelTelegramBot\Console;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Kalexhaym\LaravelTelegramBot\Telegram;

/**
 * Class SetTelegramHook
 */
class SetHook extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:set-hook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Telegram hook';

    /**
     * Execute the console command.
     *
     * @throws ConnectionException
     *
     * @return void
     */
    public function handle(): void
    {
        $telegram = new Telegram();

        $this->info(json_encode($telegram->setWebhook()));
    }
}
