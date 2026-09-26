<div class="flex flex-col min-h-screen bg-slate-50">

    <!-- Header Banner -->
    <section class="text-white py-12 lg:py-16 bg-slate-900 relative overflow-hidden" style="background-color: #0E3A59;">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/Rosset Welfare team members sitting at a conference table reviewing documents during an official organization meeting..jpg') }}" 
                 alt="ROSSET-SWA Profile" 
                 class="w-full h-full object-cover filter brightness-40">
            <div class="absolute inset-0 bg-[#0E3A59]/90"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-sky-400/30 inline-block" style="background-color: rgba(46, 163, 242, 0.2); color: #2EA3F2;">
                Member Account Settings
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Complete Your <span style="color: #2EA3F2;">Profile Details</span>
            </h1>
            <p class="text-sm sm:text-base text-slate-200 max-w-xl mx-auto">
                Please provide your official information to finalize your membership record with ROSSET-SWA.
            </p>
        </div>
    </section>

    <!-- Main Form Section -->
    <section class="py-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex-grow">
        <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 sm:p-8 space-y-6">

            @if (session()->has('message'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <form wire:submit="updateProfile" class="space-y-6">
                
                <!-- Profile Picture Upload Section -->
                <div class="flex items-center space-x-6 pb-4 border-b border-slate-100">
                    <div class="relative w-20 h-20 rounded-full bg-slate-100 border-2 border-slate-200 overflow-hidden flex items-center justify-center text-slate-400 shadow-xs">
                        @if ($profile_picture)
                            <img src="{{ $profile_picture->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($existing_profile_picture)
                            <img src="{{ asset('storage/' . $existing_profile_picture) }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="user" class="w-10 h-10 text-slate-400"></i>
                        @endif

                        <!-- Camera Upload Overlay -->
                        <label for="profilePictureInput" class="absolute inset-0 bg-slate-900/40 hover:bg-slate-900/60 transition flex flex-col items-center justify-center text-white cursor-pointer opacity-90">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5">Change</span>
                        </label>
                        <input type="file" id="profilePictureInput" wire:model="profile_picture" class="hidden" accept="image/*">
                    </div>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Profile Picture</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Upload a professional passport or headshot (Max 2MB).</p>
                        @error('profile_picture') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Read-only / Disabled Credentials Info Box -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Membership No (SN)</label>
                        <input type="text" disabled wire:model="membership_number" class="w-full text-xs border border-slate-200 rounded-lg p-2 bg-slate-100 text-slate-500 font-mono cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">First Name (Locked)</label>
                        <input type="text" disabled wire:model="first_name" class="w-full text-xs border border-slate-200 rounded-lg p-2 bg-slate-100 text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Last Name (Locked)</label>
                        <input type="text" disabled wire:model="last_name" class="w-full text-xs border border-slate-200 rounded-lg p-2 bg-slate-100 text-slate-500 cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Salutation -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Salutation</label>
                        <select wire:model="salutation" class="w-full text-xs border border-slate-200 rounded-lg p-2.5 bg-white text-slate-700 focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden">
                            <option value="">Select Salutation</option>
                            <option value="Mr">Mr.</option>
                            <option value="Mrs">Mrs.</option>
                        </select>
                        @error('salutation') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Gender</label>
                        <select wire:model="gender" class="w-full text-xs border border-slate-200 rounded-lg p-2.5 bg-white text-slate-700 focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Phone Number (Safaricom) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Safaricom Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="phone" class="w-full text-xs border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden" placeholder="0712345678">
                        @error('phone') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email Address (Required) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="email" class="w-full text-xs border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden" placeholder="teacher@example.com">
                        @error('email') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- TSC Number -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">TSC Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="tsc_number" class="w-full text-xs border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden" placeholder="e.g. 1098234">
                        @error('tsc_number') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- ID Number -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">National ID Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="id_number" class="w-full text-xs border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden" placeholder="e.g. 28492019">
                        @error('id_number') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- School Level -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">School Level</label>
                        <select wire:model="school_level" class="w-full text-xs border border-slate-200 rounded-lg p-2.5 bg-white text-slate-700 focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden">
                            <option value="">Select Level</option>
                            <option value="Junior School">Junior School</option>
                            <option value="Senior School">Senior School</option>
                        </select>
                        @error('school_level') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- School Name -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">School Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="school" class="w-full text-xs border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden" placeholder="e.g. Rongo Secondary School">
                        @error('school') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4">
                    <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow-sm bg-slate-900 hover:bg-slate-800 cursor-pointer flex items-center justify-center space-x-2">
                        <span wire:loading.remove>Update Profile</span>
                        <span wire:loading>Saving Changes...</span>
                    </button>
                </div>

            </form>
        </div>
    </section>

</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        lucide.createIcons();
    });
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>