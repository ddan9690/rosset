<?php

namespace App\Livewire;

use App\Models\SolidarityFund;
use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\MembershipRequest;
use App\Services\WelfareRatingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Portal extends Component
{
    use WithPagination;

    public $title = 'Member Portal | ROSSET-SWA';
    public $memberName = '';
    public $membershipNumber = '';
    public $memberStatus = '';
    public $isRegistrationPaid = false;
    public $isProfileComplete = false;
    public $solidarityBalance = 0;
    public $welfareRatingScore = 0;

    // Search and Sort properties for Benevolence Cases
    public $search = '';
    public $sortBy = 'newest'; // Default to newest created first

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function mount(WelfareRatingService $ratingService)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow previously onboarded or active members to bypass the membership request check
        if ($user->status === 'active' || $user->registration_fee_paid) {
            // Fully onboarded, proceed to portal
        } else {
            $membershipReq = MembershipRequest::where('user_id', $user->id)->latest()->first();

            if (!$membershipReq || in_array($membershipReq->status, ['pending', 'rejected'])) {
                return redirect()->route('membership.status');
            }

            if ($membershipReq->status === 'approved' && !$user->registration_fee_paid) {
                return redirect()->route('register.fee');
            }
        }

        $this->memberName = trim(
            ($user->salutation ?? '') . ' ' .
                ($user->first_name ?? '') . ' ' .
                ($user->last_name ?? '')
        );

        $this->membershipNumber = $user->membership_number ?? $user->tsc_number ?? 'N/A';
        $this->memberStatus = ucfirst($user->status ?? 'Pending');
        $this->isRegistrationPaid = (bool) ($user->registration_fee_paid ?? false);

        $this->isProfileComplete =
            !empty($user->tsc_number) &&
            !empty($user->id_number) &&
            !empty($user->school) &&
            !str_starts_with($user->tsc_number, 'TSC-');

        $wallet = SolidarityFund::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $this->solidarityBalance = $wallet->balance ?? 0;

        // Calculate Member Welfare Rating Score
        $ratingData = $ratingService->calculateRating($user);
        $this->welfareRatingScore = $ratingData['score'] ?? 0;
    }

    public function redirectToPayment()
    {
        return redirect()->route('register.fee');
    }

    public function render()
    {
        $user = Auth::user();

        // Build Query for Benevolence Cases with Search & Sorting
        $query = BenevolenceCase::with(['member', 'category'])
            ->whereIn('status', ['active', 'closed']);

        // Only show cases created on or after the member's joined_at timestamp
        if ($user->joined_at) {
            $query->where('created_at', '>=', $user->joined_at);
        }

        // Search filter (Case Number or Member's First/Last Name)
        if (!empty($this->search)) {
            $searchTerm = trim($this->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('case_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('member', function ($memberQuery) use ($searchTerm) {
                        $memberQuery->where('first_name', 'like', "%{$searchTerm}%")
                                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                                    ->orWhere('tsc_number', 'like', "%{$searchTerm}%")
                                    ->orWhere('membership_number', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Sorting configuration
        if ($this->sortBy === 'deadline_soonest') {
            $query->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
                  ->orderBy('deadline', 'asc');
        } elseif ($this->sortBy === 'deadline_latest') {
            $query->orderBy('deadline', 'desc');
        } elseif ($this->sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            // Default: newest created first
            $query->orderBy('created_at', 'desc');
        }

        $benevolenceCases = $query->paginate(6)->through(function ($case) use ($user) {
            $case->contribution_made = BenevolenceContribution::query()
                ->where('user_id', $user->id)
                ->where('benevolence_case_id', $case->id)
                ->exists();

            return $case;
        });

        return view('livewire.portal', [
            'benevolenceCases' => $benevolenceCases,
        ]);
    }
}