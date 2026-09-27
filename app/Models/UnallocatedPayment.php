<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnallocatedPayment extends Model
{
    use HasFactory;

    protected $table = 'unallocated_payments';

    protected $fillable = [
        'reference',
        'amount',
        'customer_reference',
        'customer_name',
        'phone_number',
        'reason',
        'status',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'amount' => 'decimal:2',
    ];
}