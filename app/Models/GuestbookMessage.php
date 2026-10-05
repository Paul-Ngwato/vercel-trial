<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestbookMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'message', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
