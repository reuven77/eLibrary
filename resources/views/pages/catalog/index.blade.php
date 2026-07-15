<x-ruangbaca-layout title="Katalog — RuangBaca">
    <x-slot name="header">
        <p class="rb-eyebrow">025 · Katalog</p>
        <h1 class="mt-1 font-display text-3xl text-navy">Temukan buku & statusnya</h1>
    </x-slot>

    <x-flash />

    <div class="rb-container space-y-8 py-10">
        <div class="rb-catalog-banner rb-reveal relative z-0">
            <div class="relative z-10">
                <p class="font-mono text-[11px] uppercase tracking-[0.08em] text-brass">Rak terbuka</p>
                <p class="mt-2 max-w-xl font-display text-2xl italic text-onionskin">
                    Sentuh kartu untuk membaca sinopsis. Stempel status ada di pojok kanan sampul.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('catalog.index') }}" class="rb-reveal rb-reveal-delay-1 grid gap-3 border border-navy/15 bg-white/70 p-4 sm:grid-cols-[1fr_14rem_auto]">
            <label class="sr-only" for="q">Cari</label>
            <input
                id="q"
                type="search"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Judul, penulis, ISBN, atau nomor panggil"
                class="border-navy/20 bg-onionskin/40 font-sans text-charcoal focus:border-brass focus:ring-brass"
            >

            <label class="sr-only" for="category">Kategori</label>
            <select id="category" name="category" class="border-navy/20 bg-onionskin/40 focus:border-brass focus:ring-brass">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="rb-btn-primary">Cari</button>
        </form>

        <div x-data="revealOnScroll" class="rb-section-in">
            <x-category-shelf :categories="$categories" />
        </div>

        <div
            x-data="revealOnScroll"
            class="rb-section-in grid gap-5 sm:grid-cols-2 lg:grid-cols-4"
        >
            @forelse ($books as $book)
                <x-catalog-card :book="$book" :stagger="$loop->index % 8" />
            @empty
                <p class="col-span-full border border-dashed border-navy/20 bg-white/50 p-8 text-charcoal-muted">
                    Tidak ada buku yang cocok dengan pencarian ini.
                </p>
            @endforelse
        </div>

        <div>{{ $books->withQueryString()->links() }}</div>
    </div>
</x-ruangbaca-layout>
