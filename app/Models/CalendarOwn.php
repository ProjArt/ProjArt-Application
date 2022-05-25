<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarOwn extends Model
{
    use HasFactory;

    protected $table = 'calendars';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'calendar_user_own', 'calendar_id', 'user_id');
    }
}
