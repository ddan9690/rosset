<div
    class="min-h-screen bg-slate-50 py-8"
    @if($stkSent)
        wire:poll.3s="checkPaymentStatus"
    @endif
>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- TOP NAVIGATION -->

        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-sm">

            <a
                href="{{ route('portal') }}"
                wire:navigate
                class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline"
            >
                &larr; Back to Portal
            </a>

            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Benevolence Contribution
            </span>

        </div>


        <!-- PAYMENT CARD -->

        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm max-w-md mx-auto space-y-6">

            <!-- ICON -->

            <div class="flex flex-col items-center space-y-2">

                <div class="w-14 h-14 bg-blue-50 text-[#2EA3F2] rounded-2xl flex items-center justify-center border border-blue-100">

                    <i
                        data-lucide="shield-check"
                        class="w-7 h-7"
                    ></i>

                </div>

                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-50 text-emerald-700 border border-emerald-200">

                    Contribute

                </span>

            </div>


            <!-- CASE -->

            <div class="space-y-2 text-center">

                <h3 class="text-base font-bold text-slate-900">

                    Case No. {{ $case->case_number }}

                </h3>

                <p class="text-xs text-slate-600 leading-relaxed">

                    Contributing towards Gordon Awino for the loss of 
                    <strong class="text-slate-900 lowercase">
                        {{ $case->category->name ?? 'benevolence' }}
                    </strong>

                </p>

            </div>


            <!-- PAYMENT INFORMATION -->

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3 text-xs">

                <div class="flex justify-between border-t border-slate-200 pt-3">

                    <span class="text-slate-600 font-bold">
                        Contribution Amount:
                    </span>

                    <span class="text-[#2EA3F2] font-mono text-sm font-bold">

                        KSH {{ number_format($amount, 0) }}

                    </span>

                </div>

            </div>


            <!-- PAYMENT FORM -->

            @if(!$stkSent)

                <form
                    wire:submit="sendStkPrompt"
                    class="space-y-4"
                >

                    <!-- PHONE -->

                    <div>

                        <div class="flex items-center justify-between mb-1">

                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">

                                M-Pesa Phone Number

                            </label>

                            <button
                                type="button"
                                wire:click="togglePhoneEditable"
                                class="text-[11px] font-bold text-[#2EA3F2] hover:underline"
                            >

                                {{
                                    $isPhoneEditable
                                        ? 'Use my number'
                                        : 'Change number'
                                }}

                            </button>

                        </div>


                        <input
                            type="text"
                            wire:model="phone"

                            @disabled(!$isPhoneEditable)

                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none transition
                            {{
                                $isPhoneEditable
                                    ? 'bg-white'
                                    : 'bg-slate-100 text-slate-500'
                            }}"

                            placeholder="07XXXXXXXX"
                        >


                        @error('phone')

                            <span class="text-red-500 text-[10px] mt-1 block">

                                {{ $message }}

                            </span>

                        @enderror

                    </div>


                    <!-- PAY BUTTON -->

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="sendStkPrompt"
                        class="w-full py-3.5 px-4 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow bg-[#2EA3F2] hover:bg-sky-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >

                        <i
                            data-lucide="send"
                            class="w-4 h-4"
                            wire:loading.remove
                            wire:target="sendStkPrompt"
                        ></i>

                        <span
                            wire:loading.remove
                            wire:target="sendStkPrompt"
                        >
                            Pay KSH {{ number_format($amount, 0) }}
                        </span>

                        <span
                            wire:loading
                            wire:target="sendStkPrompt"
                        >
                            Sending STK Push...
                        </span>

                    </button>

                </form>

            @else

                <!-- WAITING -->

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 text-center space-y-4">

                    <div class="flex justify-center">

                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">

                            <i
                                data-lucide="smartphone"
                                class="w-6 h-6 text-blue-600"
                            ></i>

                        </div>

                    </div>


                    <div>

                        <h4 class="text-sm font-bold text-blue-900">

                            Waiting for Payment Confirmation

                        </h4>

                        <p class="text-xs text-blue-800 mt-2 leading-relaxed">

                            An M-Pesa prompt has been sent to:

                        </p>

                        <p class="font-mono font-bold text-blue-900 mt-1">

                            {{ $phone }}

                        </p>

                    </div>


                    <div class="bg-white border border-blue-200 rounded-lg p-3">

                        <p class="text-[11px] text-blue-800 leading-relaxed">

                            Enter your M-Pesa PIN on your phone.

                            <br>

                            This page will automatically redirect after KCB confirms the payment.

                        </p>

                    </div>


                    <div class="flex items-center justify-center gap-2 text-xs text-blue-700">

                        <i
                            data-lucide="loader-circle"
                            class="w-4 h-4 animate-spin"
                        ></i>

                        <span>
                            Checking payment status...
                        </span>

                    </div>

                </div>

            @endif


            <!-- BACK -->

            <div class="text-center pt-2">

                <a
                    href="{{ route('portal') }}"
                    wire:navigate
                    class="text-xs font-bold text-slate-600 hover:underline"
                >

                    &larr; Back to Portal

                </a>

            </div>

        </div>

    </div>
</div>


<script>

    function initializeLucideIcons() {

        if (window.lucide) {

            window.lucide.createIcons();

        }

    }

    document.addEventListener(
        'DOMContentLoaded',
        initializeLucideIcons
    );

    document.addEventListener(
        'livewire:navigated',
        initializeLucideIcons
    );


    /*
    |--------------------------------------------------------------------------
    | STK SENT
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'stk-sent',
        event => {

            const detail =
                event.detail?.[0]
                ?? event.detail
                ?? {};

            Swal.fire({

                title: 'STK Push Sent!',

                text:
                    'Check your phone (' +
                    (detail.phone ?? '') +
                    ') and enter your M-Pesa PIN to complete the KSH ' +
                    Number(detail.amount ?? 0).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}) +
                    ' contribution.',

                icon: 'info',

                timer: 3000,

                timerProgressBar: true,

                showConfirmButton: false,

                allowOutsideClick: false

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | STK ERROR
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'stk-error',
        event => {

            const detail =
                event.detail?.[0]
                ?? event.detail
                ?? {};

            Swal.fire({

                title: 'Payment Request Failed',

                text:
                    detail.message
                    ??
                    'Unable to initiate payment prompt.',

                icon: 'error',

                confirmButtonText: 'Try Again',

                confirmButtonColor: '#2EA3F2'

            });

        }
    );

</script>