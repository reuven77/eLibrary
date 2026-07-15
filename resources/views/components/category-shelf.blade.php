@props(['categories'])

@php
    $spine = [
        'fiksi' => 'bg-brass',
        'sains' => 'bg-forest',
        'sejarah' => 'bg-rust',
        'teknologi' => 'bg-navy',
        'anak' => 'bg-brass-soft',
    ];
@endphp

<section {{ $attributes }}>
    <div class="mb-5 flex items-end justify-between gap-4">
        <div>
            <p class="rb-eyebrow">Rak pilihan</p>
            <h2 class="mt-1 font-display text-2xl text-navy">Kategori sebagai punggung buku</h2>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        @foreach ($categories as $category)
            @php $color = $spine[$category->slug] ?? 'bg-navy'; @endphp
            <a
                href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                class="rb-shelf-chip group inline-flex items-stretch overflow-hidden border border-navy/15 bg-white/70"
            >
                <span class="{{ $color }} w-1.5" aria-hidden="true"></span>
                <span class="px-4 py-2.5 text-sm text-charcoal group-hover:text-navy">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
