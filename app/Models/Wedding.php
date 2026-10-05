<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wedding extends Model
{
    use HasFactory;

    protected $fillable = [
        'bride_name', 'groom_name', 'invitation_message', 'wedding_date', 'wedding_time',
        'ceremony_venue', 'ceremony_address', 'ceremony_map_url',
        'reception_venue', 'reception_address', 'reception_map_url',
        'theme', 'couple_photo_path', 'logo_path', 'hero_image_path',
        'dress_code', 'rsvp_deadline', 'family_bride_info', 'family_groom_info',
        'meta_title', 'meta_description', 'meta_image_path',
        'primary_color', 'secondary_color', 'bg_color', 'accent_color',
        'contact_email', 'contact_phone', 'is_active',
    ];

    protected $casts = [
        'wedding_date' => 'date',
        'wedding_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function church()
    {
        return $this->hasOne(Church::class);
    }

    public function heroImages()
    {
        return $this->hasMany(HeroImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function introImages()
    {
        return $this->hasMany(IntroImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class)->orderBy('display_order');
    }

    public function informationItems()
    {
        return $this->hasMany(InformationItem::class)->orderBy('display_order');
    }

    public function storyMilestones()
    {
        return $this->hasMany(StoryMilestone::class)->orderBy('display_order');
    }

    public function daysUntilWedding(): int
    {
        return (int) now()->diffInDays($this->wedding_date, false);
    }

    public function isPastWedding(): bool
    {
        return $this->wedding_date->isPast();
    }

    public function getFullTitleAttribute(): string
    {
        return "{$this->bride_name} & {$this->groom_name}";
    }
}
