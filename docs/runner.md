# Runner

Automatic restart of the bot using a long poll if it crashes.

`run.php`:
```php
<?php 

$maxCountFails = 5;
$fails = 0;

while (true) {
    passthru('php bot.php');
    echo "-------------------------------\n";
    echo "Whoops! Bot down, restarting ...\n";
    echo "-------------------------------\n";
    if (++$fails >= $maxCountFails) break;
}

bot('123456890:ABC_TOKEN')->sendMessage('ADMIN_ID', 'Hey, I down now.');
```

File structure:
```
...
bot.php
run.php
...
```

And run bot:

```bash
php run.php
```

With using `screen`:
```bash
screen -S my_bot php run.php
```