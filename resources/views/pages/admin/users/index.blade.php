<x-ruangbaca-layout title="Manajemen User — RuangBaca">
    <x-slot name="header">
        <p class="rb-eyebrow">Admin · Pengguna</p>
        <h1 class="mt-1 font-display text-3xl text-navy">Manajemen user</h1>
    </x-slot>

    <x-flash />

    <div class="rb-container py-10">
        <div class="overflow-hidden border border-navy/15 bg-white/80">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-navy/10 bg-onionskin/60 font-mono text-utility uppercase text-charcoal-muted">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-t border-navy/10" x-data="{ resetOpen: false }">
                            <td class="px-4 py-3 text-navy">{{ $user->name }}</td>
                            <td class="px-4 py-3 font-mono text-[12px]">{{ $user->email }}</td>
                            <td class="px-4 py-3 uppercase font-mono text-utility">{{ $user->role }}</td>
                            <td class="px-4 py-3">
                                <x-stamp-badge :status="$user->is_active ? 'aktif' : 'diblokir'" />
                                @if (! $user->is_active && $user->blocked_reason)
                                    <p class="mt-1 text-xs text-charcoal-muted">{{ $user->blocked_reason }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    @can('toggleStatus', $user)
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                            @csrf
                                            <button class="text-sm underline decoration-brass underline-offset-4 text-navy">
                                                {{ $user->is_active ? 'Blokir' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-charcoal-muted">—</span>
                                    @endcan

                                    @can('resetPassword', $user)
                                        <button type="button" class="text-sm text-rust underline underline-offset-4" @click="resetOpen = true">
                                            Reset password
                                        </button>

                                        <div
                                            x-show="resetOpen"
                                            x-cloak
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-navy/40 p-4"
                                            @keydown.escape.window="resetOpen = false"
                                        >
                                            <div class="w-full max-w-md border border-navy/15 bg-onionskin p-6 shadow-card" @click.outside="resetOpen = false">
                                                <h3 class="font-display text-xl text-navy">Reset password</h3>
                                                <p class="mt-1 text-sm text-charcoal-muted">{{ $user->name }} · {{ $user->email }}</p>

                                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="mt-4 space-y-3">
                                                    @csrf
                                                    <label class="block text-sm">Password baru
                                                        <input type="password" name="password" required class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass">
                                                    </label>
                                                    <label class="block text-sm">Konfirmasi password
                                                        <input type="password" name="password_confirmation" required class="mt-1 w-full border-navy/20 focus:border-brass focus:ring-brass">
                                                    </label>
                                                    <div class="flex gap-2 pt-2">
                                                        <button class="rb-btn-primary">Simpan</button>
                                                        <button type="button" class="rb-btn-ghost" @click="resetOpen = false">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-charcoal-muted">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    </div>
</x-ruangbaca-layout>
