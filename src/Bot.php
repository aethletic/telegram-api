<?php

namespace Telegram;

use Curl\Curl;
use Telegram\Exception\BotException;
use Telegram\Util\Helpers;
use Telegram\Traits\Request;
use Telegram\Traits\Telegram;
use Telegram\Traits\Router;
use Telegram\Traits\Events;
use Telegram\Database\Connector;

class Bot
{
    use Request;
    use Router;
    use Telegram;
    use Events;

    private static $instance = null;

    /**
     * Telegram Bot Token.
     *
     * @var string
     */
    private $token;

    /**
     * Bot configurations.
     *
     * @var Collection
     */
    private $config = [];

    /**
     * Incoming update.
     *
     * @var Collection
     */
    private $update = false;

    /**
     * Curl http client.
     *
     * @var Curl
     */
    private $curl;

    private const TELEGRAM_API_URL = 'https://api.telegram.org/bot';
    private const TELEGRAM_API_FILE = 'https://api.telegram.org/file/bot';

    /**
     * @var Helpers
     */
    public $helper;

    /**
     * @var Keyboard
     */
    public $keyboard;

    private $mappedMethods = [];

    /**
     * @var Localization
     */
    private $lang;

    /**
     * @var Illuminate\Database\Capsule\Manager
     */
    private $db;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var State
     */
    private $state;

    /**
     * @param string $token
     * @param array $config
     */
    public function create(string $token = null, array $config = [])
    {
        if (!$token) {
            new BotException("Please, pass your bot token.");
        }

        $this->token = $token;
        $this->config = collect($this->config)->merge($config);

        date_default_timezone_set($this->config('general.timezone', 'UTC'));

        // регистрируем обязательные классы
        $this->curl = new Curl();
        $this->helper = new Helpers();
        $this->keyboard = new Keyboard();
        
        $this->setUpdate();
        $this->decodeCallback();
        
        // база данных
        if ($this->config('database.enable')) {
            $this->db = Connector::create();
            // TODO вынести все в run чтобы при вебхуке не тормозило
            if ($this->config('database.collect_statistics') && $this->isUpdate()) {
                Statistics::collect();
            }

            if ($this->isUpdate()) {
                $this->user = new User($this->update('*.from.id'), true);
                $this->state = new State;
            }
        }

        // локализация
        $this->lang = $this->isUpdate() ? (new Localization())->autoload() : new Localization();

        // cache
        if ($this->config('cache.enable')) {
            $this->cache = (new Cache)($this->config('cache'));
        }

        // log
        if ($this->config('log.enable')) {
            $this->log = new Log($this->config('log.dir'));
        }

        // store в самом конце т.к. он может зависеть от бд, в перспективе от кеша?
        $this->store = new Store($this->config()->get('store'));

        $this->loadComponents();

        return $this;
    }

    private function __clone()
    {
    }
    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function store($key = null, $value = null)
    {
        if ($key && is_null($value)) {
            return $this->store->get($key);
        }

        if ($key && !is_null($value)) {
            return $this->store->set($key, $value);
        }

        return $this->store;
    }

    public function db($table = null)
    {
        if (!$this->db) {
            return false;
        }
        return !$table ? $this->db : $this->db->table($table);
    } 

    public function state($name = null, $data = null)
    {
        if ($name || $data) {
            return $this->state->set($name, $data);
        }

        return $this->state;
    }

    public function user($userId = null)
    {
        return !$userId ? $this->user : $this->user->getDataById($userId);
    }

    public function cache()
    {
        return $this->cache;
    }

    public function log()
    {
        return $this->log;
    }

    public function lang($code = null, $replace = null)
    {
        return !$code ? $this->lang : $this->lang->get($code, $replace);
    }

    public function curl()
    {
        return new Curl;
    }

    public function keyboard($keyboard = false, $oneTime = false, $resize = true)
    {
        if (!$keyboard) {
            return $this->keyboard->hide();
        }
        return $this->keyboard->show($keyboard, $oneTime, $resize);
    }

    private function loadComponents()
    {
        $components = $this->config()->get('components');

        if (!$components) {
            return false;
        }

        foreach ($components as $key => $component) {
            if (!$component['enable'] ?? null) {
                continue;
            }

            if (file_exists($component['entrypoint'] ?? null)) {
                require_once $component['entrypoint'];
            }
        }
    }

    /**
     * Return instance of Collection if call like config()
     * Pass params for get value from array use dot notation.
     * Can call as `config()` function.
     * 
     * @param string|null $key
     * @param mixed $default
     * @return Collection|mixed
     */
    public function config($key = null, $default = null)
    {
        if (!$key && !$default) {
            return $this->config;
        }

        $data = data_get($this->config->toArray(), $key, $default);
        $data = is_array($data) ? array_filter($data) : $data;
        return is_array($data) && count($data) > 1 ? collect($data) : (is_array($data) && $data !== [] ? head($data) : ($data == [] ? $default : $data));
    }

    /**
     * Return instance of Collection if call like update()
     * Pass params for get value from array use dot notation.
     * Can call as `update()` function.
     * 
     * @param string|null $key
     * @param mixed $default
     * @return Collection|mixed
     */
    public function update($key = null, $default = null)
    {
        if (!$this->isUpdate()) {
            return false;
        }

        if (!$key && !$default) {
            return $this->update;
        }

        $data = data_get($this->update->toArray(), $key, $default);
        $data = is_array($data) ? array_filter($data) : $data;
        return is_array($data) && count($data) > 1 ? collect($data) : (is_array($data) && $data !== [] ? head($data) : ($data == [] ? $default : $data));
    } 

    /**
     * Alias for `update` method.
     * Can call as `get()` function.
     *
     * @param string|null $key
     * @param mixed $default
     * @return Collection|mixed
     */
    public function get($key = null, $default = null)
    {
        return $this->update($key, $default);
    }

    /**
     * Send loading message.
     * 
     * @param array $elements Array with text, will be sent from index 0
     * @param integer|boolean $delay
     * @return boolean
     */
    public function loading(array $elements = [], $delay = 1)
    {
        $messageId = false;
        while ($element = array_shift($elements)) {
            if (!$messageId) {
                $result = say($element)->get('result');
                $messageId = $result['message_id'];
            } else {
                $this->editMessageText($messageId, $this->update('*.chat.id'), $element);
            }
            usleep(round($delay * 1000000));
        }
        return true;
    }

    private function decodeCallback()
    {
        if (!$this->isUpdate()) {
            return;
        }

        if (!$this->isCallback()) {
            return;
        }

        $method = $this->config('telegram.safe_callback_method');

        if (!$method) {
            return;
        }

        $update = $this->update()->toArray();

        $data = $update['callback_query']['data'] ?? false;

        if (!$data) {
            return;
        }

        switch (strtolower($method)) {
            case 'encode':
                $data = gzinflate(base64_decode($data));
                break;
            case 'hash':
                // code...
                break;
        }

        $update['callback_query']['data'] = $data;
        
        $this->update = collect($update);
    }

    /**
     * Just wait some time, sipport milliseconds.
     *
     * @param integer|boolean $delay
     * @return boolean
     */
    public function wait($delay = 1)
    {
        usleep(round($delay * 1000000));
        return true;
    }

    /**
     * Check update exists.
     *
     * @return boolean
     */
    public function isUpdate()
    {
        return !is_bool($this->update);
    }

    public function setUpdate($update = null, $isJson = false)
    {
        $this->startTime = microtime(true);
        
        if ($update) {
            $this->update = $isJson ? collect(json_decode($update, true)) : collect($update);
            $this->decodeCallback();
            return;
        }

        $input = file_get_contents('php://input');
        $this->update = $input ? collect(json_decode($input, true)) : false;
        $this->decodeCallback();
    }

    public function helper()
    {
        return $this->helper;
    }

    public function longpoll($func)
    {
        echo "Long polling started ..." . PHP_EOL;

        $updateId = -1;

        while (true) {
            foreach ($this->getUpdates($updateId + 1, 1)->get('result') as $update) {
                $start = microtime(true); // dev debug

                $this->setUpdate($update);
                $updateId = $this->update('update_id', -1);

                /**
                 * Перед выполнением событий
                 * Только самое необходимое
                 */
                if (!is_null($this->db)) {
                    $this->user = new User($this->update('*.from.id'), true);
                    $this->state = new State;
                }

                $this->lang = (new Localization)->autoload();

                /**
                 * Регистрация и выполнение событий
                 */
                $this->execute($func, [$this]);
                $this->run();

                /**
                 * После выполнения событий, чтобы не тормозить ответ бота
                 */
                if (!is_null($this->db)) {
                    if ($this->config('database.collect_statistics')) {
                        Statistics::collect();
                    }

                    if ($this->config('database.user_auto_update.enable') && strtolower($this->config('database.user_auto_update.method', 'after'))) {
                        $this->user()->autoUpdateUserInfo();
                    }
                }

                echo PHP_EOL . round(microtime(true) - $start, 5); // dev debug
            }
        }
    }

    private function autoLogWrite($name = 'AUTO')
    {
        if ($this->isUpdate() && $this->log) {
            $this->log()->write($this->update()->toArray(), 'AUTO');
        }
    }

    private function execute($func, $args = [])
    {
        return $func ? call_user_func_array($func, $args) : false;
    }

    private function getRequestUrl($method = null)
    {
        return self::TELEGRAM_API_URL . "{$this->token}/{$method}";
    }

    public function adminAuth($password)
    {
        if (!$this->isAdmin()) {
            return false;
        }

        $username = $this->update('*.from.username');
        $userId = $this->update('*.from.id');
        return $password == $this->config("admin.list.{$username}", $this->config("admin.list.{$userId}", false));
    }

    public function getSystemLoad()
    {
      return sys_getloadavg();
    }

    public function sendJson()
    {
        if (!$this->isUpdate()) {
          return false;
        }

        return $this->request('sendMessage',[
            'chat_id' => $this->update('*.chat.id'),
            'text' => '<code>'.json_encode($this->update->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</code>',
        ]);
    }

    public function parse($delimiter = ' ')
    {
        if ($this->isCallback()) {
            return explode($delimiter, $this->update('*.data'));
        } else if ($this->isInline()) {
            return explode($delimiter, $this->update('*.query'));
        } else if ($this->isMessage() || $this->isEditedMessage() || $this->isCommand()) {
            return explode($delimiter, $this->update('*.text'));
        }
    }

    public function time($lenght = 6)
    {
        return round(microtime(true) - $this->startTime, $lenght);
    }

    public function map($method, $func)
    {
        $this->mappedMethods[$method] = $func;
    }

    public function mapOnce($method, $func)
    {
        $this->mappedMethods[$method] = $this->execute($func);
    }
    
    public function __call($method, $args)
    {
        $tmp = $this->mappedMethods[$method];
        return is_callable($tmp) ? $this->execute($tmp, $args) : $tmp;
    }
}
