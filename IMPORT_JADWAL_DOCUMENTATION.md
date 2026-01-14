# Dokumentasi Fitur Import Excel Jadwal Mengajar

## Overview
Fitur import Excel untuk jadwal mengajar memudahkan admin dalam menambahkan banyak jadwal sekaligus melalui file Excel. Sistem akan otomatis memvalidasi data dan mengecek konflik jadwal.

## ✨ **UPDATE TERBARU: USER-FRIENDLY DENGAN DROPDOWN!**

### Fitur Baru (2026-01-14):
🎉 **TIDAK PERLU MENGINGAT ID LAGI!**

- ✅ **Dropdown Otomatis** untuk Hari, Guru, Mapel, Kelas, Semester
- ✅ **5 Sheet** dalam template: Template, Data Guru, Data Mapel, Data Kelas, Petunjuk
- ✅ **Data Referensi Lengkap** dengan NIP dan Kode Mapel
- ✅ **Format User-Friendly**: `Nama Guru (NIP)`, `Nama Mapel (Kode)`
- ✅ **Backward Compatible** dengan template lama (masih support ID)

## Status Implementasi
✅ **SUDAH TERSEDIA DAN LENGKAP** + **SUDAH DITINGKATKAN!**

Semua fitur import jadwal mengajar sudah diimplementasikan dengan lengkap di aplikasi ini, termasuk:
- Form upload Excel ✅
- Template Excel dengan dropdown dan petunjuk ✅ **NEW!**
- Data referensi di sheet terpisah ✅ **NEW!**
- Validasi data lengkap ✅
- Support nama dan ID ✅ **NEW!**
- Pengecekan konflik jadwal otomatis ✅
- Error handling dan reporting ✅
- Export Excel untuk backup ✅

## Fitur Utama

### 1. Upload File Excel
- **Format**: `.xlsx` atau `.xls`
- **Ukuran maksimal**: 5MB
- **Validasi client-side**: JavaScript untuk validasi file sebelum submit
- **Validasi server-side**: Keamanan file upload dengan helper `validate_file_upload()`

### 2. Template Excel (✨ UPDATED - User-Friendly!)

**File**: `template-import-jadwal-YYYY-MM-DD.xlsx`

Template baru berisi **5 Sheet** dengan dropdown otomatis:
#### **Sheet 1: Template Import Jadwal** ⭐
  - **Header**: HARI, JAM MULAI, JAM SELESAI, **NAMA GURU**, **MATA PELAJARAN**, **KELAS**, SEMESTER, TAHUN AJARAN
  - **Dropdown Validation** untuk 50 baris:
    - ✅ **Hari**: Dropdown (Senin, Selasa, Rabu, Kamis, Jumat)
    - ✅ **Nama Guru**: Dropdown dengan format `Nama Lengkap (NIP)`
    - ✅ **Mata Pelajaran**: Dropdown dengan format `Nama Mapel (Kode)`
    - ✅ **Kelas**: Dropdown dengan Nama Kelas
    - ✅ **Semester**: Dropdown (Ganjil, Genap)
  - 1 baris sample data dengan nama (bukan ID)
  - Wide columns untuk readability
  - Freeze header row
  - Styled header (background biru, bold, white text, bordered)

#### **Sheet 2: Data Guru** 📋
  - Kolom: ID, NIP, NAMA LENGKAP
  - Data diambil dari database (guru aktif)
  - Digunakan untuk referensi saat mengisi template
  - **Auto-populated** saat download template

#### **Sheet 3: Data Mata Pelajaran** 📚
  - Kolom: ID, KODE, NAMA MATA PELAJARAN
  - Data diambil dari database
  - Digunakan untuk referensi saat mengisi template
  - **Auto-populated** saat download template

#### **Sheet 4: Data Kelas** 🏫
  - Kolom: ID, NAMA KELAS
  - Data diambil dari database
  - Digunakan untuk referensi saat mengisi template
  - **Auto-populated** saat download template

#### **Sheet 5: Petunjuk** 📖
  - Cara mengisi template dengan dropdown
  - Penjelasan lengkap setiap kolom
  - Format data yang benar
  - Tips penting dan validasi otomatis
  - Contoh data valid
  - Referensi sheet data

### 3. Validasi Data Import

#### Validasi Format:
- ✅ File type (Excel only)
- ✅ File size (max 5MB)
- ✅ Required fields tidak boleh kosong
- ✅ Format jam (HH:MM:SS)
- ✅ Hari harus valid (Senin-Jumat)
- ✅ Semester (Ganjil/Genap)
- ✅ Tahun ajaran format (YYYY/YYYY)

#### Validasi Relasi:
- ✅ Guru ID harus ada di database
- ✅ Mata Pelajaran ID harus ada di database
- ✅ Kelas ID harus ada di database

#### Validasi Konflik:
- ✅ Cek konflik jadwal untuk guru (tidak boleh mengajar 2 kelas di jam yang sama)
- ✅ Cek konflik jadwal untuk kelas (tidak boleh ada 2 pelajaran di jam yang sama)

### 4. Opsi Import

#### Skip Duplicate (Lewati Jadwal Konflik)
- **Checked**: Data yang konflik akan dilewati, proses lanjut dengan data valid lainnya
- **Unchecked**: Proses akan berhenti jika ada konflik

### 5. Error Handling & Reporting
- Transaction rollback untuk setiap row yang error
- Detailed error messages dengan nomor baris
- Summary report: Berhasil vs Gagal
- Flash message untuk info result
- Session storage untuk detailed errors

## Cara Menggunakan

### Langkah-langkah Import:

1. **Login sebagai Admin**
   - Buka aplikasi dan login dengan role `admin`

2. **Buka Menu Jadwal Mengajar**
   - Navigasi ke menu `Admin` → `Jadwal Mengajar`
   - URL: `/admin/jadwal`

3. **Klik Tombol Import**
   - Klik tombol **"Import"** (warna ungu) di pojok kanan atas
   - Akan membuka halaman import: `/admin/jadwal/import`

4. **Download Template Excel**
   - Klik tombol **"Download Template"** (warna hijau)
   - File `template-import-jadwal.xlsx` akan terdownload
   - Buka file di Excel/LibreOffice

5. **Isi Data di Template**
   - **Sheet "Template Import Jadwal"**: Isi data jadwal
   - Lihat **Sheet "Petunjuk"** untuk panduan detail
   - Hapus sample data, isi dengan data real

6. **Format Data Template:**
   ```
   | HARI   | JAM MULAI | JAM SELESAI | GURU_ID | MATA_PELAJARAN_ID | KELAS_ID | SEMESTER | TAHUN AJARAN |
   |--------|-----------|-------------|---------|-------------------|----------|----------|--------------|
   | Senin  | 07:00:00  | 08:30:00    | 1       | 1                 | 1        | Ganjil   | 2023/2024    |
   | Senin  | 08:30:00  | 10:00:00    | 2       | 2                 | 1        | Ganjil   | 2023/2024    |
   | Selasa | 07:00:00  | 08:30:00    | 1       | 1                 | 2        | Ganjil   | 2023/2024    |
   ```

7. **Mendapatkan ID yang Valid:**
   - **Guru ID**: Lihat di menu `Admin` → `Guru` → kolom ID
   - **Mata Pelajaran ID**: Lihat di menu `Admin` → `Mata Pelajaran` → kolom ID
   - **Kelas ID**: Lihat di menu `Admin` → `Kelas` → kolom ID

8. **Upload File Excel**
   - Kembali ke halaman import
   - Klik area upload atau drag & drop file
   - Nama file akan muncul

9. **Pilih Opsi Import**
   - ☑️ **Lewati jadwal konflik**: Direkomendasikan untuk import massal
   - Jika dicentang, data konflik akan dilewati dan dilaporkan

10. **Klik Proses Import**
    - Konfirmasi dialog akan muncul
    - Klik OK untuk melanjutkan
    - Loading indicator akan muncul
    - Tunggu proses selesai

11. **Lihat Hasil Import**
    - Akan redirect ke `/admin/jadwal`
    - Flash message menampilkan summary: "Berhasil: X, Gagal: Y"
    - Jika ada error, list error akan ditampilkan dengan detail baris

## Format Data Detail

### 1. HARI
- **Type**: Enum
- **Valid values**: `Senin`, `Selasa`, `Rabu`, `Kamis`, `Jumat`
- **Case sensitive**: Ya
- **Contoh**: `Senin` ✅ | `senin` ❌

### 2. JAM MULAI & JAM SELESAI
- **Format**: `HH:MM:SS`
- **Range**: 00:00:00 - 23:59:59
- **Contoh**: `07:00:00`, `08:30:00`, `15:45:00`
- **Validasi**: Jam selesai harus > jam mulai

### 3. GURU_ID
- **Type**: Integer
- **Referensi**: Tabel `guru`, kolom `id`
- **Validasi**: ID harus ada di database
- **Contoh**: `1`, `5`, `12`

### 4. MATA_PELAJARAN_ID
- **Type**: Integer
- **Referensi**: Tabel `mata_pelajaran`, kolom `id`
- **Validasi**: ID harus ada di database
- **Contoh**: `1`, `3`, `8`

### 5. KELAS_ID
- **Type**: Integer
- **Referensi**: Tabel `kelas`, kolom `id`
- **Validasi**: ID harus ada di database
- **Contoh**: `1`, `2`, `5`

### 6. SEMESTER
- **Type**: Enum
- **Valid values**: `Ganjil`, `Genap`
- **Case sensitive**: Ya
- **Contoh**: `Ganjil` ✅ | `ganjil` ❌

### 7. TAHUN AJARAN
- **Format**: `YYYY/YYYY`
- **Pattern**: Tahun pertama harus = tahun kedua - 1
- **Contoh**: `2023/2024` ✅ | `2023-2024` ❌

## Validasi Konflik

### Konflik Guru
Terjadi ketika **guru yang sama** mengajar di **hari yang sama** dengan **jam yang overlap**.

**Contoh Konflik:**
```
Jadwal 1: Guru ID 1, Senin, 07:00:00 - 08:30:00
Jadwal 2: Guru ID 1, Senin, 08:00:00 - 09:30:00  ❌ KONFLIK (overlap jam)
```

### Konflik Kelas
Terjadi ketika **kelas yang sama** memiliki jadwal di **hari yang sama** dengan **jam yang overlap**.

**Contoh Konflik:**
```
Jadwal 1: Kelas ID 1, Senin, 07:00:00 - 08:30:00
Jadwal 2: Kelas ID 1, Senin, 08:00:00 - 09:30:00  ❌ KONFLIK (overlap jam)
```

**Tidak Konflik:**
```
Jadwal 1: Kelas ID 1, Senin, 07:00:00 - 08:30:00
Jadwal 2: Kelas ID 1, Senin, 08:30:00 - 10:00:00  ✅ AMAN (jam tepat bersebelahan)
```

## Error Messages

### Common Errors:

1. **"Data tidak lengkap pada baris X"**
   - Solusi: Pastikan semua kolom terisi

2. **"Hari tidak valid: XXX"**
   - Solusi: Gunakan: Senin, Selasa, Rabu, Kamis, atau Jumat

3. **"Guru ID X tidak ditemukan"**
   - Solusi: Cek ID guru di menu Guru, pastikan ID valid

4. **"Mata Pelajaran ID X tidak ditemukan"**
   - Solusi: Cek ID mapel di menu Mata Pelajaran

5. **"Kelas ID X tidak ditemukan"**
   - Solusi: Cek ID kelas di menu Kelas

6. **"Guru [Nama] sudah memiliki jadwal di waktu yang sama"**
   - Solusi: Ubah jam atau hari, atau centang "Lewati jadwal konflik"

7. **"Kelas [Nama] sudah memiliki jadwal di waktu yang sama"**
   - Solusi: Ubah jam atau hari, atau centang "Lewati jadwal konflik"

## Export Excel

Selain import, aplikasi juga menyediakan fitur **Export Excel** untuk:
- Backup data jadwal
- Template dengan data existing
- Analisis jadwal

**Cara Export:**
1. Buka menu `Jadwal Mengajar`
2. Filter semester dan tahun ajaran (optional)
3. Klik tombol **"Export"** (warna hijau)
4. File Excel akan terdownload

**Output Export berisi:**
- No, Hari, Jam, Kelas, Guru (dengan NIP), Mata Pelajaran (dengan kode), Semester, Tahun Ajaran
- Format siap print
- Styled header

## Tips & Best Practices

### 1. Persiapan Data
✅ Siapkan data di Excel terpisah dulu (ID guru, mapel, kelas)
✅ Copy-paste ke template setelah yakin data valid
✅ Jangan ubah nama kolom header
✅ Hapus baris kosong di tengah data

### 2. Menghindari Error
✅ Download data guru/mapel/kelas dulu untuk referensi ID
✅ Gunakan Excel formula untuk validasi (VLOOKUP)
✅ Test dengan data kecil dulu (5-10 baris)
✅ Backup data sebelum import massal

### 3. Import Massal
✅ Centang "Lewati jadwal konflik"
✅ Import per semester
✅ Import per tingkat kelas
✅ Review error setelah import, perbaiki manual jika perlu

### 4. Troubleshooting
- **Import gagal total**: Cek format file (harus .xlsx/.xls)
- **Banyak error**: Download template baru, copy paste data dengan hati-hati
- **Konflik banyak**: Export data existing dulu, bandingkan dengan data import

## Technical Details

### Controller Methods:
- `import()`: Menampilkan form import
- `processImport()`: Memproses file Excel
- `downloadTemplate()`: Generate dan download template
- `export()`: Export data jadwal ke Excel

### Files:
- **Controller**: `app/Controllers/Admin/JadwalController.php`
- **View**: `app/Views/admin/jadwal/import.php`
- **Routes**: `app/Config/Routes.php` (lines 138-141)
- **Model**: `app/Models/JadwalMengajarModel.php`

### Dependencies:
- PhpSpreadsheet (untuk baca/tulis Excel)
- Security helper (untuk validasi file upload)

### Database Table:
```sql
jadwal_mengajar:
  - id (PK)
  - guru_id (FK -> guru.id)
  - mata_pelajaran_id (FK -> mata_pelajaran.id)
  - kelas_id (FK -> kelas.id)
  - hari (ENUM: Senin-Jumat)
  - jam_mulai (TIME)
  - jam_selesai (TIME)
  - semester (ENUM: Ganjil/Genap)
  - tahun_ajaran (VARCHAR)
  - created_at (DATETIME)
```

## Security

### File Upload Security:
- ✅ Allowed MIME types: Excel only
- ✅ File size limit: 5MB
- ✅ File extension validation
- ✅ CSRF protection
- ✅ Role-based access (admin only)

### Data Security:
- ✅ Transaction untuk atomicity
- ✅ Rollback on error
- ✅ SQL injection prevention (query builder)
- ✅ XSS prevention (output escaping)

## Conclusion

Fitur import Excel jadwal mengajar sudah **lengkap dan siap digunakan**. Tidak perlu implementasi tambahan. Admin dapat langsung menggunakan fitur ini untuk:

1. Import jadwal baru secara massal
2. Update jadwal per semester
3. Export data untuk backup
4. Download template untuk panduan

Fitur ini menghemat waktu signifikan dibanding input manual satu per satu, terutama untuk sekolah dengan banyak guru dan kelas.
