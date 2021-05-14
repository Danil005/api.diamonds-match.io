<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id', 'with_questionnaire_id', 'total',
        'test', 'appearance', 'personal_qualities', 'information', 'about_me'
    ];
}
