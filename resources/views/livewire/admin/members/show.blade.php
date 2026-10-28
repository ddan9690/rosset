<div class="space-y-6 max-w-6xl mx-auto pb-12"
     x-data="{}"
     @swal:success.window="Swal.fire({ title: 'Success!', text: $event.detail[0].message, icon: 'success', timer: 2500, showConfirmButton: false })">

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
            <a href="{{ route('admin.members.edit', $user->id) }}" class="px-3 py-1.5 bg-[#2EA3F2] hover:bg-sky-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition">Full Edit Form</a>
            
            @if($user->status !== 'suspended')
                <button type="button" onclick="confirmStatusChange('suspended')" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">Suspend</button>
            @endif

            @if($user->status !== 'deregistered')
                <button type="button" onclick="confirmStatusChange('deregistered')" class="px-3 py-1.5 bg-red-600 hover:bg-red-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">Deregister</button>
            @endif

            @if($user->status !== 'active')
                <button type="button" onclick="confirmStatusChange('active')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">Make Active</button>
            @endif

            <a href="{{ route('admin.members') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-lg transition">&larr; Back</a>
        </div>
    </div>

    <!-- ================= TOP SECTION: PROFILE CARD & RATING/WALLET CARD ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Profile Overview Card -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-6 text-xs">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                <div class="flex items-center gap-4">
                    <div class="relative group cursor-pointer" wire:click="openAvatarModal" title="Click to view/update profile picture">
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
                        <!-- Name takes place of membership number and is bold -->
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Name</span>
                        <span class="text-base font-bold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</span>
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
                <!-- Membership Number takes place of name -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Membership Number</span>
                    <span class="text-slate-800 font-mono font-bold">{{ $user->membership_number ?? 'Not Assigned' }}</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Gender</span>
                    <span class="text-slate-800 uppercase font-medium">{{ $user->gender ?? 'N/A' }}</span>
                </div>

                <!-- Inline Editable Email -->
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Email Address</span>
                        @if($editingField !== 'email')
                            <button type="button" wire:click="editField('email')" class="text-[#2EA3F2] hover:underline font-bold text-[10px] flex items-center gap-0.5 cursor-pointer"><i data-lucide="edit-2" class="w-3 h-3"></i> Edit</button>
                        @endif
                    </div>
                    @if($editingField === 'email')
                        <div class="mt-1 space-y-1">
                            <input type="email" wire:model="email" class="w-full px-2.5 py-1.5 border border-slate-300 rounded text-xs font-mono">
                            <div class="flex items-center space-x-1.5">
                                <button type="button" wire:click="saveField('email')" class="px-2 py-1 bg-emerald-600 text-white rounded font-bold text-[10px]">Save</button>
                                <button type="button" wire:click="cancelEdit" class="px-2 py-1 bg-slate-200 text-slate-700 rounded font-bold text-[10px]">Cancel</button>
                            </div>
                        </div>
                    @else
                        <span class="text-slate-800 font-mono">{{ $user->email }}</span>
                    @endif
                </div>

                <!-- Inline Editable Phone -->
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Phone Number</span>
                        @if($editingField !== 'phone')
                            <button type="button" wire:click="editField('phone')" class="text-[#2EA3F2] hover:underline font-bold text-[10px] flex items-center gap-0.5 cursor-pointer"><i data-lucide="edit-2" class="w-3 h-3"></i> Edit</button>
                        @endif
                    </div>
                    @if($editingField === 'phone')
                        <div class="mt-1 space-y-1">
                            <input type="text" wire:model="phone" class="w-full px-2.5 py-1.5 border border-slate-300 rounded text-xs font-mono">
                            <div class="flex items-center space-x-1.5">
                                <button type="button" wire:click="saveField('phone')" class="px-2 py-1 bg-emerald-600 text-white rounded font-bold text-[10px]">Save</button>
                                <button type="button" wire:click="cancelEdit" class="px-2 py-1 bg-slate-200 text-slate-700 rounded font-bold text-[10px]">Cancel</button>
                            </div>
                        </div>
                    @else
                        <span class="text-slate-800 font-mono">{{ $user->phone }}</span>
                    @endif
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">National ID Number</span>
                    <span class="text-slate-800 font-mono">{{ $user->id_number ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">TSC Number</span>
                    <span class="text-slate-800 font-mono">{{ $user->tsc_number ?? 'N/A' }}</span>
                </div>

                <!-- Inline Editable School -->
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">School / Institution</span>
                        @if($editingField !== 'school')
                            <button type="button" wire:click="editField('school')" class="text-[#2EA3F2] hover:underline font-bold text-[10px] flex items-center gap-0.5 cursor-pointer"><i data-lucide="edit-2" class="w-3 h-3"></i> Edit</button>
                        @endif
                    </div>
                    @if($editingField === 'school')
                        <div class="mt-1 space-y-1">
                            <input type="text" wire:model="school" class="w-full px-2.5 py-1.5 border border-slate-300 rounded text-xs">
                            <div class="flex items-center space-x-1.5">
                                <button type="button" wire:click="saveField('school')" class="px-2 py-1 bg-emerald-600 text-white rounded font-bold text-[10px]">Save</button>
                                <button type="button" wire:click="cancelEdit" class="px-2 py-1 bg-slate-200 text-slate-700 rounded font-bold text-[10px]">Cancel</button>
                            </div>
                        </div>
                    @else
                        <span class="text-slate-800">{{ $user->school ?? 'N/A' }}</span>
                    @endif
                </div>

                <!-- Inline Editable School Level -->
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">School Level</span>
                        @if($editingField !== 'school_level')
                            <button type="button" wire:click="editField('school_level')" class="text-[#2EA3F2] hover:underline font-bold text-[10px] flex items-center gap-0.5 cursor-pointer"><i data-lucide="edit-2" class="w-3 h-3"></i> Edit</button>
                        @endif
                    </div>
                    @if($editingField === 'school_level')
                        <div class="mt-1 space-y-1">
                            <select wire:model="school_level" class="w-full px-2.5 py-1.5 border border-slate-300 rounded text-xs bg-white">
                                <option value="">Select Level</option>
                                <option value="Primary">Primary</option>
                                <option value="Junior">Junior</option>
                                <option value="Senior">Senior</option>
                            </select>
                            <div class="flex items-center space-x-1.5">
                                <button type="button" wire:click="saveField('school_level')" class="px-2 py-1 bg-emerald-600 text-white rounded font-bold text-[10px]">Save</button>
                                <button type="button" wire:click="cancelEdit" class="px-2 py-1 bg-slate-200 text-slate-700 rounded font-bold text-[10px]">Cancel</button>
                            </div>
                        </div>
                    @else
                        <span class="text-slate-800">{{ $user->school_level ?? 'N/A' }}</span>
                    @endif
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Joined</span>
                    <span class="text-slate-800 font-mono">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Last Active</span>
                    <span class="text-slate-800 font-mono">{{ $user->last_active_at ? \Carbon\Carbon::parse($user->last_active_at)->diffForHumans() : 'Never' }}</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Welfare Rating & Solidarity Fund Card in Same Line / Grid -->
        <div class="space-y-6 flex flex-col justify-between">
            
            <!-- Welfare Rating Card -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-1.5">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-900">Welfare Rating</h3>
                        <button type="button" wire:click="$set('showRatingInfoModal', true)" class="text-[11px] text-[#2EA3F2] font-bold hover:underline cursor-pointer">what is this?</button>
                    </div>
                    <span class="text-xl font-extrabold font-mono text-slate-900">{{ $ratingData['score'] }}%</span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs pt-1">
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase text-[9px] block">Obliged Cases</span>
                        <strong class="text-slate-900 font-mono text-sm">{{ $ratingData['total_eligible'] }}</strong>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase text-[9px] block">Contributed</span>
                        <strong class="text-emerald-600 font-mono text-sm">{{ $ratingData['contributed_count'] }}</strong>
                    </div>
                </div>
            </div>

            <!-- Solidarity Fund Balance Card (Whole number, no decimals) -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-6 shadow-md flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Solidarity Fund Balance</span>
                    <h3 class="text-2xl font-extrabold font-mono text-white tracking-tight mt-1">
                        Ksh {{ number_format($wallet->balance, 0) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-500/20 text-[#2EA3F2] flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>

        </div>

    </div>

    <!-- ================= CLAIM ELIGIBILITY ALERT ================= -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
        @if($eligibilityError)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-800 flex items-start space-x-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <strong class="font-bold uppercase tracking-wider text-[11px] block text-red-900">Ineligible to Open/Claim Benevolence</strong>
                    <p class="mt-0.5 text-slate-700">{{ $eligibilityError }}</p>
                </div>
            </div>
        @else
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-xs text-emerald-800 flex items-start space-x-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <strong class="font-bold uppercase tracking-wider text-[11px] block text-emerald-900">Eligible to Open/Claim Benevolence</strong>
                    <p class="mt-0.5 text-slate-700">Member meets all constitutional requirements, annual fees, profile completion, and contribution participation rules.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- ================= BENEVOLENCE CASE CONTRIBUTIONS TABLE ================= -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">Benevolence Case Contributions</h3>
        </div>
        <div class="overflow-x-auto">
            @if($eligibleCases->count() > 0)
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
                        @foreach($eligibleCases as $case)
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
                                    {{ $isPaid ? number_format($contribution->amount, 0) : '-' }}
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
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-12 text-center text-slate-400 text-xs">
                    No contribution records found for the member.
                </div>
            @endif
        </div>
    </div>

    <!-- ================= RATING EXPLANATION MODAL ================= -->
    @if($showRatingInfoModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="bg-white rounded-xl shadow-lg border border-slate-200 max-w-md w-full p-6 space-y-4 text-xs animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold uppercase tracking-wider text-slate-900">About Welfare Rating</h3>
                    <button type="button" wire:click="$set('showRatingInfoModal', false)" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
                </div>
                
                <div class="space-y-3 text-slate-600 leading-relaxed">
                    <p>The system analyses a combination of factors like how the member contributes, deadlines, and wallet solidarity commitments to determine the welfare ratings.</p>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="button" wire:click="$set('showRatingInfoModal', false)" class="px-4 py-2 bg-slate-900 text-white rounded-lg font-bold uppercase tracking-wider hover:bg-slate-800 transition cursor-pointer">Got It</button>
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

                <div class="space-y-4">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="w-24 h-24 rounded-full border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden bg-slate-50">
                            @if($user->profile_picture && Storage::disk('public')->exists($user->profile_picture))
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="user" class="w-10 h-10 text-slate-400"></i>
                            @endif
                        </div>

                        @if($user->profile_picture && Storage::disk('public')->exists($user->profile_picture))
                            <button type="button" wire:click="openFullPictureModal" class="text-xs font-bold text-[#2EA3F2] hover:underline cursor-pointer">View Full Picture</button>
                        @endif
                    </div>

                    <form wire:submit="updateAvatar" class="space-y-3 pt-3 border-t border-slate-100">
                        <div>
                            <input type="file" wire:model="avatar" accept="image/*" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                            @error('avatar') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeAvatarModal" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-slate-900 text-white rounded-lg font-bold uppercase tracking-wider hover:bg-slate-800 transition">Update Profile Picture</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Full Size Picture Lightbox Modal -->
    @if($showFullPictureModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="relative max-w-2xl w-full flex flex-col items-center">
                <button type="button" wire:click="closeFullPictureModal" class="absolute -top-10 right-0 text-white hover:text-slate-300 text-lg font-bold cursor-pointer p-2">✕ Close</button>
                @if($user->profile_picture && Storage::disk('public')->exists($user->profile_picture))
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" class="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain border border-slate-700">
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:navigated', () => { if (window.lucide) lucide.createIcons(); });
    document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });

    function confirmStatusChange(status) {
        let titleText = 'Confirm Status Change';
        let confirmColor = '#2EA3F2';
        let actionDesc = `Are you sure you want to change this member's status to ${status.toUpperCase()}?`;

        if (status === 'suspended') {
            confirmColor = '#f59e0b';
            actionDesc = 'Suspending this member will restrict their portal privileges and welfare claims.';
        } else if (status === 'deregistered') {
            confirmColor = '#ef4444';
            actionDesc = 'Deregistering this member will terminate their active welfare membership status.';
        } else if (status === 'active') {
            confirmColor = '#10b981';
            actionDesc = 'Reactivating this member will restore their full welfare membership and privileges.';
        }

        Swal.fire({
            title: titleText,
            text: actionDesc,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#64748b',
            confirmButtonText: `Yes, Make ${status.toUpperCase()}`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('updateStatusFromJS', status);
            }
        });
    }
</script>