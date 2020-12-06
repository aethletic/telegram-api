# Telegram Bot Api Library

### Example
Create your first `Hello World` bot:
```php
require './vendor/autoload.php';

$bot = bot('1234567890:ABC_TOKEN');

$bot->longpoll(function ($bot) {
    $bot->hear('Hello', fn () => say('Hello World 👋'));
});
```

Run bot, type in terminal:
```bash 
$php hello_world.php
```

Now, open your bot in Telegram and send message `Hello`.

**More awesome examples see [here](https://google.ru).**