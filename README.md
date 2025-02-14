<p align="center">
<a href="https://github.com/kalexhaym/laravel-telegram-bot/actions" target="_blank"><img src="https://github.com/kalexhaym/laravel-telegram-bot/workflows/Tests/badge.svg"></a>
<a href="https://codecov.io/gh/kalexhaym/laravel-telegram-bot" target="_blank"><img src="https://codecov.io/gh/kalexhaym/laravel-telegram-bot/branch/master/graph/badge.svg" /></a>
<a href="https://packagist.org/packages/kalexhaym/laravel-telegram-bot" target="_blank"><img alt="Packagist" src="https://img.shields.io/packagist/dt/kalexhaym/laravel-telegram-bot.svg"></a>
</p>

# Installation

    composer require kalexhaym/laravel-telegram-bot

# Publish config

    php artisan vendor:publish --tag=telegram-config

# Make Commands and Callbacks

    php artisan make:telegram-command Start
    php artisan make:telegram-callback Start

# Make Text Handler

    php artisan make:telegram-text-handler

# Setup

After creating a Command or Callback, it must be registered in config/telegram.php

![commands-registration.png](.github/IMAGES/commands-registration.png)

Add Telegram Bot token in .env

    TELEGRAM_TOKEN=

# Getting updates

There are two mutually exclusive ways of receiving updates for your bot - the getUpdates method
    
    php artisan telegram:poll-updates

and webhooks

    php artisan telegram:set-hook
