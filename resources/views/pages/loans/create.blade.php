<x-ruangbaca-layout :title="'Ajukan pinjaman — '.$book->title">
    <x-slot name="header">
        <p class="rb-eyebrow">027 · Pengajuan</p>
        <h1 class="mt-1 font-display text-3xl text-navy">Ajukan peminjaman</h1>
        <p class="mt-2 max-w-2xl text-sm text-charcoal-muted">
            Lengkapi data identitas. Pustakawan akan meninjau pengajuan sebelum buku bisa diambil.
        </p>
    </x-slot>

    <x-flash />

    <div class="rb-container py-10">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,16rem)_1fr]">
            <aside class="border border-navy/15 bg-white/80 p-4">
                @if ($book->cover_image_path)
                    <img
                        src="{{ asset('storage/'.$book->cover_image_path) }}"
                        alt="Sampul {{ $book->title }}"
                        class="aspect-[3/4] w-full object-cover"
                    >
                @else
                    <div class="flex aspect-[3/4] flex-col justify-end bg-navy p-4 text-onionskin">
                        <span class="font-display text-xl italic">{{ $book->title }}</span>
                    </div>
                @endif
                <h2 class="mt-4 font-display text-lg text-navy">{{ $book->title }}</h2>
                <p class="text-sm text-charcoal-muted">{{ $book->author->name }}</p>
                <p class="mt-2 font-mono text-[11px] text-charcoal-muted">{{ $book->call_number }}</p>
            </aside>

            <form
                method="POST"
                action="{{ route('loans.store', $book) }}"
                enctype="multipart/form-data"
                class="space-y-5 border border-navy/15 bg-white/80 p-6"
                x-data="{ idPreview: null }"
            >
                @csrf

                @if ($errors->any())
                    <div class="rb-flash-error">
                        <ul class="list-inside list-disc text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <label class="block text-sm">Nomor HP
                    <input
                        type="text"
                        name="borrower_phone"
                        value="{{ old('borrower_phone') }}"
                        class="mt-1 w-full border-navy/20 font-mono focus:border-brass focus:ring-brass"
                        placeholder="08xxxxxxxxxx"
                        required
                    >
                </label>

                <label class="block text-sm">Alamat lengkap
                    <textarea
                        name="borrower_address"
                        rows="3"
                        class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass"
                        placeholder="Jl. … RT/RW, kelurahan, kota"
                        required
                    >{{ old('borrower_address') }}</textarea>
                </label>

                <div class="space-y-2">
                    <label class="block text-sm">Foto kartu identitas (KTP / kartu pelajar)
                        <input
                            type="file"
                            name="id_card"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            class="mt-1 block w-full text-sm file:mr-3 file:border-0 file:bg-navy file:px-3 file:py-1.5 file:text-onionskin"
                            required
                            @change="idPreview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null"
                        >
                    </label>
                    <template x-if="idPreview">
                        <img :src="idPreview" alt="Preview identitas" class="mt-2 max-h-40 border border-navy/15 object-contain">
                    </template>
                    <p class="text-xs text-charcoal-muted">JPEG/PNG/WEBP · maks. 2 MB</p>
                </div>

                <label class="block text-sm">Catatan (opsional)
                    <textarea
                        name="borrower_notes"
                        rows="2"
                        class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass"
                        placeholder="Mis. akan diambil siang hari"
                    >{{ old('borrower_notes') }}</textarea>
                </label>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="rb-btn-primary">Kirim pengajuan</button>
                    <a href="{{ route('catalog.show', $book) }}" class="rb-btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-ruangbaca-layout>
