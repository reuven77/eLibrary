<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Bukti Peminjaman — {{ $loan->book->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Newsreader:opsz,ital,wght@6..72,0,400;6..72,0,500;6..72,1,400;6..72,1,500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --navy: #1E2A47;
            --onionskin: #ECEAE2;
            --brass: #B08D57;
            --charcoal: #24211C;
        }

        body.receipt-page {
            background: var(--onionskin);
            color: var(--charcoal);
            font-family: "IBM Plex Sans", sans-serif;
        }

        .no-print {
            /* toolbar */
        }

        .receipt-card {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border: 6px solid var(--navy);
            box-shadow: 0 8px 24px rgba(30, 42, 71, 0.08);
        }

        .receipt-inner {
            border: 1px dashed rgba(30, 42, 71, 0.35);
            margin: 12px;
            padding: 28px 32px;
        }

        .receipt-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 2px solid var(--navy);
            padding-bottom: 16px;
        }

        .receipt-brand {
            font-family: Newsreader, serif;
            font-style: italic;
            font-size: 1.75rem;
            color: var(--navy);
            line-height: 1.2;
        }

        .receipt-subtitle {
            font-family: "IBM Plex Mono", monospace;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--brass);
            margin-top: 4px;
        }

        .receipt-meta {
            text-align: right;
            font-family: "IBM Plex Mono", monospace;
            font-size: 0.75rem;
            color: #5C574E;
        }

        .receipt-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 28px;
            margin-top: 24px;
        }

        .receipt-field dt {
            font-family: "IBM Plex Mono", monospace;
            font-size: 0.68rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #5C574E;
        }

        .receipt-field dd {
            margin-top: 4px;
            font-size: 1rem;
            color: var(--navy);
            font-weight: 500;
        }

        .receipt-title {
            grid-column: 1 / -1;
        }

        .receipt-title dd {
            font-family: Newsreader, serif;
            font-size: 1.35rem;
            font-style: italic;
        }

        .receipt-footer {
            margin-top: 36px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            align-items: end;
            border-top: 1px solid rgba(30, 42, 71, 0.2);
            padding-top: 24px;
        }

        .signature-line {
            border-top: 1px solid var(--navy);
            width: 220px;
            margin-top: 48px;
            padding-top: 8px;
            font-size: 0.85rem;
            color: var(--navy);
        }

        .signature-role {
            font-family: "IBM Plex Mono", monospace;
            font-size: 0.68rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #5C574E;
        }

        .qr-box {
            text-align: center;
        }

        .qr-box img,
        .qr-box svg {
            display: inline-block;
            width: 110px;
            height: 110px;
        }

        .stamp {
            display: inline-flex;
            align-items: center;
            border: 2px solid var(--brass);
            color: var(--brass);
            font-family: "IBM Plex Mono", monospace;
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 999px;
            transform: rotate(-4deg);
        }

        @media print {
            @page {
                size: A5;
                margin: 12mm;
            }

            body.receipt-page {
                background: #fff !important;
            }

            .no-print {
                display: none !important;
            }

            .receipt-card {
                box-shadow: none;
                border-width: 5px;
                max-width: none;
            }

            .receipt-inner {
                margin: 8px;
                padding: 20px;
            }
        }
    </style>
</head>
<body class="receipt-page min-h-screen py-8">
    <div class="no-print rb-container mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('loans.index') }}" class="text-sm text-navy underline decoration-brass underline-offset-4">
            ← Kembali ke pinjaman
        </a>
        <button type="button" onclick="window.print()" class="rb-btn-primary">
            Cetak Kartu
        </button>
    </div>

    <article class="receipt-card" aria-label="Kartu bukti peminjaman">
        <div class="receipt-inner">
            <header class="receipt-header">
                <div>
                    <p class="receipt-brand">RuangBaca</p>
                    <p class="receipt-subtitle">Kartu Bukti Peminjaman</p>
                    <p class="stamp mt-3" role="status">{{ $loan->status === 'terlambat' ? 'Terlambat' : 'Disetujui' }}</p>
                </div>
                <div class="receipt-meta">
                    <div>ID LOAN</div>
                    <div>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($loan->id, 13, '')) }}</div>
                    <div class="mt-2">{{ $loan->book->call_number }}</div>
                </div>
            </header>

            <dl class="receipt-grid">
                <div class="receipt-field receipt-title">
                    <dt>Judul buku</dt>
                    <dd>{{ $loan->book->title }}</dd>
                </div>
                <div class="receipt-field">
                    <dt>Penulis</dt>
                    <dd>{{ $loan->book->author->name ?? '—' }}</dd>
                </div>
                <div class="receipt-field">
                    <dt>Nama peminjam</dt>
                    <dd>{{ $loan->user->name }}</dd>
                </div>
                <div class="receipt-field">
                    <dt>Tanggal pinjam</dt>
                    <dd class="font-mono">{{ $loan->borrowed_at?->timezone(config('app.timezone'))->format('d M Y, H:i') }}</dd>
                </div>
                <div class="receipt-field">
                    <dt>Tanggal jatuh tempo</dt>
                    <dd class="font-mono">{{ $loan->due_at?->timezone(config('app.timezone'))->format('d M Y') }}</dd>
                </div>
                <div class="receipt-field">
                    <dt>Disetujui oleh</dt>
                    <dd>{{ $loan->reviewer->name ?? 'Petugas Perpustakaan' }}</dd>
                </div>
                <div class="receipt-field">
                    <dt>Email peminjam</dt>
                    <dd class="font-mono text-sm">{{ $loan->user->email }}</dd>
                </div>
            </dl>

            <footer class="receipt-footer">
                <div>
                    <p class="signature-role">Tanda tangan digital</p>
                    <div class="signature-line">
                        {{ $loan->reviewer->name ?? 'Petugas Perpustakaan' }}
                        <div class="signature-role mt-1">Petugas Perpustakaan</div>
                    </div>
                    <p class="mt-4 text-xs text-charcoal-muted">
                        Kartu ini sah sebagai bukti peminjaman koleksi fisik RuangBaca.
                        Harap kembalikan sebelum jatuh tempo untuk menghindari denda.
                    </p>
                </div>

                <div class="qr-box">
                    {!! QrCode::size(110)->margin(0)->generate(route('loans.print', $loan)) !!}
                    <p class="mt-2 font-mono text-[10px] uppercase tracking-wider text-charcoal-muted">Verifikasi</p>
                </div>
            </footer>
        </div>
    </article>
</body>
</html>
