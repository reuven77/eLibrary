@props([
    'status' => 'tersedia',
    'label' => null,
])

@php
    $status = strtolower((string) $status);

    $map = [
        'tersedia' => ['class' => 'border-brass text-brass', 'text' => 'Tersedia'],
        'menunggu_persetujuan' => ['class' => 'border-brass text-brass', 'text' => 'Menunggu'],
        'disetujui' => ['class' => 'border-forest text-forest', 'text' => 'Disetujui'],
        'dipinjam' => ['class' => 'border-forest text-forest', 'text' => 'Dipinjam'],
        'ditolak' => ['class' => 'border-rust text-rust', 'text' => 'Ditolak'],
        'terlambat' => ['class' => 'border-rust text-rust', 'text' => 'Terlambat'],
        'dikembalikan' => ['class' => 'border-charcoal-muted text-charcoal-muted', 'text' => 'Dikembalikan'],
        'aktif' => ['class' => 'border-forest text-forest', 'text' => 'Aktif'],
        'diblokir' => ['class' => 'border-rust text-rust', 'text' => 'Diblokir'],
    ];

    $meta = $map[$status] ?? $map['tersedia'];
    $text = $label ?? $meta['text'];
@endphp

<span
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center rounded-[100%] border-2 px-3 py-1 font-mono text-[11px] uppercase tracking-[0.08em] -rotate-[4deg] '.$meta['class'],
        'role' => 'status',
        'aria-label' => 'Status: '.$text,
    ]) }}
>
    <span aria-hidden="true" class="mr-1.5 text-[10px]">●</span>
    {{ $text }}
</span>
