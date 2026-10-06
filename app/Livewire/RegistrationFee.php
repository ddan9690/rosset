<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\Transaction;
use App\Services\KcbPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Registration Fee Payment | ROSSET-SWA')]
class RegistrationFee extends Component
{
    public $phone = '';
    public $stkSent = false;
    public $registrationFeeAmount;
    public $activeCheckoutRequestId = null;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->phone = $user->phone ?? '';
            
            // If the user already paid, redirect straight away with flash message
            if ($user->registration_fee_paid) {
                session()->flash('message', 'You have already paid your registration fee. Welcome to the portal.');
                return redirect()->route('portal');
            }
        }

        // Fetch registration fee dynamically from system settings
        $this->loadRegistrationFee();
    }

    protected function loadRegistrationFee()
    {
        $setting = Setting::current();
        $this->registrationFeeAmount = $setting->registration_fee;
    }

    protected function getFlashMessage(string $type = 'success'): string
    {
        $user = Auth::user();
        $userName = $user->name ?? 'Member';

        if ($type === 'already') {
            return 'You have already paid your registration fee. Welcome back, ' . $userName . '.';
        }

        return 'Thank you for paying your registration fee. Your account is now active. Welcome, ' . $userName . '!';
    }

    /**
     * Polling method called every 3 seconds by frontend 
     * to check if the payment has been completed via IPN webhook.
     */
    public function checkPaymentStatus()
    {
        $user = Auth::user();

        if (!$user || !$this->activeCheckoutRequestId) {
            return;
        }

        // Check transactions table for successful payment matching checkout request ID
        $successfulTransaction = Transaction::query()
            ->where('user_id', $user->id)
            ->where('checkout_request_id', $this->activeCheckoutRequestId)
            ->where('type', 'registration_fee')
            ->where('status', 'success')
            ->first();

        if ($successfulTransaction || $user->fresh()->registration_fee_paid) {
            $this->stkSent = false;
            $this->activeCheckoutRequestId = null;

            // Ensure user activation and flag are set if transaction was captured externally
            $freshUser = $user->fresh();
            if (!$freshUser->registration_fee_paid) {
                $freshUser->activateAfterPayment();
            }

            session()->flash('message', $this->getFlashMessage('success'));
            return redirect()->route('portal');
        }

        $failedTransaction = Transaction::query()
            ->where('user_id', $user->id)
            ->where('checkout_request_id', $this->activeCheckoutRequestId)
            ->where('type', 'registration_fee')
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
        $user = Auth::user();
        if ($user && $user->registration_fee_paid) {
            session()->flash('message', $this->getFlashMessage('already'));
            return redirect()->route('portal');
        }

        $this->validate([
            'phone' => ['required', 'string', 'regex:/^(?:254[17]\d{8}|0[17]\d{8}|[17]\d{8})$/'],
        ], [
            'phone.regex' => 'Please enter a valid phone number format.',
        ]);

        // Always fetch the freshest amount from the Setting model right before initiating STK
        $this->loadRegistrationFee();

        $accountIdentifier = config('services.kcb.account_number', '7936435');
        $amount = (float) $this->registrationFeeAmount; 

        // Let the Service handle the API call and Transaction creation cleanly
        $result = $paymentService->stkPush(
            phone: $this->phone,
            amount: $amount, 
            accountIdentifier: $accountIdentifier,
            description: 'ROSSET-SWA Registration Fee',
            userId: $user->id,
            transactionType: 'registration_fee'
        );

        if ($result['success']) {
            $this->activeCheckoutRequestId = $result['checkout_request_id'] ?? null;

            if (!$this->activeCheckoutRequestId) {
                Log::error('Registration Fee STK Accepted But Checkout ID Missing', [
                    'result' => $result,
                ]);

                $this->dispatch('stk-error', ['message' => 'KCB accepted the request, but the checkout reference could not be read.']);
                return;
            }

            $this->stkSent = true;
            $this->dispatch('stk-sent', [
                'phone' => $this->phone
            ]);
        } else {
            $this->dispatch('stk-error', [
                'message' => $result['message'] ?? 'Unable to initiate registration fee payment.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.registration-fee');
    }
}