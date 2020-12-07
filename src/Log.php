<?php

namespace Telegram;

class Log
{
    private $dir;

    public function __construct($dir)
    {
        if (!$dir) {
            return false;
        }

        $this->dir = rtrim($dir, '/');
    }

    public function write($data = false, $type = 'info')
    {
        if (!$data) {
            return;
        }

        $date = date("d.m.Y, H:i:s");
        $data = is_array($data) ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : trim($data);
        $log = "[$date] [$type]\n$data";

        $filename = 'bot_' . date("d-m-Y") . '.log';
        file_put_contents("{$this->dir}/{$filename}", $log . PHP_EOL, FILE_APPEND);
    }
}
