<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Markdetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'weight',
        'value',
    ];

    protected $hidden = [
        'mark_id',
        'created_at',
        'updated_at',
    ];
}
