<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'amount',
        'customer_id',
        'billing_type',
        'status',
        'description',
        'payment_data',
        'response_data',
    ];

    protected $casts = [
        'payment_data' => 'array',
        'response_data' => 'array',
    ];
}
