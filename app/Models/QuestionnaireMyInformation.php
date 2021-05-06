<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireMyInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'age', 'place_birth', 'city', 'zodiac_signs', 'height', 'weight',
        'marital_status', 'languages', 'moving_city', 'moving_country',
        'children', 'children_desire', 'children_count', 'smoking',
        'alcohol', 'religion', 'sport', 'education', 'work',
        'salary', 'health_problems', 'allergies', 'pets', 'have_pets',
        'films_or_books', 'relax', 'countries_was', 'countries_dream',
        'best_gift', 'hobbies', 'kredo', 'features_repel', 'age_difference',
        'films', 'songs', 'ideal_weekend', 'sleep', 'doing_10', 'signature_dish',
        'clubs', 'best_gift_received', 'talents'
    ];
}
