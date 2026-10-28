<div class="min-h-screen bg-slate-50 py-8">
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
                    <span>Top-up Guide</span>
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

        <!-- ================= TOP-UP GUIDE MODAL ================= -->
        @if($showTopUpModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-5">
                    
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">Solidarity Wallet Top-up Guide</h3>
                        <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                    </div>

                    <div class="space-y-3 text-xs text-slate-700 leading-relaxed">
                        <p>
                            To top up your solidarity fund virtual balance, please follow the manual payment steps below:
                        </p>

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2.5 font-mono text-[11px]">
                            <div class="flex justify-between">
                                <span class="text-slate-500 font-bold">Paybill Number:</span>
                                <span class="text-slate-900 font-bold">522533</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-slate-500 font-bold">Account Number:</span>
                                <span class="text-slate-900 font-bold text-right">7936435#&lt;member_no&gt;<br><span class="text-[10px] text-slate-400 font-normal">(e.g., 7936435#121 for Member No. 121)</span></span>
                            </div>
                        </div>

                        <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 text-sky-900 space-y-1.5 font-medium">
                            <p class="font-bold uppercase tracking-wider text-[10px] text-sky-800">Next Step (Important):</p>
                            <p>
                                Once you have completed the M-Pesa payment, please send the confirmation message to Treasurer <strong>Mr. Josephine</strong> via phone number <strong class="font-mono">0711317235</strong>.
                            </p>
                            <p class="text-sky-700 italic">
                                Kindly inform the treasurer explicitly that the money is strictly for your <strong>Solidarity Fund</strong>.
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="button" wire:click="closeModal" class="px-5 py-2.5 rounded-lg bg-[#2EA3F2] text-white font-bold text-xs uppercase tracking-wider hover:bg-sky-500 transition cursor-pointer shadow-xs">
                            Close Guide
                        </button>
                    </div>

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
</script>