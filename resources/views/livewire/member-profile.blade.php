<div class="min-h-screen bg-slate-50 py-8" x-data="{ photoPreview: null }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Navigation / Back Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            <a href="{{ route('portal') }}" wire:navigate
                class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                <span>&larr; Back to Portal</span>
            </a>

            <div class="text-xs font-mono font-semibold text-slate-500 uppercase tracking-wider">
                ROSSET-SWA Member Profile
            </div>
        </div>

        <!-- Success Flash Message -->
        @if(session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold flex items-center space-x-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Profile Update Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs space-y-6">
            
            <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-extrabold text-slate-900">Member Profile Settings</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Update your personal contact details and professional school information.</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Status: {{ $status }}
                </span>
            </div>

            <form wire:submit="updateProfile" class="space-y-6">

                <!-- Profile Picture Section -->
                <div class="flex items-center space-x-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="shrink-0">
                        <!-- Preview or Existing Picture -->
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-16 h-16 object-cover rounded-full border-2 border-slate-300 shadow-sm">
                        </template>
                        <template x-if="!photoPreview">
                            @if($existing_profile_picture)
                                <img src="{{ asset('storage/' . $existing_profile_picture) }}" alt="Profile" class="w-16 h-16 object-cover rounded-full border-2 border-slate-300 shadow-sm">
                            @else
                                <div class="w-16 h-16 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1)) }}
                                </div>
                            @endif
                        </template>
                    </div>

                    <div class="flex-1 space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Profile Picture</label>
                        <input type="file" 
                               wire:model="profile_picture" 
                               accept="image/*"
                               @change="
                                   const file = $event.target.files[0];
                                   if (file) {
                                       photoPreview = URL.createObjectURL(file);
                                   }
                               "
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-white file:text-slate-700 hover:file:bg-slate-100 cursor-pointer border border-slate-300 rounded">
                        @error('profile_picture') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        <p class="text-[11px] text-slate-400">PNG, JPG, or GIF up to 2MB.</p>
                    </div>
                </div>

                <!-- Disabled / Read-Only Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Salutation</label>
                        <input type="text" value="{{ $salutation }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-xs text-slate-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">First Name</label>
                        <input type="text" value="{{ $first_name }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-xs text-slate-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Last Name</label>
                        <input type="text" value="{{ $last_name }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-xs text-slate-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Gender</label>
                        <input type="text" value="{{ ucfirst($gender) }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-xs text-slate-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Membership Number</label>
                        <input type="text" value="{{ $membership_number }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-xs font-mono text-slate-500 cursor-not-allowed font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">TSC Number</label>
                        <input type="text" value="{{ $tsc_number }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-xs font-mono text-slate-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">ID Number</label>
                        <input type="text" value="{{ $id_number }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-xs font-mono text-slate-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Date Joined</label>
                        <input type="text" value="{{ $joined_at }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-xs font-mono text-slate-500 cursor-not-allowed">
                    </div>
                </div>

                <hr class="border-slate-100 my-4">

                <!-- Editable Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Phone Number</label>
                        <input type="text" wire:model="phone" required class="w-full px-3 py-2.5 border border-slate-300 rounded text-xs font-mono focus:ring-2 focus:ring-[#0E3A59] focus:outline-none">
                        @error('phone') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Address</label>
                        <input type="email" wire:model="email" required class="w-full px-3 py-2.5 border border-slate-300 rounded text-xs focus:ring-2 focus:ring-[#0E3A59] focus:outline-none">
                        @error('email') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">School Level</label>
                        <select wire:model="school_level" class="w-full px-3 py-2.5 border border-slate-300 rounded text-xs bg-white focus:ring-2 focus:ring-[#0E3A59] focus:outline-none cursor-pointer">
                            <option value="Primary">Primary</option>
                            <option value="Junior">Junior</option>
                            <option value="Senior">Senior</option>
                        </select>
                        @error('school_level') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">School Name</label>
                        <input type="text" wire:model="school" required class="w-full px-3 py-2.5 border border-slate-300 rounded text-xs focus:ring-2 focus:ring-[#0E3A59] focus:outline-none">
                        @error('school') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="pt-4 flex items-center justify-end space-x-3">
                    <a href="{{ route('portal') }}" wire:navigate class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                        &larr; Back to Portal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" class="px-6 py-3.5 bg-[#0E3A59] hover:bg-opacity-90 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-md flex items-center space-x-2 cursor-pointer">
                        <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                        <span wire:loading wire:target="updateProfile">Saving...</span>
                    </button>
                </div>

            </form>

        </div>

    </div>
</div>