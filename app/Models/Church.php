<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Church extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'pastor_name', 'address', 'phone', 'email',
        'website', 'logo_path', 'message_from_church',
    ];
}
