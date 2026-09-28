<div class="space-y-6">

    <!-- Page Header & Search Bar -->
    <div class="bg-white px-5 py-4 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-sm font-bold uppercase tracking-wider text-slate-800">Members Directory</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Comprehensive directory and status of all registered members</p>
        </div>

        <div class="w-full sm:w-72">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search name, phone, TSC, member no..." 
                class="w-full px-3.5 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900"
            >
        </div>
    </div>

    <!-- Members Table Section -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-[11px] whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-2.5 px-3">#</th>
                        <th class="py-2.5 px-3">Mem No</th>
                        <th class="py-2.5 px-3">Name</th>
                        <th class="py-2.5 px-3">Gender</th>
                        <th class="py-2.5 px-3">School</th>
                        <th class="py-2.5 px-3">Level</th>
                        <th class="py-2.5 px-3">Phone</th>
                        <th class="py-2.5 px-3">TSC No</th>
                        <th class="py-2.5 px-3">ID No</th>
                        <th class="py-2.5 px-3">Email</th>
                        <th class="py-2.5 px-3">Reg Fee</th>
                        <th class="py-2.5 px-3">Profile</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3">Last Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $user)
                        <tr class="{{ $loop->even ? 'bg-slate-50/60' : 'bg-white' }} hover:bg-slate-100/60 transition">
                            <td class="py-2 px-3 text-slate-500 font-mono">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="py-2 px-3 font-mono font-bold text-slate-800">
                                {{ $user->membership_number ?? 'N/A' }}
                            </td>
                            <td class="py-2 px-3 font-semibold text-slate-800">
                                {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'N/A' }}
                            </td>
                            <td class="py-2 px-3 uppercase text-slate-600 font-medium">
                                @if($user->gender === 'male')
                                    M
                                @elseif($user->gender === 'female')
                                    F
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="py-2 px-3 text-slate-600 max-w-[160px] truncate" title="{{ $user->school }}">
                                {{ $user->school ?? 'N/A' }}
                            </td>
                            <td class="py-2 px-3 text-slate-600">
                                {{ $user->school_level ?? 'N/A' }}
                            </td>
                            <td class="py-2 px-3 font-mono text-slate-600">
                                {{ $user->phone }}
                            </td>
                            <td class="py-2 px-3 font-mono text-slate-600">
                                {{ $user->tsc_number ?? 'N/A' }}
                            </td>
                            <td class="py-2 px-3 font-mono text-slate-600">
                                {{ $user->id_number ?? 'N/A' }}
                            </td>
                            <td class="py-2 px-3 text-slate-600 max-w-[180px] truncate" title="{{ $user->email }}">
                                {{ $user->email ?? 'N/A' }}
                            </td>
                            <td class="py-2 px-3">
                                @if($user->registration_fee_paid)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Yes
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200">
                                        No
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                @if($user->is_profile_complete)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Complete
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        Incomplete
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider 
                                    @if($user->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-200 
                                    @elseif($user->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200 
                                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 font-mono text-[10px] text-slate-500">
                                @if($user->last_active_at)
                                    {{ \Carbon\Carbon::parse($user->last_active_at)->diffForHumans() }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <i data-lucide="search-x" class="w-5 h-5"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-700">No member found</p>
                                    <p class="text-[11px] text-slate-400">No registered member matches "{{ $search }}". Check your search keyword and try again.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Details Footer -->
        <div class="px-5 py-3 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div>
                Showing <span class="font-semibold text-slate-700">{{ $users->firstItem() ?? 0 }}</span> to <span class="font-semibold text-slate-700">{{ $users->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-700">{{ $users->total() }}</span> members
            </div>
            
            <div class="w-full sm:w-auto flex justify-center sm:justify-end">
                {{ $users->onEachSide(1)->links() }}
            </div>
        </div>

    </div>

</div>