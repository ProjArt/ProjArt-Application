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
    ];

    public function menu(){
        return $this->hasOne(Menu::class);
    }

}
