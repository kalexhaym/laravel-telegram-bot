<?php

namespace Kalexhaym\LaravelTelegramBot;

use Illuminate\Support\Facades\Cache;

class Telegram extends Curl
{
    const BOT_COMMAND_TYPE = 'bot_command';

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $commands_list;

    /**
     * Telegram constructor.
     */
    public function __construct()
    {
        $this->url = 'https://api.telegram.org/bot' . config('telegram.bot.token');

        $this->commands_list = $this->loadCommands();
    }

    private function loadCommands(): array
    {
        $classes = config('telegram.commands');

        $commands_list = [];

        foreach ($classes as $class_name) {
            $class = new $class_name();
            $commands_list[$class->command] = $class_name;
        }

        return $commands_list;
    }

    /**
     * @return array
     */
    public function setWebhook(): array
    {
        $method = '/setWebhook';

        return $this->post($this->url . $method, [
            'url' => route('telegram-hook')
        ]);
    }

    /**
     * @return array
     */
    public function getUpdates(): array
    {
        $method = '/getUpdates';

        $cache_key = config('telegram.cache.key') . '-last-update';

        $offset = Cache::get($cache_key, 0);

        $data = [
            'offset' => $offset + 1,
            'limit' => config('telegram.poll.limit', 100),
            'timeout' => config('telegram.poll.timeout', 50),
        ];

        $result = $this->post($this->url . $method, $data);

        if (!empty($result['data']['result'])) {
            Cache::put($cache_key, last($result['data']['result'])['update_id']);
        }

        return $result;
    }

    /**
     * @return void
     */
    public function pollUpdates(): void
    {
        while(true) {
            $result = $this->getUpdates();

            foreach ($result['data']['result'] as $update) {
                if (empty($update['message'])) {
                    continue;
                }

                $message = $update['message'];

                if ($this->hasCommands($message)) {
                    foreach ($this->getCommands($message) as $command) {
                        if (key_exists($command, $this->commands_list)) {
                            $class = new $this->commands_list[$command]();
                            $class->execute($message, $this);
                        }
                    }
                }
            }

            sleep(config('telegram.poll.sleep', 2));
        }
    }

    /**
     * @param array $message
     *
     * @return bool
     */
    private function hasCommands(array $message): bool
    {
        return !empty($message['entities']) && in_array(self::BOT_COMMAND_TYPE, array_column($message['entities'], 'type'));
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
     * @param int $chat_id
     * @param string $message
     * @param array $reply_markup
     * @param bool $disable_notification
     *
     * @return array
     */
    public function sendMessage(int $chat_id, string $message, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendMessage';

        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'disable_notification' => $disable_notification
        ];

        if (!empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url . $method, $data);
    }

    /**
     * @param int $chat_id
     * @param string $document
     * @param null $caption
     * @param array $reply_markup
     * @param bool $disable_notification
     *
     * @return array
     */
    public function sendDocument(int $chat_id, string $document, $caption = null, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendDocument';

        $data = [
            'chat_id' => $chat_id,
            'document' => $document,
            'caption' => $caption,
            'disable_notification' => $disable_notification
        ];

        if (!empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url . $method, $data);
    }

    /**
     * @param int $chat_id
     * @param string $photo
     * @param null $caption
     * @param array $reply_markup
     * @param bool $disable_notification
     *
     * @return array
     */
    public function sendPhoto(int $chat_id, string $photo, $caption = null, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendPhoto';

        $data = [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'disable_notification' => $disable_notification
        ];

        if ($reply_markup) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url . $method, $data);
    }

    /**
     * @param int $chat_id
     * @param int $message_id
     * @param array $reply_markup
     *
     * @return array
     */
    public function editMessageReplyMarkup(int $chat_id, int $message_id, array $reply_markup = []): array
    {
        $method = '/editMessageReplyMarkup';

        $data = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ];

        if (!empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($this->url . $method, $data);
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
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ];

        return $this->post($this->url . $method, $data);
    }
}
