<?php

declare(strict_types=1);

namespace Kalexhaym\LaravelTelegramBot\Tests\Unit;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Kalexhaym\LaravelTelegramBot\Callback;
use Kalexhaym\LaravelTelegramBot\Command;
use Kalexhaym\LaravelTelegramBot\Exceptions\CallbackException;
use Kalexhaym\LaravelTelegramBot\Exceptions\CommandException;
use Kalexhaym\LaravelTelegramBot\Exceptions\TextHandlerException;
use Kalexhaym\LaravelTelegramBot\Message;
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
     * @var string
     */
    private string $testUrl = 'https://api.example.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('telegram.api.url', $this->testUrl);
    }

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
     * @throws ConnectionException
     */
    public function testSetWebhook(): void
    {
        $this->app['config']->set('telegram.commands', []);
        $this->app['config']->set('telegram.callbacks', []);
        $this->app['config']->set('telegram.hook.route-name', 'telegram-hook');

        Route::get('/test-telegram-hook', function () {
            response();
        })->name('telegram-hook');

        $this->get('/test-telegram-hook')
            ->assertStatus(200);

        Http::fake([
            $this->testUrl.'/setWebhook' => Http::response(['success' => true], 200),
        ]);

        $class = new Telegram();

        $response = $class->setWebhook();

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/setWebhook' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'url' => route('telegram-hook'),
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testHook(): void
    {
        $this->app['config']->set('telegram.commands', []);
        $this->app['config']->set('telegram.callbacks', []);

        $request = \Illuminate\Http\Request::create('/some-url', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['key' => 'value']));

        $class = new Telegram();
        $class->hook($request);
        $this->assertTrue(true);
    }

    /**
     * @throws ConnectionException
     */
    public function testGetUpdates(): void
    {
        $this->app['config']->set('telegram.commands', []);
        $this->app['config']->set('telegram.callbacks', []);
        $this->app['config']->set('telegram.poll.limit', 100);
        $this->app['config']->set('telegram.poll.timeout', 50);

        Http::fake([
            $this->testUrl.'/getUpdates' => Http::response(['success' => true], 200),
        ]);

        $class = new Telegram();

        $response = $class->getUpdates();

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/getUpdates' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'offset'  => 1,
                    'limit'   => config('telegram.poll.limit', 100),
                    'timeout' => config('telegram.poll.timeout', 50),
                ];
        });
    }

    //    public function testPollUpdates(): void
    //    {
    //
    //    }

    /**
     * @throws ConnectionException
     */
    public function testProcessUpdate(): void
    {
        $this->app['config']->set('telegram.commands', []);
        $this->app['config']->set('telegram.callbacks', []);

        $method = self::getMethod('processUpdate');
        $class = new Telegram();
        $method->invokeArgs($class, ['update' => [
            'callback_query' => [
                'message' => [
                    'chat' => [
                        'id' => 1,
                    ],
                    'message_id' => 1,
                ],
                'data' => 'callback=test',
            ],
            'message' => [
                'chat' => [
                    'id' => 1,
                ],
                'message_id' => 1,
                'entities'   => [
                    [
                        'type'   => 'bot_command',
                        'offset' => 1,
                        'length' => 1,
                    ],
                    [
                        'type' => 'text',
                    ],
                ],
                'text' => 'test',
            ],
        ]]);
        $this->assertTrue(true);
    }
}

class TestCommand extends Command
{
    /**
     * @var string
     */
    public string $command = 'test-command';

    /**
     * @param Message $message
     *
     * @return void
     */
    public function execute(Message $message): void {}
}

class TestCallback extends Callback
{
    /**
     * @var string
     */
    public string $callback = 'test-callback';

    /**
     * @param Message $message
     * @param array   $params
     *
     * @return void
     */
    public function execute(Message $message, array $params = []): void {}
}

class TestTextHandler extends TextHandler
{
    /**
     * @param Message $message
     *
     * @return void
     */
    public function execute(Message $message): void {}
}
