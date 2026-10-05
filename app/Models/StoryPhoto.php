<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryPhoto extends Model
{
    protected $fillable = [
        'story_milestone_id', 'file_path', 'caption', 'display_order',
    ];

    public function milestone()
    {
        return $this->belongsTo(StoryMilestone::class, 'story_milestone_id');
    }
}
