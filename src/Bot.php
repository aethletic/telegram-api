<?php

namespace Telegram;

use Curl\Curl;
use Telegram\Exception\BotException;
use Telegram\Util\Helpers;
use Telegram\Traits\Request;
use Telegram\Traits\Telegram;
use Telegram\Traits\Router;
use Telegram\Traits\Events;

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
     * @param string $token
     */
    public function create(string $token = null, array $config = [])
    {
        if (!$token) {
            new BotException("Please, pass your bot token.");
        }

        $this->token = $token;
        $this->config = collect($this->config)->merge($config);
        $this->curl = new Curl();
        $this->helper = new Helpers();
        $this->keyboard = new Keyboard();

        if ($this->config('localization.enable')) {
            $defaultLang = $this->config('localization.default_language', 'en');
            $this->lang = (new Localization())
                ->setDefault($defaultLang)
                ->setLanguage($this->update('*.from.language_code', $defaultLang))
                ->load();
        }

        $this->setUpdate();
        
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

    public function lang($code = null, $replace = null)
    {
        return !$code ? $this->lang : $this->lang->get($code, $replace);
    }

    public function keyboard($keyboard = false, $oneTime = false, $resize = true)
    {
        if (!$keyboard) {
            return $this->keyboard->hide();
        }
        return $this->keyboard->show($keyboard, $oneTime, $resize);
    }

    /**
     * Return instance of Collection if call like config()
     * Pass params for get value from array use dot notation.
     * 
     * @param string|null $key
     * @param mixed $default
     * @return Collection|mixed
     */
    public function config($key = null, $default = null)
    {
        if (!$key && !$default) {
            return $this->update;
        }

        $data = data_get($this->config->toArray(), $key, $default);
        $data = is_array($data) ? array_filter($data) : $data;
        return is_array($data) && count($data) > 1 ? collect($data) : (is_array($data) ? head($data) : $data);
    }

    /**
     * Return instance of Collection if call like update()
     * Pass params for get value from array use dot notation.
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
        return is_array($data) && count($data) > 1 ? collect($data) : (is_array($data) ? head($data) : $data);
    }

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

    /**
     * Check update exists.
     *
     * @return boolean
     */
    public function isUpdate()
    {
        return !is_bool($this->update);
    }

    public function setUpdate($update = null)
    {
        $input = file_get_contents('php://input');
        $this->update = $input ? collect(json_decode($input, true)) : false;
    }

    public function longpoll($func)
    {
        echo "Long polling started ..." . PHP_EOL;

        $updateId = -1;

        while (true) {
            foreach ($this->getUpdates($updateId + 1, 1)->get('result') as $update) {
                $start = microtime(true);
                $this->update = collect($update);
                $updateId = $this->update('update_id', -1);
                $this->execute($func, [$this]);
                echo PHP_EOL . round(microtime(true) - $start, 5);
            }
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

    public function map($method, $func)
    {
        $this->mappedMethods[$method] = $func;
    }

    public function mapOnce($method, $func)
    {
        $this->mappedMethods[$method] = $this->execute($func);
    }

    public function __call($method, $args) {
        $tmp = $this->mappedMethods[$method];
        return is_callable($tmp) ? $this->execute($tmp, $args) : $tmp;
    }
}
