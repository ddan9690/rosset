<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_fee',
        'agm_contribution_fee',
        'registration_deadline_month',
        'registration_deadline_day',
        'late_registration_waiting_period_days',
        'defaulting_waiting_period_days',
    ];

    /**
     * Retrieve the global settings instance safely.
     */
    public static function current(): self
    {
        return static::first() ?? static::create([
            'registration_fee' => 150.00,
            'agm_contribution_fee' => 150.00,
            'registration_deadline_month' => 2,
            'registration_deadline_day' => 28,
            'late_registration_waiting_period_days' => 30,
            'defaulting_waiting_period_days' => 90,
        ]);
    }
}
