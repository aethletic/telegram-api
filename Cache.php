<?php

namespace Telegram;

class Cache
{
    public function __invoke($config)
    {
        if (!$config) {
            return;
        }

        $driver = $config['driver'];

        switch ($driver) {
            case 'memcached':
                if (!class_exists('Memcached')) {
                    return false;
                }
                $cache = new \Memcached();
                $cache->addServer($config[$driver]['host'], $config[$driver]['port']);
                break;

            case 'redis':
                if (!class_exists('Redis')) {
                    return false;
                }
                $cache = new \Redis();
                $cache->connect($config[$driver]['host'], $config[$driver]['port']);
                break;

            default:
                $cache = false;
                break;
        }

        return $cache;
    }
}
