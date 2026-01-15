# 🎯 Getting Started - SIMACCA

Panduan memulai untuk pengguna baru. Pilih skenario yang sesuai dengan kebutuhan Anda.

---

## 🤔 Saya Ingin...

### 1️⃣ Langsung coba aplikasi (Testing/Development)

**Waktu: ~5 menit**

```bash
# Clone repository
git clone https://github.com/username/simacca.git
cd simacca

# Install dependencies
composer install

# Setup environment
cp env .env
php spark key:generate

# Edit .env - minimal konfigurasi database
nano .env  # atau gunakan text editor favorit

# Buat database
mysql -u root -p -e "CREATE DATABASE simacca_db"

# Setup dengan data dummy
php spark setup --with-dummy

# Jalankan server
php spark serve
```

**Buka browser:** http://localhost:8080  
**Login:** username `admin`, password `admin123`

📖 **Detail:** [QUICK_START.md](QUICK_START.md)

---

### 2️⃣ Install untuk production (Tanpa data dummy)

**Waktu: ~15 menit**

```bash
# Clone dan install
git clone https://github.com/username/simacca.git
cd simacca
composer install --no-dev --optimize-autoloader

# Setup environment
cp env .env
php spark key:generate

# Konfigurasi lengkap di .env
nano .env

# Setup database (tanpa dummy)
php spark setup

# Setup web server (Apache/Nginx)
# Lihat panduan deployment
```

📖 **Detail:** [PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md)

---

### 3️⃣ Deploy ke shared hosting

**Waktu: ~20 menit**

1. **Persiapan di local:**
   ```bash
   composer install --no-dev
   zip -r simacca.zip . -x "*.git*" "vendor/*"
   ```

2. **Upload via FTP/cPanel**
3. **Extract di server**
4. **Install dependencies** (via SSH jika tersedia)
5. **Setup database** via phpMyAdmin
6. **Configure .env**

📖 **Detail:** [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md#-deployment-ke-shared-hosting)

---

### 4️⃣ Deploy ke VPS/Cloud Server

**Waktu: ~30 menit**

```bash
# Di server
sudo apt update
sudo apt install apache2 mysql-server php8.1 composer git

# Clone dan setup
cd /var/www
git clone https://github.com/username/simacca.git
cd simacca
composer install --no-dev

# Configure
cp env .env
php spark key:generate
nano .env

# Database
php spark migrate
php spark db:seed AdminSeeder

# Web server & SSL
sudo a2enmod rewrite
sudo certbot --apache
```

📖 **Detail:** [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md#-deployment-ke-vpsdedicated-server-linux)

---

## 📚 Dokumentasi Lengkap

| Dokumen | Deskripsi | Untuk Siapa? |
|---------|-----------|--------------|
| [QUICK_START.md](QUICK_START.md) | Panduan super cepat 5 menit | Pemula yang ingin coba aplikasi |
| [PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md) | Panduan lengkap instalasi | Semua user, referensi utama |
| [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) | Panduan deployment production | System admin, deployment ke server |
| [README.md](README.md) | Overview aplikasi & fitur | Semua user |

---

## 🔑 Default Credentials

Setelah setup, gunakan credentials berikut untuk login:

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Guru Mapel | `dirwan.jaya1` | `guru123` |
| Wali Kelas | `gani828` | `wali123` |
| Siswa | `siswa1` | `siswa123` |

⚠️ **PENTING:** Ganti semua password default setelah login!

---

## ✅ Minimum Requirements

Pastikan sistem Anda memenuhi:

- ✅ PHP 8.1 or higher
- ✅ MySQL 5.7+ or MariaDB 10.3+
- ✅ Composer 2.0+
- ✅ Git

**Cek requirements:**
```bash
php -v
composer -v
mysql --version
```

---

## 🆘 Butuh Bantuan?

### Masalah Umum

**❌ Database connection failed**
```bash
# Cek MySQL service
# Windows: net start mysql
# Linux: sudo systemctl start mysql

# Cek konfigurasi .env
```

**❌ Encryption key not found**
```bash
php spark key:generate
```

**❌ Permission denied (writable folder)**
```bash
# Linux/Mac
chmod -R 777 writable/

# Windows (as admin)
icacls writable /grant Everyone:F /T
```

**❌ Composer install gagal**
```bash
composer clear-cache
composer install
```

### Dokumentasi Troubleshooting

📖 Lihat section troubleshooting di [PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md#-troubleshooting)

---

## 🎓 Next Steps

Setelah aplikasi berjalan:

1. ✅ Login dengan akun admin
2. ✅ Ganti password default
3. ✅ Explore menu dan fitur
4. ✅ Tambahkan data guru (Admin → Guru)
5. ✅ Tambahkan data siswa (Admin → Siswa)
6. ✅ Buat jadwal mengajar (Admin → Jadwal)

---

## 📞 Support

- 📖 **Dokumentasi**: Lihat file `.md` di repository
- 🐛 **Bug Report**: Create issue di GitHub
- 💡 **Feature Request**: Create issue di GitHub
- 📧 **Email**: support@yourcompany.com

---

## 🎯 Quick Links

- [Features Overview](README.md#-fitur-utama)
- [System Architecture](README.md#-struktur-sistem)
- [API Documentation](README.md#-api-documentation)
- [Changelog](CHANGELOG.md)

---

**Selamat menggunakan SIMACCA! 🚀**

*Sistem Monitoring Absensi dan Catatan Cara Ajar*

---

*Getting Started Guide v1.0*  
*Terakhir diupdate: 2026-01-15*
