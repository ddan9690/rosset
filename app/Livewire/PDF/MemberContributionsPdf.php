<?php

namespace App\Livewire\PDF;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberContributionsPdf extends Component
{
    public $memberName;
    public $membershipNumber;
    public $email;
    public $phone;
    public $school;
    public $memberStatus;
    public $contributionHistory;
    public $generatedAt;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Construct full member name
        $this->memberName = trim(
            ($user->salutation ?? '') . ' ' .
            ($user->first_name ?? '') . ' ' .
            ($user->last_name ?? '')
        );

        if (empty($this->memberName)) {
            $this->memberName = 'Valued Member';
        }

        $this->membershipNumber = $user->tsc_number ?? $user->membership_number ?? 'N/A';
        $this->email = $user->email ?? 'N/A';
        $this->phone = $user->phone ?? 'N/A';
        $this->school = $user->school ?? 'N/A';
        $this->memberStatus = ucfirst($user->status ?? 'Pending');

        $this->contributionHistory = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'success')
            ->whereNotNull('paid_at')
            ->latest('paid_at')
            ->get();

        $this->generatedAt = now()->format('d/m/Y H:i');

        $data = [
            'memberName' => $this->memberName,
            'membershipNumber' => $this->membershipNumber,
            'email' => $this->email,
            'phone' => $this->phone,
            'school' => $this->school,
            'memberStatus' => $this->memberStatus,
            'contributionHistory' => $this->contributionHistory,
            'generatedAt' => $this->generatedAt,
        ];

        $pdf = Pdf::loadView('livewire.p-d-f.member-contributions-pdf', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'member-contributions-' . str_replace('/', '-', $this->membershipNumber) . '.pdf');
    }

    public function render()
    {
        return view('livewire.p-d-f.member-contributions-pdf');
    }
}