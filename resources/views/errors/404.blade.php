<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | ROSSET-SWA</title>

    <!-- Favicons & Manifest -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-slate-50 flex items-center justify-center p-6 text-slate-800">
    <div class="max-w-md w-full bg-white border border-slate-200 rounded-2xl p-8 shadow-xs text-center space-y-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-50 text-[#2EA3F2] rounded-full font-bold text-2xl">
            404
        </div>
        <div class="space-y-2">
            <h1 class="font-bold text-lg text-slate-900 uppercase tracking-wider" style="color: #0E3A59;">Page Not Found</h1>
            <p class="text-xs text-slate-500 leading-relaxed">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        </div>
        <div>
            <a href="{{ url('/') }}" class="inline-block w-full py-2.5 px-4 bg-[#0E3A59] hover:bg-slate-900 text-white text-xs font-bold rounded-lg transition shadow-xs">
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>