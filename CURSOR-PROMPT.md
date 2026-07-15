# Cursor Prompt: Fix Vite Assets Loading di Railway

Salin semuanya dan paste ke Cursor AI chat, atau ke `.cursor/rules` untuk persistent rules.

---

## **Untuk Chat Cursor (Copy-paste ini ke chat)**

```
Saya punya project Laravel 13 + Vite + Tailwind di Railway, tapi styling tidak load di production.
HTML render tapi CSS/JS assets 404 atau tidak include.

Masalah: manifest.json tidak ter-generate atau tidak ter-serve.

Bantuan yang saya butuhkan:
1. Review & fix vite.config.js - pastikan manifest: true ada
2. Review & fix Blade template (app.blade.php) - pastikan @vite() tag ada
3. Review & fix Build Command untuk Railway
4. Review & fix Pre-Deploy Command untuk Railway  
5. Review & fix Environment Variables di Railway
6. Buat checklist apa yang perlu diverifikasi sebelum deploy

Context:
- Project path: /path/to/elibrary
- Production URL: https://elibrary-production-fb6e.up.railway.app
- Database: PostgreSQL via Railway plugin
- Framework: Laravel 11/13
- Build tool: Vite with Tailwind CSS
- Deploy: Railway (using Railpack builder)

Lihat juga file-file yang saya upload:
- vite.config.js
- app.blade.php (atau layout utama)
- package.json
- composer.json

Please:
- Identify root cause why assets not loading
- Suggest exact fixes untuk setiap file
- Provide exact commands untuk local testing
- Provide exact Railway dashboard settings
```

---

## **Untuk .cursor/rules (Persistent Instructions)**

Buat file `.cursor/rules` di root project dengan isi:

```markdown
# Railway Vite Assets Fix Rules

## Context
- Project: Laravel 13 library system (RuangBaca)
- Deployed to: Railway with Railpack builder
- Issue: Vite assets (CSS/JS) not loading in production

## Rules for Code Review

### 1. Vite Configuration
When reviewing vite.config.js:
- [ ] Check `manifest: true` exists in build config
- [ ] Check `outDir: 'public/build'` is set
- [ ] Check laravel plugin is configured with correct input paths
- [ ] Ensure no ASSET_URL override in build

Example correct config:
```javascript
build: {
    outDir: 'public/build',
    manifest: true,
}
```

### 2. Blade Template
When reviewing app.blade.php or main layout:
- [ ] Check @vite() tag exists in <head>
- [ ] Verify paths match: ['resources/css/app.css', 'resources/js/app.js']
- [ ] Ensure tag is NOT in conditional block that might skip it

Correct usage:
```blade
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

### 3. Build Process
When setting up Build/Deploy commands:

BUILD COMMAND (must succeed without errors):
```bash
composer install --no-dev --optimize-autoloader && npm install --omit=dev && npm run build
```

PRE-DEPLOY COMMAND (runs after build, before start):
```bash
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force
```

### 4. Environment Variables
Critical variables for production:
- APP_KEY: must be set (run: php artisan key:generate --show)
- APP_ENV: must be "production"
- APP_DEBUG: must be "false"
- APP_URL: must EXACTLY match production domain (https://elibrary-production-fb6e.up.railway.app)
- LOG_CHANNEL: must be "stderr" (Railway ephemeral filesystem)
- DB_URL: use ${{Postgres.DATABASE_URL}}

DO NOT set ASSET_URL in production - leave empty/unset

### 5. Verification Checklist
Before deploying:
- [ ] public/build/manifest.json exists locally
- [ ] npm run build completes without errors
- [ ] vite.config.js has manifest: true
- [ ] @vite() tag in layout
- [ ] APP_URL in Railway = production domain
- [ ] git add . && git push done
- [ ] Trigger Manual Deploy in Railway
- [ ] Wait for "Deployment successful"
- [ ] Hard refresh browser (Ctrl+Shift+R)
- [ ] Check Network tab: app.css status = 200

### 6. Debugging Steps
If assets still not loading:
1. Check browser: https://elibrary-production-fb6e.up.railway.app/build/manifest.json
   - Should return JSON (not 404)
2. Check DevTools Network tab
   - app.css, app.js should be 200 (not 404 or error)
3. Check Railway deployment logs for errors
4. Run local: cat public/build/manifest.json
5. Verify APP_URL matches exact domain

## Commands Reference

### Local Testing
```bash
# Clean rebuild
rm -rf node_modules package-lock.json bootstrap/cache/*
npm install
npm run build

# Verify manifest
ls -la public/build/manifest.json
cat public/build/manifest.json
```

### Deployment
```bash
# After fixes
git add .
git commit -m "Fix Vite assets for production"
git push origin main

# Then in Railway dashboard:
# 1. Settings → Build → paste Build Command
# 2. Settings → Deploy → Pre-Deploy → paste Pre-Deploy Command
# 3. Settings → Variables → update APP_URL
# 4. Deployments → Manual Deploy
```

## Files to Review
- vite.config.js (root)
- resources/views/layouts/app.blade.php
- package.json (verify build script)
- .env.example (verify all vars listed)
- public/build/manifest.json (after npm run build)

## Related Docs
- Laravel Vite Plugin: https://laravel.com/docs/vite
- Railway Deployment: https://railway.app/docs
- Tailwind CSS: https://tailwindcss.com/docs

---

## When to Apply These Rules
- Code review sebelum deploy ke Railway
- Troubleshoot asset loading issues
- Setup new Vite project di Railway
```

---

## **Cara Menggunakan di Cursor**

### **Option 1: Chat langsung**
1. Buka Cursor
2. Klik chat icon
3. Paste **"Untuk Chat Cursor"** section di atas
4. Cursor akan analyze files kamu & beri fix suggestions

### **Option 2: Persistent Rules**
1. Buat file di root project: `.cursor/rules`
2. Paste **"Untuk .cursor/rules"** section di atas
3. Cursor akan auto-apply rules saat review code
4. Kamu bisa reference dengan `@rules` di chat

### **Option 3: Upload File ke Cursor**
1. Download `CURSOR-PROMPT.md` dari output
2. Buka Cursor → Drag-drop file ke chat
3. Cursor akan baca & apply sebagai context

---

## **Contoh Cara Pakai di Cursor Chat**

Setelah paste prompt:

```
@Cursor: 
I have a Laravel Vite project with asset loading issues on Railway.
Can you review my vite.config.js and app.blade.php?
Here are the files: [drag & drop files]
```

Cursor akan:
✅ Identify issues
✅ Suggest exact fixes dengan code snippets
✅ Generate corrected files
✅ Provide step-by-step deployment guide

---

## **Quick Copy-Paste untuk Cursor Chat**

Jika ingin langsung, copy ini:

```
Project: Laravel 13 + Vite + Tailwind deployment to Railway.
Problem: Assets (CSS/JS) not loading in production.
Symptoms: HTML renders but no styling, manifest.json might be missing.

Files to review:
1. vite.config.js - check manifest: true in build config
2. resources/views/layouts/app.blade.php - check @vite() tag exists
3. package.json - verify build script is "vite build"
4. Build Command in Railway: composer install --no-dev --optimize-autoloader && npm install --omit=dev && npm run build
5. Pre-Deploy Command: php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force
6. Environment: APP_URL must match production domain exactly (https://elibrary-production-fb6e.up.railway.app)

Please provide:
1. Root cause analysis
2. Fixed versions of key files
3. Step-by-step deployment checklist
4. Verification commands
```

Kirim prompt ini ke Cursor, dan Cursor akan automatic analyze + fix semua issues.
