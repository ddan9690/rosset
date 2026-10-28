<?php

namespace App\Livewire;

use App\Models\BenevolenceClaim;
use App\Services\BenevolenceClaimEligibilityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('My Benevolence Claims | ROSSET-SWA')]
class MemberBenevolenceClaims extends Component
{
    use WithPagination;

    public $description = '';
    public $showClaimModal = false;

    public function openClaimModal(BenevolenceClaimEligibilityService $eligibilityService)
    {
        $user = Auth::user();

        // 1. Check if user has an active pending claim (excluding withdrawn ones)
        $hasPendingClaim = BenevolenceClaim::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingClaim) {
            $memberName = trim(($user->first_name ?? 'Member') . ' ' . ($user->last_name ?? ''));
            
            $this->dispatch('swal:pending-claim', [
                'title' => 'Pending Claim Notice',
                'message' => "Dear {$memberName}, you have a pending benevolence claim. Please contact the welfare for further assistance.",
            ]);
            return;
        }

        // 2. Constitutional eligibility check
        $eligibilityError = $eligibilityService->check($user);
        if ($eligibilityError) {
            session()->flash('error', $eligibilityError);
            return;
        }

        session()->forget('error');
        $this->reset(['description']);
        $this->resetErrorBag();
        $this->showClaimModal = true;
    }

    public function closeClaimModal()
    {
        $this->showClaimModal = false;
        $this->reset(['description']);
    }

    public function saveClaim(BenevolenceClaimEligibilityService $eligibilityService)
    {
        $user = Auth::user();

        $hasPendingClaim = BenevolenceClaim::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingClaim) {
            $memberName = trim(($user->first_name ?? 'Member') . ' ' . ($user->last_name ?? ''));
            $this->closeClaimModal();
            
            $this->dispatch('swal:pending-claim', [
                'title' => 'Pending Claim Notice',
                'message' => "Dear {$memberName}, you have a pending benevolence claim. Please contact the welfare for further assistance.",
            ]);
            return;
        }

        $eligibilityError = $eligibilityService->check($user);
        if ($eligibilityError) {
            session()->flash('error', $eligibilityError);
            $this->closeClaimModal();
            return;
        }

        $this->validate([
            'description' => 'required|string|min:10|max:1000',
        ], [
            'description.required' => 'Please provide a clear description or reason for your claim.',
            'description.min' => 'The description must be at least 10 characters long.',
        ]);

        $year = date('Y');
        $membershipNo = $user->membership_number ?? $user->id;
        $claimNumber = 'CLM-' . $year . '-' . $membershipNo . '-' . strtoupper(Str::random(4));

        BenevolenceClaim::create([
            'claim_number' => $claimNumber,
            'user_id' => $user->id,
            'description' => trim($this->description),
            'status' => 'pending',
        ]);

        $this->closeClaimModal();
        
        $this->dispatch('swal:success', [
            'message' => "Your benevolence claim has been successfully submitted. Your claim number is {$claimNumber}."
        ]);
    }

    public function withdrawClaim($id)
    {
        $user = Auth::user();

        $claim = BenevolenceClaim::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$claim) {
            session()->flash('error', 'Claim not found or cannot be withdrawn because it has already been processed.');
            return;
        }

        $claim->update(['status' => 'withdrawn']);

        $this->dispatch('swal:success', [
            'message' => "Claim {$claim->claim_number} has been successfully withdrawn."
        ]);
    }

    public function render()
    {
        // Exclude withdrawn claims from the member's view table (only show pending, approved, rejected)
        $claims = BenevolenceClaim::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest('created_at')
            ->paginate(5);

        return view('livewire.member-benevolence-claims', [
            'claims' => $claims,
        ]);
    }
}