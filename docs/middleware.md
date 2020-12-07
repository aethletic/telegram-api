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

### `addMiddleware(string $name, $function) : void`

The function or method of the class that is passed as the second parameter must return `TRUE` or `FALSE`.

`TRUE` - if passed successfully.

`FALSE` - if not passed.

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

### `middleware(string|array $name) : Bot`

Method `middleware()` should always be done before the event.

**Supported methods:**
* `on()`
* `hear()`
* `command()`
* `callback()`

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
