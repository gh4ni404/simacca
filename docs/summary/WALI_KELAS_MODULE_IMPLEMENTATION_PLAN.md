# Wali Kelas Module Implementation Plan

## Executive Summary

Dokumen ini berisi analisis dan rencana implementasi untuk memperbarui modul Wali Kelas agar memiliki fungsi dan tampilan yang berbeda dari Guru Mata Pelajaran. Saat ini, modul Wali Kelas masih terbatas dan belum memanfaatkan sepenuhnya peran unik sebagai wali kelas.

**Tanggal Dibuat**: 2026-01-19  
**Status**: Planning Phase  
**Priority**: High

---

## 1. Analisis Kondisi Saat Ini

### 1.1 Database Structure

**Tabel Guru:**
- `is_wali_kelas` (BOOLEAN) - flag apakah guru adalah wali kelas
- `kelas_id` (INT) - ID kelas yang diampu (untuk wali kelas)
- `mata_pelajaran_id` (INT) - Mata pelajaran yang diajar

**Tabel Kelas:**
- `wali_kelas_id` (INT) - foreign key ke guru.id
- Relasi: Satu kelas hanya punya satu wali kelas

**Kesimpulan Struktur:**
- ✅ Database sudah mendukung konsep wali kelas
- ✅ Relasi one-to-one antara guru dan kelas untuk wali kelas
- ✅ Foreign key constraints sudah benar
- ✅ Wali kelas bisa mengajar mata pelajaran (punya mata_pelajaran_id)

### 1.2 Controller yang Ada

**Wali Kelas Controllers:**
1. `DashboardController` - Dashboard dengan statistik kelas
2. `AbsensiController` - Monitoring absensi kelas
3. `IzinController` - Approval izin siswa
4. `LaporanController` - Laporan kehadiran kelas
5. `SiswaController` - Data siswa di kelas

**Guru Controllers:**
1. `DashboardController` - Dashboard dengan jadwal mengajar
2. `AbsensiController` - Input/edit absensi per jadwal mengajar
3. `JadwalController` - Lihat jadwal mengajar
4. `JurnalController` - Jurnal KBM per pertemuan
5. `LaporanController` - Laporan absensi per mata pelajaran

### 1.3 Perbedaan Konsep

**PENTING: Wali Kelas = Guru Mata Pelajaran + Fitur Tambahan Homeroom**

**Guru Mata Pelajaran:**
- Fokus: Per jadwal mengajar dan mata pelajaran
- Akses: Multiple kelas yang diajar
- Fungsi: Input absensi, jurnal KBM, laporan per mapel
- View: Device-specific (mobile/desktop)

**Wali Kelas:**
- Fokus: **DUAL ROLE** - Mengajar mapel + Monitoring kelas yang diampu
- Akses: 
  - Sebagai Guru: Multiple kelas yang diajar
  - Sebagai Wali: Kelas yang diampu (homeroom)
- Fungsi: 
  - **Semua fungsi Guru** (Input absensi, jurnal KBM, laporan per mapel)
  - **PLUS menu khusus**: Monitoring kehadiran kelas, approval izin, student management
- View: Masih menggunakan main_layout (perlu upgrade ke device-specific)

---

## 2. Gap Analysis - Apa yang Kurang?

### 2.1 Fungsionalitas yang Kurang

#### A. Dashboard Wali Kelas
**Status Saat Ini:** ✅ Ada, tapi bisa ditingkatkan
**Yang Kurang:**
1. ❌ Tidak ada device-specific layout (mobile/desktop)
2. ❌ Grafik/chart untuk visualisasi kehadiran
3. ❌ Notifikasi real-time untuk izin pending
4. ❌ Quick actions untuk tugas sehari-hari
5. ❌ Ranking kehadiran siswa
6. ❌ Trend kehadiran bulanan

#### B. Monitoring Absensi Kelas (HOMEROOM SPECIFIC)
**Status Saat Ini:** ⚠️ Basic monitoring, perlu enhancement major
**Yang Kurang:**
1. ❌ **Filter by mata pelajaran** - Lihat absensi kelas per mapel
2. ❌ **Detail kehadiran per mapel** - Klik mapel → lihat detail siswa
3. ❌ **Rekapan bulanan per siswa** - Tabel dengan kolom: Nama, Hadir, Sakit, Izin, Alpa
4. ❌ **Statistik rata-rata** - Rata-rata kehadiran, persentase hadir/sakit/izin/alpa
5. ❌ Export ke Excel/PDF
6. ❌ Comparison antar periode
7. ❌ Alert untuk siswa dengan masalah kehadiran
8. ❌ View per hari dengan semua mapel

#### C. Manajemen Siswa
**Status Saat Ini:** ⚠️ Basic list dengan statistik
**Yang Kurang:**
1. ❌ Profile lengkap siswa
2. ❌ History kehadiran per siswa
3. ❌ Catatan/notes per siswa
4. ❌ Contact orang tua/wali
5. ❌ Achievement/prestasi siswa
6. ❌ Export daftar siswa

#### D. Approval Izin
**Status Saat Ini:** ✅ Ada dan berfungsi
**Yang Kurang:**
1. ❌ Upload bukti surat izin
2. ❌ History approval
3. ❌ Bulk approval
4. ❌ Notification ke siswa setelah approval
5. ❌ Filter advanced (by tipe, tanggal range)

#### E. Laporan
**Status Saat Ini:** ✅ Ada, cukup lengkap
**Yang Kurang:**
1. ❌ Export multiple format (Excel, PDF)
2. ❌ Template laporan yang bisa dikustomisasi
3. ❌ Laporan per mata pelajaran di kelas
4. ❌ Laporan per minggu/bulan
5. ❌ Grafik trend kehadiran
6. ❌ Perbandingan dengan kelas lain

### 2.2 Fitur Unik yang Seharusnya Ada

**Fitur yang HANYA untuk Wali Kelas:**

1. **Homeroom Management**
   - Catatan kelas (class notes)
   - Pengumuman khusus kelas
   - Schedule kelas (bukan jadwal mengajar)

2. **Student Profiling**
   - Detailed profile per siswa
   - Behavioral notes
   - Academic progress tracking
   - Parent/guardian contact

3. **Attendance Oversight**
   - View ALL absensi (semua mapel) untuk kelasnya
   - Cross-reference izin dengan absensi
   - Identify patterns (siswa sering alpa hari tertentu)

4. **Communication Hub**
   - Broadcast message ke siswa di kelas
   - Parent communication log
   - Meeting notes

5. **Class Statistics & Analytics**
   - Overall class performance
   - Attendance trends
   - Problem identification
   - Comparison with school average

6. **Administrative Tasks**
   - Class schedule management
   - Student roster management
   - Approval workflow (izin, cuti, dll)

---

## 3. Rekomendasi Implementasi

### Phase 1: Core Improvements (Priority: HIGH)

#### 1.1 Dashboard Enhancement
**Tasks:**
- [ ] Create device-specific views (mobile/desktop) seperti guru
- [ ] Add interactive charts (Chart.js/ApexCharts)
- [ ] Implement real-time notification counter
- [ ] Add quick action cards
- [ ] Show top 5 siswa with attendance issues
- [ ] Add monthly trend comparison

**Files to Create/Modify:**
- `app/Views/walikelas/dashboard_mobile.php` (NEW)
- `app/Views/walikelas/dashboard_desktop.php` (NEW)
- `app/Controllers/WaliKelas/DashboardController.php` (MODIFY)

#### 1.2 Enhanced Siswa Management
**Tasks:**
- [ ] Add detailed siswa profile view
- [ ] Implement notes/catatan per siswa
- [ ] Add parent/wali contact information
- [ ] Create attendance history view per siswa
- [ ] Add export functionality

**Files to Create/Modify:**
- `app/Views/walikelas/siswa/detail.php` (NEW)
- `app/Views/walikelas/siswa/catatan.php` (NEW)
- `app/Controllers/WaliKelas/SiswaController.php` (MODIFY)
- `app/Models/SiswaModel.php` (ADD methods)

#### 1.3 Advanced Absensi Monitoring
**Tasks:**
- [ ] Add filter by mata pelajaran
- [ ] Implement export to Excel/PDF
- [ ] Create detail view per hari
- [ ] Add comparison tools
- [ ] Alert system for problematic patterns

**Files to Create/Modify:**
- `app/Views/walikelas/absensi/detail.php` (NEW)
- `app/Controllers/WaliKelas/AbsensiController.php` (MODIFY)
- Add export library/helper

### Phase 2: Unique Features (Priority: MEDIUM)

#### 2.1 Homeroom Management
**Tasks:**
- [ ] Class notes/catatan kelas
- [ ] Pengumuman khusus kelas
- [ ] Class calendar/schedule

**Files to Create:**
- `app/Controllers/WaliKelas/HomeroomController.php` (NEW)
- `app/Models/ClassNotesModel.php` (NEW)
- `app/Views/walikelas/homeroom/` (NEW folder)
- Migration for `class_notes` table

#### 2.2 Communication Hub
**Tasks:**
- [ ] Broadcast message system
- [ ] Parent communication log
- [ ] Meeting notes

**Files to Create:**
- `app/Controllers/WaliKelas/CommunicationController.php` (NEW)
- `app/Models/ClassCommunicationModel.php` (NEW)
- `app/Views/walikelas/communication/` (NEW folder)

#### 2.3 Analytics Dashboard
**Tasks:**
- [ ] Advanced statistics
- [ ] Trend analysis
- [ ] Comparison with other classes
- [ ] Predictive insights

**Files to Create:**
- `app/Controllers/WaliKelas/AnalyticsController.php` (NEW)
- `app/Views/walikelas/analytics/` (NEW folder)

### Phase 3: Polish & Optimization (Priority: LOW)

#### 3.1 UI/UX Improvements
**Tasks:**
- [ ] Consistent design language
- [ ] Better mobile experience
- [ ] Loading states and animations
- [ ] Toast notifications

#### 3.2 Performance Optimization
**Tasks:**
- [ ] Query optimization
- [ ] Caching strategies
- [ ] Lazy loading for large datasets

#### 3.3 Documentation
**Tasks:**
- [ ] User guide for wali kelas
- [ ] API documentation
- [ ] Code documentation

---

## 4. Menu Structure Comparison

### Current Menu - Guru Mata Pelajaran
```
📊 Dashboard
   - Jadwal hari ini
   - Statistik absensi per mapel
   - Jurnal KBM terbaru

📅 Jadwal Mengajar
   - List jadwal per hari
   - Filter by kelas/mapel

✅ Absensi
   - Input absensi per jadwal
   - Edit absensi (jika unlocked)
   - History absensi per mapel
   - Print absensi

📝 Jurnal KBM
   - Create jurnal per pertemuan
   - Edit jurnal
   - Upload foto kegiatan
   - Print jurnal

📈 Laporan
   - Laporan per mata pelajaran
   - Filter by kelas dan periode
   - Export/Print
```

### Proposed Menu - Wali Kelas (DUAL ROLE!)
```
═══════════════════════════════════════════════
SECTION 1: FITUR GURU MATA PELAJARAN (EXISTING)
═══════════════════════════════════════════════
📊 Dashboard Guru
   - Jadwal mengajar hari ini
   - Statistik absensi per mapel yang diajar
   - Jurnal KBM terbaru

📅 Jadwal Mengajar
   - List jadwal per hari
   - Filter by kelas/mapel

✅ Absensi (Input/Edit)
   - Input absensi per jadwal mengajar
   - Edit absensi (jika unlocked)
   - History absensi per mapel
   - Print absensi

📝 Jurnal KBM
   - Create jurnal per pertemuan
   - Edit jurnal
   - Upload foto kegiatan
   - Print jurnal

📈 Laporan Mengajar
   - Laporan per mata pelajaran yang diajar
   - Filter by kelas dan periode

═══════════════════════════════════════════════
SECTION 2: FITUR WALI KELAS (HOMEROOM - NEW!)
═══════════════════════════════════════════════
🏠 Dashboard Wali Kelas
   - Overview kelas yang diampu
   - Statistik kehadiran kelas (ALL mapel)
   - Siswa bermasalah
   - Izin pending
   - Quick actions

👥 Data Siswa Kelas
   - List siswa kelas yang diampu
   - Detail profile siswa
   - Catatan per siswa
   - Contact orang tua
   - History kehadiran per siswa
   - Export daftar siswa

📊 Monitoring Kehadiran Kelas ⭐ PRIORITAS TINGGI
   ┌─────────────────────────────────────────┐
   │ VIEW 1: Per Mata Pelajaran              │
   │  - List semua mapel di kelas            │
   │  - Statistik per mapel (H/S/I/A)        │
   │  - Klik mapel → Detail kehadiran        │
   └─────────────────────────────────────────┘
   ┌─────────────────────────────────────────┐
   │ VIEW 2: Detail per Mapel                │
   │  - Tabel siswa dengan status kehadiran  │
   │  - Per pertemuan/tanggal                │
   │  - Filter by periode                    │
   └─────────────────────────────────────────┘
   ┌─────────────────────────────────────────┐
   │ VIEW 3: Rekapan Bulanan ⭐              │
   │  Tabel: Nama | Hadir | Sakit | Izin |  │
   │         Alpa | Total | % Hadir          │
   │  - Filter bulan                         │
   │  - Rata-rata kelas                      │
   │  - Export Excel/PDF                     │
   └─────────────────────────────────────────┘

✋ Persetujuan Izin
   - List izin masuk dari siswa kelas
   - Approve/Reject izin
   - History approval
   - Upload bukti surat
   - Notification ke siswa

📈 Laporan Kelas
   - Laporan kehadiran kelas (semua mapel)
   - Per siswa / Per mapel / Per periode
   - Grafik & chart
   - Comparison dengan kelas lain
   - Export multiple format

📝 Homeroom Management
   - Catatan kelas
   - Pengumuman kelas
   - Schedule & calendar
   - Meeting notes

💬 Komunikasi
   - Broadcast message ke siswa kelas
   - Parent communication log
   - Announcement history
```

### Key Differences Summary

| Aspect | Guru Mata Pelajaran | Wali Kelas |
|--------|-------------------|------------|
| **Dual Role** | ❌ Hanya mengajar | ✅ Mengajar + Homeroom teacher |
| **Menu Structure** | Single section | **Two sections**: Guru + Homeroom |
| **Scope Mengajar** | Per mata pelajaran yang diajar | Same - per mata pelajaran |
| **Scope Homeroom** | ❌ Tidak ada | ✅ Satu kelas yang diampu (ALL mapel) |
| **Classes Access** | Multiple kelas yang diajar | Multiple (mengajar) + 1 homeroom |
| **Absensi Input** | ✅ Input & edit (per jadwal) | ✅ Same - Input & edit |
| **Absensi Monitoring** | ❌ Tidak ada | ✅ **Monitor ALL mapel di kelas homeroom** |
| **Jurnal KBM** | ✅ Ya (per pertemuan mengajar) | ✅ Same - Ya (per pertemuan) |
| **Jadwal** | Jadwal mengajar pribadi | Same + Schedule kelas homeroom |
| **Student Data** | Basic view per kelas yang diajar | **Deep profile untuk kelas homeroom** |
| **Izin** | ❌ Tidak bisa approve | ✅ Approve/Reject (kelas homeroom) |
| **Laporan Mengajar** | Per mata pelajaran yang diajar | Same - per mata pelajaran |
| **Laporan Homeroom** | ❌ Tidak ada | ✅ **Semua mapel di kelas homeroom** |
| **Communication** | ❌ Tidak ada | ✅ Broadcast & parent contact |
| **Homeroom Mgmt** | ❌ Tidak ada | ✅ Class notes, announcements |
| **Analytics** | Basic stats mengajar | Same + Advanced analytics homeroom |

---

## 5. Database Schema Changes Needed

### 5.1 New Tables Required

#### Table: `class_notes`
```sql
CREATE TABLE class_notes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kelas_id INT UNSIGNED NOT NULL,
    wali_kelas_id INT UNSIGNED NOT NULL,
    judul VARCHAR(200) NOT NULL,
    isi TEXT NOT NULL,
    tipe ENUM('catatan', 'pengumuman', 'meeting') DEFAULT 'catatan',
    tanggal DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (wali_kelas_id) REFERENCES guru(id) ON DELETE CASCADE
);
```

#### Table: `siswa_notes`
```sql
CREATE TABLE siswa_notes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT UNSIGNED NOT NULL,
    wali_kelas_id INT UNSIGNED NOT NULL,
    catatan TEXT NOT NULL,
    tipe ENUM('behavior', 'academic', 'health', 'other') DEFAULT 'other',
    is_private BOOLEAN DEFAULT FALSE,
    tanggal DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (wali_kelas_id) REFERENCES guru(id) ON DELETE CASCADE
);
```

#### Table: `parent_contacts`
```sql
CREATE TABLE parent_contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT UNSIGNED NOT NULL,
    nama_wali VARCHAR(100) NOT NULL,
    hubungan ENUM('ayah', 'ibu', 'wali') NOT NULL,
    telepon VARCHAR(20),
    email VARCHAR(100),
    alamat TEXT,
    pekerjaan VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
);
```

#### Table: `class_communications`
```sql
CREATE TABLE class_communications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kelas_id INT UNSIGNED NOT NULL,
    wali_kelas_id INT UNSIGNED NOT NULL,
    tipe ENUM('broadcast', 'parent_meeting', 'announcement') NOT NULL,
    judul VARCHAR(200) NOT NULL,
    pesan TEXT NOT NULL,
    tanggal DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (wali_kelas_id) REFERENCES guru(id) ON DELETE CASCADE
);
```

### 5.2 Existing Table Modifications

#### Add to `izin_siswa` table:
```sql
ALTER TABLE izin_siswa 
ADD COLUMN bukti_file VARCHAR(255) AFTER keterangan,
ADD COLUMN notified_at DATETIME AFTER approved_at;
```

#### Add to `siswa` table:
```sql
ALTER TABLE siswa
ADD COLUMN catatan_wali TEXT AFTER tahun_ajaran,
ADD COLUMN status_khusus ENUM('normal', 'perhatian_khusus', 'bermasalah') DEFAULT 'normal';
```

---

## 6. Routing Changes

### Current Routes (Minimal)
```php
$routes->group('walikelas', ['filter' => 'role:wali_kelas'], function ($routes) {
    $routes->get('dashboard', 'WaliKelas\DashboardController::index');
    $routes->get('siswa', 'WaliKelas\SiswaController::index');
    $routes->get('absensi', 'WaliKelas\AbsensiController::index');
    $routes->get('izin', 'WaliKelas\IzinController::index');
    $routes->post('izin/approve/(:num)', 'WaliKelas\IzinController::approve/$1');
    $routes->post('izin/reject/(:num)', 'WaliKelas\IzinController::reject/$1');
    $routes->get('laporan', 'WaliKelas\LaporanController::index');
});
```

### Proposed Routes (Comprehensive)
```php
$routes->group('walikelas', ['filter' => 'role:wali_kelas'], function ($routes) {
    
    // Dashboard
    $routes->get('dashboard', 'WaliKelas\DashboardController::index');
    
    // Siswa Management (Enhanced)
    $routes->get('siswa', 'WaliKelas\SiswaController::index');
    $routes->get('siswa/detail/(:num)', 'WaliKelas\SiswaController::detail/$1');
    $routes->get('siswa/history/(:num)', 'WaliKelas\SiswaController::history/$1');
    $routes->post('siswa/note/(:num)', 'WaliKelas\SiswaController::addNote/$1');
    $routes->delete('siswa/note/(:num)', 'WaliKelas\SiswaController::deleteNote/$1');
    $routes->get('siswa/export', 'WaliKelas\SiswaController::export');
    
    // Parent Contacts
    $routes->get('siswa/parent/(:num)', 'WaliKelas\SiswaController::parentContact/$1');
    $routes->post('siswa/parent/(:num)', 'WaliKelas\SiswaController::saveParentContact/$1');
    
    // Absensi Monitoring (Enhanced)
    $routes->get('absensi', 'WaliKelas\AbsensiController::index');
    $routes->get('absensi/detail/(:segment)', 'WaliKelas\AbsensiController::detail/$1');
    $routes->get('absensi/export', 'WaliKelas\AbsensiController::export');
    
    // Izin Management (Enhanced)
    $routes->get('izin', 'WaliKelas\IzinController::index');
    $routes->get('izin/detail/(:num)', 'WaliKelas\IzinController::detail/$1');
    $routes->post('izin/approve/(:num)', 'WaliKelas\IzinController::approve/$1');
    $routes->post('izin/reject/(:num)', 'WaliKelas\IzinController::reject/$1');
    $routes->post('izin/upload-bukti/(:num)', 'WaliKelas\IzinController::uploadBukti/$1');
    
    // Laporan (Enhanced)
    $routes->get('laporan', 'WaliKelas\LaporanController::index');
    $routes->get('laporan/export/(:segment)', 'WaliKelas\LaporanController::export/$1');
    $routes->get('laporan/print', 'WaliKelas\LaporanController::print');
    
    // Homeroom Management (NEW)
    $routes->get('homeroom', 'WaliKelas\HomeroomController::index');
    $routes->get('homeroom/create', 'WaliKelas\HomeroomController::create');
    $routes->post('homeroom/store', 'WaliKelas\HomeroomController::store');
    $routes->get('homeroom/edit/(:num)', 'WaliKelas\HomeroomController::edit/$1');
    $routes->post('homeroom/update/(:num)', 'WaliKelas\HomeroomController::update/$1');
    $routes->delete('homeroom/delete/(:num)', 'WaliKelas\HomeroomController::delete/$1');
    
    // Communication (NEW)
    $routes->get('komunikasi', 'WaliKelas\CommunicationController::index');
    $routes->get('komunikasi/broadcast', 'WaliKelas\CommunicationController::broadcast');
    $routes->post('komunikasi/send', 'WaliKelas\CommunicationController::send');
    $routes->get('komunikasi/history', 'WaliKelas\CommunicationController::history');
    
    // Analytics (NEW)
    $routes->get('analytics', 'WaliKelas\AnalyticsController::index');
    $routes->get('analytics/comparison', 'WaliKelas\AnalyticsController::comparison');
});
```

---

## 7. Implementation Priority Matrix

| Feature | Priority | Effort | Impact | Dependencies |
|---------|----------|--------|--------|--------------|
| Dashboard Device-Specific Views | HIGH | Medium | High | None |
| Enhanced Siswa Management | HIGH | High | High | None |
| Siswa Detail & Notes | HIGH | Medium | High | Siswa Management |
| Advanced Absensi Monitoring | HIGH | Medium | High | None |
| Export Functionality (Excel/PDF) | HIGH | Medium | High | Library (PHPSpreadsheet) |
| Izin Upload Bukti | MEDIUM | Low | Medium | None |
| Parent Contact Management | MEDIUM | Medium | Medium | DB Migration |
| Homeroom Management | MEDIUM | High | Medium | DB Migration |
| Communication Hub | LOW | High | Medium | DB Migration |
| Analytics Dashboard | LOW | High | High | All data complete |
| Charts & Visualizations | MEDIUM | Medium | High | Chart library |

---

## 8. Technical Stack Recommendations

### Frontend Libraries
```json
{
  "charts": "ApexCharts.js or Chart.js",
  "export": "SheetJS (for Excel client-side)",
  "notifications": "SweetAlert2 (already used)",
  "datatables": "DataTables.js (for advanced tables)",
  "icons": "Font Awesome (already used)"
}
```

### Backend Libraries
```json
{
  "excel": "PhpSpreadsheet",
  "pdf": "mPDF or TCPDF (already might be used)",
  "notifications": "CodeIgniter Email (already configured)"
}
```

---

## 9. Estimated Timeline

### Phase 1 (2-3 weeks)
- Week 1: Dashboard enhancement + Device-specific views
- Week 2: Enhanced Siswa Management + Detail views
- Week 3: Advanced Absensi Monitoring + Export

### Phase 2 (3-4 weeks)
- Week 4-5: Homeroom Management module
- Week 6: Communication Hub basic features
- Week 7: Parent Contact Management

### Phase 3 (2-3 weeks)
- Week 8: Analytics Dashboard
- Week 9: Charts & Visualizations
- Week 10: Polish, Testing, Bug fixes

**Total Estimated Time: 8-10 weeks**

---

## 10. Success Metrics

### Quantitative Metrics
- ✅ 100% feature parity with proposed menu structure
- ✅ Response time < 2s for all pages
- ✅ Mobile-friendly score > 90 (Lighthouse)
- ✅ Zero critical bugs in production

### Qualitative Metrics
- ✅ Wali Kelas can manage their class without accessing guru features
- ✅ Clear distinction between guru and wali kelas roles
- ✅ Positive user feedback from wali kelas
- ✅ Reduced time for administrative tasks

---

## 11. Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|------------|--------|------------|
| Database migration issues | Medium | High | Backup database, test migrations in staging |
| Performance degradation | Low | High | Implement caching, query optimization |
| User confusion | Medium | Medium | Clear documentation, user training |
| Scope creep | High | Medium | Stick to phased approach, prioritize |
| Breaking existing features | Low | High | Comprehensive testing, backward compatibility |

---

## 12. Next Steps

### Immediate Actions (This Week)
1. ✅ Review and approve this implementation plan
2. ⬜ Create database migrations for new tables
3. ⬜ Set up development branch for wali kelas module
4. ⬜ Install required libraries (PhpSpreadsheet, etc.)

### Short Term (Next 2 Weeks)
1. ⬜ Implement Phase 1.1 - Dashboard Enhancement
2. ⬜ Implement Phase 1.2 - Enhanced Siswa Management
3. ⬜ Create device-specific views (mobile/desktop)

### Medium Term (1-2 Months)
1. ⬜ Complete Phase 1 features
2. ⬜ Begin Phase 2 implementation
3. ⬜ User testing and feedback collection

---

## 13. Questions for Stakeholders

1. **Priority Confirmation**: Apakah prioritas yang ditetapkan sudah sesuai dengan kebutuhan bisnis?

2. **Database Changes**: Apakah penambahan tabel baru dan modifikasi tabel existing dapat disetujui?

3. **Export Format**: Format export apa yang paling dibutuhkan? (Excel, PDF, CSV?)

4. **Communication Features**: Apakah fitur komunikasi dengan orang tua perlu integrasi dengan WhatsApp/Email?

5. **Analytics Scope**: Seberapa detail analytics yang dibutuhkan? Apakah perlu prediksi/ML?

6. **Mobile vs Desktop**: Apakah wali kelas lebih sering menggunakan mobile atau desktop?

7. **Parent Access**: Apakah orang tua akan punya akses login untuk melihat data anak? (Future scope)

---

## Appendix A: File Structure

```
app/
├── Controllers/
│   └── WaliKelas/
│       ├── DashboardController.php (MODIFY)
│       ├── SiswaController.php (MODIFY)
│       ├── AbsensiController.php (MODIFY)
│       ├── IzinController.php (MODIFY)
│       ├── LaporanController.php (MODIFY)
│       ├── HomeroomController.php (NEW)
│       ├── CommunicationController.php (NEW)
│       └── AnalyticsController.php (NEW)
│
├── Models/
│   ├── ClassNotesModel.php (NEW)
│   ├── SiswaNotesModel.php (NEW)
│   ├── ParentContactModel.php (NEW)
│   ├── ClassCommunicationModel.php (NEW)
│   └── SiswaModel.php (MODIFY - add methods)
│
├── Views/
│   └── walikelas/
│       ├── dashboard.php (MODIFY)
│       ├── dashboard_mobile.php (NEW)
│       ├── dashboard_desktop.php (NEW)
│       ├── siswa/
│       │   ├── index.php (MODIFY)
│       │   ├── detail.php (NEW)
│       │   ├── catatan.php (NEW)
│       │   └── parent_contact.php (NEW)
│       ├── absensi/
│       │   ├── index.php (MODIFY)
│       │   └── detail.php (NEW)
│       ├── izin/
│       │   ├── index.php (MODIFY)
│       │   └── detail.php (NEW)
│       ├── laporan/
│       │   └── index.php (MODIFY)
│       ├── homeroom/ (NEW FOLDER)
│       │   ├── index.php
│       │   ├── create.php
│       │   └── edit.php
│       ├── komunikasi/ (NEW FOLDER)
│       │   ├── index.php
│       │   ├── broadcast.php
│       │   └── history.php
│       └── analytics/ (NEW FOLDER)
│           ├── index.php
│           └── comparison.php
│
└── Database/
    └── Migrations/
        ├── YYYY-MM-DD-HHMMSS_CreateClassNotesTable.php (NEW)
        ├── YYYY-MM-DD-HHMMSS_CreateSiswaNotesTable.php (NEW)
        ├── YYYY-MM-DD-HHMMSS_CreateParentContactsTable.php (NEW)
        ├── YYYY-MM-DD-HHMMSS_CreateClassCommunicationsTable.php (NEW)
        ├── YYYY-MM-DD-HHMMSS_AddBuktiToIzinSiswa.php (NEW)
        └── YYYY-MM-DD-HHMMSS_AddStatusToSiswa.php (NEW)
```

---

## Appendix B: Code Examples

### Example: Enhanced Dashboard Controller Method
```php
public function index()
{
    $userId = session()->get('user_id');
    $guru = $this->guruModel->getByUserId($userId);
    
    if (!$guru || !$guru['is_wali_kelas']) {
        return redirect()->to('/access-denied');
    }
    
    $kelas = $this->kelasModel->getByWaliKelas($guru['id']);
    
    // Get comprehensive statistics
    $stats = [
        'total_siswa' => $this->siswaModel->getCountKelasById($kelas['id']),
        'siswa_hadir_hari_ini' => $this->getAttendanceToday($kelas['id'], 'hadir'),
        'siswa_alpa_bulan_ini' => $this->getAlpaThisMonth($kelas['id']),
        'izin_pending' => $this->izinModel->getPendingCount($kelas['id']),
        'rata_rata_kehadiran' => $this->getAverageAttendance($kelas['id']),
    ];
    
    // Get trending data for charts
    $attendanceTrend = $this->getAttendanceTrend($kelas['id'], 30); // Last 30 days
    
    // Get top 5 problematic students
    $problematicStudents = $this->getProblematicStudents($kelas['id'], 5);
    
    $data = [
        'title' => 'Dashboard Wali Kelas',
        'guru' => $guru,
        'kelas' => $kelas,
        'stats' => $stats,
        'attendanceTrend' => $attendanceTrend,
        'problematicStudents' => $problematicStudents,
    ];
    
    return view('walikelas/dashboard', $data);
}
```

---

## Document Version History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2026-01-19 | SIMACCA Dev Team | Initial comprehensive plan |

---

**Prepared by**: SIMACCA Development Team  
**Last Updated**: 2026-01-19  
**Status**: Ready for Review & Approval
