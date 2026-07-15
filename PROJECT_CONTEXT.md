# RuangBaca — Project Context (Handoff)

> Dokumen handoff untuk AI / developer lanjutan.  
> Sumber kebenaran desain/produk/aturan: `01-DESIGN.md`, `02-PRD.md`, `03-RULES.md`, `.cursor/rules/ruangbaca.mdc`.  
> Jika instruksi chat bertentangan dengan `03-RULES.md`, **tanya dulu** — jangan diam-diam melanggar (terutama raw SQL, `DB::transaction`+`lockForUpdate` untuk stok, validasi Form Request).

---

## 1. Tech stack & versions

| Layer | Teknologi | Versi / catatan |
|---|---|---|
| Runtime | PHP | **8.3+** (dev dipakai Laragon **8.4.12**). PATH Windows sering fallback ke XAMPP 8.0 — pakai `C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe` |
| Framework | Laravel | **^13.8** (installed ~13.20). PRD menyebut Laravel 11; keputusan: tetap Laravel 13 |
| Auth | Laravel Breeze | **^2.4** — Blade stack (bukan Inertia/React); UI auth sudah di-restyle RuangBaca |
| Database | PostgreSQL | **17.x** — DB name: `ruangbaca` |
| Frontend | Blade + Tailwind CSS + Alpine.js | Tailwind **3.4** via PostCSS; Alpine **^3.4**; Vite **^8** |
| Fonts | Google Fonts | Newsreader, IBM Plex Sans, IBM Plex Mono |
| Storage | Local disk `public` | Cover, e-book, **foto KTP** di `storage/app/public` (`php artisan storage:link`) |
| QR | `simplesoftwareio/simple-qrcode` | Cetak kartu bukti pinjaman |
| Test | PHPUnit | **^12.5** — testing DB: SQLite `:memory:` (`phpunit.xml`) |
| Local env | Laragon | Web via Laragon **atau** `php artisan serve` |

**Bukan** React / Inertia / Livewire. Referensi 21st.dev hanya untuk pola markup → port manual ke Blade + Alpine.

---

## 2. Database schema (tabel + relasi)

Semua PK domain = **UUID**. Timestamp bisnis = **`timestamptz`**. Uang = **`numeric(10,2)`**.

```
users ─────────┬────────── loans ────────── books
  role: member│admin      status:          │
  is_active   │           menunggu_… /     ├── authors
  blocked_…   ├────────── reviews         ├── categories
               └────────── favorites (M2M)  │
                                           format: fisik|digital|keduanya
                                           stock (fisik)
                                           call_number, isbn, file_path, cover_image_path
```

| Tabel | Kolom utama | Relasi |
|---|---|---|
| `users` | `id` uuid, `name`, `email`, `password`, `role` (member\|admin), `is_active`, `blocked_reason` | hasMany `loans`, `reviews`; belongsToMany `books` via `favorites` |
| `authors` | `id`, `name`, `bio` | hasMany `books` |
| `categories` | `id`, `name`, `slug` unique | hasMany `books` |
| `books` | `title`, `isbn`, `author_id`, `category_id`, `format`, `stock`, `synopsis`, `published_year`, `call_number`, `cover_image_path`, `file_path` | belongsTo author/category; hasMany loans/reviews |
| `loans` | `user_id`, `book_id`, `borrowed_at`, `due_at`, `returned_at`, `status`, `fine_amount`, `rejection_reason`, `reviewed_by`, **`borrower_phone`**, **`borrower_address`**, **`id_card_path`**, **`borrower_notes`** | belongsTo user, book, reviewer |
| `reviews` | `user_id`, `book_id`, `rating` 1–5, `comment`, `created_at` only | unique (user_id, book_id) |
| `favorites` | composite PK `(user_id, book_id)`, `created_at` | pivot |
| (+ Breeze) | `sessions`, `password_reset_tokens`, `cache`, `jobs`, … | — |

**Status `loans`:** `menunggu_persetujuan` → `disetujui` / `ditolak` → (aktif) `terlambat` → `dikembalikan`.

**Index wajib (sudah di migration):** `books.category_id`, `books.author_id`, `loans.user_id`, `loans.book_id`, `loans.status`, `loans.due_at`.

**Config pinjam/denda** (`config/ruangbaca.php` + `.env`):
- `RUANGBACA_LOAN_PERIOD_DAYS=7`
- `RUANGBACA_MAX_ACTIVE_LOANS=3`
- `RUANGBACA_FINE_PER_DAY=2000.00`
- `RUANGBACA_MAX_FINE=50000.00`

---

## 3. Fitur yang sudah dibuat

### Backend
- [x] Auth Breeze (login/register/logout/profile) + kolom `role`
- [x] Middleware `role:admin` (`EnsureUserHasRole`) pada prefix `/admin`
- [x] Policies: `BookPolicy`, `LoanPolicy`, `UserPolicy` (defense in depth bersama middleware)
- [x] Models + factories + `CatalogSeeder` (~20 buku, 5 kategori, 11 penulis)
- [x] **`LoanService`**
  - `meminjamBuku` — buat pengajuan + simpan identitas (HP, alamat, KTP, catatan); **belum** kurangi stok
  - `approveLoan` / `rejectLoan` — stok turun hanya saat setuju (`DB::transaction` + `lockForUpdate`)
  - `kembalikanBuku` / `hitungDenda` / `tandaiTerlambat`
- [x] Scheduler: `php artisan ruangbaca:mark-overdue` (jadwal harian `01:00` di `routes/console.php`)
- [x] `CatalogService`, `BookAdminService` (CRUD + upload cover/ebook; **penulis via teks** → create-or-reuse), `AdminDashboardService`, `UserAdminService` (blokir + reset password)
- [x] Form Requests: catalog, review, store/update book, **StoreLoanRequest**, RejectLoan, ResetUserPassword

### Frontend (desain RuangBaca / `01-DESIGN.md`)
- [x] Token warna Tailwind: `navy`, `onionskin`, `brass`, `forest`, `rust`, `charcoal`
- [x] Layout `<x-ruangbaca-layout>`, nav navy `<x-site-nav>` (admin: **Antrian** + badge, **Peminjaman**, **Admin**)
- [x] `<x-catalog-card>` — sampul **portrait full**; **kategori kiri atas** + **status kanan atas**; flip Alpine; stagger masuk saat scroll; hover lift + zoom cover
- [x] `<x-stamp-badge>` status stempel
- [x] Halaman: beranda, katalog, detail, **form pinjam + identitas**, pinjaman member, cetak kartu (QR), e-book viewer, admin dashboard / books / users / **antrian approve** / **konfirmasi pengembalian + estimasi denda**
- [x] Auth UI: `layouts/guest.blade.php` split — panel navy dekoratif (spine + stempel) + form kartu katalog (`/login`, `/register`)

### Motion & UI polish (eyecatching)
- [x] **Landing hero** full-bleed navy: brand shimmer (`rb-brand-impact`), glow orbit, grid drift, spine float liar, stamp drift, reveal blur→tajam (`rb-reveal`)
- [x] **Katalog**: banner `rb-catalog-banner`, chip kategori `rb-shelf-chip`, kartu `revealOnScroll` + `--stagger`
- [x] **Tombol animatif**: `rb-btn` / `rb-btn-primary` / `rb-btn-ghost` — hover naik + kilau, active mengecil; submit/admin buttons ikut bounce ringan
- [x] Alpine `revealOnScroll` di `resources/js/app.js` (IntersectionObserver)
- [x] Semua motion hormati `prefers-reduced-motion: reduce` (animasi dimatikan)
- [x] Setelah ubah CSS/JS: **`npm run build`** (atau `npm run dev`) — class arbitrary/baru di `app.css` wajib di-build agar tidak collapse layout

### Alur sirkulasi (ringkas)
1. Member → detail buku → **Ajukan pinjaman** → isi HP, alamat, foto KTP → status `menunggu_persetujuan`
2. Admin → **Antrian** / `admin/loans/pending` → Setujui (stok −1, `due_at`) atau Tolak (+ alasan); identitas + foto KTP tampil di antrian
3. Member → cetak kartu bukti (hanya `disetujui` / `terlambat`)
4. Admin → **Peminjaman** → **Kembalikan** → halaman konfirmasi (hari terlambat + estimasi denda) → stok +1, status `dikembalikan`

### Routes (ringkas)
| Area | Path |
|---|---|
| Publik | `/`, `/katalog`, `/katalog/{book}` |
| Member | `/pinjaman`, `GET/POST /katalog/{book}/pinjam`, `POST .../ulasan`, `/baca/{book}`, `/pinjaman/{loan}/cetak` |
| Admin | `/admin`, `/admin/books/*`, `/admin/users`, `/admin/loans`, `/admin/loans/pending`, `GET/POST .../return`, `POST .../approve|reject` |
| Auth | `/login`, `/register` (layout guest dekoratif) |

### Testing
- [x] Unit: `tests/Unit/LoanServiceTest.php`
- [x] Feature: `LoanFlowTest` (pinjam + identitas + return), `AdminBookCrudTest` (termasuk penulis baru), Breeze auth
- [x] Suite terakhir: **47 tests passed**

### Akun seed
| Email | Password | Role |
|---|---|---|
| `admin@ruangbaca.test` | `password` | admin |
| `member@ruangbaca.test` | `password` | member |

---

## 4. Fitur yang belum dibuat (TODO)

### Gap MVP / PRD yang masih longgar
- [ ] **CRUD kategori** admin (sekarang hanya seeder + filter + chip UI)
- [ ] **CRUD penulis** standalone (sekarang create-or-reuse dari form buku)
- [ ] **Reset / bayar denda** dari UI admin (denda dihitung & dicatat saat return)
- [ ] E-book file nyata di storage untuk seed (viewer 404 jika belum di-upload)
- [ ] Log baca e-book (PRD: opsional MVP+1)
- [ ] DRM / anti-download e-book (pertanyaan terbuka PRD §11)

### Fase lanjut (PRD §8)
- [ ] Favorit/bookmark (tabel + model pivot ada; belum route/UI)
- [ ] Ulasan lebih ketat (eligibility peminjam digital)
- [ ] Full-text search PostgreSQL `tsvector` / Meilisearch
- [ ] Rekomendasi co-borrow, gamifikasi
- [ ] Deploy production + storage S3/R2 + backup `pg_dump`
- [ ] Pastikan scheduler jalan di production (`php artisan schedule:work` / cron)

### Ops / kualitas
- [ ] Init Git repo + branch convention (`main` / `develop` / `feature/*`) — workspace awal bukan git repo
- [ ] Pastikan hanya **satu** `php artisan serve` (pernah kena `FATAL: too many clients already`)
- [ ] Set PHP Laragon aktif ke 8.4 di Menu Laragon (hindari XAMPP 8.0 di PATH)

---

## 5. Konvensi coding yang dipakai

### Wajib (`03-RULES.md` + Cursor rule)
1. Eloquent / Query Builder + parameter binding — **jangan** concat raw SQL dengan input user.
2. Controller **thin** → panggil `app/Services/*`.
3. Listing **wajib** `paginate()`; pilih kolom eksplisit untuk list katalog (`Book::scopeSelectCatalogColumns`).
4. Kurangi stok hanya di dalam `DB::transaction()` + `lockForUpdate()` (saat **approve**, bukan saat ajukan).
5. Validasi server-side lewat **Form Request**.
6. Role check: middleware **dan** Policy.
7. CSRF **jangan** dimatikan.
8. Jangan commit `.env` / kredensial.
9. Token warna/tipografi **hanya** dari `01-DESIGN.md` — jangan improvisasi palette baru (hindari ungu SaaS / cream+terracotta AI-slop).
10. UI berulang = Blade component di `resources/views/components/`.
11. **Motion**: landasan flip kartu katalog + animasi landing/katalog/tombol di `app.css`; selalu sediakan fallback `prefers-reduced-motion`. Tinggi/`layout` kritis kartu katalog pakai class di `app.css` (jangan andalkan arbitrary Tailwind tanpa rebuild).

### Catatan penting controller Laravel 13
- Base `Controller` **tidak** punya `$this->user()` — pakai `auth()->user()` / `$request->user()` / `auth()->id()`.

### Struktur folder penting
```
app/Http/Controllers/          # publik + Admin/
app/Services/                  # LoanService, CatalogService, BookAdminService, AdminDashboardService, UserAdminService
app/Policies/
app/Console/Commands/MarkOverdueLoansCommand.php
app/Exceptions/LoanException.php
app/View/Components/GuestLayout.php
config/ruangbaca.php
resources/css/app.css          # tokens komponen + motion (rb-*, catalog-card-*)
resources/js/app.js            # Alpine + revealOnScroll
resources/views/layouts/guest.blade.php
resources/views/pages/         # home, catalog, loans (create+index), ebooks, admin/*, member/loan-receipt
resources/views/auth/          # login, register (copy ID + style RuangBaca)
resources/views/components/    # catalog-card, stamp-badge, site-nav, category-shelf, ruangbaca-layout
routes/web.php, routes/admin.php, routes/console.php
database/seeders/CatalogSeeder.php
database/migrations/2026_07_15_000003_add_borrower_identity_to_loans_table.php
```

### Catatan lokal yang sudah diputuskan
- Session/cache lokal: **`file`** (bukan `database`) — mengurangi kehabisan koneksi PG.
- Queue lokal: **`sync`**.
- Soft-default PRD §11: max 3 pinjaman aktif; denda Rp 2.000/hari; cap Rp 50.000.
- Penulis di form buku: input teks `author_name` (bukan select-only).
- Pengajuan admin: link **Antrian** di nav (bukan “Pinjaman” member).

---

## 6. Cara menjalankan proyek

### Prasyarat
- Laragon (PHP 8.4 + Composer) atau PHP 8.3+/8.4 di PATH
- PostgreSQL 17 jalan; database `ruangbaca` sudah ada
- Node.js (untuk Vite)

### Setup sekali
```bash
cd c:\laragon\www\elibrary

# Pastikan PHP 8.4
php -v

composer install
cp .env.example .env   # jika belum; isi DB_* PostgreSQL
php artisan key:generate

# .env penting:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=ruangbaca
# DB_USERNAME=...
# DB_PASSWORD=...
# SESSION_DRIVER=file
# CACHE_STORE=file
# QUEUE_CONNECTION=sync

php artisan migrate
php artisan db:seed
php artisan storage:link

npm install
npm run build
# atau untuk hot reload: npm run dev
```

### Menjalankan harian
```bash
# Opsi A — Laragon virtual host (elibrary.test / sesuai config Laragon)

# Opsi B — built-in server (HANYA SATU instance)
php artisan serve
# lalu buka http://127.0.0.1:8000

# Setelah ubah CSS/JS motion: npm run build (atau biarkan npm run dev jalan)
```

### Tes
```bash
php artisan test
```

### Troubleshooting koneksi PG “too many clients”
1. Tutup semua `php artisan serve` / proses `php.exe` berlebih.
2. Restart service PostgreSQL Windows (`postgresql-x64-17`).
3. Pastikan `SESSION_DRIVER=file` dan `CACHE_STORE=file`.
4. `php artisan config:clear`.

### Troubleshooting UI / motion
1. Layout katalog collapse / class hilang → `npm run build`, lalu hard refresh browser (`Ctrl+F5`).
2. Cover/foto KTP 404 → `php artisan storage:link`.
3. Kartu tetap transparan → pastikan JS Alpine load; cek console; `revealOnScroll` menambahkan class `is-visible`.

### Dokumen terkait
| File | Isi |
|---|---|
| `01-DESIGN.md` | Token visual, wireframe, kartu katalog |
| `02-PRD.md` | Requirement, schema, alur user |
| `03-RULES.md` | Aturan coding & keamanan |
| `.cursor/rules/ruangbaca.mdc` | Cursor always-apply rules |
| `PROJECT_CONTEXT.md` | Dokumen ini |

---

## 7. Progress log (ringkas)

| Tanggal | Item |
|---|---|
| 2026-07-14–15 | Setup → model → LoanService → routes → UI → tests; session/cache → file |
| 2026-07-15 | Alur approve/reject; manajemen user; cetak kartu QR; cover upload; penulis text input |
| 2026-07-15 | Pengembalian (konfirmasi + denda); cover list katalog; identitas pinjam (HP, alamat, KTP); scheduler overdue |
| 2026-07-15 | Fix layout kartu katalog (tinggi di `app.css`); kategori kiri / status kanan; sampul portrait full |
| 2026-07-15 | Landing + auth dekoratif; motion ekstrem landing/katalog; tombol animatif; Alpine `revealOnScroll` |

---

*Last updated: 2026-07-15 — sync handoff setelah polish motion & UI (landing, katalog, tombol, auth).*
