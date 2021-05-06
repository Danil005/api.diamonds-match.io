<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_appearance_id', 'personal_qualities_partner_id',
        'partner_information_id', 'test_id', 'my_appearance_id', 'my_personal_qualities_id',
        'my_information_id'
    ];
}
