<?php

declare(strict_types=1);

namespace Kalexhaym\LaravelTelegramBot\Tests\Unit;

use Kalexhaym\LaravelTelegramBot\Message;
use Orchestra\Testbench\TestCase;
use ReflectionException;

/**
 * Class AlertTest.
 */
class MessageTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHasCommands(): void
    {
        $this->app['config']->set('telegram.commands', [
            TestCommand::class,
        ]);
        $this->app['config']->set('telegram.callbacks', [
            TestCallback::class,
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
            'entities'   => [
                [
                    'type' => 'bot_command',
                ],
                [
                    'type' => 'text',
                ],
            ],
        ]);
        $result = $class->hasCommands();
        $this->assertSame(true, $result);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
            'entities'   => [
                [
                    'type' => 'text',
                ],
                [
                    'type' => 'bot_command',
                ],
            ],
        ]);
        $result = $class->hasCommands();
        $this->assertSame(true, $result);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
            'entities'   => [
                [
                    'type' => 'text',
                ],
                [
                    'type' => 'text',
                ],
            ],
        ]);
        $result = $class->hasCommands();
        $this->assertSame(false, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetCommands(): void
    {
        $this->app['config']->set('telegram.commands', [
            TestCommand::class,
        ]);
        $this->app['config']->set('telegram.callbacks', [
            TestCallback::class,
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);
        $result = $class->getCommands();
        $this->assertSame([], $result);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
            'entities'   => [
                [
                    'type'   => 'bot_command',
                    'offset' => 0,
                    'length' => 1,
                ],
                [
                    'type' => 'text',
                ],
            ],
            'text' => 'test-text',
        ]);
        $result = $class->getCommands();
        $this->assertSame(['t'], $result);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
            'entities'   => [
                [
                    'type' => 'text',
                ],
                [
                    'type'   => 'bot_command',
                    'offset' => 5,
                    'length' => 3,
                ],
            ],
            'text' => 'test-text',
        ]);
        $result = $class->getCommands();
        $this->assertSame(['tex'], $result);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
            'entities'   => [
                [
                    'type' => 'text',
                ],
                [
                    'type' => 'text',
                ],
            ],
            'text' => 'test-text',
        ]);
        $result = $class->getCommands();
        $this->assertSame([], $result);
    }
}
