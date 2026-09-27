<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Create Benevolence Case</h3>
            <p class="text-xs text-slate-500 mt-0.5">Search a member first to open a new benevolence case.</p>
        </div>
        <a href="{{ route('admin.benevolence.cases.index') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:underline">&larr; Back to List</a>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-6">
        
        <!-- ================= MEMBER SEARCH SECTION ================= -->
        <div class="space-y-2 relative">
            <label class="block font-bold uppercase tracking-wider text-slate-700 text-xs">Search Member (By Membership No, TSC No, or Name)</label>
            
            @if(!$selectedMember)
                <input type="text" wire:model.live.debounce.300ms="searchMember" placeholder="Type to search member..." class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                
                @if(count($searchedMembers) > 0)
                    <div class="absolute z-20 left-0 right-0 bg-white border border-slate-200 rounded-lg shadow-lg mt-1 overflow-hidden divide-y divide-slate-100">
                        @foreach($searchedMembers as $member)
                            <button type="button" wire:click="selectMember({{ $member->id }})" class="w-full text-left px-4 py-3 hover:bg-slate-50 transition flex items-center justify-between cursor-pointer text-xs">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $member->first_name ?? 'N/A' }} {{ $member->last_name ?? '' }}</p>
                                    <p class="text-slate-500 font-mono text-[11px]">Membership No: {{ $member->membership_number ?? 'N/A' }} | TSC: {{ $member->tsc_number ?? 'N/A' }} | School: {{ $member->school ?? 'N/A' }}</p>
                                </div>
                                <span class="font-bold text-[#2EA3F2]">Select &rarr;</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <!-- Selected Member Details Card -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center justify-between">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs w-full">
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Full Name</span>
                            <span class="font-bold text-slate-900">{{ $selectedMember->first_name ?? 'N/A' }} {{ $selectedMember->last_name ?? '' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Membership No</span>
                            <span class="font-mono font-semibold text-slate-700">{{ $selectedMember->membership_number ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">TSC Number</span>
                            <span class="font-mono text-slate-700">{{ $selectedMember->tsc_number ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">School</span>
                            <span class="text-slate-700 font-medium truncate">{{ $selectedMember->school ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Phone Number</span>
                            <span class="font-mono text-slate-700">{{ $selectedMember->phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <button type="button" wire:click="clearSelectedMember" class="text-xs font-bold text-red-600 hover:underline ml-4 flex-shrink-0 cursor-pointer">Change</button>
                </div>
            @endif
            @error('user_id') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- ================= CASE FORM FIELDS (SHOWN ONLY WHEN MEMBER IS SELECTED) ================= -->
        @if($selectedMember)
            <form wire:submit="save" class="space-y-4 text-xs pt-4 border-t border-slate-200 animate-in fade-in duration-200">
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Benevolence Category</label>
                    <select wire:model="benevolence_category_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} (KES {{ number_format($category->amount) }})</option>
                        @endforeach
                    </select>
                    @error('benevolence_category_id') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Case Details / Description</label>
                    <textarea wire:model="case_details" rows="4" placeholder="Provide brief information regarding this case." class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white"></textarea>
                    @error('case_details') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Contribution Deadline</label>
                    <input type="date" wire:model="deadline" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                    @error('deadline') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.benevolence.cases.index') }}" wire:navigate class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition">Cancel</a>
                    <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer">
                        <span wire:loading.remove>Save Case</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        @else
            <div class="py-8 text-center text-slate-400 text-xs border-t border-slate-200">
                Please search and select a member above to proceed with creating the benevolence case.
            </div>
        @endif

    </div>
</div>