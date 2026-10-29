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
    /**
     * Redirect approved users away from status page during component mounting.
     */
    public function mount()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $membershipReq = MembershipRequest::where('user_id', $user->id)->latest()->first();

        // If already approved, skip straight to portal automatically
        if ($membershipReq && $membershipReq->status === 'approved') {
            return redirect()->route('portal');
        }
    }

    public function checkStatus()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $membershipReq = MembershipRequest::where('user_id', $user->id)->latest()->first();

        if ($membershipReq && $membershipReq->status === 'approved') {
            return redirect()->route('portal');
        }

        session()->flash('message', 'Status checked. Your application is still under review.');
    }

    public function render()
    {
        $user = Auth::user();
        $membershipReq = $user ? MembershipRequest::where('user_id', $user->id)->latest()->first() : null;

        return view('livewire.auth.membership-status', [
            'membershipReq' => $membershipReq,
        ]);
    }
}