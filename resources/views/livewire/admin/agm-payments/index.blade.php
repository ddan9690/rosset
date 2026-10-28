<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-2xs">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">AGM Years Directory</h3>
            <p class="text-xs text-slate-500 mt-0.5">Select an AGM year card below to view detailed reports and member statements.</p>
        </div>
        <button type="button" wire:click="openCreateModal"
                class="px-4 py-2.5 bg-[#2EA3F2] hover:bg-sky-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-2xs transition flex items-center space-x-1.5 cursor-pointer">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Capture New Payment</span>
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center space-x-2 shadow-2xs">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Year Cards Grid or Empty State -->
    @if(count($yearCards) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($yearCards as $card)
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-2xs hover:border-[#2EA3F2] transition flex flex-col justify-between space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-sky-50 text-[#2EA3F2] border border-sky-100 rounded-lg text-xs font-mono font-extrabold uppercase tracking-widest">
                            {{ $card['year'] }} AGM
                        </span>
                        <i data-lucide="calendar-check" class="w-5 h-5 text-slate-400"></i>
                    </div>

                    <!-- Primary Focus: Members Paid | Sub: Total Collections -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Members Paid</p>
                        <h3 class="text-xl font-extrabold text-slate-900 font-mono">
                            {{ number_format($card['total_members']) }} Members Paid
                        </h3>
                        <p class="text-xs text-slate-500 font-bold pt-1 font-mono">
                            Total Collections: KSH {{ number_format($card['total_collections']) }}
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <a href="{{ route('admin.agm.show', ['id' => $card['year'], 'year' => $card['year']]) }}" wire:navigate
                           class="w-full py-2.5 px-4 bg-slate-900 hover:bg-[#0E3A59] text-white rounded-xl text-xs font-bold uppercase tracking-wider transition flex items-center justify-center space-x-2">
                            <span>View {{ $card['year'] }} Report</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State When Table Has No Records -->
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center space-y-4 shadow-2xs">
            <div class="w-12 h-12 bg-sky-50 text-[#2EA3F2] rounded-full flex items-center justify-center mx-auto border border-sky-100">
                <i data-lucide="folder-open" class="w-6 h-6"></i>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider">No Payments Found for Any AGM</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Click the capture new payment button below to add a new record.</p>
            </div>
            <div class="pt-2">
                <button type="button" wire:click="openCreateModal"
                        class="px-5 py-2.5 bg-[#2EA3F2] hover:bg-sky-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-2xs transition inline-flex items-center space-x-1.5 cursor-pointer">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Capture New Payment</span>
                </button>
            </div>
        </div>
    @endif

    <!-- CAPTURE PAYMENT MODAL -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold text-sm uppercase tracking-wider text-slate-900">Capture AGM Payment</h3>
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
                                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">AGM Year <span class="text-red-500">*</span></label>
                                    <select wire:model="agmYear" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                                        @for($y = 2024; $y <= 2030; $y++)
                                            <option value="{{ $y }}">{{ $y }} AGM</option>
                                        @endfor
                                    </select>
                                    @error('agmYear') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
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
                            Please search and select a member above to proceed with recording the AGM payment.
                        </div>
                    @endif

                </div>

            </div>
        </div>
    @endif

</div>