<div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg border border-slate-200">
        
    <!-- Header & Logo -->
    <div class="text-center space-y-3">
        <a href="{{ url('/') }}" wire:navigate class="inline-block p-2 bg-slate-50 border border-slate-100 rounded-xl shadow-xs">
            <img src="{{ asset('images/logo.png') }}" alt="ROSSET Logo" class="h-20 w-auto mx-auto object-contain">
        </a>
        <h2 class="text-2xl font-extrabold tracking-tight" style="color: #0E3A59;">
            Member Portal Login
        </h2>
        <p class="text-xs text-slate-500 font-medium">
            Rongo Sub County Teachers Welfare Association
        </p>
    </div>

    <!-- Login Form -->
    <form wire:submit.prevent="authenticate" class="mt-6 space-y-5">
        
        <!-- Error Message Displayed at the Top -->
        @if($errorMessage)
            <div class="p-3 bg-red-50 border border-red-200 text-red-600 text-xs rounded font-medium text-center">
                {{ $errorMessage }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 bg-red-50 border border-red-200 text-red-600 text-xs rounded font-medium text-center">
                {{ session('error') }}
            </div>
        @endif

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">TSC Number or ID Number</label>
            <input type="text" wire:model="login" required class="w-full px-3 py-2.5 border border-slate-300 rounded focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none text-sm" placeholder="Enter TSC or ID number">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Password</label>
            <input type="password" wire:model="password" required class="w-full px-3 py-2.5 border border-slate-300 rounded focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none text-sm" placeholder="••••••••">
        </div>

        <div>
            <button type="submit" class="w-full py-3 px-4 rounded text-white font-bold text-xs uppercase tracking-wider transition shadow hover:opacity-95 cursor-pointer" style="background-color: #0E3A59;">
                Log In
            </button>
        </div>

        <div class="text-center pt-2 space-y-2">
            <p class="text-xs text-slate-600">
                Want to join ROSSET-SWA? 
                <a href="/register" wire:navigate class="font-bold hover:underline" style="color: #2EA3F2;">Register here</a>
            </p>
            <p>
                <a href="{{ url('/') }}" wire:navigate class="text-xs text-slate-500 hover:text-slate-900">&larr; Return to Homepage</a>
            </p>
        </div>

    </form>

</div>