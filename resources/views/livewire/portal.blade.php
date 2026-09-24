<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Top Fallback & Navigation Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            <a href="/" wire:navigate class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                <span>&larr; Back to Home</span>
            </a>
            <div>
                <a href="/login" wire:navigate class="text-xs font-bold hover:underline" style="color: #2EA3F2;">Back to Login</a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if(!$isRegistrationPaid)
            <!-- ================= UNPAID REGISTRATION FEE NOTICE (SIMPLIFIED) ================= -->
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs text-center max-w-md mx-auto space-y-6 my-16">
                <div class="space-y-2">
                    <p class="text-sm text-slate-700 leading-relaxed font-medium">
                        To complete your ROSSET-SWA registration, please pay the registration fee of <strong class="text-slate-900 font-mono">KES 200</strong>.
                    </p>
                </div>
                <div>
                    <button wire:click="payRegistrationFee" wire:loading.attr="disabled" class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow-sm bg-emerald-600 hover:bg-emerald-700 cursor-pointer flex items-center justify-center space-x-2">
                        <i data-lucide="credit-card" class="w-4 h-4" wire:loading.remove></i>
                        <span wire:loading.remove>Pay Registration Fee (KES 200)</span>
                        <span wire:loading>Processing Payment...</span>
                    </button>
                </div>
            </div>
        @else
            <!-- ================= FULL MEMBER PORTAL CONTENT ================= -->
            
            <!-- Member Profile & Solidarity Summary Strip -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                
                <!-- Member Details -->
                <div class="flex items-center space-x-4">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                            <h1 class="text-base font-bold text-slate-900">{{ $memberName }}</h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $memberStatus === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $memberStatus }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-mono">Membership No: <span class="font-semibold text-slate-700">{{ $membershipNumber }}</span></p>
                    </div>
                </div>

                <!-- Solidarity Fund Balance Box -->
                <div class="w-full md:w-auto bg-slate-50 border border-slate-200 rounded-lg p-3.5 flex items-center justify-between md:justify-start space-x-6">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Solidarity Fund Balance</span>
                        <div class="flex items-center space-x-3 mt-0.5">
                            <span class="text-lg font-extrabold text-slate-900 font-mono">KES {{ number_format($solidarityBalance) }}</span>
                            <a href="#" class="text-xs font-semibold text-blue-600 hover:underline flex items-center space-x-0.5">
                                <span>View</span>
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 shadow-2xs">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                    </div>
                </div>

            </div>

            <!-- TABLE 1: Cases You Have NOT Contributed To -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Pending Contributions (Not Contributed)</h2>
                        <p class="text-xs text-slate-500">Cases requiring your financial support.</p>
                    </div>
                    <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full">
                        {{ count(collect($benevolenceCases)->where('contribution_made', false)) }} Cases
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                                    <th class="py-3 px-4">Case Number</th>
                                    <th class="py-3 px-4">Member Name</th>
                                    <th class="py-3 px-4">Category</th>
                                    <th class="py-3 px-4">Required Amount</th>
                                    <th class="py-3 px-4">Deadline</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse(collect($benevolenceCases)->where('contribution_made', false) as $case)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $case['case_number'] }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-semibold text-slate-900">{{ $case['member_name'] }}</div>
                                            <div class="font-mono text-[10px] text-slate-500">{{ $case['membership_number'] }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold {{ $case['category'] === 'Self (Member Bereavement)' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                {{ $case['category'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">KES {{ number_format($case['amount']) }}</td>
                                        <td class="py-3.5 px-4 font-mono text-red-600 font-semibold">{{ $case['deadline'] }}</td>
                                        <td class="py-3.5 px-4 text-right">
                                            <button wire:click="sendContribution('{{ $case['case_number'] }}')" wire:loading.attr="disabled" class="px-3 py-1.5 rounded-lg text-white font-bold text-xs transition shadow-2xs hover:opacity-90 cursor-pointer bg-slate-900 inline-flex items-center space-x-1">
                                                <i data-lucide="send" class="w-3 h-3" wire:loading.remove wire:target="sendContribution('{{ $case['case_number'] }}')"></i>
                                                <span wire:loading.remove wire:target="sendContribution('{{ $case['case_number'] }}')">Contribute</span>
                                                <span wire:loading wire:target="sendContribution('{{ $case['case_number'] }}')">...</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400">
                                            You have successfully contributed to all pending cases!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TABLE 2: Cases You Have Contributed To -->
            <div class="space-y-4 pt-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Contribution History (Contributed)</h2>
                        <p class="text-xs text-slate-500">Record of all support payments you have processed.</p>
                    </div>
                    <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full">
                        {{ count(collect($benevolenceCases)->where('contribution_made', true)) }} Cases
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                                    <th class="py-3 px-4">Case Number</th>
                                    <th class="py-3 px-4">Member Name</th>
                                    <th class="py-3 px-4">Category</th>
                                    <th class="py-3 px-4">Amount Paid</th>
                                    <th class="py-3 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse(collect($benevolenceCases)->where('contribution_made', true) as $case)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $case['case_number'] }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-semibold text-slate-900">{{ $case['member_name'] }}</div>
                                            <div class="font-mono text-[10px] text-slate-500">{{ $case['membership_number'] }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                                {{ $case['category'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600">KES {{ number_format($case['amount']) }}</td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                <span>Contributed</span>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400">
                                            No contribution history recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @endif

    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        lucide.createIcons();
    });
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>