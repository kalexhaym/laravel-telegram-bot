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
    public array $data;

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
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'text'                 => $message,
            'disable_notification' => $disable_notification,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->post('/sendMessage', $data);
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
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if ($reply_markup) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        if ($photo instanceof Photo) {
            return $this->post('/sendPhoto', $data, $photo);
        } else {
            $data['photo'] = $photo;

            return $this->post('/sendPhoto', $data);
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
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if ($reply_markup) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        if ($audio instanceof Audio) {
            return $this->post('/sendAudio', $data, $audio);
        } else {
            $data['audio'] = $audio;

            return $this->post('/sendAudio', $data);
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
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        if ($document instanceof Document) {
            return $this->post('/sendDocument', $data, $document);
        } else {
            $data['document'] = $document;

            return $this->post('/sendDocument', $data);
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
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $disable_notification,
        ];

        if (! empty($reply_markup)) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        if ($video instanceof Video) {
            return $this->post('/sendVideo', $data, $video);
        } else {
            $data['video'] = $video;

            return $this->post('/sendVideo', $data);
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
        return $this->post('/setChatTitle', [
            'chat_id' => $this->chat_id,
            'title'   => $title,
        ]);
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
        return $this->post('/setChatDescription', [
            'chat_id'       => $this->chat_id,
            'description'   => $description,
        ]);
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
        return $this->post('/answerCallbackQuery', [
            'callback_query_id' => $callback_query_id,
        ]);
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
        $data = [
            'chat_id'    => $this->chat_id,
            'message_id' => $this->message_id,
            'text'       => $text,
        ];

        if (! empty($this->reply_markup)) {
            $data['reply_markup'] = json_encode($this->reply_markup);
        }

        return $this->post('/editMessageText', $data);
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
        return $this->post('/editMessageReplyMarkup', [
            'chat_id'      => $this->chat_id,
            'message_id'   => $this->message_id,
            'reply_markup' => json_encode($reply_markup),
        ]);
    }

    /**
     * @throws ConnectionException
     *
     * @return array
     */
    public function deleteMessage(): array
    {
        return $this->post('/deleteMessage', [
            'chat_id'    => $this->chat_id,
            'message_id' => $this->message_id,
        ]);
    }
}
