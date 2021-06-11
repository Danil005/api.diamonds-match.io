<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnairePersonalQualitiesPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'calm', 'energetic', 'indifferent_life',
        'live_in_moment', 'pragmatic', 'ambitious', 'modest',
        'housewifely', 'lover_going_out', 'home', 'adventuress', 'rational',
        'aristocratic', 'strong-willed', 'soft', 'lark', 'owl', 'humanitarian', 'mathematical',
        'mature', 'simple', 'cautious', 'extrovert', 'open',
        'infantile', 'introvert', 'spiteful', 'compliant'
    ];
}
