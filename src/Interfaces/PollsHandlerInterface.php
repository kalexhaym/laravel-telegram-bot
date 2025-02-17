<?php

namespace Kalexhaym\LaravelTelegramBot\Interfaces;

interface PollsHandlerInterface
{
    /**
     * @param array $data
     *
     * @return void
     */
    public function execute(array $data): void;
}
