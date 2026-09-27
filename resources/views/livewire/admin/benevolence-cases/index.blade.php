<div class="space-y-6">

    <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Benevolence Cases</h3>
            <p class="text-xs text-slate-500 mt-0.5">Manage member benevolence claims and fundraising cases.</p>
        </div>
        <a href="{{ route('admin.benevolence.cases.create') }}" wire:navigate class="px-4 py-2.5 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer flex items-center space-x-1">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Create Case</span>
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Case No, Member Name, TSC..." class="px-3.5 py-2 border border-slate-300 rounded-lg text-xs w-72 focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-6">Case No</th>
                        <th class="py-3 px-6">Member</th>
                        <th class="py-3 px-6">Category</th>
                        <th class="py-3 px-6">Deadline</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($cases as $case)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900">{{ $case->case_number }}</td>
                            <td class="py-3.5 px-6 font-semibold text-slate-800">{{ $case->member->first_name ?? '' }} {{ $case->member->last_name ?? '' }}</td>
                            <td class="py-3.5 px-6">{{ $case->category->name ?? 'N/A' }}</td>
                            <td class="py-3.5 px-6 font-mono text-slate-500">{{ $case->deadline }}</td>
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider 
                                    @if($case->status === 'active') bg-emerald-100 text-emerald-800 
                                    @elseif($case->status === 'suspended') bg-amber-100 text-amber-800 
                                    @else bg-slate-200 text-slate-700 @endif">
                                    {{ ucfirst($case->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right space-x-3">
                                <a href="{{ route('admin.benevolence.cases.show', [$case->id, $case->slug]) }}" wire:navigate class="font-bold text-slate-600 hover:underline">View</a>
                                <a href="{{ route('admin.benevolence.cases.edit', [$case->id, $case->slug]) }}" wire:navigate class="font-bold text-[#2EA3F2] hover:underline">Edit</a>
                                <button type="button" onclick="confirmDelete({{ $case->id }})" class="font-bold text-red-600 hover:underline cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No benevolence cases found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ $cases->links() }}
        </div>
    </div>
</div>

<script>
    function confirmDelete(caseId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete this benevolence case and all its linked records.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deleteCase', caseId);
            }
        });
    }
</script>