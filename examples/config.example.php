<?php

return [
    'bot' => [
        'token' => '1234567890:ABC_TOKEN',
        'name' => 'MyBot',
        'username' => 'MyTelegram_bot',
        'handler' => 'https://example.com/bot.php',
        'version' => '1.0.0',
    ],
    'general' => [
        'timezone' => 'UTC',
        'spam_timeout' => 1,
        'max_system_load' => 1,
    ],
    'admin' => [
        'list' => [
            'aethletic' => 'password',
            '436432850' => 'password',
        ]
    ],
    'telegram' => [
        'parse_mode' => 'html',
        'safe_callback_method' => 'encode',
    ],
    'database' => [
        'enable' => false,
        'driver' => 'mysql',
        'sqlite' => [
            'database' => '/path/to/database.sqlite',
        ],
        'mysql' => [
            'host'      => 'localhost',
            'database'  => 'telegram',
            'username'  => 'mysql',
            'password'  => 'mysql',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ],
        'collect_statistics' => true,
        'user_auto_update' => [
            'enable' => true,
            'method' => 'before',
        ],
        'user_fields' => [],
    ],
    'cache' => [
        'enable' => false,
        'driver' => 'memcached',
        'memcached' => [
            'host'  => 'localhost',
            'port' => '11211',
        ],
        'redis' => [
            'host'  => '127.0.0.1',
            'port' => '6379',
        ],
    ],
    'store' => [
        'driver' => 'file',
        'file' => [
            'dir' => __DIR__ . '/store',
        ],
        'database' => [],
        'ram' => [],
    ],
    'localization' => [
        'default_language' => 'ru',
        'dir' => __DIR__ . '/localization',
    ],
    'log' => [
        'enable' => false,
        'dir' => __DIR__ . '/logs',
    ],
    'components' => [
        'vendor.component_name' => [
            'enable' => true,
            'entrypoint' => __DIR__ . '/components/vendor/component_name/component.php',
        ],
    ],
];
