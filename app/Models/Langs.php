<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Langs extends Model
{
    use HasFactory;

    protected $fillable = [
        'nameEN', 'nameRU', 'nativeName', 'code'
    ];
}
