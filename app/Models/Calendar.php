<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name"
    ];

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
