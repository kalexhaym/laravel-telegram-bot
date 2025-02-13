<?php

declare(strict_types=1);

namespace Kalexhaym\LaravelTelegramBot\Tests\Unit;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Kalexhaym\LaravelTelegramBot\Message;
use Orchestra\Testbench\TestCase;
use ReflectionException;

/**
 * Class AlertTest.
 */
class MessageTest extends TestCase
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

    /**
     * @throws ConnectionException
     */
    public function testSendMessage(): void
    {
        Http::fake([
            $this->testUrl.'/sendMessage' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $reply_markup = ['markup'];

        $response = $class->sendMessage('Test Text', $reply_markup, true);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) use ($reply_markup) {
            return $request->url() === $this->testUrl.'/sendMessage' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'text'                 => 'Test Text',
                    'disable_notification' => true,
                    'reply_markup'         => json_encode($reply_markup),
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendPhoto(): void
    {
        Http::fake([
            $this->testUrl.'/sendPhoto' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $reply_markup = ['markup'];

        $response = $class->sendPhoto('Test Photo', 'Test Caption', $reply_markup, true);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) use ($reply_markup) {
            return $request->url() === $this->testUrl.'/sendPhoto' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => true,
                    'reply_markup'         => json_encode($reply_markup),
                    'photo'                => 'Test Photo',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendAudio(): void
    {
        Http::fake([
            $this->testUrl.'/sendAudio' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $reply_markup = ['markup'];

        $response = $class->sendAudio('Test Audio', 'Test Caption', $reply_markup, true);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) use ($reply_markup) {
            return $request->url() === $this->testUrl.'/sendAudio' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => true,
                    'reply_markup'         => json_encode($reply_markup),
                    'audio'                => 'Test Audio',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendDocument(): void
    {
        Http::fake([
            $this->testUrl.'/sendDocument' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $reply_markup = ['markup'];

        $response = $class->sendDocument('Test Document', 'Test Caption', $reply_markup, true);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) use ($reply_markup) {
            return $request->url() === $this->testUrl.'/sendDocument' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => true,
                    'reply_markup'         => json_encode($reply_markup),
                    'document'             => 'Test Document',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendVideo(): void
    {
        Http::fake([
            $this->testUrl.'/sendVideo' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $reply_markup = ['markup'];

        $response = $class->sendVideo('Test Video', 'Test Caption', $reply_markup, true);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) use ($reply_markup) {
            return $request->url() === $this->testUrl.'/sendVideo' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => true,
                    'reply_markup'         => json_encode($reply_markup),
                    'video'                => 'Test Video',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSetChatTitle(): void
    {
        Http::fake([
            $this->testUrl.'/setChatTitle' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setChatTitle('Test Title');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/setChatTitle' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'=> 1,
                    'title'=> 'Test Title',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSetChatDescription(): void
    {
        Http::fake([
            $this->testUrl.'/setChatDescription' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setChatDescription('Test Description');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/setChatDescription' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'=> 1,
                    'description'=> 'Test Description',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testAnswerCallbackQuery(): void
    {
        Http::fake([
            $this->testUrl.'/answerCallbackQuery' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->answerCallbackQuery(1);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/answerCallbackQuery' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'callback_query_id' => 1,
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testEditMessageText(): void
    {
        Http::fake([
            $this->testUrl.'/editMessageText' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id'   => 1,
            'reply_markup' => ['markup'],
        ]);

        $response = $class->editMessageText('Test Text');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/editMessageText' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'      => 1,
                    'message_id'   => 1,
                    'text'         => 'Test Text',
                    'reply_markup' => json_encode(['markup']),
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testEditMessageReplyMarkup(): void
    {
        Http::fake([
            $this->testUrl.'/editMessageReplyMarkup' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->editMessageReplyMarkup(['markup']);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/editMessageReplyMarkup' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'      => 1,
                    'message_id'   => 1,
                    'reply_markup' => json_encode(['markup']),
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testDeleteMessage(): void
    {
        Http::fake([
            $this->testUrl.'/deleteMessage' => Http::response(['success' => true], 200),
        ]);

        $class = new Message([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->deleteMessage();

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/deleteMessage' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'    => 1,
                    'message_id' => 1,
                ];
        });
    }
}
