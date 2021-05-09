<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applications extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_name', 'service_type', 'status', 'questionnaire_id', 'responsibility',
        'link', 'link_active'
    ];
}
