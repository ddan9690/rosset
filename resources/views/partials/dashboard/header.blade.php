<header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-200 shadow-xs z-10">
    
    <!-- Left: Mobile Toggle & Title -->
    <div class="flex items-center space-x-4">
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-600 hover:text-slate-900 md:hidden focus:outline-none">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <h1 class="text-lg font-bold" style="color: #0E3A59;">
            {{ $headerTitle ?? 'ROSSET-SWA - DASHBOARD' }}
        </h1>
    </div>

    <!-- Right: User Icon with Alpine Dropdown -->
    <div class="relative" x-data="{ profileDropdown: false }">
        <button @click="profileDropdown = !profileDropdown" @click.outside="profileDropdown = false" class="flex items-center space-x-3 focus:outline-none group">
            <div class="text-right hidden sm:block">
                <div class="text-xs font-bold text-slate-700 group-hover:text-[#2EA3F2] transition">{{ Auth::user()->first_name ?? 'Administrator' }}</div>
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Super Admin</div>
            </div>
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shadow-sm transition group-hover:opacity-90" style="background-color: #0E3A59;">
                <i data-lucide="user" class="w-5 h-5 text-[#2EA3F2]"></i>
            </div>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="profileDropdown" 
             x-transition:enter="transition ease-out duration-100" 
             x-transition:enter-start="transform opacity-0 scale-95" 
             x-transition:enter-end="transform opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-75" 
             x-transition:leave-start="transform opacity-100 scale-100" 
             x-transition:leave-end="transform opacity-0 scale-95" 
             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50" style="display: none;">
            
            <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                <p class="text-xs font-bold text-slate-800">{{ Auth::user()->first_name ?? 'Administrator' }}</p>
                <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Super Admin</p>
            </div>

            <a href="#" class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                <i data-lucide="user-pen" class="w-4 h-4 mr-2.5 text-slate-400"></i>
                Update Profile
            </a>

            <a href="#" class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                <i data-lucide="key-round" class="w-4 h-4 mr-2.5 text-slate-400"></i>
                Change Password
            </a>

            <div class="border-t border-slate-100 my-1"></div>

            <button type="button" @click="Swal.fire({
                title: 'Are you sure?',
                text: 'You will be logged out of your session.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0E3A59',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log out!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })" class="w-full flex items-center px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 transition cursor-pointer">
                <i data-lucide="log-out" class="w-4 h-4 mr-2.5"></i>
                Logout
            </button>
        </div>
    </div>
</header>

<!-- Hidden Logout Form for SweetAlert -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>