<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold" style="color: #0E3A59;">System Settings</h2>
            
        </div>
    </div>

    <!-- Flash Notification Success Message -->
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Inline Editing Settings Grid -->
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
                        <button wire:click="updateSetting('registration_fee')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition">Cancel</button>
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
                    <button wire:click="edit('registration_fee')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1">
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
                        <button wire:click="updateSetting('agm_contribution_fee')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition">Cancel</button>
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
                    <button wire:click="edit('agm_contribution_fee')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 3. Annual Registration Deadline (Month & Day Selectors) -->
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

                            <button wire:click="updateSetting('deadline')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition">Save</button>
                            <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition">Cancel</button>
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
                    <button wire:click="edit('deadline')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 4. Late Registration Waiting Period -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">Late Registration Waiting Period (Days)</p>
                <p class="text-xs text-slate-400">Waiting duration before scheme benefits unlock if a member registers after the deadline.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'late_registration_waiting_period_days')
                    <div class="flex items-center gap-2">
                        <input type="number" min="0" wire:model="late_registration_waiting_period_days" class="w-28 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]" placeholder="Days">
                        <button wire:click="updateSetting('late_registration_waiting_period_days')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition">Cancel</button>
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
                    <button wire:click="edit('late_registration_waiting_period_days')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

        <!-- 5. Defaulting Waiting Period -->
        <div class="bg-white p-5 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-700">Defaulting Waiting Period (Days)</p>
                <p class="text-xs text-slate-400">Waiting period served by a defaulter after clearing all accumulated contribution arrears.</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                @if($editingField === 'defaulting_waiting_period_days')
                    <div class="flex items-center gap-2">
                        <input type="number" min="0" wire:model="defaulting_waiting_period_days" class="w-28 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]" placeholder="Days">
                        <button wire:click="updateSetting('defaulting_waiting_period_days')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition">Save</button>
                        <button wire:click="cancelEdit" class="px-3 py-1.5 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition">Cancel</button>
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
                    <button wire:click="edit('defaulting_waiting_period_days')" class="text-xs font-bold text-[#2EA3F2] hover:underline flex items-center gap-1">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Update
                    </button>
                @endif
            </div>
        </div>

    </div>

</div>