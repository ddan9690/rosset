<div class="min-h-screen bg-slate-50 py-8" @if ($stkSent) wire:poll.3s="checkPaymentStatus" @endif>
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            <a href="{{ route('portal') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                <span>&larr; Back to Portal</span>
            </a>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Benevolence Contribution</span>
        </div>

        @if (session()->has('message'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- ================= BENEVOLENCE PAYMENT CARD ================= -->
        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs max-w-md mx-auto space-y-6 my-8">

            <!-- KCB / Payment Header Icon with Badge -->
            <div class="flex flex-col items-center space-y-2">
                <div class="w-14 h-14 bg-blue-50 text-[#2EA3F2] rounded-2xl flex items-center justify-center shadow-xs border border-blue-100">
                    <i data-lucide="shield-check" class="w-7 h-7"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-50 text-emerald-700 border border-emerald-200">
                    KCB M-Pesa Secure Pay
                </span>
            </div>

            <div class="space-y-1 text-center">
                <h3 class="text-base font-bold text-slate-900">Case #{{ $case->case_number }}</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Contributing towards <strong class="text-slate-900">{{ trim(($case->member->salutation ?? '') . ' ' . ($case->member->first_name ?? '') . ' ' . ($case->member->last_name ?? '')) }}</strong>
                </p>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Category:</span>
                    <span class="font-bold text-slate-800 uppercase">{{ $case->category->name ?? 'Benevolence' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Member:</span>
                    <span class="font-bold text-slate-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2 font-bold">
                    <span class="text-slate-600">Amount Due:</span>
                    <span class="text-[#2EA3F2] font-mono text-sm">KES {{ number_format($amount) }}</span>
                </div>
            </div>

            <!-- Green Important Note -->
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl font-medium flex items-start space-x-2">
                <i data-lucide="info" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <span><strong>Note:</strong> Only valid Safaricom M-Pesa numbers are allowed for STK push payments.</span>
            </div>

            <form wire:submit="sendStkPrompt" class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">M-Pesa Phone Number</label>
                        <button type="button" wire:click="togglePhoneEditable" class="text-[11px] font-bold text-[#2EA3F2] hover:underline cursor-pointer focus:outline-none">
                            {{ $isPhoneEditable ? 'Use my number' : 'Change number' }}
                        </button>
                    </div>
                    <input type="text" wire:model="phone" @if(!$isPhoneEditable) disabled @endif
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none transition {{ $isPhoneEditable ? 'bg-white' : 'bg-slate-100 text-slate-500 cursor-not-allowed' }}"
                        placeholder="07XXXXXXXX">
                    @error('phone')
                        <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3.5 px-4 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow bg-[#2EA3F2] hover:bg-sky-500 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center space-x-2">
                    <i data-lucide="send" class="w-4 h-4" wire:loading.remove wire:target="sendStkPrompt"></i>
                    <span wire:loading.remove wire:target="sendStkPrompt">Pay KES {{ number_format($amount) }} (STK Push)</span>
                    <span wire:loading wire:target="sendStkPrompt">Sending STK Push...</span>
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="{{ route('portal') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:underline">
                    &larr; Back to Portal
                </a>
            </div>

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

    window.addEventListener('stk-sent', event => {
        const phone = event.detail[0]?.phone || '';
        Swal.fire({
            title: 'STK Push Sent!',
            text: 'Please check your phone (' + phone + ') and enter your M-Pesa PIN to complete the contribution.',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            timerProgressBar: true
        });
    });

    window.addEventListener('payment-successful', event => {
        Swal.fire({
            title: 'Contribution Successful!',
            text: 'Thank you! Your contribution has been verified successfully.',
            icon: 'success',
            timer: 2500,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false
        }).then(() => {
            window.location.href = "{{ route('portal') }}";
        });
    });

    window.addEventListener('stk-error', event => {
        Swal.fire({
            title: 'Payment Request Failed',
            text: 'Unable to initiate payment prompt. Please check your phone number and try again.',
            icon: 'error',
            confirmButtonColor: '#2EA3F2',
            confirmButtonText: 'Try Again'
        });
    });
</script>