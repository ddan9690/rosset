<div class="flex flex-col min-h-screen">

    <!-- Hero Section -->
    @include('partials.frontend.hero')

    <!-- Scrolling Ticker -->
    @include('partials.frontend.ticker')

    <!-- About Section -->
    @include('partials.frontend.about')

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
                    <a href="/register" class="font-bold px-8 py-4 rounded-lg text-center transition-all shadow-lg text-base hover:opacity-95" style="background-color: #2EA3F2; color: #0E3A59;">
                        Register Membership
                    </a>
                    <a href="/login" class="border border-slate-400 hover:bg-white/10 text-white font-semibold px-8 py-4 rounded-lg text-center transition-all text-base">
                        Member Portal Login
                    </a>
                </div>

            </div>
        </div>
    </section>

</div>