<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start',
        'end',
        'location',
    ];

    protected $casts = [
        'start' => 'datetime:Y-m-d H:i:s',
        'end' => 'datetime:Y-m-d H:i:s',
    ];

    protected $hidden = [
        'calendar_id',
        'created_at',
        'updated_at',
    ];

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function scopeNexts($query, $items)
    {
        return $query->where('end', '>=', now()->format('Y-m-d H:i:s'))->orderBy('start', 'asc')->take($items);
    }
}
