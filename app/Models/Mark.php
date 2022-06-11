<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        'markmodule_id',
        'module_code',
        'course_code',
        'course_name',
        'value',
        'years',
        'weight',
        'weight_percentage',
    ];

    protected $with = [
        'details',
    ];

    protected $hidden = [
        'user_id',
        'markmodule_id',
        'created_at',
        'updated_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(Markdetail::class);
    }
}
