<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignQuestionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'questionnaire_id', 'active', 'sign'
    ];
}
