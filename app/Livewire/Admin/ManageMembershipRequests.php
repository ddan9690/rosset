<?php

namespace App\Livewire\Admin;

use App\Models\MembershipRequest;
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

    // Full-size image preview modal state
    public ?string $selectedImage = null;

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

    public function approveRequest($id)
    {
        $request = MembershipRequest::with('user')->findOrFail($id);

        if ($request->status === 'approved' && $request->user && $request->user->registration_fee_paid) {
            session()->flash('message', 'This request is already fully processed.');
            return;
        }

        $request->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        if ($request->user) {
            $request->user->update([
                'status' => 'approved',
            ]);
        }

        session()->flash('message', 'Membership request approved successfully. The user can now proceed to pay the registration fee.');
    }

    public function rejectRequest($id)
    {
        $request = MembershipRequest::with('user')->findOrFail($id);

        $request->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        session()->flash('message', 'Membership request rejected.');
    }

    public function render()
    {
        $requests = MembershipRequest::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $term = '%' . $this->search . '%';
                $query->whereHas('user', function ($q) use ($term) {
                    $q->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('tsc_number', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->oldest()
            ->paginate(30);

        return view('livewire.admin.manage-membership-requests', [
            'requests' => $requests,
        ]);
    }
}