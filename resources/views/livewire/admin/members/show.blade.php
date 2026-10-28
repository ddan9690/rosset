<div class="space-y-6 max-w-6xl mx-auto pb-12">

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white px-5 py-4 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-sm font-bold uppercase tracking-wider text-slate-800">Member Profile & Welfare Scorecard</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Comprehensive participation, solidarity fund balance, and account details for {{ $user->first_name }} {{ $user->last_name }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" wire:click="openEditModal" class="px-3 py-1.5 bg-[#2EA3F2] hover:bg-sky-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">Edit Profile</button>
            
            @if($user->status !== 'suspended')
                <button type="button" wire:click="openStatusModal('suspended')" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">Suspend</button>
            @endif

            @if($user->status !== 'deregistered')
                <button type="button" wire:click="openStatusModal('deregistered')" class="px-3 py-1.5 bg-red-600 hover:bg-red-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">Deregister</button>
            @endif

            @if($user->status !== 'active')
                <button type="button" wire:click="openStatusModal('active')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">Make Active</button>
            @endif

            <a href="{{ route('admin.members') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-lg transition">&larr; Back</a>
        </div>
    </div>

    <!-- ================= TOP SECTION: PROFILE CARD & WALLET SUMMARY ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Profile Overview Card -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-6 text-xs">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                <div class="flex items-center gap-4">
                    <div class="relative group cursor-pointer" wire:click="openAvatarModal" title="Click to update profile picture">
                        @if($user->profile_picture && Storage::disk('public')->exists($user->profile_picture))
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->first_name }}" class="w-16 h-16 rounded-full object-cover border-2 border-slate-200 group-hover:border-slate-900 transition shadow-xs">
                        @else
                            <div class="w-16 h-16 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center text-slate-400 group-hover:border-slate-900 group-hover:text-slate-700 transition shadow-xs">
                                <i data-lucide="user" class="w-8 h-8"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 rounded-full bg-slate-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Membership Number</span>
                        <span class="text-base font-mono font-bold text-slate-900">{{ $user->membership_number ?? 'Not Assigned' }}</span>
                        <p class="text-[11px] text-slate-500 mt-0.5">Click profile picture to update</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider 
                        @if($user->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-200 
                        @elseif($user->status === 'suspended') bg-amber-50 text-amber-700 border border-amber-200 
                        @else bg-red-50 text-red-700 border border-red-200 @endif">
                        {{ ucfirst($user->status) }}
                    </span>
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider 
                        @if($user->registration_fee_paid) bg-emerald-50 text-emerald-700 border border-emerald-200 @else bg-red-50 text-red-700 border border-red-200 @endif">
                        Reg Fee: {{ $user->registration_fee_paid ? 'Paid' : 'Unpaid' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Full Name</span>
                    <span class="text-slate-800 font-semibold text-sm">{{ $user->first_name }} {{ $user->last_name }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Email Address</span>
                    <span class="text-slate-800 font-mono">{{ $user->email }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Phone Number</span>
                    <span class="text-slate-800 font-mono">{{ $user->phone }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Gender</span>
                    <span class="text-slate-800 uppercase font-medium">{{ $user->gender ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">National ID Number</span>
                    <span class="text-slate-800 font-mono">{{ $user->id_number ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">TSC Number</span>
                    <span class="text-slate-800 font-mono">{{ $user->tsc_number ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">School / Institution</span>
                    <span class="text-slate-800">{{ $user->school ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">School Level</span>
                    <span class="text-slate-800">{{ $user->school_level ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Account Created</span>
                    <span class="text-slate-800 font-mono">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Last Active</span>
                    <span class="text-slate-800 font-mono">{{ $user->last_active_at ? \Carbon\Carbon::parse($user->last_active_at)->diffForHumans() : 'Never' }}</span>
                </div>
            </div>
        </div>

        <!-- Solidarity Fund Balance Summary Card -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-6 shadow-md flex flex-col justify-between space-y-6">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Solidarity Fund Balance</span>
                <div class="w-8 h-8 rounded-full bg-blue-500/20 text-[#2EA3F2] flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold font-mono text-white tracking-tight">
                    Ksh {{ number_format($wallet->balance, 2) }}
                </h3>
                <p class="text-xs text-slate-400 mt-1">Virtual fund used to auto-settle bereavement contributions & store credit.</p>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-700/60 text-xs">
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Total Top-ups</span>
                    <span class="font-mono font-bold text-emerald-400">Ksh {{ number_format($wallet->total_topups, 2) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Total Deductions</span>
                    <span class="font-mono font-bold text-red-400">Ksh {{ number_format($wallet->total_deductions, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= RATING SCORECARD & CLAIM ELIGIBILITY ================= -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="flex items-center space-x-2">
                <div>
                    <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">Welfare Rating & Claim Eligibility Scorecard</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Calculated score across all eligible cases since member joining date.</p>
                </div>
                <!-- Interactive Rating Tooltip/Info Button -->
                <button type="button" wire:click="$set('showRatingInfoModal', true)" class="text-slate-400 hover:text-[#2EA3F2] transition cursor-pointer p-1" title="How is this rating calculated?">
                    <i data-lucide="info" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="flex items-center space-x-3 border px-4 py-2 rounded-xl {{ $ratingData['badge_color'] }}">
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase block">{{ $ratingData['tier'] }}</span>
                    <span class="text-xl font-extrabold font-mono">{{ $ratingData['score'] }}%</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-white/30 flex items-center justify-center font-bold text-sm">
                    {{ $ratingData['contributed_count'] }}/{{ $ratingData['total_eligible'] }}
                </div>
            </div>
        </div>

        <!-- Claim Eligibility Status Alert -->
        @if($eligibilityError)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-800 flex items-start space-x-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <strong class="font-bold uppercase tracking-wider text-[11px] block text-red-900">Ineligible to Claim Assistance</strong>
                    <p class="mt-0.5 text-slate-700">{{ $eligibilityError }}</p>
                </div>
            </div>
        @else
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-xs text-emerald-800 flex items-start space-x-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <strong class="font-bold uppercase tracking-wider text-[11px] block text-emerald-900">Fully Eligible for Welfare Claim</strong>
                    <p class="mt-0.5 text-slate-700">Member meets all constitutional requirements, annual fees, profile completion, and contribution participation rules.</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="text-slate-500 font-bold uppercase text-[10px]">Total Obliged Cases</span>
                <h4 class="text-xl font-extrabold font-mono text-slate-900 mt-1">{{ $ratingData['total_eligible'] }}</h4>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="text-slate-500 font-bold uppercase text-[10px]">Contributed Cases</span>
                <h4 class="text-xl font-extrabold font-mono text-emerald-600 mt-1">{{ $ratingData['contributed_count'] }}</h4>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="text-slate-500 font-bold uppercase text-[10px]">Punctual Payments</span>
                <h4 class="text-xl font-extrabold font-mono text-blue-600 mt-1">{{ $ratingData['punctual_count'] }}</h4>
            </div>
        </div>
    </div>

    <!-- ================= SINGLE CONSOLIDATED BENEVOLENCE CONTRIBUTIONS TABLE ================= -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">Benevolence Case Contributions & Participation Log</h3>
                <p class="text-[11px] text-slate-500">Single consolidated statement for all eligible benevolence cases in the association.</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-500">{{ $eligibleCases->count() }} cases logged</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                        <th class="py-3 px-4">Case No</th>
                        <th class="py-3 px-4">Affected Member</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Amount Paid</th>
                        <th class="py-3 px-4">Date Paid</th>
                        <th class="py-3 px-4">Payment Channel</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($eligibleCases as $case)
                        @php
                            $contribution = $contributions->get($case->id);
                            $isPaid = !is_null($contribution);
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $case->case_number }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $case->member?->first_name }} {{ $case->member?->last_name }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-600">
                                {{ $case->category?->name ?? 'Benevolence' }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold {{ $isPaid ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $isPaid ? 'Ksh ' . number_format($contribution->amount, 0) : '-' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">
                                {{ $isPaid ? $contribution->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-700 font-medium">
                                @if($isPaid)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $contribution->payment_channel }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($isPaid)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Paid
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-100 text-red-800 border border-red-200">
                                        Not Paid
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No eligible benevolence cases found for this member.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= RATING EXPLANATION MODAL ================= -->
    @if($showRatingInfoModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="bg-white rounded-xl shadow-lg border border-slate-200 max-w-md w-full p-6 space-y-4 text-xs animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold uppercase tracking-wider text-slate-900">How Welfare Rating Is Calculated</h3>
                    <button type="button" wire:click="$set('showRatingInfoModal', false)" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
                </div>
                
                <div class="space-y-3 text-slate-600 leading-relaxed">
                    <p>The ROSSET-SWA welfare rating score evaluates a member's active solidarity and commitment across three main metrics:</p>
                    
                    <ul class="space-y-2 pl-4 list-disc text-slate-700">
                        <li><strong>Base Participation Ratio (Up to 85%):</strong> Computed from the proportion of closed/active benevolence cases the member has contributed to since joining.</li>
                        <li><strong>Punctuality Tracking:</strong> Contributions made on or before the case deadline receive full credit (100%), while post-deadline/late contributions receive partial credit (70%).</li>
                        <li><strong>Solidarity Wallet Commitment (Up to +15% Bonus):</strong> Maintaining a healthy virtual wallet balance or topping up funds to auto-settle bereavement cases demonstrates proactive brotherhood/sisterhood, boosting the final score.</li>
                    </ul>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-800 uppercase tracking-wider text-[10px] block mb-1">Rating Categories:</span>
                        <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                            <span class="text-emerald-700 font-semibold">• 90%+: Exemplary Pillar</span>
                            <span class="text-blue-700 font-semibold">• 75%–89%: Reliable Contributor</span>
                            <span class="text-amber-700 font-semibold">• 50%–74%: Active Member</span>
                            <span class="text-red-700 font-semibold">• Below 50%: At-Risk Status</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="button" wire:click="$set('showRatingInfoModal', false)" class="px-4 py-2 bg-slate-900 text-white rounded-lg font-bold uppercase tracking-wider hover:bg-slate-800 transition cursor-pointer">Got It</button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= EDIT PROFILE MODAL ================= -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="bg-white rounded-xl shadow-lg border border-slate-200 max-w-lg w-full p-6 space-y-4 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold uppercase tracking-wider text-slate-800">Edit Member Details</h3>
                    <button type="button" wire:click="closeEditModal" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
                </div>

                <form wire:submit="updateMemberDetails" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">First Name</label>
                            <input type="text" wire:model="first_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2]">
                            @error('first_name') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Last Name</label>
                            <input type="text" wire:model="last_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2]">
                            @error('last_name') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Email Address</label>
                            <input type="email" wire:model="email" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-mono focus:ring-2 focus:ring-[#2EA3F2]">
                            @error('email') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Phone Number</label>
                            <input type="text" wire:model="phone" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-mono focus:ring-2 focus:ring-[#2EA3F2]">
                            @error('phone') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">School / Institution</label>
                            <input type="text" wire:model="school" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2]">
                            @error('school') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">School Level</label>
                            <select wire:model="school_level" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] bg-white">
                                <option value="">Select Level</option>
                                <option value="Primary">Primary</option>
                                <option value="Junior">Junior</option>
                                <option value="Senior">Senior</option>
                            </select>
                            @error('school_level') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeEditModal" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-[#2EA3F2] text-white rounded-lg font-bold uppercase tracking-wider hover:bg-sky-500 transition">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= STATUS CONFIRMATION MODAL ================= -->
    @if($showStatusModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="bg-white rounded-xl shadow-lg border border-slate-200 max-w-sm w-full p-6 space-y-4 text-xs">
                <h3 class="font-bold uppercase tracking-wider text-slate-900">Confirm Status Change</h3>
                <p class="text-slate-600">
                    Are you sure you want to change this member's status to <span class="font-bold uppercase text-[#0E3A59]">{{ $selectedStatus }}</span>?
                </p>
                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" wire:click="$set('showStatusModal', false)" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    <button type="button" wire:click="updateStatus" class="px-4 py-2 rounded-lg bg-[#2EA3F2] text-white font-bold uppercase tracking-wider hover:bg-sky-500 transition cursor-pointer">Confirm</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Update Avatar Modal -->
    @if($showAvatarModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="bg-white rounded-xl shadow-lg border border-slate-200 max-w-md w-full p-6 space-y-4 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold uppercase tracking-wider text-slate-800">Update Profile Picture</h3>
                    <button type="button" wire:click="closeAvatarModal" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
                </div>

                <form wire:submit="updateAvatar" class="space-y-4">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="w-24 h-24 rounded-full border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden bg-slate-50">
                            @if ($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif($user->profile_picture && Storage::disk('public')->exists($user->profile_picture))
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="user" class="w-10 h-10 text-slate-400"></i>
                            @endif
                        </div>

                        <div class="w-full">
                            <input type="file" wire:model="avatar" accept="image/*" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                            @error('avatar') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeAvatarModal" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-slate-900 text-white rounded-lg font-semibold hover:bg-slate-800 transition">Upload Picture</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:navigated', () => { if (window.lucide) lucide.createIcons(); });
    document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });
</script>