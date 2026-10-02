<div class="space-y-8">

    <!-- Header / Switch to Portal Bar -->
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 rounded-xl shadow-xs border border-slate-200">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">
                Dashboard Overview
            </h2>

            <p class="text-xs text-slate-500">
                Monitor live activity, metrics, and member statistics.
            </p>
        </div>

        <div>
            <a href="{{ route('portal') }}" wire:navigate
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#2EA3F2] hover:bg-sky-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm transition-all duration-200 flex-shrink-0 cursor-pointer">

                <i data-lucide="external-link" class="w-4 h-4"></i>

                Switch to Portal
            </a>
        </div>
    </div>


    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-6">

        <!-- Total Members Card -->
        <div class="bg-white p-5 rounded-xl shadow-xs border border-slate-200 space-y-4">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total Members
                    </p>

                    <h3 class="text-2xl sm:text-3xl font-extrabold mt-1 text-slate-900">
                        {{ number_format($totalMembers) }}
                    </h3>
                </div>

                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center bg-blue-50 text-[#2EA3F2] flex-shrink-0">
                    <i data-lucide="users" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>

            </div>

            <div class="border-t border-slate-100 pt-3 grid grid-cols-2 gap-2 text-[11px]">

                <div class="text-slate-600">
                    Male:
                    <strong class="text-slate-800 font-mono">
                        {{ $maleMembers }}
                    </strong>
                </div>

                <div class="text-slate-600">
                    Female:
                    <strong class="text-slate-800 font-mono">
                        {{ $femaleMembers }}
                    </strong>
                </div>

                <div class="text-slate-600">
                    Junior:
                    <strong class="text-slate-800 font-mono">
                        {{ $juniorSchoolMembers }}
                    </strong>
                </div>

                <div class="text-slate-600">
                    Senior:
                    <strong class="text-slate-800 font-mono">
                        {{ $seniorSchoolMembers }}
                    </strong>
                </div>

            </div>

        </div>


        <!-- Membership Requests Card -->
        <div class="bg-white p-5 rounded-xl shadow-xs border border-slate-200 flex flex-col justify-between space-y-4">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">
                        Membership Requests
                    </p>

                    <h3 class="text-2xl sm:text-3xl font-extrabold mt-1 text-amber-600">
                        {{ number_format($pendingRequestsCount) }}
                    </h3>
                </div>

                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 flex-shrink-0">
                    <i data-lucide="user-plus" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>

            </div>

            <div class="border-t border-slate-100 pt-2">

                <a href="{{ route('admin.membership-requests') }}" wire:navigate
                    class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-3 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded-lg text-xs font-bold transition">

                    Review Requests

                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>

                </a>

            </div>

        </div>


        <!-- Benevolence Cases Count -->
        <div class="bg-white p-5 rounded-xl shadow-xs border border-slate-200 flex items-center justify-between">

            <div>

                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">
                    Benevolence Cases
                </p>

                <h3 class="text-2xl sm:text-3xl font-extrabold mt-1 text-indigo-600">
                    {{ number_format($benevolenceCasesCount) }}
                </h3>

                <p class="text-[10px] text-slate-500 mt-1">
                    Total recorded cases
                </p>

            </div>

            <div
                class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center bg-indigo-50 text-indigo-600 flex-shrink-0">
                <i data-lucide="shield-alert" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>

        </div>


        <!-- Solidarity Fund Overall Balance -->
        <div class="bg-white p-5 rounded-xl shadow-xs border border-slate-200 flex items-center justify-between">

            <div>

                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">
                    Solidarity Fund Balance
                </p>

                <h3 class="text-xl sm:text-2xl font-extrabold mt-1 text-emerald-600 font-mono">
                    Ksh {{ number_format($totalSolidarityBalance, 0) }}
                </h3>

            </div>

            <div
                class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 flex-shrink-0">
                <i data-lucide="wallet" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>

        </div>


        <!-- Defaulters, Suspended & Deregistered -->
        <div class="bg-white p-5 rounded-xl shadow-xs border border-slate-200 space-y-3">

            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">
                Account Exceptions
            </p>

            <div class="grid grid-cols-3 gap-1 text-center">

                <div class="bg-red-50 p-2 rounded-lg border border-red-100">

                    <span class="block text-[9px] font-bold text-red-600 uppercase">
                        Defaulters
                    </span>

                    <span class="text-sm font-extrabold text-red-700 font-mono">
                        {{ $defaultersCount }}
                    </span>

                </div>

                <div class="bg-amber-50 p-2 rounded-lg border border-amber-100">

                    <span class="block text-[9px] font-bold text-amber-600 uppercase">
                        Suspended
                    </span>

                    <span class="text-sm font-extrabold text-amber-700 font-mono">
                        {{ $suspendedCount }}
                    </span>

                </div>

                <div class="bg-slate-100 p-2 rounded-lg border border-slate-200">

                    <span class="block text-[9px] font-bold text-slate-600 uppercase">
                        Deregistered
                    </span>

                    <span class="text-sm font-extrabold text-slate-700 font-mono">
                        {{ $deregisteredCount }}
                    </span>

                </div>

            </div>

        </div>

    </div>


    <!-- Benevolence Cases Overview Table -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">

            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">
                Active Benevolence Cases Tracking
            </h3>

            <span class="text-xs font-medium text-slate-500">
                Live Status Feed
            </span>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">

                <thead>

                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">

                        <th class="py-3 px-4">
                            #
                        </th>

                        <th class="py-3 px-4">
                            Case Number
                        </th>

                        <th class="py-3 px-4">
                            Member / Category
                        </th>

                        <th class="py-3 px-4">
                            Contributors
                        </th>

                        <th class="py-3 px-4">
                            Deadline
                        </th>

                        <th class="py-3 px-4">
                            Total Collected
                        </th>

                        <th class="py-3 px-4">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100 text-slate-700">

                    @forelse($benevolenceCases as $index => $case)
                        <tr class="hover:bg-slate-50 transition">

                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $index + 1 }}
                            </td>

                            <td class="py-3 px-4 font-mono font-bold text-[#2EA3F2]">
                                {{ $case->case_number }}
                            </td>

                            <td class="py-3 px-4">

                                <span class="font-semibold text-slate-900 block">
                                    {{ trim(
                                        ($case->member->salutation ?? '') .
                                            ' ' .
                                            ($case->member->first_name ?? '') .
                                            ' ' .
                                            ($case->member->last_name ?? ''),
                                    ) }}
                                </span>

                                <span class="text-[10px] text-slate-400 uppercase">
                                    {{ $case->category->name ?? 'General' }}
                                </span>

                            </td>

                            <td class="py-3 px-4 font-mono">

                                <strong class="text-slate-900">
                                    {{ $case->contributors_count }}
                                </strong>

                                <span class="text-slate-400">
                                    / {{ $totalActiveMembers }} members
                                </span>

                            </td>

                            <td class="py-3 px-4 font-mono text-slate-600">
                                {{ $case->deadline }}
                            </td>

                            <td class="py-3 px-4 font-mono font-bold text-emerald-600">
                                Ksh {{ number_format($case->total_collected ?? 0, 0) }}
                            </td>

                            <td class="py-3 px-4">

                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ ucfirst($case->status) }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No benevolence cases found in the system.
                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <!-- Recent Gateway Transactions -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between">

            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">
            Transactions
            </h3>

            {{-- <span class="text-[10px] font-medium text-slate-400">
                M-Pesa / STK
            </span> --}}

        </div>


        <!-- Compact Transactions Table -->
        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse text-[11px] whitespace-nowrap">

                <thead>

                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[9px]">

                        <th class="py-2 px-3">
                            Member
                        </th>

                        <th class="py-2 px-3">
                            MEM NO
                        </th>

                        <th class="py-2 px-3">
                            Phone
                        </th>

                        <th class="py-2 px-3">
                            Reference
                        </th>

                        <th class="py-2 px-3">
                            Type
                        </th>

                        <th class="py-2 px-3 text-right">
                            Amount
                        </th>

                        <th class="py-2 px-3">
                            Status
                        </th>

                        <th class="py-2 px-3">
                            Date
                        </th>

                        <th class="py-2 px-3">
                            Time
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100 text-slate-700">

                    @forelse($recentTransactions as $tx)
                        @php
                            $transactionDate = $tx->created_at;
                        @endphp

                        <tr class="hover:bg-slate-50 transition">

                            <!-- Member -->
                            <td class="py-2 px-3">

                                @if ($tx->user)
                                    <span class="font-semibold text-slate-900">
                                        {{ trim(($tx->user->first_name ?? '') . ' ' . ($tx->user->last_name ?? '')) }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">
                                        Unknown Member
                                    </span>
                                @endif

                            </td>


                            <!-- Membership Number -->
                            <td class="py-2 px-3 font-mono font-semibold text-[#2EA3F2]">
                                {{ $tx->user?->membership_number ?? '—' }}
                            </td>


                            <!-- Phone Used for Payment -->
                            <td class="py-2 px-3 font-mono text-slate-600">
                                {{ $tx->phone_number ?: '—' }}
                            </td>


                            <!-- Reference -->
                            <td class="py-2 px-3 font-mono font-semibold text-slate-900">
                                {{ $tx->reference_number ?: '—' }}
                            </td>


                            <!-- Transaction Type -->
                            <td class="py-2 px-3 uppercase text-[9px] font-semibold text-slate-600">
                                {{ str_replace('_', ' ', $tx->type) }}
                            </td>


                            <!-- Amount -->
                            <td class="py-2 px-3 text-right font-mono font-bold text-emerald-600">
                                {{ number_format($tx->amount, 0) }}
                            </td>


                            <!-- Status -->
                            <td class="py-2 px-3">

                                @php
                                    $statusClasses = match (strtolower($tx->status)) {
                                        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'failed' => 'bg-red-50 text-red-700 border-red-200',
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp

                                <span
                                    class="inline-flex px-1.5 py-0.5 rounded border text-[8px] font-bold uppercase tracking-wider {{ $statusClasses }}">
                                    {{ ucfirst($tx->status) }}
                                </span>

                            </td>


                            <!-- Date -->
                            <td class="py-2 px-3 font-mono text-slate-600">
                                {{ $transactionDate?->format('d-M-y') }}
                            </td>


                            <!-- Time -->
                            <td class="py-2 px-3 font-mono text-slate-600">
                                {{ $transactionDate?->format('H:i:s') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9" class="py-6 text-center text-slate-400">
                                No transactions recorded.
                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- View More Transactions -->
        <div class="px-5 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end">

            <a href="{{ route('admin.transactions') }}" wire:navigate
                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#2EA3F2] hover:text-sky-600 transition-all">

                View More Transactions

                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>

            </a>

        </div>

    </div>

</div>


<script>
    document.addEventListener('livewire:navigated', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
