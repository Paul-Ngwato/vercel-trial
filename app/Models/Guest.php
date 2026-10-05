<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'relationship', 'relationship_other', 'supporting',
        'email', 'phone', 'invite_code',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Guest $guest) {
            if (empty($guest->invite_code)) {
                $guest->invite_code = strtoupper(Str::random(8));
            }
        });
    }

    public function rsvp()
    {
        return $this->hasOne(Rsvp::class);
    }

    public function isAttending(): bool
    {
        return $this->rsvp?->attendance === 'attending';
    }
}
