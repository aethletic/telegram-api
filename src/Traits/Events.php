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

    public function isInline()
    {
        return array_has($this->update, 'inline_query');
    }

    public function isEditedMessage()
    {
        return array_has($this->update, 'edited_message');
    }

    public function isBot()
    {
        return $this->update('*.from.is_bot', false);
    }

    public function isSticker()
    {
        return $this->update('*.sticker', false);
    }

    public function isVoice()
    {
        return $this->update('*.voice', false);
    }

    public function isAnimation()
    {
        return $this->update('*.animation', false);
    }

    public function isDocument()
    {
        return $this->update('*.document', false);
    }

    public function isAudio()
    {
        return $this->update('*.audio', false);
    }

    public function isPhoto()
    {
        return $this->update('*.photo', false);
    }

    public function isVideo()
    {
        return $this->update('*.video', false);
    }

    public function isPoll()
    {
        return $this->update('*.poll', false);
    }

    public function isVideoNote()
    {
        return $this->update('*.video_note', false);
    }

    public function isContact()
    {
        return $this->update('*.contact', false);
    }

    public function isLocation()
    {
        return $this->update('*.location', false);
    }

    public function isVenue()
    {
        return $this->update('*.venue', false);
    }

    public function isDice()
    {
        return $this->update('*.dice', false);
    }

    public function isNewChatMembers()
    {
        return $this->update('*.new_chat_members', false);
    }

    public function isLeftChatMember()
    {
        return $this->update('*.left_chat_member', false);
    }

    public function isNewChatTitle()
    {
        return $this->update('*.new_chat_title', false);
    }

    public function isNewChatPhoto()
    {
        return $this->update('*.new_chat_photo', false);
    }

    public function isDeleteChatPhoto()
    {
        return $this->update('*.delete_chat_photo', false);
    }

    public function isChannelChatCreated()
    {
        return $this->update('*.channel_chat_created', false);
    }

    public function isMigrateToChatId()
    {
        return $this->update('*.migrate_to_chat_id', false);
    }

    public function isMigrateFromChatId()
    {
        return $this->update('*.migrate_from_chat_id', false);
    }

    public function isPinnedMessage()
    {
        return $this->update('*.pinned_message', false);
    }

    public function isInvoice()
    {
        return $this->update('*.invoice', false);
    }

    public function isSucessfulPayment()
    {
        return $this->update('*.successful_payment', false);
    }

    public function isConnectedWebsite()
    {
        return $this->update('*.connected_website', false);
    }

    public function isPassportData()
    {
        return $this->update('*.passport_data', false);
    }

    public function isReplyMarkup()
    {
        return $this->update('*.reply_markup', false);
    }

    public function isReply()
    {
        return $this->update('*.reply_to_message', false);
    }

    public function isCaption()
    {
        return $this->update('*.caption', false);
    }

    public function isForward()
    {
        return $this->update('*.forward_date', false) || $this->update('*.forward_from', false);
    }

    public function isSuperGroup()
    {
        return $this->update('*.chat.type', false) == 'supergroup';
    }

    public function isGroup()
    {
        return $this->update('*.chat.type', false) == 'group';
    }
    
    public function isChannel()
    {
        return $this->update('*.chat.type', false) == 'channel';
    }
    
    public function isPrivate()
    {
        return $this->update('*.chat.type', false) == 'private';
    }

    public function isNewVersion()
    {
        if (!$this->db || !$this->user) {
            return;
        }

        return $this->user()->isNewVersion;
    }

    public function isNewUser()
    {
        if (!$this->db || !$this->user) {
            return;
        }

        return $this->user()->isNewUser;
    }

    public function isSpam()
    {
        if (!$this->db || !$this->user) {
            return;
        }

        return $this->user()->isSpam;
    }

    public function isBanned()
    {
        if (!$this->db || !$this->user) {
            return;
        }

        return $this->user()->isBanned;
    }

    public function isUpdated()
    {
        if (!$this->db || !$this->user) {
            return;
        }

        return $this->user()->isUpdated;
    }

    public function isAdmin() {
        $adminList = $this->config('admin.list', [])->toArray();
        if (array_key_exists($this->update('*.from.id'), $adminList) || array_key_exists($this->update('*.from.id'), $adminList)) {
            return true;
        }
        return false;
    }

    public function onMaxSystemLoad($func)
    {
      $load = $this->getSystemLoad();
      if ($load[0] > $this->config('general.max_system_load')) {
        $this->executeFunction($func, [$load]);
      }
    }
}
