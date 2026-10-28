<div class="space-y-6">

    <!-- Header & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-xl shadow-2xs border border-slate-200">
        <div>
            <span class="text-[10px] font-bold font-mono uppercase tracking-wider text-[#2EA3F2] bg-sky-50 px-2.5 py-0.5 rounded border border-sky-100">
                AGM Year Statement (ID: {{ $id }})
            </span>
            <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wider pt-1">
                {{ $year }} Annual General Meeting Collections
            </h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- PDF Download Button -->
            <a href="{{ route('admin.pdf.agm-payments', $year) }}" target="_blank"
               class="px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-slate-800 hover:bg-slate-900 text-white transition inline-flex items-center space-x-1.5 shadow-2xs">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Download PDF Report</span>
            </a>
            
            <button type="button" wire:click="openCreateModal"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-[#2EA3F2] hover:bg-sky-500 text-white transition inline-flex items-center space-x-1.5 cursor-pointer shadow-2xs">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Capture New AGM Payment</span>
            </button>
            <a href="{{ route('admin.agm.index') }}" wire:navigate 
               class="px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 transition inline-flex items-center space-x-1">
                <span>&larr; Back</span>
            </a>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center space-x-2 shadow-2xs">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- STATISTICS CARDS (Primary Focus: Members Paid) -->
    <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        
        <!-- Total Members Paid Focus -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-1 col-span-3 sm:col-span-1 lg:col-span-2">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Members Paid</p>
            <h3 class="text-xl font-extrabold text-slate-900 font-mono">
                {{ number_format($totalMembersPaid) }} Paid
            </h3>
            <p class="text-[11px] text-slate-500 font-bold pt-0.5 font-mono">
                Total Collections: KSH {{ number_format($totalCollections) }}
            </p>
        </div>

        <!-- Gender Breakdown -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-1 col-span-3 sm:col-span-1 lg:col-span-1">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Gender</p>
            <div class="space-y-0.5 text-xs pt-1 font-mono">
                <div class="text-blue-600 font-bold">M: {{ $maleCount }}</div>
                <div class="text-pink-600 font-bold">F: {{ $femaleCount }}</div>
            </div>
        </div>

        <!-- School Level Breakdown -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-1 col-span-3 sm:col-span-1 lg:col-span-3">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">School Level Breakdown</p>
            <div class="grid grid-cols-3 gap-1 pt-1 text-center">
                <div class="bg-slate-50 p-1 rounded border border-slate-100">
                    <span class="text-[9px] text-slate-500 block">Pri</span>
                    <strong class="text-xs font-extrabold text-slate-900 font-mono">{{ $levelBreakdown['Primary'] ?? 0 }}</strong>
                </div>
                <div class="bg-slate-50 p-1 rounded border border-slate-100">
                    <span class="text-[9px] text-slate-500 block">Jun</span>
                    <strong class="text-xs font-extrabold text-slate-900 font-mono">{{ $levelBreakdown['Junior'] ?? 0 }}</strong>
                </div>
                <div class="bg-slate-50 p-1 rounded border border-slate-100">
                    <span class="text-[9px] text-slate-500 block">Sen</span>
                    <strong class="text-xs font-extrabold text-slate-900 font-mono">{{ $levelBreakdown['Senior'] ?? 0 }}</strong>
                </div>
            </div>
        </div>

    </div>

    <!-- PAID MEMBERS TABLE SECTION -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-2xs overflow-hidden p-5 space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">
                Paid Members ({{ $year }} AGM)
            </h3>

            <!-- Search Filter -->
            <div class="relative w-full sm:w-72">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, membership or TSC..."
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-hidden focus:ring-1 focus:ring-[#2EA3F2]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">M/NO</th>
                        <th class="py-3 px-4">MEMBER</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Date Paid</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($payments as $index => $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $payments->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-[#2EA3F2]">
                                {{ $p->user->membership_number ?? $p->user->tsc_number ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ trim(($p->user->salutation ?? '') . ' ' . ($p->user->first_name ?? '') . ' ' . ($p->user->last_name ?? '')) }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-600">
                                {{ number_format($p->amount) }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">
                                {{ $p->paid_at ? \Carbon\Carbon::parse($p->paid_at)->format('d-M-y') : ($p->created_at ? $p->created_at->format('d-M-y') : 'N/A') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button type="button" wire:click="deletePayment({{ $p->id }})" wire:confirm="Are you sure you want to delete this payment?"
                                        class="text-xs font-bold text-red-600 hover:underline cursor-pointer">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                No member payments recorded for the {{ $year }} AGM yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-3">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- CAPTURE PAYMENT MODAL -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-sm uppercase tracking-wider text-slate-900">Capture {{ $year }} AGM Payment</h3>
                        <p class="text-[11px] text-slate-500">Record a new contribution for Year: {{ $year }}</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer text-sm p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    
                    <!-- MEMBER SEARCH SECTION -->
                    <div class="space-y-2 relative">
                        <label class="block font-bold uppercase tracking-wider text-slate-700 text-xs">Search Member (By Membership No, TSC No, or Name)</label>
                        
                        @if(!$selectedMember)
                            <input type="text" wire:model.live.debounce.300ms="searchMember" placeholder="Type to search member..." 
                                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden bg-white">
                            
                            @if(count($searchedMembers) > 0)
                                <div class="absolute z-20 left-0 right-0 bg-white border border-slate-200 rounded-lg shadow-lg mt-1 overflow-hidden divide-y divide-slate-100 max-h-48 overflow-y-auto">
                                    @foreach($searchedMembers as $member)
                                        <button type="button" wire:click="selectMember({{ $member->id }})" 
                                                class="w-full text-left px-4 py-3 hover:bg-slate-50 transition flex items-center justify-between cursor-pointer text-xs">
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $member->first_name ?? 'N/A' }} {{ $member->last_name ?? '' }}</p>
                                                <p class="text-slate-500 font-mono text-[11px]">Membership No: {{ $member->membership_number ?? 'N/A' }} | TSC: {{ $member->tsc_number ?? 'N/A' }}</p>
                                            </div>
                                            <span class="font-bold text-[#2EA3F2]">Select &rarr;</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <!-- Selected Member Details Card -->
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between">
                                <div class="space-y-0.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Selected Member:</span>
                                    <h4 class="font-bold text-slate-900">{{ $selectedMember->first_name ?? 'N/A' }} {{ $selectedMember->last_name ?? '' }}</h4>
                                    <p class="text-slate-600 font-mono text-[11px]">Membership No: {{ $selectedMember->membership_number ?? 'N/A' }} | TSC: {{ $selectedMember->tsc_number ?? 'N/A' }}</p>
                                </div>
                                <button type="button" wire:click="clearSelectedMember" class="text-xs font-bold text-red-600 hover:underline ml-4 flex-shrink-0 cursor-pointer">Change</button>
                            </div>
                        @endif
                        @error('user_id') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    @if($selectedMember)
                        <form wire:submit.prevent="savePayment" class="space-y-4 pt-4 border-t border-slate-200 animate-in fade-in duration-200">
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">AGM Year</label>
                                    <input type="text" wire:model="agmYear" readonly
                                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg font-bold font-mono text-slate-600 bg-slate-100 cursor-not-allowed">
                                </div>

                                <div>
                                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Amount (KSH) <span class="text-red-500">*</span></label>
                                    <input type="number" wire:model="amount" step="1" 
                                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg font-mono font-bold text-slate-900 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                                    @error('amount') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Paid On Date <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="paid_at" 
                                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg font-mono font-bold text-slate-900 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                                @error('paid_at') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Transaction / Receipt Number <span class="text-slate-400 font-normal">(Optional)</span></label>
                                <input type="text" wire:model="referenceNumber" placeholder="e.g. QK12345678" 
                                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg font-mono font-bold uppercase text-slate-900 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Remarks / Notes <span class="text-slate-400 font-normal">(Optional)</span></label>
                                <input type="text" wire:model="notes" placeholder="e.g. Paid cash to treasurer" 
                                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-slate-800 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                            </div>

                            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                                <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold uppercase tracking-wider transition shadow-xs bg-[#0E3A59] hover:bg-slate-900 cursor-pointer">
                                    <span wire:loading.remove wire:target="savePayment">Save Payment</span>
                                    <span wire:loading wire:target="savePayment">Saving...</span>
                                </button>
                            </div>

                        </form>
                    @else
                        <div class="py-6 text-center text-slate-400 text-xs border-t border-slate-200">
                            Please search and select a member above to record this payment.
                        </div>
                    @endif

                </div>

            </div>
        </div>
    @endif

</div>