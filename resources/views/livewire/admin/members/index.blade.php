<div class="space-y-6">

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div
            class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" onclick="this.parentElement.remove()"
                class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()"
                class="text-red-600 hover:text-red-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Page Header & Search Bar / Add Action -->
    <div
        class="bg-white px-5 py-4 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-sm font-bold uppercase tracking-wider text-slate-800">Members Directory</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Manage registered members, view details, and track statuses</p>
        </div>

        <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-64">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search name, phone, TSC, email..."
                    class="w-full px-3.5 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
            </div>
            <!-- Manage Roles Button with wire:navigate -->
            <a href="{{ route('admin.roles') }}" wire:navigate
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-1.5 bg-slate-100 text-slate-700 border border-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-200 transition">
                Manage Roles
            </a>

            <!-- Add Member Button with wire:navigate -->
            <a href="{{ route('admin.members.create') }}" wire:navigate
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg hover:bg-slate-800 transition">
                + Add Member
            </a>
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
                        <th class="py-2.5 px-3">Phone</th>
                        <th class="py-2.5 px-3">School</th>
                        <th class="py-2.5 px-3">Reg Fee</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $user)
                        <tr class="{{ $loop->even ? 'bg-slate-50/60' : 'bg-white' }} hover:bg-slate-100/60 transition">
                            <td class="py-2.5 px-3 text-slate-500 font-mono">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-800">
                                {{ $user->membership_number ?? 'N/A' }}
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-slate-800">
                                {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'N/A' }}
                            </td>
                            <td class="py-2.5 px-3 font-mono text-slate-600">
                                {{ $user->phone }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 max-w-[180px] truncate" title="{{ $user->school }}">
                                {{ $user->school ?? 'N/A' }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if ($user->registration_fee_paid)
                                    <span
                                        class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200">Unpaid</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <span
                                    class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider 
                                    @if ($user->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-200 
                                    @elseif($user->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200 
                                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right space-x-2">
                                <a href="{{ route('admin.members.show', $user->id) }}"
                                    class="text-sky-600 hover:text-sky-900 font-semibold">View</a>
                                <a href="{{ route('admin.members.edit', $user->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                <button type="button" wire:click="deleteMember({{ $user->id }})"
                                    wire:confirm="Are you sure you want to delete this member?"
                                    class="text-red-600 hover:text-red-900 font-medium">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <p class="text-xs font-semibold text-slate-700">No member found</p>
                                    <p class="text-[11px] text-slate-400">No registered member matches
                                        "{{ $search }}".</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div
            class="px-5 py-3 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div>
                Showing <span class="font-semibold text-slate-700">{{ $users->firstItem() ?? 0 }}</span> to <span
                    class="font-semibold text-slate-700">{{ $users->lastItem() ?? 0 }}</span> of <span
                    class="font-semibold text-slate-700">{{ $users->total() }}</span> members
            </div>
            <div class="w-full sm:w-auto flex justify-center sm:justify-end">
                {{ $users->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>
