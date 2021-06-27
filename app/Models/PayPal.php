<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayPal extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'status', 'sum',
        'currency', 'link', 'application_id'
    ];
}
