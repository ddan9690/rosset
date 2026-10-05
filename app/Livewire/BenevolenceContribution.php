<?php

namespace App\Livewire;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution as BenevolenceContributionModel;
use App\Models\Transaction;
use App\Services\KcbPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            session()->flash('message', $this->getFlashMessage('already'));

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

    protected function getFlashMessage(string $type = 'success'): string
    {
        $memberName = trim(($this->case->member->first_name ?? '') . ' ' . ($this->case->member->last_name ?? ''));

        if ($type === 'already') {
            return 'You have already contributed to case ' . $this->case->case_number . '. Thank you for standing with ' . $memberName . '. Coming together for unity and support.';
        }

        return 'Thank you for standing with ' . $memberName . ' in respect of case ' . $this->case->case_number . '. Coming together for unity and support.';
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
        if (!Auth::check() || !$this->activeCheckoutRequestId) {
            return;
        }

        $successfulTransaction = Transaction::query()
            ->where('user_id', Auth::id())
            ->where('case_number', $this->case->case_number)
            ->where('checkout_request_id', $this->activeCheckoutRequestId)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'success')
            ->first();

        if ($successfulTransaction) {
            DB::transaction(function () use ($successfulTransaction) {
                BenevolenceContributionModel::firstOrCreate(
                    [
                        'transaction_id' => $successfulTransaction->id,
                    ],
                    [
                        'benevolence_case_id' => $this->case->id,
                        'user_id' => Auth::id(),
                        'amount' => $successfulTransaction->amount,
                        'payment_channel' => 'KCB Paybill / STK Polling',
                        'reference_number' => $successfulTransaction->reference_number ?? ('KCB-' . $successfulTransaction->checkout_request_id),
                        'notes' => 'Confirmed via Livewire polling check',
                    ]
                );
            });

            Log::info('Benevolence Contribution Confirmed By Polling', [
                'user_id' => Auth::id(),
                'case_number' => $this->case->case_number,
                'transaction_id' => $successfulTransaction->id,
                'reference' => $successfulTransaction->reference_number,
                'amount' => $successfulTransaction->amount,
            ]);

            $this->stkSent = false;
            $this->activeCheckoutRequestId = null;

            session()->flash('message', $this->getFlashMessage('success'));

            return redirect()->route('portal');
        }

        $failedTransaction = Transaction::query()
            ->where('user_id', Auth::id())
            ->where('checkout_request_id', $this->activeCheckoutRequestId)
            ->where('case_number', $this->case->case_number)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'failed')
            ->first();

        if ($failedTransaction) {
            $this->stkSent = false;
            $this->activeCheckoutRequestId = null;
            $this->dispatch('stk-error', ['message' => 'Payment failed or was cancelled.']);
        }
    }

    public function sendStkPrompt(KcbPaymentService $paymentService)
    {
        if ($this->hasContributed()) {
            $this->alreadyContributed = true;

            session()->flash('message', $this->getFlashMessage('already'));

            return redirect()->route('portal');
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

        $user = Auth::user();
        $amount = (float) ($this->case->category->amount ?? 0);
        $accountIdentifier = config('services.kcb.account_number', '7936435');

        $existingPending = Transaction::where('user_id', $user->id)
            ->where('case_number', $this->case->case_number)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();

        if ($existingPending) {
            $this->dispatch('stk-error', ['message' => 'Payment request already sent. Try again in a moment.']);
            return;
        }

        $result = $paymentService->stkPush(
            phone: $this->phone,
            amount: $amount,
            accountIdentifier: $accountIdentifier,
            description: 'Benevolence Contribution - Case ' . $this->case->case_number
        );

        if ($result['success']) {
            $checkoutRequestId = $result['checkout_request_id'] ?? null;
            $merchantRequestId = $result['merchant_request_id'] ?? null;
            $normalizedPhone = $result['phone_number'] ?? $this->phone;

            if (!$checkoutRequestId) {
                Log::error('Benevolence STK Request Accepted But Checkout ID Missing', ['result' => $result]);
                $this->dispatch('stk-error', ['message' => 'KCB accepted the payment request, but the checkout reference could not be read.']);
                return;
            }

            Transaction::create([
                'user_id' => $user->id,
                'reference_number' => null,
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
                'type' => 'benevolence_contribution',
                'case_number' => $this->case->case_number,
                'amount' => $amount,
                'currency' => 'KES',
                'status' => 'pending',
                'phone_number' => $normalizedPhone,
                'description' => 'Benevolence Contribution - Case ' . $this->case->case_number,
            ]);

            $this->activeCheckoutRequestId = $checkoutRequestId;
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