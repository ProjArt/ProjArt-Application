<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    public $incrementing = false;
    public $keyType = 'string';
    public $primaryKey = 'code';

    protected $fillable = [
        'code',
        'name',
        'faculty',
    ];

    protected $appends = [
        "gapsUsers",
        "professors",
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function gapsUsers()
    {
        return $this->belongsToMany(GapsUser::class, 'course_gaps_user', 'course_code', 'gaps_user_username');
    }

    public function getGapsUsersAttribute()
    {
        return $this->gapsUsers()->get();
    }

    public function getProfessorsAttribute()
    {
        return $this->gapsUsers()->where('is_teacher', true)->get();
    }
}
