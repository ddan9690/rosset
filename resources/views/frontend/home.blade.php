<x-app-layout 
    title="ROSSET-SWA | Rongu Sub County Secondary Teachers Social Welfare Association"
    metaDescription="Official home platform for the Rongu Sub County Secondary Teachers Social Welfare Association. Empowering secondary educators through mutual welfare support, financial transparency via KCB paybill, and educational innovation."
>
    <!-- Hero Section with Team Portrait -->
    @include('partials.frontend.hero')

    <!-- About Section -->
    @include('partials.frontend.about')

    <!-- Core Pillars Section -->
    @include('partials.frontend.pillars')

    <!-- Additional Community Engagement Section / Call to Action -->
    <section class="py-20 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-rosset-primary to-slate-900 rounded-3xl p-8 sm:p-12 lg:p-16 text-white shadow-2xl relative overflow-hidden flex flex-col lg:flex-row items-center justify-between gap-8">
                
                <!-- Background Decoration Element -->
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-rosset-secondary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="space-y-4 max-w-2xl relative z-10 text-center lg:text-left" data-aos="fade-right">
                    <span class="bg-rosset-secondary/20 text-rosset-secondary text-xs font-bold uppercase tracking-widest px-3.5 py-1.5 rounded-full inline-block">
                        Join Your Colleagues
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                        Strengthen Your Welfare & Professional Network Today
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        Log in to your member account to review contribution statements, track KCB paybill updates, and connect with secondary school educators across Rongu Sub County.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 relative z-10 w-full lg:w-auto flex-shrink-0" data-aos="fade-left">
                    <a href="/login" class="bg-rosset-secondary hover:bg-sky-400 text-rosset-dark font-bold px-8 py-4 rounded-xl text-center shadow-lg transition-all">
                        Member Login
                    </a>
                    <a href="/register" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold px-8 py-4 rounded-xl text-center transition-all">
                        Register Account
                    </a>
                </div>

            </div>
        </div>
    </section>

</x-app-layout>