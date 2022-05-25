<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry',
        'plate',
        'dessert',
        'menu_id',
    ];

    protected $appends = [
        'date',
    ];

    protected $hidden = [
        'menu_id',
        'created_at',
        'updated_at',
    ];

    public function getDateAttribute()
    {
        return $this->menu->date;
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
