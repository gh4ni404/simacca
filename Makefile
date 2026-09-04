.PHONY: help dev setup up down restart logs logs-app logs-nginx logs-db bash spark migrate seed test clean

# Default target: tampilkan bantuan
help:
	@echo "╔══════════════════════════════════════════════════════════════════╗"
	@echo "║                   SIMACCA - Docker CLI Shortcuts                 ║"
	@echo "╚══════════════════════════════════════════════════════════════════╝"
	@echo "Perintah yang tersedia:"
	@echo "  make dev         - Menyiapkan seluruh lingkungan (1-command setup)"
	@echo "  make up          - Menjalankan seluruh container di background"
	@echo "  make down        - Menghentikan dan menghapus container"
	@echo "  make restart     - Restart semua service"
	@echo "  make logs        - Menampilkan realtime logs dari semua service"
	@echo "  make logs-app    - Menampilkan logs dari container PHP-FPM"
	@echo "  make logs-nginx  - Menampilkan logs dari webserver Nginx"
	@echo "  make logs-db     - Menampilkan logs dari database MariaDB"
	@echo "  make bash        - Masuk ke terminal container PHP (sh)"
	@echo "  make setup       - Menjalankan wizard migrasi & seeding dummy data"
	@echo "  make migrate     - Menjalankan migrasi database"
	@echo "  make test        - Menjalankan PHPUnit test"
	@echo "  make clean       - Membersihkan cache dan session CodeIgniter"
	@echo ""

# 1-Command Developer Setup
dev:
	@bash ./setup-dev.sh

# Menyalakan container
up:
	docker compose up -d

# Menghentikan container
down:
	docker compose down

# Restart container
restart:
	docker compose restart

# Melihat log
logs:
	docker compose logs -f

logs-app:
	docker compose logs -f app

logs-nginx:
	docker compose logs -f webserver

logs-db:
	docker compose logs -f db

# Masuk ke container shell
bash:
	docker compose exec app sh

# Setup database & dummy data
setup:
	docker compose exec app php spark setup --with-dummy

# Migrasi database
migrate:
	docker compose exec app php spark migrate

# Jalankan test suite
test:
	docker compose exec app ./vendor/bin/phpunit

# Bersihkan cache
clean:
	docker compose exec app php spark cache:clear
	docker compose exec app php spark session:cleanup
