<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapsUser extends Model
{
    use HasFactory;

    protected $table = 'gaps_users';

    public $incrementing = false;
    public $keyType = 'string';
    public $primaryKey = 'username';

    protected $fillable = [
        'username',
        'firstname',
        'name',
        'is_teacher',
        'mail'
    ];

    protected $append = [
        'courses'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_gaps_user', 'gaps_user_username', 'course_code');
    }

    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->name;
    }
}
