<x-ruangbaca-layout title="Pinjaman Saya — RuangBaca">
    <x-slot name="header">
        <p class="rb-eyebrow">027 · Sirkulasi</p>
        <h1 class="mt-1 font-display text-3xl text-navy">Pinjaman saya</h1>
        <p class="mt-2 max-w-2xl text-sm text-charcoal-muted">
            Pengembalian buku fisik dikonfirmasi oleh pustakawan di perpustakaan.
            Pastikan buku dikembalikan sebelum jatuh tempo agar tidak kena denda.
        </p>
    </x-slot>

    <x-flash />

    <div class="rb-container py-10">
        <div class="overflow-hidden border border-navy/15 bg-white/80">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-navy/10 bg-onionskin/60 font-mono text-utility uppercase text-charcoal-muted">
                    <tr>
                        <th class="px-4 py-3">Buku</th>
                        <th class="px-4 py-3">Dipinjam</th>
                        <th class="px-4 py-3">Jatuh tempo</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Denda</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        <tr class="border-t border-navy/10 {{ $loan->isOverdue() || $loan->status === 'terlambat' ? 'bg-rust/5' : '' }}">
                            <td class="px-4 py-4">
                                <div class="font-medium text-navy">{{ $loan->book->title }}</div>
                                <div class="font-mono text-[11px] text-charcoal-muted">{{ $loan->book->call_number }}</div>
                            </td>
                            <td class="px-4 py-4 font-mono text-utility">{{ $loan->borrowed_at?->timezone(config('app.timezone'))->format('Y-m-d') }}</td>
                            <td class="px-4 py-4 font-mono text-utility {{ $loan->isOverdue() || $loan->status === 'terlambat' ? 'text-rust' : '' }}">
                                {{ $loan->due_at?->timezone(config('app.timezone'))->format('Y-m-d') ?? '—' }}
                            </td>
                            <td class="px-4 py-4">
                                <x-stamp-badge :status="$loan->status" />
                            </td>
                            <td class="px-4 py-4 font-mono">{{ $loan->fine_amount }}</td>
                            <td class="px-4 py-4 text-right">
                                @if ($loan->canPrintReceipt())
                                    <a
                                        href="{{ route('loans.print', $loan) }}"
                                        class="text-navy underline decoration-brass underline-offset-4"
                                    >
                                        Cetak kartu
                                    </a>
                                @elseif ($loan->status === 'dikembalikan')
                                    <span class="text-charcoal-muted">Selesai</span>
                                @elseif ($loan->status === 'menunggu_persetujuan')
                                    <span class="text-charcoal-muted">Menunggu admin</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-charcoal-muted">Belum ada riwayat peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $loans->links() }}</div>
    </div>
</x-ruangbaca-layout>
