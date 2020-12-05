<?php 

namespace Telegram;

use Exception;

class Localization 
{
    private $language;
    private $default;
    private $bot;
    private $data = [];

    public function __construct()
    {
        $this->bot = Bot::getInstance();
    }

    public function setLanguage(string $language = null)
    {
        $this->language = $language ?? $this->language;
        return $this;
    }

    public function setDefault(string $default = null)
    {
        $this->default = $default ?? $this->default;
        return $this;
    }

    public function load($file = null)
    {
        if ($file) {
            $this->data = require_once $file;
            return $this;
        } 
        
        $path = rtrim($this->bot->config('localization.dir', false), '\/');
        
        if (!$path) {
            return $this;
        }

        $file = "{$path}/{$this->language}.php";

        if (!file_exists($file)) {
            return $this;
        }

        $this->data = require $file;

        return $this;
    }

    public function get($code, $replace = null)
    {
        if (!array_key_exists($code, $this->data)) {
            return;
        }

        $text = $this->data[$code];
        return $replace ? strtr($text, $replace) : $text;
    }
}