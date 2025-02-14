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
     * A simple method for testing your bot's authentication token. Requires no parameters. Returns basic information about the bot in form of a User object.
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function getMe(): array
    {
        return $this->get('/getMe');
    }

    /**
     * Use this method to send text messages. On success, the sent Message is returned.
     *
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
     * Use this method to send photos. On success, the sent Message is returned.
     *
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
     * Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .MP3 or .M4A format. On success, the sent Message is returned. Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
     *
     * For sending voice messages, use the sendVoice method instead.
     *
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
     * Use this method to send general files. On success, the sent Message is returned. Bots can currently send files of any type of up to 50 MB in size, this limit may be changed in the future.
     *
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
     * Use this method to send video files, Telegram clients support MPEG4 videos (other formats may be sent as Document). On success, the sent Message is returned. Bots can currently send video files of up to 50 MB in size, this limit may be changed in the future.
     *
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
     * Use this method to change the title of a chat. Titles can't be changed for private chats. The bot must be an administrator in the chat for this to work and must have the appropriate administrator rights. Returns True on success.
     *
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
     * Use this method to change the description of a group, a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the appropriate administrator rights. Returns True on success.
     *
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
     * Use this method to send answers to callback queries sent from inline keyboards. The answer will be displayed to the user as a notification at the top of the chat screen or as an alert. On success, True is returned.
     *
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
     * Use this method to edit text and game messages. On success, if the edited message is not an inline message, the edited Message is returned, otherwise True is returned. Note that business messages that were not sent by the bot and do not contain an inline keyboard can only be edited within 48 hours from the time they were sent.
     *
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
     * Use this method to edit only the reply markup of messages. On success, if the edited message is not an inline message, the edited Message is returned, otherwise True is returned. Note that business messages that were not sent by the bot and do not contain an inline keyboard can only be edited within 48 hours from the time they were sent.
     *
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
     * Use this method to delete a message, including service messages, with the following limitations:
     * - A message can only be deleted if it was sent less than 48 hours ago.
     * - Service messages about a supergroup, channel, or forum topic creation can't be deleted.
     * - A dice message in a private chat can only be deleted if it was sent more than 24 hours ago.
     * - Bots can delete outgoing messages in private chats, groups, and supergroups.
     * - Bots can delete incoming messages in private chats.
     * - Bots granted can_post_messages permissions can delete outgoing messages in channels.
     * - If the bot is an administrator of a group, it can delete any message there.
     * - If the bot has can_delete_messages permission in a supergroup or a channel, it can delete any message there.
     * Returns True on success.
     *
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
