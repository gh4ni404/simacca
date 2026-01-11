# Fix: Absensi List & Access Control for Substitute Teachers

## 🐛 Problems

### Problem 1: Guru Pengganti Tidak Melihat Absensi yang Sudah Diinput
Setelah guru pengganti berhasil input absensi, data tidak muncul di halaman "Data Absensi". Daftar absensi kosong.

### Problem 2: Guru Asli Tidak Bisa Edit/Delete Absensi dari Guru Pengganti
Ketika guru asli (pemilik jadwal) login dan ingin mengedit atau menghapus absensi yang diinput oleh guru pengganti, muncul error: *"Anda tidak memiliki akses ke absensi ini"*

## 🔍 Root Cause

### Problem 1 Root Cause
Method `getByGuru()` di `AbsensiModel` hanya menampilkan absensi berdasarkan `jadwal_mengajar.guru_id`:

```php
// SEBELUM
->where('jadwal_mengajar.guru_id', $guruId)
```

Ini berarti:
- Guru A: Hanya melihat absensi untuk jadwal miliknya
- Guru B (pengganti): Tidak melihat absensi yang diinput untuk jadwal Guru A

### Problem 2 Root Cause
Semua method akses kontrol (show, edit, update, delete, print) hanya mengecek `created_by`:

```php
// SEBELUM
if ($absensi['created_by'] != $userId) {
    return redirect()->to('/guru/absensi')->with('error', 'Anda tidak memiliki akses');
}
```

Ini berarti:
- Guru A tidak bisa akses absensi yang diinput oleh Guru B (walaupun itu jadwal Guru A sendiri)

## ✅ Solutions

### Solution 1: Update `getByGuru()` Model

Mengubah query untuk menampilkan absensi berdasarkan 2 kriteria:
1. **Jadwal milik guru** (normal mode)
2. **Dibuat oleh guru** (substitute mode)

```php
// SESUDAH
->join('users', 'users.id = absensi.created_by')
->join('guru guru_creator', 'guru_creator.user_id = users.id')
->groupStart()
    ->where('jadwal_mengajar.guru_id', $guruId)  // Schedule belongs to this teacher
    ->orWhere('guru_creator.id', $guruId)        // Or created by this teacher (substitute)
->groupEnd()
```

**Benefits:**
- Guru A melihat: Absensi untuk jadwal A (baik yang diinput sendiri maupun oleh pengganti)
- Guru B (pengganti) melihat: Absensi yang diinput sebagai pengganti

### Solution 2: Update Access Control Logic

Mengubah validasi akses di semua method untuk mengizinkan 2 scenario:
1. **User yang input absensi** (`created_by`)
2. **Pemilik jadwal** (`guru_id` dari jadwal)

```php
// SESUDAH
$jadwal = $this->jadwalModel->find($absensi['jadwal_mengajar_id']);
$hasAccess = ($absensi['created_by'] == $userId) || ($jadwal && $jadwal['guru_id'] == $guru['id']);

if (!$hasAccess) {
    return redirect()->to('/guru/absensi')->with('error', 'Anda tidak memiliki akses');
}
```

## 🔧 Changes Applied

### File 1: `app/Models/AbsensiModel.php`

#### Method: `getByGuru()`

**Before:**
```php
public function getByGuru($guruId, $startDate = null, $endDate = null)
{
    $builder = $this->select('absensi.*,
                        guru.nama_lengkap as nama_guru,
                        mata_pelajaran.nama_mapel,
                        kelas.nama_kelas,
                        COUNT(absensi_detail.id) as total_siswa,
                        SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) as hadir,
                        ROUND((SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) / COUNT(absensi_detail.id)) * 100, 0) as percentage')
        ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
        ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
        ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
        ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
        ->join('absensi_detail', 'absensi_detail.absensi_id = absensi.id', 'left')
        ->where('jadwal_mengajar.guru_id', $guruId)  // ❌ Only schedule owner
        ->groupBy('absensi.id')
        ->orderBy('absensi.tanggal', 'DESC');

    return $builder->findAll();
}
```

**After:**
```php
public function getByGuru($guruId, $startDate = null, $endDate = null)
{
    $builder = $this->select('absensi.*,
                        guru.nama_lengkap as nama_guru,
                        guru_pengganti.nama_lengkap as nama_guru_pengganti,  // ✅ Added
                        mata_pelajaran.nama_mapel,
                        kelas.nama_kelas,
                        COUNT(absensi_detail.id) as total_siswa,
                        SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) as hadir,
                        ROUND((SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) / COUNT(absensi_detail.id)) * 100, 0) as percentage')
        ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
        ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
        ->join('guru guru_pengganti', 'guru_pengganti.id = absensi.guru_pengganti_id', 'left')  // ✅ Added
        ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
        ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
        ->join('absensi_detail', 'absensi_detail.absensi_id = absensi.id', 'left')
        ->join('users', 'users.id = absensi.created_by')  // ✅ Added
        ->join('guru guru_creator', 'guru_creator.user_id = users.id')  // ✅ Added
        ->groupStart()  // ✅ Added
            ->where('jadwal_mengajar.guru_id', $guruId)  // Schedule belongs to this teacher
            ->orWhere('guru_creator.id', $guruId)        // Or created by this teacher (substitute)
        ->groupEnd()  // ✅ Added
        ->groupBy('absensi.id')
        ->orderBy('absensi.tanggal', 'DESC');

    return $builder->findAll();
}
```

### File 2: `app/Controllers/Guru/AbsensiController.php`

#### Methods Updated:
1. **show($id)** - Line ~295-303
2. **edit($id)** - Line ~347-354  
3. **update($id)** - Line ~418-425
4. **delete($id)** - Line ~518-527
5. **print($id)** - Line ~651-659

**Before (All Methods):**
```php
// Verify the absensi belongs to this teacher
if ($absensi['created_by'] != $userId) {
    $this->session->setFlashdata('error', 'Anda tidak memiliki akses ke absensi ini.');
    return redirect()->to('/guru/absensi');
}
```

**After (All Methods):**
```php
// Verify access: Allow if user created the absensi OR if schedule belongs to this teacher
$jadwal = $this->jadwalModel->find($absensi['jadwal_mengajar_id']);
$hasAccess = ($absensi['created_by'] == $userId) || ($jadwal && $jadwal['guru_id'] == $guru['id']);

if (!$hasAccess) {
    $this->session->setFlashdata('error', 'Anda tidak memiliki akses ke absensi ini.');
    return redirect()->to('/guru/absensi');
}
```

## 📊 Access Matrix

### Who Can See What?

| Scenario | Guru A (Schedule Owner) | Guru B (Substitute) |
|----------|------------------------|---------------------|
| Guru A input absensi untuk jadwal A | ✅ See & Edit | ❌ No access |
| Guru B input absensi untuk jadwal A (substitute) | ✅ See & Edit | ✅ See & Edit |
| Guru A input absensi untuk jadwal A dengan Guru C sebagai pengganti | ✅ See & Edit | ❌ No access |

### Access Control Logic

```
hasAccess = (created_by == current_user_id) OR (jadwal.guru_id == current_guru_id)
```

**Examples:**

**Example 1: Normal Mode**
```
Absensi:
  - jadwal_mengajar_id: 1 (Guru A's schedule)
  - created_by: User ID Guru A
  - guru_pengganti_id: NULL

Access:
  - Guru A: ✅ (created_by match)
  - Guru B: ❌ (no match)
```

**Example 2: Substitute Mode**
```
Absensi:
  - jadwal_mengajar_id: 1 (Guru A's schedule)
  - created_by: User ID Guru B
  - guru_pengganti_id: Guru B

Access:
  - Guru A: ✅ (jadwal.guru_id match)
  - Guru B: ✅ (created_by match)
  - Guru C: ❌ (no match)
```

## 🧪 Testing Scenarios

### Test 1: Guru Pengganti Melihat Daftar Absensi
```
Given: Guru B login (sebagai pengganti)
And: Guru B sudah input absensi untuk jadwal Guru A
When: Buka halaman /guru/absensi
Then: Absensi yang diinput Guru B muncul di list ✅
And: Menampilkan badge "Guru Pengganti" ✅
```

### Test 2: Guru Asli Melihat Daftar Absensi
```
Given: Guru A login (pemilik jadwal)
And: Guru B pernah input absensi untuk jadwal Guru A (substitute)
When: Buka halaman /guru/absensi
Then: Absensi muncul di list ✅
And: Menampilkan nama "Guru A" sebagai guru
And: Menampilkan "Guru B" sebagai guru pengganti ✅
```

### Test 3: Guru Asli Edit Absensi dari Guru Pengganti
```
Given: Guru A login
And: Guru B pernah input absensi untuk jadwal Guru A
When: Guru A klik "Edit" pada absensi tersebut
Then: Form edit terbuka ✅
And: Guru A bisa edit dan save ✅
```

### Test 4: Guru Asli Delete Absensi dari Guru Pengganti
```
Given: Guru A login
And: Guru B pernah input absensi untuk jadwal Guru A (< 24 jam)
When: Guru A klik "Delete" pada absensi tersebut
Then: Absensi berhasil dihapus ✅
```

### Test 5: Guru Lain Tidak Bisa Akses
```
Given: Guru C login
When: Guru C coba akses absensi Guru A atau Guru B
Then: Error "Anda tidak memiliki akses" ✅
And: Redirect ke /guru/absensi ✅
```

## 🔐 Security Considerations

### Access Control Principles

1. **Dual Ownership**
   - Creator ownership: Yang input absensi berhak akses
   - Schedule ownership: Pemilik jadwal berhak akses
   
2. **Logical OR**
   - Hanya perlu salah satu kondisi terpenuhi
   - Tidak perlu keduanya sekaligus

3. **Time-based Edit Lock**
   - Edit/Delete hanya dalam 24 jam (sudah ada sebelumnya)
   - Berlaku untuk semua guru (creator maupun schedule owner)

4. **Audit Trail**
   - `created_by` tetap mencatat siapa yang input
   - `guru_pengganti_id` menunjukkan jika ada pengganti
   - Laporan admin menampilkan lengkap

### Why This is Secure

1. **No Privilege Escalation**
   - Guru hanya bisa akses jadwal mereka sendiri atau yang mereka input
   - Tidak bisa akses jadwal guru lain yang tidak terkait

2. **Clear Responsibility**
   - Creator: Yang input dan bertanggung jawab atas data
   - Schedule Owner: Yang bertanggung jawab atas pembelajaran

3. **Transparent Logging**
   - Semua aksi tercatat di `created_by`
   - Admin bisa audit siapa input apa

## 📝 Summary

### Files Modified
1. `app/Models/AbsensiModel.php` - Method `getByGuru()`
2. `app/Controllers/Guru/AbsensiController.php` - Methods:
   - `show()`
   - `edit()`
   - `update()`
   - `delete()`
   - `print()`

### Impact
- ✅ **Guru pengganti bisa melihat absensi yang diinput**
- ✅ **Guru asli bisa edit/delete absensi dari guru pengganti**
- ✅ **List absensi menampilkan info guru pengganti**
- ✅ **Dual ownership: creator DAN schedule owner**
- ✅ **Security maintained**: Guru lain tetap tidak bisa akses
- ✅ **No breaking changes**: Existing functionality tidak terpengaruh

## ✨ Expected Behavior

### For Substitute Teacher (Guru B)
1. Input absensi untuk jadwal Guru A → ✅ Success
2. Lihat daftar absensi → ✅ Absensi muncul
3. Edit/Delete absensi sendiri → ✅ Bisa (dalam 24 jam)
4. Isi jurnal KBM → ✅ Bisa

### For Original Teacher (Guru A)
1. Lihat daftar absensi → ✅ Termasuk yang diinput Guru B
2. Edit absensi dari Guru B → ✅ Bisa (dalam 24 jam)
3. Delete absensi dari Guru B → ✅ Bisa (dalam 24 jam)
4. Lihat detail → ✅ Menampilkan info guru pengganti

### For Other Teacher (Guru C)
1. Coba akses absensi Guru A/B → ❌ Error "Tidak memiliki akses"
2. List absensi → ❌ Tidak muncul absensi Guru A/B

---

**Fixed**: 2026-01-12  
**Issues**: 
1. Substitute teacher can't see their own absensi records
2. Original teacher can't edit/delete absensi from substitute  
**Status**: ✅ Resolved  
**Related**: 
- SUBSTITUTE_TEACHER_MODE_FIX.md
- SUBSTITUTE_MODE_ACCESS_FIX.md
- JURNAL_SUBSTITUTE_ACCESS_FIX.md
