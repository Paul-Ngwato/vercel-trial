<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_id', 'title', 'description', 'event_date', 'start_time',
        'end_time', 'location', 'display_order', 'status',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('display_order');
    }

    public function scopeForDisplay($query)
    {
        return $query->where('status', 'active')->orderBy('display_order');
    }
}
