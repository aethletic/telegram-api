<?php

namespace Telegram\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Telegram\Bot;

class Connector
{
    public static function create()
    {
        $bot = Bot::getInstance();

        $capsule = new Capsule;

        $driver = $bot->config('database.driver');
        $config = $bot->config('database.' . $driver);
        $config['driver'] = $driver;

        $capsule->addConnection($config->toArray());
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }
}
