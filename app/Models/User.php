<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'salutation',
        'gender',
        'phone',
        'tsc_number',
        'id_number',
        'membership_number',
        'school_level',
        'school',
        'email',
        'status',
        'registration_fee_paid',
        'password',
        'profile_picture',
        'email_otp',
        'email_otp_expires_at',
        'email_verified_at',
        'sms_otp',
        'sms_otp_expires_at',
        'sms_verified_at',
        'last_login_at',
        'last_active_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'sms_verified_at' => 'datetime',
            'email_otp_expires_at' => 'datetime',
            'sms_otp_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_active_at' => 'datetime',
            'registration_fee_paid' => 'boolean',
        ];
    }
}
