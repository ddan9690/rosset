<div class="min-h-screen bg-slate-50 py-8" @if ($stkSent) wire:poll.3s="checkPaymentStatus" @endif>
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            <a href="/" wire:navigate
                class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                <span>&larr; Back to Home</span>
            </a>
            <div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-red-600 hover:underline cursor-pointer">Log
                        Out</button>
                </form>
            </div>
        </div>

        @if (session()->has('message'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- ================= REGISTRATION FEE PAYMENT CARD ================= -->
        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs max-w-md mx-auto space-y-6 my-8">

            <div class="w-12 h-12 bg-blue-50 text-[#2EA3F2] rounded-full flex items-center justify-center mx-auto">
                <i data-lucide="credit-card" class="w-6 h-6"></i>
            </div>

            <div class="space-y-2 text-center">
                <h3 class="text-base font-bold text-slate-900">Registration Fee Payment</h3>
                <p class="text-sm text-slate-700 leading-relaxed font-medium">
                    Dear <strong class="text-slate-900">{{ Auth::user()->first_name ?? 'Member' }}</strong>, please
                    complete your registration by paying the registration fee of <strong
                        class="text-slate-900 font-mono">KES {{ number_format($registrationFeeAmount) }}</strong>.
                </p>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-600">Account Name:</span>
                    <span class="font-bold text-slate-800">{{ Auth::user()->first_name }}
                        {{ Auth::user()->last_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">TSC Number:</span>
                    <span class="font-bold text-slate-800">{{ Auth::user()->tsc_number }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2 font-bold">
                    <span class="text-slate-600">Amount Due:</span>
                    <span class="text-[#2EA3F2]">KES {{ number_format($registrationFeeAmount) }}</span>
                </div>
            </div>

            <form wire:submit="sendStkPrompt" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">M-Pesa Phone
                        Number</label>
                    <input type="text" wire:model="phone" required
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none"
                        placeholder="07XXXXXXXX">
                    @error('phone')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Professional Brand Blue Submit Button with Loading State -->
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3.5 px-4 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow bg-[#2EA3F2] hover:bg-sky-500 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center space-x-2">
                    <i data-lucide="send" class="w-4 h-4" wire:loading.remove wire:target="sendStkPrompt"></i>
                    <span wire:loading.remove wire:target="sendStkPrompt">Send STK Prompt</span>
                    <span wire:loading wire:target="sendStkPrompt">Please Wait...</span>
                </button>
            </form>

        </div>

    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        lucide.createIcons();
    });
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });

    // Listen for successful STK dispatch
    window.addEventListener('stk-sent', event => {
        Swal.fire({
            title: 'STK Push Sent!',
            text: 'Please check your phone and enter your M-Pesa PIN to complete the registration fee payment.',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            timerProgressBar: true
        });
    });

    // Listen for confirmed payment completion from the backend IPN webhook
    window.addEventListener('payment-successful', event => {
        Swal.fire({
            title: 'Payment Successful!',
            text: 'Payment successful, redirecting you to the portal.',
            icon: 'success',
            timer: 2500,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false
        }).then(() => {
            window.location.href = "{{ route('portal') }}";
        });
    });

    // Listen for failed STK dispatch with friendly user-facing message
    window.addEventListener('stk-error', event => {
        Swal.fire({
            title: 'Payment Request Failed',
            text: 'Sorry, payment is not successful. Please try again or contact admin.',
            icon: 'error',
            confirmButtonColor: '#2EA3F2',
            confirmButtonText: 'Try Again'
        });
    });
</script>
