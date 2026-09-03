# 🚀 Panduan Deployment Docker & Perintah CLI (PC Target)

Panduan ini ditujukan untuk menjalankan aplikasi **SIMACCA** di PC Server / Target menggunakan **Docker Image** dan **Cloudflare Tunnel**, tanpa perlu memodifikasi kode atau memasang PHP/Composer di OS Host PC tersebut.

---

## 📋 Daftar Isi
1. [Metode 1 (Rekomendasi Utama): Distribusi via Docker Hub (Online Pull)](#1-metode-1-rekomendasi-utama-distribusi-via-docker-hub-online-pull)
2. [Metode 2: Distribusi Offline via File .tar (Flashdisk / LAN)](#2-metode-2-distribusi-offline-via-file-tar-flashdisk--lan)
3. [Struktur File di PC Target](#3-struktur-file-di-pc-target)
4. [Sinkronisasi File Upload & Izin Folder (PENTING)](#4-sinkronisasi-file-upload--izin-folder-penting)
5. [Perintah Manajemen Docker](#5-perintah-manajemen-docker)
6. [Kumpulan Perintah PHP Spark di Dalam Container](#6-kumpulan-perintah-php-spark-di-dalam-container)
7. [Setup Cronjob di PC Target (Opsional)](#7-setup-cronjob-di-pc-target-opsional)
8. [Troubleshooting & Solusi Masalah Nyata](#8-troubleshooting--solusi-masalah-nyata)

---

## 1. Metode 1 (Rekomendasi Utama): Distribusi via Docker Hub (Online Pull)

Dengan Docker Hub, proses deployment di PC target sangat cepat dan ringan karena hanya perlu menarik (*pull*) image yang sudah di-compile.

### A. Di PC Asal (Build & Push ke Docker Hub)

1. **Login ke Docker Hub**:
   ```bash
   docker login
   ```
   *(Masukkan username dan password / Access Token Docker Hub).*

2. **Build & Tag Image**:
   ```bash
   docker build -t gh4ni404/simacca-app:latest .
   ```

3. **Push Image ke Docker Hub**:
   ```bash
   docker push gh4ni404/simacca-app:latest
   ```

4. **Siapkan Backup File Upload (Opsional tapi Direkomendasikan)**:
   Agar foto profil guru/siswa dan logo sekolah lama langsung tampil di PC target:
   ```bash
   # Pilihan Ringan: Hanya foto profil & logo (±3.3 MB)
   tar -czvf profile_uploads.tar.gz -C writable/uploads profile logo

   # Atau Pilihan Lengkap: Semua foto jurnal, progress, absensi (±600 MB)
   tar -czvf uploads_backup.tar.gz -C writable uploads
   ```

---

### B. Di PC Target (Tarik Image & Jalankan)

1. **Buat folder project di Linux Home PC target (WSL)**:
   ```bash
   mkdir -p ~/simacca/cloudflare ~/simacca/writable/uploads
   cd ~/simacca
   ```

2. **Salin file-file konfigurasi berikut ke `~/simacca`**:
   * `docker-compose.yaml` (salin dari `docker-compose.prod.yaml`, pastikan nama image `gh4ni404/simacca-app:latest`)
   * `nginx.conf`
   * `.env` (berisi konfigurasi database cPanel & domain baseURL)
   * Folder `cloudflare/` (berisi file `config.yml` dan `<tunnel-id>.json`)
   * File arsip upload: `profile_uploads.tar.gz` atau `uploads_backup.tar.gz`

3. **Ekstrak File Upload & Set Izin Folder**:
   ```bash
   cd ~/simacca
   sudo chmod -R 777 writable
   tar -xzvf uploads_backup.tar.gz -C writable/
   sudo chmod -R 777 writable
   ```

4. **Tarik Image & Jalankan Container**:
   ```bash
   docker compose pull
   docker compose up -d
   ```

5. **Cek Status Semua Container**:
   ```bash
   docker compose ps
   ```
   *(Pastikan service `app`, `webserver`, dan `tunnel` berstatus `Up` / `running`).*

---

## 2. Metode 2: Distribusi Offline via File .tar (Flashdisk / LAN)

Gunakan metode ini jika PC target tidak memiliki koneksi internet untuk mengunduh image dari Docker Hub.

### A. Di PC Asal
1. **Export Image Docker**:
   ```bash
   docker save -o simacca-app.tar gh4ni404/simacca-app:latest
   ```
2. Salin `simacca-app.tar`, `docker-compose.prod.yaml` (rename jadi `docker-compose.yaml`), `nginx.conf`, `.env`, folder `cloudflare/`, dan `uploads_backup.tar.gz` ke Flashdisk/LAN.

### B. Di PC Target
1. **Masuk ke folder project**:
   ```bash
   mkdir -p ~/simacca/cloudflare ~/simacca/writable/uploads
   cd ~/simacca
   ```
2. **Load Image & Jalankan**:
   ```bash
   docker load -i simacca-app.tar
   sudo chmod -R 777 writable
   tar -xzvf uploads_backup.tar.gz -C writable/
   sudo chmod -R 777 writable
   docker compose up -d
   ```

---

## 3. Struktur File di PC Target

Pastikan struktur direktori di `~/simacca` pada PC target seperti berikut:

```text
simacca/
├── docker-compose.yaml          # File compose (image: gh4ni404/simacca-app:latest)
├── nginx.conf                   # Konfigurasi reverse proxy & fastcgi
├── .env                         # Konfigurasi environment & database cPanel
├── cloudflare/                  # Kredensial Cloudflare Tunnel
│   ├── config.yml
│   └── 0958554b-1e80-49b4-a292-03db1dd27535.json
└── writable/
    └── uploads/                 # Foto profil, jurnal, logo (persisten di harddisk)
        ├── absensi_guru/
        ├── izin/
        ├── jurnal/
        ├── jurnal_piket/
        ├── jurnal_pkl/
        ├── jurnal_wali/
        ├── logo/
        ├── pkl_progress/
        └── profile/
```

---

## 4. Sinkronisasi File Upload & Izin Folder (PENTING)

1. **Mengapa file upload berada di host (Volume)?**
   Folder `writable/uploads` sengaja tidak dimasukkan ke dalam Image Docker agar image tetap ringan dan data foto yang di-upload oleh user tidak hilang saat container di-restart atau di-pull ulang.
2. **Izin Folder (`Permission Denied`)**:
   Karena PHP di dalam Docker berjalan sebagai user `www-data` (UID 82), selalu pastikan folder `writable` memiliki izin tulis penuh:
   ```bash
   sudo chmod -R 777 ~/simacca/writable
   ```

---

## 5. Perintah Manajemen Docker

Jalankan perintah ini dari folder `~/simacca`:

| Kebutuhan | Perintah |
| :--- | :--- |
| **Melihat status container** | `docker compose ps` |
| **Melihat log aplikasi (PHP-FPM)** | `docker compose logs -f app` |
| **Melihat log webserver (Nginx)** | `docker compose logs -f webserver` |
| **Melihat log Cloudflare Tunnel** | `docker compose logs -f tunnel` |
| **Restart semua service** | `docker compose restart` |
| **Restart service tertentu** | `docker compose restart app` / `docker compose restart webserver` |
| **Update aplikasi (Tarik Image Baru)** | `docker compose pull app && docker compose up -d app` |
| **Menghentikan sementara** | `docker compose stop` |
| **Menyalakan kembali** | `docker compose start` |
| **Mematikan & melepas container** | `docker compose down` |
| **Masuk ke terminal container PHP** | `docker compose exec app sh` |

---

## 6. Kumpulan Perintah PHP Spark di Dalam Container

Jalankan perintah dari terminal PC target dengan format:
```bash
docker compose exec app php spark <perintah>
```

### 🧹 A. Pemeliharaan & Cache
* **Membersihkan Cache**: `docker compose exec app php spark cache:clear`
* **Membersihkan Session Expired**: `docker compose exec app php spark session:cleanup`
* **Membersihkan Token Expired**: `docker compose exec app php spark token:cleanup`

### ⏰ B. Otomasi SIMACCA
* **Tandai Guru Alpha**: `docker compose exec app php spark absensi:mark-alpha-guru`
* **Update Kelengkapan Profil**: `docker compose exec app php spark profile:set-completion`
* **Tes Email**: `docker compose exec app php spark email:test penerima@email.com`
* **Diagnostik SMTP**: `docker compose exec app php spark email:diagnostics`

### 🔧 C. Database & Migrasi
* **Jalankan Migrasi**: `docker compose exec app php spark migrate`
* **Status Migrasi**: `docker compose exec app php spark migrate:status`

---

## 7. Setup Cronjob di PC Target (Opsional)

Tambahkan di Host OS (`crontab -e`):
```cron
# Bersihkan sesi kadaluarsa setiap hari pukul 01:00 WITA
0 1 * * * cd /home/smkn_8_bone/simacca && docker compose exec -T app php spark session:cleanup >> /var/log/simacca_cron.log 2>&1

# Bersihkan token kadaluarsa setiap hari pukul 02:00 WITA
0 2 * * * cd /home/smkn_8_bone/simacca && docker compose exec -T app php spark token:cleanup >> /var/log/simacca_cron.log 2>&1

# Tandai guru alpha setiap hari Senin-Jumat pukul 16:30 WITA
30 16 * * 1-5 cd /home/smkn_8_bone/simacca && docker compose exec -T app php spark absensi:mark-alpha-guru >> /var/log/simacca_cron.log 2>&1
```

---

## 8. Troubleshooting & Solusi Masalah Nyata

### 1. Error `mkdir(): Permission denied` saat Upload Foto
* **Penyebab**: Folder upload baru belum dibuat atau folder `writable` dimiliki oleh user Linux host sehingga PHP (`www-data`) tidak bisa menulis.
* **Solusi**:
  ```bash
  sudo chmod -R 777 ~/simacca/writable
  ```

### 2. Foto Profil / Logo Sekolah Menampilkan 404 (Tidak Ditemukan)
* **Penyebab**: Database merujuk pada file gambar asli yang belum disalin ke PC target.
* **Solusi**: Ekstrak arsip `profile_uploads.tar.gz` atau `uploads_backup.tar.gz` ke dalam `~/simacca/writable/`:
  ```bash
  sudo chmod -R 777 ~/simacca/writable
  tar -xzvf uploads_backup.tar.gz -C writable/
  sudo chmod -R 777 ~/simacca/writable
  ```

### 3. Redirect Localhost (Misal `localhost:8081`) Mengarah ke `localhost/login` (Port Hilang)
* **Penyebab**: FastCGI Nginx menggunakan variabel `$host` yang memotong port.
* **Solusi**: Pastikan di `nginx.conf` menggunakan `$http_host`:
  ```nginx
  fastcgi_param HTTP_X_FORWARDED_HOST $http_host;
  ```
  Lalu restart webserver: `docker compose restart webserver`.

### 4. Cloudflare Tunnel Error / Bad Gateway 502
* **Penyebab**: Service `webserver` belum siap atau file kredensial tunnel tidak terbaca.
* **Solusi**:
  1. Cek log: `docker compose logs -f tunnel`
  2. Pastikan file JSON di `cloudflare/` memiliki izin baca: `chmod 644 cloudflare/*.json`
  3. Pastikan `config.yml` mengarah ke service Nginx internal: `service: http://webserver:80`.

### 5. Gambar Asset Publik (`assets/images/sekolah.png` / `provinsi.png`) Menampilkan 404 pada Laporan Cetak
* **Penyebab**: Di PC target, Nginx (`nginx:alpine`) tidak me-mount folder `public` host karena seluruh asset statis telah ter-bundling di dalam container PHP (`simacca_app`). Nginx mengoper request ke PHP-FPM, dan CodeIgniter kini telah dilengkapi fallback route otomatis untuk menyajikan asset tersebut.
* **Solusi**:
  1. Di PC Asal: Build & push image terbaru ke Docker Hub:
     ```bash
     docker build -t gh4ni404/simacca-app:latest .
     docker push gh4ni404/simacca-app:latest
     ```
  2. Di PC Target: Tarik image terbaru dan restart container:
     ```bash
     docker compose pull app
     docker compose up -d app
     ```
