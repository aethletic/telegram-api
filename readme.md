# WIP: Telegram Bot Api Library

Simple way for building Telegram bots.

# No longer supported and considered abandoned.

## Example

Create your first `Hello World` bot based on Webhook:

```php
require './vendor/autoload.php';

$bot = bot('1234567890:ABC_TOKEN');
$bot->hear('Hello', fn () => say('Hello World 👋'));
$bot->run();
```

More awesome examples see [here](https://github.com/aethletic/telegram-api/tree/master/examples).

## Documentation
Documentation can be found [here](https://github.com/aethletic/telegram-api/tree/master/docs).
