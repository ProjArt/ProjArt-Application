<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'orientation',
        'unity',
        'e',
        't1',
        't2',
        't3',
        't4',
        'total',
        'relative_period',
        'relative_rate',
        'relative_rate_unjustified',
        'absolute_period',
        'absolute_rate',
        'absolute_rate_unjustified',
    ];
}
