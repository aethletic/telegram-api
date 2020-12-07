<?php

namespace Telegram;

/**
 * Runtime store data.
 * Most actual for long poll.
 */
class Store
{
    private $data = [];
    private $driver;
    private $dir;
    private $db;

    public function __construct($config)
    {
        $this->driver = strtolower($config['driver']) ?? null;
        $this->dir = rtrim($config['file']['dir'], '\/') ?? null;

        if ($this->driver == 'database') {
            $this->db = Bot::getInstance()->db('store');
        }
    }

    public function set($key, $value)
    {
        switch ($this->driver) {
            case 'file':
                file_put_contents($this->dir . '/' . md5($key), serialize($value));
                break;
            
            case 'database':
                $this->has($key) ? $this->db->where('name', md5($key))->update(['name' => md5($key), 'value' => base64_encode(serialize($value))]) : $this->db->insert(['name' => md5($key), 'value' => base64_encode(serialize($value))]);
                break;
            
            default:
                $this->data[md5($key)] = $value;
                break;
        }
    }

    public function get($key)
    {
        switch ($this->driver) {
            case 'file':
                return $this->has($key) ? unserialize(file_get_contents($this->dir . '/' . md5($key))) : false;
                break;
            
            case 'database':
                return $this->has($key) ? unserialize(base64_decode($this->db->select('value')->where('name', md5($key))->first()->value)) : false;
                break;
                
            default:
                return $this->has($key) ? $this->data[md5($key)] : false;
                break;
        }
    }

    public function has($key)
    {
        switch ($this->driver) {
            case 'file':
                return file_exists($this->dir . '/' . md5($key));
                break;
            
            case 'database':
                return $this->db->where('name', md5($key))->exists();
                break;
            
            default:
                return array_key_exists(md5($key), $this->data);
                break;
        }
    }

    public function delete($key)
    {
        switch ($this->driver) {
            case 'file':
                $this->has($key) ? unlink($this->dir . '/' . md5($key)) : false;
                break;
            
            case 'database':
                $this->has($key) ? $this->db->where('name', md5($key))->delete() : false;
                break;
            
            default:
                unset($this->data[md5($key)]);
                break;
        }
    }
}
