<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/'.config('telegram.hook.uri').'/'.config('telegram.bot.token'), [\Kalexhaym\LaravelTelegramBot\Telegram::class, 'hook'])->name(config('telegram.hook.route-name'));
