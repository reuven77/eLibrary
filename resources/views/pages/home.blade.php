<x-ruangbaca-layout title="RuangBaca — ruang baca, tanpa antre">
    <x-flash />

    {{-- Hero full-bleed ekstrem --}}
    <section class="rb-hero">
        <div class="rb-hero-grid" aria-hidden="true"></div>
        <div class="rb-hero-glow" aria-hidden="true"></div>
        <div class="rb-hero-glow-2" aria-hidden="true"></div>

        <div class="rb-container relative grid gap-12 py-16 lg:grid-cols-[1.15fr_0.85fr] lg:items-center lg:py-24">
            <div>
                <p class="rb-reveal rb-eyebrow text-brass/90">028.9 · Koleksi terbuka</p>
                <p class="rb-reveal rb-reveal-delay-1 mt-4 font-display text-4xl italic leading-none sm:text-5xl lg:text-6xl">
                    <span class="rb-brand-impact">RuangBaca</span>
                </p>
                <h1 class="rb-reveal rb-reveal-delay-2 mt-5 max-w-xl font-display text-2xl leading-snug text-onionskin/95 sm:text-3xl not-italic">
                    Ruang baca, tanpa antre.
                </h1>
                <p class="rb-reveal rb-reveal-delay-3 mt-4 max-w-lg text-base leading-relaxed text-onionskin/70 sm:text-lg">
                    Cari, pinjam, atau baca — semua tercatat rapi seperti kartu katalog, secepat klik.
                </p>
                <div class="rb-reveal rb-reveal-delay-4 mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('catalog.index') }}" class="rb-btn bg-onionskin text-navy hover:bg-white">
                        Jelajahi Katalog
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="rb-btn border border-onionskin/35 text-onionskin hover:border-brass hover:text-brass">
                            Buat akun
                        </a>
                    @else
                        <a href="{{ route('catalog.index', ['category' => 'fiksi']) }}" class="rb-btn border border-onionskin/35 text-onionskin hover:border-brass hover:text-brass">
                            Baca Sekarang
                        </a>
                    @endguest
                </div>
            </div>

            <div class="rb-reveal rb-reveal-delay-5 relative" aria-hidden="true">
                <div class="rb-spine-stack">
                    <div class="rb-spine rb-float left-[8%] top-8 bg-rust" style="transform: rotate(-12deg);">
                        <span>Sejarah · 900</span>
                    </div>
                    <div class="rb-spine rb-float-delay left-[28%] top-4 bg-forest" style="transform: rotate(-4deg);">
                        <span>Sains · 500</span>
                    </div>
                    <div class="rb-spine rb-float left-[48%] top-2 bg-brass" style="transform: rotate(3deg);">
                        <span>Fiksi · 800</span>
                    </div>
                    <div class="rb-spine rb-float-delay left-[68%] top-10 bg-navy-soft border-brass/30" style="transform: rotate(10deg);">
                        <span>Teknologi · 000</span>
                    </div>

                    <div class="rb-stamp-drift absolute bottom-6 right-4 rounded-[100%] border-2 border-brass bg-navy/80 px-5 py-3 font-mono text-xs uppercase tracking-[0.12em] text-brass">
                        ● Tersedia
                    </div>
                </div>

                <dl class="rb-counter mt-2 grid grid-cols-2 gap-3 border border-onionskin/15 bg-navy-soft/40 p-4 backdrop-blur-sm">
                    <div>
                        <dt class="font-mono text-[10px] uppercase tracking-[0.08em] text-onionskin/50">Di rak</dt>
                        <dd class="mt-1 font-mono text-2xl text-onionskin">{{ $books->total() }}</dd>
                    </div>
                    <div>
                        <dt class="font-mono text-[10px] uppercase tracking-[0.08em] text-onionskin/50">Kategori</dt>
                        <dd class="mt-1 font-mono text-2xl text-onionskin">{{ $categories->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="rb-container py-14" x-data="revealOnScroll" :class="{ 'rb-section-in': true }">
        <x-category-shelf :categories="$categories" />
    </section>

    <section class="rb-container pb-20" x-data="revealOnScroll" :class="{ 'rb-section-in': true }">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="rb-eyebrow">Kartu katalog digital</p>
                <h2 class="mt-1 font-display text-2xl text-navy">Koleksi pilihan</h2>
            </div>
            <a href="{{ route('catalog.index') }}" class="rb-btn-ghost !py-1.5 text-sm">
                Lihat semua
            </a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($books as $book)
                <x-catalog-card :book="$book" :stagger="$loop->index" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $books->onEachSide(1)->links() }}
        </div>
    </section>
</x-ruangbaca-layout>
