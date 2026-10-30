<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold" style="color: #0E3A59;">System Settings</h2>
        </div>
    </div>

    <!-- Flash Notification Messages -->
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MANAGE ROLES SECTION                       -->
    <!-- ========================================== -->
    <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-base font-extrabold" style="color: #0E3A59;">Administrative Role Management</h3>
            </div>
            <div class="flex items-center gap-2">
                @if($isDeveloper || $isSuperAdmin)
                    <button wire:click="openRoleModal('super-admin')" class="px-3 py-2 bg-[#0E3A59] text-white rounded-lg text-xs font-bold hover:bg-opacity-90 transition flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Add Super Admin
                    </button>
                    <button wire:click="openRoleModal('welfare-admin')" class="px-3 py-2 bg-[#2EA3F2] text-white rounded-lg text-xs font-bold hover:bg-opacity-90 transition flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Add Welfare Admin
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Super Admins List -->
            <div class="border border-slate-200 rounded-xl p-4 space-y-3 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600">Super Admins</h4>
                    <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded-full text-[10px] font-bold">{{ $superAdmins->count() }}</span>
                </div>
                <div class="space-y-2">
                    @forelse($superAdmins as $admin)
                        <div class="bg-white p-3 rounded-lg border border-slate-200 flex items-center justify-between gap-2">
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ $admin->first_name }} {{ $admin->last_name }}</p>
                            </div>
                            @if(($isDeveloper || $isSuperAdmin) && $admin->id !== $authUserId)
                                <button type="button" 
                                        onclick="confirmRemoveRole({{ $admin->id }}, 'super-admin', '{{ $admin->first_name }} {{ $admin->last_name }}', 'Super Admin')" 
                                        class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg text-[11px] font-bold transition cursor-pointer">
                                    Remove
                                </button>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">No Super Admins found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Welfare Admins List -->
            <div class="border border-slate-200 rounded-xl p-4 space-y-3 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600">Welfare Admins</h4>
                    <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded-full text-[10px] font-bold">{{ $welfareAdmins->count() }}</span>
                </div>
                <div class="space-y-2">
                    @forelse($welfareAdmins as $wAdmin)
                        <div class="bg-white p-3 rounded-lg border border-slate-200 flex items-center justify-between gap-2">
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ $wAdmin->first_name }} {{ $wAdmin->last_name }}</p>
                            </div>
                            @if(($isDeveloper || $isSuperAdmin) && $wAdmin->id !== $authUserId)
                                <button type="button" 
                                        onclick="confirmRemoveRole({{ $wAdmin->id }}, 'welfare-admin', '{{ $wAdmin->first_name }} {{ $wAdmin->last_name }}', 'Welfare Admin')" 
                                        class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg text-[11px] font-bold transition cursor-pointer">
                                    Remove
                                </button>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">No Welfare Admins found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Live Search Modal for Assigning Roles -->
    @if($showRoleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                        Assign Role: {{ ucwords(str_replace('-', ' ', $selectedRoleType)) }}
                    </h3>
                    <button wire:click="closeRoleModal" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 flex-1 overflow-y-auto">
                    <div class="space-y-1.5">
                        <label class="block font-bold uppercase tracking-wider text-slate-700 text-xs">Search Member (By Membership No, TSC No, or Name)</label>
                        <input type="text" wire:model.live.debounce.300ms="searchMember" placeholder="Type to search member..." class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                    </div>

                    <div class="space-y-2">
                        @if(strlen(trim($searchMember)) > 0)
                            @if(count($searchedMembers) > 0)
                                <div class="space-y-2 divide-y divide-slate-100">
                                    @foreach($searchedMembers as $member)
                                        @php
                                            $alreadyHasRole = $member->hasRole($selectedRoleType);
                                        @endphp
                                        <div class="pt-2.5 first:pt-0 flex items-center justify-between gap-3">
                                            <div>
                                                <p class="font-bold text-slate-900 text-xs">{{ $member->first_name ?? 'N/A' }} {{ $member->last_name ?? '' }}</p>
                                                <p class="text-slate-500 text-[11px]">School: {{ $member->school ?? 'N/A' }}</p>
                                                <p class="text-slate-400 font-mono text-[10px]">Membership No: {{ $member->membership_number ?? 'N/A' }} | TSC: {{ $member->tsc_number ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                @if($alreadyHasRole)
                                                    <span class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-xs font-bold cursor-not-allowed">Already Assigned</span>
                                                @else
                                                    <button type="button" wire:click="assignRoleToUser({{ $member->id }})" class="px-3.5 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition cursor-pointer">Add</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 text-center py-6">No matching members found.</p>
                            @endif
                        @else
                            <p class="text-xs text-slate-400 text-center py-6">Type above to search and assign roles to members...</p>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer with Cancel option -->
                <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button type="button" wire:click="closeRoleModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- SYSTEM SETTINGS CARDS GRID                 -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6">
        
        <!-- 1. Annual Registration Fee -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">Annual Registration Fee (Ksh)</p>
                <p class="text-xs text-slate-400">The yearly membership registration fee.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'registration_fee')
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model="registration_fee" class="w-36 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]" placeholder="Amount">
                        <button wire:click="updateSetting('registration_fee')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition cursor-pointer">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    </div>
                    @error('registration_fee') <span class="text-red-500 text-[11px] block">{{ $message }}</span> @enderror
                @else
                    <span class="text-xl font-extrabold" style="color: #0E3A59;">
                        @if($settings && $settings->registration_fee !== null)
                            Ksh {{ number_format($settings->registration_fee) }}
                        @else
                            <span class="text-slate-400 font-normal italic text-sm">Not Set</span>
                        @endif
                    </span>
                    <button wire:click="edit('registration_fee')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1 cursor-pointer">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 2. AGM Contribution Fee -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">AGM Contribution Fee (Ksh)</p>
                <p class="text-xs text-slate-400">The Annual General Meeting financial contribution expected per member.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'agm_contribution_fee')
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model="agm_contribution_fee" class="w-36 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]" placeholder="Amount">
                        <button wire:click="updateSetting('agm_contribution_fee')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition cursor-pointer">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    </div>
                    @error('agm_contribution_fee') <span class="text-red-500 text-[11px] block">{{ $message }}</span> @enderror
                @else
                    <span class="text-xl font-extrabold" style="color: #0E3A59;">
                        @if($settings && $settings->agm_contribution_fee !== null)
                            Ksh {{ number_format($settings->agm_contribution_fee) }}
                        @else
                            <span class="text-slate-400 font-normal italic text-sm">Not Set</span>
                        @endif
                    </span>
                    <button wire:click="edit('agm_contribution_fee')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1 cursor-pointer">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 3. Maximum Solidarity Fund Balance -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">Maximum Solidarity Fund Balance (Ksh)</p>
                <p class="text-xs text-slate-400">The maximum amount a member can hold in their solidarity fund wallet at any time.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'solidarity_max_balance')
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model="solidarity_max_balance" class="w-36 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]" placeholder="Amount">
                        <button wire:click="updateSetting('solidarity_max_balance')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition cursor-pointer">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    </div>
                    @error('solidarity_max_balance') <span class="text-red-500 text-[11px] block">{{ $message }}</span> @enderror
                @else
                    <span class="text-xl font-extrabold" style="color: #0E3A59;">
                        @if($settings && $settings->solidarity_max_balance !== null)
                            Ksh {{ number_format($settings->solidarity_max_balance) }}
                        @else
                            <span class="text-slate-400 font-normal italic text-sm">Not Set</span>
                        @endif
                    </span>
                    <button wire:click="edit('solidarity_max_balance')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1 cursor-pointer">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 4. Annual Registration Deadline -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">Annual Registration Deadline Date</p>
                <p class="text-xs text-slate-400">Deadline date by which annual subscriptions must be cleared.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'deadline')
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <!-- Month Dropdown -->
                            <select wire:model.live="registration_deadline_month" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>

                            <!-- Dependent Day Dropdown -->
                            <select wire:model="registration_deadline_day" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]">
                                @for($d = 1; $d <= $this->maxDays; $d++)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endfor
                            </select>

                            <button wire:click="updateSetting('deadline')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition cursor-pointer">Save</button>
                            <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                        </div>
                        @error('registration_deadline_month') <span class="text-red-500 text-[11px] block">{{ $message }}</span> @enderror
                        @error('registration_deadline_day') <span class="text-red-500 text-[11px] block">{{ $message }}</span> @enderror
                    </div>
                @else
                    <span class="text-xl font-extrabold" style="color: #0E3A59;">
                        @if($settings && $settings->registration_deadline_month && $settings->registration_deadline_day)
                            {{ date('F', mktime(0, 0, 0, $settings->registration_deadline_month, 10)) }} {{ $settings->registration_deadline_day }}
                        @else
                            <span class="text-slate-400 font-normal italic text-sm">Not Set</span>
                        @endif
                    </span>
                    <button wire:click="edit('deadline')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1 cursor-pointer">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 5. Late Registration Waiting Period -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">Late Registration Waiting Period (Days)</p>
                <p class="text-xs text-slate-400">Waiting duration before scheme benefits unlock if a member registers after the deadline.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'late_registration_waiting_period_days')
                    <div class="flex items-center gap-2">
                        <input type="number" min="0" wire:model="late_registration_waiting_period_days" class="w-28 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]" placeholder="Days">
                        <button wire:click="updateSetting('late_registration_waiting_period_days')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition cursor-pointer">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    </div>
                    @error('late_registration_waiting_period_days') <span class="text-red-500 text-[11px] block">{{ $message }}</span> @enderror
                @else
                    <span class="text-xl font-extrabold" style="color: #0E3A59;">
                        @if($settings && $settings->late_registration_waiting_period_days !== null)
                            {{ $settings->late_registration_waiting_period_days }} Days
                        @else
                            <span class="text-slate-400 font-normal italic text-sm">Not Set</span>
                        @endif
                    </span>
                    <button wire:click="edit('late_registration_waiting_period_days')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1 cursor-pointer">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 6. Defaulting Waiting Period -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">Defaulting Waiting Period (Days)</p>
                <p class="text-xs text-slate-400">Waiting period served by a defaulter after clearing all accumulated contribution arrears.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'defaulting_waiting_period_days')
                    <div class="flex items-center gap-2">
                        <input type="number" min="0" wire:model="defaulting_waiting_period_days" class="w-28 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]" placeholder="Days">
                        <button wire:click="updateSetting('defaulting_waiting_period_days')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition cursor-pointer">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    </div>
                    @error('defaulting_waiting_period_days') <span class="text-red-500 text-[11px] block">{{ $message }}</span> @enderror
                @else
                    <span class="text-xl font-extrabold" style="color: #0E3A59;">
                        @if($settings && $settings->defaulting_waiting_period_days !== null)
                            {{ $settings->defaulting_waiting_period_days }} Days
                        @else
                            <span class="text-slate-400 font-normal italic text-sm">Not Set</span>
                        @endif
                    </span>
                    <button wire:click="edit('defaulting_waiting_period_days')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1 cursor-pointer">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 7. Max Missed Benevolence Cases for Defaulting -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">Max Missed Benevolence Cases Before Defaulting</p>
                <p class="text-xs text-slate-400">Number of missed benevolence/bereavement contributions allowed before a member is marked as a defaulter.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'max_missed_benevolence_cases')
                    <div class="flex items-center gap-2">
                        <input type="number" min="1" wire:model="max_missed_benevolence_cases" class="w-28 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]" placeholder="Count">
                        <button wire:click="updateSetting('max_missed_benevolence_cases')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition cursor-pointer">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                    </div>
                    @error('max_missed_benevolence_cases') <span class="text-red-500 text-[11px] block">{{ $message }}</span> @enderror
                @else
                    <span class="text-xl font-extrabold" style="color: #0E3A59;">
                        @if($settings && $settings->max_missed_benevolence_cases !== null)
                            {{ $settings->max_missed_benevolence_cases }} {{ Str::plural('Case', $settings->max_missed_benevolence_cases) }}
                        @else
                            <span class="text-slate-400 font-normal italic text-sm">Not Set</span>
                        @endif
                    </span>
                    <button wire:click="edit('max_missed_benevolence_cases')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1 cursor-pointer">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

    </div>

</div>

@script
<script>
    // SweetAlert2 Confirmation for Role Removal
    window.confirmRemoveRole = function(userId, roleName, userName, roleLabel) {
        Swal.fire({
            title: 'Remove Privilege?',
            text: `Are you sure you want to remove ${roleLabel} privileges from ${userName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('removeRoleFromUser', userId, roleName);
            }
        });
    }

    // Success Listener
    window.addEventListener('swal:success', event => {
        Swal.fire({
            title: 'Success!',
            text: event.detail.message,
            icon: 'success',
            timer: 2500,
            showConfirmButton: false
        });
    });

    // Error Listener
    window.addEventListener('swal:error', event => {
        Swal.fire({
            title: 'Action Denied',
            text: event.detail.message,
            icon: 'error',
            confirmButtonColor: '#0E3A59'
        });
    });
</script>
@endscript