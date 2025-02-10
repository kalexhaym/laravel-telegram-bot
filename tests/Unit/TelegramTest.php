<?php

declare(strict_types=1);

namespace Kalexhaym\LaravelTelegramBot\Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Kalexhaym\LaravelTelegramBot\Callback;
use Kalexhaym\LaravelTelegramBot\Command;
use Kalexhaym\LaravelTelegramBot\Telegram;
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
    }

    /**
     * @throws ReflectionException
     */
    public function testLoadCallbacks(): void
    {
        $this->app['config']->set('telegram.commands', [
            TestCommand::class,
        ]);
        $this->app['config']->set('telegram.callbacks', [
            TestCallback::class,
        ]);

        $method = self::getMethod('loadCallbacks');
        $class = new Telegram();
        $result = $method->invokeArgs($class, []);

        $this->assertSame([
            'test-callback' => 'Kalexhaym\LaravelTelegramBot\Tests\Unit\TestCallback',
        ], $result);
    }
}

class TestCommand extends Command
{
    /**
     * @var string
     */
    public $command = 'test-command';

    public function execute(array $message, Telegram $telegram) {}
}

class TestCallback extends Callback
{
    /**
     * @var string
     */
    public $callback = 'test-callback';

    public function execute(array $message, Telegram $telegram, array $params = []) {}
}
