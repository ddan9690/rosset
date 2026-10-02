<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Manage System Roles</h3>
            <p class="text-xs text-slate-500 mt-0.5">Control and assign administrative privileges across the portal.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:underline">&larr; Back to Dashboard</a>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-xs font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-xs font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- ================= SUPER ADMIN SECTION ================= -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900">Super Administrators</h4>
                <p class="text-[11px] text-slate-500">Users with full unrestricted platform access.</p>
            </div>
            <button type="button" wire:click="toggleSuperAdminSearch" class="px-3.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg hover:bg-slate-800 transition cursor-pointer">
                {{ $showSuperAdminSearch ? 'Close Search' : '+ Add Super Admin' }}
            </button>
        </div>

        @if($showSuperAdminSearch)
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-3 relative">
                <label class="block font-bold uppercase tracking-wider text-slate-700 text-[11px]">Search Member to Make Super Admin</label>
                <input type="text" wire:model.live.debounce.300ms="searchSuperAdmin" placeholder="Type member name, membership no, or TSC..." class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]">

                @if(count($searchedSuperUsers) > 0)
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm divide-y divide-slate-100 overflow-hidden">
                        @foreach($searchedSuperUsers as $user)
                            <div class="px-4 py-2.5 flex items-center justify-between text-xs hover:bg-slate-50">
                                <div>
                                    <span class="font-bold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</span>
                                    <span class="text-slate-500 font-mono text-[10px] ml-2">No: {{ $user->membership_number ?? 'N/A' }} | TSC: {{ $user->tsc_number ?? 'N/A' }}</span>
                                </div>
                                <button type="button" wire:click="addRole({{ $user->id }}, 'super admin')" class="px-3 py-1 bg-[#2EA3F2] text-white rounded font-bold hover:bg-sky-500 transition cursor-pointer">
                                    Assign
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2.5">Name</th>
                        <th class="px-4 py-2.5">Membership No</th>
                        <th class="px-4 py-2.5">Email / Phone</th>
                        <th class="px-4 py-2.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($superAdmins as $admin)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $admin->first_name }} {{ $admin->last_name }}</td>
                            <td class="px-4 py-3 font-mono text-slate-700">{{ $admin->membership_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span class="block text-slate-900">{{ $admin->email }}</span>
                                <span class="font-mono text-[10px] text-slate-500">{{ $admin->phone ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if(Auth::id() !== $admin->id)
                                    <button type="button" wire:click="removeRole({{ $admin->id }}, 'super admin')" wire:confirm="Are you sure you want to remove super admin rights?" class="text-red-600 font-semibold hover:underline cursor-pointer">
                                        Remove
                                    </button>
                                @else
                                    <span class="text-slate-400 italic">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-400 italic">No super administrators found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= WELFARE ADMIN SECTION ================= -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900">Welfare Administrators</h4>
                <p class="text-[11px] text-slate-500">Users who manage welfare funds, contributions, and benevolence cases.</p>
            </div>
            <button type="button" wire:click="toggleWelfareAdminSearch" class="px-3.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg hover:bg-slate-800 transition cursor-pointer">
                {{ $showWelfareAdminSearch ? 'Close Search' : '+ Add Welfare Admin' }}
            </button>
        </div>

        @if($showWelfareAdminSearch)
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-3 relative">
                <label class="block font-bold uppercase tracking-wider text-slate-700 text-[11px]">Search Member to Make Welfare Admin</label>
                <input type="text" wire:model.live.debounce.300ms="searchWelfareAdmin" placeholder="Type member name, membership no, or TSC..." class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#2EA3F2]">

                @if(count($searchedWelfareUsers) > 0)
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm divide-y divide-slate-100 overflow-hidden">
                        @foreach($searchedWelfareUsers as $user)
                            <div class="px-4 py-2.5 flex items-center justify-between text-xs hover:bg-slate-50">
                                <div>
                                    <span class="font-bold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</span>
                                    <span class="text-slate-500 font-mono text-[10px] ml-2">No: {{ $user->membership_number ?? 'N/A' }} | TSC: {{ $user->tsc_number ?? 'N/A' }}</span>
                                </div>
                                <button type="button" wire:click="addRole({{ $user->id }}, 'welfare admin')" class="px-3 py-1 bg-[#2EA3F2] text-white rounded font-bold hover:bg-sky-500 transition cursor-pointer">
                                    Assign
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2.5">Name</th>
                        <th class="px-4 py-2.5">Membership No</th>
                        <th class="px-4 py-2.5">Email / Phone</th>
                        <th class="px-4 py-2.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($welfareAdmins as $admin)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $admin->first_name }} {{ $admin->last_name }}</td>
                            <td class="px-4 py-3 font-mono text-slate-700">{{ $admin->membership_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span class="block text-slate-900">{{ $admin->email }}</span>
                                <span class="font-mono text-[10px] text-slate-500">{{ $admin->phone ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" wire:click="removeRole({{ $admin->id }}, 'welfare admin')" wire:confirm="Are you sure you want to remove welfare admin rights?" class="text-red-600 font-semibold hover:underline cursor-pointer">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-400 italic">No welfare administrators found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>