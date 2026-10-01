<div class="space-y-6">

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Page Header & Search Bar -->
    <div class="bg-white px-5 py-4 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-sm font-bold uppercase tracking-wider text-slate-800">Membership Requests</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Review and manage incoming membership applications from teachers</p>
        </div>

        <div class="w-full sm:w-auto flex items-center gap-3">
            <div class="w-full sm:w-72">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search by name, phone, TSC, email..." 
                    class="w-full px-3.5 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900"
                >
            </div>
        </div>
    </div>

    <!-- Requests Table Section -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-[11px] whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-2.5 px-3">#</th>
                        <th class="py-2.5 px-3">TSC Number</th>
                        <th class="py-2.5 px-3">Applicant Name</th>
                        <th class="py-2.5 px-3">Phone</th>
                        <th class="py-2.5 px-3">Email</th>
                        <th class="py-2.5 px-3">Date Applied</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $index => $req)
                        <tr class="{{ $loop->even ? 'bg-slate-50/60' : 'bg-white' }} hover:bg-slate-100/60 transition">
                            <td class="py-2.5 px-3 text-slate-500 font-mono">
                                {{ $requests->firstItem() + $index }}
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-800">
                                {{ $req->user->tsc_number ?? 'N/A' }}
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-slate-800">
                                {{ trim(($req->user->first_name ?? '') . ' ' . ($req->user->last_name ?? '')) ?: ($req->user->name ?? 'N/A') }}
                            </td>
                            <td class="py-2.5 px-3 font-mono text-slate-600">
                                {{ $req->user->phone ?? $req->user->phone_number ?? 'N/A' }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                {{ $req->user->email ?? 'N/A' }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-500 font-mono">
                                {{ $req->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider 
                                    @if($req->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200 
                                    @elseif($req->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200 
                                    @else bg-red-50 text-red-700 border border-red-200 @endif">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right space-x-2">
                                <button 
                                    type="button" 
                                    wire:click="viewDetails({{ $req->id }})" 
                                    class="text-sky-600 hover:text-sky-900 font-semibold">
                                    View
                                </button>
                                @if($req->status === 'pending')
                                    <button 
                                        type="button" 
                                        wire:click="approveRequest({{ $req->id }})" 
                                        wire:confirm="Approve this membership request?"
                                        class="text-emerald-600 hover:text-emerald-900 font-semibold">
                                        Approve
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="rejectRequest({{ $req->id }})" 
                                        wire:confirm="Reject this membership request?"
                                        class="text-red-600 hover:text-red-900 font-semibold">
                                        Reject
                                    </button>
                                @else
                                    <span class="text-slate-400 italic">Actioned</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <p class="text-xs font-semibold text-slate-700">No membership requests found</p>
                                    <p class="text-[11px] text-slate-400">No pending requests match "{{ $search }}".</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-5 py-3 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div>
                Showing <span class="font-semibold text-slate-700">{{ $requests->firstItem() ?? 0 }}</span> to <span class="font-semibold text-slate-700">{{ $requests->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-700">{{ $requests->total() }}</span> requests
            </div>
            <div class="w-full sm:w-auto flex justify-center sm:justify-end">
                {{ $requests->onEachSide(1)->links() }}
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    @if($showModal && $selectedRequest)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden">
                <!-- Modal Header -->
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Applicant Membership Details</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Full Name</span>
                            <span class="font-bold text-slate-800">{{ trim(($selectedRequest->user->first_name ?? '') . ' ' . ($selectedRequest->user->last_name ?? '')) ?: ($selectedRequest->user->name ?? 'N/A') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">TSC Number</span>
                            <span class="font-mono font-bold text-slate-800">{{ $selectedRequest->user->tsc_number ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Phone Number</span>
                            <span class="font-mono text-slate-700">{{ $selectedRequest->user->phone ?? $selectedRequest->user->phone_number ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Email Address</span>
                            <span class="text-slate-700">{{ $selectedRequest->user->email ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">School / Station</span>
                            <span class="text-slate-700">{{ $selectedRequest->user->school ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Date Applied</span>
                            <span class="font-mono text-slate-700">{{ $selectedRequest->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Current Status</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider inline-block mt-1
                                @if($selectedRequest->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200 
                                @elseif($selectedRequest->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200 
                                @else bg-red-50 text-red-700 border border-red-200 @endif">
                                {{ ucfirst($selectedRequest->status) }}
                            </span>
                        </div>
                        @if($selectedRequest->approver)
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Actioned By</span>
                                <span class="text-slate-700 font-semibold">{{ $selectedRequest->approver->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer / Actions -->
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                    <button wire:click="closeModal" class="px-3.5 py-1.5 bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-300 transition">
                        Close
                    </button>

                    <div class="space-x-2">
                        @if($selectedRequest->status === 'pending')
                            <button 
                                wire:click="approveRequest({{ $selectedRequest->id }})" 
                                wire:confirm="Approve this membership request?"
                                class="px-3.5 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition">
                                Approve Request
                            </button>
                            <button 
                                wire:click="rejectRequest({{ $selectedRequest->id }})" 
                                wire:confirm="Reject this membership request?"
                                class="px-3.5 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition">
                                Reject Request
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>