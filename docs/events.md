# Events

This is simple example of event:
```php 
$bot->on(['message.text' => 'Hello'], fn() => say('Hello World 🌎'));
```
```php 
$bot->on('message.sticker', fn() => say('I love stickers! ❤'));
```
```php 
$bot->on('message.voice', fn() => say('Whoops! I cannot yet listen 🙄'));
```
