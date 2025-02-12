<?php

namespace Kalexhaym\LaravelTelegramBot;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Kalexhaym\LaravelTelegramBot\Exceptions\CallbackException;
use Kalexhaym\LaravelTelegramBot\Exceptions\CommandException;
use Kalexhaym\LaravelTelegramBot\Exceptions\TextHandlerException;

class Telegram extends Curl
{
    const BOT_COMMAND_TYPE = 'bot_command';

    /**
     * @var string
     */
    private string $url;

    /**
     * @var array
     */
    private array $commands_list;

    /**
     * @var array
     */
    private array $callbacks_list;

    /**
     * @var TextHandler
     */
    private TextHandler $text_handler;

    /**
     * Telegram constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->url = 'https://api.telegram.org/bot'.config('telegram.bot.token');
        $this->commands_list = $this->loadCommands();
        $this->callbacks_list = $this->loadCallbacks();
        $this->text_handler = $this->loadTextHandler();
    }

    /**
     * @throws CommandException
     *
     * @return array
     */
    private function loadCommands(): array
    {
        $classes = config('telegram.commands');

        $commands_list = [];

        foreach ($classes as $class_name) {
            $class = new $class_name();
            if (! $class instanceof Command) {
                throw new CommandException($class_name.' is not a valid command');
            }
            $commands_list[$class->command] = $class_name;
        }

        return $commands_list;
    }

    /**
     * @throws CallbackException
     *
     * @return array
     */
    private function loadCallbacks(): array
    {
        $classes = config('telegram.callbacks');

        $commands_list = [];

        foreach ($classes as $class_name) {
            $class = new $class_name();
            if (! $class instanceof Callback) {
                throw new CallbackException($class_name.' is not a valid callback');
            }
            $commands_list[$class->callback] = $class_name;
        }

        return $commands_list;
    }

    /**
     * @throws TextHandlerException
     *
     * @return TextHandler
     */
    private function loadTextHandler(): TextHandler
    {
        $class_name = config('telegram.text-handler');
        if (! empty($class_name)) {
            $text_handler = new $class_name();
            if (! $text_handler instanceof TextHandler) {
                throw new TextHandlerException($class_name.' is not a valid text handler');
            }

            return $text_handler;
        }

        return new DefaultTextHandler();
    }

    /**
     * @return array
     */
    public function setWebhook(): array
    {
        $method = '/setWebhook';

        return $this->post($this->url.$method, [
            'url' => route('telegram-hook'),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    public function hook(Request $request): void
    {
        $update = json_decode($request->getContent(), true);

        $this->processUpdate($update);
    }

    /**
     * @return array
     */
    public function getUpdates(): array
    {
        $method = '/getUpdates';

        $cache_key = config('telegram.cache.key').'-last-update';

        $offset = Cache::get($cache_key, 0);

        $data = [
            'offset'  => $offset + 1,
            'limit'   => config('telegram.poll.limit', 100),
            'timeout' => config('telegram.poll.timeout', 50),
        ];

        $result = $this->post($this->url.$method, $data);

        if (! empty($result['data']['result'])) {
            Cache::put($cache_key, last($result['data']['result'])['update_id']);
        }

        return $result;
    }

    /**
     * @return void
     */
    public function pollUpdates(): void
    {
        while (true) {
            $result = $this->getUpdates();

            if (! empty($result['data']['result'])) {
                foreach ($result['data']['result'] as $update) {
                    $this->processUpdate($update);
                }
            }

            sleep(config('telegram.poll.sleep', 2));
        }
    }

    /**
     * @param array $update
     *
     * @return void
     */
    private function processUpdate(array $update): void
    {
        if (! empty($update['callback_query'])) {
            $callback_query = $update['callback_query'];
            $message = $callback_query['message'];
            $callback_data = explode(' ', $callback_query['data']);

            $params = [];

            foreach ($callback_data as $item) {
                [$k, $v] = explode('=', $item);
                if ($k == 'callback') {
                    $callback = $v;
                } else {
                    $params[$k] = $v;
                }
            }

            if (! empty($callback) && array_key_exists($callback, $this->callbacks_list)) {
                $class = new $this->callbacks_list[$callback]();
                $class->execute($message, $this, $params);
                $this->answerCallbackQuery($callback_query['id']);
            }
        }

        if (! empty($update['message'])) {
            $message = $update['message'];

            if ($this->hasCommands($message)) {
                foreach ($this->getCommands($message) as $command) {
                    if (array_key_exists($command, $this->commands_list)) {
                        $class = new $this->commands_list[$command]();
                        $class->execute($message, $this);
                    }
                }
            } else {
                $this->text_handler->execute($message, $this);
            }
        }
    }

    /**
     * @param array $message
     *
     * @return bool
     */
    private function hasCommands(array $message): bool
    {
        return ! empty($message['entities']) && in_array(self::BOT_COMMAND_TYPE, array_column($message['entities'], 'type'));
    }

    /**
     * @param array $message
     *
     * @return array
     */
    private function getCommands(array $message): array
    {
        if (empty($message['entities'])) {
            return [];
        }

        $commands = [];

        foreach ($message['entities'] as $entity) {
            if ($entity['type'] == self::BOT_COMMAND_TYPE) {
                $commands[] = substr($message['text'], $entity['offset'], $entity['length']);
            }
        }

        return $commands;
    }

    /**
     * @param int $callback_query_id
     *
     * @return array
     */
    public function answerCallbackQuery(int $callback_query_id): array
    {
        $method = '/answerCallbackQuery';

        $data = [
            'callback_query_id' => $callback_query_id,
        ];

        return $this->post($this->url.$method, $data);
    }

    /**
     * @param int    $chat_id
     * @param string $message
     * @param array  $reply_markup
     * @param bool   $disable_notification
     *
     * @return array
     */
    public function sendMessage(int $chat_id, string $message, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendMessage';

        $data = [
            'chat_id'              => $chat_id,
            'text'                 => $message,
            'disable_notification' => $disable_notification,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url.$method, $data);
    }

    /**
     * @param int    $chat_id
     * @param string $document
     * @param null   $caption
     * @param array  $reply_markup
     * @param bool   $disable_notification
     *
     * @return array
     */
    public function sendDocument(int $chat_id, string $document, $caption = null, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendDocument';

        $data = [
            'chat_id'              => $chat_id,
            'document'             => $document,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url.$method, $data);
    }

    /**
     * @param int    $chat_id
     * @param string $photo
     * @param null   $caption
     * @param array  $reply_markup
     * @param bool   $disable_notification
     *
     * @return array
     */
    public function sendPhoto(int $chat_id, string $photo, $caption = null, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendPhoto';

        $data = [
            'chat_id'              => $chat_id,
            'photo'                => $photo,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if ($reply_markup) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url.$method, $data);
    }

    /**
     * @param int    $chat_id
     * @param int    $message_id
     * @param string $text
     * @param array  $reply_markup
     *
     * @return array
     */
    public function editMessageText(int $chat_id, int $message_id, string $text, array $reply_markup = []): array
    {
        $method = '/editMessageText';

        $data = [
            'chat_id'    => $chat_id,
            'message_id' => $message_id,
            'text'       => $text,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url.$method, $data);
    }

    /**
     * @param int   $chat_id
     * @param int   $message_id
     * @param array $reply_markup
     *
     * @return array
     */
    public function editMessageReplyMarkup(int $chat_id, int $message_id, array $reply_markup = []): array
    {
        $method = '/editMessageReplyMarkup';

        $data = [
            'chat_id'    => $chat_id,
            'message_id' => $message_id,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url.$method, $data);
    }

    /**
     * @param int $chat_id
     * @param int $message_id
     *
     * @return array
     */
    public function deleteMessage(int $chat_id, int $message_id): array
    {
        $method = '/deleteMessage';

        $data = [
            'chat_id'    => $chat_id,
            'message_id' => $message_id,
        ];

        return $this->post($this->url.$method, $data);
    }
}
