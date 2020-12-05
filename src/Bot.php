<?php

namespace Telegram;

use GuzzleHttp\Client as Http;
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
        return call_user_func_array($func, $args);
    }
}
