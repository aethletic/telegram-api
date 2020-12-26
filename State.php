<?php

namespace Telegram;

class State
{
    public $name = null;
    public $data = null;

    /**
     * User ID from update.
     *
     * @var integer
     */
    private $currentUserId;

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var Illuminate\Database\Capsule\Manager
     */
    private $db;

    private $driver;

    public function __construct()
    {
        $this->bot = Bot::getInstance();

        $this->driver = $this->bot->config('state.driver', 'store');

        switch ($this->driver) {
            case 'store':
                $this->setDataFromUpdate();
                break;
            
            case 'database':
                $this->db = $this->bot->db();
                if ($this->bot->user()) {
                    $this->currentUserId = $this->bot->user()->get('user_id');
                    $this->name = $this->bot->user()->get('state_name');
                    $this->data = $this->bot->user()->get('state_data');
                } elseif ($this->bot->isUpdate()) {
                    $this->setDataFromUpdate();
                }
                break;
        }
    }

    private function setDataFromUpdate()
    {
        $this->currentUserId = $this->bot->update('*.form.id');
        $state = $this->getById($this->currentUserId);
        $this->name = $state['state_name'] ?? null;
        $this->data = $state['state_data'] ?? null;
    }

    public function get()
    {
        return $this->currentUserId ? $this->getById($this->currentUserId) : false;
    }

    public function getById($userId)
    {
        switch ($this->driver) {
            case 'store':
                return Bot::getInstance()->store()->get($this->userStateFile($userId));
                break;
            
            case 'database':
                return $this->db
                    ->table('users')
                    ->select('state_name', 'state_data')
                    ->where('user_id', $userId)
                    ->first();
                break;
        }
    }

    public function set($name = null, $data = null)
    {
        $this->setById($this->currentUserId, $name, $data);
    }

    public function save()
    {
        $this->setById($this->currentUserId, $this->name, $this->data);
    }

    public function setById($userId, $name = null, $data = null)
    {
        switch ($this->driver) {
            case 'store':
                return Bot::getInstance()->store()->set($this->userStateFile($userId), [
                    'state_name' => $name, 
                    'state_data' => $data
                ]);
                break;
            
            case 'database':
                return $this->db
                    ->table('users')
                    ->where('user_id', $userId)
                    ->update([
                        'state_name' => $name,
                        'state_data' => $data,
                    ]);
                break;
        }
    }

    public function clear()
    {
        $this->clearById($this->currentUserId);
    }

    public function clearById($userId)
    {
        switch ($this->driver) {
            case 'store':
                return Bot::getInstance()->store()->delete($this->userStateFile($userId));
                break;
            
            case 'database':
                return $this->db
                    ->table('users')
                    ->where('user_id', $userId)
                    ->update([
                        'state_name' => null,
                        'state_data' => null,
                    ]);
                break;
        }
    }

    public function setName($name)
    {
        switch ($this->driver) {
            case 'store':
                return Bot::getInstance()->store()->set($this->userStateFile($this->currentUserId), serialize([
                    'state_name' => $name, 
                ]));
                break;
            
            case 'database':
                return $this->db
                    ->table('users')
                    ->where('user_id', $this->currentUserId)
                    ->update([
                        'state_name' => $name,
                    ]);
                break;
        }
    }

    public function setData($data)
    {
        switch ($this->driver) {
            case 'store':
                return Bot::getInstance()->store()->set($this->userStateFile($this->currentUserId), serialize([
                    'state_data' => $data,
                ]));
                break;
            
            case 'database':
                return $this->db
                    ->table('users')
                    ->where('user_id', $this->currentUserId)
                    ->update([
                        'state_data' => $data,
                    ]);
                break;
        }
    }

    private function userStateFile($userId)
    {
        return md5("{userId}__USER__STATE__FILE");
    }
}
