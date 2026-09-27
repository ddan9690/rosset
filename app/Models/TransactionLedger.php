<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLedger extends Model
{
    use HasFactory;

    protected $table = 'transaction_ledgers';

    protected $fillable = [
        'user_id',
        'reference',
        'amount',
        'type',
        'channel',
        'account_identifier',
        'phone_number',
        'description',
        'status',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}