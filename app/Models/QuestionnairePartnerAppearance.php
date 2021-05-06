<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnairePartnerAppearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'sex', 'ethnicity', 'booty', 'body_type',
        'hair_length', 'chest', 'eye_color', 'hair_color'
    ];
}
