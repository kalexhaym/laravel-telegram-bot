<?php

declare(strict_types=1);

namespace Kalexhaym\LaravelTelegramBot\Tests\Unit;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Kalexhaym\LaravelTelegramBot\Keyboard;
use Kalexhaym\LaravelTelegramBot\Message;
use Kalexhaym\LaravelTelegramBot\Poll;
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

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);
        $result = $class->getCommands();
        $this->assertSame([], $result);

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1, 1);
        $class->setData([
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
     * @return void
     */
    public function testSetKeyboard(): void
    {
        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $result = $class->setKeyboard(new Keyboard());
        $this->assertInstanceOf(Message::class, $result);
    }

    /**
     * @return void
     */
    public function testDisableNotification(): void
    {
        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $result = $class->disableNotification();
        $this->assertInstanceOf(Message::class, $result);
    }

    /**
     * @throws ConnectionException
     */
    public function testGetMe(): void
    {
        Http::fake([
            $this->testUrl.'/getMe' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->getMe();

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/getMe' &&
                $request->method() === 'GET' &&
                $request->data() === [];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendMessage(): void
    {
        Http::fake([
            $this->testUrl.'/sendMessage' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setKeyboard(new Keyboard())->sendMessage('Test Text');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/sendMessage' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'text'                 => 'Test Text',
                    'disable_notification' => false,
                    'reply_markup'         => json_encode(['keyboard' => []]),
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

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setKeyboard(new Keyboard())->sendPhoto('Test Photo', 'Test Caption');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/sendPhoto' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => false,
                    'reply_markup'         => json_encode(['keyboard' => []]),
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

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setKeyboard(new Keyboard())->sendAudio('Test Audio', 'Test Caption');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/sendAudio' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => false,
                    'reply_markup'         => json_encode(['keyboard' => []]),
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

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setKeyboard(new Keyboard())->sendDocument('Test Document', 'Test Caption');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/sendDocument' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => false,
                    'reply_markup'         => json_encode(['keyboard' => []]),
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

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setKeyboard(new Keyboard())->sendVideo('Test Video', 'Test Caption');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/sendVideo' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => false,
                    'reply_markup'         => json_encode(['keyboard' => []]),
                    'video'                => 'Test Video',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendAnimation(): void
    {
        Http::fake([
            $this->testUrl.'/sendAnimation' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setKeyboard(new Keyboard())->sendAnimation('Test Animation', 'Test Caption');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/sendAnimation' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => false,
                    'reply_markup'         => json_encode(['keyboard' => []]),
                    'animation'            => 'Test Animation',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendVoice(): void
    {
        Http::fake([
            $this->testUrl.'/sendVoice' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setKeyboard(new Keyboard())->sendVoice('Test Voice', 'Test Caption');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/sendVoice' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'caption'              => 'Test Caption',
                    'disable_notification' => false,
                    'reply_markup'         => json_encode(['keyboard' => []]),
                    'voice'                => 'Test Voice',
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testBanChatMember(): void
    {
        Http::fake([
            $this->testUrl.'/banChatMember' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->banChatMember(1, true, 123);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/banChatMember' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'         => 1,
                    'user_id'         => 1,
                    'revoke_messages' => true,
                    'until_date'      => 123,
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testUnbanChatMember(): void
    {
        Http::fake([
            $this->testUrl.'/unbanChatMember' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->unbanChatMember(1, false);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/unbanChatMember' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'         => 1,
                    'user_id'         => 1,
                    'only_if_banned'  => false,
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendLocation(): void
    {
        Http::fake([
            $this->testUrl.'/sendLocation' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->setKeyboard(new Keyboard())->sendLocation(0.1, 0.2);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/sendLocation' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'              => 1,
                    'latitude'             => 0.1,
                    'longitude'            => 0.2,
                    'disable_notification' => false,
                    'reply_markup'         => json_encode(['keyboard' => []]),
                ];
        });
    }

    /**
     * @throws ConnectionException
     */
    public function testSendPoll(): void
    {
        Http::fake([
            $this->testUrl.'/sendPoll' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $options = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

        $poll = (new Poll('Test Question', $options))
            ->notAnonymous()
            ->allowsMultipleAnswers()
            ->quiz(2, 'explanation')
            ->openPeriod(5);

        $response = $class->setKeyboard(new Keyboard())->sendPoll($poll);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) use ($options) {
            return $request->url() === $this->testUrl.'/sendPoll' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'                 => 1,
                    'question'                => 'Test Question',
                    'options'                 => json_encode($options),
                    'type'                    => 'quiz',
                    'allows_multiple_answers' => true,
                    'correct_option_id'       => 2,
                    'is_anonymous'            => false,
                    'is_closed'               => false,
                    'explanation'             => 'explanation',
                    'open_period'             => 5,
                    'disable_notification'    => false,
                    'reply_markup'            => json_encode(['keyboard' => []]),
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

        $class = new Message(1, 1);
        $class->setData([
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
                    'chat_id' => 1,
                    'title'   => 'Test Title',
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

        $class = new Message(1, 1);
        $class->setData([
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
                    'chat_id'     => 1,
                    'description' => 'Test Description',
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

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id'   => 1,
            'reply_markup' => ['markup'],
        ]);

        $this->expectException(Exception::class);
        $class->editMessageText('Test Text');
    }

    /**
     * @throws ConnectionException
     */
    public function testEditMessageReplyMarkup(): void
    {
        Http::fake([
            $this->testUrl.'/editMessageReplyMarkup' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id'   => 1,
            'reply_markup' => ['markup'],
        ]);

        $this->expectException(Exception::class);
        $class->editMessageReplyMarkup(['markup']);
    }

    /**
     * @throws ConnectionException
     */
    public function testEditMessageKeyboard(): void
    {
        Http::fake([
            $this->testUrl.'/editMessageReplyMarkup' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id' => 1,
        ]);

        $response = $class->editMessageKeyboard(new Keyboard());

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.'/editMessageReplyMarkup' &&
                $request->method() === 'POST' &&
                $request->data() === [
                    'chat_id'      => 1,
                    'message_id'   => 1,
                    'reply_markup' => json_encode(['keyboard' => []]),
                ];
        });

        $class = new Message(1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id'   => 1,
            'reply_markup' => ['markup'],
        ]);

        $this->expectException(Exception::class);
        $class->editMessageKeyboard(new Keyboard());
    }

    /**
     * @throws ConnectionException
     */
    public function testDeleteMessage(): void
    {
        Http::fake([
            $this->testUrl.'/deleteMessage' => Http::response(['success' => true], 200),
        ]);

        $class = new Message(1, 1);
        $class->setData([
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

        $class = new Message(1);
        $class->setData([
            'chat' => [
                'id' => 1,
            ],
            'message_id'   => 1,
            'reply_markup' => ['markup'],
        ]);

        $this->expectException(Exception::class);
        $class->deleteMessage();
    }
}
