<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireAppointedDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'with_questionnaire_id', 'questionnaire_id',
        'date', 'time'
    ];
}
