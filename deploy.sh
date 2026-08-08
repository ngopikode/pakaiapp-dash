#!/bin/bash

# ==============================================================================
# PRODUCTION DEPLOY SCRIPT
# Pakaiapp Dashboard — Laravel + Livewire 4 + Stancl Tenancy + Redis
# ==============================================================================

set -e  # Exit immediately on error

START_TIME=$(date +%s)
TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")

# Log utama — coba /var/log/ dulu, fallback ke storage/logs/, fallback ke /tmp/
# Ini memastikan log tetap bisa ditulis apapun user yang menjalankan script
LOG_FILE="/var/log/deploy-pakaiapp.log"
if ! touch "$LOG_FILE" 2>/dev/null; then
    LOG_FILE="$(pwd)/storage/logs/deploy.log"
    if ! touch "$LOG_FILE" 2>/dev/null; then
        LOG_FILE="/tmp/deploy-pakaiapp.log"
    fi
fi

# Helper: log ke console DAN file sekaligus (tidak pernah abort jika gagal)
log() {
    echo "$1"
    echo "[$TIMESTAMP] $1" >> "$LOG_FILE" 2>/dev/null || true
}

# Helper: jalankan artisan sebagai www-data agar bisa tulis storage/logs/laravel.log
# meskipun storage/logs/ di-restrict hanya untuk www-data
artisan() {
    sudo -u www-data php artisan "$@"
}

# ==============================================================================
# GUARD: Force Deploy
# ==============================================================================
FORCE_DEPLOY=false
if [ "$1" = "--force" ] || [ "$1" = "-f" ]; then
    FORCE_DEPLOY=true
    log "⚠️  Force mode aktif: Deploy akan dilanjutkan meskipun tidak ada update dari git."
fi

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
trap 'log "❌ Deploy GAGAL! Mengaktifkan kembali aplikasi..."; artisan up; log "--- DEPLOY FAILED [$TIMESTAMP] ---"' ERR

log "--- DEPLOY STARTED [$TIMESTAMP] ---"
log "🚀 Memulai proses deployment Pakaiapp..."

# ==============================================================================
# 1. CEK APAKAH ADA UPDATE DARI REMOTE
# ==============================================================================
log "🔍 Mengecek update dari remote..."
git fetch origin master
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/master)

if [ "$LOCAL" = "$REMOTE" ] && [ "$FORCE_DEPLOY" = false ]; then
    log "✅ Tidak ada update baru. Deploy dibatalkan (sudah up-to-date)."
    log "💡 Gunakan './deploy.sh --force' untuk memaksa deploy meskipun tidak ada update."
    exit 0
elif [ "$LOCAL" = "$REMOTE" ] && [ "$FORCE_DEPLOY" = true ]; then
    log "✅ Tidak ada update baru dari git, namun melanjutkan deploy karena FORCE MODE."
fi

# ==============================================================================
# 2. TARIK KODE TERBARU
# ==============================================================================
log "📥 Menarik kode terbaru dari git (master)..."
git pull --ff-only origin master

# ==============================================================================
# 3b. PERBAIKI PERMISSION (antisipasi file baru dari git dengan owner berbeda)
# ==============================================================================
log "🔐 Memastikan permission storage & bootstrap/cache..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# ==============================================================================
# 4. COMPOSER INSTALL
# ==============================================================================
log "📦 Install/update Composer dependencies (no-dev)..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# ==============================================================================
# 4b. BUILD FRONTEND ASSETS
# ==============================================================================
log "🎨 Membangun frontend assets (Vite)..."
npm install --no-audit --no-fund
npm run build

# ==============================================================================
# 5. CLEAR CONFIG DULU sebelum cache — penting agar migrate pakai config terbaru
# ==============================================================================
log "🧹 Membersihkan semua cache lama..."
artisan optimize:clear

# ==============================================================================
# 5b. CLEAR TENANT COMPILED VIEWS
# Mengatasi error absolute path nyangkut jika ada perubahan struktur
# ==============================================================================
log "🗑️  Membersihkan compiled views seluruh tenant..."
sudo rm -rf storage/tenants/*/framework/views/* || true
sudo rm -rf storage/tenants/*/framework/cache/* || true

# ==============================================================================
# 6. BUILD CACHE BARU
# ==============================================================================
# Note: route:cache sengaja tidak dijalankan.
# Multi-domain foreach loop di web.php mendaftarkan route name yang sama
# untuk setiap domain (home, blog.index, dll.) — ini incompatible dengan
# route:cache yang mensyaratkan nama route unik. Ini adalah keterbatasan
# desain Stancl Tenancy + Route::domain() loop, bukan bug.
log "⚙️  Membangun ulang cache (config, view, event)..."
artisan config:cache
artisan view:cache
artisan event:cache

# ==============================================================================
# 7. AKTIFKAN MAINTENANCE MODE & MIGRASI DATABASE CENTRAL
# ==============================================================================
log "🚧 Mengaktifkan maintenance mode sementara untuk migrasi..."
artisan down || true

log "🗄️  Menjalankan migrasi database central..."
artisan migrate --force

# ==============================================================================
# 8. MIGRASI DATABASE SEMUA TENANT (retail + resto)
# ==============================================================================
log "🏪 Menjalankan migrasi semua tenant database (type=all)..."
artisan tenants:migrate-type all --force

# ==============================================================================
# 9. RESTART QUEUE WORKER & REVERB (agar pickup kode baru)
# ==============================================================================
log "🔄 Merestart queue worker dan reverb..."
artisan queue:restart
artisan reverb:restart

# ==============================================================================
# 9b. PASTIKAN STORAGE SYMLINK ADA
# ==============================================================================
log "🔗 Memastikan storage symlink ada (public/storage → storage/app/public)..."
artisan storage:link --relative 2>/dev/null || true

# ==============================================================================
# 10. MATIKAN MAINTENANCE MODE
# ==============================================================================
log "✅ Mematikan maintenance mode..."
artisan up

# ==============================================================================
# 10b. RELOAD PHP-FPM — flush OPcache agar kode baru langsung aktif di memori
# ==============================================================================
log "⚡ Mereload PHP-FPM (flush OPcache)..."
sudo systemctl reload php8.3-fpm || log "⚠️  PHP-FPM reload gagal. Coba manual: sudo systemctl reload php8.3-fpm"

# ==============================================================================
# 11. CEK DEPLOY CONFIGS (Reverb, Queue, Nginx)
# ==============================================================================
log "🔍 Mengecek konfigurasi deploy (Nginx & Supervisor)..."
# Periksa apakah dijalankan secara interaktif (di terminal)
if [ -t 1 ]; then
    echo ""
    echo "==========================================================="
    echo "Terdapat konfigurasi server di folder deploy/:"
    echo " - nginx-wildcard.conf"
    echo " - supervisor-reverb.conf"
    echo " - supervisor-queue.conf"
    echo "==========================================================="
    echo -n "Apakah Anda ingin memasang/memperbarui konfigurasi ini ke sistem server (/etc/)? [y/N] (Otomatis skip dalam 10 detik): "
    read -t 10 CONFIRM || CONFIRM="n"
    if [[ "$CONFIRM" =~ ^[Yy]$ ]]; then
        log "⚙️  Memasang konfigurasi sistem dari deploy/..."
        sudo cp deploy/nginx-wildcard.conf /etc/nginx/sites-available/wildcard.pakaiapp.online.conf
        sudo cp deploy/supervisor-reverb.conf /etc/supervisor/conf.d/pakaiapp-reverb.conf
        sudo cp deploy/supervisor-queue.conf /etc/supervisor/conf.d/pakaiapp-queue.conf
        
        log "🗑️  Menghapus konfigurasi lama (pakaiapp-worker.conf)..."
        sudo rm -f /etc/supervisor/conf.d/pakaiapp-worker.conf
        
        log "🔄 Restarting Nginx & Supervisor..."
        sudo nginx -t && sudo systemctl reload nginx || log "⚠️  Nginx reload gagal/diabaikan."
        sudo supervisorctl update || true
        sudo supervisorctl restart pakaiapp-reverb:* || true
        log "✅ Konfigurasi sistem berhasil dipasang!"
    else
        log "⏭️  Pemasangan konfigurasi sistem dilewati."
    fi
else
    log "ℹ️  Non-interactive mode. Pemasangan konfigurasi sistem dilewati."
fi

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
