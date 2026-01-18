# Update Form Admin untuk Role Wakakur

## 📋 Ringkasan Update

Dokumentasi ini menjelaskan perubahan yang dilakukan pada form admin untuk mendukung pemilihan role **wakakur** saat create/edit data guru.

---

## ✅ File yang Dimodifikasi

### 1. **Form Create Guru** (`app/Views/admin/guru/create.php`)

**Perubahan**: Menambahkan option wakakur di dropdown Role

```php
<select name="role" id="roleSelect" required>
    <option value="">Pilih Role</option>
    <option value="guru_mapel">Guru Mata Pelajaran</option>
    <option value="wali_kelas">Wali Kelas</option>
    <option value="wakakur">Wakil Kepala Kurikulum (Wakakur)</option> ✨ BARU
</select>
```

---

### 2. **Form Edit Guru** (`app/Views/admin/guru/edit.php`)

**Perubahan**: Menambahkan option wakakur di dropdown Role

```php
<select name="role" id="roleSelect" required>
    <option value="">Pilih Role</option>
    <option value="guru_mapel">Guru Mata Pelajaran</option>
    <option value="wali_kelas">Wali Kelas</option>
    <option value="wakakur">Wakil Kepala Kurikulum (Wakakur)</option> ✨ BARU
</select>
```

**Note**: Form akan otomatis select option yang sesuai dengan role user saat ini.

---

### 3. **Halaman Index Guru** (`app/Views/admin/guru/index.php`)

#### a. **Filter Dropdown**
```php
<select id="filterRole">
    <option value="">Semua Role</option>
    <option value="guru_mapel">Guru Mapel</option>
    <option value="wali_kelas">Wali Kelas</option>
    <option value="wakakur">Wakakur</option> ✨ BARU
</select>
```

#### b. **Display Role Badge**
```php
<td data-role="<?= esc($g['role']); ?>"> ✨ BARU: data-role attribute
    <?php if ($g['role'] === 'wakakur'): ?> ✨ BARU
        <span class="badge bg-purple-100 text-purple-800">
            <i class="fas fa-user-graduate"></i> Wakakur
        </span>
    <?php elseif ($g['is_wali_kelas']): ?>
        <span class="badge badge-wali">Wali Kelas</span>
    <?php else: ?>
        <span class="badge badge-guru">Guru Mapel</span>
    <?php endif; ?>
</td>
```

#### c. **JavaScript Filter Update**
```javascript
// OLD (based on text content)
const isWaliKelas = roleCell.textContent.includes('Wali Kelas');
const roleMatch = roleValue === '' ||
    (roleValue === 'wali_kelas' && isWaliKelas) ||
    (roleValue === 'guru_mapel' && !isWaliKelas);

// NEW (based on data-role attribute) ✨
const roleData = roleCell.getAttribute('data-role');
const roleMatch = roleValue === '' ||
    (roleValue === 'wakakur' && roleData === 'wakakur') ||
    (roleValue === 'wali_kelas' && roleData === 'wali_kelas') ||
    (roleValue === 'guru_mapel' && roleData === 'guru_mapel');
```

---

### 4. **Halaman Show Guru** (`app/Views/admin/guru/show.php`)

**Perubahan**: Update helper function untuk display role name

```php
function get_role_name_from_role($role) {
    $roleNames = [
        'admin' => 'Administrator',
        'guru_mapel' => 'Guru Mata Pelajaran',
        'wali_kelas' => 'Wali Kelas',
        'wakakur' => 'Wakil Kepala Kurikulum', ✨ BARU
        'siswa' => 'Siswa'
    ];
    return $roleNames[$role] ?? 'Unknown';
}
```

---

### 5. **Controller Validation** (`app/Controllers/Admin/GuruController.php`)

**Sudah diupdate sebelumnya**:

```php
// Create validation
'role' => 'required|in_list[guru_mapel,wali_kelas,wakakur]', ✅

// Edit validation  
'role' => 'required|in_list[guru_mapel,wali_kelas,wakakur]', ✅

// Import validation
$role = in_array($role, ['guru_mapel', 'wali_kelas', 'wakakur']) ? $role : 'guru_mapel'; ✅
```

---

### 6. **Import Template** (`app/Controllers/Admin/GuruController.php`)

**Perubahan**: Menambahkan contoh data wakakur

```php
$sheet->fromArray([
    // ... existing data ...
    [
        '1122334455',           // NIP
        'Ahmad Wakakur',        // Nama
        'L',                    // Jenis Kelamin
        'ahmad.wakakur',        // Username
        'password123',          // Password
        'ahmad@email.com',      // Email
        'wakakur',              // Role ✨ BARU
        1,                      // Mata Pelajaran ID
        5,                      // Kelas ID
        1                       // Is Wali Kelas
    ]
], null, 'A2');
```

---

### 7. **Import Guide** (`app/Views/admin/guru/import.php`)

**Perubahan**: Update dokumentasi role

```html
<li>Role: <b>guru_mapel</b>, <b>wali_kelas</b>, atau <b>wakakur</b></li> ✨ UPDATED
```

---

## 🎨 Visual Changes

### Badge Wakakur (Purple)
```css
.bg-purple-100 { background: #F3E8FF; }
.text-purple-800 { color: #6B21A8; }
```

**Appearance**:
- 🟣 Purple badge untuk wakakur
- 🔵 Blue badge untuk wali kelas  
- 🟢 Green badge untuk guru mapel

---

## 🧪 Testing Checklist

### Manual Testing

- [ ] **Create Guru dengan role wakakur**
  1. Admin Panel > Data Guru > Tambah Guru
  2. Pilih Role: "Wakil Kepala Kurikulum (Wakakur)"
  3. Isi form lengkap
  4. Submit
  5. ✅ User berhasil dibuat dengan role wakakur

- [ ] **Edit Guru ke role wakakur**
  1. Admin Panel > Data Guru > Edit (guru existing)
  2. Ubah Role menjadi: "Wakil Kepala Kurikulum (Wakakur)"
  3. Submit
  4. ✅ Role berhasil diupdate

- [ ] **Filter role wakakur**
  1. Admin Panel > Data Guru
  2. Pilih Filter Role: "Wakakur"
  3. ✅ Hanya guru dengan role wakakur yang ditampilkan

- [ ] **Display badge wakakur**
  1. Admin Panel > Data Guru
  2. ✅ Guru wakakur ditampilkan dengan badge purple "Wakakur"

- [ ] **Import guru dengan role wakakur**
  1. Download template import
  2. ✅ Template sudah ada contoh row wakakur
  3. Fill data dengan role: wakakur
  4. Import
  5. ✅ Data berhasil diimport

---

## 📊 Database Consistency

**Important**: Role wakakur sudah tersedia di database melalui migration:

```sql
-- Cek apakah role wakakur ada
SHOW COLUMNS FROM users LIKE 'role';
-- Output: ENUM('admin', 'guru_mapel', 'wali_kelas', 'wakakur', 'siswa')
```

**Verify existing data**:
```sql
-- Cek users dengan role wakakur
SELECT id, username, role FROM users WHERE role = 'wakakur';
```

---

## 🔄 Backward Compatibility

✅ **Existing data tidak terpengaruh**:
- Guru dengan role `guru_mapel` tetap normal
- Guru dengan role `wali_kelas` tetap normal
- Badge display backward compatible
- Filter backward compatible

---

## 📝 Implementation Summary

| Component | Status | Notes |
|-----------|--------|-------|
| Form Create | ✅ | Option wakakur added |
| Form Edit | ✅ | Option wakakur added |
| Form Show | ✅ | Role display updated |
| Index Filter | ✅ | Wakakur filter added |
| Index Display | ✅ | Purple badge for wakakur |
| JavaScript Filter | ✅ | data-role attribute method |
| Import Template | ✅ | Sample wakakur row added |
| Import Guide | ✅ | Documentation updated |
| Validation | ✅ | wakakur in validation rules |

---

## 🚀 Next Steps

### For Administrators:
1. ✅ Form sudah siap digunakan
2. ✅ Bisa create/edit guru dengan role wakakur
3. ✅ Bisa filter dan view guru wakakur
4. ✅ Bisa import guru dengan role wakakur

### For Testing:
1. Create test user dengan role wakakur
2. Login sebagai wakakur
3. Verify akses ke dashboard wakakur
4. Test semua fitur wakakur (absensi, jurnal, laporan, dll)

---

**Status**: ✅ **COMPLETE**
**Date**: 2026-01-18
**Version**: 1.0.1
