<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireMatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'questionnaire_id', 'with_questionnaire_id', 'total',
        'test', 'appearance', 'personal_qualities', 'information', 'about_me'
    ];
}
