<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphChat as BaseModel;

class TelegramChat extends BaseModel
{
    protected $table = 'telegraph_chats';

    public function gapsUsers()
    {
        return $this->belongsToMany(User::class, 'telegraph_chat_user', 'telegraph_chat_id', 'user_id');
    }
}
