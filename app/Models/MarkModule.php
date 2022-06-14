<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkModule extends Model
{
    use HasFactory;

    protected $table = 'markmodules';

    protected $fillable = [
        'code',
        'name',
        'status',
        'years',
        'credits',
        'mark',
        'user_id'
    ];

    protected $with = [
        'marks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'markmodule_id');
    }
}
