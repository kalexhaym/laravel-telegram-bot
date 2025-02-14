<?php

namespace Kalexhaym\LaravelTelegramBot\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kalexhaym\LaravelTelegramBot\File;

trait Requests
{
    /**
     * @param Response $response
     *
     * @return array
     */
    private function result(Response $response): array
    {
        $result = [
            'data' => json_decode($response->getBody(), true),
        ];

        if (config('telegram.debug.http')) {
            Log::debug(json_encode($result));
        }

        return $result;
    }

    /**
     * @param string $method
     * @param array  $data
     * @param ?File  $attachment
     * @param array  $headers
     * @param int    $timeout
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function post(string $method, array $data = [], ?File $attachment = null, array $headers = [], int $timeout = 30): array
    {
        $request = Http::timeout($timeout)
            ->withHeaders($headers);

        if (! empty($attachment)) {
            $attachment = $attachment->get();
            $request->attach(
                $attachment['name'],
                $attachment['contents'],
                $attachment['filename'],
                $attachment['headers'] ?? []
            );
        }

        return $this->result(
            $request->post(config('telegram.api.url').config('telegram.bot.token').$method, $data)
        );
    }

    /**
     * @param string $method
     * @param array  $query
     * @param array  $headers
     * @param int    $timeout
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function get(string $method, array $query = [], array $headers = [], int $timeout = 30): array
    {
        return $this->result(
            Http::timeout($timeout)
                ->withHeaders($headers)
                ->get(config('telegram.api.url').config('telegram.bot.token').$method, $query)
        );
    }
}
