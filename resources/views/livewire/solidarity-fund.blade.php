<div class="min-h-screen bg-slate-50 py-8" @if ($stkSent) wire:poll.3s="checkPaymentStatus" @endif>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Navigation Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            @if(!$stkSent)
                <a href="{{ route('portal') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                    <span>&larr; Back to Portal</span>
                </a>
            @else
                <span class="text-xs font-bold text-slate-400">Transaction in Progress...</span>
            @endif
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Member Solidarity Fund</span>
        </div>

        @if (session()->has('message'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if($stkSent)
            <!-- WAITING FOR PAYMENT BANNER -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 shadow-sm text-center space-y-4 max-w-lg mx-auto">
                <div class="flex justify-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i data-lucide="smartphone" class="w-6 h-6 text-blue-600 animate-pulse"></i>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-blue-900">Waiting for M-Pesa Payment Confirmation</h4>
                    <p class="text-xs text-blue-800 mt-1">An STK push prompt has been sent to <span class="font-mono font-bold">{{ $phone }}</span>.</p>
                </div>
                <div class="flex items-center justify-center gap-2 text-xs text-blue-700 font-medium">
                    <i data-lucide="loader-circle" class="w-4 h-4 animate-spin"></i>
                    <span>Polling payment status automatically...</span>
                </div>
            </div>
        @endif

        <!-- Summary Card Grid (Single Available Balance Card) -->
        <div class="grid grid-cols-1 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Available Balance</p>
                        <span class="text-slate-300">•</span>
                        <button type="button" wire:click="$set('showInfoModal', true)" class="text-[11px] font-bold text-[#2EA3F2] hover:underline cursor-pointer">
                            What is solidarity fund?
                        </button>
                    </div>
                    <h3 class="text-2xl font-extrabold text-slate-900 font-mono mt-1">KSH {{ number_format($wallet->balance, 0) }}</h3>
                </div>
                <button wire:click="openTopUpModal" class="px-4 py-2 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer flex items-center space-x-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Top-up</span>
                </button>
            </div>
        </div>

        <!-- Wallet Top-Up Statement Table -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">Wallet Top-up Statement</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                            <th class="py-3 px-4">Reference Number</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Date Paid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($statements as $tx)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $tx->reference_number }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-emerald-600">{{ number_format($tx->amount, 0) }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">
                                    {{ $tx->paid_at ? $tx->paid_at->format('d-M-y H:i') : $tx->created_at->format('d-M-y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">No wallet top-up transactions found in your statement yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">
                {{ $statements->links() }}
            </div>
        </div>

        <!-- ================= WHAT IS SOLIDARITY FUND MODAL ================= -->
        @if(isset($showInfoModal) && $showInfoModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-[#2EA3F2]"></i>
                            About Solidarity Fund
                        </h3>
                        <button type="button" wire:click="$set('showInfoModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                    </div>

                    <div class="space-y-3 text-xs text-slate-600 leading-relaxed">
                        <p>
                            The <strong>Solidarity Fund</strong> is a virtual wallet designed for your convenience. It allows funds to be deducted automatically whenever bereavement cases arise, saving you from the hassle of paying for each case individually.
                        </p>
                        <p>
                            You never have to worry about defaulting or forgetting to contribute. Just top up your solidarity fund and let your benevolence obligations be debited seamlessly.
                        </p>
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-amber-900 space-y-1">
                            <p class="font-bold uppercase text-[10px] tracking-wide text-amber-800">Please Note:</p>
                            <p>• The maximum balance a member can hold in the fund is <strong>KSH {{ number_format($maxLimit, 0) }}</strong>.</p>
                            <p>• The solidarity fund is <strong>non-withdrawable</strong>.</p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="button" wire:click="$set('showInfoModal', false)" class="px-5 py-2.5 rounded-lg bg-[#2EA3F2] text-white font-bold text-xs uppercase tracking-wider hover:bg-sky-500 transition cursor-pointer shadow-xs">
                            Got It
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- ================= TOP-UP MODAL ================= -->
        @if($showTopUpModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-6">
                    
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">Top-up Solidarity Wallet</h3>
                        <button type="button" wire:click="closeModal" wire:loading.attr="disabled" wire:target="topUpWallet" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">✕</button>
                    </div>

                    <form wire:submit="topUpWallet" class="space-y-4">
                        <p class="text-xs text-slate-600 leading-relaxed">
                            An STK push will be sent to your phone number to complete the top-up. (Maximum allowed balance: <strong class="font-mono">Ksh {{ number_format($maxLimit, 0) }}</strong>)
                        </p>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Amount (KSH)</label>
                            <input type="number" wire:model="amount" placeholder="e.g. 500" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                            @error('amount') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">M-Pesa Phone Number</label>
                                <button type="button" wire:click="togglePhoneEditable" wire:loading.attr="disabled" wire:target="topUpWallet" class="text-[11px] font-bold text-[#2EA3F2] hover:underline cursor-pointer disabled:opacity-50">
                                    {{ $isPhoneEditable ? 'Use my number' : 'Change' }}
                                </button>
                            </div>
                            <input type="text" wire:model="phone" @if(!$isPhoneEditable) disabled @endif class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none {{ $isPhoneEditable ? 'bg-white' : 'bg-slate-100 text-slate-500 cursor-not-allowed' }}">
                            @error('phone') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-2">
                            <button type="button" wire:click="closeModal" wire:loading.attr="disabled" wire:target="topUpWallet" class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-300 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer flex items-center justify-center space-x-1.5 disabled:opacity-50">
                                <span wire:loading.remove wire:target="topUpWallet">Send STK Push</span>
                                <span wire:loading wire:target="topUpWallet">Please Wait...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => { 
        if (window.lucide) { window.lucide.createIcons(); }
    });
    document.addEventListener('DOMContentLoaded', () => { 
        if (window.lucide) { window.lucide.createIcons(); }
    });

    window.addEventListener('stk-sent', event => {
        const detail = event.detail?.[0] ?? event.detail ?? {};
        const formattedAmount = Number(detail.amount ?? 0).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});

        Swal.fire({ 
            title: 'STK Push Sent!', 
            html: 'Check your phone (<strong>' + (detail.phone ?? '') + '</strong>) and enter your M-Pesa PIN to complete the top-up.<br><br>' +
                  '<div style="background-color: #fef3c7; border: 1px solid #fde68a; padding: 10px; border-radius: 6px; text-align: left; font-size: 13px; color: #92400e;">' +
                  '<strong>Please Note:</strong> This process may take up to <strong>40 seconds</strong>. Please be patient, and <strong>do not close or refresh this page</strong>.' +
                  '</div>',
            icon: 'info', 
            showConfirmButton: false, 
            allowOutsideClick: false
        });
    });

    window.addEventListener('exceeds-limit', event => {
        const detail = event.detail?.[0] ?? event.detail ?? {};
        const maxLimit = Number(detail.max ?? 1000).toLocaleString();
        
        Swal.fire({
            title: 'Maximum Limit Exceeded',
            text: 'You can only have a maximum of Ksh ' + maxLimit + ' in your solidarity fund.',
            icon: 'warning',
            confirmButtonColor: '#2EA3F2',
            confirmButtonText: 'Got It'
        });
    });

    window.addEventListener('payment-successful', () => {
        Swal.close();
        Swal.fire({
            title: 'Payment Successful!',
            text: 'Wallet top-up confirmed successfully!',
            icon: 'success',
            timer: 2500,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false
        });
    });

    window.addEventListener('stk-error', event => {
        Swal.close();
        const detail = event.detail?.[0] ?? event.detail ?? {};
        Swal.fire({ 
            title: 'Payment Request Failed', 
            text: detail.message ?? 'Sorry, payment is not successful. Please try again.', 
            icon: 'error',
            confirmButtonColor: '#2EA3F2',
            confirmButtonText: 'Try Again'
        });
    });
</script>