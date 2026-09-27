<div class="space-y-6 max-w-3xl mx-auto">
    <!-- Header Section -->
    <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Case Details: {{ $case->case_number }}</h3>
            <p class="text-xs text-slate-500 mt-0.5">Affected Member: <span class="font-bold text-slate-700">{{ $case->member->first_name ?? '' }} {{ $case->member->last_name ?? '' }}</span> | TSC: <span class="font-mono text-slate-700">{{ $case->member->tsc_number ?? 'N/A' }}</span> | Phone: <span class="font-mono text-slate-700">{{ $case->member->phone ?? 'N/A' }}</span> | Category: <span class="font-bold text-slate-700">{{ $case->category->name ?? 'N/A' }}</span></p>
        </div>
        <div class="flex items-center space-x-2 flex-shrink-0">
            <!-- Status Action Buttons (Trigger Modals) -->
            @if($case->status !== 'active')
                <button type="button" wire:click="openStatusModal('active')" class="px-3 py-2 rounded-lg bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-emerald-500 transition cursor-pointer">Make Active</button>
            @endif
            @if($case->status !== 'suspended')
                <button type="button" wire:click="openStatusModal('suspended')" class="px-3 py-2 rounded-lg bg-amber-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-amber-400 transition cursor-pointer">Suspend</button>
            @endif
            @if($case->status !== 'closed')
                <button type="button" wire:click="openStatusModal('closed')" class="px-3 py-2 rounded-lg bg-slate-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-slate-500 transition cursor-pointer">Close</button>
            @endif

            <a href="{{ route('admin.benevolence.cases.edit', [$case->id, $case->slug]) }}" wire:navigate class="px-3.5 py-2 rounded-lg bg-[#2EA3F2] text-white font-bold text-xs uppercase tracking-wider hover:bg-sky-500 transition">Edit</a>
            <a href="{{ route('admin.benevolence.cases.index') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:underline px-2">&larr; Back</a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Main Content Details Card -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-6 text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-slate-200 pb-6">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Affected Member Details</p>
                <p class="font-bold text-slate-900 text-sm mt-1">{{ $case->member->first_name ?? '' }} {{ $case->member->last_name ?? '' }}</p>
                <p class="text-slate-500 font-mono mt-0.5">Membership No: {{ $case->member->membership_number ?? 'N/A' }} | TSC: {{ $case->member->tsc_number ?? 'N/A' }}</p>
                <p class="text-slate-500 font-mono mt-0.5">School: {{ $case->member->school ?? 'N/A' }} | Phone: {{ $case->member->phone ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Category & Standard Amount</p>
                <p class="font-bold text-slate-900 text-sm mt-1">{{ $case->category->name ?? 'N/A' }}</p>
                <p class="font-mono font-bold text-emerald-600 mt-0.5">KES {{ number_format($case->category->amount ?? 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 border-b border-slate-200 pb-6 items-center">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Contribution Deadline</p>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="font-mono font-bold text-slate-900">{{ $case->deadline }}</span>
                    <button type="button" wire:click="openDeadlineModal" class="text-[#2EA3F2] font-bold hover:underline cursor-pointer text-[11px]">[Change]</button>
                </div>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</p>
                <p class="mt-1">
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider 
                        @if($case->status === 'active') bg-emerald-100 text-emerald-800 
                        @elseif($case->status === 'suspended') bg-amber-100 text-amber-800 
                        @else bg-slate-200 text-slate-700 @endif">
                        {{ ucfirst($case->status) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Created By (Admin)</p>
                <p class="font-bold text-slate-800 mt-1">{{ $case->creator->first_name ?? 'Admin' }} {{ $case->creator->last_name ?? '' }}</p>
            </div>
        </div>

        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Case Details / Description</p>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 text-slate-700 leading-relaxed">
                {{ $case->case_details }}
            </div>
        </div>
    </div>

    <!-- ================= STATUS MODAL ================= -->
    @if($showStatusModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 p-6 max-w-sm w-full space-y-4 text-xs">
                <h4 class="font-bold text-sm uppercase tracking-wider text-slate-900">Confirm Status Change</h4>
                <p class="text-slate-600">Are you sure you want to change this case status to <span class="font-bold uppercase text-[#0E3A59]">{{ $selectedStatus }}</span>?</p>
                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" wire:click="$set('showStatusModal', false)" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    <button type="button" wire:click="updateStatus" class="px-4 py-2 rounded-lg bg-[#2EA3F2] text-white font-bold uppercase tracking-wider hover:bg-sky-500 transition cursor-pointer">Confirm</button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= DEADLINE MODAL ================= -->
    @if($showDeadlineModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 p-6 max-w-sm w-full space-y-4 text-xs">
                <h4 class="font-bold text-sm uppercase tracking-wider text-slate-900">Change Contribution Deadline</h4>
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">New Deadline Date</label>
                    <input type="date" wire:model="newDeadline" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                    @error('newDeadline') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" wire:click="$set('showDeadlineModal', false)" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    <button type="button" wire:click="updateDeadline" class="px-4 py-2 rounded-lg bg-[#2EA3F2] text-white font-bold uppercase tracking-wider hover:bg-sky-500 transition cursor-pointer">Save Deadline</button>
                </div>
            </div>
        </div>
    @endif
</div>