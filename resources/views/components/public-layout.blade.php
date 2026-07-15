<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RuangBaca') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="font-semibold">RuangBaca</a>
                <a href="{{ route('catalog.index') }}" class="text-sm @if(request()->routeIs('catalog.*')) underline @endif">Katalog</a>
                @auth
                    <a href="{{ route('loans.index') }}" class="text-sm @if(request()->routeIs('loans.*')) underline @endif">Pinjaman</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm @if(request()->routeIs('admin.*')) underline @endif">Admin</a>
                    @endif
                @endauth
            </div>
            <div class="text-sm flex gap-3">
                @auth
                    <a href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="underline">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="underline">Masuk</a>
                    <a href="{{ route('register') }}" class="underline">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>
</body>
</html>
