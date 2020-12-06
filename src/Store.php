<?php

namespace Telegram;

/**
 * Runtime store data.
 * Most actual for long poll.
 */
class Store
{
    private $data = [];

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function get($key)
    {
        return $this->has($key) ? $this->data[$key] : false;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function delete($key)
    {
        unset($this->data[$key]);
    }
}
