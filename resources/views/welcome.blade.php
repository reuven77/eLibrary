<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RuangBaca') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-site-nav />
    <div class="rb-container py-16 text-center">
        <h1 class="font-display text-4xl italic text-navy">RuangBaca</h1>
        <p class="mt-3 text-charcoal-muted">Ruang baca, tanpa antre.</p>
        <a href="{{ route('home') }}" class="rb-btn-primary mt-8 inline-flex">Masuk ke beranda</a>
    </div>
</body>
</html>
