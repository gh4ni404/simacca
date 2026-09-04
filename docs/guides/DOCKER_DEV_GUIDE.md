# 🐳 Panduan Docker Development SIMACCA

Panduan ini ditujukan bagi **developer** yang baru melakukan `clone` repository SIMACCA dan ingin langsung menjalankan aplikasi di lingkungan lokal menggunakan Docker secara **mudah, cepat, dan zero-host-dependency** (tidak perlu memasang PHP, Composer, maupun MySQL di komputer lokal).

---

## ⚡ 1. Cara Cepat: Setup 1-Perintah (Rekomendasi)

Setelah melakukan `git clone`, jalankan perintah berikut dari folder root project:

```bash
# Opsi A: Menggunakan Makefile (Linux/macOS/WSL)
make dev

# Opsi B: Menggunakan Bash Script langsung
./setup-dev.sh
```

Skrip ini akan secara otomatis:
1. Menyalin `.env.example` menjadi `.env` (sudah terkonfigurasi untuk Docker).
2. Membangun dan menyalakan container: PHP 8.3 FPM (`app`), Nginx (`webserver`), dan MariaDB 10.11 (`db`).
3. Mengunduh dependensi Composer jika belum ada.
4. Menunggu database siap dan menjalankan wizard migrasi + pengisian data dummy.
5. Menampilkan URL aplikasi dan kredensial login default.

Setelah selesai, buka browser Anda di:
👉 **[http://localhost:8081](http://localhost:8081)**

---

## 🛠️ 2. Cara Manual (Langkah demi Langkah)

Jika Anda lebih memilih menjalankan perintah Docker secara manual:

### Langkah 1: Clone & Siapkan File Konfigurasi
```bash
git clone https://github.com/gh4ni404/simacca.git
cd simacca

# Salin konfigurasi Docker
cp .env.example .env
```

### Langkah 2: Nyalakan Container Docker
```bash
docker compose up -d --build
```
> *Catatan: Container PHP akan otomatis memasang dependensi Composer dan menyiapkan izin folder `writable/` pada saat pertama kali berjalan.*

### Langkah 3: Setup Database & Dummy Data
```bash
# Jalankan migrasi dan seeding data dummy testing
docker compose exec app php spark setup --with-dummy
```

### Langkah 4: Buka Aplikasi di Browser
Buka browser ke `http://localhost:8081`

---

## 🔑 Akun & Kredensial Default

| Role | Username | Password | Keterangan |
|---|---|---|---|
| **Admin** | `admin` | `admin123` | Akses penuh sistem |
| **Guru** | `guru` | `guru123` | Absensi & Jurnal Mengajar |
| **Siswa** | `siswa` | `siswa123` | Presensi Siswa & Jurnal PKL |
| **Wakakur** | `wakakur` | `wakakur123` | Monitoring Kurikulum |

---

## 📋 Shortcut Perintah Makefile

Tersedia berbagai shortcut perintah untuk memudahkan produktivitas harian Anda:

| Perintah | Fungsi | Ekuivalen Docker Compose |
|---|---|---|
| `make up` | Menyalakan semua container di background | `docker compose up -d` |
| `make down` | Mematikan dan melepas semua container | `docker compose down` |
| `make restart` | Restart seluruh service | `docker compose restart` |
| `make logs` | Melihat log realtime semua service | `docker compose logs -f` |
| `make logs-app` | Melihat log PHP-FPM aplikasi | `docker compose logs -f app` |
| `make logs-nginx` | Melihat log webserver Nginx | `docker compose logs -f webserver` |
| `make logs-db` | Melihat log database MariaDB | `docker compose logs -f db` |
| `make bash` | Masuk ke terminal shell container PHP | `docker compose exec app sh` |
| `make setup` | Menjalankan ulang wizard setup database | `docker compose exec app php spark setup --with-dummy` |
| `make migrate` | Menjalankan migrasi database baru | `docker compose exec app php spark migrate` |
| `make test` | Menjalankan automated test (PHPUnit) | `docker compose exec app ./vendor/bin/phpunit` |
| `make clean` | Membersihkan cache & session | `docker compose exec app php spark cache:clear && ...` |

---

## 🗄️ Koneksi Database dari GUI Tool (DBeaver, TablePlus, Navicat, HeidiSQL)

Container MariaDB mengekspos port ke host OS sehingga Anda bisa terhubung menggunakan aplikasi GUI database favorit Anda:

* **Host**: `127.0.0.1` atau `localhost`
* **Port**: `3306` (atau sesuai `DB_PORT` di file `.env`)
* **Database**: `simacca_db`
* **Username**: `simacca_user` (atau `root`)
* **Password**: `simacca_pass` (atau `root` untuk user root)

---

## 💻 Menjalankan Perintah PHP Spark di Docker

Semua perintah `php spark` dapat dijalankan langsung di dalam container tanpa perlu memasang PHP di OS komputer Anda:

```bash
# Format umum:
docker compose exec app php spark <perintah>

# Contoh:
docker compose exec app php spark key:generate
docker compose exec app php spark cache:clear
docker compose exec app php spark absensi:mark-alpha-guru
```

---

## 🌐 Menjalankan Cloudflare Tunnel (Opsional)

Service Cloudflare Tunnel dipisahkan ke dalam *profile* tersendiri agar tidak mengganggu proses development lokal. Jika Anda ingin mengaktifkan tunnel:

```bash
docker compose --profile tunnel up -d
```
*(Pastikan file kredensial di folder `cloudflare/` sudah dikonfigurasi).*

---

## ❓ Troubleshooting & Pertanyaan Umum (FAQ)

### 1. Port 8081 atau Port 3306 Bentrok dengan Aplikasi Lain
Jika port `8081` atau `3306` sudah digunakan di komputer Anda:
1. Buka file `.env`.
2. Ubah nilai port sesuai keinginan, contoh:
   ```ini
   APP_PORT = 8082
   DB_PORT = 3307
   app.baseURL = 'http://localhost:8082/'
   ```
3. Restart container: `docker compose up -d`.

### 2. Error `Permission Denied` pada Folder `writable/`
Jika terjadi kendala izin tulis saat PHP menyimpan file:
```bash
docker compose exec app chmod -R 777 /var/www/html/writable
```

### 3. Reset Database ke Kondisi Awal
Untuk mereset database dan mengulangi migrasi + seeding data dummy:
```bash
docker compose exec app php spark setup --force --with-dummy
```
