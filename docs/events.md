# Events

This is simple example of event:
```php 
$bot->on(['message.text' => 'Hello'], fn() => say('Hello World 🌎'));
```

Few? Some more examples:

```php 
$bot->on('message.sticker', fn() => say('I love stickers! ❤'));
```
```php 
$bot->on('message.voice', fn() => say('Whoops! I cannot yet listen 🙄'));
```

```php 
$bot->on(['message.voice', 'message.sticker'], fn() => say('I love stickers and voices!'));
```

## Methods

#### `on(string|array $data, $function) : void`

This is a universal and very flexible method, based on this method events are built such as `hear`, `command` and `callback`.

The first parameter `$data`, is a string or array that supports **dot notation** and **regular expressions**.

> **NOTE:** `regular expressions` available only as value and `dot notation` available only as key.

**Examples:**

Execute event if the update has a `sticker` key in `message` array.
```php
$bot->on('message.sticker', fn() => say('I love stickers! ❤'));
```

Execute event if the update has a `sticker` or `voice` key in `message` array.
```php
$bot->on(['message.voice', 'message.sticker'], fn() => say('I love stickers and voices!'));
```

Execute event if the update has a `text` key with value `Hello` in **any** array.
```php
$bot->on(['*.text' => 'Hello'], fn() => say('Hello World 🌎'));
```

> **NOTE:** Symbol *\** means any key.

Also, for case insensitive `hello`, you can use regular expressions pattern like:
```php
$bot->on(['*.text' => '/^hello$/i'], fn() => say('Hello World 🌎'));
```

> **NOTE:** You can use any regular expressions pattern, just do it.

If you need to execute one event for multiple similar text, then you need to pass an array:
```php
$bot->on([
        ['*.text' => '/hello/i'],
        ['*.text' => '/sup bro/i'],
    ], function () {
        say('Hello World 🌎');
    });
```

You can combine:
```php
$bot->on([
        ['*.text' => '/hello/i'], // only array for comparison value
        'message.sticker', // string support
        ['message.voice'], // array support
    ], function () {
        /* do something */
    });
```

---

#### `hear(string|array $text, $function) : void`
This method only catches **text messages** and **edited messages**.

> **NOTE:** This method supports regular expressions.

**Examples:**

```php
$bot->hear('Hello', function () use ($bot) {
    $bot->say('Hello 👋');
});
```

With regular expression:
```php
$bot->hear('/hello/i', function () use ($bot) {
    $bot->reply('Hello again 👋');
});
```

Catch multiple messages:
```php
$bot->hear(['/hello/i', 'sup bro'], function () {
    say('Hello again 👋');
});
```

---

#### `command(string|array $command, $function) : void`
This method only catches **command messages**.

> **NOTE:** This method supports regular expressions.

**Examples:**

```php
$bot->command('/keyboard', function () use ($bot) {
    $bot->say('Keyboard here 👇', keyboard([
        ['Button']
    ]));
});
```

With regular expression:
```php
$bot->hear('/\/keyboard/i', function () use ($bot) {
    $bot->reply('Keyboard here 👇', $bot->keyboard([
        ['Button']
    ]));
});
```

Catch multiple messages:
```php
$bot->hear(['/\/keyboard/i', '/buttons'], function () {
    say('Keyboard here 👇', keyboard([
        ['Button 1', 'Button 2']
    ]));
});
```

---

#### `hear(string|array $callback_data, $function) : void`
This method only catches **callback query**.

> **NOTE:** This method supports regular expressions.

**Examples:**

First, let's agree that we send an inline keyboard:

```php
say('Inline keyboard', keyboard([
    [
        ['text' => 'Video 🎬', 'callback_data' => 'show_video'],
        ['text' => 'Image 🎨', 'callback_data' => 'show_image'],
    ]
]));
```

Now, we can start handling key presses:

```php
$bot->callback('show_video', function () use ($bot) {
    $userId = user()->get('user_id');
    $bot->sendVideo($userId, upload_file('./video.mp4'));
});
```

With regular expression:
```php
$bot->hear('/show_image/i', function () use ($bot) {
    $userId = user()->get('user_id');
    $bot->sendPhoto($userId, upload_file('./gf_nudes.jpg'), 'Enjoy, heh');
});
```

Catch multiple messages:
```php
$bot->hear(['/show_video/i', 'show_image'], function () {
    $userId = user()->get('user_id');
    bot()->sendVideo($userId, upload_file('./video.mp4'));
    bot()->sendPhoto($userId, upload_file('./gf_nudes.jpg'), 'Enjoy, heh');
});
```