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
            echo 'Error code ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            var_dump($this->getRequestUrl($method), $params);
        } else {
            return collect(json_decode(json_encode($this->curl->response), true));
        }
    }

    private function buildRequestParams(array $params = [], array $keyboard = null, array $extra = [])
    {
        if ($keyboard) {
            $params['reply_markup'] = $keyboard;
        }

        $params['parse_mode'] = $this->config('telegram.parse_mode', 'html');

        return array_merge($params, $extra);
    }

    private function getRequestUrl($method = null)
    {
        return self::TELEGRAM_API_URL . "{$this->token}/{$method}";
    }
}