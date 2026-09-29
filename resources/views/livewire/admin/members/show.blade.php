<div class="space-y-6 max-w-6xl mx-auto pb-12">

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white px-5 py-4 rounded-xl shadow-xs border border-slate-200 flex items-center justify-between">
        <div>
            <h1 class="text-sm font-bold uppercase tracking-wider text-slate-800">Member Details & Participation</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Viewing complete profile, solidarity fund, and benevolence records for {{ $user->first_name }} {{ $user->last_name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.members.edit', $user->id) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">Edit Profile</a>
            <a href="{{ route('admin.members') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">&larr; Back</a>
        </div>
    </div>

    <!-- ================= TOP SECTION: PROFILE CARD & WALLET SUMMARY ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Profile Overview Card -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-6 text-xs">
            <!-- Top Banner with Avatar & Status -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                <div class="flex items-center gap-4">
                    <!-- Clickable Avatar Container -->
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
                        @elseif($user->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200 
                        @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                        {{ ucfirst($user->status) }}
                    </span>
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider 
                        @if($user->registration_fee_paid) bg-emerald-50 text-emerald-700 border border-emerald-200 @else bg-red-50 text-red-700 border border-red-200 @endif">
                        Reg Fee: {{ $user->registration_fee_paid ? 'Paid' : 'Unpaid' }}
                    </span>
                </div>
            </div>

            <!-- Details Grid -->
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
                    <span class="text-slate-800 font-mono">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : 'N/A' }}</span>
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
                    KES {{ number_format($wallet->balance, 2) }}
                </h3>
                <p class="text-xs text-slate-400 mt-1">Available virtual wallet balance for benevolence contributions.</p>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-700/60 text-xs">
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Top-ups</span>
                    <span class="font-mono font-bold text-emerald-400">KES {{ number_format($wallet->total_topups, 2) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Deductions</span>
                    <span class="font-mono font-bold text-red-400">KES {{ number_format($wallet->total_deductions, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= SECTION 1: BENEVOLENCE PARTICIPATION SCORECARD ================= -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">Benevolence Participation Scorecard</h3>
                <p class="text-xs text-slate-500 mt-0.5">Rating based on contribution records across all closed & inactive benevolence cases.</p>
            </div>
            <div class="flex items-center space-x-3 bg-blue-50 border border-blue-200 px-4 py-2 rounded-xl">
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase text-blue-600 block">Rating Score</span>
                    <span class="text-xl font-extrabold font-mono text-blue-900">{{ $scorePercentage }}%</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#2EA3F2] text-white flex items-center justify-center font-bold text-sm">
                    {{ $userContributedCount }}/{{ $eligibleTotalCases }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="text-slate-500 font-bold uppercase text-[10px]">Total Closed Cases</span>
                <h4 class="text-xl font-extrabold font-mono text-slate-900 mt-1">{{ $eligibleTotalCases }}</h4>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="text-slate-500 font-bold uppercase text-[10px]">Cases Contributed To</span>
                <h4 class="text-xl font-extrabold font-mono text-emerald-600 mt-1">{{ $userContributedCount }}</h4>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="text-slate-500 font-bold uppercase text-[10px]">Cases Missed / Uncontributed</span>
                <h4 class="text-xl font-extrabold font-mono text-amber-600 mt-1">{{ $eligibleTotalCases - $userContributedCount }}</h4>
            </div>
        </div>
    </div>

    <!-- ================= SECTION 2: CASES CONTRIBUTED TO ================= -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">Benevolence Cases Contributed To</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                        <th class="py-3 px-4">Case Number</th>
                        <th class="py-3 px-4">Affected Member</th>
                        <th class="py-3 px-4">Benevolence Category</th>
                        <th class="py-3 px-4">Amount Paid</th>
                        <th class="py-3 px-4">Transaction Ref</th>
                        <th class="py-3 px-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($contributedTransactions as $tx)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $tx->case_number ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $tx->benevolenceCase?->user?->first_name }} {{ $tx->benevolenceCase?->user?->last_name }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-50 text-blue-700">
                                    {{ $tx->benevolenceCase?->category?->name ?? 'Benevolence' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-600">KES {{ number_format($tx->amount, 2) }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-700">{{ $tx->reference_number }}</td>
                            <td class="py-3 px-4 font-mono text-slate-500">
                                {{ $tx->paid_at ? $tx->paid_at->format('Y-m-d H:i') : $tx->created_at->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No benevolence contributions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= SECTION 3: CASES NOT CONTRIBUTED TO ================= -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">Benevolence Cases Not Contributed To</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                        <th class="py-3 px-4">Case Number</th>
                        <th class="py-3 px-4">Affected Member</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Deadline</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($missedCases as $case)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $case->case_number }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $case->user?->first_name }} {{ $case->user?->last_name }}
                            </td>
                            <td class="py-3 px-4 font-medium">{{ $case->category?->name }}</td>
                            <td class="py-3 px-4 font-mono text-red-600">{{ $case->deadline }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700">
                                    {{ ucfirst($case->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Fantastic! Member has participated in all available benevolence cases.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= SECTION 4: FINANCIAL STATEMENT ================= -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">Solidarity Fund Statement (Including Top-ups & Registration Fee)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                        <th class="py-3 px-4">Transaction Number</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($financialStatements as $stmt)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $stmt->reference_number }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $stmt->type === 'registration_fee' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ str_replace('_', ' ', $stmt->type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">{{ $stmt->description ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-600">+ KES {{ number_format($stmt->amount, 2) }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700">
                                    {{ ucfirst($stmt->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">
                                {{ $stmt->paid_at ? $stmt->paid_at->format('Y-m-d H:i') : $stmt->created_at->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No financial transactions recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
                        <!-- Preview Box -->
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