# 🎓 SIMACCA - Sistem Monitoring Absensi dan Catatan Cara Ajar

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.4-red)](https://codeigniter.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**Framework:** CodeIgniter 4.6.4  
**Database:** MySQL  
**Styling:** Tailwind CSS  
**Version:** 2.0.0  
**Last Updated:** 2026-01-20

---

## 📖 Tentang SIMACCA

SIMACCA adalah sistem informasi berbasis web untuk monitoring absensi siswa dan pencatatan kegiatan belajar mengajar (KBM). Sistem ini dirancang untuk mempermudah guru, wali kelas, dan admin sekolah dalam mengelola data kehadiran siswa serta membuat laporan yang akurat.

### ✨ Fitur Utama

- 🔐 **Multi-Role System** - Admin, Guru Mapel, Wali Kelas, Siswa, Wakakur
- 📊 **Dashboard Interaktif** - Statistik real-time untuk setiap role
- ✅ **Absensi Digital** - Input cepat dengan UI mobile-friendly
- 📝 **Jurnal KBM** - Dokumentasi kegiatan belajar mengajar
- 📸 **Auto Image Optimization** - Kompresi otomatis 70-85%
- 👨‍🏫 **Guru Pengganti** - Sistem untuk guru piket/pengganti
- 📱 **Dual Layout System** - Auto-detect desktop & mobile layouts
- 🔓 **Admin Unlock Absensi** - Unlock absensi terkunci untuk edit
- 📧 **Email Notifications** - Password reset & notifikasi otomatis
- 📄 **Export Reports** - Download laporan dalam format Excel/PDF

---

## 🚀 Quick Start

**Ingin langsung coba? Ikuti 8 langkah ini (waktu: ~5 menit):**

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
nano .env  # atau gunakan text editor favorit

# 5. Buat database MySQL
mysql -u root -p -e "CREATE DATABASE simacca_db"

# 6. Setup database dengan data dummy
php spark setup --with-dummy

# 7. Jalankan server
php spark serve

# 8. Buka browser: http://localhost:8080
# Login: admin / admin123
```

⚠️ **Jangan lupa ganti password setelah login pertama!**

📖 **Butuh detail lengkap?** → [docs/guides/QUICK_START.md](docs/guides/QUICK_START.md)

---

## 📚 Dokumentasi

### 🎯 Untuk Pemula

| Dokumen | Deskripsi | Waktu |
|---------|-----------|-------|
| [**Quick Start Guide**](docs/guides/QUICK_START.md) | Panduan instalasi super cepat untuk testing | 5 menit |
| [**System Requirements**](docs/guides/REQUIREMENTS.md) | Cek kebutuhan sistem & compatibility | 5 menit |
| [**Panduan Instalasi**](docs/guides/PANDUAN_INSTALASI.md) | Panduan instalasi lengkap (Bahasa Indonesia) | 15-30 menit |

### 📖 Panduan Development

| Dokumen | Untuk Siapa? | Waktu |
|---------|--------------|-------|
| [**Layouts Complete Guide**](docs/guides/LAYOUTS_COMPLETE_GUIDE.md) | Developer | 20 menit |
| [**Deployment Guide**](docs/guides/DEPLOYMENT_GUIDE.md) | System Admin | 30-60 menit |
| [**Gmail App Password Setup**](docs/guides/GMAIL_APP_PASSWORD_SETUP.md) | Admin | 10 menit |

### 👥 Panduan User

| Dokumen | Untuk Siapa? | Waktu |
|---------|--------------|-------|
| [**Admin Unlock Absensi**](docs/guides/ADMIN_UNLOCK_ABSENSI_QUICKSTART.md) | Admin | 5 menit |
| [**Wakakur Role Guide**](docs/guides/WAKAKUR_ROLE_GUIDE.md) | Wakakur | 10 menit |

### 📋 Referensi Teknis

| Dokumen | Deskripsi |
|---------|-----------|
| [**Security Audit Report**](docs/summary/SECURITY_AUDIT_REPORT.md) | Security checklist & findings |
| [**Routes Optimization**](docs/summary/ROUTES_OPTIMIZATION_SUMMARY.md) | Route architecture |
| [**Bug Fixes Applied**](docs/summary/BUG_FIXES_APPLIED.md) | Historical bug fixes |
| [**Database Fix Summary**](docs/summary/DATABASE_FIX_SUMMARY.md) | Database schema fixes |

### 🗂️ Dokumentasi Lengkap

Dokumentasi telah diorganisir dan dirapikan. Struktur baru:

```
docs/
├── README.md        📚 Documentation index (START HERE!)
├── guides/          📖 How-to guides & tutorials (8 files)
├── summary/         📋 Technical summaries & specs (6 files)
└── email/           📧 Email service documentation
```

**👉 Lihat semua dokumentasi:** [docs/README.md](docs/README.md) ⭐ **NEW!**

---

## 🎯 Fitur Unggulan

### 📱💻 Dual Layout System (v2.0.0)
- **Auto-detection** - Otomatis pilih layout desktop/mobile
- **Desktop Layout** - Sidebar navigation, collapsible menu
- **Mobile Layout** - Bottom tab bar, touch-optimized
- **Manual Switch** - User bisa override pilihan layout
- **Responsive** - Seamless transition antar device

### 🔓 Admin Unlock Absensi (v2.0.0)
- **Single Unlock** - Unlock satu absensi dengan mudah
- **Bulk Unlock** - Unlock banyak absensi sekaligus
- **Time Tracking** - Monitor waktu unlock dengan jelas
- **Badge System** - Visual indicator untuk status locked/unlocked
- **24-hour Window** - Guru punya 24 jam untuk edit setelah unlock

### 👨‍🎓 Wakakur Role (v2.0.0)
- **Dual Access** - Bisa mengajar DAN supervisi
- **Teaching Features** - Akses penuh ke fitur guru (absensi, jurnal)
- **Admin Features** - Dashboard sekolah, laporan detail
- **Student Management** - Kelola data siswa sekolah
- **Permission Approval** - Approve izin siswa

### 📸 Auto Image Optimization (v1.5.0)
- **Auto-rotate EXIF orientation** - Foto landscape otomatis benar
- Kompresi otomatis 70-85% tanpa loss kualitas
- Profile photos: 800x800px @ 85% quality
- Journal photos: 1920x1920px @ 85% quality
- Support: JPEG, PNG, GIF, WebP

### 👨‍🏫 Guru Pengganti System (v1.2.0)
- Mode selection UI (Normal vs Pengganti)
- Auto-detect substitute teacher
- Dual ownership access control
- Full integration dengan absensi & jurnal

---

## 🛠️ Technology Stack

- **Backend:** PHP 8.1+ (CodeIgniter 4.6.4)
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** Tailwind CSS 3.x
- **JavaScript:** Vanilla JS (No frameworks)
- **Image Processing:** PHP GD Library + EXIF
- **Email:** SMTP (Gmail, Mailtrap, etc)

---

## 🔗 Quick Access URLs

Setelah server berjalan (`php spark serve`):

- **Login:** http://localhost:8080/login
- **Admin Dashboard:** http://localhost:8080/admin/dashboard
- **Guru Dashboard:** http://localhost:8080/guru/dashboard
- **Wakakur Dashboard:** http://localhost:8080/wakakur/dashboard
- **Wali Kelas Dashboard:** http://localhost:8080/walikelas/dashboard
- **Siswa Dashboard:** http://localhost:8080/siswa/dashboard

**Default Login:**
- Username: `admin`
- Password: `admin123`

**Note:** Untuk testing Wakakur role, upgrade user existing via command atau database.

---

## 🎯 Command Reference

| Command | Description |
|---------|-------------|
| `php spark setup` | Setup lengkap (migrations + seeding) |
| `php spark setup --with-dummy` | Setup dengan data dummy untuk testing |
| `php spark setup --force` | Reset database dan setup ulang |
| `php spark serve` | Jalankan development server |
| `php spark migrate:status` | Cek status migrations |
| `php spark cache:clear` | Clear application cache |
| `php spark email:test` | Test email configuration |
| `php spark token:cleanup` | Clean expired tokens |

---

## 📊 Module Status

| Module | Status | Progress | Last Update |
|--------|--------|----------|-------------|
| Authentication | ✅ Complete | 100% | 2026-01-15 |
| Admin Module | ✅ Complete | 100% | 2026-01-20 |
| Admin Unlock Absensi | ✅ Complete | 100% | 2026-01-20 |
| Guru Mapel Module | ✅ Complete | 100% | 2026-01-20 |
| Guru Pengganti/Piket | ✅ Complete | 100% | 2026-01-12 |
| Wakakur Module | ✅ Complete | 100% | 2026-01-20 |
| Wali Kelas Module | ✅ Complete | 100% | 2026-01-11 |
| Siswa Module | ✅ Complete | 100% | 2026-01-11 |
| Dual Layout System | ✅ Complete | 100% | 2026-01-20 |
| Profile & Photo | ✅ Complete | 100% | 2026-01-15 |
| Image Optimization | ✅ Complete | 100% | 2026-01-15 |
| Email Service | ✅ Complete | 100% | 2026-01-15 |

**Legend:**
- ✅ Complete - Fully functional & tested
- 🚧 In Progress - Under development
- 📋 Planned - Not yet started

---

## 🆘 Troubleshooting

### Database Connection Failed
```bash
# 1. Pastikan MySQL berjalan
sudo systemctl start mysql  # Linux
# atau net start mysql       # Windows

# 2. Cek kredensial di .env
nano .env

# 3. Buat database
mysql -u root -p -e "CREATE DATABASE simacca_db"
```

### Permission Errors (writable/)
```bash
# Linux/Mac
chmod -R 777 writable/

# Windows
# Right-click writable → Properties → Security → Edit permissions
```

### Composer Not Found
Download dan install dari [getcomposer.org](https://getcomposer.org/)

### Session/CSRF Errors
```bash
# Clear cache dan regenerate key
php spark cache:clear
php spark key:generate
```

📖 **Troubleshooting lengkap:** [docs/guides/PANDUAN_INSTALASI.md#troubleshooting](docs/guides/PANDUAN_INSTALASI.md)

---

## 🤝 Contributing

Kami sangat welcome kontribusi dari developer lain! Berikut cara berkontribusi:

1. Fork repository ini
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

📖 **Detail lengkap:** [CONTRIBUTING.md](CONTRIBUTING.md)

---

## 📝 License

Project ini dilisensikan under MIT License. Lihat [LICENSE](LICENSE) file untuk detail.

---

## 👥 Tim Pengembang

- **Mohd. Abdul Ghani** - Lead Developer
- **Dirwan Jaya** - Developer

---

## 📞 Support & Contact

Untuk pertanyaan, bug report, atau feature request:

- 📧 **Email:** [Email developer jika ada]
- 🐛 **Issues:** [GitHub Issues](https://github.com/gh4ni404/simacca/issues)
- 💬 **Discussions:** [GitHub Discussions](https://github.com/gh4ni404/simacca/discussions)

---

## 🌟 Star History

Jika project ini bermanfaat, jangan lupa kasih ⭐ di GitHub!

---

<div align="center">

**Made with ❤️ for Indonesian Education**

[⬆ Back to top](#-simacca---sistem-monitoring-absensi-dan-catatan-cara-ajar)

</div>
