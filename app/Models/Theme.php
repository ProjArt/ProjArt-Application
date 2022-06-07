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
        'accent',
        'inactive',
        'text',
        'background',
        'information',
    ];

    protected $hidden = [
        'primary_color_id',
        'secondary_color_id',
        'accent_color_id',
        'inactive_color_id',
        'text_color_id',
        'background_color_id',
        'information_color_id',
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

    public function accent()
    {
        return $this->belongsTo(Color::class, 'accent_color_id', 'id');
    }

    public function inactive()
    {
        return $this->belongsTo(Color::class, 'inactive_color_id', 'id');
    }

    public function text()
    {
        return $this->belongsTo(Color::class, 'text_color_id', 'id');
    }

    public function background()
    {
        return $this->belongsTo(Color::class, 'background_color_id', 'id');
    }

    public function information()
    {
        return $this->belongsTo(Color::class, 'information_color_id', 'id');
    }
}
