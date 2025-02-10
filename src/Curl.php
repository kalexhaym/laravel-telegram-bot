<?php

namespace Kalexhaym\LaravelTelegramBot;

class Curl
{
    /**
     * @param $curl
     *
     * @return array
     */
    protected function result($curl): array
    {
        $result = [
            'data' => curl_exec($curl),
        ];

        if (config('telegram.curl.error')) {
            $result['error'] = curl_error($curl);
        }

        if (config('telegram.curl.info')) {
            $result['info'] = curl_getinfo($curl);
        }

        $result['data'] = json_decode($result['data'], true);

        return $result;
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $header
     *
     * @return array
     */
    protected function post(string $url, array $data, array $header = []): array
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        $result = $this->result($curl);

        curl_close($curl);

        return $result;
    }

    /**
     * @param string $url
     * @param array $header
     *
     * @return array
     */
    protected function get(string $url, array $header = []): array
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        $result = $this->result($curl);

        curl_close($curl);

        return $result;
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $header
     *
     * @return array
     */
    protected function put(string $url, array $data, array $header = []): array
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        $result = $this->result($curl);

        curl_close($curl);

        return $result;
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $header
     *
     * @return array
     */
    protected function delete(string $url, array $data, array $header = []): array
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        $result = $this->result($curl);

        curl_close($curl);

        return $result;
    }
}
