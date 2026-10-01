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

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount($id)
    {
        $this->caseId = $id;

        $this->case = BenevolenceCase::with([
            'member',
            'category',
        ])->findOrFail($id);

        $user = Auth::user();

        abort_unless($user, 403);

        /*
        |--------------------------------------------------------------------------
        | Phone
        |--------------------------------------------------------------------------
        */

        $this->phone = $user->phone ?? '';

        $this->defaultPhone = $user->phone ?? '';

        /*
        |--------------------------------------------------------------------------
        | Set Dynamic Amount From Category
        |--------------------------------------------------------------------------
        */

        $this->amount = $this->case->category->amount ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Check Existing Contribution
        |--------------------------------------------------------------------------
        */

        $this->alreadyContributed = $this->hasContributed();

        if ($this->alreadyContributed) {

            session()->flash(
                'message',
                'You have already contributed to case ' .
                $this->case->case_number . '.'
            );

            return redirect()->route('portal');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Check If Member Has Contributed
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Toggle Phone
    |--------------------------------------------------------------------------
    */

    public function togglePhoneEditable()
    {
        $this->isPhoneEditable = !$this->isPhoneEditable;

        if (!$this->isPhoneEditable) {
            $this->phone = $this->defaultPhone;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Check Payment Status
    |--------------------------------------------------------------------------
    */

    public function checkPaymentStatus()
    {
        if (!Auth::check()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Check Database Directly
        |--------------------------------------------------------------------------
        */

        $successfulTransaction = Transaction::query()
            ->where('user_id', Auth::id())
            ->where(
                'case_number',
                $this->case->case_number
            )
            ->where(
                'type',
                'benevolence_contribution'
            )
            ->where(
                'status',
                'success'
            )
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Payment Confirmed
        |--------------------------------------------------------------------------
        */

        if ($successfulTransaction) {

            Log::info(
                'Benevolence Contribution Confirmed By Polling',
                [
                    'user_id' => Auth::id(),
                    'case_number' =>
                        $this->case->case_number,
                    'transaction_id' =>
                        $successfulTransaction->id,
                    'reference' =>
                        $successfulTransaction->reference_number,
                    'amount' =>
                        $successfulTransaction->amount,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Stop Polling
            |--------------------------------------------------------------------------
            */

            $this->stkSent = false;

            /*
            |--------------------------------------------------------------------------
            | Redirect Directly To Portal
            |--------------------------------------------------------------------------
            */

            return redirect()->route(
                'portal',
                [
                    'payment' => 'success',
                    'case' =>
                        $this->case->case_number,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Still Pending
        |--------------------------------------------------------------------------
        */

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Send STK Prompt
    |--------------------------------------------------------------------------
    */

    public function sendStkPrompt(
        KcbPaymentService $paymentService
    ) {

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Contribution
        |--------------------------------------------------------------------------
        */

        if ($this->hasContributed()) {

            $this->alreadyContributed = true;

            return redirect()->route(
                'portal',
                [
                    'payment' => 'already',
                    'case' =>
                        $this->case->case_number,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Phone
        |--------------------------------------------------------------------------
        */

        $this->validate(
            [
                'phone' => [
                    'required',
                    'string',
                    'regex:/^(?:254[17]\d{8}|0[17]\d{8}|[17]\d{8})$/',
                ],
            ],
            [
                'phone.regex' =>
                    'Please enter a valid Safaricom phone number.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Dynamic Amount
        |--------------------------------------------------------------------------
        */

        $amount = $this->case->category->amount ?? 0;

        /*
        |--------------------------------------------------------------------------
        | KCB Account
        |--------------------------------------------------------------------------
        */

        $accountIdentifier = config(
            'services.kcb.account_prefix',
            '7936435'
        );

        /*
        |--------------------------------------------------------------------------
        | Send STK
        |--------------------------------------------------------------------------
        */

        $result = $paymentService->stkPush(

            phone: $this->phone,

            amount: $amount,

            accountIdentifier: $accountIdentifier,

            description:
                'Benevolence Contribution - Case ' .
                $this->case->case_number,

            userId: Auth::id(),

            transactionType:
                'benevolence_contribution',

            caseNumber:
                $this->case->case_number
        );

        /*
        |--------------------------------------------------------------------------
        | Successful STK Request
        |--------------------------------------------------------------------------
        */

        if ($result['success']) {

            $this->activeCheckoutRequestId =
                $result['checkout_request_id']
                ?? data_get(
                    $result,
                    'data.response.CheckoutRequestID'
                )
                ?? null;

            if (!$this->activeCheckoutRequestId) {

                Log::error(
                    'STK Request Accepted But Checkout ID Missing',
                    [
                        'result' => $result,
                    ]
                );

                $this->dispatch(
                    'stk-error',
                    message:
                        'KCB accepted the payment request, but the checkout reference could not be read.'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Start Polling
            |--------------------------------------------------------------------------
            */

            $this->stkSent = true;

            /*
            |--------------------------------------------------------------------------
            | Inform Browser
            |--------------------------------------------------------------------------
            */

            $this->dispatch(
                'stk-sent',
                [
                    'phone' =>
                        $this->phone,

                    'amount' =>
                        $amount,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | STK Failed
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'stk-error',
            [
                'message' =>
                    $result['message']
                    ??
                    'Unable to initiate payment.',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.benevolence-contribution'
        );
    }
}