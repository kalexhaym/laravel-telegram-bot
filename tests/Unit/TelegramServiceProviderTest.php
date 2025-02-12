<?php

namespace Unit;

use Illuminate\Support\ServiceProvider;
use Kalexhaym\LaravelTelegramBot\TelegramServiceProvider;
use Orchestra\Testbench\TestCase;

class TelegramServiceProviderTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstance(): void
    {
        $handler = new TelegramServiceProvider($this->app);
        $this->assertInstanceOf(ServiceProvider::class, $handler);
    }
}
