<?php

namespace App\Livewire;

use App\Models\BenevolenceCase;
use App\Models\Transaction;
use App\Services\KcbPaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Benevolence Contribution | ROSSET-SWA')]
class BenevolenceContribution extends Component
{
    public $caseId;
    public $case;
    public $amount = 1; // Set to 1 for testing (change to category amount later if needed)
    public $phone = '';
    public $defaultPhone = '';
    public $isPhoneEditable = false;
    public $stkSent = false;
    public $activeCheckoutRequestId = null;

    public function mount($id)
    {
        $this->caseId = $id;
        $this->case = BenevolenceCase::with(['member', 'category'])->findOrFail($id);
        
        $user = Auth::user();
        if ($user) {
            $this->phone = $user->phone ?? '';
            $this->defaultPhone = $user->phone ?? '';

            // Check if already successfully contributed
            $alreadyContributed = Transaction::where('user_id', $user->id)
                ->where('case_number', $this->case->case_number)
                ->where('type', 'benevolence_contribution')
                ->where('status', 'success')
                ->exists();

            if ($alreadyContributed) {
                session()->flash('message', 'You have already contributed to case ' . $this->case->case_number);
                return redirect()->route('portal');
            }
        }
    }

    public function togglePhoneEditable()
    {
        $this->isPhoneEditable = !$this->isPhoneEditable;
        if (!$this->isPhoneEditable) {
            $this->phone = $this->defaultPhone;
        }
    }

    public function checkPaymentStatus()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $success = Transaction::where('user_id', $user->id)
            ->where('case_number', $this->case->case_number)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'success')
            ->exists();

        if ($success) {
            $this->stkSent = false;
            $this->dispatch('payment-successful');
        }
    }

    public function sendStkPrompt(KcbPaymentService $paymentService)
    {
        $this->validate([
            'phone' => ['required', 'string', 'regex:/^(?:254[17]\d{8}|0[17]\d{8}|[17]\d{8})$/'],
        ], [
            'phone.regex' => 'Please enter a valid phone number format.',
        ]);

        $user = Auth::user();
        $accountIdentifier = config('services.kcb.account_prefix', '7936435');

        $result = $paymentService->stkPush(
            phone: $this->phone,
            amount: (float) $this->amount,
            accountIdentifier: $accountIdentifier,
            description: 'Benevolence Contribution - ' . $this->case->case_number,
            userId: $user->id,
            transactionType: 'benevolence_contribution',
            caseNumber: $this->case->case_number
        );

        if ($result['success']) {
            $responseData = $result['data'];
            $this->activeCheckoutRequestId = $responseData['Body']['stkCallback']['CheckoutRequestID']
                ?? $responseData['CheckoutRequestID']
                ?? $responseData['checkoutRequestID']
                ?? null;

            $this->stkSent = true;
            $this->dispatch('stk-sent', ['phone' => $this->phone]);
            return;
        }

        $this->dispatch('stk-error');
    }

    public function render()
    {
        return view('livewire.benevolence-contribution');
    }
}