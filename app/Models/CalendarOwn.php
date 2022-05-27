<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarOwn extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'calendars';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'pivot',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'calendar_user_own', 'calendar_id', 'user_id');
    }

    public static function boot()
    {
        parent::boot();

        /*  static::belongToManySyncing(function ($relation, $parent, $ids) {
            dd("Attaching roles to user {$parent->name}. {$relation} {$ids}");
        }); */
    }
}
