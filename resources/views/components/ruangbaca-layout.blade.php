<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'RuangBaca') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Newsreader:opsz,ital,wght@6..72,0,400;6..72,0,500;6..72,1,400;6..72,1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <x-site-nav />

    @isset($header)
        <header class="border-b border-navy/10 bg-onionskin/80">
            <div class="rb-container py-5">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t border-navy/10">
        <div class="rb-container flex flex-col gap-2 py-8 text-sm text-charcoal-muted sm:flex-row sm:items-center sm:justify-between">
            <p class="font-display italic text-navy">RuangBaca</p>
            <p class="font-mono text-utility uppercase">Katalog digital · tanpa antre</p>
        </div>
    </footer>
</body>
</html>
