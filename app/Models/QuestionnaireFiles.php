<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'path', 'type', 'key', 'name', 'size',
        'questionnaire_id'
    ];
}
