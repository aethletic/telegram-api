<?php

namespace Telegram\Traits;

trait Request
{

    public function request(string $method, array $params = [], bool $isFile = false)
    {
        if ($isFile) {
            $this->curl->setHeader('Content-Type', 'multipart/form-data');
        } else {
            $this->curl->setHeader('Content-Type', 'application/json');
        }

        $this->curl->post($this->getRequestUrl($method), $params);

        if ($this->curl->error) {
            echo "\n\nError code " . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n\n";
            var_dump($this->getRequestUrl($method), $params);
        } else {
            return collect(json_decode(json_encode($this->curl->response), true));
        }
    }

    private function buildRequestParams($params = [], $keyboard = null, $extra = [])
    {
        if ($keyboard) {
            $params['reply_markup'] = $keyboard;
        }

        $params['parse_mode'] = $this->config('telegram.parse_mode', 'html');

        return array_merge($params, (array) $extra);
    }
}
