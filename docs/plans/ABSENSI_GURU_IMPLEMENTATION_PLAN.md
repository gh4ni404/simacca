# 📋 Rencana Implementasi: Absensi Guru Mandiri

## 🎯 Overview
Sistem absensi mandiri untuk guru dengan fitur check-in/check-out menggunakan tombol "Datang" dan "Pulang", dilengkapi dengan validasi foto selfie untuk mencegah fraud.

**Priority:** HIGH  
**Estimated Time:** 3-4 hari pengembangan  
**Date Created:** 2026-01-30

---

## 📊 Fase 1: Database & Backend Foundation (Day 1)

### 1.1 Database Schema Design

**Table: `absensi_guru`**
```sql
CREATE TABLE absensi_guru (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guru_id INT(11) UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    jam_datang TIME NULL,
    jam_pulang TIME NULL,
    foto_datang VARCHAR(255) NULL,
    foto_pulang VARCHAR(255) NULL,
    status ENUM('hadir', 'terlambat', 'izin', 'sakit', 'alpha') DEFAULT 'alpha',
    latitude_datang DECIMAL(10, 8) NULL,
    longitude_datang DECIMAL(11, 8) NULL,
    latitude_pulang DECIMAL(10, 8) NULL,
    longitude_pulang DECIMAL(11, 8) NULL,
    keterangan TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    
    FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    UNIQUE KEY unique_guru_tanggal (guru_id, tanggal),
    INDEX idx_tanggal (tanggal),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Business Rules:**
- Satu guru hanya bisa absen 1x per hari (UNIQUE constraint)
- Status otomatis:
  - `hadir`: Datang <= 07:30 WIB
  - `terlambat`: Datang > 07:30 WIB
  - `izin`: Manual set by admin
  - `sakit`: Manual set by admin
  - `alpha`: Default jika tidak check-in sampai jam 12:00

### 1.2 Migration File

**File:** `app/Database/Migrations/2026-01-30-XXXXXX_CreateAbsensiGuruTable.php`

**Tasks:**
- [ ] Create migration file dengan schema di atas
- [ ] Add rollback method (drop table)
- [ ] Test migration: `php spark migrate`
- [ ] Test rollback: `php spark migrate:rollback`

### 1.3 Model Creation

**File:** `app/Models/AbsensiGuruModel.php`

**Features:**
- Extends CodeIgniter Model
- Validation rules untuk setiap field
- Custom methods:
  - `checkIn($guruId, $fotoPath, $lat, $lng)` - Process check-in
  - `checkOut($guruId, $fotoPath, $lat, $lng)` - Process check-out
  - `getTodayAttendance($guruId)` - Get today's record
  - `getMonthlyAttendance($guruId, $month, $year)` - History per bulan
  - `getAllTodayAttendance($date)` - Admin monitoring
  - `getStatistics($startDate, $endDate)` - Stats untuk dashboard
  - `calculateStatus($jamDatang)` - Auto determine status

---

## 🎨 Fase 2: Guru Module - Self Check-in/out (Day 2)

### 2.1 Controller: Guru/AbsensiGuruController

**File:** `app/Controllers/Guru/AbsensiGuruController.php`

**Methods:**
1. `index()` - Display tombol datang/pulang + history
2. `checkIn()` - Process check-in dengan foto selfie (POST)
3. `checkOut()` - Process check-out dengan foto selfie (POST)
4. `history()` - Display history absensi per bulan (AJAX)
5. `uploadSelfie()` - Handle foto upload & validation

**Validation Rules:**
- Foto wajib (required)
- Format: JPG, JPEG, PNG only
- Max size: 5MB (akan di-compress ke 500KB)
- Jam check-in: 05:00 - 23:59
- Jam check-out: harus setelah check-in
- GPS: optional (jika aktif, radius max 500m dari sekolah)

### 2.2 View: guru/absensi_guru/index.php

**Layout Sections:**

**Section A: Status Card (Top)**
```
┌─────────────────────────────────────┐
│  📅 Absensi Hari Ini                │
│  Tanggal: Jumat, 30 Januari 2026   │
│                                     │
│  Status: [BELUM ABSEN]              │
│  Jam Datang: -                      │
│  Jam Pulang: -                      │
└─────────────────────────────────────┘
```

**Section B: Action Buttons (Middle)**
```
┌─────────────────────────────────────┐
│  [📸 DATANG] [📸 PULANG]            │
│  (Big buttons with camera icon)    │
│                                     │
│  [📷 Camera Preview Area]           │
│  (Hidden by default)                │
└─────────────────────────────────────┘
```

**Section C: History Table (Bottom)**
```
┌─────────────────────────────────────┐
│  Riwayat Absensi Bulan Ini          │
│  [Filter: Bulan ▼] [Tahun ▼]       │
│                                     │
│  | Tgl | Datang | Pulang | Status | │
│  |-----|--------|--------|---------|│
│  | ... | ...    | ...    | ...     |│
└─────────────────────────────────────┘
```

---

## 👨‍💼 Fase 3: Admin Module - Monitoring (Day 3)

### 3.1 Controller: Admin/AbsensiGuruController

**File:** `app/Controllers/Admin/AbsensiGuruController.php`

**Methods:**
1. `index()` - Dashboard monitoring real-time
2. `laporan()` - Halaman laporan dengan filter
3. `exportExcel()` - Export laporan ke Excel
4. `detail($guruId)` - Detail absensi per guru
5. `updateStatus()` - Manual update status (izin/sakit)

### 3.2 View: admin/absensi_guru/index.php

**Features:**
- Real-time monitoring kehadiran guru hari ini
- List semua guru dengan status (Hadir/Belum Absen/Terlambat)
- Filter by status, tanggal
- Quick stats: Total Hadir, Terlambat, Alpha
- Action: View detail, Update status manual

### 3.3 View: admin/absensi_guru/laporan.php

**Features:**
- Filter range tanggal (start - end)
- Filter by guru (dropdown)
- Filter by status
- Summary statistics (pie chart)
- Export to Excel button
- Printable view

---

## 📸 Fase 4: Camera Integration (Day 4)

### 4.1 JavaScript Camera Handler

**File:** `public/js/absensi-guru-camera.js`

**Features:**
- Access device camera dengan `navigator.mediaDevices.getUserMedia()`
- Live camera preview di canvas/video element
- Capture foto selfie
- Convert to base64 atau Blob
- Preview before submit
- Retake option
- Error handling (camera not found, permission denied)

**User Flow:**
1. Click tombol "Datang" atau "Pulang"
2. Modal muncul dengan camera preview
3. User klik "Ambil Foto"
4. Preview foto muncul
5. Options: "Ulangi" atau "Kirim"
6. Submit dengan AJAX ke backend

### 4.2 Image Optimization

**Use existing helper:** `app/Helpers/image_helper.php`

**Function:** `optimize_image($sourcePath, $destPath, $maxWidth = 800)`
- Compress to 500KB max
- Resize jika terlalu besar
- Maintain aspect ratio
- EXIF rotation handling

---

## 🗺️ Fase 5: GPS Validation (Optional)

### 5.1 JavaScript Geolocation

**Features:**
- Get current position dengan `navigator.geolocation.getCurrentPosition()`
- Calculate distance from school coordinates
- Validation: Max 500m radius
- Display warning jika di luar radius
- Allow override dengan keterangan

### 5.2 Configuration

**File:** `app/Config/App.php`

**Add:**
```php
public $schoolLatitude = -6.2088;  // Koordinat sekolah
public $schoolLongitude = 106.8456;
public $attendanceRadius = 500;    // Radius dalam meter
public $enableGPSValidation = true; // Toggle GPS
```

---

## 🔔 Fase 6: Notification System (Optional)

### 6.1 Real-time Notification

**Trigger Events:**
- Guru check-in (notify admin)
- Guru check-out (notify admin)
- Guru terlambat (notify admin)
- Guru belum check-in jam 12:00 (notify admin & guru)

**Implementation:**
- Use existing email service
- Add database notification table (future)
- Browser push notification (future)

---

## 📝 Fase 7: Routes Configuration

### 7.1 Add Routes

**File:** `app/Config/Routes.php`

```php
// Guru - Absensi Guru Mandiri
$routes->group('guru', ['filter' => 'auth,role:guru_mapel,wali_kelas'], function($routes) {
    $routes->get('absensi-guru', 'Guru\AbsensiGuruController::index');
    $routes->post('absensi-guru/check-in', 'Guru\AbsensiGuruController::checkIn');
    $routes->post('absensi-guru/check-out', 'Guru\AbsensiGuruController::checkOut');
    $routes->get('absensi-guru/history', 'Guru\AbsensiGuruController::history');
});

// Admin - Monitoring Absensi Guru
$routes->group('admin', ['filter' => 'auth,role:admin'], function($routes) {
    $routes->get('absensi-guru', 'Admin\AbsensiGuruController::index');
    $routes->get('absensi-guru/laporan', 'Admin\AbsensiGuruController::laporan');
    $routes->get('absensi-guru/export-excel', 'Admin\AbsensiGuruController::exportExcel');
    $routes->get('absensi-guru/detail/(:num)', 'Admin\AbsensiGuruController::detail/$1');
    $routes->post('absensi-guru/update-status', 'Admin\AbsensiGuruController::updateStatus');
});
```

---

## ✅ Testing Checklist

### Unit Testing
- [ ] Model validation rules
- [ ] checkIn() method logic
- [ ] checkOut() method logic
- [ ] Status calculation (hadir vs terlambat)
- [ ] Duplicate check-in prevention

### Integration Testing
- [ ] Full check-in flow (upload foto + save)
- [ ] Full check-out flow
- [ ] History display dengan pagination
- [ ] Admin monitoring real-time
- [ ] Excel export functionality

### UI/UX Testing
- [ ] Camera access di berbagai browser (Chrome, Firefox, Safari)
- [ ] Camera access di mobile (Android, iOS)
- [ ] Responsive design (desktop, tablet, mobile)
- [ ] Error handling UI (camera denied, upload failed)
- [ ] Loading states & feedback messages

### Security Testing
- [ ] File upload validation (type, size)
- [ ] SQL injection prevention
- [ ] XSS protection (esc() function)
- [ ] CSRF protection
- [ ] Authorization check (guru hanya bisa absen untuk dirinya sendiri)

---

## 📚 Documentation

### 7.1 User Guide

**Create:** `docs/guides/ABSENSI_GURU_USER_GUIDE.md`

**Content:**
- Cara check-in (dengan screenshot)
- Cara check-out
- Troubleshooting camera issues
- FAQ

### 7.2 Admin Guide

**Create:** `docs/guides/ABSENSI_GURU_ADMIN_GUIDE.md`

**Content:**
- Cara monitoring kehadiran guru
- Cara export laporan
- Cara update status manual (izin/sakit)
- Interpretasi statistik

---

## 🚀 Deployment Checklist

- [ ] Run migration: `php spark migrate`
- [ ] Create uploads folder: `writable/uploads/absensi_guru/`
- [ ] Set folder permissions: `chmod 755`
- [ ] Test camera access di production (HTTPS required)
- [ ] Update .env dengan koordinat sekolah
- [ ] Test GPS validation
- [ ] Verify email notification (jika aktif)
- [ ] Backup database sebelum deploy
- [ ] Monitor logs untuk errors

---

## 📊 Success Metrics

**KPI (Key Performance Indicators):**
1. **Adoption Rate:** > 90% guru menggunakan sistem dalam 1 bulan
2. **Compliance Rate:** > 95% guru check-in tepat waktu
3. **Fraud Prevention:** 0 kasus manipulasi absensi
4. **System Uptime:** > 99.5%
5. **User Satisfaction:** > 4.5/5 stars

---

## 🔮 Future Enhancements

### Phase 2 Features:
- [ ] Face recognition untuk validasi selfie
- [ ] Integrasi dengan fingerprint reader
- [ ] Mobile app (Android/iOS)
- [ ] Overtime tracking (lembur)
- [ ] Leave request system terintegrasi
- [ ] Payroll integration (hitung jam kerja)
- [ ] Analytics dashboard dengan AI prediction

---

## 📞 Support & Maintenance

**Contact:**
- Developer: Mohd. Abdul Ghani, Dirwan Jaya
- Email: support@simacca.sch.id
- Documentation: `docs/guides/`

**Maintenance Schedule:**
- Daily: Monitor system logs
- Weekly: Database backup
- Monthly: Performance optimization review
- Quarterly: Security audit

---

**Last Updated:** 2026-01-30  
**Version:** 1.0 (Planning Phase)
