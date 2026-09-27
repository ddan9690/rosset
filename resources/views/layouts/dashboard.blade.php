<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ $title ?? 'Admin Dashboard | ROSSET-SWA' }}</title>
    
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicons & Manifest -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery (Required for Select2 & Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Toastr CDN (CSS & JS) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Select2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Select2 Tailwind Compatibility Styles -->
    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            padding: 6px 12px;
            border-radius: 0.375rem;
            border-color: #cbd5e1; /* slate-300 */
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            color: #334155; /* slate-700 */
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-dropdown {
            border-color: #cbd5e1;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
    </style>
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

    <!-- Select2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof createIcons !== 'undefined' && typeof icons !== 'undefined') {
                createIcons({ icons });
            }
        });
    </script>
</body>
</html>