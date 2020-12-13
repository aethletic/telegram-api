<?php 

$bot->setDefaultAnswer(function () {
    say('default answer');
});

$bot->setDefaultMessageAnswer(function () {
    say('message default answer');
});

$bot->setDefaultCommandAnswer(function () {
    say('command default answer');
});

$bot->setDefaultCallbackAnswer(function () {
    say('callback default answer');
});