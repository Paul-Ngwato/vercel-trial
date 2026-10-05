<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id', 'attendance', 'guest_count',
        'dietary_requirements', 'message',
    ];

    protected $casts = [
        'guest_count' => 'integer',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
