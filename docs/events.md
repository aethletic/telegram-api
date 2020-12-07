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

The first parameter `$data`, is a string or array that supports **dot notation** and **regex**.

> **NOTE:** `Regex` available only as value and `dot notation` available only as key.

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

Also, for case insensitive `hello`, you can use regex pattern like:
```php
$bot->on(['*.text' => '/^hello$/i'], fn() => say('Hello World 🌎'));
```

> **NOTE:** You can use any regex pattern, just do it.

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