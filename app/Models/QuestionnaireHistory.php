<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'from', 'comment', 'questionnaire_id'
    ];
}
