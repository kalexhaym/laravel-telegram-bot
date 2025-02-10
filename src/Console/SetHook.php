<?php

namespace Kalexhaym\LaravelTelegramBot\Console;

use Illuminate\Console\Command;
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
     * @return mixed
     */
    public function handle()
    {
        $telegram = new Telegram();

        $this->info($telegram->setWebhook());
    }
}
