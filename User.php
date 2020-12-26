<?php

namespace Telegram;

use Illuminate\Support\Arr;

class User
{
    private $data = [];
    private $userId;
    private $bot;
    private $db;

    public $isNewUser = false;
    public $isNewVersion = false;
    public $isSpam = false;
    public $isBanned = false;
    public $isUpdated = false;

    // новое значение из апдейта при авто-обновлении юзера
    public $changedUserInfo = null;

    public function __construct($userId = false, $inserteUserIfNotExists = false)
    {
        if (!$userId) {
            $this->data = collect([]);
        }

        $this->bot = Bot::getInstance();
        $this->db = $this->bot->db();

        $this->userId = $userId;

        // Если юзер существует в БД, получаем его данные
        if ($this->exists($userId)) {
            $this->data = collect($this->getDataById($userId));
            $this->diffBotVersion();

            // check spam time and update last_message
            $diffMessageTime = time() - $this->data->get('last_message');

            $timeout = $this->bot->config('general.spam_timeout');

            if ($diffMessageTime <= $timeout) {
                $this->isSpam = $timeout - $diffMessageTime;
            } else {
                $this->update(['last_message' => time()]);
            }

            $this->isBanned = $this->data->get('banned') == 1;

            if ($this->bot->config('database.user_auto_update.enable') && strtolower($this->bot->config('database.user_auto_update.method', 'after')) == 'before') {
                $this->autoUpdateUserInfo();
            }

            return;
        }

        $this->isNewUser = true;

        if (!$inserteUserIfNotExists) {
            $this->data = collect([]);
            return;
        }

        // получаем источник откуда пришел юзер
        $source = null;
        $text = $this->bot->update('*.text');
        if ($this->bot->isCommand() && $text && stripos($text, '/start') !== false) {
            $text = explode(' ', $text);
            if (is_array($text) && count($text) > 1) {
                unset($text[0]);
                $source = implode(' ', $text);
            }
        }

        // Создаем новую запись о юзере
        $from = collect($this->bot->update('*.from'));
        $firstname = $from->get('first_name', null);
        $lastname = $from->get('last_name', null);
        $data = [
            // Общная информация
            'user_id' => $from->get('id', null), // telegram id юзера
            'active' => 1, // юзер не заблокировал бота
            'fullname' => trim("{$firstname} {$lastname}"), // имя фамилия
            'firstname' => $firstname, // имя
            'lastname' => $lastname, // фамилия
            'username' => $from->get('username', null), // telegram юзернейм
            'lang' => $from->get('language_code', $this->bot->config('localization.default_language', 'en')), // язык
            'photo' => null, // фото

            // Сообщения
            'first_message' => time(), // первое сообщение (дата регистрации) (unix)
            'last_message' => time(), // последнее сообщение (unix)
            'source' => $source, // откуда пользователь пришел (/start botcatalog)

            // Бан
            'banned' => 0, // забанен или нет
            'ban_comment' => null, // комментарий при бане
            'ban_date_from' => null, // бан действует с (unix)
            'ban_date_to' => null, // бан до (unix)

            // Стейты
            'state_name' => null, // название стейта
            'state_data' => null, // значение стейта

            // Дополнительно
            'role' => 'user', // группа юзера
            'nickname' => null, // никнейм (например для игровых ботов)
            'emoji' => null, // эмодзи/иконка (префикс)

            // Служебное
            'note' => null, // заметка о юзере
            'version' => $this->bot->config('bot.version'), // последняя версия бота с которой взаимодействовал юзер
        ];

        $data = array_merge($data, $this->bot->config()->get('database')['user_fields']);
        
        $this->db
            ->table('users')
            ->insert($data);

        $this->data = collect($data);
    }

    public function get($key, $default = false)
    {
        return $this->data->get($key, $default);
    }

    public function update($data)
    {
        return $this->updateById($this->userId, $data);
    }

    public function updateById($userId, $data)
    {
        return $this->db
            ->table('users')
            ->where('user_id', $userId)
            ->update($data);
    }

    public function exists($userId)
    {
        return $this->db
            ->table('users')
            ->where('user_id', $userId)
            ->count() > 0;
    }

    // Получить данные о юзере
    public function getDataById($userId)
    {
        if (!$this->exists($userId)) {
            return false;
        }

        return $this->db
            ->table('users')
            ->where('user_id', $userId)
            ->first();
    }

    private function diffBotVersion()
    {
        $userVersion = $this->data->get('version');
        $currentVersion = $this->bot->config('bot.version');

        $this->isNewVersion = $userVersion != $currentVersion;

        if ($this->isNewVersion) {
            $this->update(['version' => $currentVersion]);
        }
    }

    public function autoUpdateUserInfo()
    {
        $fromData = Arr::only($this->bot->update('*.from'), ['username', 'first_name', 'last_name']);
        $data = array_values(array_filter(Arr::only($this->data->toArray(), ['username', 'firstname', 'lastname'])));
        $from = array_values(array_filter($fromData));

        foreach ($data as $key => $value) {
            if ($value !== $from[$key]) {
                $this->update([
                    'username' => @$fromData['username'],
                    'firstname' => @$fromData['first_name'],
                    'lastname' => @$fromData['last_name'],
                    'active' => 1,
                ]);
                $this->isUpdated = true;
                $this->changedUserInfo = $from[$key];
                break;
            }
        }
        
        if (!$this->isUpdated && $this->get('active') == 0) {
            $this->update([
                'active' => 1,
            ]);
        }
    }

    public function getNewUserInfo()
    {
        return $this->changedUserInfo;
    }
}
