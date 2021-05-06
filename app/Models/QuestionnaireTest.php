<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'lies', 'intervention', 'value', 'motive_marriage',
        'family_atmosphere', 'position_sex', 'books', 'friends', 'leisure',
        'discussion_feelings', 'work_relationship', 'family_decisions', 'consent',
        'interests_partner', 'first_place_relationship', 'position_society', 'conflicts',
        'cleanliness', 'clear_plan', 'conflict_behavior', 'life'
    ];
}
