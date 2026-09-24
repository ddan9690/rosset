<div class="max-w-lg w-full space-y-8 bg-white p-8 rounded-xl shadow-lg border border-slate-200 my-8">
    
    <!-- Header -->
    <div class="text-center space-y-2">
        <h1 class="text-xs uppercase tracking-widest font-bold text-slate-500">
            Rongo Sub County Teachers Welfare Association (ROSSET-SWA)
        </h1>
        <h2 class="text-2xl font-extrabold tracking-tight" style="color: #0E3A59;">
            Membership Registration
        </h2>
    </div>

    @if($step === 1)
        <!-- ================= STEP 1: LOOKUP EXISTING RECORD ================= -->
        <div>
            <form wire:submit="checkMember" class="mt-6 space-y-5">
                
                <div class="text-xs text-slate-600 text-center leading-relaxed">
                    Please enter your <strong>TSC Number</strong> to begin your registration.
                </div>

                <div>
                    <input type="text" wire:model="lookup_input" required placeholder="Enter TSC Number" class="w-full px-4 py-3.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none text-base bg-white shadow-xs" autofocus>
                    @error('lookup_input') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 px-4 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow hover:opacity-95 cursor-pointer flex items-center justify-center space-x-2" style="background-color: #0E3A59;">
                        <span wire:loading.remove wire:target="checkMember">Continue</span>
                        <span wire:loading wire:target="checkMember">Please wait, checking...</span>
                    </button>
                </div>

                <div class="text-center pt-2">
                    <p class="text-xs text-slate-600">
                        Already a member of ROSSET-SWA? 
                        <a href="/login" wire:navigate class="font-bold hover:underline" style="color: #2EA3F2;">Log in here</a>
                    </p>
                </div>
            </form>
        </div>
    @else
        <!-- ================= STEP 2: FULL REGISTRATION FORM ================= -->
        <div x-data="{ showPassword: false }">
            <form wire:submit="register" class="mt-6 space-y-4">
                
                <div class="text-xs text-slate-600 font-medium text-center pb-2">
                    Please fill out this form to register your account.
                </div>

                <!-- Salutation & Gender -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Salutation</label>
                        <select wire:model="salutation" class="w-full px-3 py-2 border border-slate-300 rounded text-sm bg-white">
                            <option value="Mr.">Mr.</option>
                            <option value="Mrs.">Mrs.</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Gender</label>
                        <select wire:model="gender" class="w-full px-3 py-2 border border-slate-300 rounded text-sm bg-white">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <!-- First Name & Last Name -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">First Name</label>
                        <input type="text" wire:model="first_name" required class="w-full px-3 py-2 border border-slate-300 rounded text-sm" placeholder="First Name">
                        @error('first_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Last Name</label>
                        <input type="text" wire:model="last_name" required class="w-full px-3 py-2 border border-slate-300 rounded text-sm" placeholder="Last Name">
                        @error('last_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- TSC Number & ID Number -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">TSC Number</label>
                        <input type="text" wire:model="tsc_number" readonly class="w-full px-3 py-2 border border-slate-300 bg-slate-100 rounded text-sm cursor-not-allowed text-slate-600 focus:outline-none">
                        @error('tsc_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">ID Number</label>
                        <input type="text" wire:model="id_number" required class="w-full px-3 py-2 border border-slate-300 rounded text-sm" placeholder="ID No.">
                        @error('id_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- School Level & School Name -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">School Level</label>
                        <select wire:model="school_level" class="w-full px-3 py-2 border border-slate-300 rounded text-sm bg-white">
                            <option value="Primary">Primary</option>
                            <option value="Junior">Junior</option>
                            <option value="Senior">Senior</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">School Name</label>
                        <input type="text" wire:model="school" required class="w-full px-3 py-2 border border-slate-300 rounded text-sm" placeholder="School Name">
                        @error('school') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Phone Number & Email -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Phone Number</label>
                        <input type="text" wire:model="phone" required class="w-full px-3 py-2 border border-slate-300 rounded text-sm" placeholder="07XXXXXXXX">
                        @error('phone') 
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            <span class="text-slate-600 text-[10px] mt-1 block bg-slate-50 border border-slate-200 rounded p-1.5 leading-relaxed">
                                <strong>Accepted Formats:</strong> 07XXXXXXXX · 01XXXXXXXX · 7XXXXXXXX · 1XXXXXXXX · 2547XXXXXXXX · 2541XXXXXXXX
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email</label>
                        <input type="email" wire:model="email" required class="w-full px-3 py-2 border border-slate-300 rounded text-sm" placeholder="email@example.com">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Password & Confirm Password -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" wire:model="password" required class="w-full px-3 py-2 pr-10 border border-slate-300 rounded text-sm" placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-500 hover:text-slate-700 text-xs font-bold cursor-pointer">
                                <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                            </button>
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Confirm Password</label>
                        <input type="password" wire:model="password_confirmation" required class="w-full px-3 py-2 border border-slate-300 rounded text-sm" placeholder="••••••••">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="pt-2 flex space-x-3">
                    <button type="button" wire:click="$set('step', 1)" class="w-1/3 py-3 px-2 border border-slate-300 rounded text-slate-600 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 cursor-pointer">
                        &larr; Back
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="w-2/3 py-3 px-4 rounded text-white font-bold text-xs uppercase tracking-wider transition shadow bg-red-600 hover:bg-red-700 cursor-pointer flex items-center justify-center space-x-2">
                        <span wire:loading.remove wire:target="register">Register</span>
                        <span wire:loading wire:target="register">Submitting...</span>
                    </button>
                </div>

            </form>
        </div>
    @endif

</div>