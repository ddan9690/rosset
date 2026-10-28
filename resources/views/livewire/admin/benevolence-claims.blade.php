<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs gap-4">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Member Benevolence Claims</h3>
            <p class="text-xs text-slate-500 mt-0.5">Review and manage assistance claims submitted by members.</p>
        </div>

        <!-- Search and Filter Bar -->
        <div class="flex items-center space-x-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search member or description..."
                    class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-1 focus:ring-slate-900">
            </div>

            <div class="relative">
                <select wire:model.live="statusFilter"
                    class="py-2 px-3 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-1 focus:ring-slate-900 cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Claims Table Section -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-6">Date</th>
                        <th class="py-3 px-6">Member</th>
                        <th class="py-3 px-6">Description</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($claims as $claim)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-6 font-mono text-slate-500 whitespace-nowrap">
                                {{ $claim->created_at->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a') }}
                            </td>
                            <td class="py-3.5 px-6 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block">
                                    {{ trim(($claim->user->salutation ?? '') . ' ' . ($claim->user->first_name ?? '') . ' ' . ($claim->user->last_name ?? '')) }}
                                </span>
                                <span class="text-[11px] font-mono text-slate-500">M/NO: {{ $claim->user->membership_number ?? $claim->user->tsc_number ?? 'N/A' }}</span>
                            </td>
                            <td class="py-3.5 px-6 max-w-xs truncate">
                                {{ $claim->description }}
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                @if($claim->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Approved
                                    </span>
                                @elseif($claim->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap space-x-2">
                                <button type="button" wire:click="viewClaim({{ $claim->id }})" class="font-bold text-[#2EA3F2] hover:underline cursor-pointer">View</button>
                                @if($claim->status !== 'approved')
                                    <button type="button" wire:click="updateStatus({{ $claim->id }}, 'approved')" class="font-bold text-emerald-600 hover:underline cursor-pointer">Approve</button>
                                @endif
                                @if($claim->status !== 'rejected')
                                    <button type="button" wire:click="updateStatus({{ $claim->id }}, 'rejected')" class="font-bold text-red-600 hover:underline cursor-pointer">Reject</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">No benevolence claims found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ $claims->links() }}
        </div>
    </div>

    <!-- ================= VIEW CLAIM MODAL ================= -->
    @if($showViewModal && $selectedClaim)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-5 animate-in fade-in zoom-in duration-200">
                
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">Claim Details</h3>
                    <button wire:click="closeViewModal" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Member Name</span>
                            <strong class="text-slate-900 text-sm font-bold">
                                {{ trim(($selectedClaim->user->salutation ?? '') . ' ' . ($selectedClaim->user->first_name ?? '') . ' ' . ($selectedClaim->user->last_name ?? '')) }}
                            </strong>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Membership / TSC No</span>
                            <span class="font-mono text-slate-800 font-bold">{{ $selectedClaim->user->membership_number ?? $selectedClaim->user->tsc_number ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Date Submitted</span>
                            <span class="font-mono text-slate-600">{{ $selectedClaim->created_at->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Current Status</span>
                            <span class="uppercase font-bold 
                                @if($selectedClaim->status === 'approved') text-emerald-600 
                                @elseif($selectedClaim->status === 'rejected') text-red-600 
                                @else text-amber-600 @endif">
                                {{ $selectedClaim->status }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Claim Description / Reason</span>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-slate-700 leading-relaxed min-h-[100px]">
                            {{ $selectedClaim->description }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                    <div class="space-x-2">
                        @if($selectedClaim->status !== 'approved')
                            <button type="button" wire:click="updateStatus({{ $selectedClaim->id }}, 'approved')" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-xs uppercase cursor-pointer">Approve</button>
                        @endif
                        @if($selectedClaim->status !== 'rejected')
                            <button type="button" wire:click="updateStatus({{ $selectedClaim->id }}, 'rejected')" class="px-3.5 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg font-bold text-xs uppercase cursor-pointer">Reject</button>
                        @endif
                    </div>
                    <button type="button" wire:click="closeViewModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-bold text-xs cursor-pointer">Close</button>
                </div>

            </div>
        </div>
    @endif

</div>

<script>
    document.addEventListener('livewire:navigated', () => { if (window.lucide) lucide.createIcons(); });
    document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });
</script>