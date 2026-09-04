#!/bin/bash
set -e

# ==============================================================================
# SIMACCA - One-Command Automated Setup Script for Developers
# Menyiapkan seluruh lingkungan Docker SIMACCA dari 0 sampai siap pakai
# ==============================================================================

CYAN='\033[0;36m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BOLD='\033[1m'
NC='\033[0m' # No Color

echo -e "${CYAN}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║     SIMACCA - Quick Setup Wizard for Docker        ║${NC}"
echo -e "${CYAN}║  Sistem Monitoring Absensi dan Catatan Cara Ajar   ║${NC}"
echo -e "${CYAN}╚════════════════════════════════════════════════════╝${NC}"
echo ""

# 1. Pastikan Docker dan Docker Compose terinstall
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Error: Docker belum terpasang di sistem Anda.${NC}"
    echo "Silakan pasang Docker Desktop / Docker Engine terlebih dahulu: https://docs.docker.com/get-docker/"
    exit 1
fi

# 2. Siapkan file .env jika belum ada
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}📝 Membuat file .env dari template .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✓ File .env berhasil dibuat!${NC}"
else
    echo -e "${GREEN}✓ File .env sudah ada.${NC}"
fi

# 3. Build & Jalankan Container Docker
echo ""
echo -e "${CYAN}🐳 Menyalakan container Docker (App, Webserver, Database)...${NC}"
docker compose up -d --build

# 4. Tunggu container database (simacca_db) siap menerima koneksi
echo ""
echo -e "${YELLOW}⏳ Menunggu inisialisasi Database MariaDB hingga sehat (healthy)...${NC}"
MAX_WAIT=30
WAITED=0

until [ "$(docker inspect -f {{.State.Health.Status}} simacca_db 2>/dev/null)" = "healthy" ]; do
    sleep 2
    WAITED=$((WAITED+2))
    if [ $WAITED -ge $MAX_WAIT ]; then
        echo -e "${RED}⚠️ Database memakan waktu lebih lama untuk inisialisasi.${NC}"
        break
    fi
    echo -n "."
done
echo ""
echo -e "${GREEN}✓ Database siap digunakan!${NC}"

# 5. Jalankan migrasi & data dummy SIMACCA
echo ""
echo -e "${CYAN}🔄 Menjalankan migrasi database dan pengisian dummy data...${NC}"
docker compose exec -T app php spark setup --with-dummy --force

echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}${BOLD}🎉 SETUP BERHASIL! SIMACCA SIAP DIGUNAKAN${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "🌐 ${BOLD}URL Aplikasi:${NC}  http://localhost:8081"
echo -e "👤 ${BOLD}Akun Admin:${NC}    Username: ${YELLOW}admin${NC} | Password: ${YELLOW}admin123${NC}"
echo -e "👨‍🏫 ${BOLD}Akun Guru:${NC}     Username: ${YELLOW}guru${NC}  | Password: ${YELLOW}guru123${NC}"
echo -e "🎓 ${BOLD}Akun Siswa:${NC}    Username: ${YELLOW}siswa${NC} | Password: ${YELLOW}siswa123${NC}"
echo ""
echo -e "💡 ${BOLD}Perintah Bermanfaat:${NC}"
echo -e "  - Matikan container:    ${CYAN}docker compose down${NC} (atau ${CYAN}make down${NC})"
echo -e "  - Nyalakan container:   ${CYAN}docker compose up -d${NC} (atau ${CYAN}make up${NC})"
echo -e "  - Lihat log sistem:     ${CYAN}docker compose logs -f${NC} (atau ${CYAN}make logs${NC})"
echo -e "  - Masuk ke container:   ${CYAN}docker compose exec app sh${NC} (atau ${CYAN}make bash${NC})"
echo ""
