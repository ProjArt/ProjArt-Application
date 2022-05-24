<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function usersOwn()
    {
        return $this->belongsToMany(User::class, 'calendar_user_own', 'calendar_id', 'user_id');
    }

    public function usersFollow()
    {
        return $this->belongsToMany(User::class, 'calendar_user_follow', 'calendar_id', 'user_id');
    }
}
