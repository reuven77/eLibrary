<x-ruangbaca-layout title="Kelola Buku — RuangBaca">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="rb-eyebrow">Admin · Koleksi</p>
                <h1 class="mt-1 font-display text-3xl text-navy">Kelola buku</h1>
            </div>
            <a href="{{ route('admin.books.create') }}" class="rb-btn-primary">+ Tambah buku</a>
        </div>
    </x-slot>

    <x-flash />

    <div class="rb-container py-10">
        <div class="overflow-hidden border border-navy/15 bg-white/80">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-navy/10 bg-onionskin/60 font-mono text-utility uppercase text-charcoal-muted">
                    <tr>
                        <th class="px-4 py-3">Call no.</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Format</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $book)
                        <tr class="border-t border-navy/10">
                            <td class="px-4 py-3 font-mono text-[11px]">{{ $book->call_number }}</td>
                            <td class="px-4 py-3">
                                <div class="text-navy">{{ $book->title }}</div>
                                <div class="text-charcoal-muted">{{ $book->author->name }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono">{{ $book->stock }}</td>
                            <td class="px-4 py-3">{{ $book->format }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.books.edit', $book) }}" class="text-navy underline decoration-brass underline-offset-4">Edit</a>
                                <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus buku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rust underline underline-offset-4">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $books->links() }}</div>
    </div>
</x-ruangbaca-layout>
