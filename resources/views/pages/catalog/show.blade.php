@php
    $avg = $book->reviews->avg('rating');
    $reviewCount = $book->reviews->count();
@endphp

<x-ruangbaca-layout :title="$book->title.' — RuangBaca'">
    <x-flash />

    <div class="rb-container py-10 lg:py-14">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,18rem)_1fr]">
            {{-- Cover / kartu katalog visual --}}
            <aside class="relative border border-navy/15 bg-white/80 p-5 shadow-card">
                <div class="absolute left-4 right-4 top-0 border-t border-dashed border-navy/25"></div>
                <p class="mb-4 text-right font-mono text-[11px] uppercase tracking-[0.04em] text-charcoal-muted">
                    {{ $book->call_number }}
                </p>

                @if ($book->cover_image_path)
                    <img
                        src="{{ asset('storage/'.$book->cover_image_path) }}"
                        alt="Sampul {{ $book->title }}"
                        class="aspect-[3/4] w-full object-cover"
                    >
                @else
                    <div class="flex aspect-[3/4] w-full flex-col justify-between bg-navy p-5 text-onionskin">
                        <span class="font-mono text-utility uppercase tracking-[0.08em] text-brass">{{ $book->category->name }}</span>
                        <span class="font-display text-2xl italic leading-snug">{{ $book->title }}</span>
                        <span class="text-sm text-onionskin/70">{{ $book->author->name }}</span>
                    </div>
                @endif
            </aside>

            <div>
                <p class="rb-eyebrow">
                    {{ $book->call_number }} · {{ strtoupper($book->category->name) }}
                </p>
                <h1 class="mt-3 font-display text-display-sm text-navy sm:text-display-md not-italic">
                    {{ $book->title }}
                </h1>
                <p class="mt-2 text-charcoal-muted">oleh {{ $book->author->name }}</p>

                <div class="mt-4 flex flex-wrap items-center gap-4">
                    @if ($reviewCount > 0)
                        <p class="text-sm text-charcoal">
                            <span aria-hidden="true">{{ str_repeat('★', (int) round($avg)) }}{{ str_repeat('☆', 5 - (int) round($avg)) }}</span>
                            <span class="sr-only">Rating rata-rata {{ number_format($avg, 1) }} dari 5</span>
                            <span class="text-charcoal-muted">({{ $reviewCount }} ulasan)</span>
                        </p>
                    @else
                        <p class="text-sm text-charcoal-muted">Belum ada ulasan</p>
                    @endif

                    <x-stamp-badge :status="$book->availabilityStatus()" />
                </div>

                <dl class="mt-6 grid gap-3 border-y border-navy/10 py-4 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="font-mono text-utility uppercase text-charcoal-muted">Format</dt>
                        <dd class="mt-1">{{ $book->format }}</dd>
                    </div>
                    <div>
                        <dt class="font-mono text-utility uppercase text-charcoal-muted">Stok fisik</dt>
                        <dd class="mt-1 font-mono">{{ $book->stock }}</dd>
                    </div>
                    <div>
                        <dt class="font-mono text-utility uppercase text-charcoal-muted">Tahun</dt>
                        <dd class="mt-1 font-mono">{{ $book->published_year ?? '—' }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-wrap gap-3">
                    @auth
                        @if ($book->isPhysical())
                            <a href="{{ route('loans.create', $book) }}" class="rb-btn-primary">Pinjam Buku</a>
                        @endif
                        @if ($book->isDigital())
                            <a href="{{ route('ebooks.show', $book) }}" class="rb-btn-ghost">Baca Sekarang</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="rb-btn-primary">Masuk untuk pinjam/baca</a>
                    @endauth
                </div>

                <div class="mt-10">
                    <p class="rb-eyebrow">Sinopsis</p>
                    <p class="mt-3 max-w-2xl text-base leading-relaxed text-charcoal">{{ $book->synopsis }}</p>
                </div>
            </div>
        </div>

        <div class="mt-14 grid gap-10 lg:grid-cols-2">
            @auth
                <section class="border border-navy/15 bg-white/70 p-6">
                    <h2 class="font-display text-xl text-navy">Tulis ulasan</h2>
                    <form method="POST" action="{{ route('reviews.store', $book) }}" class="mt-4 space-y-4">
                        @csrf
                        <label class="block text-sm">
                            Rating (1–5)
                            <input type="number" name="rating" min="1" max="5" value="{{ old('rating', 5) }}" class="mt-1 block w-28 border-navy/20 focus:border-brass focus:ring-brass" required>
                        </label>
                        <label class="block text-sm">
                            Komentar
                            <textarea name="comment" rows="4" class="mt-1 block w-full border-navy/20 focus:border-brass focus:ring-brass" placeholder="Opsional">{{ old('comment') }}</textarea>
                        </label>
                        <button type="submit" class="rb-btn-primary">Kirim ulasan</button>
                    </form>
                </section>
            @endauth

            <section class="border border-navy/15 bg-white/70 p-6 {{ auth()->check() ? '' : 'lg:col-span-2' }}">
                <h2 class="font-display text-xl text-navy">Ulasan pembaca</h2>
                <ul class="mt-4 space-y-4">
                    @forelse ($book->reviews as $review)
                        <li class="border-b border-navy/10 pb-4">
                            <div class="flex items-baseline justify-between gap-3">
                                <p class="text-sm font-medium text-navy">{{ $review->user->name }}</p>
                                <p class="font-mono text-utility text-charcoal-muted" aria-label="Rating {{ $review->rating }} dari 5">
                                    {{ $review->rating }}/5
                                </p>
                            </div>
                            @if ($review->comment)
                                <p class="mt-2 text-sm leading-relaxed text-charcoal">{{ $review->comment }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-sm text-charcoal-muted">Belum ada ulasan.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-ruangbaca-layout>
