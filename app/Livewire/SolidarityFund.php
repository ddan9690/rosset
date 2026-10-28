<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\SolidarityFund as SolidarityModel;
use App\Models\Transaction;
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

    public $showTopUpModal = false;
    public $showInfoModal = false;

    public function openTopUpModal()
    {
        $this->showTopUpModal = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showTopUpModal = false;
    }

    /* 
    |--------------------------------------------------------------------------
    | STK Push Top-Up Methods (Temporarily Disabled per Association Guidelines)
    |--------------------------------------------------------------------------
    | The automated STK payment methods below are retained in the codebase for future 
    | use but are currently bypassed in favor of manual Paybill instructions to the treasurer.
    */

    /*
    public function topUpWallet(KcbPaymentService $paymentService)
    {
        // Disabled
    }

    public function checkPaymentStatus()
    {
        // Disabled
    }
    */

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