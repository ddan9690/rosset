<footer class="text-white pt-16 pb-8 border-t border-slate-800" style="background-color: #0E3A59;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
        <div class="space-y-4">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.png') }}" alt="ROSSET-SWA Logo" class="h-9 w-auto object-contain bg-white/10 p-1 rounded">
                <span class="font-bold text-base tracking-wide text-white">ROSSET-SWA</span>
            </div>
            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                A dependable mutual welfare safety net uniting secondary school educators across Rongo Sub County in brotherhood, sisterhood, and shared support.
            </p>
        </div>
        <div>
            <h4 class="font-semibold text-sm mb-4 tracking-wider uppercase" style="color: #2EA3F2;">Quick Links</h4>
            <ul class="space-y-2.5 text-sm text-slate-300">
                <li><a href="{{ route('home') }}" wire:navigate class="hover:text-white transition">Home</a></li>
                <li><a href="#about" class="hover:text-white transition">Who We Are</a></li>
                <li><a href="#support" class="hover:text-white transition">Standing Together</a></li>
                <li><a href="{{ route('updates') }}" wire:navigate class="hover:text-white transition">Notices & Updates</a></li>
                <li><a href="{{ route('privacy-policy') }}" wire:navigate class="hover:text-white transition">Privacy Policy</a></li>
                <li><a href="{{ route('terms-and-conditions') }}" wire:navigate class="hover:text-white transition">Terms & Conditions</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold text-sm mb-4 tracking-wider uppercase" style="color: #2EA3F2;">Member Services</h4>
            <ul class="space-y-2.5 text-sm text-slate-300">
                <li><a href="{{ route('login') }}" wire:navigate class="hover:text-white transition">Member Portal Login</a></li>
                <li><a href="{{ route('register') }}" wire:navigate class="hover:text-white transition">Join Hands With Us</a></li>
                <li><a href="#support" class="hover:text-white transition">Welfare Benefits</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold text-sm mb-4 tracking-wider uppercase" style="color: #2EA3F2;">Sub County Secretariat</h4>
            <p class="text-sm text-slate-300 leading-relaxed mb-2">
                Rongo Sub County, Migori County, Kenya.
            </p>
            <p class="text-sm text-slate-300">
                Email: <span class="text-white font-medium">info@rossetsn.org</span>
            </p>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-700/60 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} Rongo Sub County Secondary Teachers Social Welfare Association (ROSSET-SWA). All rights reserved.</p>
        <p class="mt-2 sm:mt-0 font-medium text-slate-300">Unity • Transparency • Mutual Care</p>
    </div>
</footer>