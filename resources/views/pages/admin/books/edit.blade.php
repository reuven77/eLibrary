<x-ruangbaca-layout title="Edit Buku — RuangBaca">
    <x-slot name="header">
        <p class="rb-eyebrow">Admin · Koleksi</p>
        <h1 class="mt-1 font-display text-3xl text-navy">Edit buku</h1>
    </x-slot>

    <div class="rb-container max-w-3xl py-10">
        @include('pages.admin.books._form', [
            'action' => route('admin.books.update', $book),
            'method' => 'PUT',
            'book' => $book,
            'authors' => $authors,
            'categories' => $categories,
        ])
    </div>
</x-ruangbaca-layout>
