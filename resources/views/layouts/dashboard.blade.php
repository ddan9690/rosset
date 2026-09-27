<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ $title ?? 'Admin Dashboard | ROSSET-SWA' }}</title>
    
    <meta name="robots" content="noindex, nofollow">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery (Required for Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Toastr CDN (CSS & JS) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Partial -->
        @include('partials.dashboard.sidebar')

        <!-- Content Area -->
        <div class="flex flex-col flex-1 h-full overflow-hidden">
            
            <!-- Header Partial -->
            @include('partials.dashboard.header')

            <!-- Main Dashboard Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-6">
                {{ $slot }}
            </main>

            <!-- Footer Partial -->
            @include('partials.dashboard.footer')

        </div>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof createIcons !== 'undefined' && typeof icons !== 'undefined') {
                createIcons({ icons });
            }
        });
    </script>
</body>
</html>