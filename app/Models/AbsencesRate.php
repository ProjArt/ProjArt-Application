<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsencesRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'absolute',
        'relative'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->hasOne(Course::class);
    }
}
