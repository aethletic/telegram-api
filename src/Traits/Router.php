<?php

namespace Telegram\Traits;

trait Router
{
    private $middlewares = [];
    private $middlewarePassed = null;
    private $middlewareCurrent = null;
    private $stateCurrent = null;

    private $defaultBotAnswer = null;
    private $defaultBotMessageAnswer = null;
    private $defaultBotCommandAnswer = null;
    private $defaultBotCallbackAnswer = null;

    private $queue = [];

    public function on($data, $func)
    {
        foreach ((array) $data as $key => $value) {

            if ($value == '{any}') {
                $this->queue[] = [
                    'val' => $value,
                    'func' => $func,
                    'middleware' => $this->middlewareCurrent,
                    'state' => $this->stateCurrent,
                ];
                break;
            }

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
                $this->queue[] = [
                    'val' => $value,
                    'func' => $func,
                    'middleware' => $this->middlewareCurrent,
                    'state' => $this->stateCurrent,
                ];
                break;
            }

            /**
             * Формат: 
             * ['*.text' => 'hello']
             */
            if (!$found = $this->update($key, false)) {
                continue;
            }

            if ($found == $value) {
                $this->queue[] = [
                    'val' => $value,
                    'func' => $func,
                    'middleware' => $this->middlewareCurrent,
                    'state' => $this->stateCurrent,
                ];
                break;
            }

            // regex
            if ($this->helper->isRegEx($value)) {
                preg_match($value, $found, $matches);
                if (sizeof($matches) > 0) {
                    $this->queue[] = [
                        'val' => $value,
                        'func' => $func,
                        'middleware' => $this->middlewareCurrent,
                        'state' => $this->stateCurrent,
                    ];
                    break;
                }
            }
        }

        $this->middlewareCurrent = null;
        $this->stateCurrent = null;
    }

    public function setDefaultAnswer($func)
    {
        $this->defaultBotAnswer = $func;
    }
    public function setDefaultMessageAnswer($func)
    {
        $this->defaultBotMessageAnswer = $func;
    }
    public function setDefaultCommandAnswer($func)
    {
        $this->defaultBotCommandAnswer = $func;
    }
    public function setDefaultCallbackAnswer($func)
    {
        $this->defaultBotCallbackAnswer = $func;
    }

    private function executeDefaults() {
        if ($this->isMessage() && !$this->isCommand() && !is_null($this->defaultBotMessageAnswer)) {
            $this->execute($this->defaultBotMessageAnswer);
            $this->collectStatistics();
            return $this->autoLogWrite('AUTO_DEFAULT_MESSAGE_ANSWER');
        }

        if ($this->isCommand() && !is_null($this->defaultBotCommandAnswer)) {
            $this->execute($this->defaultBotCommandAnswer);
            $this->collectStatistics();
            return $this->autoLogWrite('AUTO_DEFAULT_COMMAND_ANSWER');
        }

        if ($this->isCallback() && !is_null($this->defaultBotCallbackAnswer)) {
            $this->execute($this->defaultBotCallbackAnswer);
            $this->collectStatistics();
            return $this->autoLogWrite('AUTO_DEFAULT_CALLBACK_ANSWER');
        }

        if (!is_null($this->defaultBotAnswer)) {
            $this->execute($this->defaultBotAnswer);
            $this->collectStatistics();
            return $this->autoLogWrite('AUTO_DEFAULT_ANSWER');
        }
    }

    public function run()
    {
        print_r($this->queue);
        $this->middlewareCurrent = null;
        $this->stateCurrent = null;

        if ($this->queue === [] && !$this->isSpam()) {
            return $this->executeDefaults();
        }

        $hasOneExecuted = false;

        foreach ($this->queue as $event) {
            // если есть middleware, выполняем проверку
            $passed = true;
            if ($event['middleware']) {
                $passed = $this->checkMiddleware($event['middleware']);
            }

            if ($event['state']) {
                $passed = $this->checkState($event['state']);
            }

            // если не прошел проверку, пропускаем событие
            if ($passed === false) {
                continue;
            }

            // выполняем функцию события
            $this->execute($event['func']);

            $hasOneExecuted = true;
        }

        // очищаем очередь, актуально для лонгпула
        $this->queue = [];

        if (!$hasOneExecuted) {
            return $this->executeDefaults();
        }

        $this->autoLogWrite('AUTO');
        $this->collectStatistics();
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
        $this->middlewareCurrent = null;
        $this->stateCurrent = null;
    }

    public function command($messages, $func)
    {
        if ($this->isCommand() && !$this->isCallback()) {
            $data = collect($messages)->map(function ($item) {
                return ['*.text' => $item];
            })->toArray();
            return $this->on($data, $func);
        }
        $this->middlewareCurrent = null;
        $this->stateCurrent = null;
    }

    public function callback($messages, $func)
    {
        if ($this->isCallback()) {
            $data = collect($messages)->map(function ($item) {
                return ['callback_query.data' => $item];
            })->toArray();
            return $this->on($data, $func);
        }
        $this->middlewareCurrent = null;
        $this->stateCurrent = null;
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
    
    // TODO: переделать 
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

    public function onState($names, $stopWords = null)
    {
        $this->stateCurrent = [
            'names' => (array) $names,
            'stopWords' => $stopWords,
        ];

        return $this;
    }

    // TODO: переделать 
    private function checkState($state)
    {
        $names = $state['names'];
        $stopWords = $state['stopWords'];

        if ($stopWords) {
            $stopWords = (array) $stopWords;
        }

        if (in_array($this->state()->name, $names)) {
            if ($stopWords && in_array($this->isMessage() ? $this->update('*.text', []) : $this->update('*.data', []), $stopWords)) {
                $this->statePassed = false;
            } else {
                $this->statePassed = true;
            }
        } else {
            $this->statePassed = false;
        }

        $this->stateCurrent = null;

        if ($this->statePassed === null) {
            return true;
        }

        $next = $this->statePassed;
        $this->statePassed = null;

        return $next;
    }
}
