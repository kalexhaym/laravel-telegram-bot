<?php

namespace Kalexhaym\LaravelTelegramBot;

use Kalexhaym\LaravelTelegramBot\Console\PollUpdates;
use Kalexhaym\LaravelTelegramBot\Console\SetHook;
use Illuminate\Support\ServiceProvider;
use Kalexhaym\LaravelTelegramBot\Console\TelegramCallbackMakeCommand;
use Kalexhaym\LaravelTelegramBot\Console\TelegramCommandMakeCommand;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!defined('TELEGRAM_PATH')) {
            define('TELEGRAM_PATH', realpath(__DIR__ . '/laravel-telegram-bot/'));
        }

        $this->configure();
        $this->offerPublishing();
        $this->registerCommands();
    }

    /**
     * Setup the configuration for Telegram.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/telegram.php', 'telegram'
        );
    }

    /**
     * Register the Telegram routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }

    /**
     * Register the Telegram Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TelegramCommandMakeCommand::class,
                TelegramCallbackMakeCommand::class,
                SetHook::class,
                PollUpdates::class,
            ]);
        }
    }

    /**
     * Setup the resource publishing groups for Telegram.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/telegram.php' => config_path('telegram.php'),
            ], 'telegram-config');
        }
    }
}
