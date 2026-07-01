#!/bin/bash

# ==============================================================================
# PRODUCTION DEPLOY SCRIPT
# Pakaiapp Dashboard — Laravel + Livewire 4 + Stancl Tenancy + Redis
# ==============================================================================

set -e  # Exit immediately on error

START_TIME=$(date +%s)
LOG_FILE="/var/log/deploy-pakaiapp.log"
TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")

# Helper: log ke console DAN file sekaligus
log() {
    echo "$1"
    echo "[$TIMESTAMP] $1" >> "$LOG_FILE"
}

# ==============================================================================
# GUARD: Pastikan hanya jalan di environment production
# ==============================================================================
APP_ENV_VALUE=$(grep -E "^APP_ENV=" .env | cut -d '=' -f2 | tr -d '[:space:]')
if [ "$APP_ENV_VALUE" != "production" ]; then
    echo "❌ Deploy dibatalkan! APP_ENV=$APP_ENV_VALUE (bukan production)."
    echo "   Script ini hanya boleh dijalankan di server production."
    exit 1
fi

# ==============================================================================
# TRAP: Jika script gagal di tengah jalan, otomatis jalankan php artisan up
# agar aplikasi tidak stuck di maintenance mode selamanya
# ==============================================================================
trap 'log "❌ Deploy GAGAL! Mengaktifkan kembali aplikasi..."; php artisan up; log "--- DEPLOY FAILED [$TIMESTAMP] ---"' ERR

log "--- DEPLOY STARTED [$TIMESTAMP] ---"
log "🚀 Memulai proses deployment Pakaiapp..."

# ==============================================================================
# 1. CEK APAKAH ADA UPDATE DARI REMOTE
# ==============================================================================
log "🔍 Mengecek update dari remote..."
git fetch origin master
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/master)

if [ "$LOCAL" = "$REMOTE" ]; then
    log "✅ Tidak ada update baru. Deploy dibatalkan (sudah up-to-date)."
    exit 0
fi

# ==============================================================================
# 2. AKTIFKAN MAINTENANCE MODE
# ==============================================================================
log "🚧 Mengaktifkan maintenance mode..."
php artisan down || true

# ==============================================================================
# 3. TARIK KODE TERBARU
# ==============================================================================
log "📥 Menarik kode terbaru dari git (master)..."
git pull origin master

# ==============================================================================
# 4. COMPOSER INSTALL
# ==============================================================================
log "📦 Install/update Composer dependencies (no-dev)..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# ==============================================================================
# 5. CLEAR CONFIG DULU sebelum cache — penting agar migrate pakai config terbaru
# ==============================================================================
log "🧹 Membersihkan semua cache lama..."
php artisan optimize:clear

# ==============================================================================
# 6. BUILD CACHE BARU
# ==============================================================================
log "⚙️  Membangun ulang cache (config, route, view, event)..."
php artisan optimize
php artisan view:cache
php artisan event:cache

# ==============================================================================
# 7. MIGRASI DATABASE CENTRAL
# ==============================================================================
log "🗄️  Menjalankan migrasi database central..."
php artisan migrate --force

# ==============================================================================
# 8. MIGRASI DATABASE SEMUA TENANT (retail + resto)
# ==============================================================================
log "🏪 Menjalankan migrasi semua tenant database (type=all)..."
php artisan tenants:migrate-type all --force

# ==============================================================================
# 9. RESTART QUEUE WORKER (agar pickup kode baru)
# ==============================================================================
log "🔄 Merestart queue worker..."
php artisan queue:restart

# ==============================================================================
# 10. MATIKAN MAINTENANCE MODE
# ==============================================================================
log "✅ Mematikan maintenance mode..."
php artisan up

# ==============================================================================
# SELESAI — Hitung durasi & log
# ==============================================================================
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

log "🎉 Deployment selesai dalam ${DURATION} detik!"
log "--- DEPLOY FINISHED [$TIMESTAMP] ---"
echo ""
echo "   Commit terbaru:"
git log --oneline -3
