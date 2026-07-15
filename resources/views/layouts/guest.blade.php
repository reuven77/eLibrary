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
    <div class="rb-auth-shell">
        <div class="grid min-h-screen lg:grid-cols-2">
            {{-- Panel dekoratif --}}
            <aside class="rb-auth-panel p-10 xl:p-14">
                <div class="rb-auth-orb h-40 w-40 -left-10 top-16" aria-hidden="true"></div>
                <div class="rb-auth-orb h-56 w-56 bottom-20 -right-8" style="animation-delay: 1.5s;" aria-hidden="true"></div>

                <div class="relative z-10">
                    <a href="{{ route('home') }}" class="font-display text-3xl italic text-onionskin">RuangBaca</a>
                    <p class="mt-3 max-w-sm font-mono text-utility uppercase tracking-[0.06em] text-brass/90">
                        027 · Sirkulasi anggota
                    </p>
                </div>

                <div class="relative z-10 my-auto py-16" aria-hidden="true">
                    <div class="relative h-64 max-w-sm">
                        <div class="rb-spine rb-float left-4 top-4 bg-rust" style="transform: rotate(-14deg); height: 14rem;">
                            <span>Katalog · 025</span>
                        </div>
                        <div class="rb-spine rb-float-delay left-20 top-0 bg-forest" style="transform: rotate(-5deg); height: 15rem;">
                            <span>Baca · 028</span>
                        </div>
                        <div class="rb-spine rb-float left-36 top-6 bg-brass" style="transform: rotate(6deg); height: 13.5rem;">
                            <span>Pinjam · 027</span>
                        </div>
                        <div class="rb-stamp-drift absolute bottom-2 right-2 rounded-[100%] border-2 border-brass/80 bg-navy/70 px-4 py-2 font-mono text-[11px] uppercase tracking-[0.1em] text-brass">
                            ● Anggota
                        </div>
                    </div>
                    <p class="mt-8 max-w-sm font-display text-2xl italic leading-snug text-onionskin/90">
                        Kartu katalog di saku digitalmu.
                    </p>
                    <p class="mt-3 max-w-sm text-sm leading-relaxed text-onionskin/60">
                        Satu akun untuk mencari, mengajukan pinjaman, dan membaca e-book — tanpa antre di loket.
                    </p>
                </div>

                <p class="relative z-10 font-mono text-[11px] uppercase tracking-[0.08em] text-onionskin/40">
                    Onionskin · Brass · Navy binding
                </p>
            </aside>

            {{-- Form --}}
            <div class="relative flex flex-col">
                <div class="flex items-center justify-between border-b border-navy/10 px-6 py-4 lg:px-10">
                    <a href="{{ route('home') }}" class="font-display text-xl italic text-navy lg:hidden">RuangBaca</a>
                    <nav class="ml-auto flex gap-4 text-sm">
                        <a href="{{ route('catalog.index') }}" class="text-charcoal-muted hover:text-navy">Katalog</a>
                        <a href="{{ route('home') }}" class="text-charcoal-muted hover:text-navy">Beranda</a>
                    </nav>
                </div>

                <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 lg:px-10">
                    <div class="rb-reveal mb-6 w-full max-w-md text-left">
                        @isset($heading)
                            <p class="rb-eyebrow">{{ $eyebrow ?? '027 · Akun' }}</p>
                            <h1 class="mt-2 font-display text-3xl text-navy">{{ $heading }}</h1>
                            @isset($subheading)
                                <p class="mt-2 text-sm text-charcoal-muted">{{ $subheading }}</p>
                            @endisset
                        @endisset
                    </div>

                    <div class="rb-reveal rb-reveal-delay-1 rb-auth-card">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
