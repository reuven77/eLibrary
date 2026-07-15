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

Settings → Build:

| Setting | Aksi |
|---|---|
| Builder | **Dockerfile** (atau biarkan Config as Code dari `railway.toml`) |
| Custom Build Command | **KOSONGKAN / hapus** |
| Custom Start Command | **KOSONGKAN / hapus** (pakai `CMD` di Dockerfile) |

Settings → Variables:

| Variable | Nilai |
|---|---|
| `APP_KEY` | wajib |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://elibrary-production-fb6e.up.railway.app` (exact) |
| `LOG_CHANNEL` | `stderr` |
| `DATABASE_URL` / DB_* | dari plugin Postgres |
| `ASSET_URL` | **jangan di-set** |

Lalu **Redeploy**.

## Verifikasi setelah sukses

1. `https://…/build/manifest.json` → JSON 200
2. Hard refresh → CSS/JS 200 di Network tab
3. Log start: migrate + `artisan serve` jalan

## Local (opsional)

```bash
docker build -t ruangbaca .
docker run --rm -p 8000:8000 --env-file .env ruangbaca
```
