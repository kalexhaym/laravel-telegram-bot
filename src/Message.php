<?php

namespace Kalexhaym\LaravelTelegramBot;

use Illuminate\Http\Client\ConnectionException;
use Kalexhaym\LaravelTelegramBot\Traits\Requests;

class Message
{
    use Requests;

    private const BOT_COMMAND_TYPE = 'bot_command';

    /**
     * @var array
     */
    private array $data;

    /**
     * @var int
     */
    public int $chat_id;

    /**
     * @var int
     */
    public int $message_id;

    /**
     * @var array
     */
    public array $reply_markup = [];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->chat_id = $data['chat']['id'];
        $this->message_id = $data['message_id'];
        $this->reply_markup = $data['reply_markup'] ?? [];
    }

    /**
     * @return bool
     */
    public function hasCommands(): bool
    {
        return ! empty($this->data['entities']) && in_array(self::BOT_COMMAND_TYPE, array_column($this->data['entities'], 'type'));
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        if (empty($this->data['entities'])) {
            return [];
        }

        $commands = [];

        foreach ($this->data['entities'] as $entity) {
            if ($entity['type'] == self::BOT_COMMAND_TYPE) {
                $commands[] = substr($this->data['text'], $entity['offset'], $entity['length']);
            }
        }

        return $commands;
    }

    /**
     * @param string $message
     * @param array  $reply_markup
     * @param bool   $disable_notification
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendMessage(string $message, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendMessage';

        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'text'                 => $message,
            'disable_notification' => $disable_notification,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post($method, $data);
    }

    /**
     * @param string|Photo $photo
     * @param string|null  $caption
     * @param array        $reply_markup
     * @param bool         $disable_notification
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendPhoto(string|Photo $photo, ?string $caption = null, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendPhoto';

        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if ($reply_markup) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        if ($photo instanceof Photo) {
            return $this->post($method, $data, $photo->get());
        } else {
            $data['photo'] = $photo;

            return $this->post($method, $data);
        }
    }

    /**
     * @param string|Audio $audio
     * @param string|null  $caption
     * @param array        $reply_markup
     * @param bool         $disable_notification
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendAudio(string|Audio $audio, ?string $caption = null, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendAudio';

        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if ($reply_markup) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        if ($audio instanceof Audio) {
            return $this->post($method, $data, $audio->get());
        } else {
            $data['audio'] = $audio;

            return $this->post($method, $data);
        }
    }

    /**
     * @param string|Document $document
     * @param string|null     $caption
     * @param array           $reply_markup
     * @param bool            $disable_notification
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendDocument(string|Document $document, ?string $caption = null, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendDocument';

        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        if ($document instanceof Document) {
            return $this->post($method, $data, $document->get());
        } else {
            $data['document'] = $document;

            return $this->post($method, $data);
        }
    }

    /**
     * @param string|Video $video
     * @param string|null  $caption
     * @param array        $reply_markup
     * @param bool         $disable_notification
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendVideo(string|Video $video, ?string $caption = null, array $reply_markup = [], bool $disable_notification = false): array
    {
        $method = '/sendVideo';

        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        if ($video instanceof Video) {
            return $this->post($method, $data, $video->get());
        } else {
            $data['video'] = $video;

            return $this->post($method, $data);
        }
    }

    /**
     * @param string $title
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function setChatTitle(string $title): array
    {
        $method = '/setChatTitle';

        $data = [
            'chat_id' => $this->chat_id,
            'title'   => $title,
        ];

        return $this->post($method, $data);
    }

    /**
     * @param string $description
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function setChatDescription(string $description): array
    {
        $method = '/setChatDescription';

        $data = [
            'chat_id'       => $this->chat_id,
            'description'   => $description,
        ];

        return $this->post($method, $data);
    }

    /**
     * @param int $callback_query_id
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function answerCallbackQuery(int $callback_query_id): array
    {
        $method = '/answerCallbackQuery';

        $data = [
            'callback_query_id' => $callback_query_id,
        ];

        return $this->post($method, $data);
    }

    /**
     * @param string $text
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function editMessageText(string $text): array
    {
        $method = '/editMessageText';

        $data = [
            'chat_id'    => $this->chat_id,
            'message_id' => $this->message_id,
            'text'       => $text,
        ];

        if (! empty($this->reply_markup)) {
            $data['reply_markup'] = json_encode($this->reply_markup);
        }

        return $this->post($method, $data);
    }

    /**
     * @param array $reply_markup
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function editMessageReplyMarkup(array $reply_markup = []): array
    {
        $method = '/editMessageReplyMarkup';

        $data = [
            'chat_id'      => $this->chat_id,
            'message_id'   => $this->message_id,
            'reply_markup' => json_encode($reply_markup),
        ];

        return $this->post($method, $data);
    }

    /**
     * @throws ConnectionException
     *
     * @return array
     */
    public function deleteMessage(): array
    {
        $method = '/deleteMessage';

        $data = [
            'chat_id'    => $this->chat_id,
            'message_id' => $this->message_id,
        ];

        return $this->post($method, $data);
    }
}
