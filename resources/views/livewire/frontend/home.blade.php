<div class="flex flex-col min-h-screen">

    <!-- Hero Section -->
    @include('partials.frontend.hero')

    <!-- Scrolling Ticker -->
    @include('partials.frontend.ticker')

    <!-- About Section -->
    <section class="py-20 bg-white border-b border-slate-200" id="about">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest block mb-2" style="color: #2EA3F2;">Who We Are</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight" style="color: #0E3A59;">
                    Teachers Supporting Teachers Across Rongo Sub County
                </h2>
                <p class="text-slate-600 mt-3 text-base sm:text-lg leading-relaxed">
                    ROSSET-SWA was born from a simple belief: when a colleague faces the heavy burden of bereavement or life-shaping challenges, no one should walk that path alone.
                </p>
            </div>

            <div class="max-w-4xl mx-auto space-y-6 text-center">
                <h3 class="text-2xl font-bold tracking-tight" style="color: #0E3A59;">
                    United by Brotherhood, Sisterhood & Mutual Care
                </h3>
                <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                    Day in and day out, we mold the future inside our classrooms. But beyond the chalkboard, we are mothers, fathers, brothers, and sisters to one another. By pooling our resources together, we ensure that when loss strikes in our families, we stand shoulder-to-shoulder with practical financial and moral support.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 text-left">
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background-color: rgba(46, 163, 242, 0.15); color: #2EA3F2;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="font-bold mb-2 text-slate-900">Standing Together in Sorrow</h4>
                        <p class="text-sm text-slate-600">Walking shoulder-to-shoulder with colleagues and families during times of bereavement.</p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background-color: rgba(46, 163, 242, 0.15); color: #2EA3F2;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="font-bold mb-2 text-slate-900">Shared Brotherhood & Sisterhood</h4>
                        <p class="text-sm text-slate-600">Fostering genuine bonds of care and mutual support among educators across Rongo.</p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background-color: rgba(46, 163, 242, 0.15); color: #2EA3F2;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="font-bold mb-2 text-slate-900">Open & Accountable Support</h4>
                        <p class="text-sm text-slate-600">Reliable welfare built on trust, openness, and collective responsibility.</p>
                    </div>
                </div>

                <div class="pt-6">
                    <a href="/register" wire:navigate class="inline-flex items-center space-x-2 text-sm font-bold px-8 py-4 rounded-lg shadow-sm hover:opacity-95 transition" style="background-color: #2EA3F2; color: #0E3A59;">
                        <span>Join Hands With Us</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- Safe Haven / Pillars Section -->
    @include('partials.frontend.pillars')

    <!-- Community Photo Showcase Section -->
    <section class="py-20 bg-white border-b border-slate-200" id="gallery">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest block mb-2" style="color: #2EA3F2;">Our Teachers in Action</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight" style="color: #0E3A59;">
                    United Across Rongo & Migori County
                </h2>
                <p class="text-slate-600 mt-3 text-base sm:text-lg">
                    A glimpse into our gatherings, team coordination meetings, and community welfare programs.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="overflow-hidden rounded-xl shadow-md aspect-[4/3] bg-slate-100">
                    <img src="{{ asset('images/Rosset Welfare c posing for a photo.jpg') }}" alt="ROSSET-SWA Teachers" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                </div>
                <div class="overflow-hidden rounded-xl shadow-md aspect-[4/3] bg-slate-100">
                    <img src="{{ asset('images/Rosset Welfare representatives discussing community support programs in front of a yellow school bus..jpg') }}" alt="Community Support" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                </div>
                <div class="overflow-hidden rounded-xl shadow-md aspect-[4/3] bg-slate-100">
                    <img src="{{ asset('images/Rosset Welfare staff setting up outdoor event.jpg') }}" alt="Event Setup" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                </div>
                <div class="overflow-hidden rounded-xl shadow-md aspect-[4/3] bg-slate-100">
                    <img src="{{ asset('images/Rosset Welfare team members sitting at a conference table reviewing documents during an official organization meeting..jpg') }}" alt="Official Meeting" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                </div>
            </div>
        </div>
    </section>

    <!-- Final Prominent Call to Action Section -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl p-8 sm:p-14 lg:p-16 text-white shadow-xl flex flex-col lg:flex-row items-center justify-between gap-8 border border-slate-800" style="background-color: #0E3A59;">
                
                <div class="space-y-4 max-w-2xl text-center lg:text-left">
                    <span class="text-xs font-bold uppercase tracking-widest px-3.5 py-1.5 rounded-full inline-block" style="background-color: rgba(46, 163, 242, 0.2); color: #2EA3F2;">
                        You Are Not Alone
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                        Join Migori County's Premier Teachers Welfare Association
                    </h2>
                    <p class="text-slate-300 text-base sm:text-lg leading-relaxed">
                        Whether you teach in junior school or senior secondary school, register your membership with ROSSET-SWA today and experience true community support, resource pooling, and peace of mind.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto flex-shrink-0">
                    <a href="/register" wire:navigate class="font-bold px-8 py-4 rounded-lg text-center transition-all shadow-lg text-base hover:opacity-95" style="background-color: #2EA3F2; color: #0E3A59;">
                        Register Membership
                    </a>
                    <a href="/login" wire:navigate class="border border-slate-400 hover:bg-white/10 text-white font-semibold px-8 py-4 rounded-lg text-center transition-all text-base">
                        Member Portal Login
                    </a>
                </div>

            </div>
        </div>
    </section>

</div>