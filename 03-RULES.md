# RuangBaca — Development Rules

> Ini adalah aturan wajib untuk siapapun (termasuk Cursor/AI) yang menulis kode di proyek ini. Disarikan dari materi Database MKG & PSI, diadaptasi khusus untuk Laravel + PostgreSQL. Simpan file ini di root project; kalau pakai Cursor Rules native, salin isi bagian bertanda `[CURSOR RULE]` ke `.cursor/rules/ruangbaca.mdc`.

## 1. Database & Migration

- **Boleh**: semua tabel wajib punya migration file, tidak ada perubahan schema manual lewat pgAdmin di environment yang di-share tim.
- **Boleh**: normalisasi hingga 3NF untuk tabel transaksional (`loans`, `books`, dst). Denormalisasi hanya kalau ada bukti masalah performa nyata, bukan asumsi.
- **Boleh**: gunakan tipe data tepat — `NUMERIC` untuk uang (`fine_amount`), `TIMESTAMPTZ` untuk semua waktu, `UUID` untuk primary key.
- **Tidak boleh**: menghapus foreign key/constraint demi "biar gampang seed data".
- **Tidak boleh**: kolom `VARCHAR(255)` untuk semua teks tanpa mikir — sesuaikan panjang wajar (mis. `title varchar(500)`, `synopsis text`).
- **Wajib**: index eksplisit di setiap kolom FK dan kolom yang sering di-`WHERE`/`ORDER BY` (lihat `02-PRD.md` §5).

## 2. Query & Eloquent

- **Wajib**: semua akses data lewat Eloquent ORM atau Query Builder Laravel — **dilarang keras** raw string concatenation ke SQL (`DB::raw("... $variable")` dengan variabel user-input langsung).
- Kalau terpaksa butuh raw query, wajib pakai parameter binding: `DB::select('... where id = ?', [$id])`.
- **Tidak boleh** `SELECT *` di query production — pilih kolom eksplisit, apalagi untuk listing katalog yang bisa banyak baris.
- **Wajib** pagination di semua listing (`->paginate()`), tidak pernah `->get()` semua baris tabel `books`/`loans` tanpa batas.
- Operasi peminjaman (cek stok → kurangi stok → buat record `loans`) **wajib** dibungkus `DB::transaction()` untuk mencegah race condition dua user meminjam stok terakhir bersamaan. Gunakan `lockForUpdate()` saat baca `stock`.

## 3. Backend / Controller / Service

- Controller **thin**: hanya terima Request, panggil Service/Model, kembalikan response. Logika bisnis (hitung denda, cek stok, validasi aturan pinjam) hidup di `app/Services/`, bukan di Controller.
- Validasi input **selalu** server-side lewat Laravel Form Request class — jangan hanya andalkan validasi client-side/JS.
- **Tidak boleh** mengembalikan stack trace atau pesan error SQL mentah ke user — gunakan Laravel exception handler, tampilkan pesan generik ke user, log detail ke server.
- Role check admin **di dua tempat**: middleware route (`->middleware('role:admin')`) dan Policy (`BookPolicy`, `LoanPolicy`) — bukan cuma cek `if ($user->role === 'admin')` tersebar di view.

## 4. Keamanan

- Password: pakai hashing bawaan Laravel (bcrypt/Argon2id) — **jangan pernah** custom hashing sendiri atau simpan plaintext.
- Session: gunakan session cookie HttpOnly bawaan Laravel — jangan simpan token sensitif di `localStorage`.
- CSRF protection Laravel **tidak boleh dimatikan** di form manapun kecuali endpoint API publik yang memang stateless dan sudah dilindungi cara lain.
- Rate limiting wajib di route login dan endpoint pencarian publik (`throttle` middleware) untuk cegah brute force/abuse.
- Environment secret (`DB_PASSWORD`, dsb) **hanya** di `.env`, tidak pernah di-commit — pastikan `.env` ada di `.gitignore` sejak commit pertama.
- Data PII (email, nama) tidak boleh dipakai sebagai data dummy di environment non-prod tanpa masking — pakai Faker untuk seeder.

## 5. Frontend (Blade + Tailwind + Alpine.js)

- Ikuti token warna/tipografi di `01-DESIGN.md` — jangan improvisasi warna baru di luar tabel token.
- Struktur komponen: elemen berulang (kartu katalog, badge stempel status, nav) wajib jadi Blade component (`resources/views/components/`), bukan copy-paste HTML di tiap halaman.
- Kalau mengambil referensi pola dari 21st.dev: **porting manual** markup + Tailwind class ke Blade component, ganti interaktivitas React (`useState`, dsb) dengan Alpine.js (`x-data`, `x-on:click`). Jangan biarkan JSX mentah tertinggal di file Blade.
- Alternatif (kalau nanti proyek dipindah ke Inertia+React supaya komponen 21st.dev bisa dipasang langsung via `npx shadcn add`): itu perubahan arsitektur besar, bukan tempelan — harus didiskusikan ulang sebelum dieksekusi oleh Cursor, jangan diputuskan sepihak oleh AI di tengah jalan.
- Motion terbatas: hanya elemen signature (flip kartu katalog) yang dianimasikan. Hormati `prefers-reduced-motion`.

## 6. Testing

- Setiap fitur peminjaman wajib ada test kasus: stok habis (harus gagal dengan pesan jelas), dua request bersamaan meminjam stok terakhir (harus salah satu gagal, bukan dua-duanya sukses), buku terlambat (status berubah otomatis).
- Feature test minimal untuk: login/register, CRUD buku (admin only, ditolak untuk member), alur pinjam-kembalikan penuh.

## 7. Git & Deployment

- Branch: `main` (stabil), `develop`, `feature/nama-fitur`.
- Tidak ada migration yang di-edit setelah pernah di-push & dijalankan tim lain — buat migration baru untuk perubahan schema lanjutan.
- Sebelum deploy: pastikan backup database terbaru ada, migration sudah dites di staging, ada rencana rollback.

---

### [CURSOR RULE] — versi ringkas untuk `.cursor/rules/ruangbaca.mdc`

```
- Selalu gunakan Eloquent/Query Builder dengan parameter binding, tidak pernah raw string concatenation SQL.
- Controller harus thin; logika bisnis wajib di app/Services/.
- Semua listing data wajib pakai pagination, tidak pernah SELECT * tanpa batas.
- Operasi kurangi stok buku wajib dalam DB::transaction() + lockForUpdate().
- Validasi input selalu lewat Form Request server-side.
- Ikuti token warna & tipografi di 01-DESIGN.md, jangan buat warna baru.
- Komponen UI berulang wajib jadi Blade component, bukan HTML tercopy-paste.
- Jangan matikan CSRF protection.
- Jangan commit .env atau kredensial apapun.
```
