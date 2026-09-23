<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Member Portal | ROSSET-SWA' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    {{ $slot }}

    @livewireScripts
</body>
</html>