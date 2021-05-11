<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireMailing extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id', 'added_questionnaire_id'
    ];
}
