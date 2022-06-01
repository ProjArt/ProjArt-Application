<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $primaryKey = 'name';
    public $incrementing = false;

    protected $fillable = [
        'name',
    ];

    protected $appends = [
        'total_persons'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    public function getTotalPersonsAttribute()
    {
        return $this->users->count();
    }


    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
