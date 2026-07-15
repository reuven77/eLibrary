@props(['book', 'stagger' => 0])

@php
    $status = $book->availabilityStatus();
    $synopsis = \Illuminate\Support\Str::limit(strip_tags((string) $book->synopsis), 180);
    $coverUrl = $book->cover_image_path
        ? asset('storage/'.$book->cover_image_path)
        : null;
    $categoryName = $book->category->name ?? 'Katalog';
@endphp

<article
    {{ $attributes->merge(['class' => 'catalog-card']) }}
    style="--stagger: {{ (int) $stagger }}"
    x-data="{
        flipped: false,
        expanded: false,
        reduced: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
        toggle() {
            if (this.reduced) this.expanded = !this.expanded;
            else this.flipped = !this.flipped;
        }
    }"
    :class="{ 'is-flipped': flipped && !reduced }"
>
    <div
        class="catalog-card-inner"
        role="button"
        tabindex="0"
        :aria-pressed="(reduced ? expanded : flipped).toString()"
        aria-label="Detail singkat: {{ $book->title }}"
        @click="toggle()"
        @keydown.enter.prevent="toggle()"
        @keydown.space.prevent="toggle()"
    >
        {{-- Front --}}
        <div class="catalog-card-face is-front">
            <div class="catalog-card-cover">
                @if ($coverUrl)
                    <img
                        src="{{ $coverUrl }}"
                        alt="Sampul {{ $book->title }}"
                        loading="lazy"
                    >
                @else
                    <div class="catalog-card-cover-fallback">
                        <span class="font-display text-xl italic leading-snug text-onionskin">
                            {{ \Illuminate\Support\Str::limit($book->title, 42) }}
                        </span>
                        <span class="mt-2 text-xs text-onionskin/70">{{ $book->author->name ?? '' }}</span>
                    </div>
                @endif

                <div class="catalog-card-overlays">
                    <span class="catalog-card-category">{{ $categoryName }}</span>
                    <x-stamp-badge :status="$status" class="catalog-card-status pointer-events-none shrink-0" />
                </div>
            </div>

            <div class="catalog-card-body">
                <p class="font-mono text-[10px] uppercase tracking-[0.04em] text-charcoal-muted">
                    {{ $book->call_number }}
                </p>
                <h3 class="mt-1 line-clamp-2 font-display text-base leading-snug text-navy">
                    <a href="{{ route('catalog.show', $book) }}" class="hover:text-brass" @click.stop>
                        {{ $book->title }}
                    </a>
                </h3>
                <p class="mt-1 truncate text-sm text-charcoal-muted">{{ $book->author->name ?? '—' }}</p>

                <template x-if="reduced">
                    <div class="mt-2" @click.stop>
                        <button
                            type="button"
                            class="text-sm text-navy underline decoration-brass underline-offset-4"
                            @click="expanded = !expanded"
                            x-text="expanded ? 'Sembunyikan sinopsis' : 'Tampilkan sinopsis'"
                        ></button>
                        <p class="mt-2 text-sm leading-relaxed text-charcoal" x-show="expanded">{{ $synopsis }}</p>
                    </div>
                </template>

                <p class="mt-auto pt-2 font-mono text-utility uppercase text-charcoal-muted">
                    {{ $categoryName }} · {{ $book->format }}
                </p>
            </div>
        </div>

        {{-- Back (flip) --}}
        <div class="catalog-card-face catalog-card-back" x-cloak x-show="!reduced">
            <p class="rb-eyebrow mb-2">028 · Sinopsis</p>
            <p class="text-sm leading-relaxed text-charcoal">{{ $synopsis }}</p>
            <a
                href="{{ route('catalog.show', $book) }}"
                class="mt-auto pt-4 text-sm text-navy underline decoration-brass underline-offset-4"
                @click.stop
            >
                Lihat detail
            </a>
        </div>
    </div>
</article>
