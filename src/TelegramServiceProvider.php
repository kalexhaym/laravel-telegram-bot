<?php

namespace Kalexhaym\LaravelTelegramBot;

use Illuminate\Support\ServiceProvider;
use Kalexhaym\LaravelTelegramBot\Console\PollUpdates;
use Kalexhaym\LaravelTelegramBot\Console\SetHook;
use Kalexhaym\LaravelTelegramBot\Console\TelegramCallbackMakeCommand;
use Kalexhaym\LaravelTelegramBot\Console\TelegramCommandMakeCommand;
use Kalexhaym\LaravelTelegramBot\Console\TextHandlerMakeCommand;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerRoutes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (! defined('TELEGRAM_PATH')) {
            define('TELEGRAM_PATH', realpath(__DIR__.'/laravel-telegram-bot/'));
        }

        $this->configure();
        $this->offerPublishing();
        $this->registerCommands();
    }

    /**
     * Set up the configuration for Telegram.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/telegram.php', 'telegram'
        );
    }

    /**
     * Register the Telegram routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }

    /**
     * Register the Telegram Artisan commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TextHandlerMakeCommand::class,
                TelegramCommandMakeCommand::class,
                TelegramCallbackMakeCommand::class,
                SetHook::class,
                PollUpdates::class,
            ]);
        }
    }

    /**
     * Set up the resource publishing groups for Telegram.
     *
     * @return void
     */
    protected function offerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/telegram.php' => config_path('telegram.php'),
            ], 'telegram-config');
        }
    }
}
