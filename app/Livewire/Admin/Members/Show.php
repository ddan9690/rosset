<?php

namespace App\Livewire\Admin\Members;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\SolidarityFund;
use App\Models\User;
use App\Services\BenevolenceClaimEligibilityService;
use App\Services\WelfareRatingService;
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
    
    // Modal states
    public $showAvatarModal = false;
    public $showEditModal = false;
    public $showStatusModal = false;
    public $showRatingInfoModal = false;
    public $selectedStatus = '';
    public $avatar;

    // Editable profile fields
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $school;
    public $school_level;

    public function mount($id)
    {
        $this->user = User::findOrFail($id);
        $this->fillEditableFields();
    }

    public function fillEditableFields()
    {
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->school = $this->user->school;
        $this->school_level = $this->user->school_level;
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
            'avatar' => 'required|image|max:2048',
        ]);

        if ($this->user->profile_picture && Storage::disk('public')->exists($this->user->profile_picture)) {
            Storage::disk('public')->delete($this->user->profile_picture);
        }

        $path = $this->avatar->store('profile-pictures', 'public');

        $this->user->update([
            'profile_picture' => $path,
        ]);

        $this->closeAvatarModal();
        session()->flash('message', 'Profile picture updated successfully.');
    }

    public function openEditModal()
    {
        $this->fillEditableFields();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function updateMemberDetails()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'phone' => 'required|string|max:20',
            'school' => 'nullable|string|max:255',
            'school_level' => 'nullable|in:Primary,Junior,Senior',
        ]);

        $this->user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'school' => $this->school,
            'school_level' => $this->school_level,
        ]);

        $this->closeEditModal();
        session()->flash('message', 'Member details updated successfully.');
    }

    public function openStatusModal($status)
    {
        $this->selectedStatus = $status;
        $this->showStatusModal = true;
    }

    public function updateStatus()
    {
        if (!in_array($this->selectedStatus, ['active', 'suspended', 'deregistered'])) {
            return;
        }

        $this->user->update([
            'status' => $this->selectedStatus,
        ]);

        $this->showStatusModal = false;
        session()->flash('message', 'Member status updated to ' . ucfirst($this->selectedStatus) . ' successfully.');
    }

    public function render(WelfareRatingService $ratingService, BenevolenceClaimEligibilityService $eligibilityService)
    {
        $wallet = SolidarityFund::firstOrCreate(
            ['user_id' => $this->user->id],
            ['balance' => 0, 'total_topups' => 0, 'total_deductions' => 0]
        );

        $ratingData = $ratingService->calculateRating($this->user);
        $eligibilityError = $eligibilityService->check($this->user);

        $eligibleCasesQuery = BenevolenceCase::with(['category', 'member'])
            ->whereIn('status', ['active', 'closed', 'suspended']);

        if ($this->user->joined_at) {
            $eligibleCasesQuery->where('created_at', '>=', $this->user->joined_at);
        }

        $eligibleCases = $eligibleCasesQuery->where('user_id', '!=', $this->user->id)
            ->latest('created_at')
            ->get();

        $contributions = BenevolenceContribution::where('user_id', $this->user->id)
            ->get()
            ->keyBy('benevolence_case_id');

        return view('livewire.admin.members.show', [
            'wallet' => $wallet,
            'ratingData' => $ratingData,
            'eligibilityError' => $eligibilityError,
            'eligibleCases' => $eligibleCases,
            'contributions' => $contributions,
        ]);
    }
}