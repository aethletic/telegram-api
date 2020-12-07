# Telegram Methods

> **NOTE:** The `$extra` parameter contains all additional Telegram methods that are not passed directly to the method. Method parameters can also be overwritten in this array.

---

#### `setWebhook([string $url, array $extra = ['max_connections' => 100]]) : Response`
Method in the Telegram documentation [**here**](https://core.telegram.org/bots/api#setwebhook).

```php
// if handler url is set  in config `bot.handler`
$bot->setWebhook();
```
```php
$bot->setWebhook('https://example.com/webhook/bot.php');
```
```php
$bot->setWebhook('https://example.com/webhook/bot.php', [
    'max_connections' => 100,
]);
```

---

#### `deleteWebhook() : Response`
Method in the Telegram documentation [**here**](https://core.telegram.org/bots/api#deletewebhook).
```php
$bot->deleteWebhook();
```

---

#### `getWebhookInfo() : Response`
Method in the Telegram documentation [**here**](https://core.telegram.org/bots/api#getwebhookinfo).
```php
$bot->getWebhookInfo();
```

---

#### `getUpdates(int $offset = 0, int $limit = 100, array $extra = []) : Response`
Method in the Telegram documentation [**here**](https://core.telegram.org/bots/api#getupdates).
```php
$bot->getUpdates();
```

```php
$updateId = -1;
while (true) {
    foreach ($bot->getUpdates($updateId + 1, 1)->get('result') as $update) {
        $bot->setUpdate($update);
        $updateId = $this->update('update_id', -1);

        /* do something */
    }
}
```

---

#### `say($text, [$keyboard = null, $extra = []]) : Response`

Just send a text message to the current chat where the update came from.

Short alias for `sendMessage()` method.

> **NOTE:** This method also support function `say()`.

```php
$bot->say('Hello!');
```
```php
$bot->say('Hello!', keyboard('my_keyboard'), [
    'parse_mode' => 'markdown',
]);
```
```php
say('Hello!');
```
```php
say('Hello!', keyboard('my_keyboard'), [
    'parse_mode' => 'markdown',
]);
```

---

#### `reply($text, [$keyboard = null, $extra = []]) : Response`

Send a reply to the last message from the user to the current chat where the update came from.

Short alias for `sendMessage()` method with `reply_to_message_id` parameter.

> **NOTE:** This method also support function `reply()`.

```php
$bot->reply('Ok, hello!');
```
```php
$bot->reply('Ok, hello!', keyboard('my_keyboard'), [
    'parse_mode' => 'markdown',
]);
```
```php
reply('Ok, hello!');
```
```php
reply('Ok, hello!', keyboard('my_keyboard'), [
    'parse_mode' => 'markdown',
]);
```

---

#### `notify($text, [$showAlert = false, $extra = []]) : Response`

Sends a popup message or modal box with text to the current chat where the update came from.

Short alias for `answerCallbackQuery()` method.

> **NOTE:** This method also support function `notify()`.

> **NOTE:** This method work only for incoming `callback_query` update.

```php
$bot->notify('I catch your callback!');
```
```php
notify('I catch your callback!', true);
```

---

#### `action([$action = 'typing', $extra = []]) : Bot`

Sends chat actions to the current chat where the update came from.

Short alias for `sendChatAction()` method.

> **NOTE:** This method also support function `action()`.
> **NOTE:** This method support chain style.

```php
$bot->action('typing')
    ->say('Yes, I was typing...');
```
```php
$bot->action('upload_photo')
    ->sendPhoto(...);
```

**Available actions:**

`typing` for text messages, `upload_photo` for photos, `record_video` or `upload_video` for videos, `record_voice` or `upload_voice` for `voice notes`, `upload_document` for general files, `find_location` for location data, `record_video_note` or `upload_video_note` for video notes

---

#### `dice($emoji = '🎲', [$keyboard = null, $extra = []]) : Response`

Sends dice to the current chat where the update came from.

Short alias for `sendDice()` method.

> **NOTE:** This method also support function `dice()`.

```php
$bot->dice('🎲');
```
```php
dice('🎲');
```
```php
$this->dice('dice'); // 🎲
$this->dice('darts'); // 🎯
$this->dice('basketball'); // 🏀
$this->dice('football'); // ⚽️
$this->dice('slots'); // 🎰
$this->dice('777'); // 🎰
```

---

#### `isActive($chatId, [$action = 'typing', $extra = []]) : boolean`

Checking for the presence of user activity, if the user has blocked the bot, will return `false`, otherwise `true`.

> **NOTE:** This method use `sendChatAction()` method for check.

```php
$active = $bot->isActive(1234567890);
```

---

#### `getMe() : Response`

---

#### `sendChatAction($chatId, $action = 'typing', $extra = []) : Response`

---

#### `sendMessage($chatId, $text, $keyboard = null, $extra = []) : Response`

---

#### `forwardMessage($chatId, $fromChatId, $messageId, $extra = []) : Response`

---

#### `sendReply($chatId, $messageId, $text = '', $keyboard = null, $extra = []) : Response`

---

#### `sendPhoto($chatId, $photo, $caption = '', $keyboard = null, $extra = []) : Response`

---

#### `sendAudio($chatId, $audio, $caption = '', $keyboard = null, $extra = []) : Response`

---

#### `sendDocument($chatId, $document, $caption = '', $keyboard = null, $extra = []) : Response`

---

#### `sendAnimation($chatId, $animation, $caption = '', $keyboard = null, $extra = []) : Response`

---

#### `sendVideo($chatId, $video, $caption = '', $keyboard = null, $extra = []) : Response`

---

#### `sendVideoNote($chatId, $videoNote, $keyboard = null, $extra = []) : Response`

---

#### `sendSticker($chatId, $sticker, $keyboard = null, $extra = []) : Response`

---

#### `sendVoice($chatId, $voice, $caption = '', $keyboard = null, $extra = []) : Response`

---

#### `sendMediaGroup($chatId, $media, $extra = []) : Response`

---

#### `sendLocation($chatId, $latitude, $longitude, $keyboard = null, $extra = []) : Response`

---

#### `sendDice($chatId, $emoji = '🎲', $keyboard = null, $extra = []) : Response`

---

#### `getUserProfilePhotos($userId, $offset = 0, $limit = 100) : Response`

---

#### `getFile($fileId) : Response`

---

#### `saveFile($fileUrl, $savePath) : string`

Support name generator:

```php
$bot->saveFile($fileUrlFormGetFileMethod, './storage/{basename}');
$bot->saveFile($fileUrlFormGetFileMethod, './storage/{md5}.{extension}');
$bot->saveFile($fileUrlFormGetFileMethod, './storage/{random_name}');
$bot->saveFile($fileUrlFormGetFileMethod, './storage/{time}.{extension}');
```

Available tags:
* `{basename}` - orignal file name from response;
* `{extension}` - file extension like `jpg`, `mp3`, etc... **without dot**;
* `{time}` - current timestamp;
* `{md5}` - result of `md5(time().mt_rand())`;
* `{random_name}` - result of `md5(time().mt_rand()).".{$extension}"`;

---

#### `kickChatMember($chatId, $userId, $untilDate) : Response`

---

#### `unbanChatMember($chatId, $userId) : Response`

---

#### `restrictChatMember($chatId, $userId, $permissions, $untilDate = false) : Response`

---

#### `setMyCommands($commands) : Response`

---

#### `getMyCommands() : Response`

---

#### `editMessageText($messageId, $chatId, $text = '', $keyboard = null, $extra = []) : Response`

---

#### `editMessageCaption($messageId, $chatId, $caption = '', $keyboard = null, $extra = []) : Response`

---

#### `editMessageMedia($messageId, $chatId, $media, $keyboard = null, $extra = []) : Response`

---

#### `editMessageReplyMarkup($messageId, $chatId, $keyboard = null, $extra = []) : Response`

---

#### `deleteMessage($messageId, $chatId) : Response`

---

#### `getStickerSet($name) : Response`

---

#### `uploadStickerFile($userId, $pngSticker) : Response`

---

#### `sendGame($chatId, $gameShortName, $keyboard = null, $extra = []) : Response`

---

#### `answerCallbackQuery($extra = []) : Response`

---

#### `answerInlineQuery($results = [], $extra = []) : Response`
