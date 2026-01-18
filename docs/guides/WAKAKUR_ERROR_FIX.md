# Perbaikan Error "Unknown column" di Wakakur Controllers

## 🐛 Error yang Ditemukan

```
Error: Unknown column 'guru_id' in 'where clause'
Error: Unknown column 'kelas_id' in 'where clause'
Error: Unknown column 'mapel_id' in 'where clause'
```

---

## 🔍 Root Cause Analysis

### Struktur Database Absensi

Tabel `absensi` **TIDAK** memiliki kolom-kolom berikut secara langsung:
- ❌ `guru_id`
- ❌ `kelas_id`
- ❌ `mapel_id`

**Struktur yang benar**:
```sql
CREATE TABLE absensi (
    id INT,
    jadwal_mengajar_id INT,  -- Foreign key ke jadwal_mengajar
    tanggal DATE,
    pertemuan_ke INT,
    materi_pembelajaran TEXT,
    created_by INT,
    guru_pengganti_id INT,
    created_at DATETIME,
    unlocked_at DATETIME
);
```

### Relasi Database

```
guru
  └─> jadwal_mengajar
        ├─> guru_id
        ├─> kelas_id
        ├─> mata_pelajaran_id
        └─> absensi
              └─> jadwal_mengajar_id
                    └─> absensi_detail
```

**Kesimpulan**: Untuk mendapatkan `guru_id`, `kelas_id`, atau `mapel_id` dari absensi, harus melakukan **JOIN** dengan tabel `jadwal_mengajar`.

---

## ✅ Perbaikan yang Dilakukan

### 1. **DashboardController.php**

#### ❌ **Before** (Line 59-62):
```php
$absensiGuru = $this->absensiModel->where('guru_id', $guruId)
    ->where('tanggal >=', date('Y-m-01'))
    ->where('tanggal <=', date('Y-m-t'))
    ->countAllResults();
```

**Error**: Kolom `guru_id` tidak ada di tabel `absensi`.

#### ✅ **After** (Line 59-64):
```php
$absensiGuru = $this->absensiModel
    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
    ->where('jadwal_mengajar.guru_id', $guruId)
    ->where('absensi.tanggal >=', date('Y-m-01'))
    ->where('absensi.tanggal <=', date('Y-m-t'))
    ->countAllResults();
```

#### ❌ **Before** (Line 95-102):
```php
$recentAbsensi = $this->absensiModel
    ->select('absensi.*, kelas.nama_kelas, mata_pelajaran.nama_mapel')
    ->join('kelas', 'kelas.id = absensi.kelas_id')
    ->join('mata_pelajaran', 'mata_pelajaran.id = absensi.mapel_id')
    ->where('absensi.guru_id', $guruId)
    ->orderBy('absensi.tanggal', 'DESC')
    ->limit(5)
    ->findAll();
```

**Error**: Kolom `kelas_id`, `mapel_id`, dan `guru_id` tidak ada di tabel `absensi`.

#### ✅ **After** (Line 97-98):
```php
$recentAbsensi = $this->absensiModel->getByGuru($guruId);
$recentAbsensi = array_slice($recentAbsensi, 0, 5);
```

**Solution**: Menggunakan method `getByGuru()` yang sudah ada di `AbsensiModel` yang sudah implement JOIN dengan benar.

---

### 2. **LaporanController.php**

#### ❌ **Before** (Line 55-69):
```php
$builder = $this->absensiModel
    ->select('absensi.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, guru.nama_lengkap as nama_guru')
    ->join('kelas', 'kelas.id = absensi.kelas_id')
    ->join('mata_pelajaran', 'mata_pelajaran.id = absensi.mapel_id')
    ->join('guru', 'guru.id = absensi.guru_id')
    ->where('absensi.tanggal >=', $startDate)
    ->where('absensi.tanggal <=', $endDate);

if ($kelasId) {
    $builder->where('absensi.kelas_id', $kelasId);
}

if ($mapelId) {
    $builder->where('absensi.mapel_id', $mapelId);
}
```

**Error**: Kolom `kelas_id`, `mapel_id`, dan `guru_id` tidak ada di tabel `absensi`.

#### ✅ **After** (Line 54-70):
```php
$builder = $this->absensiModel
    ->select('absensi.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, guru.nama_lengkap as nama_guru')
    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
    ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
    ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
    ->where('absensi.tanggal >=', $startDate)
    ->where('absensi.tanggal <=', $endDate);

if ($kelasId) {
    $builder->where('jadwal_mengajar.kelas_id', $kelasId);
}

if ($mapelId) {
    $builder->where('jadwal_mengajar.mata_pelajaran_id', $mapelId);
}
```

#### Similar fixes applied to:
- ✅ `detail()` method (Line 107-113)
- ✅ `print()` method (Line 164-170)
- ✅ `getStatistics()` method (Line 211-233)

---

## 📊 JOIN Pattern yang Benar

### Query Pattern untuk Absensi

```php
// CORRECT: JOIN via jadwal_mengajar
$this->absensiModel
    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
    ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
    ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
    ->where('jadwal_mengajar.guru_id', $guruId)
    ->where('jadwal_mengajar.kelas_id', $kelasId)
    ->where('jadwal_mengajar.mata_pelajaran_id', $mapelId);
```

### Kolom yang Tersedia

| Tabel | Kolom yang Bisa Difilter |
|-------|--------------------------|
| `absensi` | `id`, `jadwal_mengajar_id`, `tanggal`, `pertemuan_ke`, `created_by` |
| `jadwal_mengajar` | `guru_id`, `kelas_id`, `mata_pelajaran_id`, `hari`, `jam_mulai`, `jam_selesai` |
| `absensi_detail` | `absensi_id`, `siswa_id`, `status`, `keterangan` |

---

## 🧪 Testing

### Test Query

```php
// Test 1: Get absensi by guru
$absensi = $this->absensiModel
    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
    ->where('jadwal_mengajar.guru_id', 1)
    ->countAllResults();

// Test 2: Get absensi by kelas
$absensi = $this->absensiModel
    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
    ->where('jadwal_mengajar.kelas_id', 5)
    ->findAll();

// Test 3: Get absensi by mapel
$absensi = $this->absensiModel
    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
    ->where('jadwal_mengajar.mata_pelajaran_id', 2)
    ->findAll();
```

### Expected Results
✅ No SQL errors
✅ Correct data returned
✅ Filters working properly

---

## 📝 Lessons Learned

### 1. **Always Check Database Schema First**
Sebelum membuat query, pastikan kolom yang digunakan benar-benar ada di tabel.

### 2. **Use Existing Model Methods**
`AbsensiModel` sudah memiliki method `getByGuru()`, `getByKelas()`, dll yang sudah implement JOIN dengan benar. Gunakan method tersebut.

### 3. **Follow Database Normalization**
Database sudah di-normalize dengan baik (guru_id, kelas_id, mapel_id di `jadwal_mengajar`). Ikuti struktur ini.

### 4. **Document Complex Queries**
Tambahkan comment untuk query yang kompleks agar developer lain paham.

---

## 🔄 Migration Guide (if needed)

Jika ada controller lain yang mengalami error serupa:

### Step 1: Identify the Error
```
Error: Unknown column 'xxx_id' in 'where clause'
```

### Step 2: Check if Column Exists
```sql
SHOW COLUMNS FROM absensi;
```

### Step 3: Add Proper JOIN
```php
// Before
->where('absensi.xxx_id', $value)

// After
->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
->where('jadwal_mengajar.xxx_id', $value)
```

### Step 4: Test
- Test dengan different filters
- Test dengan NULL values
- Test dengan empty results

---

## ✅ Status

| Controller | Method | Issue | Status |
|------------|--------|-------|--------|
| DashboardController | index() | Unknown column 'guru_id' | ✅ Fixed |
| LaporanController | index() | Unknown column 'kelas_id/mapel_id' | ✅ Fixed |
| LaporanController | index() | Undefined key 'jam_mulai' | ✅ Fixed |
| LaporanController | detail() | Unknown column + Undefined key | ✅ Fixed |
| LaporanController | print() | Unknown column + Undefined key | ✅ Fixed |
| LaporanController | getStatistics() | Unknown column 'kelas_id/mapel_id' | ✅ Fixed |

---

## 🐛 Additional Fix: Undefined array key 'jam_mulai'

### Error
```
Warning: Undefined array key "jam_mulai"
Warning: Undefined array key "jam_selesai"
```

### Root Cause
Query tidak mengambil kolom `jam_mulai` dan `jam_selesai` dari tabel `jadwal_mengajar`.

### Solution
Tambahkan kolom `jam_mulai` dan `jam_selesai` di SELECT statement:

```php
// BEFORE
->select('absensi.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, guru.nama_lengkap as nama_guru')

// AFTER
->select('absensi.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, guru.nama_lengkap as nama_guru,
          jadwal_mengajar.jam_mulai, jadwal_mengajar.jam_selesai')
```

### Views Affected
- ✅ `wakakur/laporan/index.php` - Display jam in table
- ✅ `wakakur/laporan/detail.php` - Display jam in info section
- ✅ `wakakur/laporan/print.php` - Display jam in print layout
- ✅ `wakakur/dashboard_desktop.php` - Recent activities table
- ✅ `wakakur/dashboard_mobile.php` - Recent activities list

**Note**: `DashboardController::index()` menggunakan `AbsensiModel::getByGuru()` yang sudah include `jam_mulai` dan `jam_selesai`, jadi tidak perlu diperbaiki.

---

**Date**: 2026-01-18
**Version**: 1.0.3
**Status**: ✅ **ALL RESOLVED**
