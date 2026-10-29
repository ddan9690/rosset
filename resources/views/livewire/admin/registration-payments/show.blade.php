<div class="space-y-6">

    <!-- Header & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-xl shadow-2xs border border-slate-200">
        <div>
            <span class="text-[10px] font-bold font-mono uppercase tracking-wider text-[#2EA3F2] bg-sky-50 px-2.5 py-0.5 rounded border border-sky-100">
                Registration Statement (Year: {{ $year }})
            </span>
            <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wider pt-1">
                {{ $year }} Registration Fee Collections
            </h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="openCreateModal"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-[#2EA3F2] hover:bg-sky-500 text-white transition inline-flex items-center space-x-1.5 cursor-pointer shadow-2xs">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Capture Registration Fee</span>
            </button>
            <a href="{{ route('admin.registration.index') }}" wire:navigate 
               class="px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 transition inline-flex items-center space-x-1">
                <span>&larr; Back</span>
            </a>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center space-x-2 shadow-2xs">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- STATISTICS CARDS -->
    <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-1 col-span-3 sm:col-span-1 lg:col-span-2">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Paid Members</p>
            <h3 class="text-xl font-extrabold text-slate-900 font-mono">
                {{ number_format($totalMembersPaid) }} Paid
            </h3>
            <p class="text-[11px] text-slate-500 font-bold pt-0.5 font-mono">
                Total Collections: KSH {{ number_format($totalCollections) }}
            </p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-1 col-span-3 sm:col-span-1 lg:col-span-1">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Gender</p>
            <div class="space-y-0.5 text-xs pt-1 font-mono">
                <div class="text-blue-600 font-bold">M: {{ $maleCount }}</div>
                <div class="text-pink-600 font-bold">F: {{ $femaleCount }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-1 col-span-3 sm:col-span-1 lg:col-span-3">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">School Level Breakdown</p>
            <div class="grid grid-cols-3 gap-1 pt-1 text-center">
                <div class="bg-slate-50 p-1 rounded border border-slate-100">
                    <span class="text-[9px] text-slate-500 block">Pri</span>
                    <strong class="text-xs font-extrabold text-slate-900 font-mono">{{ $levelBreakdown['Primary'] ?? 0 }}</strong>
                </div>
                <div class="bg-slate-50 p-1 rounded border border-slate-100">
                    <span class="text-[9px] text-slate-500 block">Jun</span>
                    <strong class="text-xs font-extrabold text-slate-900 font-mono">{{ $levelBreakdown['Junior'] ?? 0 }}</strong>
                </div>
                <div class="bg-slate-50 p-1 rounded border border-slate-100">
                    <span class="text-[9px] text-slate-500 block">Sen</span>
                    <strong class="text-xs font-extrabold text-slate-900 font-mono">{{ $levelBreakdown['Senior'] ?? 0 }}</strong>
                </div>
            </div>
        </div>

    </div>

    <!-- DATA TABLE SECTION -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs space-y-4 p-4">
        <div class="flex items-center justify-between">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, membership no, TSC..."
                   class="px-3.5 py-2 border border-slate-300 rounded-lg text-xs w-72 focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Member Details</th>
                        <th class="p-3">Reference No</th>
                        <th class="p-3">Amount</th>
                        <th class="p-3">Payment Date</th>
                        <th class="p-3">Channel</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3">
                                <span class="font-bold text-slate-900 block">{{ $payment->user->first_name ?? 'N/A' }} {{ $payment->user->last_name ?? '' }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">No: {{ $payment->user->membership_number ?? 'N/A' }} | TSC: {{ $payment->user->tsc_number ?? 'N/A' }}</span>
                            </td>
                            <td class="p-3 font-mono text-slate-600 font-bold uppercase">{{ $payment->reference_number }}</td>
                            <td class="p-3 font-mono font-bold text-emerald-600">KES {{ number_format($payment->amount) }}</td>
                            <td class="p-3 font-mono text-slate-500">{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : 'N/A' }}</td>
                            <td class="p-3"><span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-bold">{{ $payment->payment_channel }}</span></td>
                            <td class="p-3 text-right">
                                <button type="button" wire:click="deletePayment({{ $payment->id }})"
                                        wire:confirm="Are you sure you want to delete this payment record?"
                                        class="text-red-600 hover:text-red-800 font-bold text-[11px]">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">No registration payments found for this criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $payments->links() }}
        </div>
    </div>

</div>