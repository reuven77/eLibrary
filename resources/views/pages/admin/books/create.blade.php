<x-ruangbaca-layout title="Tambah Buku — RuangBaca">
    <x-slot name="header">
        <p class="rb-eyebrow">Admin · Koleksi</p>
        <h1 class="mt-1 font-display text-3xl text-navy">Tambah buku</h1>
    </x-slot>

    <div class="rb-container max-w-3xl py-10">
        @include('pages.admin.books._form', [
            'action' => route('admin.books.store'),
            'method' => 'POST',
            'book' => null,
            'authors' => $authors,
            'categories' => $categories,
        ])
    </div>
</x-ruangbaca-layout>
