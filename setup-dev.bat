@echo off
setlocal enabledelayedexpansion

REM ==============================================================================
REM SIMACCA - One-Command Automated Setup Script for Windows (CMD / PowerShell)
REM Menyiapkan seluruh lingkungan Docker SIMACCA dari 0 sampai siap pakai
REM ==============================================================================

echo ====================================================
echo        SIMACCA - Quick Setup Wizard for Docker
echo    Sistem Monitoring Absensi dan Catatan Cara Ajar
echo ====================================================
echo.

REM 1. Periksa ketersediaan Docker
where docker >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Docker belum terpasang atau belum berjalan di Windows Anda.
    echo Silakan buka dan pastikan Docker Desktop sudah berjalan: https://docs.docker.com/desktop/setup/install/windows-install/
    pause
    exit /b 1
)

REM 2. Siapkan file .env jika belum ada
if not exist ".env" (
    echo [INFO] Membuat file .env dari template .env.example...
    copy .env.example .env >nul
    echo [OK] File .env berhasil dibuat!
) else (
    echo [OK] File .env sudah ada.
)

REM 3. Build & Jalankan Container Docker
echo.
echo [DOCKER] Menyalakan container Docker (App, Webserver, Database)...
docker compose up -d --build
if %errorlevel% neq 0 (
    echo [ERROR] Gagal menyalakan container Docker.
    pause
    exit /b 1
)

REM 4. Tunggu inisialisasi database hingga healthy
echo.
echo [INFO] Menunggu inisialisasi Database MariaDB hingga sehat...
set WAITED=0

:wait_loop
for /f "tokens=*" %%i in ('docker inspect -f "{{.State.Health.Status}}" simacca_db 2^>nul') do set DB_STATUS=%%i
if "%DB_STATUS%"=="healthy" (
    echo [OK] Database siap digunakan!
    goto run_setup
)
timeout /t 2 /nobreak >nul
set /a WAITED+=2
if %WAITED% geq 40 (
    echo [WARNING] Database memakan waktu lebih lama untuk inisialisasi. Melanjutkan...
    goto run_setup
)
echo .
goto wait_loop

:run_setup
REM 5. Jalankan migrasi & data dummy SIMACCA
echo.
echo [DATABASE] Menjalankan migrasi database dan pengisian dummy data...
docker compose exec -T app php spark setup --with-dummy --force

echo.
echo ====================================================
echo    SETUP BERHASIL! SIMACCA SIAP DIGUNAKAN
echo ====================================================
echo.
echo URL Aplikasi: http://localhost:8081
echo Akun Admin  : Username: admin ^| Password: admin123
echo Akun Guru   : Username: guru  ^| Password: guru123
echo Akun Siswa  : Username: siswa ^| Password: siswa123
echo.
echo Perintah Bermanfaat:
echo   - Matikan container : docker compose down
echo   - Nyalakan kembali  : docker compose up -d
echo   - Lihat log sistem  : docker compose logs -f
echo.
pause
