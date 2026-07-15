<x-ruangbaca-layout title="Antrian Persetujuan — RuangBaca">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="rb-eyebrow">Admin · Sirkulasi</p>
                <h1 class="mt-1 font-display text-3xl text-navy">Antrian persetujuan</h1>
            </div>
            <a href="{{ route('admin.loans.index') }}" class="rb-btn-ghost">Semua peminjaman</a>
        </div>
    </x-slot>

    <x-flash />

    <div class="rb-container space-y-4 py-10">
        @forelse ($loans as $loan)
            <article
                class="border border-navy/15 bg-white/80 p-5"
                x-data="{ rejectOpen: false }"
            >
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <x-stamp-badge status="menunggu_persetujuan" />
                        <h2 class="mt-3 font-display text-xl text-navy">{{ $loan->book->title }}</h2>
                        <p class="mt-1 text-sm text-charcoal-muted">
                            {{ $loan->user->name }} · {{ $loan->user->email }}
                        </p>
                        <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-3">
                            <div>
                                <dt class="font-mono text-utility uppercase text-charcoal-muted">Call no.</dt>
                                <dd class="font-mono">{{ $loan->book->call_number }}</dd>
                            </div>
                            <div>
                                <dt class="font-mono text-utility uppercase text-charcoal-muted">Diajukan</dt>
                                <dd class="font-mono">{{ $loan->borrowed_at?->format('Y-m-d H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="font-mono text-utility uppercase text-charcoal-muted">Stok saat ini</dt>
                                <dd class="font-mono">{{ $loan->book->stock }}</dd>
                            </div>
                            <div>
                                <dt class="font-mono text-utility uppercase text-charcoal-muted">No. HP</dt>
                                <dd class="font-mono">{{ $loan->borrower_phone ?? '—' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="font-mono text-utility uppercase text-charcoal-muted">Alamat</dt>
                                <dd>{{ $loan->borrower_address ?? '—' }}</dd>
                            </div>
                            @if ($loan->borrower_notes)
                                <div class="sm:col-span-3">
                                    <dt class="font-mono text-utility uppercase text-charcoal-muted">Catatan</dt>
                                    <dd>{{ $loan->borrower_notes }}</dd>
                                </div>
                            @endif
                            @if ($loan->id_card_path)
                                <div class="sm:col-span-3">
                                    <dt class="font-mono text-utility uppercase text-charcoal-muted">Kartu identitas</dt>
                                    <dd class="mt-1">
                                        <a
                                            href="{{ asset('storage/'.$loan->id_card_path) }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="inline-block"
                                        >
                                            <img
                                                src="{{ asset('storage/'.$loan->id_card_path) }}"
                                                alt="Kartu identitas {{ $loan->user->name }}"
                                                class="max-h-36 border border-navy/15 object-contain"
                                            >
                                        </a>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.loans.approve', $loan) }}">
                            @csrf
                            <button class="inline-flex items-center bg-forest px-4 py-2 text-sm text-onionskin hover:bg-forest-soft">
                                Setujui
                            </button>
                        </form>

                        <button
                            type="button"
                            class="inline-flex items-center bg-rust px-4 py-2 text-sm text-onionskin hover:bg-rust-soft"
                            @click="rejectOpen = true"
                        >
                            Tolak
                        </button>
                    </div>
                </div>

                {{-- Modal alasan penolakan --}}
                <div
                    x-show="rejectOpen"
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-navy/40 p-4"
                    @keydown.escape.window="rejectOpen = false"
                >
                    <div class="w-full max-w-md border border-navy/15 bg-onionskin p-6 shadow-card" @click.outside="rejectOpen = false">
                        <h3 class="font-display text-xl text-navy">Tolak pengajuan</h3>
                        <p class="mt-1 text-sm text-charcoal-muted">{{ $loan->book->title }} — {{ $loan->user->name }}</p>

                        <form method="POST" action="{{ route('admin.loans.reject', $loan) }}" class="mt-4 space-y-3">
                            @csrf
                            <label class="block text-sm">Alasan penolakan
                                <textarea
                                    name="reason"
                                    rows="4"
                                    required
                                    minlength="5"
                                    class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass"
                                    placeholder="Jelaskan alasan penolakan (min. 5 karakter)"
                                >{{ old('reason') }}</textarea>
                            </label>
                            <div class="flex gap-2 pt-2">
                                <button class="inline-flex bg-rust px-4 py-2 text-sm text-onionskin">Kirim penolakan</button>
                                <button type="button" class="rb-btn-ghost" @click="rejectOpen = false">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="border border-dashed border-navy/20 bg-white/50 p-8 text-charcoal-muted">
                Tidak ada pengajuan yang menunggu persetujuan.
            </p>
        @endforelse

        <div>{{ $loans->links() }}</div>
    </div>
</x-ruangbaca-layout>
