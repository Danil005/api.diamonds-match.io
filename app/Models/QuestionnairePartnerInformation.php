<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnairePartnerInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'age', 'place_birth', 'city', 'zodiac_signs', 'height', 'weight',
        'marital_status', 'languages', 'moving_city', 'moving_country',
        'children', 'children_desire', 'children_count', 'smoking',
        'alcohol', 'religion', 'sport'
    ];
}
