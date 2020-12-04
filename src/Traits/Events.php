<?php 

namespace Telegram\Traits;

// TODO сделать "кеширование" после проверок, чтобы избегать повторных проверок.
//  Например, проверило isMessage() занес в переменную $this->isMessage и чекаешь потом ее.

trait Events
{
    public function isMessage()
    {

        return array_has($this->update, 'message');
    }

    public function isCallback()
    {
        return array_has($this->update, 'callback_query');
    }

    public function isCommand()
    {
        return $this->update('*.entities.0.type', false) == 'bot_command';
    }

    public function isEditedMessage()
    {
        return array_has($this->update, 'edited_message');
    }
}