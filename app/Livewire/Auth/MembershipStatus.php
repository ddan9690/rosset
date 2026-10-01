<?php

namespace App\Livewire\Auth;

use App\Models\MembershipRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Membership Status | ROSSET-SWA')]
class MembershipStatus extends Component
{
    public function checkStatus()
    {
        $user = Auth::user();
        $membershipReq = MembershipRequest::where('user_id', $user->id)->latest()->first();

        if ($membershipReq && $membershipReq->status === 'approved') {
            if ($user->registration_fee_paid) {
                return redirect()->route('portal');
            }
            return redirect()->route('register.fee');
        }

        session()->flash('message', 'Status checked. Your application is still under review.');
    }

    public function render()
    {
        $user = Auth::user();
        $membershipReq = MembershipRequest::where('user_id', $user->id)->latest()->first();

        // If approved and fee paid, skip straight to portal
        if ($membershipReq && $membershipReq->status === 'approved' && $user->registration_fee_paid) {
            return redirect()->route('portal');
        }

        return view('livewire.auth.membership-status', [
            'membershipReq' => $membershipReq,
        ]);
    }
}