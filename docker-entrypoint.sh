#!/bin/sh
set -e

# ==============================================================================
# SIMACCA - Docker Entrypoint Script
# Otomatisasi inisialisasi dependensi & perizinan folder saat container dimulai
# ==============================================================================

echo "🚀 [SIMACCA] Memeriksa inisialisasi container..."

# 1. Otomatis install Composer jika folder vendor belum ada (misal: saat baru clone)
if [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo "📦 [SIMACCA] File vendor/autoload.php belum ditemukan."
    echo "📦 [SIMACCA] Menjalankan 'composer install' otomatis..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    echo "✅ [SIMACCA] Dependensi Composer berhasil dipasang!"
fi

# 2. Pastikan seluruh struktur folder runtime writable CodeIgniter 4 tersedia
WRITABLE_DIRS="
/var/www/html/writable/cache
/var/www/html/writable/logs
/var/www/html/writable/session
/var/www/html/writable/debugbar
/var/www/html/writable/tmp
/var/www/html/writable/uploads
/var/www/html/writable/uploads/profile
/var/www/html/writable/uploads/logo
/var/www/html/writable/uploads/jurnal
/var/www/html/writable/uploads/jurnal_piket
/var/www/html/writable/uploads/jurnal_pkl
/var/www/html/writable/uploads/jurnal_wali
/var/www/html/writable/uploads/absensi_guru
/var/www/html/writable/uploads/izin
/var/www/html/writable/uploads/pkl_progress
"

for dir in $WRITABLE_DIRS; do
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
    fi
done

# Berikan izin penuh pada folder writable agar PHP-FPM (www-data) bebas menulis
chmod -R 777 /var/www/html/writable 2>/dev/null || true

echo "✨ [SIMACCA] Lingkungan siap! Menjalankan perintah utama: $@"

# 3. Jalankan perintah container (default: php-fpm)
exec "$@"
