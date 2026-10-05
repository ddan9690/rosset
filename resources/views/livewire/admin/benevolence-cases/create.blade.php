<div class="space-y-6 max-w-3xl mx-auto relative">
    
    <!-- ================= MULTI-STEP PROCESSING PROGRESS MODAL ================= -->
    @if($isProcessing)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex flex-col items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 animate-in fade-in zoom-in-95 duration-200">
                
                <!-- Step Indicator Header Icons -->
                <div class="flex items-center justify-center space-x-3">
                    <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full text-xs font-bold transition-all {{ $activeStep === 1 ? 'bg-sky-500 text-white shadow-md' : 'bg-emerald-100 text-emerald-800' }}">
                        <i data-lucide="{{ $activeStep > 1 ? 'check' : 'folder-plus' }}" class="w-3.5 h-3.5"></i>
                        <span>1. Opening Case</span>
                    </div>

                    <div class="w-6 h-0.5 bg-slate-200"></div>

                    <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full text-xs font-bold transition-all {{ $activeStep === 2 ? 'bg-sky-500 text-white shadow-md' : 'bg-slate-100 text-slate-400' }}">
                        <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                        <span>2. Solidarity Deductions</span>
                    </div>
                </div>

                <!-- Dynamic Step Content -->
                <div class="space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider">
                        {{ $activeStep === 1 ? 'Initializing Case File' : 'Processing Wallet Deductions' }}
                    </h4>
                    <p class="text-xs text-slate-500 font-medium min-h-[36px] flex items-center justify-center px-4 leading-relaxed">
                        {{ $progressMessage }}
                    </p>
                </div>
                
                <!-- Progress Bar -->
                <div class="space-y-2 text-left">
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200 p-0.5">
                        <div class="bg-[#2EA3F2] h-full rounded-full transition-all duration-300 ease-out shadow-xs" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-[11px] font-mono text-slate-400">
                        <span>Execution Progress</span>
                        <span class="font-bold text-slate-700">{{ $progressPercentage }}%</span>
                    </div>
                </div>

            </div>
        </div>
    @endif

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

        <!-- ================= CASE FORM FIELDS ================= -->
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

                <!-- Solidarity Auto-Settle Checkbox Toggle -->
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 flex items-start space-x-3">
                    <input type="checkbox" wire:model="auto_settle" id="auto_settle" class="mt-0.5 rounded text-[#2EA3F2] focus:ring-[#2EA3F2] h-4 w-4 border-slate-300 cursor-pointer">
                    <div class="leading-relaxed">
                        <label for="auto_settle" class="font-bold text-slate-800 cursor-pointer">Automatically deduct from eligible members' Solidarity Funds</label>
                        <p class="text-[11px] text-slate-500 mt-0.5">If checked, members with sufficient solidarity balances (excluding the case owner, suspended members, and prior contributors) will be automatically settled.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.benevolence.cases.index') }}" wire:navigate class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition">Cancel</a>
                    <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer">
                        <span wire:loading.remove>Open Case & Process</span>
                        <span wire:loading>Initializing...</span>
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

@script
<script>
    // Listen for completion event dispatched from Livewire component
    $wire.on('case-opened-success', (event) => {
        const data = event[0] || event;
        Swal.fire({
            title: 'Case Opened Successfully!',
            text: 'Please wait while you are redirected to the management list...',
            icon: 'success',
            timer: 2400,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
            didClose: () => {
                window.location.href = data.redirectUrl;
            }
        });
    });
</script>
@endscript