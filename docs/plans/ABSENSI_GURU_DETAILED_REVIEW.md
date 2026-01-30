# 🔍 Review Detail: Rencana Implementasi Absensi Guru Mandiri

## 📅 Date: 2026-01-30
## 👥 Reviewers: Developer Team + Client

---

## ✅ **1. DATABASE SCHEMA - Yang Perlu Didiskusikan**

### **A. Field `status` - Business Logic**

**Pertanyaan Kritis:**

1️⃣ **Jam Masuk Sekolah Berapa?**
   - Rencana: Hadir jika <= 07:30, Terlambat jika > 07:30
   - ❓ **Apakah jam 07:30 sudah benar?**
   - ❓ **Apakah perlu tolerance time? (misal: 07:35 masih dianggap tepat waktu)**

2️⃣ **Status "Alpha" - Kapan Ditentukan?**
   - Rencana: Default alpha jika tidak check-in sampai jam 12:00
   - ❓ **Apakah jam 12:00 sudah tepat? Atau lebih baik jam 10:00?**
   - ❓ **Perlu auto-update status via cron job atau manual check?**

3️⃣ **Status Izin/Sakit - Siapa yang Set?**
   - Rencana: Admin set manual
   - ❓ **Apakah guru bisa submit izin/sakit sendiri? (seperti siswa)**
   - ❓ **Perlu approval workflow? (Guru request → Admin approve)**

**Rekomendasi:**
```sql
-- Tambahan field untuk workflow izin
is_pengajuan_izin BOOLEAN DEFAULT false,
jenis_izin ENUM('izin', 'sakit', 'cuti', 'tugas_dinas') NULL,
alasan TEXT NULL,
dokumen_pendukung VARCHAR(255) NULL, -- surat sakit, dll
approved_by INT NULL, -- admin yang approve
approved_at DATETIME NULL
```

---

### **B. GPS Tracking - Mandatory atau Optional?**

**Skenario 1: GPS Mandatory (Strict)**
- ✅ Anti-fraud lebih kuat
- ❌ Ribet jika GPS device error
- ❌ Guru di luar sekolah (piket lapangan) tidak bisa absen

**Skenario 2: GPS Optional with Warning (Recommended)**
- ✅ Fleksibel untuk edge cases
- ✅ Tetap record GPS jika available
- ⚠️ Admin bisa review absen tanpa GPS
- 📊 Laporan: Berapa % absen without GPS

**Skenario 3: GPS Disabled**
- ✅ Paling simple
- ❌ Lebih mudah di-fraud

**❓ Pilihan Anda: Skenario 1, 2, atau 3?**

**Rekomendasi:** Skenario 2 (Optional with Warning)
```php
// Field tambahan untuk tracking
gps_enabled BOOLEAN DEFAULT true,
gps_error_message VARCHAR(255) NULL, // "GPS tidak tersedia", dll
device_info VARCHAR(255) NULL, // Browser, OS info untuk audit
```

---

### **C. Jam Pulang - Business Rules**

**Pertanyaan:**

1️⃣ **Minimum Jam Kerja?**
   - ❓ Apakah ada minimum jam kerja? (misal: min 8 jam)
   - ❓ Boleh pulang kapan saja atau minimal jam 15:00?

2️⃣ **Lupa Check-out?**
   - ❓ Jika guru lupa check-out, bagaimana?
   - ❓ Auto set jam 16:00 pada hari berikutnya?
   - ❓ Admin bisa manual input jam pulang?

**Rekomendasi:**
```php
// Validation logic in controller
public function checkOut() {
    $jamDatang = $todayRecord['jam_datang'];
    $jamPulang = date('H:i:s');
    
    // Calculate work hours
    $workHours = (strtotime($jamPulang) - strtotime($jamDatang)) / 3600;
    
    // Warning if less than 8 hours
    if ($workHours < 8) {
        // Show warning but allow (with keterangan field)
    }
}
```

---

## 🎨 **2. UI/UX DESIGN - Yang Perlu Didiskusikan**

### **A. Layout Halaman Guru**

**Current Plan:**
```
┌─────────────────────────────────┐
│ [Card: Status Hari Ini]         │ ← Info box
├─────────────────────────────────┤
│ [Tombol DATANG] [Tombol PULANG] │ ← Action buttons
├─────────────────────────────────┤
│ [Camera Preview Area]            │ ← Hidden initially
├─────────────────────────────────┤
│ [History Table]                  │ ← Riwayat bulanan
└─────────────────────────────────┘
```

**Pertanyaan:**

1️⃣ **Placement: Dimana halaman ini diakses?**
   - Option A: Menu sidebar baru "Absensi Saya"
   - Option B: Di dashboard guru (quick access card)
   - Option C: Both (sidebar + dashboard widget)
   
   **❓ Pilihan Anda: A, B, atau C?**

2️⃣ **Mobile First?**
   - ❓ Kebanyakan guru pakai HP atau desktop?
   - ❓ Perlu dedicated mobile layout seperti `absensi/create_mobile.php`?

**Rekomendasi:** Option C + Mobile First
- Dashboard widget untuk quick access
- Sidebar menu untuk full view + history
- Responsive design dengan mobile-first approach

---

### **B. Camera UI Flow**

**Current Plan:**
1. Click tombol "Datang"
2. Modal muncul dengan camera preview
3. Click "Ambil Foto"
4. Preview foto + options: Ulangi/Kirim

**Alternative Flow (Simplified):**
1. Click tombol "Datang"
2. Direct camera access (full screen)
3. Capture → Auto submit (1-click)

**❓ Pilihan: Detailed Flow atau Simplified Flow?**

**Rekomendasi:** Detailed Flow (lebih aman, user bisa review foto sebelum submit)

---

### **C. Real-time Updates**

**Pertanyaan:**

1️⃣ **Admin Dashboard: Live Monitoring?**
   - ❓ Perlu auto-refresh setiap X detik?
   - ❓ WebSocket real-time atau AJAX polling?
   - ❓ Notifikasi badge di navbar?

**Rekomendasi:**
- AJAX auto-refresh setiap 30 detik (simple, no WebSocket complexity)
- Badge counter di navbar: "5 guru belum absen"
- Toast notification saat ada check-in baru

---

## 🔐 **3. SECURITY & VALIDATION - Yang Perlu Didiskusikan**

### **A. Anti-Fraud Measures**

**Current Safeguards:**
1. ✅ Wajib foto selfie
2. ✅ GPS location (optional)
3. ✅ Device info tracking
4. ✅ Timestamp server-side (tidak bisa manipulasi client time)

**Additional Measures?**

1️⃣ **Face Recognition?**
   - ❓ Integrate face detection API? (misal: Face-API.js, AWS Rekognition)
   - ❓ Match selfie dengan foto profil guru?
   - ⚠️ Complex implementation, butuh library eksternal

2️⃣ **IP Whitelist?**
   - ❓ Hanya allow absen dari IP sekolah?
   - ⚠️ Ribet untuk guru yang piket lapangan

3️⃣ **Duplicate Detection?**
   - ✅ Already handled by UNIQUE constraint (guru_id + tanggal)

**Rekomendasi:** Current safeguards sudah cukup untuk fase 1. Face recognition bisa jadi fase 2.

---

### **B. File Upload Limits**

**Current Plan:**
- Max upload: 5MB
- Compress to: 500KB
- Format: JPG, JPEG, PNG

**Pertanyaan:**

1️⃣ **Apakah 500KB sudah cukup untuk quality?**
   - Profile photo: 800x800px @ 85% quality ≈ 150-300 KB ✅
   - Selfie absensi: 800x800px @ 85% quality ≈ 150-300 KB ✅
   
   **Rekomendasi:** 500KB cukup, bisa turun ke 300KB jika perlu save storage

2️⃣ **Storage Cleanup?**
   - ❓ Berapa lama foto disimpan? 1 tahun? 2 tahun?
   - ❓ Auto-delete foto lama via cron job?
   - ❓ Archive to cloud storage (Google Drive, AWS S3)?

**Rekomendasi:**
```php
// Cleanup policy
- Keep current year: Full resolution (500KB)
- Archive last year: Compressed (200KB) 
- Delete > 2 years: Optional (tergantung kebijakan sekolah)
```

---

## 📊 **4. REPORTING & ANALYTICS - Yang Perlu Didiskusikan**

### **A. Laporan Admin**

**Current Plan:**
- Filter by date range, guru, status
- Export to Excel
- Summary statistics

**Additional Reports?**

1️⃣ **Rekap Bulanan per Guru**
   - Total hadir: X hari
   - Total terlambat: Y hari
   - Total alpha: Z hari
   - Persentase kehadiran: 95%
   - Trend chart (line graph)

2️⃣ **Perbandingan Antar Guru**
   - Ranking kehadiran terbaik
   - Guru dengan keterlambatan terbanyak (untuk coaching)

3️⃣ **Rekap Keterlambatan**
   - Rata-rata jam datang
   - Pola keterlambatan (hari apa paling sering telat?)
   - Heatmap kehadiran

**❓ Laporan mana yang prioritas? (pilih top 3)**

**Rekomendasi:** 
1. Rekap Bulanan per Guru (essential)
2. Export Excel (essential)
3. Perbandingan Antar Guru (nice to have)

---

### **B. Export Format**

**Excel Export - Columns:**
```
| No | NIP | Nama Guru | Tanggal | Jam Datang | Jam Pulang | Total Jam | Status | Keterangan |
```

**Pertanyaan:**

1️⃣ **Include foto dalam Excel?**
   - ❌ No (file jadi besar)
   - ✅ Yes (add URL link ke foto)
   - ⚠️ Optional (checkbox "Include Photos")

**Rekomendasi:** URL link only (Excel tetap ringan, foto bisa diakses via link)

2️⃣ **PDF Export?**
   - ❓ Perlu PDF selain Excel?
   - ❓ Format: Official report dengan header/footer sekolah?

**Rekomendasi:** Fase 1 → Excel only. Fase 2 → Add PDF with official template.

---

## ⚡ **5. PERFORMANCE & SCALABILITY**

### **A. Expected Load**

**Assumptions:**
- Total guru: 50-100 orang
- Check-in bersamaan: 8-10 guru (jam 07:00-07:30)
- Photo size: 500KB per photo
- Daily storage: 50 guru × 2 photos × 500KB = 50 MB/day
- Monthly storage: 50 MB × 22 hari = ~1.1 GB/month

**Pertanyaan:**

1️⃣ **Server Specs?**
   - ❓ Berapa RAM & Storage server saat ini?
   - ❓ Perlu upgrade storage untuk foto?

2️⃣ **Database Indexing?**
   - ✅ Already planned: INDEX on tanggal, status
   - ✅ UNIQUE constraint on (guru_id, tanggal)

**Rekomendasi:** Current specs should be fine. Monitor after 1 month deployment.

---

### **B. Caching Strategy**

**Pertanyaan:**

1️⃣ **Cache Today's Attendance?**
   - Reduce DB queries untuk "Check current status"
   - Cache duration: 5 minutes
   - Invalidate on check-in/check-out

**Rekomendasi:**
```php
// Use CodeIgniter Cache
$cache = \Config\Services::cache();
$key = "absensi_guru_today_{$guruId}";

if (!$data = $cache->get($key)) {
    $data = $this->absensiGuruModel->getTodayAttendance($guruId);
    $cache->save($key, $data, 300); // 5 minutes
}
```

---

## 🚀 **6. DEPLOYMENT STRATEGY**

### **A. Rollout Plan**

**Option 1: Big Bang (All at Once)**
- Deploy ke semua guru sekaligus
- ⚠️ High risk jika ada bug

**Option 2: Phased Rollout (Recommended)**
- Week 1: Pilot dengan 5-10 guru (early adopters)
- Week 2: Expand to 50% guru
- Week 3: Full rollout
- ✅ Lower risk, bisa fix bug sebelum full launch

**❓ Pilihan: Option 1 atau 2?**

**Rekomendasi:** Option 2 (Phased Rollout)

---

### **B. Training & Documentation**

**User Training:**
- [ ] Video tutorial (2-3 menit) cara check-in/out
- [ ] Printed quick guide (1 halaman)
- [ ] Demo session untuk guru (30 menit)
- [ ] FAQ document

**❓ Siapa yang handle training? IT team atau HR?**

---

### **C. Fallback Plan**

**Jika sistem down:**

**Plan A:** Manual attendance (Excel spreadsheet backup)
**Plan B:** SMS-based check-in (send SMS ke nomor khusus)
**Plan C:** Google Form backup

**❓ Pilihan fallback: A, B, atau C?**

**Rekomendasi:** Plan A (Excel backup) - paling simple

---

## 🔔 **7. NOTIFICATION SYSTEM - Detailed Specs**

### **A. Notification Events**

**Current Plan:**
- Guru check-in → Notify admin
- Guru check-out → Notify admin
- Guru terlambat → Notify admin
- Guru belum check-in jam 12:00 → Notify admin & guru

**Pertanyaan:**

1️⃣ **Notification Method?**
   - Email only
   - WhatsApp only
   - Both
   - In-app notification only

**❓ Pilihan: Email, WhatsApp, Both, atau In-app?**

**Rekomendasi:** Email (fase 1) + In-app notification. WhatsApp fase 2.

---

### **B. Email Templates**

**Email to Admin - Guru Check-in:**
```
Subject: ✅ [Guru] John Doe telah check-in

Halo Admin,

Guru berikut telah melakukan check-in:
- Nama: John Doe
- NIP: 123456
- Waktu: 07:15 WIB
- Status: Hadir (Tepat Waktu)

Lihat detail: [Link to Admin Dashboard]
```

**Email to Guru - Reminder:**
```
Subject: 🔔 Reminder: Belum Check-in Hari Ini

Halo Pak/Bu John Doe,

Anda belum melakukan check-in hari ini.
Tanggal: 30 Januari 2026
Waktu saat ini: 12:00 WIB

Silakan check-in melalui: [Link]

Terima kasih.
```

**❓ Template sudah OK atau perlu revisi?**

---

## 📝 **8. ADDITIONAL FEATURES - Nice to Have**

### **A. Leave Request System**

**Feature:** Guru bisa submit izin/sakit via sistem

**Workflow:**
1. Guru: Submit request (tanggal, jenis, alasan, upload dokumen)
2. Admin: Review request
3. Admin: Approve/Reject
4. System: Auto-set status absensi jika approved

**❓ Implementasi di fase 1 atau fase 2?**

**Rekomendasi:** Fase 2 (fokus fase 1 ke core features dulu)

---

### **B. Overtime Tracking**

**Feature:** Track jam lembur guru

**Fields:**
- Jam pulang normal: 16:00
- Jam pulang actual: 19:00
- Overtime hours: 3 jam
- Overtime reason: "Rapat koordinasi"

**❓ Diperlukan atau tidak?**

**Rekomendasi:** Fase 2 (jika diperlukan untuk payroll)

---

### **C. Attendance Calendar View**

**Feature:** Calendar visualization seperti heatmap

**Example:**
```
January 2026
S  M  T  W  T  F  S
            1  2  3
4  5  6  7  8  9 10
🟢 🟢 🔴 🟢 🟡 🟢 -
```

🟢 Hadir | 🟡 Terlambat | 🔴 Alpha | - Weekend

**❓ Perlu atau tidak?**

**Rekomendasi:** Phase 2 (nice to have for visualization)

---

## ✅ **DECISION CHECKLIST - Tolong Dijawab**

Silakan review dan jawab pertanyaan berikut:

### **1. Business Rules**
- [ ] Jam masuk sekolah: **___:___ WIB** (default: 07:30)
- [ ] Tolerance keterlambatan: **___ menit** (default: 0 menit)
- [ ] Auto-alpha jam: **___:___ WIB** (default: 12:00)
- [ ] Minimum jam kerja: **___ jam** (default: 8 jam)

### **2. GPS Policy**
- [ ] Mandatory (strict, must have GPS)
- [ ] Optional with warning (recommended)
- [ ] Disabled

### **3. UI/UX**
- [ ] Menu placement: Sidebar / Dashboard / Both
- [ ] Camera flow: Detailed / Simplified
- [ ] Auto-refresh interval: **___ detik** (default: 30)

### **4. Security**
- [ ] Face recognition: Fase 1 / Fase 2 / Not needed
- [ ] IP whitelist: Yes / No
- [ ] Photo retention: **___ tahun** (default: 2 tahun)

### **5. Reporting**
- [ ] Priority reports (pilih 3):
  - [ ] Rekap bulanan per guru
  - [ ] Perbandingan antar guru
  - [ ] Rekap keterlambatan
  - [ ] Heatmap kehadiran
  - [ ] Export Excel
  - [ ] Export PDF

### **6. Notifications**
- [ ] Method: Email / WhatsApp / Both / In-app
- [ ] Email templates: OK / Need revision

### **7. Deployment**
- [ ] Strategy: Big Bang / Phased Rollout
- [ ] Pilot group: **___ guru** (recommended: 5-10)
- [ ] Training: Video / Printed guide / Demo session / All

### **8. Phase 2 Features** (Optional)
- [ ] Leave request system: Yes / No / Maybe
- [ ] Overtime tracking: Yes / No / Maybe
- [ ] Calendar view: Yes / No / Maybe

---

## 🎯 **RECOMMENDED IMPLEMENTATION ORDER**

Setelah decisions dibuat, urutan implementasi:

### **Sprint 1 (Day 1-2): Core Foundation**
1. Database migration
2. Model with basic CRUD
3. Guru controller (check-in/out logic)
4. Basic view (no camera yet, upload file manual)

### **Sprint 2 (Day 3-4): Camera Integration**
5. JavaScript camera handler
6. Modal UI with live preview
7. Image optimization integration
8. Testing on multiple devices

### **Sprint 3 (Day 5): Admin Module**
9. Admin controller (monitoring)
10. Admin dashboard view
11. Laporan view with filters
12. Excel export

### **Sprint 4 (Day 6): Polish & Optional Features**
13. GPS validation (if enabled)
14. Email notifications (if enabled)
15. UI/UX improvements
16. Bug fixes

### **Sprint 5 (Day 7): Testing & Documentation**
17. Full testing checklist
18. User documentation
19. Admin guide
20. Deployment preparation

---

## 📞 **NEXT STEPS**

1. **Review this document** with team/client
2. **Answer Decision Checklist** (tolong diisi)
3. **Schedule kickoff meeting** (30-60 menit)
4. **Get approval** untuk mulai development
5. **Start Sprint 1** 🚀

---

**Document Version:** 1.0  
**Last Updated:** 2026-01-30  
**Status:** Waiting for Client Decisions  
**Estimated Implementation:** 7 days (after decisions)
