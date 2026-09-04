# ==============================================================================
# SIMACCA - One-Command Automated Setup Script for PowerShell (Windows / macOS / Linux)
# ==============================================================================

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "       SIMACCA - Quick Setup Wizard for Docker" -ForegroundColor Cyan
Write-Host "   Sistem Monitoring Absensi dan Catatan Cara Ajar" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host ""

# 1. Periksa ketersediaan Docker
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "[ERROR] Docker belum terpasang atau belum berjalan di sistem Anda." -ForegroundColor Red
    Write-Host "Silakan pastikan Docker Desktop sudah berjalan: https://docs.docker.com/desktop/"
    Exit 1
}

# 2. Siapkan file .env jika belum ada
if (-not (Test-Path ".env")) {
    Write-Host "[INFO] Membuat file .env dari template .env.example..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    Write-Host "[OK] File .env berhasil dibuat!" -ForegroundColor Green
} else {
    Write-Host "[OK] File .env sudah ada." -ForegroundColor Green
}

# 3. Build & Jalankan Container Docker
Write-Host ""
Write-Host "[DOCKER] Menyalakan container Docker (App, Webserver, Database)..." -ForegroundColor Cyan
docker compose up -d --build

# 4. Tunggu inisialisasi database
Write-Host ""
Write-Host "[INFO] Menunggu inisialisasi Database MariaDB hingga sehat..." -ForegroundColor Yellow
$maxWait = 30
$waited = 0

while ($waited -lt $maxWait) {
    Start-Sleep -Seconds 2
    $waited += 2
    $status = docker inspect -f '{{.State.Health.Status}}' simacca_db 2>$null
    if ($status -eq "healthy") {
        break
    }
    Write-Host -NoNewline "."
}
Write-Host ""
Write-Host "[OK] Database siap digunakan!" -ForegroundColor Green

# 5. Jalankan migrasi & data dummy SIMACCA
Write-Host ""
Write-Host "[DATABASE] Menjalankan migrasi database dan pengisian dummy data..." -ForegroundColor Cyan
docker compose exec -T app php spark setup --with-dummy --force

Write-Host ""
Write-Host "====================================================" -ForegroundColor Green
Write-Host "   SETUP BERHASIL! SIMACCA SIAP DIGUNAKAN" -ForegroundColor Green
Write-Host "====================================================" -ForegroundColor Green
Write-Host ""
Write-Host "URL Aplikasi : http://localhost:8081" -ForegroundColor Yellow
Write-Host "Akun Admin   : Username: admin | Password: admin123"
Write-Host "Akun Guru    : Username: guru  | Password: guru123"
Write-Host "Akun Siswa   : Username: siswa | Password: siswa123"
Write-Host ""
Write-Host "Perintah Bermanfaat:"
Write-Host "  - Matikan container : docker compose down"
Write-Host "  - Nyalakan kembali  : docker compose up -d"
Write-Host "  - Lihat log sistem  : docker compose logs -f"
Write-Host ""
