<?php

namespace Kalexhaym\LaravelTelegramBot;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Kalexhaym\LaravelTelegramBot\Exceptions\CallbackException;
use Kalexhaym\LaravelTelegramBot\Exceptions\CommandException;
use Kalexhaym\LaravelTelegramBot\Exceptions\TextHandlerException;
use Kalexhaym\LaravelTelegramBot\Traits\Requests;

class Telegram
{
    use Requests;

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
     * @throws ConnectionException
     *
     * @return array
     */
    public function setWebhook(): array
    {
        $method = '/setWebhook';

        return $this->post($method, [
            'url' => route(config('telegram.hook.route-name')),
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws ConnectionException
     *
     * @return void
     */
    public function hook(Request $request): void
    {
        $update = json_decode($request->getContent(), true);

        $this->processUpdate($update);
    }

    /**
     * @throws ConnectionException
     *
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

        $result = $this->post($method, $data, [], [], config('telegram.poll.timeout', 50) + 5);

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
     * @throws ConnectionException
     *
     * @return void
     */
    private function processUpdate(array $update): void
    {
        if (! empty($update['callback_query'])) {
            $callback_query = $update['callback_query'];
            $message = new Message($callback_query['message']);
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
                $class->execute($message, $params);
                $message->answerCallbackQuery($callback_query['id']);
            }
        }

        if (! empty($update['message'])) {
            $message = new Message($update['message']);

            if ($message->hasCommands()) {
                foreach ($message->getCommands() as $command) {
                    if (array_key_exists($command, $this->commands_list)) {
                        $class = new $this->commands_list[$command]();
                        $class->execute($message);
                    }
                }
            } else {
                $this->text_handler->execute($message);
            }
        }
    }
}
