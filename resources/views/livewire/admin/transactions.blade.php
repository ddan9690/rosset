<div class="space-y-4">
    <!-- Header & Page Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-3.5 rounded-xl shadow-xs border border-slate-200">
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Transactions</h2>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-lg transition-all">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Total Transacted Summary Card -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-4 rounded-xl shadow-sm text-white flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-100">
                @if($startDate || $endDate || $search)
                    Filtered Total Transacted 
                @else
                    Total Transacted 
                @endif
            </p>
            <h3 class="text-xl sm:text-2xl font-extrabold font-mono mt-0.5">KSH {{ number_format($totalTransactedAmount, 2) }}</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
            <i data-lucide="wallet" class="w-5 h-5 text-white"></i>
        </div>
    </div>

    <!-- Compact Filters and Search Bar -->
    <div class="bg-white p-3 rounded-xl shadow-xs border border-slate-200 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
        <!-- Search Input -->
        <div class="sm:col-span-2">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Search</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Enter reference, phone..." class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-[#2EA3F2] font-mono">
            </div>
        </div>

        <!-- Start Date -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">From Date</label>
            <input type="date" wire:model.live="startDate" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-[#2EA3F2] font-mono">
        </div>

        <!-- End Date & Reset -->
        <div class="flex gap-2">
            <div class="flex-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">To Date</label>
                <input type="date" wire:model.live="endDate" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-[#2EA3F2] font-mono">
            </div>
            @if($search || $startDate || $endDate)
                <div class="flex items-end">
                    <button wire:click="clearFilters" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-all flex items-center justify-center" title="Reset Filters">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Toolbar (Download PDF Button Above Table) -->
    <div class="flex justify-end">
        <a href="{{ route('admin.pdf.transactions.download', ['search' => $search, 'startDate' => $startDate, 'endDate' => $endDate]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-xs">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            Download PDF Report
        </a>
    </div>

    <!-- High-Density Transactions Table -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-[11px] whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[9px]">
                        <th class="py-2.5 px-3">Reference</th>
                        <th class="py-2.5 px-3">Amount</th>
                        <th class="py-2.5 px-3">Phone</th>
                        <th class="py-2.5 px-3">Type</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3">Date / Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2 px-3">
                                <span class="font-mono font-bold text-slate-900">{{ $tx->reference_number ?? '—' }}</span>
                            </td>
                            <td class="py-2 px-3 font-mono font-bold text-emerald-600">
                                {{ number_format($tx->amount) }}
                            </td>
                            <td class="py-2 px-3">
                                <span class="font-mono text-slate-800">{{ $tx->phone_number }}</span>
                            </td>
                            <td class="py-2 px-3 uppercase text-[10px] font-semibold text-slate-600">
                                {{ str_replace('_', ' ', $tx->type) }}
                            </td>
                            <td class="py-2 px-3">
                                @php
                                    $statusColors = [
                                        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'failed' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                    $badgeClass = $statusColors[$tx->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                    {{ ucfirst($tx->status) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 font-mono text-slate-500 text-[10px]">
                                {{ $tx->created_at?->format('d-m-y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No transactions found matching your criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Compact Pagination Footer -->
        <div class="px-3 py-2.5 border-t border-slate-200 bg-slate-50/50 flex items-center justify-end text-xs">
            <div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        if (window.lucide) { window.lucide.createIcons(); }
    });
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) { window.lucide.createIcons(); }
    });
</script>