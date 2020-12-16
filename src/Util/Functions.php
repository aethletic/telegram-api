<?php

use Telegram\Bot;
use Telegram\Util\Helpers;

/**
 * Keyboards 
 */
if (!function_exists('keyboard')) {
    function keyboard($keyboard = false, $oneTime = false, $resize = true)
    {
        if (!$keyboard) {
            return Bot::getInstance()->keyboard->hide();
        }
        return Bot::getInstance()->keyboard->show($keyboard, $oneTime, $resize);
    }
}

if (!function_exists('keyboard_hide')) {
    function keyboard_hide()
    {
        return Bot::getInstance()->keyboard->hide();
    }
}

if (!function_exists('keyboard_add')) {
    function keyboard_add($keyboards = [])
    {
        return Bot::getInstance()->keyboard->add($keyboards);
    }
}

if (!function_exists('keyboard_set')) {
    function keyboard_set($keyboards = [])
    {
        return Bot::getInstance()->keyboard->set($keyboards);
    }
}

if (!function_exists('bot')) {
    function bot($token = null, $config = null)
    {
        return !$token && !$config ? Bot::getInstance() : Bot::getInstance()->create($token, (array) $config);
    }
}

if (!function_exists('update')) {
    function update($key = null, $default = null)
    {
        return Bot::getInstance()->update($key, $default);
    }
}

if (!function_exists('get')) {
    function get($key = null, $default = null)
    {
        return Bot::getInstance()->get($key, $default);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        return Bot::getInstance()->config($key, $default);
    }
}

if (!function_exists('say')) {
    function say($text, $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->say($text, $keyboard, $extra);
    }
}

if (!function_exists('reply')) {
    function reply($text, $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->reply($text, $keyboard, $extra);
    }
}

if (!function_exists('notify')) {
    function notify($text, $showAlert = false, $extra = [])
    {
        return Bot::getInstance()->notify($text, $showAlert, $extra);
    }
}

if (!function_exists('action')) {
    function action($action = 'typing', $extra = [])
    {
        return Bot::getInstance()->action($action, $extra);
    }
}

if (!function_exists('dice')) {
    function dice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->dice($emoji, $keyboard, $extra);
    }
}

if (!function_exists('plural')) {
    function plural($n, $forms)
    {
        return (new Helpers)->plural($n, $forms);
    }
}

if (!function_exists('lang')) {
    function lang($code, $replace = null)
    {
        return Bot::getInstance()->lang($code, $replace);
    }
}

if (!function_exists('helper')) {
    function helper()
    {
        return Bot::getInstance()->helper();
    }
}

if (!function_exists('store')) {
    function store($key = null, $value = null)
    {
        if ($key && is_null($value)) {
            return Bot::getInstance()->store()->get($key);
        }

        if ($key && !is_null($value)) {
            return Bot::getInstance()->store()->set($key, $value);
        }

        return Bot::getInstance()->store();
    }
}

if (!function_exists('state')) {
    function state($name = null, $data = false)
    {
        if ($name || $data) {
            return Bot::getInstance()->state()->set($name, $data);
        }

        return Bot::getInstance()->state();
    }
}

if (!function_exists('user')) {
    function user($userId = null)
    {
        return Bot::getInstance()->user($userId);
    }
}

if (!function_exists('db')) {
    function db($table = null)
    {
        return Bot::getInstance()->db($table);
    }
}

if (!function_exists('log')) {
    function log($data = false, $type = 'info')
    {
        return $data ? Bot::getInstance()->log()->write($data, $type) : Bot::getInstance()->log();
    }
}

if (!function_exists('upload_file')) {
    function upload_file($path = false)
    {
        return Bot::getInstance()->helper()->upload($path);
    }
}
