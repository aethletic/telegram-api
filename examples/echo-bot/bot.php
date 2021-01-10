<?php

require __DIR__ . '/../../vendor/autoload.php';

// Init our bot
$bot = bot('1234567890:ABC_TOKEN');

// Catch all `message` and `edit_message`, because only they have `*.text` key.
$bot->on('*', function () {
    // And now, get come `text` from update and send to user
    say(update('*.text', 'default answer if *.text not exists'));
});

$bot->run();
