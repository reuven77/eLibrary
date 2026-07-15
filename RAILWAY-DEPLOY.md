# Railway Deploy — Vite Assets Checklist (RuangBaca)

## Root cause (paling sering)

`public/build` **di-gitignore**. Production hanya punya CSS/JS jika Railway menjalankan `npm run build` sukses.  
Perintah **`npm install --omit=dev` salah** untuk proyek ini: Vite & Tailwind ada di `devDependencies`, jadi build bisa gagal / tanpa assets.

## Yang sudah diperbaiki di repo

| File | Perubahan |
|---|---|
| `vite.config.js` | `outDir: public/build` + `manifest: 'manifest.json'` |
| `nixpacks.toml` | `npm ci` → `npm run build` → assert `manifest.json` + PHP 8.4 extensions |
| `railway.toml` | builder NIXPACKS |
| Layouts Blade | sudah memakai `@vite([...])` (ruangbaca-layout, guest, app) |
| `.env.example` | catatan production Railway + larangan `ASSET_URL` |

## Railway Variables (wajib)

| Variable | Nilai |
|---|---|
| `APP_KEY` | dari `php artisan key:generate --show` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://elibrary-production-fb6e.up.railway.app` (exact domain) |
| `LOG_CHANNEL` | `stderr` |
| `DB_*` / `DATABASE_URL` | dari plugin Postgres (`${{Postgres.DATABASE_URL}}`) |
| `SESSION_DRIVER` | `file` (atau `database` bila disiapkan) |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `ASSET_URL` | **jangan di-set** |

## Build / Start

Build diatur di **`nixpacks.toml`** (bukan custom Build Command yang `omit=dev`).

Jika mengisi Manual Build Command di dashboard, pakai ini:

```bash
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader && npm ci && npm run build && test -f public/build/manifest.json
```

Pre-Deploy / Start (sudah di `nixpacks.toml` start):

```bash
php artisan storage:link --force && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=$PORT
```

## Local verify

```bash
npm ci
npm run build
# Windows PowerShell:
Test-Path public\build\manifest.json
Get-Content public\build\manifest.json
```

## Deploy checklist

- [ ] `npm run build` lokal sukses; `public/build/manifest.json` ada
- [ ] `vite.config.js` punya manifest + outDir
- [ ] Layout utama punya `@vite(...)`
- [ ] `nixpacks.toml` tidak memakai `--omit=dev`
- [ ] Railway `APP_URL` = domain production exact
- [ ] Railway **tidak** set `ASSET_URL`
- [ ] Push + redeploy; tunggu sukses
- [ ] Buka `/build/manifest.json` → 200 JSON
- [ ] DevTools Network: CSS/JS 200; hard refresh

## Debug cepat

1. `https://elibrary-production-fb6e.up.railway.app/build/manifest.json` → 404? Build gagal / file tidak masuk image.
2. HTML mengarah ke `http://127.0.0.1:5173`? Ada file `public/hot` di image — hapus hot, jangan deploy file hot.
3. CSS 404 path aneh domain? Cek `APP_URL` vs domain aktual.
