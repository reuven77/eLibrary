# RuangBaca — production image for Railway
# Bypass Nixpacks (hindari konflik nodejs_22 + override Build Command omit=dev)

# ---------- Frontend (Vite) ----------
FROM node:22-bookworm-slim AS frontend
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
RUN mkdir -p public

RUN npm run build \
    && test -f public/build/manifest.json

# ---------- PHP runtime ----------
FROM php:8.4-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        intl \
        mbstring \
        pdo_pgsql \
        pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        storage/app/public \
        bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && composer dump-autoload --optimize \
    && php artisan package:discover --ansi \
    && test -f public/build/manifest.json

ENV PORT=8000

# Safety net if dashboard still runs `artisan serve` with an unexpanded $PORT
RUN php docker/patch-serve.php \
    && chmod +x docker/start.sh

# Prefer start.sh; railway.toml startCommand forces this over dashboard leftovers
CMD ["/bin/sh", "./docker/start.sh"]
