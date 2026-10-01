<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Membership Requests</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review and manage teacher membership applications directly from the table.</p>
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
            <button type="button" wire:click="$set('session.message', null)" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    <!-- Requests Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold tracking-wider border-b border-slate-200 text-[10px]">
                    <tr>
                        <th class="py-3 px-3">#</th>
                        <th class="py-3 px-3">Date</th>
                        <th class="py-3 px-3">Applicant Name</th>
                        <th class="py-3 px-3">Phone</th>
                        <th class="py-3 px-3">TSC No.</th>
                        <th class="py-3 px-3">Gender</th>
                        <th class="py-3 px-3">School Level</th>
                        <th class="py-3 px-3">School</th>
                        <th class="py-3 px-3 text-center">Profile Picture</th>
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
                            <td class="py-3 px-3 font-mono text-slate-600">{{ $req->user->phone ?? 'N/A' }}</td>
                            <td class="py-3 px-3 font-mono text-slate-600">{{ $req->user->tsc_number ?? 'N/A' }}</td>
                            <td class="py-3 px-3 font-mono uppercase text-[11px] text-slate-600 font-bold">
                                @if(strtolower($req->user->gender ?? '') === 'male')
                                    M
                                @elseif(strtolower($req->user->gender ?? '') === 'female')
                                    F
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600">{{ $req->user->school_level ?? 'N/A' }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $req->user->school ?? 'N/A' }}</td>
                            <td class="py-3 px-3 text-center">
                                @if($req->user && $req->user->profile_picture)
                                    <button type="button" wire:click="viewImage('{{ asset('storage/' . $req->user->profile_picture) }}')" class="inline-block focus:outline-none group">
                                        <img src="{{ asset('storage/' . $req->user->profile_picture) }}" alt="Profile" class="w-9 h-9 object-cover rounded-lg border border-slate-200 shadow-2xs group-hover:scale-105 transition">
                                    </button>
                                @else
                                    <span class="inline-flex items-center justify-center w-9 h-9 bg-slate-100 text-slate-400 rounded-lg text-[10px] font-bold">N/A</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Pending
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right space-x-1.5">
                                @if($req->status !== 'approved')
                                    <button wire:click="approveRequest({{ $req->id }})" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded text-[11px] transition">Approve</button>
                                @endif

                                @if(!$req->user || !$req->user->registration_fee_paid)
                                    @if($req->status !== 'rejected')
                                        <button wire:click="rejectRequest({{ $req->id }})" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded text-[11px] transition">Reject</button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">
                                No membership requests found.
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
                    <button type="button" wire:click="closeImageModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>