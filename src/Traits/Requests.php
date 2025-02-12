<?php

namespace Kalexhaym\LaravelTelegramBot\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        if (config('telegram.http.debug')) {
            Log::debug(json_encode($result));
        }

        return $result;
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @param int    $timeout
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function post(string $url, array $data, array $headers = [], int $timeout = 30): array
    {
        return $this->result(
            Http::timeout($timeout)
                ->withHeaders($headers)
                ->post($url, $data)
        );
    }

    /**
     * @param string $url
     * @param array  $query
     * @param array  $headers
     * @param int    $timeout
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function get(string $url, array $query = [], array $headers = [], int $timeout = 30): array
    {
        return $this->result(
            Http::timeout($timeout)
                ->withHeaders($headers)
                ->get($url, $query)
        );
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @param int    $timeout
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function put(string $url, array $data, array $headers = [], int $timeout = 30): array
    {
        return $this->result(
            Http::timeout($timeout)
                ->withHeaders($headers)
                ->put($url, $data)
        );
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @param int    $timeout
     *
     * @throws ConnectionException
     *
     * @return array
     */
    public function delete(string $url, array $data, array $headers = [], int $timeout = 30): array
    {
        return $this->result(
            Http::timeout($timeout)
                ->withHeaders($headers)
                ->delete($url, $data)
        );
    }
}
