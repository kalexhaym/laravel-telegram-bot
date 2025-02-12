<?php

declare(strict_types=1);

namespace Kalexhaym\LaravelTelegramBot\Tests\Unit;

use Kalexhaym\LaravelTelegramBot\Callback;
use Kalexhaym\LaravelTelegramBot\Command;
use Kalexhaym\LaravelTelegramBot\Exceptions\CallbackException;
use Kalexhaym\LaravelTelegramBot\Exceptions\CommandException;
use Kalexhaym\LaravelTelegramBot\Exceptions\TextHandlerException;
use Kalexhaym\LaravelTelegramBot\Telegram;
use Kalexhaym\LaravelTelegramBot\TextHandler;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class AlertTest.
 */
class TelegramTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    protected static function getMethod($name): ReflectionMethod
    {
        $class = new ReflectionClass(Telegram::class);

        return $class->getMethod($name);
    }

    /**
     * @throws ReflectionException
     */
    public function testLoadCommands(): void
    {
        $this->app['config']->set('telegram.commands', [
            TestCommand::class,
        ]);
        $this->app['config']->set('telegram.callbacks', []);

        $method = self::getMethod('loadCommands');
        $class = new Telegram();
        $result = $method->invokeArgs($class, []);

        $this->assertSame([
            'test-command' => 'Kalexhaym\LaravelTelegramBot\Tests\Unit\TestCommand',
        ], $result);

        $this->app['config']->set('telegram.commands', [
            TestTextHandler::class,
        ]);
        $this->app['config']->set('telegram.callbacks', []);

        $this->expectException(CommandException::class);
        $class = new Telegram();
        $method->invokeArgs($class, []);
    }

    /**
     * @throws ReflectionException
     */
    public function testLoadCallbacks(): void
    {
        $this->app['config']->set('telegram.commands', []);
        $this->app['config']->set('telegram.callbacks', [
            TestCallback::class,
        ]);

        $method = self::getMethod('loadCallbacks');

        $class = new Telegram();
        $result = $method->invokeArgs($class, []);

        $this->assertSame([
            'test-callback' => 'Kalexhaym\LaravelTelegramBot\Tests\Unit\TestCallback',
        ], $result);

        $this->app['config']->set('telegram.commands', []);
        $this->app['config']->set('telegram.callbacks', [
            TestTextHandler::class,
        ]);

        $this->expectException(CallbackException::class);
        $class = new Telegram();
        $method->invokeArgs($class, []);
    }

    /**
     * @throws ReflectionException
     */
    public function testLoadTextHandler(): void
    {
        $this->app['config']->set('telegram.commands', []);
        $this->app['config']->set('telegram.callbacks', []);

        $method = self::getMethod('loadTextHandler');

        $class = new Telegram();
        $result = $method->invokeArgs($class, []);
        $this->assertInstanceOf(TextHandler::class, $result);

        $this->app['config']->set('telegram.text-handler', TestTextHandler::class);
        $this->assertInstanceOf(TextHandler::class, $result);

        $this->app['config']->set('telegram.text-handler', TestCommand::class);
        $this->expectException(TextHandlerException::class);
        new Telegram();
    }

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

        $method = self::getMethod('hasCommands');
        $class = new Telegram();

        $result = $method->invokeArgs($class, ['message' => [
            'entities' => [
                [
                    'type' => 'bot_command',
                ],
                [
                    'type' => 'text',
                ],
            ],
        ]]);
        $this->assertSame(true, $result);

        $result = $method->invokeArgs($class, ['message' => [
            'entities' => [
                [
                    'type' => 'text',
                ],
                [
                    'type' => 'bot_command',
                ],
            ],
        ]]);
        $this->assertSame(true, $result);

        $result = $method->invokeArgs($class, ['message' => [
            'entities' => [
                [
                    'type' => 'text',
                ],
                [
                    'type' => 'text',
                ],
            ],
        ]]);
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

        $method = self::getMethod('getCommands');
        $class = new Telegram();

        $result = $method->invokeArgs($class, ['message' => []]);
        $this->assertSame([], $result);

        $result = $method->invokeArgs($class, ['message' => [
            'entities' => [
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
        ]]);
        $this->assertSame(['t'], $result);

        $result = $method->invokeArgs($class, ['message' => [
            'entities' => [
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
        ]]);
        $this->assertSame(['tex'], $result);

        $result = $method->invokeArgs($class, ['message' => [
            'entities' => [
                [
                    'type' => 'text',
                ],
                [
                    'type' => 'text',
                ],
            ],
            'text' => 'test-text',
        ]]);
        $this->assertSame([], $result);
    }
}

class TestCommand extends Command
{
    /**
     * @var string
     */
    public string $command = 'test-command';

    /**
     * @param array    $message
     * @param Telegram $telegram
     *
     * @return void
     */
    public function execute(array $message, Telegram $telegram): void {}
}

class TestCallback extends Callback
{
    /**
     * @var string
     */
    public string $callback = 'test-callback';

    /**
     * @param array    $message
     * @param Telegram $telegram
     * @param array    $params
     *
     * @return void
     */
    public function execute(array $message, Telegram $telegram, array $params = []): void {}
}

class TestTextHandler extends TextHandler
{
    /**
     * @param array    $message
     * @param Telegram $telegram
     *
     * @return void
     */
    public function execute(array $message, Telegram $telegram): void {}
}
