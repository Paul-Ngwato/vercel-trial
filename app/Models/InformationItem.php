<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_id', 'title', 'content', 'icon', 'display_order', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('display_order');
    }
}
