# Sistem Monitoring Absensi dan Catatan Cara Ajar

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

📝 **Lihat detail lengkap di:** [FEATURES.md](FEATURES.md)

## Instalasi
1. Clone Repository:
```bash
git clone https://github.com/gh4ni404/simacca.git
```
2. Install Dependencies:
```bash
composer install
```
3. Copy .env file
```bash
cp env .env
```
4. Konfiguraswi database di .env
5. jalankan migrations:
```bash
php spark migrate
```
6. Jalankan seeder:
```bash
php spark db:seed DummyDataSeeder
php spark db:seed AdminSeeder
```

## Development Server
```bash
php spark serve
```

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

Untuk informasi lebih detail tentang fitur dan development:

### General Documentation
- **[FEATURES.md](FEATURES.md)** - Daftar lengkap fitur sistem dengan detail setiap modul
- **[TODO.md](TODO.md)** - Task list pengembangan dan bug tracking
- **[CodeIgniter 4 User Guide](https://codeigniter.com/user_guide/)** - Dokumentasi framework

### Technical Notes
All detailed technical documentation has been archived. Key information about recent updates and fixes can be found in the **Recent Updates & Fixes** section of FEATURES.md.

## 🚀 Quick Start

### Default Login Credentials

Setelah menjalankan seeder, gunakan kredensial berikut:

**Admin:**
- Username: `admin`
- Password: `password` (atau cek di AdminSeeder)

**Guru/Siswa:**
- Lihat data dummy di DummyDataSeeder

### Access URLs

Setelah server berjalan (`php spark serve`), akses:

- **Login Page:** `http://localhost:8080/login`
- **Admin Dashboard:** `http://localhost:8080/admin/dashboard`
- **Guru Dashboard:** `http://localhost:8080/guru/dashboard`
- **Wali Kelas Dashboard:** `http://localhost:8080/walikelas/dashboard`
- **Siswa Dashboard:** `http://localhost:8080/siswa/dashboard`

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

### Common Issues

**Database Connection Error:**
```bash
# Check .env configuration
database.default.hostname = localhost
database.default.database = simacca_db
database.default.username = your_username
database.default.password = your_password
```

**Migration Error:**
```bash
# Reset migrations
php spark migrate:rollback
php spark migrate
```

**Permission Error (writable folder):**
```bash
# Linux/Mac
chmod -R 777 writable/
# Windows - Check folder permissions manually
```

**Composer Dependencies:**
```bash
composer update
composer dump-autoload
```

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
 
