# 🎓 SIMACCA - Sistem Monitoring Absensi dan Catatan Cara Ajar

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.4-red)](https://codeigniter.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**Framework:** CodeIgniter 4.6.4  
**Database:** MySQL  
**Styling:** Tailwind CSS  
**Version:** 3.0.0  
**Last Updated:** 2026-08-02

---

## 📖 Tentang SIMACCA

SIMACCA adalah sistem informasi berbasis web untuk monitoring absensi siswa dan pencatatan kegiatan belajar mengajar (KBM). Sistem ini dirancang untuk mempermudah guru, wali kelas, dan admin sekolah dalam mengelola data kehadiran siswa serta membuat laporan yang akurat.

### ✨ Fitur Utama

- 🔐 **Multi-Role System** - Admin, Guru Mapel, Wali Kelas, Siswa, Wakakur, Instruktur, Ketua Jurusan
- 📊 **Dashboard Interaktif** - Statistik real-time untuk setiap role
- ✅ **Absensi Digital** - Input cepat dengan UI mobile-friendly
- 👨‍🏫 **Absensi Guru** - Self check-in/check-out dengan foto & kamera
- 📝 **Jurnal KBM** - Dokumentasi kegiatan belajar mengajar
- 🏭 **Jurnal PKL (Task-Oriented)** - Jurnal praktik kerja lapangan berbasis tugas
- 🔍 **Verifikasi 2-Tahap** - Verifikasi pembimbing + instruktur untuk jurnal PKL
- 📸 **Auto Image Optimization** - Kompresi otomatis 70-85%
- 👨‍🏫 **Guru Pengganti** - Sistem untuk guru piket/pengganti
- 📱 **Dual Layout System** - Auto-detect desktop & mobile layouts
- 🔓 **Admin Unlock Absensi** - Unlock absensi terkunci untuk edit
- 📧 **Email Notifications** - Password reset, welcome, perubahan password/email, notifikasi
- 📄 **Export Reports** - Download laporan dalam format Excel (browser print untuk PDF)
- 🏭 **Manajemen PKL** - Pembimbing, tempat, siswa, kategori, dan task PKL
- 📋 **Izin Guru** - Sistem pengajuan izin guru dengan persetujuan wakakur
- 📊 **Laporan Mingguan** - Cetak laporan mengajar mingguan
- 🔄 **Rollover Tahun Ajaran** - Backup & restore data antar tahun ajaran
- 👥 **Multi-Role Users** - Satu user bisa memiliki beberapa role sekaligus
- 🔒 **Security Helpers** - Validasi upload file, sanitasi filename, safe redirect
- 🧩 **Component Library** - Komponen UI reusable (alerts, badges, cards, modals, tables)
- 🖨️ **Print Layout** - Template cetak khusus untuk laporan
- 💬 **Casual Message System** - Pesan ramah pengguna berbahasa Indonesia
- 🎓 **XII Filter** - Batasi akses Jurnal PKL hanya untuk siswa kelas 12

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
| [**Security Audit Report**](docs/audit/SECURITY_AUDIT_REPORT.md) | Security checklist & findings |
| [**Implementation Details**](docs/archive/IMPLEMENTATION_DETAILS.md) | Technical implementation (routes, database, statistics) |
| [**Bug Fixes History**](docs/archive/BUG_FIXES.md) | Historical bug fixes |
| [**Code Quality Audit**](docs/audit/CODE_QUALITY_ARCHITECTURE_AUDIT_2026-01-30.md) | Code quality assessment |

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
- **Layout Switcher** - URL endpoints untuk testing (`/layout/desktop`, `/layout/mobile`, `/layout/auto`)
- **Device Info** - Endpoint `/layout/device-info` untuk debug device detection

### 🔓 Admin Unlock Absensi (v2.0.0)
- **Single Unlock** - Unlock satu absensi dengan mudah
- **Bulk Unlock** - Unlock banyak absensi sekaligus
- **Time Tracking** - Monitor waktu unlock dengan jelas
- **Badge System** - Visual indicator untuk status locked/unlocked
- **24-hour Window** - Guru punya 24 jam untuk edit setelah unlock
- **Absensi Guru Monitoring** - Monitor absensi guru dengan detail & laporan

### 👨‍🎓 Wakakur Role (v2.0.0)
- **Dual Access** - Bisa mengajar DAN supervisi
- **Teaching Features** - Akses penuh ke fitur guru (absensi, jurnal)
- **Admin Features** - Dashboard sekolah, laporan detail
- **Student Management** - Kelola data siswa sekolah
- **Permission Approval** - Approve izin siswa & guru
- **Absensi Guru Monitoring** - Monitor & export absensi guru
- **Print Laporan** - Cetak laporan administrasi

### 📸 Auto Image Optimization (v1.5.0)
- **Auto-rotate EXIF orientation** - Foto landscape otomatis benar
- Kompresi otomatis 70-85% tanpa loss kualitas
- Profile photos: 800x800px @ 85% quality
- Journal photos: 1920x1920px @ 85% quality
- Absensi guru photos: Auto-optimize saat check-in/check-out
- Jurnal PKL photos: Auto-optimize untuk dokumentasi PKL
- PKL progress photos: Auto-optimize untuk bukti tugas
- Support: JPEG, PNG, GIF, WebP
- Secure file serving via FileController

### 👨‍🏫 Guru Pengganti System (v1.2.0)
- Mode selection UI (Normal vs Pengganti)
- Auto-detect substitute teacher
- Dual ownership access control
- Full integration dengan absensi & jurnal

### 🖨️ Print Layout System (v3.0.0) [NEW]
- **Print Layout Template** - Template cetak khusus untuk semua laporan
- **Guru Absensi Print** - Cetak absensi per pertemuan
- **Guru Jurnal Print** - Cetak jurnal KBM
- **Guru Laporan Print** - Cetak laporan mengajar
- **Weekly Report Print** - Cetak laporan mingguan
- **Siswa PKL Print** - Cetak jurnal PKL, catatan, rekap
- **Admin Laporan Print** - Cetak detail absensi admin
- **Wakakur Laporan Print** - Cetak laporan wakakur

### 🏭 PKL Module (v3.0.0) [NEW]
- **Manajemen Pembimbing PKL** - Assign guru sebagai pembimbing PKL
- **Manajemen Tempat PKL** - Kelola lokasi/prusahaan PKL
- **Manajemen Siswa PKL** - Assign siswa ke tempat PKL
- **Kategori PKL** - Kategorisasi kegiatan PKL
- **Mapping Kategori** - Mapping kategori ke kelas/jurusan
- **Master Task PKL** - Template tugas PKL untuk instruktur
- **Jurnal PKL Task-Oriented** - Jurnal berbasis tugas dengan tracking per hari
- **Verifikasi 2-Tahap** - Pembimbing verifikasi → Instruktur verifikasi
- **Rekap Absensi PKL** - Rekap kehadiran siswa PKL per pembimbing
- **Arsip Jurnal PKL** -Arsip jurnal PKL yang sudah selesai

### 👨‍🏫 Absensi Guru (v3.0.0) [NEW]
- **Self Check-in/Check-out** - Guru absensi mandiri
- **Foto Dokumentasi** - Upload foto saat check-in/check-out
- **Riwayat Absensi** - History kehadiran guru
- **Monitoring Admin** - Admin & wakakur monitor absensi guru
- **Export Excel** - Export laporan absensi guru ke Excel
- **Keterangan** - Catatan keterangan untuk absensi guru

### 🔍 Verifikasi Jurnal PKL (v3.0.0) [NEW]
- **2-Tahap Verifikasi** - Pembimbing → Instruktur
- **Status Flow** - draft → submitted → revision → approved → verified
- **Batal Verifikasi** - Pembimbing & ketua jurusan bisa batal verifikasi
- **Catatan Instruktur** - Instruktur bisa berikan catatan ke progress siswa
- **Revision Loop** - Siswa bisa revisi dan submit ulang

### 👥 Multi-Role Users (v3.0.0) [NEW]
- **User Roles Table** - Satu user bisa punya beberapa role
- **Flexible Access** - Contoh: ketua_jurusan + guru_mapel
- **Role-Based Filtering** - Filter otomatis berdasarkan role

### 👨‍🏫 Instruktur Role (v3.0.0) [NEW]
- **Jurnal PKL Monitoring** - Monitor jurnal PKL semua siswa
- **Verifikasi Progress** - Verifikasi progress siswa setelah pembimbing
- **Task Template Management** - Kelola template tugas PKL
- **Pending Review** - Antrian review yang perlu diverifikasi
- **Catatan Instruktur** - Berikan catatan ke progress siswa
- **Batal Verifikasi** - Batalkan verifikasi jika perlu revisi

### 👔 Ketua Jurusan Role (v3.0.0) [NEW]
- **Read-Only Monitoring** - Monitor PKL tanpa bisa edit
- **Jurnal PKL Monitoring** - Lihat semua jurnal PKL jurusan
- **Siswa PKL Monitoring** - Lihat semua siswa PKL jurusan
- **Absensi PKL Monitoring** - Lihat rekap absensi PKL
- **Batal Verifikasi** - Batalkan verifikasi jurnal PKL
- **Tambah Catatan** - Berikan catatan ke jurnal PKL

### 📋 Izin Guru (v3.0.0) [NEW]
- **Pengajuan Izin** - Guru ajukan izin dengan berkas
- **Persetujuan Wakakur** - Wakakur approve/reject izin
- **Upload Berkas** - Upload bukti pendukung (JPG, PNG, PDF)

### 📊 Laporan Mingguan (v3.0.0) [NEW]
- **Weekly Report** - Cetak laporan mengajar per minggu
- **Print Layout** - Format cetak yang rapi

### 🔄 Rollover Tahun Ajaran (v3.0.0) [NEW]
- **Backup Data** - Backup data sebelum rollover
- **Restore Data** - Restore data dari backup
- **Academic Year Management** - Kelola tahun ajaran aktif
- **Pengaturan PKL** - Konfigurasi tanggal mulai/akhir jurnal PKL
- **Pengaturan Umum** - Pengaturan aplikasi lainnya

---

## 🛠️ Technology Stack

- **Backend:** PHP 8.1+ (CodeIgniter 4.6.4)
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** Tailwind CSS 3.x
- **JavaScript:** Vanilla JS (No frameworks)
- **Image Processing:** PHP GD Library + EXIF
- **Email:** SMTP (Gmail, Mailtrap, etc)
- **File Upload:** Secure file serving with validation

---

## 🔗 Quick Access URLs

Setelah server berjalan (`php spark serve`):

- **Login:** http://localhost:8080/login
- **Admin Dashboard:** http://localhost:8080/admin/dashboard
- **Guru Dashboard:** http://localhost:8080/guru/dashboard
- **Wakakur Dashboard:** http://localhost:8080/wakakur/dashboard
- **Wali Kelas Dashboard:** http://localhost:8080/walikelas/dashboard
- **Siswa Dashboard:** http://localhost:8080/siswa/dashboard
- **Instruktur Dashboard:** http://localhost:8080/instruktur/dashboard
- **Ketua Jurusan Dashboard:** http://localhost:8080/ketua-jurusan/dashboard

**Default Login:**
- Username: `admin`
- Password: `admin123`

**Note:** Untuk testing role lain, upgrade user existing via command atau database.

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
| `php spark email:diagnostics` | Diagnosa masalah email |
| `php spark token:cleanup` | Clean expired tokens |
| `php spark session:cleanup` | Clean expired sessions |
| `php spark mark-alpha-guru` | Tandai alpha untuk guru yang tidak check-in |
| `php spark set-profile-completion` | Set status profil completion user |
| `php spark check-wakakur-schedule` | Cek jadwal wakakur |
| `php spark check-wakakur-profile` | Cek profil wakakur |

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
| PKL Module | ✅ Complete | 100% | 2026-07-28 |
| Instruktur Module | ✅ Complete | 100% | 2026-07-28 |
| Ketua Jurusan Module | ✅ Complete | 100% | 2026-07-28 |
| Absensi Guru | ✅ Complete | 100% | 2026-02-12 |
| Izin Guru | ✅ Complete | 100% | 2026-02-12 |
| Weekly Report | ✅ Complete | 100% | 2026-07-28 |
| Rollover System | ✅ Complete | 100% | 2026-07-11 |
| Multi-Role Users | ✅ Complete | 100% | 2026-07-21 |
| Profile Completion | ✅ Complete | 100% | 2026-01-15 |
| Bulk Actions | ✅ Complete | 100% | 2026-07-28 |
| Jadwal Conflict Detection | ✅ Complete | 100% | 2026-07-28 |
| Component Library | ✅ Complete | 100% | 2026-07-28 |
| Soft Deletes | ✅ Complete | 100% | 2026-07-11 |
| Security Helpers | ✅ Complete | 100% | 2026-07-28 |

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
