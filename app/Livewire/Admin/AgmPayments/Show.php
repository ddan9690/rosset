<?php

namespace App\Livewire\Admin\AgmPayments;

use App\Models\AgmPayment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('AGM Year Statement | ROSSET-SWA Admin')]
class Show extends Component
{
    use WithPagination;

    public $id; 
    public $year;
    public $search = '';

    // Capture Modal Properties
    public $showModal = false;
    public $searchMember = '';
    public $selectedMember = null;
    public $user_id = '';
    public $agmYear;
    public $amount = 500;
    public $paid_at = '';
    public $referenceNumber = '';
    public $notes = '';

    public function mount($id, $year)
    {
        $this->id = $id;
        $this->year = $year;
        $this->agmYear = $year;
        $this->paid_at = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['searchMember', 'selectedMember', 'user_id', 'referenceNumber', 'notes']);
        $this->agmYear = $this->year;
        $this->amount = 500;
        $this->paid_at = date('Y-m-d');
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['searchMember', 'selectedMember', 'user_id', 'referenceNumber', 'notes']);
    }

    public function selectMember($memberId)
    {
        $this->selectedMember = User::find($memberId);
        if ($this->selectedMember) {
            $this->user_id = $this->selectedMember->id;
            $this->searchMember = '';
        }
    }

    public function clearSelectedMember()
    {
        $this->selectedMember = null;
        $this->user_id = '';
    }

    public function savePayment()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'agmYear' => 'required|integer|min:2024|max:2035',
            'amount' => 'required|numeric|min:1',
            'paid_at' => 'required|date',
            'referenceNumber' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ], [
            'user_id.required' => 'Please search and select a valid member.',
        ]);

        $user = User::findOrFail($this->user_id);
        $admin = Auth::user();
        $ref = !empty($this->referenceNumber) ? strtoupper(trim($this->referenceNumber)) : ('AGM-' . $this->agmYear . '-' . strtoupper(substr(md5(uniqid()), 0, 6)));

        DB::transaction(function () use ($user, $admin, $ref) {
            $tx = Transaction::create([
                'user_id' => $user->id,
                'recorded_by' => $admin->id,
                'reference_number' => $ref,
                'type' => 'agm_fee',
                'year' => (int)$this->agmYear,
                'payment_channel' => 'manual_admin',
                'amount' => $this->amount,
                'currency' => 'KES',
                'status' => 'success',
                'phone_number' => $user->phone ?? 'N/A',
                'description' => "AGM Fee for year {$this->agmYear} recorded manually by Admin",
                'paid_at' => $this->paid_at . ' ' . date('H:i:s'),
            ]);

            AgmPayment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'year' => (int)$this->agmYear,
                ],
                [
                    'recorded_by' => $admin->id,
                    'transaction_id' => $tx->id,
                    'amount' => $this->amount,
                    'payment_channel' => 'Manual Admin Entry',
                    'reference_number' => $ref,
                    'status' => 'success',
                    'notes' => $this->notes ?: "Captured manually by Admin {$admin->first_name}",
                    'paid_at' => $this->paid_at . ' ' . date('H:i:s'),
                ]
            );
        });

        session()->flash('message', "AGM Fee payment for {$this->agmYear} successfully recorded for {$user->first_name} {$user->last_name}.");
        $this->closeModal();
    }

    public function deletePayment($paymentId)
    {
        $payment = AgmPayment::findOrFail($paymentId);
        if ($payment->transaction_id) {
            Transaction::where('id', $payment->transaction_id)->delete();
        }
        $payment->delete();

        session()->flash('message', 'AGM payment record deleted successfully.');
    }

    public function render()
    {
        $searchedMembers = collect();
        if (strlen(trim($this->searchMember)) > 0) {
            $searchedMembers = User::where('first_name', 'like', '%' . $this->searchMember . '%')
                ->orWhere('last_name', 'like', '%' . $this->searchMember . '%')
                ->orWhere('membership_number', 'like', '%' . $this->searchMember . '%')
                ->orWhere('tsc_number', 'like', '%' . $this->searchMember . '%')
                ->limit(5)
                ->get();
        }

        $paymentsQuery = AgmPayment::with('user')
            ->where('year', (int)$this->year)
            ->where('status', 'success');

        if (!empty($this->search)) {
            $term = trim($this->search);
            $paymentsQuery->whereHas('user', function ($q) use ($term) {
                $q->where('membership_number', 'like', "%{$term}%")
                  ->orWhere('tsc_number', 'like', "%{$term}%")
                  ->orWhere('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%");
            });
        }

        $payments = $paymentsQuery->latest('paid_at')->paginate(15);

        // Core Financial & Demographic Statistics
        $totalCollections = AgmPayment::where('year', (int)$this->year)->where('status', 'success')->sum('amount');
        $totalMembersPaid = AgmPayment::where('year', (int)$this->year)->where('status', 'success')->count();

        $maleCount = AgmPayment::where('year', (int)$this->year)
            ->where('status', 'success')
            ->whereHas('user', fn($q) => $q->whereRaw('LOWER(gender) = ?', ['male']))
            ->count();

        $femaleCount = AgmPayment::where('year', (int)$this->year)
            ->where('status', 'success')
            ->whereHas('user', fn($q) => $q->whereRaw('LOWER(gender) = ?', ['female']))
            ->count();

        $levelBreakdown = [
            'Primary' => AgmPayment::where('year', (int)$this->year)->where('status', 'success')->whereHas('user', fn($q) => $q->whereRaw('LOWER(school_level) = ?', ['primary']))->count(),
            'Junior'  => AgmPayment::where('year', (int)$this->year)->where('status', 'success')->whereHas('user', fn($q) => $q->whereRaw('LOWER(school_level) = ?', ['junior']))->count(),
            'Senior'  => AgmPayment::where('year', (int)$this->year)->where('status', 'success')->whereHas('user', fn($q) => $q->whereRaw('LOWER(school_level) = ?', ['senior']))->count(),
        ];

        return view('livewire.admin.agm-payments.show', [
            'payments' => $payments,
            'totalCollections' => $totalCollections,
            'totalMembersPaid' => $totalMembersPaid,
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount,
            'levelBreakdown' => $levelBreakdown,
            'searchedMembers' => $searchedMembers,
        ]);
    }
}