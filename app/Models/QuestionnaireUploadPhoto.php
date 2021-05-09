<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireUploadPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'path', 'questionnaire_id'
    ];
}
