<?php

namespace Unit;

use Kalexhaym\LaravelTelegramBot\DefaultTextHandler;
use Kalexhaym\LaravelTelegramBot\TextHandler;
use Orchestra\Testbench\TestCase;

class DefaultTextHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstance(): void
    {
        $handler = new DefaultTextHandler();
        $this->assertInstanceOf(TextHandler::class, $handler);
    }
}
