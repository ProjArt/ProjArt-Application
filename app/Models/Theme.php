<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $with = [
        'primary',
        'secondary',
    ];

    protected $hidden = [
        'primary_color_id',
        'secondary_color_id',
        'created_at',
        'updated_at',
    ];

    public function primary()
    {
        return $this->belongsTo(Color::class, 'primary_color_id', 'id');
    }

    public function secondary()
    {
        return $this->belongsTo(Color::class, 'secondary_color_id', 'id');
    }
}
