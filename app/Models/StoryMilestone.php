<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_id', 'title', 'content', 'image_path', 'display_order',
    ];

    public function photos()
    {
        return $this->hasMany(StoryPhoto::class, 'story_milestone_id')->orderBy('display_order');
    }

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }
}
