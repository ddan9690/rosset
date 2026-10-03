<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            <div class="flex items-center space-x-4">
                <a href="/" wire:navigate
                    class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                    <span>&larr; Back to Home</span>
                </a>

                @hasanyrole(['super admin', 'welfare admin'])
                    <span class="text-slate-300">|</span>

                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                        class="px-3.5 py-1.5 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-2xs bg-[#0E3A59] hover:opacity-90 flex items-center space-x-1.5 cursor-pointer inline-flex">
                        <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                        <span>Switch to Dashboard</span>
                    </a>
                @endhasanyrole
            </div>

            <div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-red-600 hover:underline cursor-pointer">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        @if (session()->has('message'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if (!$isProfileComplete)
            <!-- ================= INCOMPLETE PROFILE NOTICE ================= -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs text-center max-w-md mx-auto space-y-6 my-16">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="user-pen" class="w-6 h-6"></i>
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900">
                        Profile Update Required
                    </h3>
                    <p class="text-sm text-slate-700 leading-relaxed font-medium">
                        Dear
                        <strong class="text-slate-900">
                            {{ Auth::user()->first_name ?? 'Member' }}
                        </strong>,
                        please update your profile in order to access your portal.
                    </p>
                </div>

                <div>
                    <a href="{{ route('profile.update') }}" wire:navigate
                        class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow-sm bg-slate-900 hover:bg-slate-800 cursor-pointer flex items-center justify-center space-x-2">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        <span>Update Profile</span>
                    </a>
                </div>
            </div>
        @elseif(!$isRegistrationPaid)
            <!-- ================= UNPAID REGISTRATION FEE NOTICE ================= -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs text-center max-w-md mx-auto space-y-6 my-16">
                <div class="w-12 h-12 bg-blue-50 text-[#2EA3F2] rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="credit-card" class="w-6 h-6"></i>
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900">
                        Registration Fee Pending
                    </h3>
                    <p class="text-sm text-slate-700 leading-relaxed font-medium">
                        Dear
                        <strong class="text-slate-900">
                            {{ Auth::user()->first_name ?? 'Member' }}
                        </strong>,
                        please complete your registration by paying registration fee of
                        <strong class="text-slate-900 font-mono">
                            KSH 150
                        </strong>.
                    </p>
                </div>

                <div>
                    <button wire:click="redirectToPayment" wire:loading.attr="disabled"
                        class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow-sm bg-red-600 hover:bg-red-700 cursor-pointer flex items-center justify-center space-x-2">
                        <span wire:loading.remove wire:target="redirectToPayment">
                            Pay Registration Fee (KSH 150)
                        </span>
                        <span wire:loading wire:target="redirectToPayment">
                            Redirecting...
                        </span>
                    </button>
                </div>
            </div>
        @else
            <!-- ================= FULL MEMBER PORTAL CONTENT ================= -->

            <!-- Member Profile & Solidarity Summary Strip -->
            <div
                class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                            <h1 class="text-base font-bold text-slate-900">
                                {{ $memberName }}
                            </h1>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $memberStatus }}
                            </span>
                        </div>

                        <div class="flex items-center space-x-3 flex-wrap gap-y-1 pt-0.5">
                            <p class="text-xs text-slate-500 font-mono">
                                Membership No:
                                <span class="font-semibold text-slate-700">
                                    {{ $membershipNumber }}
                                </span>
                            </p>

                            <span class="text-slate-300">|</span>

                            <a href="{{ route('member.dependants.update') }}" wire:navigate
                                class="text-xs font-semibold text-[#0E3A59] hover:text-slate-900 hover:underline flex items-center space-x-1">
                                <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                <span>update dependants</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div
                    class="w-full md:w-auto bg-slate-50 border border-slate-200 rounded-lg p-3.5 flex items-center justify-between md:justify-start space-x-6">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                            Solidarity Fund Balance
                        </span>
                        <div class="flex items-center space-x-3 mt-0.5">
                            <span class="text-lg font-extrabold text-slate-900 font-mono">
                                KSH {{ number_format($solidarityBalance) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div
                            class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 shadow-2xs">
                            <i data-lucide="wallet" class="w-4 h-4"></i>
                        </div>

                        <a href="{{ route('member.solidarity') }}" wire:navigate
                            class="px-3.5 py-2 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-2xs hover:opacity-90 bg-[#0E3A59]">
                            View
                        </a>
                    </div>
                </div>
            </div>

            <!-- ================= BENEVOLENCE CASES SECTION ================= -->
            <div class="space-y-4">
                <div class="border-b border-slate-200 pb-3 text-center">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                        ROSSET-SWA BENEVOLENCE CASES
                    </h2>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
                    @forelse($benevolenceCases as $case)
                        <div
                            class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs flex flex-col justify-between transition hover:shadow-md">
                            <div class="bg-slate-900 text-white px-2 sm:px-4 py-2 text-center">
                                <span
                                    class="font-mono text-[10px] sm:text-xs font-bold text-slate-100 tracking-wide uppercase truncate block">
                                    CASE : {{ $case->case_number }}
                                </span>
                            </div>

                            <div class="p-3 sm:p-5 space-y-2 sm:space-y-3 text-[11px] sm:text-xs flex-1">
                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between sm:items-start border-b border-slate-100 pb-2 gap-0.5 sm:gap-2">
                                    <span class="text-slate-500 font-medium">
                                        Affected Member:
                                    </span>
                                    <span class="font-bold text-slate-900 sm:text-right">
                                        {{ trim(
                                            ($case->member->salutation ?? '') .
                                                ' ' .
                                                ($case->member->first_name ?? '') .
                                                ' ' .
                                                ($case->member->last_name ?? ''),
                                        ) }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                    <span class="text-slate-500 font-medium">
                                        Member No:
                                    </span>
                                    <span class="font-mono font-semibold text-slate-800">
                                        {{ $case->member->tsc_number ?? ($case->member->membership_number ?? 'N/A') }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                    <span class="text-slate-500 font-medium">
                                        Category:
                                    </span>
                                    <span
                                        class="inline-flex px-1.5 sm:px-2 py-0.5 rounded text-[9px] sm:text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 uppercase">
                                        {{ $case->category->name ?? 'Benevolence' }}
                                    </span>
                                </div>

                                <div class="space-y-1 border-b border-slate-100 pb-2">
                                    <span class="text-slate-500 font-medium block">
                                        Case Details:
                                    </span>
                                    <p
                                        class="text-slate-700 italic bg-slate-50 p-1.5 sm:p-2 rounded text-[10px] sm:text-[11px] leading-relaxed">
                                        {{ $case->case_details }}
                                    </p>
                                </div>

                                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                    <span class="text-slate-500 font-medium">
                                        Deadline:
                                    </span>
                                    <span class="font-mono font-semibold text-red-600">
                                        {{ $case->deadline ? \Carbon\Carbon::parse($case->deadline)->format('d-m-Y') : 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center pt-0.5">
                                    <span class="text-slate-500 font-medium">
                                        Amount:
                                    </span>
                                    <span class="font-mono font-bold text-slate-900">
                                        KSH {{ number_format($case->category->amount ?? 0) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-2.5 sm:p-4 bg-slate-50 border-t border-slate-100">
                                @if ($case->contribution_made)
                                    <button type="button" disabled
                                        class="w-full py-2 px-2 sm:px-4 rounded-lg text-emerald-700 bg-emerald-50 border border-emerald-200 font-bold text-[10px] sm:text-xs uppercase tracking-wider flex items-center justify-center space-x-1 cursor-not-allowed">
                                        <i data-lucide="check-circle-2"
                                            class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-emerald-600"></i>
                                        <span>Contributed</span>
                                    </button>
                                @else
                                    <button type="button" wire:click="sendContribution({{ $case->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="sendContribution({{ $case->id }})"
                                        class="w-full py-2 px-2 sm:px-4 rounded-lg text-white font-bold text-[10px] sm:text-xs transition shadow-2xs hover:opacity-90 cursor-pointer bg-slate-900 flex items-center justify-center space-x-1 uppercase tracking-wider">
                                        <i data-lucide="send" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                                        <span wire:loading.remove wire:target="sendContribution({{ $case->id }})">
                                            Contribute
                                        </span>
                                        <span wire:loading wire:target="sendContribution({{ $case->id }})">
                                            Redirecting...
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-12 text-center bg-white border border-slate-200 rounded-xl text-slate-400 text-xs">
                            There are currently no active benevolence cases requiring contributions.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ================= CONTRIBUTION HISTORY ================= -->
            <div class="space-y-4 pt-4">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                        CONTRIBUTION HISTORY
                    </h2>

                    <a href="{{ route('portal.pdf.mycontribtiondowlaod') }}"
                        class="px-3 py-1.5 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-2xs bg-slate-900 hover:bg-slate-800 flex items-center space-x-1.5 cursor-pointer inline-flex">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Download PDF</span>
                    </a>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                    @if ($contributionHistory->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Date
                                        </th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Case Number
                                        </th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Affected Member Name
                                        </th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Affected Member Number
                                        </th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Amount
                                        </th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Payment Channel
                                        </th>
                                        <th
                                            class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Reference
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($contributionHistory as $contribution)
                                        <tr class="hover:bg-slate-50 transition">
                                            <!-- DATE -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span class="text-xs font-semibold text-slate-800 font-mono">
                                                    {{ $contribution->created_at->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a') }}
                                                </span>
                                            </td>

                                            <!-- CASE NUMBER -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                @if ($contribution->benevolenceCase?->case_number)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold font-mono bg-slate-100 text-slate-700 border border-slate-200">
                                                        {{ $contribution->benevolenceCase->case_number }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-slate-400">
                                                        N/A
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- AFFECTED MEMBER NAME -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span class="text-xs font-semibold text-slate-800">
                                                    {{ trim(
                                                        optional($contribution->benevolenceCase?->member)->salutation .
                                                            ' ' .
                                                            optional($contribution->benevolenceCase?->member)->first_name .
                                                            ' ' .
                                                            optional($contribution->benevolenceCase?->member)->last_name,
                                                    ) ?:
                                                        'N/A' }}
                                                </span>
                                            </td>

                                            <!-- AFFECTED MEMBER NUMBER -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span class="text-xs font-mono font-semibold text-slate-700">
                                                    {{ optional($contribution->benevolenceCase?->member)->tsc_number ??
                                                        (optional($contribution->benevolenceCase?->member)->membership_number ?? 'N/A') }}
                                                </span>
                                            </td>

                                            <!-- AMOUNT -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span class="text-xs font-mono font-bold text-slate-900">
                                                    KSH {{ number_format($contribution->amount ?? 0) }}
                                                </span>
                                            </td>

                                            <!-- PAYMENT CHANNEL -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span class="text-xs font-medium text-slate-700">
                                                    {{ $contribution->payment_channel ?? 'N/A' }}
                                                </span>
                                            </td>

                                            <!-- REFERENCE -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span class="text-xs font-mono font-bold text-slate-700">
                                                    {{ $contribution->reference_number ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-12 px-6 text-center">
                            <div
                                class="w-10 h-10 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="receipt" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">
                                No contribution record
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    document.addEventListener('livewire:updated', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
