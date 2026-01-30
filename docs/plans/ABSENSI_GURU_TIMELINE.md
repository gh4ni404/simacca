# 📅 Absensi Guru - Implementation Timeline

## 🎯 Overview
**Total Tasks:** 53 tasks across 15 phases  
**Estimated Duration:** 7 working days  
**Start Date:** 2026-01-30  
**Target Completion:** 2026-02-07  

---

## 📊 Progress Summary

```
Phase 1-2:  Database & Models        [▱▱▱▱▱▱▱▱▱▱] 0/9   (Day 1)
Phase 3-4:  Controllers              [▱▱▱▱▱▱▱▱▱▱] 0/6   (Day 2)
Phase 5-6:  Views (Guru + Wakakur)   [▱▱▱▱▱▱▱▱▱▱] 0/8   (Day 3)
Phase 7-8:  Camera & Image           [▱▱▱▱▱▱▱▱▱▱] 0/8   (Day 4)
Phase 9-10: Routes & Excel           [▱▱▱▱▱▱▱▱▱▱] 0/6   (Day 5)
Phase 11:   Business Logic           [▱▱▱▱▱▱▱▱▱▱] 0/3   (Day 5)
Phase 12:   Testing                  [▱▱▱▱▱▱▱▱▱▱] 0/8   (Day 6)
Phase 13-15: Documentation & Deploy  [▱▱▱▱▱▱▱▱▱▱] 0/5   (Day 7)
```

**Overall Progress:** 0/53 tasks (0%)

---

## 🗓️ Day-by-Day Timeline

### **DAY 1: Database & Models Foundation** (4-5 hours)

#### **Morning (09:00 - 12:00)** - Phase 1: Database Schema
- [ ] **Task 1:** Create migration `CreateAbsensiGuruTable.php` (30 min)
  - 13 columns: id, guru_id, tanggal, jam_datang, jam_pulang, foto_datang, foto_pulang, status, latitude/longitude, keterangan, early_checkout, early_checkout_reason, timestamps
  - UNIQUE constraint (guru_id + tanggal)
  - Foreign key to guru table
  - Indexes on tanggal, status

- [ ] **Task 2:** Create migration `CreateIzinGuruTable.php` (30 min)
  - Izin request workflow table
  - Fields: id, guru_id, tanggal_mulai, tanggal_selesai, jenis, alasan, dokumen, status_approval, approved_by, rejection_reason, timestamps
  - Foreign keys to guru & users (wakakur)

- [ ] **Task 3:** Run migrations (10 min)
  ```bash
  php spark migrate
  php spark migrate:status
  ```

#### **Afternoon (13:00 - 17:00)** - Phase 2: Models
- [ ] **Task 4:** Create `AbsensiGuruModel.php` (1 hour)
  - Basic CRUD setup
  - Validation rules (required fields, date formats, ENUM values)
  - Relationships: belongsTo guru

- [ ] **Task 5:** Add custom methods to AbsensiGuruModel (1.5 hours)
  ```php
  - checkIn($guruId, $fotoPath, $lat, $lng)
  - checkOut($guruId, $fotoPath, $lat, $lng)
  - getTodayAttendance($guruId)
  - getMonthlyAttendance($guruId, $month, $year)
  - getAllTodayAttendance($date)
  - getStatistics($startDate, $endDate)
  - calculateStatus($jamDatang) // hadir vs terlambat
  - getForExport($startDate, $endDate, $guruId)
  ```

- [ ] **Task 6:** Create `IzinGuruModel.php` (45 min)
  - CRUD + validation
  - Methods: getPendingRequests(), approveRequest(), rejectRequest()

**Day 1 Deliverable:** ✅ Database ready + Models with business logic

---

### **DAY 2: Controllers Logic** (5-6 hours)

#### **Morning (09:00 - 12:00)** - Phase 3: Guru Controllers
- [ ] **Task 7:** Create `Guru/AbsensiGuruController.php` (1.5 hours)
  ```php
  - index()          // Display dashboard + history
  - checkIn()        // POST - Process check-in with photo
  - checkOut()       // POST - Process check-out with photo
  - history()        // AJAX - Get monthly history
  - uploadSelfie()   // Helper for photo upload
  ```
  - Include rate limiting logic
  - Include 8-hour minimum validation
  - Return JSON for AJAX

- [ ] **Task 8:** Create `Guru/IzinGuruController.php` (1 hour)
  ```php
  - index()   // List request history
  - create()  // Form ajukan izin
  - store()   // POST - Submit request
  ```

#### **Afternoon (13:00 - 17:00)** - Phase 4: Wakakur Controllers
- [ ] **Task 9:** Create `Wakakur/AbsensiGuruController.php` - Part 1 (1 hour)
  ```php
  - index()           // Real-time monitoring dashboard
  - getTodayData()    // AJAX endpoint for auto-refresh
  - manualSet()       // POST - Wakakur set status manual
  ```

- [ ] **Task 10:** Create `Wakakur/IzinGuruController.php` (1 hour)
  ```php
  - index()      // List pending requests
  - approve()    // POST - Approve request
  - reject()     // POST - Reject with reason
  ```

- [ ] **Task 11:** Add to `Wakakur/AbsensiGuruController.php` - Part 2 (45 min)
  ```php
  - laporan()    // Rekap bulanan view
  - detail()     // Detail per guru
  ```

- [ ] **Task 12:** Add Excel export method (45 min)
  ```php
  - exportExcel() // PhpSpreadsheet implementation
  ```

**Day 2 Deliverable:** ✅ All controllers ready with business logic

---

### **DAY 3: Views - Guru & Wakakur** (6 hours)

#### **Morning (09:00 - 12:00)** - Phase 5: Guru Views
- [ ] **Task 13:** Create `guru/absensi_guru/index.php` (1.5 hours)
  - Mobile-first layout
  - Status card (today's status)
  - Action buttons (DATANG / PULANG)
  - Camera modal placeholder
  - Include Tailwind responsive classes

- [ ] **Task 14:** Update `guru/dashboard.php` - Add widget (45 min)
  - Quick access widget at top
  - Show today status
  - Large buttons (DATANG / PULANG)
  - Link to full page

- [ ] **Task 15:** Create history views (45 min)
  - `_history_table.php` (desktop: table format)
  - `_history_cards.php` (mobile: card format)
  - Color-coded status badges
  - Filter by month/year

- [ ] **Task 16:** Create `guru/izin_guru/create.php` (30 min)
  - Form: tanggal, jenis izin, alasan, upload dokumen
  - Validation display
  - Submit button

#### **Afternoon (13:00 - 17:00)** - Phase 6: Wakakur Views
- [ ] **Task 17:** Create `wakakur/absensi_guru/index.php` (1.5 hours)
  - Quick stats cards (total, hadir, terlambat, belum check-in)
  - Real-time list dengan color-coded
  - Filter by status dropdown
  - Manual set status modal

- [ ] **Task 18:** Add AJAX auto-refresh (30 min)
  ```javascript
  setInterval(() => {
    fetch('/wakakur/absensi-guru/get-today-data')
      .then(res => res.json())
      .then(data => updateDashboard(data));
  }, 30000); // 30 seconds
  ```

- [ ] **Task 19:** Create `wakakur/absensi_guru/laporan.php` (1 hour)
  - Filter form (guru, bulan, tahun)
  - Summary stats
  - Detail table
  - Export Excel button

- [ ] **Task 20:** Create `wakakur/izin_guru/index.php` (45 min)
  - List pending requests
  - Approve/Reject buttons
  - Modal for rejection reason

**Day 3 Deliverable:** ✅ All views ready (Guru + Wakakur)

---

### **DAY 4: Camera Feature & Image Processing** (5-6 hours)

#### **Morning (09:00 - 12:00)** - Phase 7: Camera JavaScript
- [ ] **Task 21:** Create `public/js/absensi-guru-camera.js` - Skeleton (30 min)
  - Module structure
  - Global variables
  - Event listeners setup

- [ ] **Task 22:** Implement `getUserMedia()` camera access (1 hour)
  ```javascript
  async function initCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'user' } // Front camera
    });
    videoElement.srcObject = stream;
  }
  ```
  - Handle permission denied
  - Handle camera not found
  - Show error messages

- [ ] **Task 23:** Implement capture, preview, retake flow (1.5 hours)
  ```javascript
  - capturePhoto()    // Canvas snapshot
  - showPreview()     // Display captured image
  - retakePhoto()     // Reset to camera view
  - prepareUpload()   // Convert to Blob/FormData
  ```

#### **Afternoon (13:00 - 17:00)** - Phase 7 & 8: Integration + Image Processing
- [ ] **Task 24:** AJAX upload integration (1 hour)
  ```javascript
  async function submitCheckIn(photoBlob) {
    const formData = new FormData();
    formData.append('photo', photoBlob);
    formData.append('csrf_token', getCSRFToken());
    
    const response = await fetch('/guru/absensi-guru/check-in', {
      method: 'POST',
      body: formData
    });
    
    // Handle success/error
  }
  ```

- [ ] **Task 25:** Backend: Use `optimize_image()` helper (30 min)
  - Already exists in `image_helper.php`
  - Call in controller after upload
  - Compress to 300-500KB

- [ ] **Task 26:** Implement date hierarchy storage (45 min)
  ```php
  $uploadPath = "writable/uploads/absensi_guru/" . date('Y/m/d');
  if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
  }
  $filename = "{$type}_guru{$guruId}_" . date('His') . ".jpg";
  ```

- [ ] **Task 27:** Add rate limiting logic (30 min)
  - Cache-based implementation
  - 3 attempts per 5 minutes
  - Return error message if exceeded

- [ ] **Task 28:** Optional: Add EXIF validation (30 min)
  - Check DateTimeOriginal
  - Warning if photo > 5 minutes old
  - Log warning but allow

**Day 4 Deliverable:** ✅ Camera fully functional + Image processing ready

---

### **DAY 5: Routes, Excel, Business Logic** (5 hours)

#### **Morning (09:00 - 12:00)** - Phase 9: Routes
- [ ] **Task 29:** Add Guru routes in `Config/Routes.php` (30 min)
  ```php
  $routes->group('guru', ['filter' => 'auth,role:guru_mapel,wali_kelas'], function($routes) {
    $routes->get('absensi-guru', 'Guru\AbsensiGuruController::index');
    $routes->post('absensi-guru/check-in', 'Guru\AbsensiGuruController::checkIn');
    $routes->post('absensi-guru/check-out', 'Guru\AbsensiGuruController::checkOut');
    $routes->get('absensi-guru/history', 'Guru\AbsensiGuruController::history');
    
    $routes->get('izin-guru', 'Guru\IzinGuruController::index');
    $routes->get('izin-guru/create', 'Guru\IzinGuruController::create');
    $routes->post('izin-guru/store', 'Guru\IzinGuruController::store');
  });
  ```

- [ ] **Task 30:** Add Wakakur routes (30 min)
  ```php
  $routes->group('wakakur', ['filter' => 'auth,role:wakakur'], function($routes) {
    $routes->get('absensi-guru', 'Wakakur\AbsensiGuruController::index');
    $routes->get('absensi-guru/get-today-data', 'Wakakur\AbsensiGuruController::getTodayData');
    $routes->post('absensi-guru/manual-set', 'Wakakur\AbsensiGuruController::manualSet');
    $routes->get('absensi-guru/laporan', 'Wakakur\AbsensiGuruController::laporan');
    $routes->get('absensi-guru/export-excel', 'Wakakur\AbsensiGuruController::exportExcel');
    
    $routes->get('izin-guru', 'Wakakur\IzinGuruController::index');
    $routes->post('izin-guru/approve/(:num)', 'Wakakur\IzinGuruController::approve/$1');
    $routes->post('izin-guru/reject/(:num)', 'Wakakur\IzinGuruController::reject/$1');
  });
  ```

- [ ] **Task 31:** Add FileController route for photos (30 min)
  ```php
  $routes->get('files/absensi-guru/(:any)', 'FileController::serveAbsensiGuruPhoto/$1');
  ```
  - Auth check before serving
  - Return 403 if not authorized

#### **Afternoon (13:00 - 17:00)** - Phase 10 & 11
- [ ] **Task 32:** Implement PhpSpreadsheet Excel export (1 hour)
  - Use existing pattern from sistem (Guru/Siswa export)
  - 11 columns format
  - Header row formatting

- [ ] **Task 33:** Add color-coded status cells (30 min)
  - Green: Hadir
  - Yellow: Terlambat
  - Red: Alpha
  - Blue: Izin/Sakit

- [ ] **Task 34:** Add clickable foto URL links (30 min)
  ```php
  $sheet->getCell("J{$row}")->getHyperlink()
       ->setUrl(base_url("files/absensi-guru/{$fotoDatang}"));
  ```

- [ ] **Task 35:** Business Logic: Auto-alpha at 10:00 (45 min)
  - Option A: Cron job (recommended)
  - Option B: On-demand check saat wakakur buka dashboard
  - Update status = 'alpha' WHERE jam_datang IS NULL AND tanggal = TODAY

- [ ] **Task 36:** Add 8-hour minimum validation modal (30 min)
  - Frontend: JavaScript check before submit
  - Backend: Validate and require keterangan if < 8 hours
  - Set early_checkout = true

- [ ] **Task 37:** Add early_checkout fields logic (15 min)
  - Set flag in database
  - Display warning badge in laporan

**Day 5 Deliverable:** ✅ Routes configured + Excel export + Business rules implemented

---

### **DAY 6: Comprehensive Testing** (5-6 hours)

#### **Full Day Testing** - Phase 12
- [ ] **Task 38:** Test Guru check-in flow (45 min)
  - Login as guru
  - Click DATANG button
  - Camera modal opens
  - Capture photo → preview → submit
  - Verify database record
  - Verify photo saved in correct path

- [ ] **Task 39:** Test check-out with validation (45 min)
  - Check-out < 8 hours → warning modal muncul
  - Input keterangan → allow check-out
  - Verify early_checkout = true
  - Check-out > 8 hours → no warning

- [ ] **Task 40:** Test izin request workflow (30 min)
  - Guru submit request
  - Wakakur sees in pending list
  - Wakakur approve → status updated
  - Wakakur reject → guru notified

- [ ] **Task 41:** Test Wakakur manual set (30 min)
  - Wakakur set guru status = Izin/Sakit
  - Verify absensi record created
  - Verify keterangan saved

- [ ] **Task 42:** Test real-time monitoring (30 min)
  - Wakakur open dashboard
  - Auto-refresh every 30 seconds
  - Stats update correctly
  - List updates real-time

- [ ] **Task 43:** Test Excel export (45 min)
  - Filter by date range
  - Filter by specific guru
  - Download Excel
  - Verify 11 columns
  - Verify foto links clickable
  - Verify color-coded status

- [ ] **Task 44:** Test camera on multiple devices (1.5 hours)
  - **Mobile:**
    - Android Chrome
    - iOS Safari
  - **Desktop:**
    - Chrome
    - Firefox
    - Edge
  - Verify camera access works
  - Verify responsive layout

- [ ] **Task 45:** Test security features (45 min)
  - Rate limiting: Try 4 check-in dalam 5 menit → blocked
  - EXIF validation: Upload old photo → warning logged
  - Direct photo URL access → 403 Forbidden (if not logged in)
  - Authorization: Guru A tidak bisa akses absensi Guru B

**Day 6 Deliverable:** ✅ All features tested and bugs fixed

---

### **DAY 7: Documentation & Deployment Prep** (4-5 hours)

#### **Morning (09:00 - 12:00)** - Phase 13: Documentation
- [ ] **Task 46:** Create printed quick guide content (1 hour)
  - Design 1-page A4 landscape
  - Include screenshots
  - Troubleshooting section
  - Contact info
  - Save as PDF for printing

- [ ] **Task 47:** Update TODO.md (30 min)
  - Add deployment notes section
  - Update completed tasks
  - Add Phase 2 features list (GPS, face recognition, etc)

- [ ] **Task 48:** Update CHANGELOG.md (30 min)
  ```markdown
  ## v2.0.0 - 2026-02-07
  
  ### Added - Absensi Guru Mandiri
  - Self check-in/check-out with selfie photo
  - Wakakur real-time monitoring dashboard
  - Izin request workflow (guru submit → wakakur approve)
  - Rekap bulanan per guru
  - Excel export with 11 columns
  - Mobile-first responsive design
  - Rate limiting anti-fraud
  - 8-hour minimum with warning
  - Date hierarchy photo storage
  - 2-year photo retention policy
  ```

#### **Afternoon (13:00 - 17:00)** - Phase 14 & 15: Cleanup & Deploy Prep
- [ ] **Task 49:** Create .htaccess for photo security (15 min)
  ```apache
  # writable/uploads/absensi_guru/.htaccess
  <IfModule authz_core_module>
      Require all denied
  </IfModule>
  ```

- [ ] **Task 50:** Create CLI command for photo cleanup (1 hour)
  ```php
  // app/Commands/CleanupAbsensiGuruPhotos.php
  php spark cleanup:absensi-guru-photos
  ```
  - Delete records > 2 years
  - Delete physical files
  - Log deleted count

- [ ] **Task 51:** Create deployment checklist (45 min)
  ```markdown
  - [ ] Run migrations
  - [ ] Create upload folders with permissions
  - [ ] Update .env (if needed)
  - [ ] Test on production URL (HTTPS for camera)
  - [ ] Setup cron job for auto-alpha
  - [ ] Backup database before deploy
  - [ ] Create test guru accounts (for pilot)
  ```

- [ ] **Task 52:** Prepare demo session materials (1 hour)
  - Create PowerPoint presentation (10 slides)
  - Prepare test account credentials
  - Write demo script
  - Setup test environment

- [ ] **Task 53:** Final review & go-live readiness (1 hour)
  - Code review checklist
  - Security audit
  - Performance check
  - Mobile responsiveness check
  - Browser compatibility check
  - Get approval from stakeholder

**Day 7 Deliverable:** ✅ Documentation complete + Ready for deployment

---

## 🎯 Critical Path Dependencies

```
Day 1 (Database + Models) 
  ↓
Day 2 (Controllers) 
  ↓
Day 3 (Views) 
  ↓
Day 4 (Camera + Images) ← Critical for testing
  ↓
Day 5 (Routes + Excel + Business Logic)
  ↓
Day 6 (Testing) ← Find and fix all bugs
  ↓
Day 7 (Documentation + Deploy Prep)
```

**⚠️ Cannot skip:** Day 1-4 are critical foundation. Day 5-7 can be parallelized if multiple developers.

---

## 📈 Success Metrics

### **Development Phase (Day 1-7)**
- [ ] All 53 tasks completed
- [ ] 0 critical bugs
- [ ] All tests passing
- [ ] Code review approved

### **Pilot Phase (Week 1)**
- [ ] 10 guru successfully use system
- [ ] 90%+ successful check-in rate
- [ ] < 5 bug reports
- [ ] Positive feedback from users

### **Full Launch (Week 3)**
- [ ] 50 guru adoption (100%)
- [ ] 95%+ daily usage
- [ ] < 5 support requests/day
- [ ] System uptime 99%+

---

## 🚨 Risk Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| Camera permission denied | High | Clear instructions in quick guide + demo session |
| Mobile browser compatibility | Medium | Test on multiple devices Day 6 |
| Photo storage full | Medium | 2-year retention + cleanup command |
| Guru forget check-in | Medium | Wakakur can manual set + notification (Phase 2) |
| System down on launch day | High | Fallback to manual Excel backup |

---

## 📞 Support Plan

### **Week 1-3 (Rollout Period)**
- **Primary Support:** IT Team (WhatsApp group)
- **Response Time:** < 30 minutes
- **Availability:** 07:00 - 16:00 WIB (jam kerja)

### **Post-Launch (Week 4+)**
- **Support Level:** Passive (on-demand only)
- **Response Time:** < 2 hours
- **Known Issues:** Track in TODO.md

---

**Last Updated:** 2026-01-30  
**Status:** Ready to Start Implementation  
**Next Action:** Begin Day 1 - Task 1 (Create migration file)
