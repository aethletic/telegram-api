<?php

namespace Telegram\Traits;

trait Router
{
    private $middlewares = [];
    private $middlewarePassed = null;
    private $middlewareCurrent = null;
    private $queue = [];

    private function check()
    {
        if (!$this->checkMiddleware()) {
            return false;
        }

        return true;
    }

    public function on($data, $func)
    {
        // if (!$this->check()) {
        //     return false;
        // }

        foreach ((array) $data as $key => $value) {

            /**
             * Формат: 
             * [
             *   ['*.text' => '/qwe/i'],
             *   ['*.text' => '/asd/i'],
             * ]
             */
            if (is_numeric($key) && is_array($value)) {
                $this->on($value, $func);
                continue;
            }

            /**
             * Формат: 
             * ['*.text', '*.sticker']
             */
            if (is_numeric($key) && $this->update($value, false)) {
                // return $this->execute($func);
                $this->queue[] = [
                    'func' => $func,
                    'middleware' => $this->middlewareCurrent,
                ];
                return;
            }

            /**
             * Формат: 
             * ['*.text' => 'hello']
             */
            if (!$found = $this->update($key, false)) {
                continue;
            }

            if ($found == $value) {
                // return $this->execute($func);
                $this->queue[] = [
                    'func' => $func,
                    'middleware' => $this->middlewareCurrent,
                ];
                return;
            }

            // regex
            if ($this->helper->isRegEx($value)) {
                preg_match($value, $found, $matches);
                if (sizeof($matches) > 0) {
                    // return $this->execute($func);
                    $this->queue[] = [
                        'func' => $func,
                        'middleware' => $this->middlewareCurrent,
                    ];
                    return;
                }
            }
        }

        $this->middlewareCurrent = null;
    }

    public function run()
    {
        foreach ($this->queue as $event) {

            // если есть middleware, выполняем проверку
            $passed = true;
            if ($event['middleware']) {
                $passed = $this->checkMiddleware($event['middleware']);
            }

            // если не прошел проверку, пропускаем событие
            if ($passed === false) {
                continue;
            }

            // выполняем функцию события
            $this->execute($event['func']);
        }

        // очищаем очередь, актуально для лонгпула
        $this->queue = [];
    }

    public function hear($messages, $func)
    {
        if ($this->isMessage() || $this->isEditedMessage()) {
            if (!$this->isCommand() && !$this->isCallback()) {
                $data = collect($messages)->map(function ($item) {
                    return ['*.text' => $item];
                })->toArray();
                return $this->on($data, $func);
            }
        }
    }

    public function command($messages, $func)
    {
        if ($this->isCommand() && !$this->isCallback()) {
            $data = collect($messages)->map(function ($item) {
                return ['*.text' => $item];
            })->toArray();
            return $this->on($data, $func);
        }
    }

    public function callback($messages, $func)
    {
        if ($this->isCallback()) {
            $data = collect($messages)->map(function ($item) {
                return ['callback_query.data' => $item];
            })->toArray();
            return $this->on($data, $func);
        }
    }

    public function addMiddleware($name, $func)
    {
        $this->middlewares[$name] = $func;
    }

    public function middleware($middlewares)
    {
        $this->middlewareCurrent = $middlewares;
        return $this;
    }

    private function checkMiddleware($middleware = [])
    {
        foreach ((array) $middleware as $item) {
            $name = is_array($item) ? $item['name'] : $item;
            $fallback = is_array($item) ? $item['fallback'] : false;

            // если предыдущая проверка вернула false, то пропускаем все остальное
            if ($this->middlewarePassed === false) {
                break;
            }

            $next = isset($this->middlewares[$name]) ? call_user_func($this->middlewares[$name]) : false;
            $next = is_bool($next) ? $next : false;
            $this->middlewarePassed = $next;

            if ($this->middlewarePassed === false && $fallback) {
                $this->execute($fallback);
            }
        }

        $this->middlewareCurrent = null;

        if ($this->middlewarePassed === null) {
            return true;
        }

        $next = $this->middlewarePassed;
        $this->middlewarePassed = null;

        return $next;
    }
}
