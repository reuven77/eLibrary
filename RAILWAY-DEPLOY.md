# Railway Deploy — RuangBaca

## Penyebab build gagal (dari log terakhir)

1. **Custom Build Command di Railway** menimpa nixpacks:
   `composer install ... && npm install --omit=dev && npm run build`  
   → Vite/Tailwind di `devDependencies` jadi hilang / build rusak.
2. **Konflik Nixpacks Node**: `providers = ["php","node"]` + `nodejs_22` dobel →
   `Unable to build profile... conflict ... nodejs-22.12.0` vs `nodejs-22.11.0`.

## Perbaikan di repo

- **`Dockerfile`** multi-stage (Node 22 build Vite → PHP 8.4 runtime)
- **`railway.toml`** → `builder = "DOCKERFILE"`
- **`.dockerignore`** agar image ramping
- `nixpacks.toml` disederhanakan (cadangan saja)

## WAJIB di Railway Dashboard

Error `ServeCommand.php ... Unsupported operand types: string + int` artinya
Railway masih menjalankan `php artisan serve` (Custom Start Command lama),
bukan `docker/start.sh`.

Settings → Build / Deploy:

| Setting | Aksi |
|---|---|
| Builder | **Dockerfile** (Config as Code dari `railway.toml`) |
| Custom Build Command | **KOSONGKAN / hapus** |
| Custom Start Command | **KOSONGKAN** atau set ke: `/bin/sh ./docker/start.sh` |

Settings → Variables:

| Variable | Nilai |
|---|---|
| `APP_KEY` | wajib |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | **`https://…`** (bukan `http://`) — kalau `http`, CSS/JS diblokir mixed content |
| `LOG_CHANNEL` | `stderr` |
| `DATABASE_URL` / DB_* | dari plugin Postgres |
| `ASSET_URL` | **jangan di-set** |

Halaman putih tanpa style + teks tumpang tindih = CSS tidak masuk. Cek Network:
asset harus `https://…/build/assets/….css` (bukan `http://`).

Lalu **Redeploy**.

## Verifikasi setelah sukses

1. `https://…/build/manifest.json` → JSON 200
2. Hard refresh → CSS/JS 200 di Network tab
3. Log start: migrate + `php -S` (bukan `artisan serve`) jalan

## Local (opsional)

```bash
docker build -t ruangbaca .
docker run --rm -p 8000:8000 --env-file .env ruangbaca
```
