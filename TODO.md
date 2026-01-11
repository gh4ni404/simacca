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
- [x] **Guru Pengganti/Piket Feature** ✅ BARU (2026-01-12)
  - [x] Mode Selection UI (Normal vs Pengganti)
  - [x] Lihat semua jadwal untuk mode pengganti
  - [x] Input absensi sebagai guru pengganti
  - [x] Auto-detect dan record guru pengganti
  - [x] Dual ownership access control
  - [x] Integrated dengan Jurnal KBM

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
- [x] Migration untuk field `guru_pengganti_id` ✅ (2026-01-12)
- [x] Enhanced queries dengan dual ownership logic ✅ (2026-01-12)

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
- [x] `app/Views/siswa/dashboard.php` ✅ SELESAI
- [x] `app/Views/siswa/jadwal/index.php` ✅ SELESAI
- [x] `app/Views/siswa/absensi/index.php` ✅ SELESAI
- [x] `app/Views/siswa/izin/index.php` ✅ SELESAI
- [x] `app/Views/siswa/izin/create.php` ✅ SELESAI
- [x] `app/Views/siswa/profil/index.php` ✅ SELESAI

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
- [x] **Guru Pengganti/Piket System** ✅ SELESAI (2026-01-12)
  - Mode selection untuk input absensi normal vs pengganti
  - Lihat semua jadwal di mode pengganti
  - Auto-detect dan record guru pengganti
  - Dual ownership access control (creator & schedule owner)
  - Integrated dengan jurnal KBM dan laporan
- [ ] QR Code untuk absensi siswa
- [ ] Geolocation untuk validasi absensi
- [ ] Rekap absensi per bulan/semester
- [ ] Alert untuk siswa yang sering tidak hadir

---

## 🐛 Bug dan Perbaikan

### Recently Fixed ✅ (2026-01-12)
- [x] **Guru Pengganti Access Issues** - Fixed mode selection, access control, and list display
- [x] **Jurnal KBM Access for Substitute Teachers** - Updated validation logic
- [x] **Absensi List Display** - Added dual ownership query logic
- [x] **Edit/Delete Access for Original Teachers** - Allow schedule owner to manage substitute's records
- [x] **CSRF Protection** - Implemented across all forms
- [x] **Session Security** - Fixed session key handling and logout mechanism
- [x] **Redirect Loop Issues** - Fixed authentication and role-based redirects

### Critical
- [ ] Check SQL injection vulnerabilities (ongoing review)
- [ ] Add XSS protection for user inputs (ongoing implementation)

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

### Recently Created ✅ (2026-01-12)
- [x] **GURU_PENGGANTI_FEATURE.md** - Feature overview dan usage guide
- [x] **SUBSTITUTE_TEACHER_MODE_FIX.md** - Technical implementation details
- [x] **SUBSTITUTE_MODE_ACCESS_FIX.md** - Access validation fix documentation
- [x] **JURNAL_SUBSTITUTE_ACCESS_FIX.md** - Jurnal KBM access fix
- [x] **ABSENSI_LIST_AND_ACCESS_FIX.md** - List display and access control fix
- [x] **DATABASE_MIGRATION_GURU_PENGGANTI.md** - Migration guide with SQL examples
- [x] **QUICK_DEPLOYMENT_GUIDE.md** - 5-minute deployment checklist
- [x] **CSRF_FIX.md** - CSRF protection implementation
- [x] **SESSION_KEY_FIXES.md** - Session security fixes
- [x] **SESSION_LOGOUT_FIX.md** - Logout mechanism fixes
- [x] **REDIRECT_LOOP_FIX.md** - Authentication redirect fixes
- [x] **SECURITY_FIXES_REPORT.md** - Comprehensive security improvements
- [x] **ERROR_MESSAGES_IMPROVEMENT_REPORT.md** - Error handling enhancements

### Code Documentation
- [ ] Add PHPDoc comments untuk semua classes
- [ ] Document API endpoints (jika ada)
- [x] Database schema documentation (via migration docs) ✅
- [x] Document deployment process (QUICK_DEPLOYMENT_GUIDE.md) ✅

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

**Last Updated:** 2026-01-12

---

## 🎉 Recent Achievements (January 2026)

### Major Feature: Guru Pengganti/Piket System (2026-01-12)
Implementasi lengkap sistem guru pengganti untuk menangani situasi ketika guru berhalangan hadir:

#### What's New:
1. **Mode Selection Interface**
   - Toggle UI untuk memilih "Jadwal Saya Sendiri" atau "Guru Pengganti"
   - Visual feedback yang jelas dengan icon dan warna berbeda
   - Dynamic label berdasarkan mode yang dipilih

2. **Smart Backend Logic**
   - Auto-detect substitute mode berdasarkan guru_id jadwal
   - Auto-set guru_pengganti_id untuk mode pengganti
   - Dual ownership access control (creator OR schedule owner)
   - Enhanced queries dengan groupStart/groupEnd untuk OR conditions

3. **Complete Access Control**
   - Guru pengganti bisa lihat daftar absensi yang diinput
   - Guru asli bisa edit/delete absensi dari guru pengganti
   - Both can create jurnal KBM
   - Proper validation across all CRUD operations

4. **Integration Points**
   - Absensi module: show, edit, update, delete, print
   - Jurnal KBM module: create, edit, show, print
   - Laporan admin: menampilkan info guru pengganti
   - Database: field guru_pengganti_id dengan foreign key

#### Files Modified:
- Controllers: `AbsensiController.php`, `JurnalController.php`
- Models: `AbsensiModel.php` (enhanced getByGuru method)
- Views: `create.php`, `edit.php`, `show.php` (absensi & jurnal)
- Database: Migration file untuk guru_pengganti_id

#### Documentation:
- 7 comprehensive markdown files created
- Flow diagrams and test scenarios included
- Deployment guide with checklist
- Security considerations documented

### Security Enhancements (Previous Updates)
- CSRF protection across all forms
- Session key handling fixes
- Proper logout mechanism
- Redirect loop fixes
- XSS protection improvements
- Error message sanitization
