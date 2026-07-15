@props([])

@php
    $user = auth()->user();
    $pendingCount = $user?->isAdmin()
        ? \App\Models\Loan::query()->where('status', \App\Models\Loan::STATUS_PENDING)->count()
        : 0;
@endphp

<header class="bg-navy text-onionskin">
    <div class="rb-container flex h-14 items-center justify-between gap-4">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="font-display text-xl italic tracking-tight text-onionskin focus-visible:ring-offset-navy">
                RuangBaca
            </a>

            <nav class="hidden items-center gap-6 text-sm sm:flex" aria-label="Utama">
                <a href="{{ route('catalog.index') }}"
                   class="{{ request()->routeIs('catalog.*') ? 'text-brass border-b border-brass' : 'text-onionskin/85 hover:text-onionskin' }} pb-0.5 transition">
                    Katalog
                </a>
                @auth
                    @if ($user?->isAdmin())
                        <a href="{{ route('admin.loans.pending') }}"
                           class="{{ request()->routeIs('admin.loans.pending') ? 'text-brass border-b border-brass' : 'text-onionskin/85 hover:text-onionskin' }} inline-flex items-center gap-1.5 pb-0.5 transition">
                            Antrian
                            @if ($pendingCount > 0)
                                <span class="rounded-full bg-brass px-1.5 py-0.5 font-mono text-[10px] leading-none text-navy">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.loans.index') }}"
                           class="{{ request()->routeIs('admin.loans.index') ? 'text-brass border-b border-brass' : 'text-onionskin/85 hover:text-onionskin' }} pb-0.5 transition">
                            Peminjaman
                        </a>
                        <a href="{{ route('admin.dashboard') }}"
                           class="{{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.books.*') || request()->routeIs('admin.users.*') ? 'text-brass border-b border-brass' : 'text-onionskin/85 hover:text-onionskin' }} pb-0.5 transition">
                            Admin
                        </a>
                    @else
                        <a href="{{ route('loans.index') }}"
                           class="{{ request()->routeIs('loans.*') ? 'text-brass border-b border-brass' : 'text-onionskin/85 hover:text-onionskin' }} pb-0.5 transition">
                            Pinjaman
                        </a>
                    @endif
                @endauth
            </nav>
        </div>

        <div class="flex items-center gap-3 text-sm">
            @auth
                <span class="hidden text-onionskin/70 sm:inline">{{ $user->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-onionskin/85 underline decoration-brass/40 underline-offset-4 hover:text-onionskin">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-onionskin/85 hover:text-onionskin">Masuk</a>
                <a href="{{ route('register') }}" class="rb-btn-primary !bg-onionskin !py-1.5 !text-navy hover:!bg-white">Daftar</a>
            @endauth
        </div>
    </div>
</header>
