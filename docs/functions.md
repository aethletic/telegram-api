# Functions (short aliases)

#### `bot([$token = null, $config = null]) : Bot`

Returns a `Bot` object if called with empty parameters, otherwise, if parameters are passed, it initializes the bot.

This is short alias for `Bot::getInstance()` method.

**Examples:**

```php
// Init bot without config parameter.
$bot = bot('1234567890:ABC_TOKEN'); 

// Init bot with configurations.
$bot = bot('1234567890:ABC_TOKEN', require './config.php'); 

// Just get bot object:
$bot = bot();

// You can also use anywhere function
bot()->sendMessage(/* something*/);
bot()->keyboard(/* something*/);
bot()->hear(/* something*/);
bot()->user()->get('username');
```

The above code is equivalent to this code:

```php
use Telegram\Bot;

$bot = Bot::getInstance()->create(); 

$bot = bot()->create('1234567890:ABC_TOKEN', require './config.php'); 

$bot = Bot::getInstance();

Bot::getInstance()->sendMessage(/* something*/);
Bot::getInstance()->keyboard(/* something*/);
Bot::getInstance()->hear(/* something*/);
Bot::getInstance()->user()->get('username');
```

> **NOTE:** Anywhere you can access the original Bot object using the method `Bot::getInstance` or `bot()` function.
> 
> **NOTE:** Class `Bot` is the singleton.

---

#### `update($key = null, $default = null)`
Returns an `update` [collection](https://laravel.com/docs/8.x/collections) if you pass empty parameters, otherwise, if you pass parameters, it will return the result of the update selection.

This is short alias for `$bot->update()` method.

> **NOTE:** `$key` support dot notation.

```php
$userId = update('*.from.id');
$chatId = update('*.chat.id');
$updateId = update('update_id');
$updateId = update()->get('update_id');
```

> **NOTE:** `update()` return object of [Laravel Collection](https://laravel.com/docs/8.x/collections).

---

#### `say($text, $keyboard = null, $extra = [])`

This is short alias for `$bot->say()` method.

You can see [examples here](https://github.com/aethletic/telegram-api/blob/master/docs/telegram-methods.md#saytext-keyboard--null-extra----response)

---

#### `reply($text, $keyboard = null, $extra = [])`

This is short alias for `$bot->reply()` method.

You can see [examples here](https://github.com/aethletic/telegram-api/blob/master/docs/telegram-methods.md#replytext-keyboard--null-extra----response)

---

#### `notify($text, $showAlert = false, $extra = [])`

This is short alias for `$bot->notify()` method.

You can see [examples here](https://github.com/aethletic/telegram-api/blob/master/docs/telegram-methods.md#notifytext-showalert--false-extra----response)

---

#### `action($action = 'typing', $extra = [])`

This is short alias for `$bot->action()` method.

You can see [examples here](https://github.com/aethletic/telegram-api/blob/master/docs/telegram-methods.md#diceemoji---keyboard--null-extra----response)

---

#### `dice($emoji = '🎲', $keyboard = null, $extra = [])`

This is short alias for `$bot->dice()` method.

You can see [examples here](https://github.com/aethletic/telegram-api/blob/master/docs/telegram-methods.md#actionaction--typing-extra----bot)

---

#### `get($key = null, $default = null)`
This is just short alias for `update()` alias.

---

#### `config($key = null, $default = null)`

Returns an object of the `config` [collection](https://laravel.com/docs/8.x/collections). if you not pass parameters, otherwise, if you pass parameters, returns the selected value.

This is short alias for `$bot->config()` method.

> **NOTE:** `$key` support dot notation.

```php
$username = config('bot.username');
$enabled = config('database.enable');
$databaseCfg = config()->get('database');
```

> **NOTE:** `config()` return object of [Laravel Collection](https://laravel.com/docs/8.x/collections).
> 
---

#### `keyboard($keyboard = false, $oneTime = false, $resize = true)`

Show keyboard in chat.

This is short alias for `$bot->keyboard()` method.

```php 
$bot->say('Hello user!', keyboard([
    [
        'Hello bot!'
    ]
]));

// equal to
$bot->say('Hello user!', $bot->keyboard([
    [
        'Hello bot!'
    ]
]));
```

---

#### `keyboard_hide()`

Hide keyboard in chat.

This is short alias for `$bot->keyboard(false)` and `$bot->keyboard->hide()` method.

```php 
$bot->say('...', keyboard_hide());

// equal to
$bot->say('...', $bot->keyboard(false));
```

---

#### `keyboard_set($keyboards = [])`

Set keyboards for later reuse in different places.

This is short alias for `$bot->keyboard->set()` method.

> **NOTE:** Overwrites all previously added keyboards.

```php 
$keyboards = [
    'numbers' => [
        ['7', '8', '9'],
        ['4', '5', '6'],
        ['1', '2', '3'],
             ['0'],
    ],
    'inline' => [
        [
            ['text' => 'My Button 1', 'callback_data' => 'callback_name_1'],
            ['text' => 'My Button 2', 'callback_data' => 'callback_name_2'],
        ]
    ]
];

keyboard_set($keyboards);

// and use it
$bot->say('...', keyboard('numbers'));
$bot->say('...', keyboard('inline'));
```

---

#### `keyboard_add($keyboards = [])`

Add (merge with current) keyboards for later reuse in different places.

This is short alias for `$bot->keyboard->add()` method.

```php 
$keyboards = [
    'numbers' => [
        ['7', '8', '9'],
        ['4', '5', '6'],
        ['1', '2', '3'],
             ['0'],
    ],
    'inline' => [
        [
            ['text' => 'My Button 1', 'callback_data' => 'callback_name_1'],
            ['text' => 'My Button 2', 'callback_data' => 'callback_name_2'],
        ]
    ]
];

keyboard_add($keyboards);

// and use it
$bot->say('...', keyboard('numbers'));
$bot->say('...', keyboard('inline'));
```

---

#### `plural($n, $forms)`

Pluralization for Russian language.

This is short alias for `$bot->helper()->plural()` and `helper()->plural()` methods.

> **NOTE:** For English you can use `$bot->helper()->pluralEng()` or `helper()->pluralEng()` methods.

```php 
$bot->say("У меня есть 3 " . plural(3, ['арбуз', 'арбуза', 'арбузов'])); // У меня есть 3 арбуза

$bot->say("У меня есть 21 " . plural(21, ['арбуз', 'арбуза', 'арбузов'])); // У меня есть 1 арбуз

$bot->say("У меня есть 16 " . plural(16, ['арбуз', 'арбуза', 'арбузов'])); // У меня есть 16 арбузов
```

---

#### `lang($code, $replace = null)`

Get localization messages.

This is short alias for `$bot->lang(...)` and `$bot->lang()->get(...)` methods.

```php 
$bot->say(lang('HELLO_WORLD'));

$bot->say(lang('HELLO_NAME', [
    '{name}' => user()->get('fullname'),
]));
```

```php
// file: /localiztion/en.php

return [
    'HELLO_WORLD' => 'Hello World!',
    'HELLO_NAAME' => 'Hello {name}!',
];
```

---

#### `helper()`

Return helper class.

This is short alias for `$bot->helper()` method.

```php 
$name = helper()->random(['Batman', 'Superman', 'Durov']);
```

---

#### `store($key = null, $value = null)`

Returns an object of the `Store` class if you pass empty parameters, otherwise it will set a new value if you pass two parameters, or return the selected value if you pass only the first parameter.

This is short alias for `$bot->store()` method.

```php 
$value = store('myKey'); // will return the stored value
store('myKey', 'newValue'); // will save the new value with key `myKey`
$store = store(); // return object of `Store`
$hasKey = store()->has('myKey'); // chain style
```

---

#### `user($userId = null)`

Returns an object of the `User` class if you pass an empty parameter, otherwise, if you pass the `$userId`, it will return a [collection](https://laravel.com/docs/8.x/collections) from the database with this user **(only data from the database)**

This is short alias for `$bot->user()` method.

> **WARNING:** Works only if the database is connected!
>
> See in config `database` section.
 
```php 
$userData = user(123456789);
$username = user()->get('username');
$userIsBanned = user()->get('banned');
```

---

#### `db($table = null)`

Returns a [database object](https://laravel.com/docs/8.x/queries) if passed an empty parameter, otherwise returns a database object with the selected table.

This is short alias for `$bot->db()` method.

> **WARNING:** Works only if the database is connected!
>
> See in config `database` section.
 
```php 
$users = db('users')->get();
$users = db()->table('users')->get();
```

---

#### `log($data = false, $type = 'info')`

Returns a `Log` object if you pass empty parameters, otherwise it will write a new log if you pass parameters.

This is short alias for `$bot->log()` method.

> **NOTE:** You should enable logging in bot confguretions (`log.enable` section).
 
```php 
log('some data for log'); 

log(['some' => 'data', 'for' => 'store'], 'INFO'); // array encoded as json

log()->write('some data for log', 'WARNING'); 
```

---

#### `upload_file($path = false)`

Use this function when you want to send a file from the server.

This is short alias for `$bot->helper()->upload(...)` and `helper()->upload(...)` methods.
 
```php 
$bot->sendDocument($chatId, upload_file('./document.pdf')); 
$bot->sendPhoto($chatId, upload_file('./photo.jpg')); 
```


