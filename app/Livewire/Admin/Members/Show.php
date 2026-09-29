<?php

namespace App\Livewire\Admin\Members;

use App\Models\BenevolenceCase;
use App\Models\SolidarityFund;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
#[Title('Member Details | Admin ROSSET-SWA')]
class Show extends Component
{
    use WithFileUploads;

    public User $user;
    
    // Modal state & upload property
    public $showAvatarModal = false;
    public $avatar;

    public function mount($id)
    {
        $this->user = User::findOrFail($id);
    }

    public function openAvatarModal()
    {
        $this->reset('avatar');
        $this->showAvatarModal = true;
    }

    public function closeAvatarModal()
    {
        $this->showAvatarModal = false;
        $this->reset('avatar');
    }

    public function updateAvatar()
    {
        $this->validate([
            'avatar' => 'required|image|max:2048', // Max 2MB
        ]);

        // Delete old profile picture if exists
        if ($this->user->profile_picture && Storage::disk('public')->exists($this->user->profile_picture)) {
            Storage::disk('public')->delete($this->user->profile_picture);
        }

        // Store new image
        $path = $this->avatar->store('profile-pictures', 'public');

        $this->user->update([
            'profile_picture' => $path,
        ]);

        $this->closeAvatarModal();
        session()->flash('message', 'Profile picture updated successfully.');
    }

    public function render()
    {
        // 1. Solidarity Fund & Financial Statement
        $wallet = SolidarityFund::firstOrCreate(
            ['user_id' => $this->user->id],
            ['balance' => 0, 'total_topups' => 0, 'total_deductions' => 0]
        );

        $financialStatements = Transaction::where('user_id', $this->user->id)
            ->whereIn('type', ['wallet_topup', 'registration_fee', 'benevolence_contribution'])
            ->where('status', 'success')
            ->latest('paid_at')
            ->get();

        // 2. Benevolence Participation Scorecard & Lists (Excluding active cases)
        $eligibleTotalCases = BenevolenceCase::where('status', '!=', 'active')->count();

        $contributedCaseNumbers = Transaction::where('user_id', $this->user->id)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'success')
            ->whereNotNull('case_number')
            ->pluck('case_number')
            ->unique();

        $userContributedCount = $contributedCaseNumbers->count();

        $scorePercentage = $eligibleTotalCases > 0 ? min(100, round(($userContributedCount / $eligibleTotalCases) * 100)) : 0;

        $contributedTransactions = Transaction::where('user_id', $this->user->id)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'success')
            ->with(['benevolenceCase.category'])
            ->latest('paid_at')
            ->get();

        $missedCases = BenevolenceCase::where('status', '!=', 'active')
            ->whereNotIn('case_number', $contributedCaseNumbers)
            ->with(['category', 'user'])
            ->get();

        return view('livewire.admin.members.show', [
            'wallet' => $wallet,
            'financialStatements' => $financialStatements,
            'eligibleTotalCases' => $eligibleTotalCases,
            'userContributedCount' => $userContributedCount,
            'scorePercentage' => $scorePercentage,
            'contributedTransactions' => $contributedTransactions,
            'missedCases' => $missedCases,
        ]);
    }
}