# TODO - Sistem Monitoring Absensi dan Catatan Cara Ajar (SIMACCA)

## 📋 Daftar Isi
- [Fitur yang Sudah Selesai](#fitur-yang-sudah-selesai)
- [Tugas yang Belum Dikembangkan](#tugas-yang-belum-dikembangkan)
- [Bug dan Perbaikan](#bug-dan-perbaikan)
- [Fitur Enhancement](#fitur-enhancement)
- [Dokumentasi](#dokumentasi)

---

## ✅ Fitur yang Sudah Selesai

### 🔐 Authentication & Authorization
- [x] Login/Logout System
- [x] Multi-role support (Admin, Guru Mapel, Wali Kelas, Siswa)
- [x] Role-based access control (Filters)
- [x] Session management
- [x] Access denied page

### 👤 Admin Module
- [x] Dashboard dengan statistik
- [x] Manajemen Guru (CRUD, Import/Export Excel, Status Active/Inactive)
- [x] Manajemen Siswa (CRUD, Import/Export Excel, Bulk Actions)
- [x] Manajemen Kelas (CRUD, Assign Wali Kelas, Move Siswa)
- [x] Manajemen Mata Pelajaran (CRUD)
- [x] Manajemen Jadwal Mengajar (CRUD, Check Conflict)
- [x] Laporan Absensi
- [x] Laporan Statistik

### 👨‍🏫 Guru Mapel Module
- [x] Dashboard
- [x] Lihat Jadwal Mengajar
- [x] Input Absensi Siswa (CRUD)
- [x] Print Absensi
- [x] Input Jurnal KBM (CRUD)
- [x] Laporan

### 👨‍👩‍👧‍👦 Wali Kelas Module (Controllers Created)
- [x] DashboardController
- [x] SiswaController
- [x] AbsensiController
- [x] IzinController (Approve/Reject)
- [x] LaporanController

### 🎓 Siswa Module (Controllers Created)
- [x] DashboardController
- [x] JadwalController
- [x] AbsensiController
- [x] IzinController
- [x] ProfilController

### 🗄️ Database
- [x] Migrations untuk semua tabel
- [x] Models untuk semua entitas
- [x] Seeders (Admin & Dummy Data)

---

## 🚧 Tugas yang Belum Dikembangkan

### 🎯 PRIORITAS TINGGI

#### 1. Views yang Hilang - Wali Kelas
- [x] `app/Views/walikelas/dashboard.php` ✅ SELESAI
- [x] `app/Views/walikelas/siswa/index.php` ✅ SELESAI
- [x] `app/Views/walikelas/absensi/index.php` ✅ SELESAI
- [x] `app/Views/walikelas/izin/index.php` ✅ SELESAI
- [x] `app/Views/walikelas/laporan/index.php` ✅ SELESAI

#### 2. Views yang Hilang - Siswa
- [ ] `app/Views/siswa/dashboard.php`
- [ ] `app/Views/siswa/jadwal/index.php`
- [ ] `app/Views/siswa/absensi/index.php`
- [ ] `app/Views/siswa/izin/index.php`
- [ ] `app/Views/siswa/izin/create.php`
- [ ] `app/Views/siswa/profil/index.php`

#### 3. ProfileController Implementation
- [ ] Implement ProfileController methods (index, update)
- [ ] Create `app/Views/profile/index.php`
- [ ] Create `app/Views/profile/edit.php`
- [ ] Add profile photo upload feature
- [ ] Add change password in profile

#### 4. Password Reset System
- [ ] Implement email service configuration
- [ ] Complete `AuthController::processForgotPassword()` (currently has TODO)
- [ ] Complete `AuthController::processResetPassword()` (currently has TODO)
- [ ] Create password reset token table/migration
- [ ] Create email templates for password reset
- [ ] Add token expiration logic

### 🎯 PRIORITAS SEDANG

#### 5. Dashboard Implementations
- [ ] Complete Wali Kelas Dashboard dengan statistik kelas
- [ ] Complete Siswa Dashboard dengan informasi personal
- [ ] Add grafik/chart untuk statistik absensi
- [ ] Add quick actions untuk setiap role

#### 6. Laporan & Export Features
- [ ] Export laporan ke PDF (Admin)
- [ ] Export laporan ke Excel (Admin)
- [ ] Print laporan absensi per kelas
- [ ] Generate laporan bulanan otomatis
- [ ] Export jurnal KBM guru

#### 7. Izin Siswa Features
- [ ] Upload dokumen pendukung izin (surat sakit, dll)
- [ ] Notifikasi ke wali kelas saat ada izin baru
- [ ] History izin siswa
- [ ] Filter & search izin

#### 8. Notification System
- [ ] Real-time notification untuk izin siswa
- [ ] Email notification untuk laporan bulanan
- [ ] Alert untuk absensi yang belum diisi
- [ ] Reminder untuk guru mengisi jurnal

### 🎯 PRIORITAS RENDAH

#### 9. User Management Enhancement
- [ ] User profile photo upload
- [ ] User activity log
- [ ] Last login tracking (method exists but not used)
- [ ] Bulk user import dengan validation lebih baik

#### 10. Kelas Management Enhancement
- [ ] Riwayat perubahan wali kelas
- [ ] Riwayat perpindahan siswa antar kelas
- [ ] Kapasitas maksimal kelas
- [ ] Auto-assign siswa ke kelas

#### 11. Jadwal Management Enhancement
- [ ] Generate jadwal otomatis
- [ ] Check bentrok jadwal lebih detail
- [ ] Import jadwal dari Excel
- [ ] View jadwal dalam format kalender

#### 12. Absensi Enhancement
- [ ] QR Code untuk absensi siswa
- [ ] Geolocation untuk validasi absensi
- [ ] Rekap absensi per bulan/semester
- [ ] Alert untuk siswa yang sering tidak hadir

---

## 🐛 Bug dan Perbaikan

### Critical
- [ ] Review all form validations
- [ ] Add CSRF protection to all forms
- [ ] Check SQL injection vulnerabilities
- [ ] Add XSS protection for user inputs

### High Priority
- [ ] Handle error pages (404, 500, etc.) dengan template yang sesuai
- [ ] Add proper error logging
- [ ] Fix timezone settings
- [ ] Validate file uploads (size, type, etc.)

### Medium Priority
- [ ] Optimize database queries (add indexes if needed)
- [ ] Add pagination for large datasets
- [ ] Improve loading performance
- [ ] Add caching for frequently accessed data

### Low Priority
- [ ] Refactor duplicate code
- [ ] Add code comments untuk fungsi kompleks
- [ ] Standardize naming conventions
- [ ] Clean up unused imports

---

## 🎨 Fitur Enhancement

### UI/UX Improvements
- [ ] Add loading indicators untuk AJAX requests
- [ ] Improve responsive design untuk mobile
- [ ] Add dark mode option
- [ ] Improve form UX dengan better validation messages
- [ ] Add breadcrumb navigation
- [ ] Improve table sorting and filtering

### Performance
- [ ] Implement lazy loading untuk tabel besar
- [ ] Optimize image uploads (resize, compress)
- [ ] Add database query caching
- [ ] Minimize CSS/JS files

### Security
- [ ] Add two-factor authentication (2FA)
- [ ] Implement rate limiting untuk login
- [ ] Add password strength requirements
- [ ] Session timeout management
- [ ] Audit trail untuk aktivitas penting

### Integration
- [ ] API endpoints untuk mobile app
- [ ] WhatsApp notification integration
- [ ] Google Calendar sync untuk jadwal
- [ ] Excel/PDF template customization

---

## 📚 Dokumentasi

### Code Documentation
- [ ] Add PHPDoc comments untuk semua classes
- [ ] Document API endpoints (jika ada)
- [ ] Create database schema documentation
- [ ] Document deployment process

### User Documentation
- [ ] Create user manual untuk Admin
- [ ] Create user manual untuk Guru
- [ ] Create user manual untuk Wali Kelas
- [ ] Create user manual untuk Siswa
- [ ] Create video tutorials

### Developer Documentation
- [ ] Setup development environment guide
- [ ] Code contribution guidelines
- [ ] Testing guidelines
- [ ] Deployment checklist

---

## 📝 Notes

### Development Guidelines
- All controllers must extend BaseController
- Include proper authentication checks using session & filters
- Create corresponding view files for all controller actions
- Test all routes after creation
- Follow CodeIgniter 4 best practices
- Use models for database operations (no direct queries in controllers)

### Testing Checklist
- [ ] Test all CRUD operations
- [ ] Test authentication flows
- [ ] Test role-based access control
- [ ] Test file uploads
- [ ] Test data exports
- [ ] Test form validations
- [ ] Cross-browser testing
- [ ] Mobile responsiveness testing

### Deployment Checklist
- [ ] Update .env for production
- [ ] Set CI_ENVIRONMENT=production
- [ ] Disable debug mode
- [ ] Setup database backup schedule
- [ ] Configure email service
- [ ] Setup SSL certificate
- [ ] Configure file upload limits
- [ ] Test all features in production

---

## 👥 Tim Pengembang
- Mohd. Abdul Ghani
- Dirwan Jaya

---

**Last Updated:** 2026-01-11
