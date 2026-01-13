# 🚀 Quick Fix Instructions - CSRF Error

## ✅ Fix Sudah Diterapkan!

Perubahan telah dilakukan pada:
1. ✅ `app/Config/Security.php`
2. ✅ `app/Views/admin/jadwal/create.php`
3. ✅ `app/Views/admin/jadwal/edit.php`

---

## 📋 LANGKAH WAJIB - Ikuti Urutan Ini!

### ⚠️ Step 1: RESTART WEB SERVER

**Jika menggunakan PHP built-in server:**
```bash
# 1. Stop server (tekan Ctrl+C di terminal server)
# 2. Start ulang:
php spark serve --port=8080
```

**Jika menggunakan Apache/XAMPP:**
1. Buka XAMPP Control Panel
2. Klik "Stop" pada Apache
3. Klik "Start" pada Apache

**✅ Server sudah restart? Lanjut ke step 2!**

---

### 🧹 Step 2: CLEAR BROWSER (SANGAT PENTING!)

1. **Buka Browser** (Chrome/Edge/Firefox)

2. **Tekan F12** untuk buka Developer Tools

3. **Clear Storage:**
   - Klik tab **"Application"** (Chrome) atau **"Storage"** (Firefox)
   - Klik **"Storage"** di sidebar kiri
   - Klik **"Clear Site Data"**
   - ✅ Pastikan semua checkbox tercentang
   - Klik **"Clear site data"**

4. **Clear Cookies:**
   - Masih di tab Application
   - Klik **"Cookies"** di sidebar kiri
   - Klik **"http://localhost:8080"**
   - Klik kanan → **"Clear"** atau tekan Delete pada setiap cookie
   - ✅ Pastikan `csrf_cookie_name` terhapus

5. **CLOSE Browser Completely**
   - Tutup SEMUA tab dan window browser
   - Tunggu 5 detik
   - Buka browser baru

**✅ Browser sudah di-clear dan di-restart? Lanjut ke step 3!**

---

### 🧪 Step 3: TEST FORM

1. **Buka fresh browser ke:**
   ```
   http://localhost:8080/login
   ```

2. **Login sebagai admin**
   - Username: admin
   - Password: admin123

3. **Buka form tambah jadwal:**
   ```
   http://localhost:8080/admin/jadwal/tambah
   ```

4. **Isi form dengan data:**
   - Guru: Pilih salah satu
   - Mata Pelajaran: Pilih salah satu
   - Kelas: Pilih salah satu
   - Hari: Senin
   - Jam Mulai: 07:00
   - Jam Selesai: 08:30
   - Semester: Ganjil
   - Tahun Ajaran: 2025/2026

5. **Klik "Simpan Jadwal"**

---

## ✅ Expected Result (Hasil yang Diharapkan)

### Jika Berhasil:
- ✅ Form ter-submit tanpa error
- ✅ Redirect ke halaman list jadwal (`/admin/jadwal`)
- ✅ Muncul pesan sukses hijau: "Jadwal baru siap! Let's teach"
- ✅ Data jadwal muncul di tabel

### Jika Ada Conflict:
- ⚠️ Tetap di halaman form
- ⚠️ Muncul alert: "Guru bentrok nih!" atau "Kelas udah ada jadwal"
- ✅ **Ini berarti CSRF sudah fix!** (error karena jadwal bentrok, bukan CSRF)
- 💡 Ubah jam atau hari untuk menghindari conflict, lalu submit lagi

---

## ❌ Jika Masih Error "Action not allowed"

### Checklist Debugging:

1. **Cek apakah server sudah restart?**
   - [ ] Server PHP/Apache sudah direstart
   - [ ] Tidak ada error saat restart

2. **Cek apakah browser sudah di-clear?**
   - [ ] Cookies sudah dihapus (cek di F12 → Application → Cookies)
   - [ ] Storage sudah di-clear
   - [ ] Browser sudah di-close dan dibuka ulang

3. **Cek CSRF token di form:**
   - Buka F12 → Elements
   - Cari `<input type="hidden" name="csrf_test_name"`
   - Value harus terisi (32 karakter)
   - [ ] Token terisi dengan benar

4. **Cek CSRF cookie:**
   - F12 → Application → Cookies → localhost:8080
   - Harus ada cookie `csrf_cookie_name`
   - [ ] Cookie ada dan value terisi

5. **Cek Console Error:**
   - F12 → Console
   - [ ] Tidak ada error JavaScript

### Jika Semua Checklist ✅ Tapi Masih Error:

Coba **Incognito/Private Mode:**
```
1. Buka browser Incognito (Ctrl+Shift+N)
2. Buka http://localhost:8080/login
3. Test form lagi
```

Jika di Incognito berhasil → Masalah di browser cache.  
Solusi: Clear browser data completely via Settings.

---

## 📞 Troubleshooting Lanjutan

Baca file ini untuk troubleshooting detail:
- 📄 **`CSRF_TROUBLESHOOTING_GUIDE.md`** - Panduan lengkap
- 📄 **`BUGFIX_CSRF_JADWAL.md`** - Penjelasan teknis

---

## 🎯 Summary Perubahan

| Setting | Before | After | Alasan |
|---------|--------|-------|--------|
| `expires` | 7200s (2h) | 14400s (4h) | Lebih fleksibel untuk session panjang |
| `regenerate` | true | **false** | ⚠️ Fix untuk AJAX compatibility |
| `redirect` | conditional | true | Error handling konsisten |
| JS Function | Static token | Dynamic `getCsrfToken()` | Token selalu up-to-date |

---

## ✨ Setelah Berhasil

Setelah form berhasil submit, Anda bisa:
1. ✅ Test form edit jadwal juga
2. ✅ Test dengan berbagai kombinasi data
3. ✅ Test schedule conflict detection
4. ✅ Lanjut ke fitur lain

---

**Good luck! 🍀**

Jika masih ada masalah, screenshot error dan hubungi developer.
