<?php

namespace App\Livewire\Admin\RegistrationPayments;

use App\Models\RegistrationPayment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Registration Statement | ROSSET-SWA Admin')]
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
    public $regYear;
    public $amount = 150;
    public $paid_at = '';
    public $referenceNumber = '';
    public $notes = '';

    public function mount($id, $year)
    {
        $this->id = $id;
        $this->year = $year;
        $this->regYear = $year;
        $this->paid_at = date('Y-m-d');

        $settings = Setting::first();
        if ($settings && $settings->registration_fee) {
            $this->amount = (float) $settings->registration_fee;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['searchMember', 'selectedMember', 'user_id', 'referenceNumber', 'notes']);
        $this->regYear = $this->year;
        
        $settings = Setting::first();
        $this->amount = $settings?->registration_fee ?? 150;
        
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
            'regYear' => 'required|integer|min:2024|max:2035',
            'amount' => 'required|numeric|min:1',
            'paid_at' => 'required|date',
            'referenceNumber' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ], [
            'user_id.required' => 'Please search and select a valid member.',
        ]);

        $user = User::findOrFail($this->user_id);
        $admin = Auth::user();
        $ref = !empty($this->referenceNumber) ? strtoupper(trim($this->referenceNumber)) : ('REG-' . $this->regYear . '-' . strtoupper(substr(md5(uniqid()), 0, 6)));

        DB::transaction(function () use ($user, $admin, $ref) {
            $tx = Transaction::create([
                'user_id' => $user->id,
                'recorded_by' => $admin->id,
                'reference_number' => $ref,
                'type' => 'registration_fee',
                'year' => (int)$this->regYear,
                'payment_channel' => 'manual_admin',
                'amount' => $this->amount,
                'currency' => 'KES',
                'status' => 'success',
                'phone_number' => $user->phone ?? 'N/A',
                'description' => "Registration Fee for year {$this->regYear} recorded manually by Admin",
                'paid_at' => $this->paid_at . ' ' . date('H:i:s'),
            ]);

            RegistrationPayment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'year' => (int)$this->regYear,
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

            $user->update(['registration_fee_paid' => true]);
        });

        session()->flash('message', "Registration payment for {$this->regYear} successfully recorded for {$user->first_name} {$user->last_name}.");
        $this->closeModal();
    }

    public function deletePayment($paymentId)
    {
        $payment = RegistrationPayment::findOrFail($paymentId);
        if ($payment->transaction_id) {
            Transaction::where('id', $payment->transaction_id)->delete();
        }
        $payment->delete();

        session()->flash('message', 'Registration payment record deleted successfully.');
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

        $paymentsQuery = RegistrationPayment::with('user')
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
        $totalCollections = RegistrationPayment::where('year', (int)$this->year)->where('status', 'success')->sum('amount');
        $totalMembersPaid = RegistrationPayment::where('year', (int)$this->year)->where('status', 'success')->count();

        $maleCount = RegistrationPayment::where('year', (int)$this->year)
            ->where('status', 'success')
            ->whereHas('user', fn($q) => $q->whereRaw('LOWER(gender) = ?', ['male']))
            ->count();

        $femaleCount = RegistrationPayment::where('year', (int)$this->year)
            ->where('status', 'success')
            ->whereHas('user', fn($q) => $q->whereRaw('LOWER(gender) = ?', ['female']))
            ->count();

        $levelBreakdown = [
            'Primary' => RegistrationPayment::where('year', (int)$this->year)->where('status', 'success')->whereHas('user', fn($q) => $q->whereRaw('LOWER(school_level) = ?', ['primary']))->count(),
            'Junior'  => RegistrationPayment::where('year', (int)$this->year)->where('status', 'success')->whereHas('user', fn($q) => $q->whereRaw('LOWER(school_level) = ?', ['junior']))->count(),
            'Senior'  => RegistrationPayment::where('year', (int)$this->year)->where('status', 'success')->whereHas('user', fn($q) => $q->whereRaw('LOWER(school_level) = ?', ['senior']))->count(),
        ];

        return view('livewire.admin.registration-payments.show', [
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