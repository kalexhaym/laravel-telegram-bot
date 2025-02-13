<?php

namespace Unit;

use Kalexhaym\LaravelTelegramBot\DefaultTextHandler;
use Kalexhaym\LaravelTelegramBot\Message;
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

    /**
     * @return void
     */
    public function testExecute(): void
    {
        $this->app['config']->set('telegram.commands', []);
        $this->app['config']->set('telegram.callbacks', []);

        $handler = new DefaultTextHandler();
        $handler->execute(new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1
        ]));
        $this->assertTrue(true);
    }
}
