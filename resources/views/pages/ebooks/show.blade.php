<x-ruangbaca-layout :title="'Baca: '.$book->title">
    <x-slot name="header">
        <p class="rb-eyebrow">{{ $book->call_number }} · E-book</p>
        <h1 class="mt-1 font-display text-3xl text-navy">{{ $book->title }}</h1>
        <p class="mt-1 text-sm text-charcoal-muted">{{ $book->author->name }}</p>
    </x-slot>

    <div class="rb-container space-y-4 py-8">
        <div class="border border-navy/15 bg-white/80 p-3">
            @if ($book->file_path)
                <iframe
                    src="{{ route('ebooks.file', $book) }}"
                    class="h-[70vh] w-full border border-navy/10 bg-onionskin"
                    title="Pembaca e-book {{ $book->title }}"
                ></iframe>
            @else
                <p class="p-8 text-charcoal-muted">File digital belum tersedia untuk buku ini.</p>
            @endif
        </div>
        <a href="{{ route('catalog.show', $book) }}" class="text-sm text-navy underline decoration-brass underline-offset-4">
            Kembali ke detail
        </a>
    </div>
</x-ruangbaca-layout>
