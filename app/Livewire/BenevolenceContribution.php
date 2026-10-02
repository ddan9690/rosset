<?php

namespace App\Livewire;

use App\Models\BenevolenceCase;
use App\Models\Transaction;
use App\Services\KcbPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Benevolence Contribution | ROSSET-SWA')]
class BenevolenceContribution extends Component
{
    public $caseId;
    public $case;
    public $amount = 0;
    public $phone = '';
    public $defaultPhone = '';
    public $isPhoneEditable = false;
    public $stkSent = false;
    public $activeCheckoutRequestId = null;
    public $alreadyContributed = false;

    public function mount($id)
    {
        $this->caseId = $id;

        $this->case = BenevolenceCase::with([
            'member',
            'category',
        ])->findOrFail($id);

        $user = Auth::user();

        abort_unless($user, 403);

        $this->phone = $user->phone ?? '';
        $this->defaultPhone = $user->phone ?? '';
        $this->amount = $this->case->category->amount ?? 0;

        $this->alreadyContributed = $this->hasContributed();

        if ($this->alreadyContributed) {
            session()->flash(
                'message',
                'You have already contributed to case ' . $this->case->case_number . '.'
            );

            return redirect()->route('portal');
        }
    }

    protected function hasContributed(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return Transaction::query()
            ->where('user_id', $user->id)
            ->where('case_number', $this->case->case_number)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'success')
            ->exists();
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
        if (!Auth::check()) {
            return;
        }

        $successfulTransaction = Transaction::query()
            ->where('user_id', Auth::id())
            ->where('case_number', $this->case->case_number)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'success')
            ->latest('id')
            ->first();

        if ($successfulTransaction) {
            Log::info('Benevolence Contribution Confirmed By Polling', [
                'user_id' => Auth::id(),
                'case_number' => $this->case->case_number,
                'transaction_id' => $successfulTransaction->id,
                'reference' => $successfulTransaction->reference_number,
                'amount' => $successfulTransaction->amount,
            ]);

            $this->stkSent = false;

            return redirect()->route('portal', [
                'payment' => 'success',
                'case' => $this->case->case_number,
            ]);
        }

        return null;
    }

    public function sendStkPrompt(KcbPaymentService $paymentService)
    {
        if ($this->hasContributed()) {
            $this->alreadyContributed = true;

            return redirect()->route('portal', [
                'payment' => 'already',
                'case' => $this->case->case_number,
            ]);
        }

        $this->validate(
            [
                'phone' => [
                    'required',
                    'string',
                    'regex:/^(?:254[17]\d{8}|0[17]\d{8}|[17]\d{8})$/',
                ],
            ],
            [
                'phone.regex' => 'Please enter a valid Safaricom phone number.',
            ]
        );

        $amount = $this->case->category->amount ?? 0;

        $accountIdentifier = config('services.kcb.account_prefix', '7936435');

        $result = $paymentService->stkPush(
            phone: $this->phone,
            amount: $amount,
            accountIdentifier: $accountIdentifier,
            description: 'Benevolence Contribution - Case ' . $this->case->case_number,
            userId: Auth::id(),
            transactionType: 'benevolence_contribution',
            caseNumber: $this->case->case_number
        );

        if ($result['success']) {
            $this->activeCheckoutRequestId = $result['checkout_request_id']
                ?? data_get($result, 'data.response.CheckoutRequestID')
                ?? null;

            if (!$this->activeCheckoutRequestId) {
                Log::error('STK Request Accepted But Checkout ID Missing', [
                    'result' => $result,
                ]);

                $this->dispatch(
                    'stk-error',
                    message: 'KCB accepted the payment request, but the checkout reference could not be read.'
                );

                return;
            }

            $this->stkSent = true;

            $this->dispatch('stk-sent', [
                'phone' => $this->phone,
                'amount' => $amount,
            ]);

            return;
        }

        $this->dispatch('stk-error', [
            'message' => $result['message'] ?? 'Unable to initiate payment.',
        ]);
    }

    public function render()
    {
        return view('livewire.benevolence-contribution');
    }
}