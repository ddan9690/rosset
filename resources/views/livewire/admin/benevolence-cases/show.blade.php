<div class="space-y-6 max-w-5xl mx-auto pb-12"
     x-data="{}"
     @swal:success.window="Swal.fire({ title: 'Success!', text: $event.detail[0].message, icon: 'success', timer: 2500, showConfirmButton: false })"
     @swal:error.window="Swal.fire({ title: 'Error!', text: $event.detail[0].message, icon: 'error' })"
     @swal:warning.window="Swal.fire({ title: $event.detail[0].title || 'Warning', text: $event.detail[0].message, icon: 'warning', confirmButtonColor: '#0E3A59', confirmButtonText: 'OK' })"
     @swal:confirm-excess.window="
        Swal.fire({
            title: $event.detail[0].title,
            text: $event.detail[0].message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0E3A59',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Credit Solidarity Fund',
            cancelButtonText: 'No, Re-enter Amount'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.confirmSaveWithExcess();
            }
        });
     ">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs gap-4">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">Case Details: {{ $case->case_number }}</h3>
            <p class="text-xs text-slate-500 mt-0.5">Affected Member: <span class="font-bold text-slate-700">{{ $case->member->first_name ?? '' }} {{ $case->member->last_name ?? '' }}</span> | TSC: <span class="font-mono text-slate-700">{{ $case->member->tsc_number ?? 'N/A' }}</span> | Category: <span class="font-bold text-slate-700">{{ $case->category->name ?? 'N/A' }}</span></p>
        </div>
        <div class="flex items-center space-x-2 flex-shrink-0">
            @if($case->status !== 'active')
                <button type="button" wire:click="openStatusModal('active')" class="px-3 py-2 rounded-lg bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-emerald-500 transition cursor-pointer">Make Active</button>
            @endif
            
            @if($case->status === 'active')
                <button type="button" onclick="confirmSuspend()" class="px-3 py-2 rounded-lg bg-amber-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-amber-400 transition cursor-pointer">Suspend & Refund</button>
            @endif

            @if($case->status !== 'closed')
                <button type="button" wire:click="openStatusModal('closed')" class="px-3 py-2 rounded-lg bg-slate-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-slate-500 transition cursor-pointer">Close</button>
            @endif

            <a href="{{ route('admin.pdf.benevolence-case', [$case->id, $case->slug]) }}" target="_blank" class="px-3.5 py-2 rounded-lg bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider hover:bg-emerald-600 transition flex items-center space-x-1">
                <span>PDF Report</span>
            </a>

            <a href="{{ route('admin.benevolence.cases.edit', [$case->id, $case->slug]) }}" wire:navigate class="px-3.5 py-2 rounded-lg bg-[#2EA3F2] text-white font-bold text-xs uppercase tracking-wider hover:bg-sky-500 transition">Edit</a>
            <a href="{{ route('admin.benevolence.cases.index') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:underline px-2">&larr; Back</a>
        </div>
    </div>

    <!-- Compact Informative Case Details Card -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-4 space-y-4 text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-3 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Affected Member</span>
                <strong class="text-slate-900 font-bold">{{ $case->member->first_name ?? '' }} {{ $case->member->last_name ?? '' }}</strong>
                <p class="text-slate-500 font-mono text-[11px]">M/NO: {{ $case->member->membership_number ?? 'N/A' }} | TSC: {{ $case->member->tsc_number ?? 'N/A' }}</p>
                <p class="text-slate-500 text-[11px] truncate">School: {{ $case->member->school ?? 'N/A' }} | Ph: {{ $case->member->phone ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Category & Amount</span>
                <strong class="text-slate-900 font-bold">{{ $case->category->name ?? 'N/A' }}</strong>
                <p class="font-mono font-bold text-emerald-600">KES {{ number_format($case->category->amount ?? 0, 2) }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Deadline & Status</span>
                <div class="flex items-center space-x-1.5 mt-0.5">
                    <span class="font-mono font-bold text-slate-800">{{ $case->deadline }}</span>
                    <button type="button" wire:click="openDeadlineModal" class="text-[#2EA3F2] font-bold hover:underline cursor-pointer text-[10px]">[Change]</button>
                </div>
                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider 
                    @if($case->status === 'active') bg-emerald-100 text-emerald-800 
                    @elseif($case->status === 'suspended') bg-amber-100 text-amber-800 
                    @else bg-slate-200 text-slate-700 @endif">
                    {{ ucfirst($case->status) }}
                </span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Created By</span>
                <strong class="text-slate-800">{{ $case->creator->first_name ?? 'Admin' }} {{ $case->creator->last_name ?? '' }}</strong>
                <p class="text-[11px] text-slate-500 truncate mt-1" title="{{ $case->case_details }}">Desc: {{ $case->case_details }}</p>
            </div>
        </div>
    </div>

    <!-- ================= CONTRIBUTION STATISTICS & ANALYTICS ================= -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-slate-400 uppercase font-bold text-[9px]">Total Collected</span>
            <h4 class="text-lg sm:text-xl font-extrabold font-mono text-emerald-600">KES {{ number_format($totalAmountCollected, 2) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-slate-400 uppercase font-bold text-[9px]">Contributors</span>
            <h4 class="text-lg sm:text-xl font-extrabold font-mono text-slate-900">{{ $totalContributorsCount }} / {{ $totalSystemMembers }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs col-span-2 flex flex-col justify-between">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 uppercase font-bold text-[9px]">Participation Rate</span>
                <span class="font-mono font-bold text-blue-600 text-xs">{{ $contributionPercentage }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, $contributionPercentage) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Demographics Breakdown -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-2">
            <h4 class="font-bold uppercase tracking-wider text-slate-800 text-[11px]">Contributors by Gender</h4>
            <div class="space-y-1.5">
                @foreach(['male' => 'Male', 'female' => 'Female'] as $key => $label)
                    <div class="flex justify-between items-center bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 text-[11px]">
                        <span class="font-medium text-slate-700 uppercase">{{ $label }}</span>
                        <span class="font-mono font-bold text-slate-900">{{ $contributorsByGender[$key] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-2">
            <h4 class="font-bold uppercase tracking-wider text-slate-800 text-[11px]">Contributors by School Level</h4>
            <div class="space-y-1.5">
                @foreach(['Primary' => 'Primary', 'Junior' => 'Junior', 'Senior' => 'Senior'] as $key => $label)
                    <div class="flex justify-between items-center bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 text-[11px]">
                        <span class="font-medium text-slate-700">{{ $label }}</span>
                        <span class="font-mono font-bold text-slate-900">{{ $contributorsBySchoolLevel[$key] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ================= FINANCIAL CONTRIBUTIONS TABLE ================= -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">Financial Contributions List</h3>
                <p class="text-[11px] text-slate-500 font-mono">{{ $contributions->count() }} records logged</p>
            </div>
            
            <button type="button" wire:click="openCaptureModal"
                    class="px-4 py-2.5 bg-gradient-to-r from-sky-500 to-[#2EA3F2] hover:from-sky-600 hover:to-sky-500 text-white text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-md transition flex items-center space-x-1.5 cursor-pointer">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Capture New Payment Record</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">M/NO</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Date Paid</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($contributions as $index => $contribution)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-[#2EA3F2]">
                                {{ $contribution->user->membership_number ?? $contribution->user->tsc_number ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ trim(($contribution->user->salutation ?? '') . ' ' . ($contribution->user->first_name ?? '') . ' ' . ($contribution->user->last_name ?? '')) }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-600">
                                {{ number_format($contribution->amount ?? 0) }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">
                                {{ $contribution->created_at ? $contribution->created_at->format('d-M-y') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                No financial contributions have been recorded for this case yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= CAPTURE PAYMENT MODAL ================= -->
    @if($showCaptureModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-sm uppercase tracking-wider text-slate-900">Capture Benevolence Payment</h3>
                        <p class="text-[11px] text-slate-500">Record a manual contribution for case: {{ $case->case_number }}</p>
                    </div>
                    <button type="button" wire:click="closeCaptureModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer text-sm p-1">✕</button>
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
                            
                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Amount (KSH) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="amount" step="1" 
                                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg font-mono font-bold text-slate-900 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                                @error('amount') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Paid On Date <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="paid_at" 
                                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg font-mono font-bold text-slate-900 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                                @error('paid_at') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Transaction / Receipt Number <span class="text-slate-400 font-normal">(Optional)</span></label>
                                <input type="text" wire:model="referenceNumber" placeholder="e.g. UJ6RD9K067" 
                                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg font-mono font-bold uppercase text-slate-900 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Remarks / Notes <span class="text-slate-400 font-normal">(Optional)</span></label>
                                <input type="text" wire:model="notes" placeholder="e.g. Paid cash to treasurer" 
                                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-slate-800 bg-white focus:ring-2 focus:ring-[#2EA3F2]">
                            </div>

                            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                                <button type="button" wire:click="closeCaptureModal" class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                                <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold uppercase tracking-wider transition shadow-xs bg-[#0E3A59] hover:bg-slate-900 cursor-pointer">
                                    <span wire:loading.remove wire:target="savePayment">Save Payment</span>
                                    <span wire:loading wire:target="savePayment">Saving...</span>
                                </button>
                            </div>

                        </form>
                    @else
                        <div class="py-6 text-center text-slate-400 text-xs border-t border-slate-200">
                            Please search and select a member above to record this contribution.
                        </div>
                    @endif

                </div>

            </div>
        </div>
    @endif

    <!-- ================= STATUS MODAL ================= -->
    @if($showStatusModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 p-6 max-w-sm w-full space-y-4 text-xs">
                <h4 class="font-bold text-sm uppercase tracking-wider text-slate-900">Confirm Status Change</h4>
                <p class="text-slate-600">
                    Are you sure you want to change this case status to <span class="font-bold uppercase text-[#0E3A59]">{{ $selectedStatus }}</span>?
                </p>
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
                    @error('newDeadline') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" wire:click="$set('showDeadlineModal', false)" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    <button type="button" wire:click="updateDeadline" class="px-4 py-2 rounded-lg bg-[#2EA3F2] text-white font-bold uppercase tracking-wider hover:bg-sky-500 transition cursor-pointer">Save Deadline</button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:navigated', () => { if (window.lucide) lucide.createIcons(); });
    document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });

    function confirmSuspend() {
        Swal.fire({
            title: 'CRITICAL ACTION REQUIRED!',
            html: '<p class="text-xs text-slate-600 mt-2">Suspending this benevolence case will automatically reverse and refund all solidarity fund deductions back to members\' balances.</p>' +
                  '<p class="text-xs font-bold text-red-600 mt-3">Type <span class="font-mono bg-slate-100 px-1 py-0.5 rounded border border-red-200">SUSPEND</span> below to confirm:</p>',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                placeholder: 'Type SUSPEND here...'
            },
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Suspend & Refund',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            customClass: {
                popup: 'rounded-xl text-xs',
                confirmButton: 'px-4 py-2 rounded-lg font-bold text-xs uppercase',
                cancelButton: 'px-4 py-2 rounded-lg font-bold text-xs uppercase'
            },
            preConfirm: (inputValue) => {
                if (inputValue !== 'SUSPEND') {
                    Swal.showValidationMessage('You must type exactly "SUSPEND" to proceed.');
                }
                return inputValue === 'SUSPEND';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('openStatusModal', 'suspended').then(() => {
                    @this.call('updateStatus');
                });
            }
        });
    }
</script>