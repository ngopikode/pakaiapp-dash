#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

echo "🚀 Memulai proses deployment..."

# 1. Mengaktifkan maintenance mode (opsional, error diabaikan jika gagal)
echo "🚧 Mengaktifkan maintenance mode..."
php artisan down || true

# 2. Tarik kode terbaru dari repositori
echo "📥 Menarik kode terbaru dari git..."
git pull origin master

# 3. Install composer dependencies (tanpa package dev)
echo "📦 Install/Update composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 4. Bersihkan cache lama dan optimasi aplikasi
echo "🧹 Membersihkan dan membangun ulang cache (config, route, view, event)..."
php artisan optimize:clear
php artisan optimize
php artisan view:cache
php artisan event:cache

# 5. Jalankan migrasi database
echo "🗄️ Menjalankan migrasi database..."
php artisan migrate --force

# 6. Matikan maintenance mode
echo "✅ Mematikan maintenance mode..."
php artisan up

echo "🎉 Deployment selesai dan berhasil!"
