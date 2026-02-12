# 🧪 Absensi Guru - Quick Testing Guide

**Test Phase:** Smoke Testing (10 Critical Tests)  
**Estimated Time:** 30 minutes  
**Environment:** Local/Staging Only

---

## ⚡ Quick Start Testing

### Prerequisites
1. ✅ Backup database first: `php spark db:seed --class BackupSeeder` or manual backup
2. ✅ Have 3 test accounts ready: Admin, Guru, Wakakur
3. ✅ Use Chrome browser (incognito mode recommended)
4. ✅ Open browser DevTools (F12) - Console tab
5. ✅ Have test image file ready (< 5MB)

---

## 📋 SMOKE TEST CHECKLIST (10 Tests)

### ✅ Test S1: Database Tables
**Time:** 2 minutes

Open MySQL/phpMyAdmin and run:
```sql
DESCRIBE absensi_guru;
DESCRIBE izin_guru;
```

**Expected:**
- ✅ `absensi_guru` has 22 columns
- ✅ `izin_guru` has 12 columns

**Result:** [ ] PASS [ ] FAIL

---

### ✅ Test S2: Guru Access
**Time:** 2 minutes

1. Login as **Guru** user
2. Check sidebar - Look for "Absensi Guru" menu
3. Click menu or go to: `http://localhost:8080/guru/absensi-guru`

**Expected:**
- ✅ Page loads without errors
- ✅ See "Absensi Guru Hari Ini" header
- ✅ See today's date
- ✅ Console shows no red errors

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

### ✅ Test S3: Admin Access
**Time:** 2 minutes

1. Logout, login as **Admin** user
2. Go to: `http://localhost:8080/admin/absensi-guru`

**Expected:**
- ✅ Monitoring dashboard loads
- ✅ See summary cards (Total Guru, Sudah Check-In, etc.)
- ✅ See attendance table
- ✅ No errors in console

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

### ✅ Test S4: Wakakur Access
**Time:** 2 minutes

1. Logout, login as **Wakakur** user
2. Go to: `http://localhost:8080/wakakur/absensi-guru`

**Expected:**
- ✅ Monitoring dashboard loads (same as Admin)
- ✅ See "Auto-refresh" toggle
- ✅ See countdown timer (30, 29, 28...)

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

### ✅ Test S5: Dashboard Widget
**Time:** 2 minutes

1. Login as **Guru** user
2. Go to: `http://localhost:8080/guru/dashboard`

**Expected:**
- ✅ See "Absensi Guru Hari Ini" widget
- ✅ If no check-in today: See "Belum Check In Hari Ini"
- ✅ See "Check In Sekarang" button
- ✅ Widget is responsive (resize browser)

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

### ✅ Test S6: Check-In Modal Opens
**Time:** 2 minutes

1. From guru dashboard or absensi page
2. Click **"Check In"** button (blue button)

**Expected:**
- ✅ Modal opens (popup)
- ✅ See "Check-In" title
- ✅ See 2 tabs: "Ambil Foto" and "Upload File"
- ✅ See "Keterangan" textarea
- ✅ See "Submit Check-In" button (disabled until photo added)

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

### ✅ Test S7: Camera Interface
**Time:** 3 minutes

1. In check-in modal, click **"Ambil Foto"** tab
2. Click **"Aktifkan Kamera"** button

**Expected:**
- ✅ Browser asks for camera permission
- ✅ After allowing, webcam video stream appears
- ✅ See your face in video preview
- ✅ "Ambil Foto" button appears
- ✅ Click "Ambil Foto" - video freezes to photo
- ✅ See "Ulangi" button to retake

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

### ✅ Test S8: Basic Check-In (CRITICAL)
**Time:** 3 minutes

**Setup:** Make sure you haven't checked in today yet

1. Click "Check In" button
2. **Option A - Camera:**
   - Click "Ambil Foto" tab
   - Click "Aktifkan Kamera"
   - Click "Ambil Foto"
   - Click "Submit Check-In"
   
   **Option B - Upload:**
   - Click "Upload File" tab
   - Choose a JPG/PNG file
   - Click "Submit Check-In"

**Expected:**
- ✅ Success message appears
- ✅ Page reloads or redirects
- ✅ Dashboard now shows check-in time
- ✅ Status badge shows "Hadir" (if before 07:15) or "Terlambat" (if after 07:15)
- ✅ Check database: `SELECT * FROM absensi_guru WHERE tanggal = CURDATE();`
  - Should have 1 new record
  - `foto_check_in` should have path like: `uploads/absensi_guru/2026/02/12/check-in_guru1_HHMMSS.jpg`

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

### ✅ Test S9: Check-Out Modal Opens
**Time:** 2 minutes

**Prerequisite:** Must have checked in first (Test S8)

1. Click **"Check Out"** button (appears after check-in)

**Expected:**
- ✅ Modal opens
- ✅ See "Check-Out" title
- ✅ Same photo options (camera/upload)
- ✅ Photo is optional (can submit without photo)
- ✅ See "Keterangan Keluar" textarea

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

### ✅ Test S10: Basic Check-Out (CRITICAL)
**Time:** 3 minutes

**Prerequisite:** Must have checked in first

1. Click "Check Out" button
2. **Optional:** Add photo (camera or upload)
3. **Optional:** Add note in "Keterangan Keluar"
4. Click **"Submit Check-Out"**

**Expected:**
- ✅ Success message
- ✅ Page reloads
- ✅ Dashboard shows both check-in and check-out times
- ✅ Shows duration (e.g., "Durasi: 2.5 jam")
- ✅ If < 8 hours, may show warning or flag
- ✅ Check database: Same record now has `check_out` time and `durasi_menit`

**Result:** [ ] PASS [ ] FAIL  
**Notes:** ___________________________

---

## 📊 SMOKE TEST SUMMARY

**Tests Passed:** ___ / 10  
**Tests Failed:** ___ / 10  
**Critical Issues:** ___ (S8, S10 are critical)  
**Decision:** [ ] Proceed to Full Testing [ ] Fix Issues First

---

## 🐛 Common Issues & Solutions

### Issue: Camera doesn't work
- **Cause:** Browser permission denied
- **Fix:** Allow camera access, try different browser, or use Upload option

### Issue: "Anda sudah melakukan check-in hari ini"
- **Cause:** Already checked in today
- **Fix:** Delete today's record from database:
  ```sql
  DELETE FROM absensi_guru WHERE tanggal = CURDATE() AND guru_id = [YOUR_GURU_ID];
  ```

### Issue: Photo not showing in database
- **Cause:** Upload failed or path wrong
- **Fix:** Check `writable/uploads/absensi_guru/YYYY/MM/DD/` folder exists and has files

### Issue: 500 Internal Server Error
- **Cause:** PHP error
- **Fix:** Check `writable/logs/log-YYYY-MM-DD.log` for error details

### Issue: Route not found (404)
- **Cause:** Routes not registered
- **Fix:** Check `app/Config/Routes.php` has absensi-guru routes

---

## ✅ After Smoke Testing

### If ALL 10 Tests Pass:
✅ **EXCELLENT!** Core functionality works  
✅ Proceed to full 95-test checklist  
✅ Start with Authentication tests (8 tests)

### If ANY Critical Test Fails (S8 or S10):
❌ **STOP!** Fix critical issues first  
❌ Check server logs  
❌ Verify database migration ran  
❌ Retest after fixes

### If Minor Tests Fail:
⚠️ **NOTE:** Document issues  
⚠️ Can proceed but fix before production  
⚠️ Mark as "known issues"

---

## 📞 Need Help?

**Logs to check:**
- Browser Console (F12)
- `writable/logs/log-YYYY-MM-DD.log`
- PHP error log

**Database queries:**
```sql
-- Today's records
SELECT * FROM absensi_guru WHERE tanggal = CURDATE();

-- Check photo paths
SELECT id, guru_id, foto_check_in, foto_check_out FROM absensi_guru WHERE tanggal = CURDATE();

-- Clear test data
DELETE FROM absensi_guru WHERE tanggal = CURDATE();
```

---

**Happy Testing! 🚀**
