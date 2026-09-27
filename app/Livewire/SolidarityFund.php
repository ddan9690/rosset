<?php

namespace App\Livewire;

use App\Models\SolidarityFund as SolidarityModel;
use App\Models\Transaction;
use App\Services\KcbPaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Solidarity Fund Statement | ROSSET-SWA')]
class SolidarityFund extends Component
{
    use WithPagination;

    public $amount = '';
    public $phone = '';
    public $defaultPhone = '';
    public $showTopUpModal = false;
    public $isPhoneEditable = false;

    public $stkSent = false;
    public $activeCheckoutRequestId = null;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->phone = $user->phone;
            $this->defaultPhone = $user->phone;
        }
    }

    public function openTopUpModal()
    {
        $this->amount = '';
        $this->phone = $this->defaultPhone;
        $this->isPhoneEditable = false;
        $this->showTopUpModal = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showTopUpModal = false;
    }

    public function togglePhoneEditable()
    {
        $this->isPhoneEditable = !$this->isPhoneEditable;
    }

    public function topUpWallet(KcbPaymentService $paymentService)
    {
        $this->validate([
            'amount' => 'required|integer|min:50',
            'phone' => ['required', 'string', 'regex:/^(?:254[17]\d{8}|0[17]\d{8}|[17]\d{8})$/'],
        ], [
            'amount.min' => 'The minimum top-up amount is KES 50.',
            'phone.regex' => 'Please enter a valid M-Pesa phone number format.',
        ]);

        $user = Auth::user();
        $accountIdentifier = config('services.kcb.account_prefix', '7936435');

        $result = $paymentService->stkPush(
            phone: $this->phone,
            amount: (float) $this->amount,
            accountIdentifier: $accountIdentifier,
            description: 'Solidarity Wallet Top-up',
            userId: $user->id,
            transactionType: 'wallet_topup'
        );

        if ($result['success']) {
            $responseData = $result['data'];
            $this->activeCheckoutRequestId = $responseData['Body']['stkCallback']['CheckoutRequestID']
                ?? $responseData['CheckoutRequestID']
                ?? $responseData['checkoutRequestID']
                ?? null;

            $this->showTopUpModal = false;
            $this->stkSent = true;
            $this->dispatch('stk-sent');
        } else {
            $this->dispatch('stk-error');
        }
    }

    public function checkPaymentStatus()
    {
        if (!$this->activeCheckoutRequestId) {
            return;
        }

        $transaction = Transaction::where('checkout_request_id', $this->activeCheckoutRequestId)
            ->where('status', 'success')
            ->first();

        if ($transaction) {
            $this->stkSent = false;
            $this->activeCheckoutRequestId = null;
            $this->dispatch('payment-successful');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $wallet = SolidarityModel::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'total_topups' => 0, 'total_deductions' => 0]
        );

        $statements = Transaction::where('user_id', $user->id)
            ->where('type', 'wallet_topup')
            ->where('status', 'success')
            ->latest('paid_at')
            ->paginate(10);

        return view('livewire.solidarity-fund', [
            'wallet' => $wallet,
            'statements' => $statements,
        ]);
    }
}