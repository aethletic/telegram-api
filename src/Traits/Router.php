<?php

namespace Telegram\Traits;

trait Router
{
    private $middlewares = [];
    private $middlewarePassed = null;

    private function check()
    {
        if (!$this->checkMiddleware()) {
            return false;
        }

        return true;
    }

    public function on($data, $func)
    {
        if (!$this->check()) {
            return false;
        }

        $data = (array) $data;

        foreach ($data as $key => $value) {
            // обычный on без значения
            if (is_numeric($key) && $this->update($value, false)) {
                return $this->execute($func);
            }

            // on со значением
            if (!$found = $this->update($key, false)) {
                continue;
            }

            if ($found == $value) {
                return $this->execute($func);
            }

            // regex
            if ($this->helper->isRegEx($value)) {
                preg_match($value, $found, $matches);
                if (sizeof($matches) > 0) {
                    return $this->execute($func);
                }
            }
        }
    }

    public function hear($messages, $func) {
        if ($this->isMessage() || $this->isEditedMessage()) {
            if (!$this->isCommand() && !$this->isCallback()) {
                $data = collect($messages)->mapWithKeys(function ($item) {
                    return ['*.text' => $item];
                })->toArray();
                return $this->on($data, $func);
            }
        }
    }

    public function command($messages, $func) {
        if ($this->isCommand() && !$this->isCallback()) {
            $data = collect($messages)->mapWithKeys(function ($item) {
                return ['*.text' => $item];
            })->toArray();
            return $this->on($data, $func);
        }
    }

    public function callback($messages, $func) {
        if ($this->isCallback()) {
            $data = collect($messages)->mapWithKeys(function ($item) {
                return ['callback_query.data' => $item];
            })->toArray();
            return $this->on($data, $func);
        }
    }

    public function addMiddleware($name, $func)
    {
        $this->middlewares[$name] = $func;
    }

    public function middleware($names = [])
    {
        $names = (array) $names;
        foreach ($names as $name) {
            if ($this->middlewarePassed === false) {
                continue;
            }

            $next = isset($this->middlewares[$name]) ? call_user_func($this->middlewares[$name]) : false;
            $next = is_bool($next) ? $next : false;
            $this->middlewarePassed = $next;
        }

        return $this;
    }

    private function checkMiddleware()
    {
        if ($this->middlewarePassed === null) {
            return true;
        }

        $next = $this->middlewarePassed;
        $this->middlewarePassed = null;

        return $next;
    }
}
