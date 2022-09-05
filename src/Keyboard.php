<?php

namespace Kalexhaym\LaravelTelegramBot;

class Keyboard
{
    /**
     * @var bool
     */
    private $resizable;

    /**
     * @var bool
     */
    private $inline;

    /**
     * @var array
     */
    private $buttons = [];

    /**
     * @return $this
     */
    public function resizable(): Keyboard
    {
        $this->resizable = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function inline(): Keyboard
    {
        $this->inline = true;

        return $this;
    }

    /**
     * @param string $name
     * @param string|null $callback
     * @param array $params
     *
     * @return $this
     */
    public function addButton(string $name, string $callback = null, array $params = []): Keyboard
    {
        $button = ['text' => $name];

        if (!empty($callback)) {
            $button['callback_data'] = 'callback=' . $callback;

            if (!empty($params)) {
                foreach ($params as $key => $param) {
                    $button['callback_data'] .= ' ' . $key . '=' . $param;
                }
            }
        }

        $this->buttons[] = $button;

        return $this;
    }

    /**
     * @param string $name
     * @param string|null $href
     *
     * @return $this
     */
    public function addLink(string $name, string $href = null): Keyboard
    {
        $button = ['text' => $name];

        if (!empty($href)) {
            $button['url'] = $href;
        }

        $this->buttons[] = $button;

        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $type = $this->inline ? 'inline_keyboard' : 'keyboard';

        $keyboard = [
            $type => [$this->buttons],
        ];

        if ($this->resizable) {
            $keyboard['resize_keyboard'] = true;
        }

        return $keyboard;
    }
}
