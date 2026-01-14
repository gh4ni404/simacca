# Debugbar Tidak Muncul di Halaman Mata Pelajaran

## 🔍 Analisis

Berdasarkan investigasi teknis:

1. ✅ **Debugbar HTML ada di response** (verified via curl test)
2. ✅ **View structure normal** (tidak ada closing tag yang salah)
3. ✅ **Tidak ada CSS conflict** (tidak ada fixed positioning)
4. ✅ **Controller clean** (tidak ada output buffer manipulation)

**Kesimpulan:** Debugbar sebenarnya di-inject ke HTML, tapi **tidak terlihat di browser**.

## 🎯 Kemungkinan Penyebab

### 1. JavaScript Error
JavaScript debugbar gagal execute karena error di halaman.

### 2. Content Security Policy (CSP) Violation
CSP memblok inline script dari debugbar.

### 3. CSS Z-Index Conflict
Element lain menutupi debugbar.

### 4. Large Data Volume
Data pagination terlalu besar, memperlambat rendering debugbar.

---

## 🔧 Troubleshooting Steps

### Step 1: Cek Browser Console

1. Buka: `http://localhost:8080/admin/mata-pelajaran`
2. Tekan **F12** (Developer Tools)
3. Klik tab **Console**
4. Lihat apakah ada:
   - ❌ **Error merah** (JavaScript error)
   - ⚠️ **Warning kuning** (CSP violation)
   - 🔴 **Failed to load resource**

**Screenshot Console Error:**
```
Example:
Refused to execute inline script because it violates CSP
Failed to load resource: debugbar.js
Uncaught ReferenceError: debugbar is not defined
```

---

### Step 2: Cek Network Tab

1. Tab **Network** di Developer Tools
2. Reload halaman (Ctrl+R)
3. Filter by: **JS** dan **CSS**
4. Cari file yang mengandung **"debugbar"**
5. Cek status:
   - ✅ **200 OK** = berhasil load
   - ❌ **404 Not Found** = file tidak ada
   - ❌ **403 Forbidden** = blocked

---

### Step 3: Cek HTML Elements

1. Tab **Elements** di Developer Tools
2. Tekan **Ctrl+F** untuk search
3. Cari: `toolbarContainer`
4. Harusnya ada:
   ```html
   <div id="toolbarContainer">
       <!-- Debugbar content -->
   </div>
   ```

5. Cek computed style:
   - Klik kanan pada `<div id="toolbarContainer">`
   - Pilih **Inspect**
   - Lihat **Styles** panel
   - Cek apakah ada:
     ```css
     display: none;  /* ❌ Ini menyembunyikan */
     visibility: hidden;  /* ❌ Ini juga */
     opacity: 0;  /* ❌ Transparan */
     z-index: -1;  /* ❌ Di belakang */
     ```

---

### Step 4: Bandingkan dengan Halaman Lain

1. Buka halaman yang debugbar-nya muncul:
   - `http://localhost:8080/admin/guru`
   - `http://localhost:8080/admin/kelas`

2. Bandingkan:
   - Console errors
   - Network requests
   - Element structure

3. Identifikasi perbedaan

---

## ✅ Solusi Berdasarkan Penyebab

### Solusi 1: Jika Ada JavaScript Error

**Penyebab:** Error di JavaScript halaman memblok debugbar script.

**Fix:**
1. Lihat error di Console
2. Perbaiki JavaScript error tersebut
3. Biasanya terkait dengan:
   - Undefined variable
   - Missing function
   - Syntax error

**Example Fix:**
```javascript
// Jika error: confirmDelete is not defined
// Pastikan function ada sebelum digunakan
function confirmDelete(id, name) {
    if (confirm(`Hapus "${name}"?`)) {
        window.location.href = `/admin/mata-pelajaran/hapus/${id}`;
    }
}
```

---

### Solusi 2: Jika CSP Violation

**Penyebab:** Content Security Policy memblok inline script debugbar.

**Fix:** Update `app/Config/ContentSecurityPolicy.php`

```php
// Tambahkan 'unsafe-inline' untuk development
public ?array $scriptSrc = [
    'self',
    'unsafe-inline',  // Allow inline scripts untuk debugbar
];
```

**Atau disable CSP sementara:**
```php
// app/Config/Filters.php
public array $globals = [
    'before' => [
        'keepalive',
        'csrf' => [...],
        // Comment out secureheaders untuk testing
        // 'secureheaders',
    ],
];
```

---

### Solusi 3: Jika Z-Index Issue

**Fix:** Force debugbar z-index via custom CSS

Tambahkan di `app/Views/templates/main_layout.php` sebelum `</head>`:

```html
<style>
    #toolbarContainer {
        z-index: 999999 !important;
        position: fixed !important;
        bottom: 0 !important;
    }
</style>
```

---

### Solusi 4: Jika Data Terlalu Besar

**Penyebab:** Pagination atau data besar memperlambat rendering.

**Fix 1:** Kurangi collectVarData

```php
// app/Config/Toolbar.php
public bool $collectVarData = false;  // Set false untuk performa
```

**Fix 2:** Kurangi maxQueries

```php
// app/Config/Toolbar.php
public int $maxQueries = 20;  // Kurangi dari 100
```

---

## 🚀 Quick Fix (Temporary)

Jika ingin cepat lihat debugbar info tanpa UI:

### Option 1: Lihat di HTML Source
1. Klik kanan → **View Page Source**
2. Scroll ke bawah
3. Lihat comment `<!-- DEBUG-BAR DATA -->`

### Option 2: Akses Debugbar History
1. Buka: `http://localhost:8080/_debugbar`
2. Pilih request yang ingin dilihat
3. Lihat detail queries, routes, etc.

### Option 3: Enable Kint Debugger
```php
// Tambahkan di controller untuk debug cepat
d($data);  // Dump dan die
```

---

## 🔍 Advanced Debugging

### Enable Debugbar Logging

Tambahkan di `app/Config/Toolbar.php`:

```php
public array $collectors = [
    Timers::class,
    Database::class,
    Logs::class,
    Views::class,
    Files::class,
    Routes::class,
    Events::class,
];
```

### Check Writable Permissions

```bash
# Pastikan writable/debugbar bisa ditulis
chmod -R 777 writable/debugbar
```

### Clear Debugbar Cache

```bash
# Hapus file cache debugbar
rm -rf writable/debugbar/*
```

---

## 📊 Comparison Table

| Halaman | Debugbar | Possible Issue |
|---------|----------|----------------|
| /admin/guru | ✅ Muncul | - |
| /admin/kelas | ✅ Muncul | - |
| /admin/mata-pelajaran | ❌ Hilang | **JS Error / CSP / Z-Index** |
| /admin/siswa | ? | Perlu dicek |

---

## 🎯 Recommended Action

1. **Buka halaman mata-pelajaran**
2. **Tekan F12 → Console**
3. **Screenshot error yang muncul**
4. **Share screenshot untuk analisis lebih lanjut**

Atau:

**Test dengan incognito mode:**
```
Ctrl+Shift+N (Chrome) atau Ctrl+Shift+P (Firefox)
Buka: http://localhost:8080/admin/mata-pelajaran
Login dan cek apakah debugbar muncul
```

Jika di incognito muncul → Issue di browser cache/extensions.  
Jika di incognito tetap hilang → Issue di code.

---

## 📝 Next Steps

Setelah menemukan error spesifik di Console:
1. Screenshot error
2. Identifikasi file dan line number
3. Fix error tersebut
4. Test ulang

---

**Last Updated:** 2026-01-14  
**Status:** Troubleshooting Guide
