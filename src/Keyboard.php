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
     * @param $name
     * @param $callback
     *
     * @return $this
     */
    public function addButton($name, $callback = null): Keyboard
    {
        $button = ['text' => $name];

        if (!empty($callback)) {
            $button['callback_data'] = $callback;
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
