<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'checkout_request_id',
        'merchant_request_id',
        'receipt_number',
        'type',
        'case_number',
        'amount',
        'currency',
        'status',
        'phone_number',
        'description',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the benevolence case associated with this transaction.
     */
    public function benevolenceCase(): BelongsTo
    {
        return $this->belongsTo(BenevolenceCase::class, 'case_number', 'case_number');
    }
}