# Sistem Monitoring Absensi dan Catatan Cara Ajar

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-red)](https://codeigniter.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> **⚡ Quick Start:** Butuh panduan cepat? Lihat [QUICK_START.md](QUICK_START.md) untuk instalasi dalam 5 menit!

**Framework:** Codeigniter 4.6.4
**Database:** MySQL
**Styling:** Tailwind CSS
**Authentication:** Myth/Auth

## 🎯 Fitur Utama

### 1. 🔐 Authentication & Authorization
- Multi-role system (Admin, Guru Mata Pelajaran, Guru Wali Kelas, Siswa)
- Login/Logout System dengan session management
- Role-based access control menggunakan filters
- Change password functionality
- Forgot password (in progress)

### 2. 👤 Admin Module
- **Dashboard** dengan statistik real-time
- **Manajemen Guru** - CRUD, Import/Export Excel, Status Active/Inactive
- **Manajemen Siswa** - CRUD, Import/Export Excel, Bulk Actions
  - 🆕 **Auto-Create Kelas saat Import** ✅ (2026-01-12)
    - Smart parsing: X-RPL, XI-TKJ, XII-MM, 10-RPL, 11-RPL, 12-RPL
    - Kelas baru otomatis dibuat dengan tingkat & jurusan yang sesuai
    - Performance optimized: 95% query reduction, 50% faster import
    - Comprehensive validation & detailed error messages
- **Manajemen Kelas** - CRUD, Assign Wali Kelas, Move Siswa
- **Manajemen Mata Pelajaran** - CRUD dengan KKM
- **Manajemen Jadwal** - CRUD dengan conflict detection
- **Laporan** - Absensi & Statistik

### 3. 👨‍🏫 Guru Mata Pelajaran Module
- **Dashboard** dengan ringkasan jadwal & statistik
- **Jadwal Mengajar** - Lihat jadwal per hari/minggu
- **Input Absensi** - CRUD dengan materi pembelajaran
- **Jurnal KBM** - Pencatatan kegiatan belajar mengajar
- **Laporan** - Rekapitulasi absensi & export
- **Print Absensi** - Print ready format
- **🆕 Guru Pengganti/Piket** ✅ NEW (2026-01-12)
  - Mode selection untuk input absensi (Normal vs Pengganti)
  - Lihat semua jadwal di mode pengganti
  - Auto-detect dan record guru pengganti
  - Dual ownership access control (creator & schedule owner)
  - Integrated dengan jurnal KBM dan laporan admin

### 4. 👨‍👩‍👧‍👦 Wali Kelas Module ✅ COMPLETE
- **Dashboard** - Statistik kelas dengan visual analytics
- **Data Siswa** - Monitoring siswa dengan kehadiran bulan ini
- **Absensi** - Monitor kehadiran kelas dengan filter periode
- **Persetujuan Izin** - Approve/Reject izin siswa dengan catatan
- **Laporan** - Laporan kehadiran lengkap (rekapitulasi & per siswa)

### 5. 🎓 Siswa Module ✅ COMPLETE
- **Dashboard** - Info personal, jadwal & statistik kehadiran
- **Jadwal Pelajaran** - Timeline view jadwal harian/mingguan
- **Riwayat Absensi** - Track kehadiran dengan filter periode
- **Pengajuan Izin** - Submit & tracking izin dengan dokumen
- **Profil** - Update data personal & change password

### 6. 📸 Profile Photo & Image Optimization System ✅ NEW (2026-01-15)
- **Profile Photo Upload** - Upload & manage profile photos untuk semua role
  - Display di navbar, profile page, list guru/siswa
  - Upload limit 5MB dengan auto-optimization
  - Fallback ke avatar initials jika tidak ada foto
- **Automatic Image Optimization** - Kompresi otomatis semua gambar
  - 70-85% file size reduction tanpa kehilangan kualitas visible
  - Profile photos: optimized ke 800x800px
  - Journal photos: optimized ke 1920x1920px  
  - Permission letters: optimized ke 1920x1920px (skip PDF)
  - Support JPEG, PNG, GIF, WebP formats
  - Real-time compression statistics logging

## 🗄️ Struktur Database
- `users` - Data user multi-role dengan authentication
- `guru` - Data guru (NIP, nama, kontak)
- `siswa` - Data siswa (NIS, NISN, nama, kontak)
- `kelas` - Data kelas dengan wali kelas
- `mata_pelajaran` - Data mata pelajaran dengan KKM
- `jadwal_mengajar` - Jadwal mengajar (guru, kelas, mapel, waktu)
- `absensi` - Header absensi (tanggal, pertemuan, materi, **guru_pengganti_id** 🆕)
- `absensi_detail` - Detail absensi per siswa (H/S/I/A)
- `jurnal_kbm` - Jurnal Kegiatan Belajar Mengajar
- `izin_siswa` - Data izin siswa dengan approval workflow

**🆕 Database Enhancements (2026-01-12):**
- Field `guru_pengganti_id` di tabel `absensi` untuk record guru pengganti
- Foreign key constraint dengan ON DELETE SET NULL
- Enhanced queries dengan dual ownership logic (OR conditions)

## 🎯 Recent Updates

### 📸 Profile Photo & Image Optimization (2026-01-15) - v1.4.0
**Features:** 
- Profile photo upload & management untuk semua user
- Automatic image compression (70-85% reduction)
- Display photos di navbar, lists, dan detail pages
- Smart optimization: Profile (800px), Jurnal/Izin (1920px)

**Impact:**
- Storage savings: 81% average reduction
- Page load: 3-5x faster
- Bandwidth: 83% reduction  
- Upload limit increased: 2MB → 5MB

### ✅ Import Siswa Auto-Create Kelas (2026-01-12)
**Problem:** Saat import siswa dengan kelas baru, kelas tidak otomatis dibuat  
**Solution:** Auto-create kelas dengan smart parsing & comprehensive validation  
**Impact:** 50% faster import, 95% query reduction, Grade A- CI4 compliance

### ✅ Guru Pengganti/Piket System (2026-01-12)
**Features:** Mode selection, dual ownership access, auto-detect substitute teacher  
**Integration:** Absensi, Jurnal KBM, Laporan Admin

### ✅ Security & Performance
- CSRF protection across all forms
- Session security fixes
- Performance optimization (request-scoped caching)
- **Image optimization system** - 70-85% compression
- CI4 4.6.4 best practices compliance (92%)

📝 **Lihat detail lengkap di:** [FEATURES.md](FEATURES.md) & [TODO.md](TODO.md)

---

## 🚀 Quick Start (TL;DR)

**Ingin langsung coba aplikasi?** Ikuti langkah ini:

```bash
# 1. Clone repository
git clone https://github.com/gh4ni404/simacca.git
cd simacca

# 2. Install dependencies
composer install

# 3. Setup environment
cp env .env
php spark key:generate

# 4. Edit .env - konfigurasi database
# nano .env  (atau gunakan text editor)

# 5. Buat database MySQL
mysql -u root -p -e "CREATE DATABASE simacca_db"

# 6. Setup database dengan data dummy
php spark setup --with-dummy

# 7. Jalankan server
php spark serve

# 8. Buka browser: http://localhost:8080
# Login: admin / admin123
```

**🎯 Total waktu: ~5 menit**

⚠️ **Ganti password setelah login pertama!**

📖 **Detail lengkap:** [QUICK_START.md](QUICK_START.md) | [PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md)

---

## 🚀 Production Deployment

> **💡 Tip:** Untuk panduan deployment lengkap (shared hosting, VPS, cloud), lihat [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)

### Quick Production Checklist

- [ ] Copy `.env.production` → `.env`
- [ ] Set `CI_ENVIRONMENT = production`
- [ ] Configure database credentials
- [ ] Generate encryption key: `php spark key:generate`
- [ ] Run migrations: `php spark migrate`
- [ ] Set permissions: `chmod -R 777 writable/`
- [ ] Configure web server (Apache/Nginx)
- [ ] Setup SSL certificate
- [ ] Test email configuration
- [ ] Setup backup schedule

📖 **Detail lengkap:** [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)

---

## 🚀 Production Deployment (Detail)

### Directory Structure
SIMACCA uses a split directory structure for enhanced security:

```
/home/user/
├── simacca_public/          # Web-accessible directory (Document Root)
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│
└── simaccaProject/          # Application files (NOT web-accessible)
    ├── app/
    ├── vendor/
    ├── writable/
    └── .env
```

### Deployment Steps

1. **Upload Files**
   - Upload `public/` contents to your web root (e.g., `public_html/`)
   - Upload application files to a directory outside web root

2. **Configure Paths**
   - Edit `public/index.php` line 50:
     ```php
     require FCPATH . '../simaccaProject/app/Config/Paths.php';
     ```

3. **Configure Environment**
   - Copy `.env.production` to `.env`
   - Update database credentials
   - Set `CI_ENVIRONMENT = production`
   - **Important:** DO NOT use PHP constants in .env file
   - **Important:** Comment out `session.savePath` and `logger.path` to use defaults

4. **Set Permissions**
   ```bash
   chmod 600 .env
   chmod -R 775 writable/
   ```

5. **Generate Encryption Key**
   ```bash
   php spark key:generate
   ```

### Important .env Configuration Notes

❌ **DON'T** use PHP constants or concatenation in .env:
```ini
# WRONG - These will cause errors
session.savePath = null
logger.path = WRITEPATH . 'logs/'
```

✅ **DO** comment them out to use defaults:
```ini
# CORRECT - Let CodeIgniter use defaults
# session.savePath = null
# logger.path = WRITEPATH . 'logs/'
```

---

# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library


## 📚 Dokumentasi

> **📑 Lihat daftar lengkap semua dokumentasi:** [DOKUMENTASI_INDEX.md](DOKUMENTASI_INDEX.md)

### 📖 Panduan Instalasi & Deployment

Pilih panduan yang sesuai dengan kebutuhan Anda:

| 📄 Dokumen | 🎯 Untuk Siapa? | ⏱️ Waktu | 📝 Deskripsi |
|-----------|----------------|----------|--------------|
| [**QUICK_START.md**](QUICK_START.md) | Pemula yang ingin coba aplikasi | 5 menit | Panduan super cepat untuk testing/development |
| [**GETTING_STARTED.md**](GETTING_STARTED.md) | Semua user | 2 menit | Panduan memilih skenario instalasi yang tepat |
| [**PANDUAN_INSTALASI.md**](PANDUAN_INSTALASI.md) | Developer & System Admin | 15-30 menit | Panduan lengkap instalasi + troubleshooting |
| [**DEPLOYMENT_GUIDE.md**](DEPLOYMENT_GUIDE.md) | System Admin | 30-60 menit | Panduan deployment ke production (shared hosting, VPS, cloud) |
| [**REQUIREMENTS.md**](REQUIREMENTS.md) | Semua | 5 menit | Daftar lengkap system requirements & compatibility |

### 🎓 Panduan Khusus Fitur

| 📄 Dokumen | 📝 Deskripsi |
|-----------|--------------|
| [EMAIL_SERVICE_QUICKSTART.md](EMAIL_SERVICE_QUICKSTART.md) | Setup email notification & reset password |
| [IMPORT_JADWAL_DOCUMENTATION.md](IMPORT_JADWAL_DOCUMENTATION.md) | Import jadwal mengajar via Excel |
| [FEATURE_ADMIN_UNLOCK_ABSENSI.md](FEATURE_ADMIN_UNLOCK_ABSENSI.md) | Fitur admin unlock absensi yang sudah dikunci |

### 📋 Changelog & Updates

| 📄 Dokumen | 📝 Deskripsi |
|-----------|--------------|
| [CHANGELOG.md](CHANGELOG.md) | History perubahan & update aplikasi |
| [FEATURES.md](FEATURES.md) | Daftar lengkap fitur aplikasi |

---

## 🚀 Quick Start (TL;DR)

**Ingin langsung coba aplikasi?** Ikuti langkah ini:

```bash
# 1. Clone repository
git clone https://github.com/username/simacca.git
cd simacca

# 2. Install dependencies
composer install

# 3. Setup environment
cp env .env
php spark key:generate

# 4. Edit .env - konfigurasi database
# nano .env  (atau gunakan text editor)

# 5. Buat database MySQL
mysql -u root -p -e "CREATE DATABASE simacca_db"

# 6. Setup database dengan data dummy
php spark setup --with-dummy

# 7. Jalankan server
php spark serve

# 8. Buka browser: http://localhost:8080
# Login: admin / admin123
```

**🎯 Total waktu: ~5 menit**

📖 **Detail lengkap:** [QUICK_START.md](QUICK_START.md)

---

## 📚 Dokumentasi (Legacy)

Untuk informasi lebih detail tentang fitur dan development:

### General Documentation
- **[FEATURES.md](FEATURES.md)** - Daftar lengkap fitur sistem dengan detail setiap modul
- **[TODO.md](TODO.md)** - Task list pengembangan dan bug tracking
- **[CodeIgniter 4 User Guide](https://codeigniter.com/user_guide/)** - Dokumentasi framework

### Technical Notes
All detailed technical documentation has been archived. Key information about recent updates and fixes can be found in the **Recent Updates & Fixes** section of FEATURES.md.

## 🎯 Quick Commands Reference

| Command | Description |
|---------|-------------|
| `php spark setup` | Setup lengkap (migrations + seeding) |
| `php spark setup --with-dummy` | Setup dengan data dummy |
| `php spark setup --force` | Reset dan setup ulang |
| `php spark serve` | Jalankan development server |
| `php spark migrate:status` | Cek status migrations |

### 🔗 Access URLs

Setelah server berjalan (`php spark serve`):

- **Login:** http://localhost:8080/login
- **Admin Dashboard:** http://localhost:8080/admin/dashboard
- **Guru Dashboard:** http://localhost:8080/guru/dashboard
- **Wali Kelas Dashboard:** http://localhost:8080/walikelas/dashboard
- **Siswa Dashboard:** http://localhost:8080/siswa/dashboard

## 📊 Status Pengembangan

| Module | Status | Progress | Last Update |
|--------|--------|----------|-------------|
| Authentication | ✅ Complete | 100% | 2026-01-11 |
| Admin Module | ✅ Complete | 100% | 2026-01-11 |
| Guru Mapel Module | ✅ Complete | 100% | 2026-01-11 |
| **Guru Pengganti/Piket** | ✅ Complete | 100% | **2026-01-12** 🆕 |
| Wali Kelas Module | ✅ Complete | 100% | 2026-01-11 |
| Siswa Module | ✅ Complete | 100% | 2026-01-11 |
| Profile Module | ⚠️ Partial | 30% | - |
| Notification System | 📋 Planned | 0% | - |
| Mobile API | 📋 Planned | 0% | - |

**Legend:**
- ✅ Complete - Fully functional & tested
- ⚠️ Partial - Controller exists, views needed
- 📋 Planned - Not yet started
- 🆕 New - Recently added feature

## 🤝 Contributing

Jika ingin berkontribusi pada project ini:

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Tim Pengembang

- **Mohd. Abdul Ghani** - Lead Developer
- **Dirwan Jaya** - Developer

---

## 🆘 Troubleshooting

### Error: "Database connection failed"
1. Pastikan MySQL service berjalan
2. Cek kredensial di `.env`
3. Pastikan database sudah dibuat: `CREATE DATABASE simacca_db;`

### Error: "Table already exists"
Reset database:
```bash
php spark setup --force
```

### Error: "composer not found"
Install Composer dari https://getcomposer.org/

### Writable Folder Permission Error
```bash
# Linux/Mac
chmod -R 777 writable/

# Windows
Right-click writable folder → Properties → Security → Edit permissions
```

**📖 Troubleshooting lengkap:** [INSTALL.md](INSTALL.md#troubleshooting)

## 📞 Support

Untuk pertanyaan atau issue, silakan:
- Open issue di GitHub repository
- Contact: [Email/WhatsApp jika ada]

---

## 🎉 What's New (January 2026)

### 🆕 Guru Pengganti/Piket System (2026-01-12)
Sistem lengkap untuk menangani situasi guru pengganti ketika guru utama berhalangan hadir:

**Key Features:**
- ✅ **Mode Selection UI** - Toggle antara "Jadwal Saya Sendiri" dan "Guru Pengganti"
- ✅ **Smart Access Control** - Guru pengganti bisa akses jadwal guru lain
- ✅ **Auto-Detection** - Sistem otomatis detect dan record guru pengganti
- ✅ **Dual Ownership** - Both creator dan schedule owner bisa manage absensi
- ✅ **Full Integration** - Terintegrasi dengan absensi, jurnal KBM, dan laporan

**Benefits:**
- Transparansi penuh siapa guru pengganti di setiap pertemuan
- Guru asli tetap bisa monitor dan manage absensi yang diinput pengganti
- Data tercatat lengkap untuk keperluan administrasi dan pelaporan

**Documentation:** 7 comprehensive markdown files dengan flow diagrams dan test scenarios

### 🔒 Security Enhancements
- ✅ CSRF Protection across all forms
- ✅ Session security improvements
- ✅ Proper logout mechanism
- ✅ Fixed authentication redirect loops
- ✅ XSS protection improvements
- ✅ Error message sanitization

---

**Version:** 1.1.0  
**Last Updated:** 2026-01-12
 
