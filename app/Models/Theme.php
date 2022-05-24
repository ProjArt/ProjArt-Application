<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    public function primary()
    {
        return $this->hasOne(Color::class, 'primary_color_id', 'id');
    }

    public function secondary()
    {
        return $this->hasOne(Color::class, 'secondary_color_id', 'id');
    }
}
