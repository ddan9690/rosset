<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
   
    <title>{{ $title ?? 'ROSSET-SWA | Rongo Sub County Secondary Teachers Social Welfare Association' }}</title>
    
    <!-- Core SEO Meta Tags -->
    <meta name="description" content="{{ $metaDescription ?? 'Official platform for the Rongu Sub County Secondary Teachers Social Welfare Association. Empowering secondary educators through mutual welfare support, financial transparency, and educational innovation.' }}">
    <meta name="keywords" content="ROSSET-SWA, Rongu Sub County secondary teachers, teachers social welfare association Kenya, secondary school educators welfare, teacher professional growth, educational innovation Kenya">
    <meta name="author" content="Rongu Sub County Secondary Teachers Social Welfare Association">
    <meta name="robots" content="index, follow">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'ROSSET-SWA | Rongu Sub County Secondary Teachers Social Welfare Association' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Empowering secondary educators in Rongu Sub County through secure welfare frameworks, mutual support, and progressive educational innovation.' }}">
    <meta property="og:image" content="{{ $metaImage ?? asset('images/Group portrait of Rosset Welfare team members.jpg') }}">
    <meta property="og:site_name" content="ROSSET-SWA">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $title ?? 'ROSSET-SWA | Rongu Sub County Secondary Teachers Social Welfare Association' }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Empowering secondary educators in Rongu Sub County through secure welfare frameworks, mutual support, and progressive educational innovation.' }}">
    <meta name="twitter:image" content="{{ $metaImage ?? asset('images/Group portrait of Rosset Welfare team members.jpg') }}">

    <!-- Favicons & Manifest -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rosset-light text-rosset-dark antialiased flex flex-col min-h-screen selection:bg-rosset-secondary selection:text-rosset-dark">

    <!-- Header Partial -->
    @include('partials.frontend.header')

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer Partial -->
    @include('partials.frontend.footer')

    <!-- Lucide & AOS Initialization Hook -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof createIcons !== 'undefined' && typeof icons !== 'undefined') {
                createIcons({ icons });
            }
        });
    </script>
</body>
</html>