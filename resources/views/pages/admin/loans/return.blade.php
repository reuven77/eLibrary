<x-ruangbaca-layout title="Konfirmasi Pengembalian — RuangBaca">
    <x-slot name="header">
        <p class="rb-eyebrow">Admin · Sirkulasi</p>
        <h1 class="mt-1 font-display text-3xl text-navy">Konfirmasi pengembalian</h1>
    </x-slot>

    <x-flash />

    <div class="rb-container max-w-2xl space-y-6 py-10">
        <div class="border border-navy/15 bg-white/80 p-6">
            <x-stamp-badge :status="$loan->status" />
            <h2 class="mt-3 font-display text-2xl text-navy">{{ $loan->book->title }}</h2>
            <p class="mt-1 text-sm text-charcoal-muted">
                {{ $loan->book->author->name ?? '—' }} · {{ $loan->book->call_number }}
            </p>

            <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="font-mono text-utility uppercase text-charcoal-muted">Anggota</dt>
                    <dd class="mt-1">{{ $loan->user->name }}</dd>
                    <dd class="font-mono text-[11px] text-charcoal-muted">{{ $loan->user->email }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-utility uppercase text-charcoal-muted">Telepon</dt>
                    <dd class="mt-1 font-mono">{{ $loan->borrower_phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-utility uppercase text-charcoal-muted">Dipinjam</dt>
                    <dd class="mt-1 font-mono">{{ $loan->borrowed_at?->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-utility uppercase text-charcoal-muted">Jatuh tempo</dt>
                    <dd class="mt-1 font-mono">{{ $loan->due_at?->format('Y-m-d') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-utility uppercase text-charcoal-muted">Hari terlambat</dt>
                    <dd class="mt-1 font-mono {{ $daysLate > 0 ? 'text-rust' : '' }}">{{ $daysLate }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-utility uppercase text-charcoal-muted">Estimasi denda</dt>
                    <dd class="mt-1 font-mono text-lg {{ bccomp((string) $estimatedFine, '0.00', 2) === 1 ? 'text-rust' : 'text-forest' }}">
                        Rp {{ $estimatedFine }}
                    </dd>
                </div>
            </dl>

            <p class="mt-6 text-sm text-charcoal-muted">
                Setelah dikonfirmasi, stok buku naik 1 dan status menjadi <em>dikembalikan</em>.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.loans.return', $loan) }}">
                    @csrf
                    <button type="submit" class="rb-btn-primary">Konfirmasi pengembalian</button>
                </form>
                <a href="{{ route('admin.loans.index') }}" class="rb-btn-ghost">Batal</a>
            </div>
        </div>
    </div>
</x-ruangbaca-layout>
