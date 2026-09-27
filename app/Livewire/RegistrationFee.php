<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Services\KcbPaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Registration Fee Payment | ROSSET-SWA')]
class RegistrationFee extends Component
{
    public $phone = '';
    public $stkSent = false;
    public $registrationFeeAmount = 150; // Default fallback

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->phone = $user->phone;
            
            // If the user already paid, redirect straight away
            if ($user->registration_fee_paid) {
                return redirect()->route('portal');
            }
        }

        // Fetch registration fee from system settings
        $setting = Setting::first();
        if ($setting && $setting->registration_fee !== null) {
            $this->registrationFeeAmount = $setting->registration_fee;
        }
    }

    /**
     * Polling method called every few seconds by frontend 
     * to check if the payment has been completed via IPN webhook.
     */
    public function checkPaymentStatus()
    {
        $user = Auth::user()?->fresh();

        if ($user && $user->registration_fee_paid) {
            // Dispatch event to show SweetAlert and redirect
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

        // Use clean paybill account identifier for authenticated STK Push
        $accountIdentifier = config('services.kcb.account_prefix', '7936435');

        // Use dynamically fetched registration fee amount from settings
        $amount = (float) $this->registrationFeeAmount; 

        // Trigger KCB STK Push passing authenticated user ID
        $result = $paymentService->stkPush(
            phone: $this->phone,
            amount: $amount, 
            accountIdentifier: $accountIdentifier,
            description: 'ROSSET-SWA Registration Fee',
            userId: $user->id
        );

        if ($result['success']) {
            $this->stkSent = true;
            // Dispatch browser event to trigger success SweetAlert
            $this->dispatch('stk-sent', [
                'phone' => $this->phone
            ]);
            return;
        }

        // Dispatch friendly user-facing error event hiding all technical details
        $this->dispatch('stk-error');
    }

    public function render()
    {
        return view('livewire.registration-fee');
    }
}