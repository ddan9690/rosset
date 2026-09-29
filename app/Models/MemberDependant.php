<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDependant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'relationship', // e.g., 'spouse', 'child', 'parent', 'sibling'
    ];

    /**
     * Get the user (member) that owns the dependant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}