<?php

namespace Kalexhaym\LaravelTelegramBot;

use Exception;
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
     * @var ?int
     */
    public ?int $message_id = null;

    /**
     * @var array
     */
    public array $reply_markup = [];

    /**
     * @var array
     */
    private array $keyboard = [];

    /**
     * @var bool
     */
    private bool $disable_notification = false;

    /**
     * @param int  $chat_id
     * @param null $message_id
     */
    public function __construct(int $chat_id, $message_id = null)
    {
        $this->chat_id = $chat_id;
        $this->message_id = $message_id;
    }

    /**
     * @param array $data
     *
     * @return Message
     */
    public function setData(array $data): Message
    {
        $this->data = $data;
        $this->reply_markup = $data['reply_markup'] ?? [];

        return $this;
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
     * @param Keyboard $keyboard
     *
     * @return $this
     */
    public function setKeyboard(Keyboard $keyboard): Message
    {
        $this->keyboard = $keyboard->get();

        return $this;
    }

    /**
     * @return $this
     */
    public function disableNotification(): Message
    {
        $this->disable_notification = true;

        return $this;
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
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendMessage(string $message): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'text'                 => $message,
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
        }

        return $this->post('/sendMessage', $data);
    }

    /**
     * Use this method to send photos. On success, the sent Message is returned.
     *
     * @param string|Photo $photo
     * @param string|null  $caption
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendPhoto(string|Photo $photo, ?string $caption = null): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
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
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendAudio(string|Audio $audio, ?string $caption = null): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
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
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendDocument(string|Document $document, ?string $caption = null): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
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
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendVideo(string|Video $video, ?string $caption = null): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
        }

        if ($video instanceof Video) {
            return $this->post('/sendVideo', $data, $video);
        } else {
            $data['video'] = $video;

            return $this->post('/sendVideo', $data);
        }
    }

    /**
     * Use this method to send animation files (GIF or H.264/MPEG-4 AVC video without sound). On success, the sent Message is returned. Bots can currently send animation files of up to 50 MB in size, this limit may be changed in the future.
     *
     * @param string|Animation $animation
     * @param string|null      $caption
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendAnimation(string|Animation $animation, ?string $caption = null): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
        }

        if ($animation instanceof Animation) {
            return $this->post('/sendAnimation', $data, $animation);
        } else {
            $data['animation'] = $animation;

            return $this->post('/sendAnimation', $data);
        }
    }

    /**
     * Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message. For this to work, your audio must be in an .OGG file encoded with OPUS, or in .MP3 format, or in .M4A format (other formats may be sent as Audio or Document). On success, the sent Message is returned. Bots can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
     *
     * @param string|Voice $voice
     * @param string|null  $caption
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendVoice(string|Voice $voice, ?string $caption = null): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'caption'              => $caption,
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
        }

        if ($voice instanceof Voice) {
            return $this->post('/sendVoice', $data, $voice);
        } else {
            $data['voice'] = $voice;

            return $this->post('/sendVoice', $data);
        }
    }

    /**
     * Use this method to ban a user in a group, a supergroup or a channel. In the case of supergroups and channels, the user will not be able to return to the chat on their own using invite links, etc., unless unbanned first. The bot must be an administrator in the chat for this to work and must have the appropriate administrator rights. Returns True on success.
     *
     * @param int      $user_id         - Unique identifier of the target user
     * @param bool     $revoke_messages - Pass True to delete all messages from the chat for the user that is being removed. If False, the user will be able to see messages in the group that were sent before the user was removed. Always True for supergroups and channels.
     * @param int|null $until_date      - Date when the user will be unbanned; Unix time. If user is banned for more than 366 days or less than 30 seconds from the current time they are considered to be banned forever. Applied for supergroups and channels only.
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function banChatMember(int $user_id, bool $revoke_messages = false, ?int $until_date = null): array
    {
        $data = [
            'chat_id'         => config('telegram.debug.chat_id') ?? $this->chat_id,
            'user_id'         => $user_id,
            'revoke_messages' => $revoke_messages,
        ];

        if (! empty($until_date)) {
            $data['until_date'] = $until_date;
        }

        return $this->post('/banChatMember', $data);
    }

    /**
     * Use this method to unban a previously banned user in a supergroup or channel. The user will not return to the group or channel automatically, but will be able to join via link, etc. The bot must be an administrator for this to work. By default, this method guarantees that after the call the user is not a member of the chat, but will be able to join it. So if the user is a member of the chat they will also be removed from the chat. If you don't want this, use the parameter only_if_banned. Returns True on success.
     *
     * @param int  $user_id        - Unique identifier of the target user
     * @param bool $only_if_banned - Do nothing if the user is not banned
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function unbanChatMember(int $user_id, bool $only_if_banned = true): array
    {
        return $this->post('/unbanChatMember', [
            'chat_id'        => config('telegram.debug.chat_id') ?? $this->chat_id,
            'user_id'        => $user_id,
            'only_if_banned' => $only_if_banned,
        ]);
    }

    /**
     * Use this method to send point on the map. On success, the sent Message is returned.
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendLocation(float $latitude, float $longitude): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            'latitude'             => $latitude,
            'longitude'            => $longitude,
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
        }

        return $this->post('/sendLocation', $data);
    }

    /**
     * Use this method to send a native poll. On success, the sent Message is returned.
     *
     * @param Poll $poll
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function sendPoll(Poll $poll): array
    {
        $data = [
            'chat_id'              => config('telegram.debug.chat_id') ?? $this->chat_id,
            ...$poll->get(),
            'disable_notification' => $this->disable_notification,
        ];

        if (! empty($this->keyboard)) {
            $data['reply_markup'] = json_encode($this->keyboard);
        }

        return $this->post('/sendPoll', $data);
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
     * @throws Exception
     * @throws ConnectionException
     *
     * @return array
     */
    public function editMessageText(string $text): array
    {
        if (empty($this->message_id)) {
            throw new Exception('You need to specify the message ID in the Message parameters to edit it.');
        }

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
     * @throws Exception
     * @throws ConnectionException
     *
     * @return array
     */
    public function editMessageReplyMarkup(array $reply_markup = []): array
    {
        if (empty($this->message_id)) {
            throw new Exception('You need to specify the message ID in the Message parameters to edit it.');
        }

        return $this->post('/editMessageReplyMarkup', [
            'chat_id'      => $this->chat_id,
            'message_id'   => $this->message_id,
            'reply_markup' => json_encode($reply_markup),
        ]);
    }

    /**
     * @param Keyboard $keyboard
     *
     * @throws Exception
     * @throws ConnectionException
     *
     * @return array
     */
    public function editMessageKeyboard(Keyboard $keyboard): array
    {
        if (empty($this->message_id)) {
            throw new Exception('You need to specify the message ID in the Message parameters to edit it.');
        }

        return $this->post('/editMessageReplyMarkup', [
            'chat_id'      => $this->chat_id,
            'message_id'   => $this->message_id,
            'reply_markup' => json_encode($keyboard->get()),
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
     * @throws Exception
     * @throws ConnectionException
     *
     * @return array
     */
    public function deleteMessage(): array
    {
        if (empty($this->message_id)) {
            throw new Exception('You need to specify the message ID in the Message parameters to delete it.');
        }

        return $this->post('/deleteMessage', [
            'chat_id'    => $this->chat_id,
            'message_id' => $this->message_id,
        ]);
    }
}
