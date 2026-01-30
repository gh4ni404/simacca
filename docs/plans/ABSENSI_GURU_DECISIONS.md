# 📋 Absensi Guru - Business Decisions Log

## 📅 Date: 2026-01-30
## 👥 Decided by: Client + Development Team

---

## ✅ KATEGORI 1: BUSINESS RULES

### **1. JAM MASUK SEKOLAH**
**Decision:** Custom Configuration
- **Jam masuk standar:** 07:15 WIB
- **Tolerance:** 165 menit (2 jam 45 menit)
- **Batas akhir hadir:** 10:00 WIB (07:15 + 165 menit)

**Status Logic:**
```
Guru check-in <= 07:15 WIB  → Status: "Hadir (Tepat Waktu)" ✅
Guru check-in 07:16 - 10:00 → Status: "Terlambat" ⚠️
Guru check-in > 10:00       → Status: "Alpha" ❌ (atau tidak bisa check-in)
```

**Implementation Notes:**
- Tolerance 165 menit = very flexible approach
- Allow guru datang sampai jam 10:00 masih dianggap terlambat (bukan alpha)
- Config harus bisa diubah via settings (tidak hardcode)

---

### **2. STATUS AUTO-ALPHA**
**Decision:** Jam 10:00 WIB (Strict)

**Logic:**
- Jika guru belum check-in sampai jam 10:00 → Status otomatis "Alpha"
- Sistem akan run cron job atau background task untuk auto-update status
- Setelah jam 10:00, guru tidak bisa lagi check-in untuk hari itu (blocked)

**Implementation:**
- Cron job: Run setiap jam 10:05 WIB
- Query: UPDATE absensi_guru SET status='alpha' WHERE tanggal=TODAY AND jam_datang IS NULL
- Alternative: Check on-demand saat admin buka laporan

---

### **3. WORKFLOW IZIN/SAKIT**
**Decision:** HYBRID - Both Options Implemented ⭐

**Option A + B Combined:**
1. **Wakakur Set Manual** (untuk kasus urgent/telepon)
   - Guru telepon/WA ke Wakakur: "Pak, saya sakit hari ini"
   - Wakakur login → Menu "Kelola Absensi Guru"
   - Wakakur pilih guru → Set status manual → Input keterangan
   - Status langsung berubah (no approval needed)

2. **Guru Submit Request → Wakakur Approve** (self-service)
   - Guru login → Menu "Absensi Saya"
   - Klik tombol "Ajukan Izin/Sakit"
   - Form: Tanggal, Jenis (Izin/Sakit/Cuti/Tugas Dinas), Alasan, Upload dokumen (optional)
   - Submit → Status = "Pending Approval"
   - Wakakur dapat notifikasi (email + in-app)
   - Wakakur review → Klik "Approve" atau "Reject"
   - Jika Approved → Status otomatis jadi Izin/Sakit
   - Jika Rejected → Guru dapat notifikasi + reason

**Key Points:**
- ⭐ **Wakakur sebagai approver** (bukan Admin)
- Reason: Wakakur handle kepegawaian & kehadiran guru
- Admin fokus ke sistem management
- Request history tetap tersimpan (audit trail)

**Database Schema Addition:**
```sql
-- Table: izin_guru (new)
CREATE TABLE izin_guru (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guru_id INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    jenis ENUM('izin', 'sakit', 'cuti', 'tugas_dinas') NOT NULL,
    alasan TEXT NOT NULL,
    dokumen_pendukung VARCHAR(255) NULL,
    status_approval ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT NULL, -- wakakur user_id
    approved_at DATETIME NULL,
    rejection_reason TEXT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (guru_id) REFERENCES guru(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);
```

**Controllers Needed:**
- `Guru/IzinGuruController` - Guru submit request, view history
- `Wakakur/IzinGuruController` - Wakakur approve/reject, manual set
- `Wakakur/AbsensiGuruController` - Wakakur manual set status absensi

---

### **4. MINIMUM JAM KERJA**
**Decision:** 8 Jam with Warning (Flexible but Controlled)

**Logic:**
```php
$jamDatang = '07:30:00';
$jamPulang = '12:00:00'; // User mau check-out
$jamKerja = (strtotime($jamPulang) - strtotime($jamDatang)) / 3600; // 4.5 jam

if ($jamKerja < 8) {
    // Show warning modal:
    // "⚠️ Anda baru kerja 4.5 jam (kurang dari 8 jam standar)."
    // "Keterangan wajib diisi untuk early check-out."
    // [Input keterangan] [Cancel] [Tetap Check-out]
    
    if (user confirms) {
        // Allow check-out dengan keterangan wajib
        // Flag record: early_checkout = true
        // Admin/Wakakur bisa review di laporan
    }
}
```

**Database Field Addition:**
```sql
ALTER TABLE absensi_guru ADD COLUMN early_checkout BOOLEAN DEFAULT false;
ALTER TABLE absensi_guru ADD COLUMN early_checkout_reason TEXT NULL;
```

**Implementation:**
- Frontend: JavaScript validation before submit check-out
- Backend: Double-check jam kerja, enforce keterangan jika < 8 jam
- Laporan: Filter "Early Check-outs" untuk Wakakur review

---

## 📊 SUMMARY CONFIGURATION

```php
// app/Config/AbsensiGuru.php (NEW FILE)

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AbsensiGuru extends BaseConfig
{
    // Jam Masuk Configuration
    public string $jamMasukStandar = '07:15:00';
    public int $toleranceMinutes = 165; // 2 jam 45 menit
    public string $batasAkhirHadir = '10:00:00'; // Calculated or manual
    
    // Status Logic
    public string $autoAlphaTime = '10:00:00';
    public bool $blockCheckInAfterAlpha = true; // Tidak bisa check-in setelah jam 10:00
    
    // Jam Kerja
    public int $minimumJamKerja = 8;
    public bool $allowEarlyCheckout = true;
    public bool $requireEarlyCheckoutReason = true;
    
    // Approval Workflow
    public string $approverRole = 'wakakur'; // Role yang bisa approve izin
    public bool $enableSelfServiceIzin = true; // Guru bisa submit request
    public bool $enableManualSetByWakakur = true; // Wakakur bisa set manual
    
    // Notification
    public bool $notifyWakakurOnRequest = true;
    public bool $notifyGuruOnApproval = true;
}
```

---

---

## 🗺️ KATEGORI 2: GPS POLICY
**Decision:** SKIPPED - Moved to TODO.md (Future Enhancement Phase 2)

**Reasoning:**
- Fokus fase 1 ke core features: Check-in/out + Photo selfie
- GPS validation bisa ditambahkan nanti tanpa mengubah struktur database
- Database schema sudah siap (fields: latitude, longitude exist)

**Added to TODO.md:**
- GPS location tracking (optional feature)
- Configuration: School coordinates, radius validation
- Warning system for out-of-radius check-ins

---

---

## 🎨 KATEGORI 3: UI/UX DESIGN

### **8. MENU PLACEMENT**
**Decision:** Both - Dashboard Widget + Sidebar Menu ⭐

**Implementation:**
1. **Dashboard Widget (Quick Access)**
   ```
   ┌─────────────────────────────────────┐
   │  📸 Absensi Hari Ini - 30 Jan 2026  │
   │  Status: Belum Absen                │
   │  ┌─────────────┐ ┌────────────────┐ │
   │  │ 📸 DATANG   │ │ 🏠 PULANG      │ │
   │  └─────────────┘ └────────────────┘ │
   │  [Lihat Riwayat Lengkap →]          │
   └─────────────────────────────────────┘
   ```
   - Position: Top of dashboard (priority placement)
   - Show today's status (Belum Absen / Sudah Datang / Sudah Pulang)
   - Quick action buttons
   - Link to full page
   
2. **Sidebar Menu (Full Access)**
   ```
   Sidebar Guru:
   ├── 📊 Dashboard
   ├── 👤 Absensi Saya  ← NEW MENU
   ├── 📅 Jadwal Mengajar
   ├── ✅ Absensi Siswa
   ├── 📖 Jurnal KBM
   └── 📄 Laporan
   ```
   - Full page: Check-in/out + History + Statistics
   - URL: `/guru/absensi-guru`

**Files:**
- Widget: `app/Views/guru/dashboard.php` (add widget section)
- Full page: `app/Views/guru/absensi_guru/index.php`

---

### **9. LAYOUT PRIORITY**
**Decision:** Mobile First (Responsive Design) 📱

**Reasoning:**
- Guru check-in saat baru sampai sekolah (belum di ruang guru)
- HP lebih praktis untuk foto selfie
- Check-out juga sering dari HP (di perjalanan pulang)

**Implementation:**
- Base CSS: Mobile layout (320px - 768px)
- Touch-friendly buttons: Min 48px height, large tap targets
- Font sizes: 16px+ (prevent mobile zoom)
- Camera interface: Optimized for mobile camera
- Desktop: Layout tetap bagus (breakpoint 768px+)

**Breakpoints:**
```css
/* Mobile First */
.btn-absensi { padding: 1rem; font-size: 1.125rem; }

/* Tablet */
@media (min-width: 768px) { ... }

/* Desktop */
@media (min-width: 1024px) { ... }
```

---

### **10. CAMERA UI FLOW**
**Decision:** Modal dengan Preview (Detailed Flow) 📸

**User Flow:**
```
Step 1: Click "DATANG" button
   ↓
Step 2: Modal popup (full screen overlay)
   - Title: "Foto Selfie - Check-in"
   - Camera permission request (jika first time)
   ↓
Step 3: Live camera preview
   - Video stream dari front camera
   - Frame guide (oval untuk posisi wajah)
   - Tips: "Pastikan wajah terlihat jelas"
   ↓
Step 4: Click "📸 Ambil Foto" button
   - Freeze video
   - Capture image
   ↓
Step 5: Preview captured photo
   - Show photo
   - Options:
     [🔄 Ulangi] - Back to Step 3
     [✅ Kirim] - Continue to Step 6
   ↓
Step 6: Submit & Process
   - Show loading spinner
   - Upload foto + data
   - Success: Close modal, show toast "✅ Check-in berhasil!"
   - Error: Show error message, allow retry
```

**UI Components:**
```html
<div id="cameraModal" class="modal">
  <div class="modal-content">
    <h3>📸 Foto Selfie - Check-in</h3>
    
    <!-- Camera Preview -->
    <video id="camera-preview" autoplay></video>
    <canvas id="photo-canvas" style="display:none"></canvas>
    
    <!-- Captured Photo Preview -->
    <img id="photo-preview" style="display:none">
    
    <!-- Actions -->
    <div id="capture-actions">
      <button id="btn-capture">📸 Ambil Foto</button>
    </div>
    
    <div id="preview-actions" style="display:none">
      <button id="btn-retake">🔄 Ulangi</button>
      <button id="btn-submit">✅ Kirim</button>
    </div>
  </div>
</div>
```

**JavaScript File:** `public/js/absensi-guru-camera.js`

---

### **11. HISTORY TABLE DESIGN**
**Decision:** Responsive (Desktop Table + Mobile Card) 📊

**Desktop View (Table):**
```
┌──────────────────────────────────────────────────────────────┐
│  Riwayat Absensi - Januari 2026                             │
│  Filter: [Bulan ▼] [Tahun ▼] [Status ▼]                    │
├──────────┬───────────┬───────────┬───────────┬──────────────┤
│ Tanggal  │ Jam Datang│ Jam Pulang│ Total Jam │ Status       │
├──────────┼───────────┼───────────┼───────────┼──────────────┤
│ 30 Jan   │ 07:15     │ 16:00     │ 8j 45m    │ ✅ Hadir     │
│ 29 Jan   │ 07:45     │ 16:05     │ 8j 20m    │ ⚠️ Terlambat│
│ 28 Jan   │ -         │ -         │ -         │ 📋 Izin      │
│ 27 Jan   │ 07:20     │ 12:30     │ 5j 10m ⚠️ │ ✅ Hadir     │
└──────────┴───────────┴───────────┴───────────┴──────────────┘
                                          [📥 Export Excel]
```
- Compact, banyak data terlihat sekaligus
- Sortable columns
- Color-coded status badges
- Action column: [👁️ Lihat Foto]

**Mobile View (Card):**
```
┌─────────────────────────────────────┐
│ 📅 Kamis, 30 Januari 2026           │
│ ┌─────────────────────────────────┐ │
│ │ ✅ Hadir (Tepat Waktu)          │ │
│ │ 🕐 Datang: 07:15 WIB            │ │
│ │ 🕔 Pulang: 16:00 WIB            │ │
│ │ ⏱️  Total: 8 jam 45 menit        │ │
│ │ [👁️ Lihat Foto]                 │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ 📅 Rabu, 29 Januari 2026            │
│ ┌─────────────────────────────────┐ │
│ │ ⚠️ Terlambat (45 menit)         │ │
│ │ 🕐 Datang: 07:45 WIB            │ │
│ │ 🕔 Pulang: 16:05 WIB            │ │
│ │ ⏱️  Total: 8 jam 20 menit        │ │
│ │ [👁️ Lihat Foto]                 │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```
- Large, touch-friendly cards
- Easy scrolling
- All info visible (no horizontal scroll)
- Color-coded cards (green=hadir, yellow=terlambat, red=alpha)

**Implementation:**
```php
// View: guru/absensi_guru/index.php
<div class="history-section">
  <!-- Desktop Table -->
  <div class="hidden md:block">
    <?= $this->include('guru/absensi_guru/_history_table') ?>
  </div>
  
  <!-- Mobile Cards -->
  <div class="block md:hidden">
    <?= $this->include('guru/absensi_guru/_history_cards') ?>
  </div>
</div>
```

---

---

## 🔐 KATEGORI 4: SECURITY & FILE UPLOAD

### **12. FILE COMPRESSION**
**Decision:** Standard Compression (300-500KB) ⚖️

**Configuration:**
- Max upload size: 5MB (before compression)
- Target size: 300-500KB (after compression)
- Resolution: 800x800px (optimal for face recognition)
- Quality: 85% JPEG
- Format: JPG/JPEG only (PNG will be converted)

**Storage Calculation:**
```
Daily: 50 guru × 2 foto × 400KB avg = 40 MB/day
Monthly: 40 MB × 22 working days = 880 MB/month
Yearly: 880 MB × 12 months = 10.5 GB/year
```

**Implementation:**
- Use existing `optimize_image()` helper from `image_helper.php`
- Already has: EXIF rotation, resize, compress, maintain aspect ratio
- Path: `writable/uploads/absensi_guru/YYYY/MM/DD/`

---

### **13. PHOTO RETENTION POLICY**
**Decision:** 2 Tahun Retention ⏰

**Policy:**
- Keep: Current + Previous academic year (2 tahun)
- Delete: Automatically after 2 tahun via cron job
- Storage: ~21 GB (2 tahun full)

**Cron Job Implementation:**
```php
// app/Commands/CleanupAbsensiGuruPhotos.php
// Run: php spark cleanup:absensi-guru-photos

$cutoffDate = date('Y-m-d', strtotime('-2 years'));

// Delete records older than 2 years
$this->absensiGuruModel->where('tanggal <', $cutoffDate)->delete();

// Delete physical files
$oldPhotoPath = "writable/uploads/absensi_guru/" . date('Y', strtotime('-2 years'));
if (is_dir($oldPhotoPath)) {
    delete_files($oldPhotoPath, true); // Recursive delete
}
```

**Schedule:** Run monthly (first day of month at 02:00 AM)

---

### **14. ANTI-FRAUD MEASURES**
**Decision:** Rate Limiting + EXIF Validation (Optional) 🛡️

**A. Rate Limiting (Mandatory)**
```php
// Prevent spam check-in attempts
$lastAttempt = $this->cache->get("absensi_attempt_{$guruId}");
if ($lastAttempt && (time() - $lastAttempt) < 300) { // 5 minutes
    return "Terlalu banyak percobaan. Tunggu 5 menit.";
}

// Track attempt
$this->cache->save("absensi_attempt_{$guruId}", time(), 600); // 10 min cache
```

**Configuration:**
- Max attempts: 3 attempts per 5 menit
- Lockout: 5 menit setelah 3 attempts
- Alert: Email ke Wakakur jika ada suspicious activity

**B. EXIF Validation (Optional - Phase 2)**
```php
// Validate photo timestamp (detect foto lama dari gallery)
if (function_exists('exif_read_data')) {
    $exif = @exif_read_data($photoPath);
    
    if (isset($exif['DateTimeOriginal'])) {
        $photoTime = strtotime($exif['DateTimeOriginal']);
        $timeDiff = time() - $photoTime;
        
        // Warning jika foto lebih dari 5 menit yang lalu
        if ($timeDiff > 300) {
            // Log warning but allow (with flag)
            log_message('warning', "Old photo used: {$guruId}, {$timeDiff}s old");
            $data['old_photo_warning'] = true;
        }
    }
}
```

**Additional Safeguards (Already Implemented):**
1. ✅ Timestamp server-side (tidak bisa manipulasi jam)
2. ✅ UNIQUE constraint (guru_id + tanggal) - prevent duplicate
3. ✅ Device info tracking (user agent, IP address)
4. ✅ Audit trail (created_at, updated_at)

**Face Detection API:** Skipped for Phase 1, consider for Phase 2

---

### **15. FILE STORAGE STRUCTURE**
**Decision:** Date Hierarchy 📁

**Structure:**
```
writable/uploads/absensi_guru/
├── 2026/
│   ├── 01/  (Januari)
│   │   ├── 30/
│   │   │   ├── datang_guru123_073015.jpg
│   │   │   ├── pulang_guru123_160030.jpg
│   │   │   ├── datang_guru456_073520.jpg
│   │   │   └── pulang_guru456_160145.jpg
│   │   ├── 29/
│   │   │   └── ...
│   │   └── ...
│   ├── 02/  (Februari)
│   │   └── ...
│   └── ...
├── 2025/
│   └── ...
└── .htaccess  (Deny direct access)
```

**Filename Format:**
```php
// Format: {type}_guru{id}_{time}.jpg
$filename = sprintf(
    '%s_guru%d_%s.jpg',
    $type,              // 'datang' or 'pulang'
    $guruId,            // 123
    date('His')         // 073015 (07:30:15)
);

// Full path: writable/uploads/absensi_guru/2026/01/30/datang_guru123_073015.jpg
```

**Benefits:**
- ✅ Organized by date (easy to find)
- ✅ Easy cleanup (delete whole year folder)
- ✅ Better file system performance (max ~100 files per folder/day)
- ✅ Supports concurrent uploads (timestamp in filename prevents collision)

**Security (.htaccess):**
```apache
# writable/uploads/absensi_guru/.htaccess
<IfModule authz_core_module>
    Require all denied
</IfModule>
<IfModule !authz_core_module>
    Deny from all
</IfModule>
```
- Photos only accessible via controller (with auth check)
- Direct URL access = 403 Forbidden

---

---

## 📊 KATEGORI 5: REPORTING & EXPORT

### **16. LAPORAN PRIORITAS**
**Decision:** A + B + E (Top 3 Essential Reports)

**A. Monitoring Real-time Hari Ini** ⭐ **PRIORITAS TERTINGGI**
- **View:** `wakakur/absensi_guru/index.php`
- **Features:**
  - Quick stats card: Total guru, Sudah check-in (%), Terlambat, Belum check-in, Izin
  - Real-time list dengan color-coded status
  - Filter by status: All / Hadir / Terlambat / Belum check-in
  - Auto-refresh setiap 30 detik (AJAX)
  - Quick action: Click guru → view detail/history
- **Use Case:** Daily ops - Monitor kehadiran pagi hari (07:00-10:00)
- **Target User:** Wakakur
- **Implementation Priority:** HIGH (Week 1)

**B. Rekap Bulanan per Guru** ⭐ **PRIORITAS TINGGI**
- **View:** `wakakur/absensi_guru/laporan.php`
- **Features:**
  - Filter: Pilih guru (dropdown), Bulan, Tahun
  - Summary stats: Total hari kerja, Hadir, Terlambat, Izin, Sakit, Alpha, Persentase
  - Detail table: Tanggal, Jam datang, Jam pulang, Total jam, Status
  - Chart: Pie chart (breakdown status)
  - Export button: Excel detailed format
- **Use Case:** Monthly report untuk evaluasi kinerja & payroll
- **Target User:** Wakakur, HRD, Kepala Sekolah
- **Implementation Priority:** HIGH (Week 1)

**E. Export Excel Master Data** ⭐ **PRIORITAS TINGGI**
- **Endpoint:** `GET /wakakur/absensi-guru/export-excel`
- **Features:**
  - Filter: Date range (from - to), Guru (all or specific), Status filter
  - Format: Detailed export (11 columns)
  - Filename: `Absensi_Guru_20260101-20260131.xlsx`
  - Auto-download
- **Use Case:** Raw data untuk custom analysis, payroll, archive
- **Target User:** Wakakur, HRD
- **Implementation Priority:** HIGH (Week 1)

**Phase 2 Reports (Not Implemented Now):**
- C. Ranking Kehadiran Guru (nice to have, manual dari Excel)
- D. Laporan Keterlambatan (analytics, manual dulu)

---

### **17. EXCEL EXPORT FORMAT**
**Decision:** B - Detailed Export (11 Columns)

**Column Structure:**
```
| No | Tanggal | NIP | Nama Guru | Jam Datang | Jam Pulang | Total Jam | Status | Keterangan | URL Foto Datang | URL Foto Pulang |
```

**Sample Data:**
```
| 1 | 30 Jan 2026 | 123456 | John Doe | 07:15 | 16:00 | 8j 45m | Hadir | - | https://simacca.../datang_guru123.jpg | https://simacca.../pulang_guru123.jpg |
| 2 | 30 Jan 2026 | 123457 | Jane Smith | 07:45 | 16:05 | 8j 20m | Terlambat | - | https://... | https://... |
| 3 | 30 Jan 2026 | 123458 | Bob Wilson | - | - | - | Izin | Sakit demam | - | - |
```

**Implementation Details:**
- Library: PhpSpreadsheet (already used in sistem)
- Foto URL: Full URL via FileController route (auth protected)
- Cell format: 
  - Tanggal: Date format (dd/mm/yyyy)
  - Jam: Time format (HH:MM)
  - Status: Color-coded (green=hadir, yellow=terlambat, red=alpha, blue=izin)
- Header row: Bold, background color
- Auto-width columns
- Total row at bottom (summary)

**Controller Method:**
```php
// Wakakur/AbsensiGuruController.php
public function exportExcel() {
    $startDate = $this->request->getGet('start_date');
    $endDate = $this->request->getGet('end_date');
    $guruId = $this->request->getGet('guru_id'); // null = all
    
    $data = $this->absensiGuruModel->getForExport($startDate, $endDate, $guruId);
    
    // Generate Excel with PhpSpreadsheet
    // Include foto URLs
    // Return download
}
```

---

## 🚀 KATEGORI 6: DEPLOYMENT STRATEGY

### **18. ROLLOUT STRATEGY**
**Decision:** B - Phased Rollout (3 Weeks, Safe Launch) 🎯

**Week 1: PILOT (10 Guru - 20%)**
- **Target:** 10 guru tech-savvy (early adopters)
- **Selection Criteria:**
  - Comfortable dengan teknologi
  - Bisa kasih feedback konstruktif
  - Mix: Senior & junior guru
  - Representative dari berbagai mapel
- **Activities:**
  - Monday: Demo session batch 1 (30 min)
  - Monday-Friday: Active monitoring by IT team
  - Daily: Check success rate, collect feedback
  - Friday: Review session, fix critical bugs
- **Success Metrics:**
  - 90%+ successful check-in rate
  - < 5 bug reports
  - Positive user feedback
- **Deliverables:**
  - Bug fix patch
  - Updated quick guide (jika ada confusing step)

**Week 2: EXPANSION (25 Guru - 70% Total)**
- **Target:** +25 guru (total 35 guru = 70%)
- **Selection:** General population, mixed skill levels
- **Activities:**
  - Monday: Demo session batch 2 (30 min)
  - Monitor scalability (35 concurrent users)
  - Support desk ready (IT team standby)
  - Wednesday: Mid-week check-in survey
- **Success Metrics:**
  - 85%+ successful check-in rate
  - System stable (no performance issue)
  - < 10 support requests/day
- **Deliverables:**
  - Performance optimization (if needed)
  - Additional FAQ based on week 2 questions

**Week 3: FULL LAUNCH (15 Guru - 100%)**
- **Target:** Remaining 15 guru
- **Selection:** All remaining guru
- **Activities:**
  - Monday: Demo session batch 3 (30 min)
  - Normal operations mode
  - Passive support (on-demand only)
- **Success Metrics:**
  - 95%+ adoption rate (all guru using system)
  - < 5 support requests/day
  - System uptime 99%+
- **Deliverables:**
  - Final documentation
  - Handover to maintenance mode

**Fallback Plan (Emergency):**
- If critical system failure: Manual Excel backup (1-2 hari max)
- Wakakur collect manual data via WhatsApp group
- IT team fix issue ASAP, resume system

**Communication Plan:**
- Week 0: Announcement to all guru (via email + WhatsApp group)
- Week 1-3: Daily status update in WhatsApp group
- Week 4: Post-launch survey (user satisfaction)

---

### **19. TRAINING & SUPPORT**
**Decision:** B + C (Printed Guide + Demo Session)

**B. Printed Quick Guide (1 Halaman, Laminated)** 📄

**Content Structure:**
```
┌─────────────────────────────────────────────┐
│   PANDUAN CEPAT - ABSENSI GURU MANDIRI      │
│   SIMACCA v2.0                              │
├─────────────────────────────────────────────┤
│                                             │
│   CARA CHECK-IN (DATANG)                    │
│   1. Buka browser → simacca.sch.id          │
│   2. Login dengan akun Anda                 │
│   3. Di Dashboard, klik tombol "DATANG"     │
│   4. [Screenshot: Tombol DATANG]            │
│   5. Izinkan akses kamera (jika diminta)    │
│   6. Ambil foto selfie Anda                 │
│   7. [Screenshot: Camera preview]           │
│   8. Klik "Kirim" → Selesai!                │
│                                             │
│   CARA CHECK-OUT (PULANG)                   │
│   - Sama seperti check-in                   │
│   - Klik tombol "PULANG" (biru)             │
│   - Foto selfie → Kirim                     │
│                                             │
│   TROUBLESHOOTING                           │
│   ❓ Kamera tidak muncul?                   │
│   → Pastikan browser punya izin kamera      │
│   → Chrome: Settings → Privacy → Camera     │
│                                             │
│   ❓ Foto blur/gelap?                       │
│   → Ulangi foto (tombol "Ulangi")           │
│   → Pastikan pencahayaan cukup              │
│                                             │
│   ❓ Tombol "PULANG" tidak aktif?           │
│   → Pastikan sudah check-in pagi            │
│                                             │
│   ❓ Lupa check-in?                         │
│   → Hubungi Wakakur untuk input manual      │
│                                             │
│   BANTUAN TEKNIS                            │
│   📞 WhatsApp: 0812-xxxx-xxxx (IT Support)  │
│   📧 Email: support@simacca.sch.id          │
│   🕒 Jam kerja: 07:00 - 16:00 WIB           │
└─────────────────────────────────────────────┘
```

**Specifications:**
- Size: A4 (landscape orientation)
- Material: Paper 120gsm, laminated glossy
- Print: Full color dengan screenshot
- Quantity: 60 copies (50 guru + 10 backup)
- Cost estimate: ~Rp 60,000 (60 × Rp 1,000)
- Distribution: 1 per guru + tempel di ruang guru/kantor Wakakur

**C. Demo Session (30 Menit × 3 Batch)** 🎓

**Session Structure:**
```
Durasi: 30 menit
Location: Lab komputer / Ruang multimedia

Agenda:
00:00 - Opening & Introduction (5 min)
  - Kenapa sistem baru?
  - Benefit untuk guru (no paper, automatic report)
  - Overview fitur

05:00 - Live Demo (10 min)
  - Presenter: IT Team dengan proyektor
  - Demo check-in: Login → Klik DATANG → Camera → Foto → Submit
  - Demo check-out: Sama seperti check-in
  - Demo lihat history (optional)
  - Show dashboard widget di homepage

15:00 - Hands-on Practice (10 min)
  - Guru try on their own phone
  - IT team berkeliling bantu yang kesulitan
  - Encourage guru buat test check-in (akan dihapus setelah demo)

25:00 - Q&A Session (5 min)
  - Open floor untuk pertanyaan
  - Collect feedback/concerns
  - Bagikan printed quick guide

30:00 - Closing
  - Reminder: Go-live date
  - Support contact info
```

**Schedule:**
- **Batch 1 (Pilot):** Week 1, Monday, 14:00-14:30 (10 guru)
- **Batch 2 (Expansion):** Week 2, Monday, 14:00-14:30 (25 guru)
- **Batch 3 (Full):** Week 3, Monday, 14:00-14:30 (15 guru)

**Resources Needed:**
- Projector + screen
- WiFi access
- Test account (untuk demo tanpa affect real data)
- Printed guides (distribute after session)

**Optional (Phase 2):**
- **A. Video Tutorial:** Record batch 1 demo → upload ke YouTube (unlisted) → QR code di printed guide
- **D. One-on-one Support:** Passive mode, IT team standby Week 1-3 for WhatsApp/phone support

---

## ✅ ALL DECISIONS FINALIZED

**Summary of All Decisions (6 Categories):**

### **1. Business Rules** ✅
- Jam masuk: 07:15 WIB, Tolerance: 165 menit (sampai 10:00 WIB = terlambat)
- Auto-alpha: Jam 10:00 WIB
- Workflow izin: Hybrid (Wakakur set manual + Guru submit request → Wakakur approve)
- Minimum jam kerja: 8 jam with warning (allow early checkout dengan keterangan)

### **2. GPS Policy** ✅
- Skipped - Phase 2 (database fields ready)

### **3. UI/UX Design** ✅
- Menu: Both (Dashboard widget + Sidebar menu)
- Layout: Mobile first (responsive design)
- Camera: Modal dengan preview (detailed flow)
- History: Responsive (desktop table, mobile card)

### **4. Security & File Upload** ✅
- Compression: 300-500KB (standard, 85% quality)
- Retention: 2 tahun
- Anti-fraud: Rate limiting + EXIF validation (optional)
- Storage: Date hierarchy (YYYY/MM/DD/)

### **5. Reporting & Export** ✅
- Laporan: A (Real-time monitoring) + B (Rekap bulanan) + E (Excel export)
- Excel format: Detailed (11 columns dengan foto links)

### **6. Deployment Strategy** ✅
- Rollout: Phased (Week 1: 10 guru, Week 2: +25, Week 3: +15)
- Training: Printed guide + Demo session 3×

---

## 🎯 NEXT STEPS: IMPLEMENTATION START

**Ready to Begin Implementation! 🚀**

Saya akan mulai dari:
1. ✅ Update TODO.md dengan final decisions
2. ✅ Create detailed technical specification
3. 🛠️ Start coding (Migration → Model → Controller → View)

**Apakah Anda ingin:**
- **A.** Saya langsung mulai implementasi sekarang? (coding dimulai)
- **B.** Review final decisions document dulu sebelum coding?
- **C.** Ada yang ingin diubah dari decisions di atas?

Silakan pilih A, B, atau C! 😊
