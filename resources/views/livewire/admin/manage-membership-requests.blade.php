<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Membership Requests</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review teacher membership applications, assign membership numbers, and activate accounts.</p>
        </div>
        
        <!-- Search Bar -->
        <div class="w-full sm:w-72">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search by name, TSC, phone..." 
                class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
            >
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" wire:click="$refresh" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    <!-- Requests Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold tracking-wider border-b border-slate-200 text-[10px]">
                    <tr>
                        <th class="py-3 px-3">#</th>
                        <th class="py-3 px-3">Requested Date</th>
                        <th class="py-3 px-3">Applicant</th>
                        <th class="py-3 px-3 text-center">Profile Picture</th>
                        <th class="py-3 px-3">Phone</th>
                        <th class="py-3 px-3">TSC No.</th>
                        <th class="py-3 px-3">Gender</th>
                        <th class="py-3 px-3">School Level</th>
                        <th class="py-3 px-3">School</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($requests as $index => $req)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-3 font-mono text-slate-400">{{ $requests->firstItem() + $index }}</td>
                            <td class="py-3 px-3 text-slate-500 font-mono uppercase">{{ $req->created_at->format('d-M-y') }}</td>
                            <td class="py-3 px-3 font-medium text-slate-900">
                                {{ trim(($req->user->first_name ?? '') . ' ' . ($req->user->last_name ?? '')) }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($req->user && $req->user->profile_picture)
                                    <button type="button" wire:click="viewImage('{{ asset('storage/' . $req->user->profile_picture) }}')" class="inline-block focus:outline-none group">
                                        <img src="{{ asset('storage/' . $req->user->profile_picture) }}" alt="Profile" class="w-9 h-9 object-cover rounded-lg border border-slate-200 shadow-2xs group-hover:scale-105 transition">
                                    </button>
                                @else
                                    <span class="inline-flex items-center justify-center w-9 h-9 bg-slate-100 text-slate-400 rounded-lg text-[10px] font-bold">N/A</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 font-mono text-slate-600">{{ $req->user->phone ?? 'N/A' }}</td>
                            <td class="py-3 px-3 font-mono text-slate-600">{{ $req->user->tsc_number ?? 'N/A' }}</td>
                            <td class="py-3 px-3 font-mono uppercase text-[11px] text-slate-600 font-bold">
                                {{ strtoupper($req->user->gender ?? 'N/A') }}
                            </td>
                            <td class="py-3 px-3 text-slate-600">{{ $req->user->school_level ?? 'N/A' }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $req->user->school ?? 'N/A' }}</td>
                            <td class="py-3 px-3">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                @elseif($req->status === 'approved')
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>
                                        <span class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $req->updated_at->setTimezone('Africa/Nairobi')->format('d-M-y') }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right space-x-1.5">
                                @if($req->status === 'pending')
                                    <button wire:click="openApproveModal({{ $req->id }})" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded text-[11px] transition cursor-pointer">Approve</button>
                                    <button wire:click="rejectRequest({{ $req->id }})" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded text-[11px] transition cursor-pointer">Reject</button>
                                @else
                                    <span class="text-[11px] text-slate-400 font-medium italic">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">
                                No active membership requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- ================= APPROVE & ASSIGN MEMBERSHIP NUMBER MODAL ================= -->
    @if($showApproveModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Assign Membership Number</h3>
                    <button type="button" wire:click="closeApproveModal" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none">&times;</button>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-slate-600">
                        You are approving membership for <strong>{{ $applicantName }}</strong>. Please verify or modify the auto-suggested membership number below:
                    </p>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Membership Number</label>
                        <input type="text" wire:model="membership_number" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none" autofocus>
                        @error('membership_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" wire:click="closeApproveModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">Cancel</button>
                    <button type="button" wire:click="processApproval(false)" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">Confirm & Approve</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Full-Size Image Preview Lightbox Modal -->
    @if($selectedImage)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4" wire:click.self="closeImageModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-4 relative space-y-3" @click.stop>
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Profile Picture Preview</h3>
                    <button type="button" wire:click="closeImageModal" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none">&times;</button>
                </div>
                <div class="flex justify-center bg-slate-900/5 rounded-xl p-2">
                    <img src="{{ $selectedImage }}" alt="Enlarged Profile" class="max-h-[70vh] w-auto object-contain rounded-lg shadow-sm">
                </div>
                <div class="flex justify-end pt-1">
                    <button type="button" wire:click="closeImageModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>

@assets
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endassets

@script
<script>
    window.addEventListener('swal:duplicate-membership', event => {
        Swal.fire({
            title: 'Membership Number Exists',
            text: `Membership number "${event.detail.number}" already exists and belongs to ${event.detail.name}. Do you want to assign this number anyway?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, assign anyway',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('processApproval', true);
            }
        });
    });
</script>
@endscript