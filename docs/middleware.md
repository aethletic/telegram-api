# Middleware

This is simple example of middleware:
```php 
// register middleware
$bot->addMiddleware('admin', function () {
    return in_array(user()->get('username'), ['aethletic']);
});

// register event
$bot->middleware('admin')
    ->command('/auth', function () {
        /* do something */
    });
```

## Methods

#### `addMiddleware(string $name, $function) : void`

---

The function or method of the class that is passed as the second parameter must return `true` or `false`.

Return `true` in case the event is allowed to execute.

Return `false` in case, it is forbidden to execute the event.

**Examples:**

```php 
// passed
$bot->addMiddleware('middleware.name', function () {
    return (2*2) === 4; 
});
```

```php 
// not passed
$bot->addMiddleware('middleware.name', function () {
    return (2*2) === 5; 
});
```

#### `middleware(string|array $name) : Bot`

---

Method `middleware()` should always be done before the event.

**Supported methods:**
* `on()`
* `hear()`
* `command()`
* `callback()`

**Examples:**

```php 
$bot->middleware('middleware.name')
    ->hear(/* something */);
```

Multiple middlewares `user` and `can.edit.post`:
```php 
$bot->middleware(['user', 'can.edit.post'])
    ->hear(/* something */);
```

> **NOTE:** If middleware `user` returns `FALSE`, then the following middlewares will be skipped and the event will not be executed.

Fallback functions if middleware not passed:
```php 
$bot->middleware([
    [
        'name' => 'can.edit.post',
        'fallback' => fn() => say('❌ You cannot edit Posts.')
    ]
])->hear(/* something */);
```

Also, you can combine fallback and just names:
```php 
$bot->middleware([
    'user',
    [
        'name' => 'can.edit.post',
        'fallback' => fn() => say('❌ You cannot edit Posts.')
    ]
])->hear(/* something */);
```
