# Day 3 Integration Testing Checklist

## Test Date: 2026-02-12
## Tester: Development Team
## Features: Absensi Guru System + UI Enhancements

---

## 🎯 Test Scope

This integration test validates the complete Absensi Guru feature set implemented on Day 3, including:
- Navigation menus
- Dashboard widgets
- Izin Guru workflow (Guru & Wakakur)
- Auto-refresh functionality
- Camera interface
- Mobile responsiveness

---

## ✅ Test Cases

### **1. Navigation & Menu Testing**

#### 1.1 Admin Navigation
- [x] Login as Admin
- [x] Verify "Absensi Guru" menu appears in sidebar
- [x] Click menu item - redirects to `/admin/absensi-guru`
- [x] Verify page loads correctly

#### 1.2 Guru Navigation
- [x] Login as Guru
- [x] Verify "Absensi Guru" menu appears in sidebar
- [x] Click menu item - redirects to `/guru/absensi-guru`
- [x] Verify page loads correctly

#### 1.3 Wakakur Navigation
- [x] Login as Wakakur
- [x] Verify "Absensi Guru" menu appears in sidebar
- [x] Click menu item - redirects to `/wakakur/absensi-guru`
- [x] Verify page loads correctly

**Status:** ✅ 1/3 Completed (Admin tested successfully)  
**Notes:** 
- Admin navigation working perfectly
- Page loads with correct data
- Fixed bugs: guru.nama → guru.nama_lengkap, success() parameter order

---

### **2. Guru Dashboard Widget Testing**

#### 2.1 Widget Display (No Check-in)
- [x] Login as Guru
- [x] Navigate to dashboard
- [x] Verify "Absensi Guru Hari Ini" widget appears
- [x] Verify shows "Belum Check In Hari Ini" state
- [x] Verify "Check In Sekarang" button is present

#### 2.2 Widget Display (After Check-in)
- [ ] After check-in, return to dashboard
- [ ] Verify widget shows check-in time
- [ ] Verify status badge (Hadir/Terlambat)
- [ ] Verify "Check Out Sekarang" button appears

#### 2.3 Widget Display (After Check-out)
- [ ] After check-out, return to dashboard
- [ ] Verify widget shows check-out time
- [ ] Verify duration calculation is correct
- [ ] Verify "Lihat Riwayat" button appears

**Status:** ⏳ Pending  
**Notes:**

---

### **3. Camera Interface Testing**

#### 3.1 Camera Permission
- [ ] Open check-in modal
- [ ] Click "Aktifkan Kamera"
- [ ] Browser requests camera permission
- [ ] Grant permission - camera stream starts
- [ ] Verify video preview displays

#### 3.2 Photo Capture
- [ ] Click "Ambil Foto" button
- [ ] Video stops, canvas shows captured image
- [ ] Verify photo preview is clear
- [ ] Verify "Ulangi" button appears

#### 3.3 Retake Functionality
- [ ] Click "Ulangi" button
- [ ] Camera restarts
- [ ] Can capture new photo
- [ ] Previous photo is discarded

#### 3.4 Camera/Upload Toggle
- [ ] Switch to "Upload File" mode
- [ ] Verify camera stops
- [ ] Verify file input appears
- [ ] Switch back to "Ambil Foto"
- [ ] Verify camera interface returns

#### 3.5 Modal Close Camera Cleanup
- [ ] Activate camera
- [ ] Close modal without capturing
- [ ] Reopen modal
- [ ] Verify camera is in reset state
- [ ] Camera light should turn off when modal closes

**Status:** ⏳ Pending  
**Notes:**

---

### **4. Check-In Workflow Testing**

#### 4.1 Check-In with Camera
- [ ] Login as Guru (not checked in today)
- [ ] Navigate to `/guru/absensi-guru`
- [ ] Click "Check-In Sekarang"
- [ ] Modal opens
- [ ] Activate camera and capture photo
- [ ] Add keterangan (optional)
- [ ] Submit form
- [ ] Verify success message
- [ ] Verify redirected to index with updated status

#### 4.2 Check-In with File Upload
- [ ] Use different guru account
- [ ] Switch to "Upload File" mode
- [ ] Select image file from device
- [ ] Submit form
- [ ] Verify file uploads successfully

#### 4.3 Check-In Validation
- [ ] Try to submit without photo
- [ ] Verify error message: "Silakan ambil foto selfie terlebih dahulu"
- [ ] Add photo and resubmit
- [ ] Verify submission succeeds

#### 4.4 GPS Location Capture
- [ ] During check-in, grant location permission
- [ ] Submit form
- [ ] Check database - verify latitude/longitude saved
- [ ] If location denied, check-in should still work

#### 4.5 Status Determination
- [ ] Check-in before 07:15 - verify status "hadir"
- [ ] Check-in after 07:15 - verify status "terlambat"

**Status:** ⏳ Pending  
**Notes:**

---

### **5. Check-Out Workflow Testing**

#### 5.1 Check-Out with Photo
- [ ] After check-in, click "Check-Out Sekarang"
- [ ] Capture photo using camera
- [ ] Add keterangan (optional)
- [ ] Submit form
- [ ] Verify check-out time recorded

#### 5.2 Check-Out without Photo
- [ ] Use account that checked in
- [ ] Open check-out modal
- [ ] Submit without taking photo
- [ ] Verify check-out succeeds (photo optional)

#### 5.3 Duration Calculation
- [ ] After check-out, verify duration_menit calculated
- [ ] Duration should be (check-out time - check-in time) in minutes
- [ ] Verify early_checkout flag if < 8 hours

**Status:** ⏳ Pending  
**Notes:**

---

### **6. Izin Guru Workflow - Guru Role**

#### 6.1 Create Izin Request
- [ ] Login as Guru
- [ ] Navigate to `/guru/izin-guru`
- [ ] Click "Ajukan Izin Baru"
- [ ] Fill form:
  - Jenis izin: Sakit
  - Tanggal mulai: Tomorrow
  - Tanggal selesai: Day after tomorrow
  - Alasan: Test izin sakit
  - Upload berkas (optional)
- [ ] Submit form
- [ ] Verify success message
- [ ] Verify appears in list with status "Pending"

#### 6.2 View Izin List
- [ ] Verify statistics cards show correct counts
- [ ] Verify submitted izin appears in table
- [ ] Verify can click "Detail" to view

#### 6.3 Delete Pending Izin
- [ ] Click "Hapus" on pending izin
- [ ] Confirm deletion
- [ ] Verify izin removed from list

**Status:** ⏳ Pending  
**Notes:**

---

### **7. Izin Guru Workflow - Wakakur Role**

#### 7.1 View Izin List
- [ ] Login as Wakakur
- [ ] Navigate to `/wakakur/izin-guru`
- [ ] Verify all guru izin requests appear
- [ ] Verify statistics cards show correct totals

#### 7.2 Filter Functionality
- [ ] Filter by status: Pending
- [ ] Filter by month/year
- [ ] Verify filtered results correct

#### 7.3 Approve Izin
- [ ] Click approve icon on pending izin
- [ ] Add catatan (optional)
- [ ] Submit approval
- [ ] Verify status changes to "Disetujui"
- [ ] Verify absensi_guru records auto-created for date range

#### 7.4 Reject Izin
- [ ] Create new izin as Guru
- [ ] As Wakakur, click reject
- [ ] Add catatan (required for rejection)
- [ ] Submit rejection
- [ ] Verify status changes to "Ditolak"
- [ ] Verify no absensi records created

**Status:** ⏳ Pending  
**Notes:**

---

### **8. Auto-Refresh Testing (Admin/Wakakur)**

#### 8.1 Admin Dashboard Auto-Refresh
- [ ] Login as Admin
- [ ] Navigate to `/admin/absensi-guru`
- [ ] Observe countdown timer (30s)
- [ ] Wait for auto-refresh
- [ ] Verify data updates without page reload
- [ ] Verify summary cards update
- [ ] Verify table updates

#### 8.2 Pause/Resume Functionality
- [ ] Click "Pause" button
- [ ] Verify countdown stops
- [ ] Button changes to "Resume"
- [ ] Click "Resume"
- [ ] Verify countdown restarts

#### 8.3 Filter Persistence
- [ ] Apply filter (e.g., status = "hadir")
- [ ] Wait for auto-refresh
- [ ] Verify filter still applied after refresh

#### 8.4 Wakakur Dashboard Auto-Refresh
- [ ] Login as Wakakur
- [ ] Navigate to `/wakakur/absensi-guru`
- [ ] Verify same auto-refresh functionality works

**Status:** ⏳ Pending  
**Notes:**

---

### **9. Mobile Responsiveness Testing**

#### 9.1 Modal Display (Mobile)
- [ ] Open browser DevTools
- [ ] Switch to mobile view (iPhone/Android)
- [ ] Open check-in modal
- [ ] Verify modal is fullscreen
- [ ] Verify buttons stack vertically
- [ ] Verify text is abbreviated appropriately

#### 9.2 Camera Interface (Mobile)
- [ ] Activate camera on mobile view
- [ ] Verify camera container height appropriate (200px)
- [ ] Verify buttons are touch-friendly
- [ ] Capture photo
- [ ] Verify image quality acceptable

#### 9.3 Touch Interactions
- [ ] Test button press feedback
- [ ] Verify buttons scale on active state
- [ ] Test toggle buttons (camera/upload)
- [ ] Verify all interactive elements responsive

#### 9.4 Responsive Breakpoints
- [ ] Test at 320px (small phone)
- [ ] Test at 375px (iPhone)
- [ ] Test at 768px (tablet)
- [ ] Test at 1024px (desktop)
- [ ] Verify layout adapts appropriately

**Status:** ⏳ Pending  
**Notes:**

---

### **10. Error Handling & Edge Cases**

#### 10.1 Camera Errors
- [ ] Deny camera permission
- [ ] Verify error message displays
- [ ] Can still use file upload as fallback

#### 10.2 Network Errors
- [ ] Simulate slow network
- [ ] Verify loading states show
- [ ] Verify timeout handling

#### 10.3 Duplicate Check-In Prevention
- [ ] After check-in, try to check-in again
- [ ] Verify error: "Sudah melakukan check-in hari ini"

#### 10.4 Duplicate Check-Out Prevention
- [ ] After check-out, try to check-out again
- [ ] Verify error: "Sudah melakukan check-out hari ini"

#### 10.5 Large File Upload
- [ ] Try to upload file > 2MB
- [ ] Verify error message

#### 10.6 Invalid File Type
- [ ] Try to upload PDF/document
- [ ] Verify error message (images only)

**Status:** ⏳ Pending  
**Notes:**

---

### **11. Database Validation**

#### 11.1 Absensi Guru Records
- [ ] Check `absensi_guru` table
- [ ] Verify foto_check_in path saved correctly
- [ ] Verify foto_check_out path saved (if provided)
- [ ] Verify latitude/longitude saved
- [ ] Verify status calculated correctly
- [ ] Verify durasi_menit calculated on check-out

#### 11.2 Izin Guru Records
- [ ] Check `izin_guru` table
- [ ] Verify berkas path saved
- [ ] Verify approval data (disetujui_oleh, tanggal_disetujui)
- [ ] Verify catatan_persetujuan saved

#### 11.3 File Storage
- [ ] Navigate to `writable/uploads/absensi_guru/`
- [ ] Verify photos are saved
- [ ] Verify filename format: `guru_{id}_{type}_{timestamp}.jpg`
- [ ] Navigate to `writable/uploads/izin_guru/`
- [ ] Verify uploaded documents saved

**Status:** ⏳ Pending  
**Notes:**

---

### **12. Cross-Browser Testing**

#### 12.1 Chrome/Edge
- [ ] All camera features work
- [ ] All modals display correctly
- [ ] Auto-refresh works

#### 12.2 Firefox
- [ ] Camera permission flow works
- [ ] Photo capture works
- [ ] File upload works

#### 12.3 Safari (if available)
- [ ] Camera API compatibility
- [ ] Modal display
- [ ] Touch interactions

#### 12.4 Mobile Browsers
- [ ] Chrome Mobile
- [ ] Safari iOS
- [ ] Samsung Internet

**Status:** ⏳ Pending  
**Notes:**

---

## 📋 Summary

**Total Test Cases:** 50+  
**Passed:** 4  
**Failed:** 0  
**Fixed:** 2 critical bugs  
**Blocked:** 0  
**Pending:** 46+  
**Last Updated:** 2026-02-12 20:15 WIB  

---

## 🐛 Issues Found

| # | Issue | Severity | Status | Notes |
|---|-------|----------|--------|-------|
| 1 | Database field error: `guru.nama` not found | High | ✅ Fixed | Changed to `guru.nama_lengkap` in 7 locations (AbsensiGuruService.php, Admin/AbsensiGuruController.php, Wakakur/AbsensiGuruController.php) |
| 2 | TypeError in BaseService::success() | High | ✅ Fixed | Parameter order was reversed. Changed from `success($message, $data)` to `success($data, $message)` in 7 locations |
| 3 | | | | |

---

## ✅ Sign-Off

- [ ] All critical test cases passed
- [ ] No blocking issues
- [ ] Ready for deployment

**Tested By:** _________________  
**Date:** _________________  
**Signature:** _________________

---

## 📝 Notes & Observations

### Testing Session 1 (2026-02-12 20:00-20:15 WIB)

**Environment:**
- Browser: Chrome
- Server: localhost:8080
- PHP: 8.2.12
- CodeIgniter: 4.6.4
- Database: MySQL

**Bugs Found & Fixed:**
1. **Database Column Mismatch**: The `guru` table uses `nama_lengkap` but queries were using `nama`. Fixed in all Service and Controller files.
2. **BaseService Method Signature**: The `success()` method expects `($data, $message)` but was being called with reversed parameters. Fixed in AbsensiGuruService.php.

**What's Working:**
- ✅ Admin can access Absensi Guru page
- ✅ Menu navigation working correctly
- ✅ Page loads without errors
- ✅ Summary statistics display correctly (even with no data)

**Next Steps:**
- Test Guru role navigation and check-in functionality
- Test camera interface
- Test Wakakur role navigation and monitoring
- Test auto-refresh feature
- Test mobile responsiveness

**Performance Notes:**
- Page load time: Fast (<1s)
- No console errors
- Database queries executing correctly
