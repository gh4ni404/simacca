# CSRF Error Troubleshooting Guide

## ❌ Error yang Muncul
```
CodeIgniter\Security\Exceptions\SecurityException #403
The action you requested is not allowed.
```

## 🔍 Root Cause Analysis

Dari screenshot error, masalahnya ada di:
- **File:** `SYSTEMPATH\Security\Security.php` line 262
- **Penyebab:** Token mismatch - token yang dikirim tidak cocok dengan token di server

### Kemungkinan Penyebab:

1. **Cookie tidak ter-set dengan benar**
2. **Token berubah setelah page load (karena regenerate=true)**
3. **Browser cache masih menyimpan token lama**
4. **Session/cookie conflict**

## ✅ Solusi Komprehensif

### Solusi 1: Ubah CSRF Regenerate ke FALSE (RECOMMENDED)

Karena kita menggunakan AJAX untuk checkConflict, `regenerate=true` bisa menyebabkan token berubah setelah AJAX call, sehingga submit form gagal.

**File: `app/Config/Security.php`**
```php
// Ubah dari true ke false
public bool $regenerate = false;
```

**Alasan:** 
- AJAX request bisa membuat token regenerate
- Ketika form di-submit, token sudah berbeda
- Setting ke `false` membuat token konsisten selama session

### Solusi 2: Clear Browser Cookies & Session

1. **Clear Browser:**
   - Tekan `F12` untuk buka Developer Tools
   - Tab "Application" → "Cookies" → Pilih `localhost:8080`
   - Delete semua cookies (terutama `csrf_cookie_name`)
   - Tab "Storage" → Clear semua storage

2. **Clear Server Session:**
   ```bash
   # Hapus file session
   rm writable/session/ci_session*
   ```

3. **Restart Browser** (penting!)

### Solusi 3: Restart Web Server

**Jika menggunakan PHP built-in server:**
```bash
# Stop server (Ctrl+C)
# Start ulang
php spark serve --port=8080
```

**Jika menggunakan Apache/XAMPP:**
- Restart Apache service

### Solusi 4: Tambah Logging untuk Debug

**File: `app/Controllers/Admin/JadwalController.php`**

Tambahkan di method `store()` sebelum validasi:

```php
public function store()
{
    // Debug CSRF
    log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));
    log_message('debug', 'CSRF Token from POST: ' . $this->request->getPost(csrf_token()));
    log_message('debug', 'CSRF Hash from Session: ' . csrf_hash());
    
    // Check if user is logged in and has admin role
    if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
        return redirect()->to('/login');
    }
    
    // ... rest of code
}
```

Kemudian cek log di `writable/logs/log-[date].php`

## 🎯 Langkah-Langkah Fix (IKUTI URUTAN INI)

### Step 1: Update Configuration
✅ SUDAH DILAKUKAN:
- `app/Config/Security.php`: expires = 14400
- `app/Config/Security.php`: redirect = true
- `app/Views/admin/jadwal/create.php`: getCsrfToken() added
- `app/Views/admin/jadwal/edit.php`: getCsrfToken() added

### Step 2: Change Regenerate to FALSE
```php
// app/Config/Security.php
public bool $regenerate = false;  // ← PENTING: Ubah dari true ke false!
```

### Step 3: Clear Everything
```bash
# 1. Clear cache
php spark cache:clear

# 2. Clear session
rm writable/session/ci_session*

# 3. Clear logs (optional)
rm writable/logs/*
```

### Step 4: Restart Server
```bash
# Stop & restart PHP server
php spark serve --port=8080
```

### Step 5: Clear Browser
1. Buka `localhost:8080`
2. Tekan `F12`
3. Application → Storage → Clear All
4. Application → Cookies → Delete All
5. Close & Reopen Browser (PENTING!)

### Step 6: Test Again
1. Buka fresh: `http://localhost:8080/login`
2. Login sebagai admin
3. Buka: `http://localhost:8080/admin/jadwal/tambah`
4. Isi form
5. Submit

## 🔧 Alternative Solution: Disable CSRF for Testing

**HANYA UNTUK TESTING - JANGAN DI PRODUCTION!**

```php
// app/Config/Filters.php
public array $globals = [
    'before' => [
        'keepalive',
        // 'csrf' => [  // ← Comment out untuk test
        //     'except' => [
        //         'api/*',
        //         'forgot-password/process',
        //         'reset-password/process',
        //         'files/*'
        //     ]
        // ],
    ],
    // ...
];
```

Jika berhasil submit tanpa CSRF filter, berarti masalahnya memang di CSRF token.

## 📊 Diagnosis Checklist

Cek hal-hal berikut:

- [ ] File `app/Config/Security.php` sudah diubah dengan benar
- [ ] Server sudah di-restart setelah perubahan config
- [ ] Browser cookies sudah di-clear
- [ ] Session files sudah dihapus
- [ ] Tidak ada error di console browser (F12)
- [ ] CSRF cookie ter-set di browser (cek di Application → Cookies)
- [ ] Form memiliki hidden input `csrf_test_name`
- [ ] Value dari hidden input tidak kosong

## 🐛 Debug Steps

### 1. Cek Cookie di Browser
```
F12 → Application → Cookies → localhost:8080
```
Harus ada: `csrf_cookie_name` dengan value terisi

### 2. Cek Hidden Input di Form
```
F12 → Elements → Cari <input type="hidden" name="csrf_test_name"
```
Value harus terisi (32 karakter hash)

### 3. Cek Network Request
```
F12 → Network → Submit form → Lihat payload
```
Harus ada: `csrf_test_name` di Form Data

### 4. Cek Console Error
```
F12 → Console
```
Tidak boleh ada error JavaScript

## ✨ Expected Result After Fix

Setelah mengikuti semua langkah:
- ✅ Form bisa di-submit tanpa error
- ✅ AJAX checkConflict bekerja
- ✅ Redirect ke list jadwal dengan success message
- ✅ Tidak ada "action you requested is not allowed"

## 📞 Jika Masih Error

Coba langkah berikut:

1. **Gunakan Browser Incognito/Private**
   - Untuk memastikan tidak ada cookies lama

2. **Cek file .htaccess**
   - Pastikan tidak ada rule yang interfere dengan cookies

3. **Cek Cookie Domain Setting**
   - `app/Config/Cookie.php` - pastikan domain kosong untuk localhost

4. **Test dengan curl** (seperti test otomatis yang sudah kita lakukan)
   - Jika curl berhasil, berarti masalah di browser

5. **Cek PHP Session Configuration**
   - Pastikan `writable/session` writable
   - Cek `app/Config/Session.php`

## 🎓 Penjelasan Teknis

### Kenapa regenerate=true bermasalah dengan AJAX?

1. User buka form → Token A di-generate
2. User ubah field → AJAX checkConflict dipanggil dengan Token A
3. Karena regenerate=true, server generate Token B baru
4. User submit form dengan Token A (yang masih di form)
5. Server expect Token B → ERROR!

### Solusi: regenerate=false

- Token tetap sama selama satu session
- AJAX tidak mengubah token
- Form submit dengan token yang sama saat page load
- ✅ Berhasil!

---

**Last Updated:** 2026-01-14  
**Status:** Pending User Test
