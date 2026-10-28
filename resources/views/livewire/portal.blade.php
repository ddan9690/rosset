<div class="min-h-screen bg-slate-50 py-8" x-data="{ showModal: false, modalTitle: '', modalDetails: '' }">
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

        @if (!$isProfileComplete)
            <!-- ================= INCOMPLETE PROFILE NOTICE ================= -->
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs text-center max-w-md mx-auto space-y-6 my-16">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="user-pen" class="w-6 h-6"></i>
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900">Profile Update Required</h3>
                    <p class="text-sm text-slate-700 leading-relaxed font-medium">
                        Dear <strong class="text-slate-900">{{ Auth::user()->first_name ?? 'Member' }}</strong>,
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
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs text-center max-w-md mx-auto space-y-6 my-16">
                <div class="w-12 h-12 bg-blue-50 text-[#2EA3F2] rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="credit-card" class="w-6 h-6"></i>
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900">Registration Fee Pending</h3>
                    <p class="text-sm text-slate-700 leading-relaxed font-medium">
                        Dear <strong class="text-slate-900">{{ Auth::user()->first_name ?? 'Member' }}</strong>,
                        please complete your registration by paying registration fee of <strong class="text-slate-900 font-mono">KSH 150</strong>.
                    </p>
                </div>

                <div>
                    <button wire:click="redirectToPayment" wire:loading.attr="disabled"
                        class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow-sm bg-red-600 hover:bg-red-700 cursor-pointer flex items-center justify-center space-x-2">
                        <span wire:loading.remove wire:target="redirectToPayment">Pay Registration Fee (KSH 150)</span>
                        <span wire:loading wire:target="redirectToPayment">Redirecting...</span>
                    </button>
                </div>
            </div>
        @else
            <!-- ================= FULL MEMBER PORTAL CONTENT ================= -->

            <!-- Member Profile & Solidarity Summary Strip -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                            <h1 class="text-base font-bold text-slate-900">{{ $memberName }}</h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $memberStatus }}
                            </span>
                        </div>

                        <div class="flex items-center space-x-3 flex-wrap gap-y-1 pt-0.5">
                            <p class="text-xs text-slate-500 font-mono">
                                Membership No: <span class="font-semibold text-slate-700">{{ $membershipNumber }}</span>
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

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full md:w-auto">
                    <!-- Submit Benevolence Claim Action Button -->
                    <a href="{{ route('member.claims') }}" wire:navigate
                        class="px-4 py-3 bg-gradient-to-r from-sky-500 to-[#2EA3F2] hover:from-sky-600 hover:to-sky-500 text-white text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-md transition flex items-center justify-center space-x-2 cursor-pointer">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Submit Benevolence Claim</span>
                    </a>

                    <!-- Solidarity Fund Summary Card -->
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 flex items-center justify-between md:justify-start space-x-6">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Solidarity Fund Balance</span>
                            <div class="flex items-center space-x-3 mt-0.5">
                                <span class="text-lg font-extrabold text-slate-900 font-mono">KSH {{ number_format($solidarityBalance) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 shadow-2xs">
                                <i data-lucide="wallet" class="w-4 h-4"></i>
                            </div>

                            <a href="{{ route('member.solidarity') }}" wire:navigate
                                class="px-3.5 py-2 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-2xs hover:opacity-90 bg-[#0E3A59]">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= BENEVOLENCE CASES SECTION ================= -->
            <div class="space-y-4">
                <div class="border-b border-slate-200 pb-3 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">ROSSET-SWA BENEVOLENCE CASES</h2>

                    <!-- Search and Filter Bar -->
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-64">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Search case no. or member..."
                                class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-1 focus:ring-slate-900">
                        </div>

                        <div class="relative">
                            <select wire:model.live="sortBy"
                                class="py-1.5 px-3 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-1 focus:ring-slate-900 cursor-pointer">
                                <option value="deadline_soonest">Deadline: Soonest First</option>
                                <option value="deadline_latest">Deadline: Latest First</option>
                                <option value="newest">Newest Created</option>
                                <option value="oldest">Oldest Created</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Flash Message -->
                @if (session()->has('message'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                            <span>{{ session('message') }} Thank you for standing with <strong>{{ Auth::user()->first_name ?? 'Member' }}</strong>.</span>
                        </div>
                    </div>
                @endif

                <!-- Benevolence Cases Table View -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                                <tr>
                                    <th class="px-5 py-3">Case Number</th>
                                    <th class="px-5 py-3">Affected Member</th>
                                    <th class="px-5 py-3">Case Details</th>
                                    <th class="px-5 py-3">Deadline</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Contribution</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($benevolenceCases as $case)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-5 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                            {{ $case->case_number }}
                                        </td>
                                        <td class="px-5 py-4 font-bold text-slate-800 whitespace-nowrap">
                                            {{ trim(($case->member->salutation ?? '') . ' ' . ($case->member->first_name ?? '') . ' ' . ($case->member->last_name ?? '')) }}
                                        </td>
                                        <td class="px-5 py-4">
                                            <button type="button"
                                                @click="showModal = true; modalTitle = 'Case: {{ $case->case_number }}'; modalDetails = @js($case->case_details);"
                                                class="text-xs text-[#2EA3F2] font-bold hover:underline inline-flex items-center space-x-1 cursor-pointer">
                                                <span>{{ Str::limit($case->case_details, 40) }}</span>
                                            </button>
                                        </td>
                                        <td class="px-5 py-4 font-mono text-red-600 font-semibold whitespace-nowrap">
                                            {{ $case->deadline ? \Carbon\Carbon::parse($case->deadline)->format('d-m-Y') : 'N/A' }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            @if(strtolower($case->status) === 'active')
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Active
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-200 text-slate-700 border border-slate-300">
                                                    Closed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right whitespace-nowrap">
                                            @if($case->contribution_made)
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Paid
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                                    Not Paid
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-slate-400">
                                            There are currently no active or closed benevolence cases.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Instructions Notice -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-xs text-emerald-900 leading-relaxed font-medium shadow-2xs">
                    <p class="font-bold mb-1 uppercase tracking-wider text-[11px] text-emerald-800 flex items-center space-x-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-emerald-600"></i>
                        <span>How to Contribute to a Case:</span>
                    </p>
                    To contribute to a case, send your contribution by Paybill no: <strong class="font-mono bg-emerald-100 px-1 py-0.5 rounded">522533</strong> | A/c: <strong class="font-mono bg-emerald-100 px-1 py-0.5 rounded">7936435#membernumber</strong> (e.g., <code class="font-mono">7936435#121</code> for member no. 121). Forward the message to the treasurer Mr Josephine <strong class="font-mono">0711317235</strong>.
                </div>

                <!-- Pagination Links -->
                @if ($benevolenceCases->hasPages())
                    <div class="pt-4">
                        {{ $benevolenceCases->links() }}
                    </div>
                @endif
            </div>

            <!-- ================= CASE DETAILS MODAL ================= -->
            <div x-cloak x-show="showModal"
                class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
                <div @click.away="showModal = false"
                    class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider" x-text="modalTitle"></h3>
                        <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-lg leading-relaxed" x-text="modalDetails"></p>
                    </div>

                    <div class="pt-2 text-right">
                        <button @click="showModal = false"
                            class="px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-slate-800 cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => { if (window.lucide) lucide.createIcons(); });
    document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });
</script>