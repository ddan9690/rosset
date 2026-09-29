<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white px-5 py-4 rounded-xl shadow-xs border border-slate-200 flex items-center justify-between">
        <div>
            <h1 class="text-sm font-bold uppercase tracking-wider text-slate-800">Add New Member</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Register a new member into the system</p>
        </div>
        <a href="{{ route('admin.members') }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
            &larr; Back to Directory
        </a>
    </div>

    <!-- Form Card -->
    <form wire:submit="save" class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-4 text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Membership Number -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">Membership Number</label>
                <input type="text" wire:model="membership_number" placeholder="e.g. MEM-001" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('membership_number') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                <input type="email" wire:model="email" placeholder="john@example.com" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('email') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- First Name -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">First Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model="first_name" placeholder="John" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('first_name') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Last Name -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model="last_name" placeholder="Doe" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('last_name') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                <input type="text" wire:model="phone" placeholder="0712345678" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('phone') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Gender -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">Gender <span class="text-red-500">*</span></label>
                <select wire:model="gender" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900 bg-white">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                @error('gender') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- ID Number -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">ID Number</label>
                <input type="text" wire:model="id_number" placeholder="National ID" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('id_number') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- TSC Number -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">TSC Number</label>
                <input type="text" wire:model="tsc_number" placeholder="TSC Number" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('tsc_number') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- School -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">School / Institution</label>
                <input type="text" wire:model="school" placeholder="School name" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('school') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- School Level -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">School Level</label>
                <input type="text" wire:model="school_level" placeholder="Primary / Secondary / Tertiary" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900">
                @error('school_level') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">Account Status</label>
                <select wire:model="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900 bg-white">
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
                @error('status') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Registration Fee Paid -->
            <div>
                <label class="block font-medium text-slate-700 mb-1">Registration Fee Status</label>
                <div class="flex items-center space-x-3 pt-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" wire:model="registration_fee_paid" class="rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                        <span>Mark Registration Fee as Paid</span>
                    </label>
                </div>
                @error('registration_fee_paid') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="sm:col-span-2">
                <label class="block font-medium text-slate-700 mb-1">Temporary Password <span class="text-red-500">*</span></label>
                <input type="text" wire:model="password" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-slate-900 font-mono">
                @error('password') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.members') }}" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-slate-900 text-white rounded-lg font-semibold hover:bg-slate-800 transition">Save Member</button>
        </div>
    </form>
</div>