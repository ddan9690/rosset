<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\SolidarityFund as SolidarityModel;
use App\Models\Transaction;
use App\Services\KcbPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    public $showInfoModal = false;
    public $isPhoneEditable = false;

    public $stkSent = false;
    public $activeCheckoutRequestId = null;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->phone = $user->phone ?? '';
            $this->defaultPhone = $user->phone ?? '';
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

        if (!$this->isPhoneEditable) {
            $this->phone = $this->defaultPhone;
        }
    }

    public function topUpWallet(KcbPaymentService $paymentService)
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
            'phone' => ['required', 'string', 'regex:/^(?:254[17]\d{8}|0[17]\d{8}|[17]\d{8})$/'],
        ], [
            'amount.min' => 'The minimum top-up amount is Ksh 1.',
            'phone.regex' => 'Please enter a valid M-Pesa phone number format.',
        ]);

        $user = Auth::user();
        $amount = (float) $this->amount;

        // Fetch current solidarity wallet and global settings limit
        $wallet = SolidarityModel::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'total_topups' => 0, 'total_deductions' => 0]
        );

        $settings = Setting::current();
        $maxLimit = (float) ($settings->solidarity_max_balance ?? 1000);
        $projectedBalance = $wallet->balance + $amount;

        // Check if the deposit exceeds the maximum allowed balance
        if ($projectedBalance > $maxLimit) {
            $this->showTopUpModal = false;
            $this->dispatch('exceeds-limit', [
                'max' => $maxLimit,
                'current' => $wallet->balance
            ]);
            return;
        }

        $accountIdentifier = config('services.kcb.account_number', '7936435');

        $existingPending = Transaction::where('user_id', $user->id)
            ->where('type', 'wallet_topup')
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();

        if ($existingPending) {
            $this->showTopUpModal = false;
            $this->dispatch('stk-error', ['message' => 'Payment request already sent. Try again in a moment.']);
            return;
        }

        // Delegate the API call and pending transaction creation to KcbPaymentService
        $result = $paymentService->stkPush(
            phone: $this->phone,
            amount: $amount,
            accountIdentifier: $accountIdentifier,
            description: 'Solidarity Wallet Topup',
            userId: $user->id,
            transactionType: 'wallet_topup'
        );

        if ($result['success']) {
            $checkoutRequestId = $result['checkout_request_id'] ?? null;

            if (!$checkoutRequestId) {
                Log::error('Wallet Top-up STK Accepted But Checkout ID Missing', ['result' => $result]);
                $this->dispatch('stk-error', ['message' => 'KCB accepted the request, but the checkout reference could not be read.']);
                return;
            }

            $this->activeCheckoutRequestId = $checkoutRequestId;
            $this->showTopUpModal = false;
            $this->stkSent = true;

            $this->dispatch('stk-sent', [
                'phone' => $this->phone,
                'amount' => $this->amount,
            ]);
        } else {
            $this->dispatch('stk-error', [
                'message' => $result['message'] ?? 'Unable to initiate wallet top-up.',
            ]);
        }
    }

    public function checkPaymentStatus()
    {
        if (!Auth::check() || !$this->activeCheckoutRequestId) {
            return;
        }

        $successfulTransaction = Transaction::query()
            ->where('user_id', Auth::id())
            ->where('checkout_request_id', $this->activeCheckoutRequestId)
            ->where('type', 'wallet_topup')
            ->where('status', 'success')
            ->first();

        if ($successfulTransaction) {
            Log::info('Wallet Topup Confirmed By Polling', [
                'user_id' => Auth::id(),
                'transaction_id' => $successfulTransaction->id,
                'reference' => $successfulTransaction->reference_number,
                'amount' => $successfulTransaction->amount,
            ]);

            $this->stkSent = false;
            $this->activeCheckoutRequestId = null;

            $this->resetPage();
            $this->dispatch('payment-successful');
            return;
        }

        $failedTransaction = Transaction::query()
            ->where('user_id', Auth::id())
            ->where('checkout_request_id', $this->activeCheckoutRequestId)
            ->where('type', 'wallet_topup')
            ->where('status', 'failed')
            ->first();

        if ($failedTransaction) {
            $this->stkSent = false;
            $this->activeCheckoutRequestId = null;
            $this->dispatch('stk-error', ['message' => 'Payment failed or was cancelled.']);
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
            'maxLimit' => Setting::current()->solidarity_max_balance ?? 1000,
        ]);
    }
}