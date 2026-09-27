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
    ];
}