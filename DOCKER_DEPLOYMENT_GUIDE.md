# 🚀 Panduan Deployment Docker & Perintah CLI (PC Target)

Panduan ini ditujukan untuk menjalankan aplikasi **SIMACCA** di PC Server / Target menggunakan **Pre-built Docker Image** dan **Cloudflare Tunnel**, tanpa perlu memodifikasi kode atau memasang PHP/Composer di OS Host PC tersebut.

---

## 📋 Daftar Isi
1. [Struktur File di PC Target](#1-struktur-file-di-pc-target)
2. [Langkah Persiapan & Menjalankan](#2-langkah-persiapan--menjalankan)
3. [Perintah Manajemen Docker](#3-perintah-manajemen-docker)
4. [Kumpulan Perintah PHP Spark di Dalam Container](#4-kumpulan-perintah-php-spark-di-dalam-container)
5. [Backup & Pemeliharaan Data](#5-backup--pemeliharaan-data)
6. [Troubleshooting](#6-troubleshooting)

---

## 1. Struktur File di PC Target

Di PC target, buat sebuah direktori (misal: `~/simacca`) dan letakkan file-file berikut:

```text
simacca/
├── simacca-app.tar              # File image hasil export (docker save)
├── docker-compose.yaml          # File compose production (dari docker-compose.prod.yaml)
├── nginx.conf                   # Konfigurasi reverse proxy & fastcgi
├── .env                         # Konfigurasi environment & database
├── cloudflare/                  # Folder kredensial Cloudflare Tunnel
│   ├── config.yml
│   └── 0958554b-1e80-49b4-a292-03db1dd27535.json
└── writable/
    └── uploads/                 # Penyimpanan upload pengguna (persisten)
```

---

## 2. Langkah Persiapan & Menjalankan

### A. Di PC Development (Asal)
1. **Export Image Docker**:
   ```bash
   docker save -o simacca-app.tar simacca-app:latest
   ```
2. Salin file `simacca-app.tar`, `docker-compose.prod.yaml` (rename jadi `docker-compose.yaml`), `nginx.conf`, `.env`, folder `cloudflare/`, dan folder `writable/uploads` ke Flashdisk / Harddisk Eksternal / Jaringan LAN.

---

### B. Di PC Target (Server)
1. **Pastikan Docker & Docker Compose sudah terpasang**:
   *(Jika menggunakan Windows WSL 2 dan belum terinstal, ikuti [Panduan Instalasi Docker di WSL](docs/guides/PANDUAN_INSTALL_DOCKER_WSL.md))*
   ```bash
   docker --version
   docker compose version
   ```
2. **Masuk ke direktori aplikasi**:
   ```bash
   cd ~/simacca
   ```
3. **Load Image Docker**:
   ```bash
   docker load -i simacca-app.tar
   ```
4. **Nyalakan Seluruh Service (App + Nginx + Cloudflare Tunnel)**:
   ```bash
   docker compose up -d
   ```
5. **Cek Status Container**:
   ```bash
   docker compose ps
   ```

Aplikasi langsung dapat diakses di:
* **Lokal**: `http://localhost:8081`
* **Online**: `https://simacca-alt.smkn8bone.sch.id`

---

## 3. Perintah Manajemen Docker

| Kebutuhan | Perintah |
| :--- | :--- |
| **Melihat status container** | `docker compose ps` |
| **Melihat log aplikasi (PHP-FPM)** | `docker compose logs -f app` |
| **Melihat log webserver (Nginx)** | `docker compose logs -f webserver` |
| **Melihat log Cloudflare Tunnel** | `docker compose logs -f tunnel` |
| **Restart semua service** | `docker compose restart` |
| **Menghentikan service** | `docker compose stop` |
| **Menyalakan kembali** | `docker compose start` |
| **Mematikan & melepas container** | `docker compose down` |
| **Masuk ke dalam terminal container PHP** | `docker compose exec app sh` |

---

## 4. Kumpulan Perintah PHP Spark di Dalam Container

Untuk menjalankan perintah CodeIgniter 4 CLI (`spark`), gunakan awalan:
```bash
docker compose exec app php spark <perintah>
```
*(Atau `docker exec -it simacca_app php spark <perintah>`)*

---

### 🔧 A. Perintah Database & Migrasi

* **Menjalankan migrasi database**:
  ```bash
  docker compose exec app php spark migrate
  ```
* **Melihat status migrasi**:
  ```bash
  docker compose exec app php spark migrate:status
  ```
* **Menjalankan Seeder database**:
  ```bash
  docker compose exec app php spark db:seed <NamaSeeder>
  ```

---

### 🧹 B. Perintah Maintenance, Cache & Session

* **Membersihkan Cache Aplikasi**:
  ```bash
  docker compose exec app php spark cache:clear
  ```
* **Membersihkan Sesi Kadaluarsa (Session Cleanup)**:
  ```bash
  docker compose exec app php spark session:cleanup
  ```
* **Membersihkan Token Remember-Me / Reset Password Expired**:
  ```bash
  docker compose exec app php spark token:cleanup
  ```

---

### ⏰ C. Perintah Otomasi & Cronjob SIMACCA

* **Menandai Otomatis Guru Alpha (Absensi Harian)**:
  ```bash
  docker compose exec app php spark absensi:mark-alpha-guru
  ```
* **Memperbarui Persentase Kelengkapan Profil Pengguna**:
  ```bash
  docker compose exec app php spark profile:set-completion
  ```
* **Diagnostik & Cek Pengaturan Email**:
  ```bash
  docker compose exec app php spark email:diagnostics
  ```
* **Mengirim Email Uji Coba**:
  ```bash
  docker compose exec app php spark email:test penerima@email.com
  ```

---

### 🔍 D. Perintah Diagnostik & Informasi

* **Melihat daftar seluruh Route yang terdaftar**:
  ```bash
  docker compose exec app php spark routes
  ```
* **Mengecek profil Wakakur**:
  ```bash
  docker compose exec app php spark wakakur:check-profile
  ```
* **Mengecek jadwal Wakakur**:
  ```bash
  docker compose exec app php spark wakakur:check-schedule
  ```
* **Menampilkan daftar seluruh perintah Spark**:
  ```bash
  docker compose exec app php spark list
  ```

---

## 5. Setup Cronjob di PC Target (Opsional)

Jika di PC target ingin menjalankan pembersihan sesi dan penandaan alpha secara otomatis setiap hari, Anda bisa menambahkan cronjob di Host OS (`crontab -e`):

```cron
# Bersihkan sesi kadaluarsa setiap hari pukul 01:00 WITA
0 1 * * * cd /home/user/simacca && docker compose exec -T app php spark session:cleanup >> /var/log/simacca_cron.log 2>&1

# Bersihkan token remember-me kadaluarsa setiap hari pukul 02:00 WITA
0 2 * * * cd /home/user/simacca && docker compose exec -T app php spark token:cleanup >> /var/log/simacca_cron.log 2>&1

# Tandai guru alpha setiap hari Senin-Jumat pukul 16:30 WITA
30 16 * * 1-5 cd /home/user/simacca && docker compose exec -T app php spark absensi:mark-alpha-guru >> /var/log/simacca_cron.log 2>&1
```
*(Catatan: flag `-T` diperlukan saat menjalankan docker compose di dalam cron).*

---

## 6. Troubleshooting

1. **Aplikasi menampilkan error database connection**:
   * Cek host database di `.env`. Jika database di host PC target (luar docker), gunakan `database.default.hostname = host.docker.internal` dan pastikan port MySQL host terbuka/dapat diakses.
2. **File upload tidak bisa disimpan (Permission Denied)**:
   * Jalankan di PC target:
     ```bash
     docker compose exec app chown -R www-data:www-data /var/www/html/writable
     docker compose exec app chmod -R 775 /var/www/html/writable
     ```
3. **Cloudflare Tunnel tidak konek**:
   * Periksa log tunnel: `docker compose logs tunnel`
   * Pastikan file credential JSON di folder `cloudflare/` memiliki permission baca: `chmod 644 cloudflare/*.json`.
