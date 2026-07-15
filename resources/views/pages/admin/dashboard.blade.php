<x-ruangbaca-layout title="Dashboard Admin — RuangBaca">
    <x-slot name="header">
        <p class="rb-eyebrow">Admin · Ringkasan</p>
        <h1 class="mt-1 font-display text-3xl text-navy">Dashboard pustakawan</h1>
    </x-slot>

    <x-flash />

    <div class="rb-container space-y-8 py-10">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="border border-navy/15 bg-white/80 p-5">
                <p class="font-mono text-utility uppercase text-charcoal-muted">Total buku</p>
                <p class="mt-2 font-mono text-4xl text-navy">{{ $summary['total_books'] }}</p>
            </div>
            <a href="{{ route('admin.loans.pending') }}" class="border border-brass/50 bg-brass/10 p-5 transition hover:bg-brass/20">
                <p class="font-mono text-utility uppercase text-charcoal-muted">Menunggu persetujuan</p>
                <p class="mt-2 font-mono text-4xl text-brass">{{ $summary['pending'] ?? 0 }}</p>
                <p class="mt-2 text-xs text-navy underline decoration-brass underline-offset-4">Buka antrian →</p>
            </a>
            <div class="border border-navy/15 bg-white/80 p-5">
                <p class="font-mono text-utility uppercase text-charcoal-muted">Dipinjam</p>
                <p class="mt-2 font-mono text-4xl text-forest">{{ $summary['borrowed'] }}</p>
            </div>
            <div class="border border-navy/15 bg-white/80 p-5">
                <p class="font-mono text-utility uppercase text-charcoal-muted">Terlambat</p>
                <p class="mt-2 font-mono text-4xl text-rust">{{ $summary['overdue'] }}</p>
            </div>
            <div class="border border-navy/15 bg-white/80 p-5">
                <p class="font-mono text-utility uppercase text-charcoal-muted">Denda outstanding</p>
                <p class="mt-2 font-mono text-4xl text-brass">{{ $summary['outstanding_fines'] }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 text-sm">
            <a href="{{ route('admin.loans.pending') }}" class="rb-btn-primary">
                Antrian persetujuan
                @if (($summary['pending'] ?? 0) > 0)
                    <span class="ml-1 font-mono">({{ $summary['pending'] }})</span>
                @endif
            </a>
            <a href="{{ route('admin.loans.index') }}" class="rb-btn-ghost">Semua peminjaman</a>
            <a href="{{ route('admin.books.index') }}" class="rb-btn-ghost">Kelola buku</a>
            <a href="{{ route('admin.users.index') }}" class="rb-btn-ghost">Manajemen user</a>
        </div>

        <section>
            <h2 class="font-display text-2xl text-navy">Transaksi terkini</h2>
            <div class="mt-4 overflow-hidden border border-navy/15 bg-white/80">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-navy/10 bg-onionskin/60 font-mono text-utility uppercase text-charcoal-muted">
                        <tr>
                            <th class="px-4 py-3">Anggota</th>
                            <th class="px-4 py-3">Buku</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Jatuh tempo</th>
                            <th class="px-4 py-3">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loans as $loan)
                            <tr class="border-t border-navy/10">
                                <td class="px-4 py-3">{{ $loan->user->name }}</td>
                                <td class="px-4 py-3">{{ $loan->book->title }}</td>
                                <td class="px-4 py-3"><x-stamp-badge :status="$loan->status" /></td>
                                <td class="px-4 py-3 font-mono text-utility">{{ $loan->due_at?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 font-mono">{{ $loan->fine_amount }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-charcoal-muted">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $loans->links() }}</div>
        </section>
    </div>
</x-ruangbaca-layout>
