<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5 border border-navy/15 bg-white/80 p-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div class="rb-flash-error">
            <ul class="list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <label class="block text-sm">Judul
        <input name="title" value="{{ old('title', $book->title ?? '') }}" class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass" required>
    </label>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-sm">ISBN
            <input name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}" class="mt-1 w-full border-navy/20 font-mono focus:border-brass focus:ring-brass">
        </label>
        <label class="block text-sm">Nomor panggil
            <input name="call_number" value="{{ old('call_number', $book->call_number ?? '') }}" class="mt-1 w-full border-navy/20 font-mono focus:border-brass focus:ring-brass" required>
        </label>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-sm">Penulis
            <input
                name="author_name"
                value="{{ old('author_name', $book?->author?->name ?? '') }}"
                class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass"
                placeholder="Nama penulis (baru atau yang sudah ada)"
                required
                list="author-suggestions"
                autocomplete="off"
            >
            @if (($authors ?? collect())->isNotEmpty())
                <datalist id="author-suggestions">
                    @foreach ($authors as $author)
                        <option value="{{ $author->name }}"></option>
                    @endforeach
                </datalist>
            @endif
        </label>
        <label class="block text-sm">Kategori
            <select name="category_id" class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <label class="block text-sm">Format
            <select name="format" class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass" required>
                @foreach (['fisik', 'digital', 'keduanya'] as $format)
                    <option value="{{ $format }}" @selected(old('format', $book->format ?? 'fisik') === $format)>{{ $format }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm">Stok
            <input type="number" min="0" name="stock" value="{{ old('stock', $book->stock ?? 0) }}" class="mt-1 w-full border-navy/20 font-mono focus:border-brass focus:ring-brass" required>
        </label>
        <label class="block text-sm">Tahun terbit
            <input type="number" name="published_year" value="{{ old('published_year', $book->published_year ?? '') }}" class="mt-1 w-full border-navy/20 font-mono focus:border-brass focus:ring-brass">
        </label>
    </div>

    <label class="block text-sm">Sinopsis
        <textarea name="synopsis" rows="5" class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass">{{ old('synopsis', $book->synopsis ?? '') }}</textarea>
    </label>

    {{-- Cover upload + preview Alpine --}}
    <div
        class="space-y-3"
        x-data="{
            preview: null,
            existing: @js(! empty($book?->cover_image_path) ? asset('storage/'.$book->cover_image_path) : null)
        }"
    >
        <p class="text-sm font-medium text-navy">Sampul buku</p>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
            <div class="aspect-[3/4] w-36 shrink-0 overflow-hidden border border-navy/15 bg-onionskin/60">
                <template x-if="preview">
                    <img :src="preview" alt="Preview sampul baru" class="h-full w-full object-cover">
                </template>
                <template x-if="!preview && existing">
                    <img :src="existing" alt="Sampul saat ini" class="h-full w-full object-cover">
                </template>
                <template x-if="!preview && !existing">
                    <div class="flex h-full items-center justify-center p-3 text-center font-mono text-utility uppercase text-charcoal-muted">
                        Belum ada sampul
                    </div>
                </template>
            </div>

            <label class="block flex-1 text-sm">
                Pilih gambar (JPEG, PNG, WEBP · maks. 2 MB)
                <input
                    type="file"
                    name="cover_image"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="mt-1 block w-full text-sm file:mr-3 file:border-0 file:bg-navy file:px-3 file:py-1.5 file:text-onionskin"
                    @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null"
                >
                @error('cover_image')
                    <span class="mt-1 block text-sm text-rust">{{ $message }}</span>
                @enderror
            </label>
        </div>
    </div>

    <label class="block text-sm">File e-book (PDF/EPUB)
        <input type="file" name="ebook_file" accept=".pdf,.epub" class="mt-1 block w-full text-sm">
        @error('ebook_file')
            <span class="mt-1 block text-sm text-rust">{{ $message }}</span>
        @enderror
    </label>

    <div class="flex gap-3 pt-2">
        <button class="rb-btn-primary">Simpan</button>
        <a href="{{ route('admin.books.index') }}" class="rb-btn-ghost">Batal</a>
    </div>
</form>
