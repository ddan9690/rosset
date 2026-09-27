<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenevolenceCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_number',
        'slug',
        'user_id',
        'benevolence_category_id',
        'case_details',
        'deadline',
        'status',
        'created_by',
    ];

    /**
     * Get the affected member (user) linked to this case.
     */
    public function member()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the benevolence category associated with this case.
     */
    public function category()
    {
        return $this->belongsTo(BenevolenceCategory::class, 'benevolence_category_id');
    }

    /**
     * Get the admin user who created this case.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}