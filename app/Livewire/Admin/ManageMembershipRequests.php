<?php

namespace App\Livewire\Admin;

use App\Models\MembershipRequest;
use App\Models\RegistrationPayment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Membership Requests | ROSSET-SWA')]
class ManageMembershipRequests extends Component
{
    use WithPagination;

    public $search = '';
    public ?string $selectedImage = null;

    // Manual approval modal state
    public bool $showApproveModal = false;
    public ?int $approvingRequestId = null;
    public ?string $membership_number = null;
    public ?string $applicantName = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewImage($url)
    {
        $this->selectedImage = $url;
    }

    public function closeImageModal()
    {
        $this->selectedImage = null;
    }

    /**
     * Open the approval modal and auto-suggest the next membership number.
     */
    public function openApproveModal($id)
    {
        $request = MembershipRequest::whereHas('user', function ($q) {
                $q->excludeDevelopers();
            })
            ->with(['user' => function ($q) {
                $q->excludeDevelopers();
            }])
            ->findOrFail($id);

        if ($request->status === 'approved' && $request->user && $request->user->status === 'active') {
            session()->flash('message', 'This request is already fully processed.');
            return;
        }

        $this->approvingRequestId = $id;
        $this->applicantName = trim(($request->user->first_name ?? '') . ' ' . ($request->user->last_name ?? ''));

        // Calculate maximum existing membership number numerically (excluding developers)
        $lastMembershipNumber = User::excludeDevelopers()->max(DB::raw('CAST(membership_number AS UNSIGNED)'));
        $nextNumber = $lastMembershipNumber ? $lastMembershipNumber + 1 : 1;

        $this->membership_number = (string) $nextNumber;
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->reset(['approvingRequestId', 'membership_number', 'applicantName']);
    }

    /**
     * Process approval, assign membership number, mark registration paid, and insert RegistrationPayment record.
     */
    public function processApproval(bool $force = false)
    {
        $this->validate([
            'membership_number' => 'required|string|max:50',
        ]);

        $request = MembershipRequest::whereHas('user', function ($q) {
                $q->excludeDevelopers();
            })
            ->with(['user' => function ($q) {
                $q->excludeDevelopers();
            }])
            ->findOrFail($this->approvingRequestId);

        // Check if membership number is already taken by another user
        $existingUser = User::excludeDevelopers()
            ->where('membership_number', $this->membership_number)
            ->when($request->user_id, function ($query) use ($request) {
                $query->where('id', '!=', $request->user_id);
            })
            ->first();

        if ($existingUser && !$force) {
            $existingName = trim($existingUser->first_name . ' ' . $existingUser->last_name);
            
            $this->dispatch('swal:duplicate-membership', [
                'number' => $this->membership_number,
                'name' => $existingName,
            ]);
            return;
        }

        DB::transaction(function () use ($request) {
            $currentYear = (int) date('Y');
            $settings = Setting::first();
            $regFee = $settings?->registration_fee ?? 150.00;

            // 1. Mark request as approved
            $request->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'registration_fee_paid' => true,
            ]);

            // 2. Activate user account and set membership details
            if ($request->user) {
                $request->user->update([
                    'registration_fee_paid' => true,
                ]);

                $request->user->activateWithMembershipNumber($this->membership_number);

                // 3. Automatically record the Registration Payment entry for the current year
                RegistrationPayment::updateOrCreate(
                    [
                        'user_id' => $request->user->id,
                        'year'    => $currentYear,
                    ],
                    [
                        'amount'          => $regFee,
                        'paid_at'         => now(),
                        'recorded_by'     => auth()->id(),
                        'payment_channel' => 'Registration Approval',
                        'status'          => 'success',
                        'notes'           => 'Auto-recorded upon membership request approval.',
                    ]
                );
            }
        });

        $this->closeApproveModal();
        session()->flash('message', 'Membership request approved, registration fee recorded, and membership number assigned successfully.');
    }

    public function rejectRequest($id)
    {
        $request = MembershipRequest::whereHas('user', function ($q) {
                $q->excludeDevelopers();
            })
            ->with(['user' => function ($q) {
                $q->excludeDevelopers();
            }])
            ->findOrFail($id);

        $request->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        session()->flash('message', 'Membership request rejected.');
    }

    public function render()
    {
        // Exclude rejected requests and developer applications from the queue
        $requests = MembershipRequest::where('status', '!=', 'rejected')
            ->whereHas('user', function ($q) {
                $q->excludeDevelopers();
            })
            ->with(['user' => function ($q) {
                $q->excludeDevelopers();
            }])
            ->when($this->search, function ($query) {
                $term = '%' . $this->search . '%';
                $query->whereHas('user', function ($q) use ($term) {
                    $q->excludeDevelopers()
                        ->where(function ($sub) use ($term) {
                            $sub->where('first_name', 'like', $term)
                                ->orWhere('last_name', 'like', $term)
                                ->orWhere('tsc_number', 'like', $term)
                                ->orWhere('email', 'like', $term)
                                ->orWhere('phone', 'like', $term);
                        });
                });
            })
            ->latest()
            ->paginate(30);

        return view('livewire.admin.manage-membership-requests', [
            'requests' => $requests,
        ]);
    }
}