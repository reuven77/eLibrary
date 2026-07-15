# RuangBaca — Design Brief & Token System

> Dokumen ini adalah hasil proses *brainstorm → token system → kritik diri* sesuai frontend-design playbook. Tujuannya: memberi Cursor (dan kamu) satu sumber kebenaran visual, bukan sekadar "pakai warna bagus".

## 0. Ground the subject

- **Produk:** RuangBaca — perpustakaan digital (E-Library) untuk peminjaman buku fisik + baca e-book.
- **Audiens:** pembaca umum / civitas akademik yang terbiasa pakai OPAC perpustakaan konvensional, tapi ingin pengalaman cepat dan tidak antre.
- **Satu pekerjaan utama halaman beranda:** membuat orang **menemukan buku yang tepat dan langsung tahu status ketersediaannya** — dipinjam siapa, kapan kembali, atau bisa dibaca sekarang.
- Karena ini bukan brief institusi resmi (bukan produk STMKG), saya beri identitas mandiri: **RuangBaca**, nada hangat-akademik, bukan korporat-dingin.

## 1. Kenapa tidak pakai default AI-look

Tiga default yang saya hindari secara sadar: (1) krem hangat + serif kontras + aksen terracotta, (2) hitam pekat + satu aksen neon, (3) broadsheet hairline serba tajam. RuangBaca butuh sesuatu yang jujur ke dunianya: **perpustakaan fisik** — katalog kartu, nomor panggil (call number), stempel tanggal kembali, punggung buku berwarna per kategori. Dari situ semua keputusan warna & tipografi diturunkan.

## 2. Token — Warna

| Nama | Hex | Pemakaian |
|---|---|---|
| Navy Binding | `#1E2A47` | Warna utama — header, nav, tombol primer, teks judul di atas terang |
| Onionskin Paper | `#ECEAE2` | Background utama (abu-hangat, *bukan* krem default — lebih dingin & bertekstur kertas tipis) |
| Brass Stamp | `#B08D57` | Aksen stempel/highlight — status "tersedia", badge, garis bawah link aktif |
| Forest Spine | `#3F5D4E` | Aksen sekunder — kategori non-fiksi/edukasi, status "dipinjam" |
| Rust Spine | `#A6472E` | Aksen peringatan — status "terlambat"/denda, error state |
| Charcoal Ink | `#24211C` | Teks body (hitam hangat, bukan `#000`) |

Aturan pemakaian: **Navy + Onionskin** adalah 90% halaman. Brass/Forest/Rust **hanya** muncul sebagai penanda status/kategori (fungsional, bukan dekorasi) — ini meniru punggung buku berwarna di rak katalog nyata.

## 3. Token — Tipografi

| Peran | Font | Alasan |
|---|---|---|
| Display (judul besar) | **Newsreader** (Google Fonts), optical size besar, pakai varian italic untuk aksen | Serif yang memang dirancang untuk teks baca-panjang — cocok tematik dengan "ruang baca", tidak generik seperti Fraunces/Playfair |
| Body | **IBM Plex Sans** | Netral, sangat terbaca di ukuran kecil, terasa institusional-akademik tanpa kaku |
| Utility / data (nomor panggil, tanggal, kode transaksi) | **IBM Plex Mono** | Meniru label ketik mesin tik di kartu katalog lama — dipakai untuk nomor klasifikasi, ISBN, tanggal jatuh tempo |

Skala tipe: Display 56/40/28px (desktop/tablet/mobile), Body 16px/1.6, Utility 13px tracking +0.02em uppercase untuk label kecil.

## 4. Layout concept

Struktur yang dipakai: **eyebrow label = nomor klasifikasi asli** (mis. `028.9 · FIKSI`), bukan angka urut dekoratif (01/02/03) — karena isinya memang sebuah kode katalog yang berarti.

### Wireframe — Beranda
```
┌─────────────────────────────────────────────┐
│ RuangBaca        Katalog  Pinjaman  Masuk    │  ← nav, Navy bg
├─────────────────────────────────────────────┤
│  028.9 · TENTANG KAMI (eyebrow, mono)        │
│  Ruang baca, tanpa antre.        [Newsreader,│
│  Cari, pinjam, atau baca — semua tercatat    │  large italic]
│  rapi seperti kartu katalog, secepat klik.   │
│  [ Jelajahi Katalog ]  [ Baca Sekarang ]     │
├─────────────────────────────────────────────┤
│  Rak Pilihan — kategori sbg "punggung buku"  │
│  ▌Fiksi ▌Sains ▌Sejarah ▌Teknologi ▌Anak     │  ← blok warna tipis
├─────────────────────────────────────────────┤
│  Kartu Katalog Digital (grid 4 kolom)        │  ← signature element
│  [card][card][card][card]                    │
└─────────────────────────────────────────────┘
```

### Wireframe — Detail Buku
```
┌───────────────────┬─────────────────────────┐
│  [cover buku]      │ 813.54 · FIKSI  (mono)  │
│  (rasio kartu      │ Judul Buku Besar         │
│   katalog, sudut   │ oleh Penulis             │
│   sedikit tercetak │ ★★★★☆ (12 ulasan)        │
│   nomor panggil)   │ Status: [Stempel: TERSEDIA]│
│                    │ [ Pinjam Buku ]           │
│                    │ Sinopsis...               │
└───────────────────┴─────────────────────────┘
```

### Wireframe — Dashboard Admin
```
┌─────────────────────────────────────────────┐
│ Ringkasan: Total Buku | Dipinjam | Terlambat │  ← angka besar mono
├─────────────────────────────────────────────┤
│ Tabel Peminjaman (status berwarna sesuai     │
│ token: Forest=dipinjam, Rust=terlambat,      │
│ Brass=tersedia)                              │
└─────────────────────────────────────────────┘
```

## 5. Signature element — "Kartu Katalog Digital"

Satu elemen yang harus diingat orang dari RuangBaca: setiap buku ditampilkan sebagai **kartu katalog digital** —
- tepi atas bergaya perforasi/putus-putus tipis (border-dashed halus, bukan skeuomorphism berlebihan),
- pojok kanan atas: nomor panggil dalam `IBM Plex Mono`, seperti kode yang diketik mesin tik,
- status ketersediaan tampil sebagai **stempel** (bentuk oval tipis, warna Brass/Forest/Rust, sedikit rotasi -4deg) — bukan badge pill generik,
- saat hover/tap, kartu **flip** menampilkan sinopsis di "sisi belakang", meniru kartu katalog fisik yang punya info di kedua sisi.

Ini satu-satunya tempat animasi/flip dipakai secara sengaja. Sisanya statis dan tenang — kalau di-overuse di semua elemen, langsung terasa "AI-generated".

## 6. Aksesibilitas & motion

- Kontras teks Charcoal Ink di atas Onionskin Paper: AA+ terpenuhi.
- Semua interaksi keyboard-focusable, focus ring pakai Brass Stamp 2px.
- `prefers-reduced-motion`: matikan flip animation, ganti jadi expand-inline.
- Status warna (Forest/Rust/Brass) **selalu disertai teks/ikon**, tidak mengandalkan warna saja (untuk buta warna).

## 7. Soal 21st.dev & godly.website

- **godly.website**: dipakai sebagai arah rasa (editorial, typography-led, tenang, bukan dashboard SaaS generik) — bukan untuk dicontek 1:1. Terapkan prinsip: hierarki tipografi kuat, whitespace disiplin, satu momen interaksi yang diorkestrasi (di sini: flip kartu katalog).
- **21st.dev**: karena berbasis React/Tailwind/Radix dan proyekmu Laravel Blade, gunakan sebagai referensi **struktur markup & pola Tailwind class**, lalu porting manual ke Blade component (`resources/views/components/`) + Alpine.js untuk interaktivitas (flip card, dropdown, modal) — bukan `npx shadcn add` langsung. Detail teknisnya ada di `03-RULES.md`.
