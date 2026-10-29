<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
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
        'joined_at',
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
            'joined_at' => 'datetime',
        ];
    }

    /** * User's Solidarity Fund wallet. */
    public function solidarityFund()
    {
        return $this->hasOne(SolidarityFund::class, 'user_id');
    }

    public function benevolenceContributions()
    {
        return $this->hasMany(BenevolenceContribution::class, 'user_id');
    }

    /**
     * Activate the user with a manually assigned membership number by the admin
     * upon approving the membership request.
     */
    public function activateWithMembershipNumber(string $membershipNumber): void
    {
        // Prevent re-processing if already active with a membership number
        if ($this->status === 'active' && $this->membership_number) {
            return;
        }

        $joinedAt = $this->joined_at ?? now();

        $this->update([
            'membership_number' => $membershipNumber,
            'joined_at' => $joinedAt,
            'status' => 'active',
        ]);
    }
}
