<!-- Mobile backdrop -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/50 md:hidden"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto" style="background-color: #0E3A59;">
    
    <!-- Sidebar Brand -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center space-x-2 text-white font-extrabold tracking-wider text-xs">
            <span class="w-3 h-3 rounded-full" style="background-color: #2EA3F2;"></span>
            <span>ROSSET-SWA ADMIN</span>
        </a>
        <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white md:hidden">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto text-sm font-medium">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center px-4 py-3 rounded-lg text-white transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#2EA3F2]/25 border-l-4 border-[#2EA3F2]' : 'hover:bg-white/5 text-slate-300' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 text-[#2EA3F2]"></i>
            Dashboard
        </a>

        <a href="#" class="flex items-center px-4 py-3 rounded-lg transition hover:bg-white/5 text-slate-300">
            <i data-lucide="users" class="w-5 h-5 mr-3 text-[#2EA3F2]"></i>
            Members Directory
        </a>

        <a href="{{ route('admin.members.onboard') }}" wire:navigate class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.members.onboard') ? 'bg-[#2EA3F2]/25 border-l-4 border-[#2EA3F2]' : 'hover:bg-white/5 text-slate-300' }}">
            <i data-lucide="user-plus" class="w-5 h-5 mr-3 text-[#2EA3F2]"></i>
            Onboard Members
        </a>

        <!-- Benevolence Categories Link -->
        <a href="{{ route('admin.benevolence.categories') }}" wire:navigate class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.benevolence.categories') ? 'bg-[#2EA3F2]/25 border-l-4 border-[#2EA3F2]' : 'hover:bg-white/5 text-slate-300' }}">
            <i data-lucide="folder-kanban" class="w-5 h-5 mr-3 text-[#2EA3F2]"></i>
            Benevolence Categories
        </a>

        <!-- Benevolence Cases Link -->
        <a href="{{ route('admin.benevolence.cases.index') }}" wire:navigate class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.benevolence.cases*') ? 'bg-[#2EA3F2]/25 border-l-4 border-[#2EA3F2]' : 'hover:bg-white/5 text-slate-300' }}">
            <i data-lucide="file-text" class="w-5 h-5 mr-3 text-[#2EA3F2]"></i>
            Benevolence Cases
        </a>

        <!-- Settings Link -->
        <a href="{{ route('admin.settings') }}" wire:navigate class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.settings') ? 'bg-[#2EA3F2]/25 border-l-4 border-[#2EA3F2]' : 'hover:bg-white/5 text-slate-300' }}">
            <i data-lucide="settings" class="w-5 h-5 mr-3 text-[#2EA3F2]"></i>
            Settings
        </a>
    </nav>

    <!-- Sidebar Footer / Portal Switch -->
    <div class="p-4 border-t border-slate-800 space-y-3">
        <a href="/" target="_blank" class="flex items-center justify-center w-full px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider text-[#0E3A59] transition hover:opacity-95" style="background-color: #2EA3F2;">
            <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
            Visit Website
        </a>
    </div>
</aside>