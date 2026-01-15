# 📖 Panduan Instalasi SIMACCA

**Sistem Monitoring Absensi dan Catatan Cara Ajar**  
Panduan lengkap untuk menginstal dan menjalankan aplikasi SIMACCA dari repository GitHub.

---

## 📋 Daftar Isi

1. [Persyaratan Sistem](#-persyaratan-sistem)
2. [Instalasi Langkah demi Langkah](#-instalasi-langkah-demi-langkah)
3. [Konfigurasi Database](#-konfigurasi-database)
4. [Konfigurasi Email (Opsional)](#-konfigurasi-email-opsional)
5. [Menjalankan Aplikasi](#-menjalankan-aplikasi)
6. [Login Pertama Kali](#-login-pertama-kali)
7. [Troubleshooting](#-troubleshooting)
8. [FAQ](#-faq)

---

## 🖥️ Persyaratan Sistem

Sebelum memulai instalasi, pastikan sistem Anda memenuhi persyaratan berikut:

### Minimum Requirements

- **PHP**: 8.1 atau lebih tinggi
- **Database**: MySQL 5.7+ atau MariaDB 10.3+
- **Web Server**: Apache/Nginx (untuk production) atau PHP Development Server (untuk testing)
- **Composer**: 2.0 atau lebih tinggi
- **Git**: Untuk clone repository

### Extensions PHP yang Diperlukan

Pastikan extensions berikut sudah aktif di PHP Anda:

```ini
extension=intl
extension=mbstring
extension=json
extension=mysqlnd
extension=xml
extension=curl
extension=fileinfo
extension=gd
extension=zip
```

### Cara Cek Requirements

```bash
# Cek versi PHP
php -v

# Cek versi Composer
composer -v

# Cek extensions yang terinstall
php -m
```

---

## 🚀 Instalasi Langkah demi Langkah

### Langkah 1: Clone Repository

Clone repository dari GitHub ke komputer Anda:

```bash
# Via HTTPS
git clone https://github.com/username/simacca.git

# Atau via SSH
git clone git@github.com:username/simacca.git

# Masuk ke folder project
cd simacca
```

### Langkah 2: Install Dependencies

Install semua dependencies yang diperlukan menggunakan Composer:

```bash
composer install
```

**Catatan:** Proses ini akan memakan waktu beberapa menit tergantung kecepatan internet Anda.

### Langkah 3: Setup File Environment

Copy file environment template dan sesuaikan dengan konfigurasi Anda:

```bash
# Windows
copy env .env

# Linux/Mac
cp env .env
```

Buka file `.env` dengan text editor favorit Anda dan sesuaikan konfigurasi.

### Langkah 4: Generate Encryption Key

Generate encryption key untuk keamanan aplikasi:

```bash
php spark key:generate
```

Command ini akan otomatis mengupdate file `.env` Anda dengan encryption key yang baru.

---

## 🗄️ Konfigurasi Database

### Langkah 1: Buat Database

Buat database baru di MySQL/MariaDB Anda:

```sql
CREATE DATABASE simacca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Anda bisa menggunakan phpMyAdmin, MySQL Workbench, atau command line:

```bash
# Via command line
mysql -u root -p

# Kemudian jalankan perintah SQL di atas
```

### Langkah 2: Konfigurasi Koneksi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```ini
#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = simacca_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

**Sesuaikan:**
- `hostname`: Biasanya `localhost` atau `127.0.0.1`
- `database`: Nama database yang sudah Anda buat
- `username`: Username MySQL Anda (default: `root`)
- `password`: Password MySQL Anda (kosongkan jika tidak ada password)

### Langkah 3: Setup Database dengan Satu Perintah

SIMACCA menyediakan command khusus untuk setup database dengan mudah:

```bash
# Setup tanpa data dummy (recommended untuk production)
php spark setup

# Setup dengan data dummy (recommended untuk testing/development)
php spark setup --with-dummy
```

**Command ini akan:**
1. ✅ Menjalankan semua migrations (membuat tabel-tabel database)
2. ✅ Membuat user admin default
3. ✅ Membuat data dummy (jika menggunakan `--with-dummy`)

### Langkah 4: Verifikasi Setup Database

Cek apakah semua tabel sudah terbuat dengan benar:

```bash
php spark migrate:status
```

Anda akan melihat daftar migrations yang sudah dijalankan.

---

## 📧 Konfigurasi Email (Opsional)

Fitur email digunakan untuk reset password dan notifikasi. Jika Anda ingin menggunakan fitur ini, lakukan konfigurasi berikut:

### Konfigurasi Email di File .env

Edit file `.env` dan tambahkan/edit konfigurasi email:

```ini
#--------------------------------------------------------------------
# EMAIL
#--------------------------------------------------------------------
email.fromEmail = noreply@yourdomain.com
email.fromName = SIMACCA System

email.protocol = smtp
email.SMTPHost = smtp.gmail.com
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password-here
email.SMTPPort = 587
email.SMTPCrypto = tls
email.mailType = html
```

### Untuk Gmail (Recommended)

1. **Aktifkan 2-Step Verification** di Google Account Anda
2. **Generate App Password**:
   - Kunjungi: https://myaccount.google.com/apppasswords
   - Pilih "App" → "Other" → Ketik "SIMACCA"
   - Copy password yang dihasilkan (16 karakter tanpa spasi)
3. **Masukkan ke .env**:
   ```ini
   email.SMTPUser = your-email@gmail.com
   email.SMTPPass = abcd efgh ijkl mnop  # App password dari step 2
   ```

### Untuk SMTP Provider Lain

**Outlook/Office 365:**
```ini
email.SMTPHost = smtp.office365.com
email.SMTPPort = 587
email.SMTPCrypto = tls
```

**Yahoo:**
```ini
email.SMTPHost = smtp.mail.yahoo.com
email.SMTPPort = 587
email.SMTPCrypto = tls
```

### Test Konfigurasi Email

Setelah konfigurasi, test apakah email berhasil dikirim:

```bash
php spark email:test your-email@example.com
```

**Catatan:** Jika Anda skip langkah ini, fitur reset password tidak akan berfungsi, tapi aplikasi tetap bisa dijalankan.

---

## 🎮 Menjalankan Aplikasi

### Untuk Development/Testing

CodeIgniter 4 menyediakan built-in development server:

```bash
php spark serve
```

Aplikasi akan berjalan di: **http://localhost:8080**

**Options tambahan:**
```bash
# Jalankan di port lain
php spark serve --port=8081

# Jalankan di host tertentu
php spark serve --host=192.168.1.100 --port=8080
```

### Untuk Production

Untuk production, gunakan web server seperti Apache atau Nginx.

#### Apache Configuration

1. **Set Document Root** ke folder `public` (atau root folder jika tidak ada folder public)
   
2. **Enable mod_rewrite**:
   ```bash
   # Linux
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

3. **Contoh VirtualHost**:
   ```apache
   <VirtualHost *:80>
       ServerName simacca.yourdomain.com
       DocumentRoot /path/to/simacca
       
       <Directory /path/to/simacca>
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/simacca-error.log
       CustomLog ${APACHE_LOG_DIR}/simacca-access.log combined
   </VirtualHost>
   ```

4. **Update .env**:
   ```ini
   CI_ENVIRONMENT = production
   app.baseURL = 'http://simacca.yourdomain.com/'
   ```

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name simacca.yourdomain.com;
    root /path/to/simacca;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

---

## 🔑 Login Pertama Kali

Setelah setup selesai, Anda bisa login dengan credentials default:

### Default User Accounts

| Role | Username | Password | Email |
|------|----------|----------|-------|
| **Admin** | `admin` | `admin123` | admin@smkn8bone.sch.id |
| **Guru Mapel** | `dirwan.jaya1` | `guru123` | guru@smkn8bone.sch.id |
| **Wali Kelas** | `gani828` | `wali123` | wali@smkn8bone.sch.id |
| **Siswa** | `siswa1` | `siswa123` | siswa@smkn8bone.sch.id |

### ⚠️ PENTING: Keamanan

**Segera ganti password default setelah login pertama kali!**

1. Login dengan akun admin
2. Klik menu **Profile** di pojok kanan atas
3. Pilih **Change Password**
4. Masukkan password baru yang kuat

### First Steps

Setelah login sebagai admin:

1. ✅ **Ganti password admin**
2. ✅ **Hapus user dummy** (jika menggunakan `--with-dummy`)
3. ✅ **Tambahkan data guru** dari menu Admin → Guru
4. ✅ **Tambahkan data siswa** dari menu Admin → Siswa
5. ✅ **Buat jadwal mengajar** dari menu Admin → Jadwal

---

## 🔧 Troubleshooting

### Problem: Database Connection Failed

**Error:**
```
Unable to connect to the database.
```

**Solusi:**
1. Pastikan MySQL service berjalan:
   ```bash
   # Windows
   net start mysql
   
   # Linux
   sudo systemctl start mysql
   ```

2. Cek konfigurasi di file `.env`
3. Pastikan database sudah dibuat
4. Cek username dan password MySQL

### Problem: 404 Not Found

**Solusi:**
1. Pastikan `.htaccess` file ada di root folder
2. Enable mod_rewrite di Apache:
   ```bash
   sudo a2enmod rewrite
   ```
3. Cek konfigurasi VirtualHost

### Problem: Encryption Key Error

**Error:**
```
Encryption key not found
```

**Solusi:**
```bash
php spark key:generate
```

### Problem: Write Permission Error

**Error:**
```
Unable to write to writable/cache
```

**Solusi:**
```bash
# Linux/Mac
chmod -R 777 writable/

# Windows (run as Administrator)
icacls writable /grant Everyone:F /T
```

### Problem: Composer Install Gagal

**Solusi:**
1. Update composer:
   ```bash
   composer self-update
   ```

2. Clear composer cache:
   ```bash
   composer clear-cache
   ```

3. Install ulang:
   ```bash
   composer install --no-cache
   ```

### Problem: PHP Extensions Missing

**Error:**
```
Extension xxx is required
```

**Solusi:**

**Windows (XAMPP/WAMPP):**
1. Buka `php.ini`
2. Uncomment extension dengan menghapus `;` di depannya
3. Restart Apache

**Linux:**
```bash
# Install extensions
sudo apt-get install php8.1-intl php8.1-mbstring php8.1-mysql php8.1-xml php8.1-curl

# Restart Apache
sudo systemctl restart apache2
```

### Problem: Email Tidak Terkirim

**Solusi:**
1. Cek konfigurasi di `.env`
2. Pastikan App Password sudah benar (untuk Gmail)
3. Test dengan command:
   ```bash
   php spark email:diagnostics
   ```
4. Cek log error di `writable/logs/`

---

## ❓ FAQ

### 1. Apakah saya perlu folder `public`?

**Tidak.** Aplikasi ini menggunakan struktur CodeIgniter 4 default tanpa folder public. File `index.php` ada di root folder.

### 2. Bagaimana cara reset database?

```bash
# Reset dan setup ulang
php spark setup --force
```

**⚠️ Warning:** Command ini akan menghapus semua data!

### 3. Bagaimana cara menambahkan data dummy?

```bash
php spark db:seed DummyDataSeeder
```

### 4. Bagaimana cara backup database?

```bash
# Via command line
mysqldump -u root -p simacca_db > backup.sql

# Atau gunakan phpMyAdmin: Export → Go
```

### 5. Bagaimana cara restore database?

```bash
# Via command line
mysql -u root -p simacca_db < backup.sql

# Atau gunakan phpMyAdmin: Import → Choose File → Go
```

### 6. Apakah bisa dijalankan di shared hosting?

**Ya**, selama memenuhi requirements:
- PHP 8.1+
- MySQL/MariaDB
- Akses ke composer (untuk install dependencies)

Upload semua file kecuali folder `vendor`, lalu jalankan `composer install` via SSH atau cPanel Terminal.

### 7. Bagaimana cara update aplikasi?

```bash
# Pull update terbaru
git pull origin main

# Update dependencies
composer update

# Run migrations jika ada yang baru
php spark migrate

# Clear cache
php spark cache:clear
```

### 8. Di mana file log disimpan?

Log disimpan di: `writable/logs/`

Cek log jika ada error:
```bash
# Linux/Mac
tail -f writable/logs/log-*.log

# Windows
type writable\logs\log-*.log
```

### 9. Bagaimana cara mengganti logo dan branding?

Edit file berikut:
- **Logo**: `public/assets/img/logo.png` (jika ada)
- **Nama aplikasi**: Edit file `app/Config/App.php`
- **Email template**: Edit files di `app/Views/emails/`

### 10. Apakah ada mode maintenance?

Ya, edit file `.env`:
```ini
# Enable maintenance mode
# app.maintenanceMode = true
```

Uncomment baris tersebut untuk enable maintenance mode.

---

## 📞 Dukungan

Jika Anda mengalami masalah yang tidak tercantum di panduan ini:

1. **Check Documentation**: Baca file-file `.md` di repository
2. **Check Logs**: Lihat error di `writable/logs/`
3. **Create Issue**: Buat issue di GitHub repository
4. **Contact Developer**: Hubungi tim developer

---

## 📝 Catatan Penting

### Keamanan

1. ✅ **Jangan commit file `.env`** ke repository
2. ✅ **Ganti semua password default**
3. ✅ **Generate encryption key baru**
4. ✅ **Set permission yang benar** untuk folder `writable/`
5. ✅ **Enable HTTPS** untuk production

### Performance

1. Enable opcache untuk production
2. Set `CI_ENVIRONMENT = production` di file `.env`
3. Gunakan cache untuk query database yang sering diakses
4. Optimize autoloader dengan `composer dump-autoload --optimize`

### Backup

Lakukan backup regular:
1. **Database**: Minimal 1x per hari
2. **Uploaded files**: Folder `writable/uploads/`
3. **Configuration**: File `.env`

---

## 🎉 Selesai!

Sekarang aplikasi SIMACCA sudah siap digunakan!

Akses aplikasi Anda di:
- **Development**: http://localhost:8080
- **Production**: http://yourdomain.com

**Happy coding! 🚀**

---

*Panduan ini dibuat untuk memudahkan instalasi dan setup aplikasi SIMACCA.*  
*Terakhir diupdate: 2026-01-15*
