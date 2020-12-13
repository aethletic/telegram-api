<?php 

require __DIR__ . '/vendor/autload.php';

$config = require __DIR__ . '/config.php';

$bot = bot($config['bot']['token'], $config);

require __DIR__ . '/bot/keyboards.php';
require __DIR__ . '/bot/defaults.php';

