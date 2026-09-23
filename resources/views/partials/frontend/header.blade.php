<header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-xs" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        
        <!-- Brand / Logo & Full Name -->
        <a href="{{ url('/') }}" wire:navigate class="flex items-center space-x-3">
            <img src="{{ asset('images/logo.png') }}" alt="ROSSET-SWA Logo" class="h-11 w-auto object-contain">
            <div>
                <span class="font-extrabold text-xs sm:text-sm tracking-tight block leading-tight uppercase" style="color: #0E3A59;">Rongo Sub County Teachers Welfare Association</span>
                <span class="text-[11px] font-bold uppercase tracking-wider block" style="color: #2EA3F2;">ROSSET-SWA</span>
            </div>
        </a>
        
        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center space-x-8 font-medium text-sm">
            <a href="{{ url('/') }}" wire:navigate class="font-semibold transition" style="color: #0E3A59;">Home</a>
            <a href="{{ url('/') }}#about" class="text-slate-600 hover:text-[#0E3A59] transition">About Us</a>
            <a href="{{ url('/') }}#safe-haven" class="text-slate-600 hover:text-[#0E3A59] transition">Welfare Support</a>
            <a href="{{ url('/') }}#gallery" class="text-slate-600 hover:text-[#0E3A59] transition">Our Community</a>
            <a href="{{ url('/updates') }}" wire:navigate class="text-slate-600 hover:text-slate-900 transition">Updates</a>
        </nav>

        <!-- Desktop Action Buttons -->
        <div class="hidden md:flex items-center space-x-4">
            <a href="/login" wire:navigate class="text-sm font-semibold hover:underline" style="color: #0E3A59;">Member Portal</a>
            <a href="/register" wire:navigate class="text-white text-sm px-5 py-2.5 rounded font-semibold shadow-xs transition-colors hover:opacity-95" style="background-color: #0E3A59;">
                Join Association
            </a>
        </div>

        <!-- Mobile Menu Trigger -->
        <div class="flex md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-600 focus:outline-none">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="mobileMenuOpen" 
         class="md:hidden bg-white border-b border-slate-200 px-4 pt-3 pb-6 space-y-3 shadow-lg" 
         style="display: none;">
        <a href="{{ url('/') }}" wire:navigate class="block px-3 py-2 rounded font-medium text-white" style="background-color: #0E3A59;">Home</a>
        <a href="{{ url('/') }}#about" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded font-medium text-slate-700 hover:bg-slate-50">About Us</a>
        <a href="{{ url('/') }}#safe-haven" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded font-medium text-slate-700 hover:bg-slate-50">Welfare Support</a>
        <a href="{{ url('/') }}#gallery" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded font-medium text-slate-700 hover:bg-slate-50">Our Community</a>
        <a href="{{ url('/updates') }}" wire:navigate class="block px-3 py-2 rounded font-medium text-slate-700 hover:bg-slate-50">Updates</a>
        <div class="pt-4 border-t border-slate-100 flex flex-col space-y-2">
            <a href="/login" wire:navigate class="text-center w-full py-2.5 border font-semibold text-sm rounded" style="border-color: #0E3A59; color: #0E3A59;">Member Portal</a>
            <a href="/register" wire:navigate class="text-center w-full py-2.5 text-white rounded font-semibold text-sm shadow-xs" style="background-color: #0E3A59;">Join Association</a>
        </div>
    </div>
</header>