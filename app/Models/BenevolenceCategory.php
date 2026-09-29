<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BenevolenceCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'amount',
    ];

    /**
     * Get the benevolence cases belonging to this category.
     */
    public function cases()
    {
        return $this->hasMany(BenevolenceCase::class, 'benevolence_category_id');
    }
}