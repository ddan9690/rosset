<div class="min-h-screen bg-slate-50 py-8" @if ($stkSent) wire:poll.3s="checkPaymentStatus" @endif>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Navigation Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            <a href="{{ route('portal') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                <span>&larr; Back to Portal</span>
            </a>
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

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Available Balance</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 font-mono mt-1">KES {{ number_format($wallet->balance, 2) }}</h3>
                </div>
                <button wire:click="openTopUpModal" class="px-4 py-2 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer flex items-center space-x-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Top-up</span>
                </button>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Lifetime Top-ups</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 font-mono mt-1">KES {{ number_format($wallet->total_topups, 2) }}</h3>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Lifetime Deductions</p>
                <h3 class="text-2xl font-extrabold text-red-600 font-mono mt-1">KES {{ number_format($wallet->total_deductions, 2) }}</h3>
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
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Date Paid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($statements as $tx)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $tx->reference_number }}</td>
                                <td class="py-3 px-4">{{ $tx->description }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-emerald-600">+ KES {{ number_format($tx->amount, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">
                                    {{ $tx->paid_at ? $tx->paid_at->format('Y-m-d H:i') : $tx->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">No wallet top-up transactions found in your statement yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">
                {{ $statements->links() }}
            </div>
        </div>

        <!-- ================= TOP-UP MODAL ================= -->
        @if($showTopUpModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-6">
                    
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">Top-up Solidarity Wallet</h3>
                        <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                    </div>

                    <form wire:submit="topUpWallet" class="space-y-4">
                        <p class="text-xs text-slate-600 leading-relaxed">
                            An STK push will be sent to your phone number to complete the top-up.
                        </p>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Amount (KES)</label>
                            <input type="number" min="50" wire:model="amount" placeholder="e.g. 500" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                            @error('amount') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">M-Pesa Phone Number</label>
                                <button type="button" wire:click="togglePhoneEditable" class="text-[11px] font-bold text-[#2EA3F2] hover:underline cursor-pointer">
                                    {{ $isPhoneEditable ? 'Use my number' : 'Change' }}
                                </button>
                            </div>
                            <input type="text" wire:model="phone" @if(!$isPhoneEditable) disabled @endif class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none {{ $isPhoneEditable ? 'bg-white' : 'bg-slate-100 text-slate-500 cursor-not-allowed' }}">
                            @error('phone') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-300 transition cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer flex items-center justify-center space-x-1.5">
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
        Swal.fire({ 
            title: 'STK Push Sent!', 
            text: 'Please check your phone (' + (detail.phone ?? '') + ') and enter your M-Pesa PIN to complete the top-up.', 
            icon: 'info', 
            showConfirmButton: false, 
            allowOutsideClick: false,
            timerProgressBar: true 
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