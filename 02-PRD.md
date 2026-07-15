# RuangBaca — Product Requirements Document (PRD)

## 1. Ringkasan

RuangBaca adalah aplikasi perpustakaan digital (E-Library) yang menangani dua jenis koleksi: **buku fisik** (peminjaman konvensional dengan tenggat & denda) dan **buku digital/e-book** (baca langsung di browser). Dibangun di atas Laravel + PostgreSQL, dijalankan lokal via Laragon selama development.

## 2. Tujuan produk

- Mempercepat proses cari → cek ketersediaan → pinjam/baca, tanpa perlu ke meja sirkulasi untuk cek stok.
- Mencatat seluruh transaksi peminjaman secara auditable (siapa pinjam apa, kapan, status, denda).
- Memberi admin/pustakawan dashboard untuk mengelola koleksi dan transaksi tanpa perlu akses langsung ke database.

## 3. Target pengguna

| Peran | Kebutuhan utama |
|---|---|
| **Member** (pembaca) | Cari buku, cek status, pinjam/baca, lihat riwayat & denda, beri ulasan |
| **Admin/Pustakawan** | CRUD koleksi, kelola transaksi & denda, kelola user, lihat statistik |

## 4. Tech stack (final, disesuaikan dari referensi awal)

| Layer | Pilihan | Catatan |
|---|---|---|
| Backend | **Laravel 11** (PHP 8.2+) | via Laragon lokal |
| Database | **PostgreSQL** | dikelola lewat **pgAdmin** |
| Frontend | **Blade** + **Tailwind CSS** + **Alpine.js** | bukan React — lihat `01-DESIGN.md` §7 untuk alasan |
| File storage buku digital (PDF/EPUB) | Lokal `storage/app/public` saat dev → migrasi ke S3-compatible (mis. Cloudflare R2) saat production | jangan simpan file besar di repo git |
| Auth | Laravel Breeze/Fortify (session-based, bukan token API kecuali nanti butuh mobile app) | sesuai konteks web-only saat ini |
| Queue/Scheduler | Laravel Queue + Scheduler bawaan (untuk hitung denda otomatis harian) | |
| Search | PostgreSQL full-text search (`tsvector`) untuk MVP; Meilisearch opsional fase lanjut | hindari overengineering di awal |

## 5. Skema database (PostgreSQL)

Disesuaikan dari referensi awal + aturan normalisasi 3NF dari materi Database MKG.

```
users
  id (uuid, pk)
  name, email (unique), password_hash
  role (enum: member, admin)
  created_at, updated_at

authors
  id (uuid, pk)
  name, bio

categories
  id (uuid, pk)
  name, slug (unique)

books
  id (uuid, pk)
  title, isbn (unique, nullable)
  author_id (fk -> authors)
  category_id (fk -> categories)
  cover_image_path
  file_path (nullable, untuk e-book)
  format (enum: fisik, digital, keduanya)
  stock (integer, default 0)   -- hanya relevan utk buku fisik
  synopsis (text)
  published_year (int)
  call_number (varchar)        -- nomor klasifikasi, ditampilkan di UI
  created_at, updated_at

loans   -- (dulu disebut "transactions")
  id (uuid, pk)
  user_id (fk -> users)
  book_id (fk -> books)
  borrowed_at, due_at, returned_at (nullable)
  status (enum: dipinjam, dikembalikan, terlambat, hilang)
  fine_amount (numeric(10,2), default 0)
  created_at, updated_at

reviews
  id (uuid, pk)
  user_id (fk -> users), book_id (fk -> books)
  rating (smallint, check 1-5)
  comment (text)
  created_at

favorites
  user_id (fk -> users), book_id (fk -> books)
  primary key (user_id, book_id)
```

Catatan kepatuhan aturan (lihat `03-RULES.md` untuk detail lengkap):
- Uang (`fine_amount`) pakai `NUMERIC`, **bukan** `FLOAT`.
- Semua timestamp `TIMESTAMPTZ`, disimpan UTC.
- FK & constraint **tidak boleh dihapus** demi kecepatan.
- Index eksplisit di `books.category_id`, `loans.user_id`, `loans.book_id`, `loans.status`.

## 6. Struktur folder Laravel

```
app/
  Http/
    Controllers/        # thin — hanya terima request, panggil service, return view/response
    Middleware/          # auth, role check (admin)
    Requests/             # Form Request untuk validasi server-side
  Models/
  Services/              # logika bisnis: LoanService (hitung denda, cek stok), CatalogService
  Policies/               # otorisasi per-resource (mis. hanya admin bisa hapus buku)
database/
  migrations/
  seeders/                # data dummy buku/kategori untuk dev
resources/
  views/
    components/            # Blade components: catalog-card.blade.php, stamp-badge.blade.php, dll
    layouts/
    pages/
  css/app.css               # entry Tailwind
routes/
  web.php
  admin.php                  # di-prefix /admin, middleware role:admin
```

## 7. Alur pengguna (User Flow)

### Member
1. Registrasi/Login (Laravel Breeze).
2. Jelajahi beranda → kategori → hasil pencarian (full-text search judul/penulis).
3. Buka detail buku → lihat status (stempel: tersedia/dipinjam/terlambat).
4. **Buku fisik**: klik "Pinjam" → sistem cek `stock > 0` di dalam **DB transaction** (hindari race condition dua user pinjam stok terakhir bersamaan) → buat `loans` row, `due_at` = +7 hari (dikonfigurasi).
5. **Buku digital**: klik "Baca Sekarang" → buka in-browser PDF/EPUB viewer, catat log baca (opsional MVP+1).
6. Riwayat & pengembalian: member lihat status, admin yang mengonfirmasi pengembalian fisik.
7. Beri ulasan setelah status `dikembalikan` atau setelah membaca e-book.

### Admin/Pustakawan
1. Login → dashboard (`/admin`): total buku, sedang dipinjam, terlambat, total denda outstanding.
2. CRUD buku (upload cover + file digital jika ada), kelola stok.
3. Kelola peminjaman: konfirmasi pengembalian fisik, lihat/reset denda.
4. Kelola user: blokir user yang menunggak.

## 8. Fitur — MVP vs Fase Lanjut

**MVP (wajib jalan dulu):**
- Auth + role (member/admin)
- CRUD buku & kategori (admin)
- Cari & filter katalog
- Pinjam/kembalikan buku fisik dengan validasi stok atomik
- Baca e-book in-browser (viewer sederhana)
- Dashboard admin dasar

**Fase lanjut:**
- Denda otomatis via `php artisan schedule` harian (job cek `due_at < now() AND status = 'dipinjam'`)
- Ulasan & rating
- Favorit/bookmark
- Rekomendasi sederhana (co-borrow pattern)
- Full-text search upgrade ke Meilisearch
- Badge/gamifikasi

## 9. Non-functional requirements

- **Keamanan**: parameterized query (Eloquent/query builder — dilarang string concatenation raw SQL), password hash Argon2id/bcrypt (default Laravel sudah bcrypt — jangan diturunkan), CSRF protection bawaan Laravel tetap aktif, role check di middleware **dan** Policy (defense in depth).
- **Performa**: query list katalog wajib pagination (jangan `SELECT *` tanpa limit), index sesuai §5.
- **Observability**: gunakan Laravel log channel + query log di staging untuk cek N+1 query.
- **Backup**: dump PostgreSQL terjadwal (pg_dump) minimal harian saat sudah production, uji restore berkala.

## 10. Fase pengembangan (disesuaikan timeline SDLC)

| Fase | Isi |
|---|---|
| 1. Setup | Laragon + Laravel + PostgreSQL connect, migration awal, repo Git, `.env` per environment |
| 2. Backend inti | Auth, model + migration semua tabel §5, CRUD buku (admin), Service layer peminjaman |
| 3. Frontend | Blade layout + Tailwind sesuai `01-DESIGN.md`, komponen kartu katalog, halaman katalog/detail/dashboard |
| 4. Integrasi & fitur peminjaman | Alur pinjam/kembali, validasi stok atomik, halaman riwayat |
| 5. Testing | Unit test `LoanService` (kasus stok habis, race condition), feature test route utama |
| 6. Deploy | Siapkan environment production (VPS/hosting PHP + managed PostgreSQL), migrasi storage file ke cloud |

## 11. Pertanyaan terbuka (perlu kamu putuskan, tandai di Cursor jika belum jelas)

- Apakah e-book viewer perlu proteksi anti-download (DRM ringan) atau cukup ditampilkan biasa?
- Apakah member bisa pinjam >1 buku fisik sekaligus? Berapa batas maksimal?
- Skema denda: nominal per hari terlambat berapa? Ada batas maksimal denda?
