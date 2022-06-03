<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendar extends Model
{
    const READ_RIGHT = 1;
    const EDIT_RIGHT = 2;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name"
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class)->orderBy('start');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(["rights"]);
    }
}
