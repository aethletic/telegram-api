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

    public function __construct()
    {
        $this->bot = Bot::getInstance();
        $this->db = $this->bot->db();

        $this->currentUserId = $this->bot->user()->get('user_id');

        if ($state = $this->get()) {
            $this->name = $state->state_name;
            $this->data = $state->state_name;
        }
    }

    public function get()
    {
        return $this->currentUserId ? $this->getById($this->currentUserId) : false;
    }

    public function getById($userId)
    {
        return $this->db
                    ->table('users')
                    ->select('state_name', 'state_data')
                    ->where('user_id', $userId)
                    ->first();
    }

    public function set($name = null, $data = null)
    {
        $this->setById($this->currentUserId, $name, $data);
        $this->name = $name;
        $this->data = $data;
    }

    public function setById($userId, $name = null, $data = null)
    {
        return $this->db
                    ->table('users')
                    ->where('user_id', $userId)
                    ->update([
                        'state_name' => $name,
                        'state_data' => $data,
                    ]);
    }

    public function clear()
    {
        $this->clearById($this->currentUserId);
        $this->name = null;
        $this->data = null;
    }

    public function clearById($userId)
    {
        return $this->db
                    ->table('users')
                    ->where('user_id', $userId)
                    ->update([
                        'state_name' => null,
                        'state_data' => null,
                    ]);
    }

    public function name($name)
    {
        return $this->db
                    ->table('users')
                    ->where('user_id', $this->bot->from->id)
                    ->update([
                        'state_name' => $name,
                    ]);
    }

    public function data($data)
    {
        return $this->db
                    ->table('users')
                    ->where('user_id', $this->bot->from->id)
                    ->update([
                        'state_data' => $data,
                    ]);
    }
}
