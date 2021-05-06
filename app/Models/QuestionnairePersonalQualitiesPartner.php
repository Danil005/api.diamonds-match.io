<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnairePersonalQualitiesPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'calm', 'energetic', 'happy', 'modest',
        'purposeful', 'weak-willed', 'self', 'dependent',
        'feminine', 'courageous', 'confident', 'delicate',
        'live_here_now', 'pragmatic', 'graceful', 'sociable',
        'smiling', 'housewifely', 'ambitious', 'artistic',
        'good', 'aristocratic', 'stylish', 'economical', 'business',
        'sports', 'fearless', 'shy', 'playful'
    ];
}
