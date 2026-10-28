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
    
    // Modal & View states
    public $showAvatarModal = false;
    public $showFullPictureModal = false;
    public $showRatingInfoModal = false;
    public $avatar;

    // Inline Editable Fields
    public $editingField = null; // 'phone', 'email', 'school', 'school_level'
    public $phone;
    public $email;
    public $school;
    public $school_level;

    public function mount($id)
    {
        $this->user = User::findOrFail($id);
        $this->resetInlineFields();
    }

    public function resetInlineFields()
    {
        $this->phone = $this->user->phone;
        $this->email = $this->user->email;
        $this->school = $this->user->school;
        $this->school_level = $this->user->school_level;
        $this->editingField = null;
    }

    public function editField($field)
    {
        $this->resetInlineFields();
        $this->editingField = $field;
    }

    public function cancelEdit()
    {
        $this->resetInlineFields();
    }

    public function saveField($field)
    {
        if ($field === 'phone') {
            $this->validate(['phone' => 'required|string|max:20|unique:users,phone,' . $this->user->id]);
            $this->user->update(['phone' => $this->phone]);
        } elseif ($field === 'email') {
            $this->validate(['email' => 'required|email|max:255|unique:users,email,' . $this->user->id]);
            $this->user->update(['email' => $this->email]);
        } elseif ($field === 'school') {
            $this->validate(['school' => 'nullable|string|max:255']);
            $this->user->update(['school' => $this->school]);
        } elseif ($field === 'school_level') {
            $this->validate(['school_level' => 'nullable|in:Primary,Junior,Senior']);
            $this->user->update(['school_level' => $this->school_level]);
        }

        $this->editingField = null;
        session()->flash('message', 'Profile updated successfully.');
    }

    public function updateStatusFromJS($status)
    {
        if (!in_array($status, ['active', 'suspended', 'deregistered'])) {
            return;
        }

        $this->user->update(['status' => $status]);
        session()->flash('message', 'Member status updated to ' . ucfirst($status) . ' successfully.');
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

    public function openFullPictureModal()
    {
        $this->showAvatarModal = false;
        $this->showFullPictureModal = true;
    }

    public function closeFullPictureModal()
    {
        $this->showFullPictureModal = false;
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
        $this->user->update(['profile_picture' => $path]);

        $this->closeAvatarModal();
        session()->flash('message', 'Profile picture updated successfully.');
    }

    public function render(WelfareRatingService $ratingService, BenevolenceClaimEligibilityService $eligibilityService)
    {
        $wallet = SolidarityFund::firstOrCreate(
            ['user_id' => $this->user->id],
            ['balance' => 0, 'total_topups' => 0, 'total_deductions' => 0]
        );

        $ratingData = $ratingService->calculateRating($this->user);
        
        $eligibilityError = $eligibilityService->check($this->user);
        if ($eligibilityError) {
            $eligibilityError = str_replace(['your ', 'you '], ['the member\'s ', 'the member '], $eligibilityError);
            $eligibilityError = preg_replace('/(As per [^,]+),\s*the member\'s/i', '$1, the member\'s', $eligibilityError);
        }

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