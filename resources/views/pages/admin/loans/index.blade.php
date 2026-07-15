@php
    $pendingCount = \App\Models\Loan::query()
        ->where('status', \App\Models\Loan::STATUS_PENDING)
        ->count();
@endphp

<x-ruangbaca-layout title="Kelola Peminjaman — RuangBaca">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="rb-eyebrow">Admin · Sirkulasi</p>
                <h1 class="mt-1 font-display text-3xl text-navy">Kelola peminjaman</h1>
            </div>
            <a href="{{ route('admin.loans.pending') }}" class="rb-btn-primary">
                Antrian persetujuan
                @if ($pendingCount > 0)
                    <span class="ml-1 font-mono">({{ $pendingCount }})</span>
                @endif
            </a>
        </div>
    </x-slot>

    <x-flash />

    <div class="rb-container space-y-6 py-10">
        @if ($pendingCount > 0 && $status !== 'menunggu_persetujuan')
            <div class="border border-brass/40 bg-brass/10 px-4 py-3 text-sm text-navy">
                Ada <strong class="font-mono">{{ $pendingCount }}</strong> pengajuan menunggu persetujuan.
                <a href="{{ route('admin.loans.pending') }}" class="ml-1 underline decoration-brass underline-offset-4">Buka antrian</a>
                atau filter status
                <a href="{{ route('admin.loans.index', ['status' => 'menunggu_persetujuan']) }}" class="underline decoration-brass underline-offset-4">menunggu_persetujuan</a>.
            </div>
        @endif

        <form method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="border-navy/20 focus:border-brass focus:ring-brass">
                <option value="">Semua status</option>
                @foreach (['menunggu_persetujuan', 'disetujui', 'ditolak', 'dikembalikan', 'terlambat'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <button class="rb-btn-primary">Filter</button>
        </form>

        <div class="overflow-hidden border border-navy/15 bg-white/80">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-navy/10 bg-onionskin/60 font-mono text-utility uppercase text-charcoal-muted">
                    <tr>
                        <th class="px-4 py-3">Anggota</th>
                        <th class="px-4 py-3">Buku</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Jatuh tempo</th>
                        <th class="px-4 py-3">Denda</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        <tr class="border-t border-navy/10" x-data="{ rejectOpen: false }">
                            <td class="px-4 py-3">
                                <div>{{ $loan->user->name }}</div>
                                <div class="font-mono text-[11px] text-charcoal-muted">{{ $loan->user->email }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $loan->book->title }}</td>
                            <td class="px-4 py-3"><x-stamp-badge :status="$loan->status" /></td>
                            <td class="px-4 py-3 font-mono text-utility">{{ $loan->due_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono">{{ $loan->fine_amount }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    @if ($loan->status === 'menunggu_persetujuan')
                                        <form method="POST" action="{{ route('admin.loans.approve', $loan) }}">
                                            @csrf
                                            <button class="bg-forest px-3 py-1.5 text-xs text-onionskin hover:bg-forest-soft">Setujui</button>
                                        </form>
                                        <button type="button" class="bg-rust px-3 py-1.5 text-xs text-onionskin hover:bg-rust-soft" @click="rejectOpen = true">
                                            Tolak
                                        </button>

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
                                                        <textarea name="reason" rows="4" required minlength="5" class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass" placeholder="Min. 5 karakter"></textarea>
                                                    </label>
                                                    <div class="flex gap-2 pt-2">
                                                        <button class="bg-rust px-4 py-2 text-sm text-onionskin">Kirim penolakan</button>
                                                        <button type="button" class="rb-btn-ghost" @click="rejectOpen = false">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif (in_array($loan->status, ['disetujui', 'terlambat'], true))
                                        <a
                                            href="{{ route('admin.loans.return.show', $loan) }}"
                                            class="text-navy underline decoration-brass underline-offset-4"
                                        >
                                            Kembalikan
                                        </a>
                                    @else
                                        <span class="text-charcoal-muted">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-charcoal-muted">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $loans->links() }}</div>
    </div>
</x-ruangbaca-layout>
