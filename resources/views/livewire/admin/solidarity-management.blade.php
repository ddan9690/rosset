<div class="space-y-6 max-w-5xl mx-auto py-6 px-3 sm:px-6 lg:px-8"
     x-data="{}"
     @swal:success.window="Swal.fire({ title: 'Success!', text: $event.detail[0].message, icon: 'success', confirmButtonColor: '#0E3A59', confirmButtonText: 'OK' })">

    <!-- Top Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white border border-slate-200 rounded-xl px-5 py-3.5 shadow-xs">
        <div>
            <h1 class="text-sm sm:text-base font-extrabold text-slate-900">Solidarity Fund Management</h1>
            {{-- <p class="text-[11px] text-slate-500">Monitor active member solidarity balances and process manual top-ups.</p> --}}
        </div>
        <div class="flex items-center justify-between sm:justify-end space-x-3">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:underline">
                &larr; Dashboard
            </a>
            <button type="button" wire:click="openTopUpModal"
                class="px-3.5 py-2 bg-gradient-to-r from-sky-500 to-[#2EA3F2] hover:from-sky-600 hover:to-sky-500 text-white text-[11px] font-extrabold uppercase tracking-wider rounded-xl shadow-sm transition flex items-center space-x-1 cursor-pointer">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                <span>Top-up Fund</span>
            </button>
        </div>
    </div>

    <!-- Summary Total Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Solidarity Pool</p>
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-mono mt-0.5">KSH {{ number_format($totalSolidarityBalance, 0) }}</h3>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="wallet" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    <!-- Members Solidarity Table Container -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs space-y-3">
        
        <!-- Search & Filter Bar -->
        <div class="p-3.5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800">Active Solidarity Ledger</h3>
            <div class="w-full sm:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search M/NO or Name..." 
                       class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden">
            </div>
        </div>

        <div>
            <table class="w-full text-left text-xs table-auto">
                <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th wire:click="sortBy('membership_number')" class="py-2.5 px-3 sm:px-5 cursor-pointer hover:text-slate-900 transition select-none w-24 sm:w-32">
                            <div class="flex items-center space-x-1">
                                <span>M/NO</span>
                                @if($sortField === 'membership_number')
                                    <span>{!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-2.5 px-3 sm:px-5">Name</th>
                        <th wire:click="sortBy('balance')" class="py-2.5 px-3 sm:px-5 text-right cursor-pointer hover:text-slate-900 transition select-none w-28 sm:w-36">
                            <div class="flex items-center justify-end space-x-1">
                                <span>Balance (KES)</span>
                                @if($sortField === 'balance')
                                    <span>{!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($solidarityAccounts as $account)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3 sm:px-5 font-mono font-bold text-[#2EA3F2]">
                                {{ $account->user->membership_number ?? '—' }}
                            </td>
                            <td class="py-3 px-3 sm:px-5 font-bold text-slate-900 truncate max-w-[150px] sm:max-w-none">
                                {{ trim(($account->user->first_name ?? '') . ' ' . ($account->user->last_name ?? '')) }}
                            </td>
                            <td class="py-3 px-3 sm:px-5 text-right font-mono font-extrabold text-emerald-600">
                                {{ number_format($account->balance, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-slate-400 text-xs">
                                No active solidarity fund balances found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($solidarityAccounts->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $solidarityAccounts->links() }}
            </div>
        @endif
    </div>

    <!-- ================= TOP-UP MEMBER WALLET MODAL ================= -->
    @if($showTopUpModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3">
            <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4 border border-slate-200">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-slate-900">Top-up Member Solidarity Fund</h3>
                    <button type="button" wire:click="closeTopUpModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer text-xs">✕</button>
                </div>

                <form wire:submit.prevent="saveTopUp" class="space-y-3.5 text-xs">
                    
                    <!-- Member Search Field -->
                    <div class="space-y-1 relative">
                        <label class="block font-bold uppercase tracking-wider text-slate-700 text-[10px]">Search Member <span class="text-red-500">*</span></label>
                        
                        @if(!$selectedMember)
                            <input type="text" wire:model.live.debounce.300ms="searchMember" placeholder="Search Membership No, TSC, or Name..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden bg-white">
                            
                            @if(count($searchedMembers) > 0)
                                <div class="absolute z-20 left-0 right-0 bg-white border border-slate-200 rounded-lg shadow-lg mt-1 overflow-hidden divide-y divide-slate-100 max-h-40 overflow-y-auto">
                                    @foreach($searchedMembers as $member)
                                        <button type="button" wire:click="selectMember({{ $member->id }})" class="w-full text-left px-3 py-2.5 hover:bg-slate-50 transition flex items-center justify-between cursor-pointer text-xs">
                                            <div>
                                                <p class="font-bold text-slate-900 text-[11px]">{{ $member->first_name ?? 'N/A' }} {{ $member->last_name ?? '' }}</p>
                                                <p class="text-slate-500 font-mono text-[10px]">Mem No: {{ $member->membership_number ?? 'N/A' }}</p>
                                            </div>
                                            <span class="font-bold text-[#2EA3F2] text-[10px]">Select &rarr;</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-slate-900 text-[11px]">{{ $selectedMember->first_name }} {{ $selectedMember->last_name }}</p>
                                    <p class="text-[10px] font-mono text-slate-500">Mem No: {{ $selectedMember->membership_number ?? 'N/A' }}</p>
                                </div>
                                <button type="button" wire:click="clearSelectedMember" class="text-[11px] font-bold text-red-600 hover:underline cursor-pointer">Change</button>
                            </div>
                        @endif
                        @error('user_id') <span class="text-red-500 text-[10px] mt-0.5 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 text-[10px] mb-1">Amount (KSH) <span class="text-red-500">*</span></label>
                        <input type="number" step="1" wire:model="amount" placeholder="e.g. 500" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden bg-white">
                        @error('amount') <span class="text-red-500 text-[10px] mt-0.5 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 text-[10px] mb-1">Notes / Remarks <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <textarea wire:model="notes" rows="2" placeholder="Add payment reference..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden bg-white"></textarea>
                        @error('notes') <span class="text-red-500 text-[10px] mt-0.5 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeTopUpModal" class="px-3.5 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition bg-[#0E3A59] hover:bg-slate-900 cursor-pointer">
                            <span wire:loading.remove wire:target="saveTopUp">Credit Wallet</span>
                            <span wire:loading wire:target="saveTopUp">Processing...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>