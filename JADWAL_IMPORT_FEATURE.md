# Fitur Import Excel Jadwal Mengajar

## Deskripsi
Fitur untuk mengimport data jadwal mengajar secara batch menggunakan file Excel. Fitur ini mempermudah admin untuk menambahkan banyak jadwal sekaligus tanpa harus input manual satu per satu.

## Fitur Utama

### 1. **Upload File Excel**
- Format: `.xlsx` atau `.xls`
- Ukuran maksimal: 5MB
- Validasi file otomatis

### 2. **Template Excel**
Template dapat didownload dengan struktur kolom:
- **HARI**: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu
- **JAM MULAI**: Format HH:MM:SS (contoh: 07:00:00)
- **JAM SELESAI**: Format HH:MM:SS (contoh: 08:30:00)
- **GURU_ID**: ID guru dari database
- **MATA_PELAJARAN_ID**: ID mata pelajaran dari database
- **KELAS_ID**: ID kelas dari database
- **SEMESTER**: Ganjil atau Genap
- **TAHUN AJARAN**: Format YYYY/YYYY (contoh: 2023/2024)

### 3. **Validasi Data**
- ✅ Validasi format file
- ✅ Validasi ukuran file
- ✅ Validasi data wajib (semua kolom harus diisi)
- ✅ Validasi hari (harus sesuai nama hari)
- ✅ Validasi format jam (HH:MM:SS)
- ✅ Validasi ID guru (harus ada di database)
- ✅ Validasi ID mata pelajaran (harus ada di database)
- ✅ Validasi ID kelas (harus ada di database)
- ✅ **Cek konflik jadwal guru** (guru tidak boleh mengajar 2 kelas di waktu yang sama)
- ✅ **Cek konflik jadwal kelas** (kelas tidak boleh ada 2 mata pelajaran di waktu yang sama)

### 4. **Opsi Import**
- **Lewati jadwal konflik**: Data yang konflik akan dilewati dan dilaporkan

### 5. **Laporan Import**
- Menampilkan jumlah data berhasil diimport
- Menampilkan jumlah data gagal
- Detail error untuk setiap baris yang gagal

## Files yang Dibuat/Dimodifikasi

### 1. **Controller** - `app/Controllers/Admin/JadwalController.php`
**Method baru:**
- `import()` - Menampilkan form import
- `processImport()` - Memproses file Excel yang diupload
- `downloadTemplate()` - Download template Excel

**Fitur dalam processImport:**
```php
- Validasi file upload (MIME type, size)
- Load dan parse Excel menggunakan PhpSpreadsheet
- Validasi setiap row data
- Cek konflik jadwal (guru & kelas)
- Insert data dengan transaction
- Generate laporan sukses/gagal
```

### 2. **View** - `app/Views/admin/jadwal/import.php`
**Komponen:**
- Header dengan judul dan deskripsi
- Petunjuk import (detail)
- Tombol download template
- Form upload file dengan drag & drop area
- Opsi import (skip duplicate)
- Validasi client-side (file size, extension)
- Konfirmasi sebelum submit

### 3. **View** - `app/Views/admin/jadwal/index.php`
**Perubahan:**
- Menambahkan tombol **"Import"** (warna purple) di header
- Posisi: Sebelum tombol Export dan Tambah Jadwal

### 4. **Routes** - `app/Config/Routes.php`
**Route baru:**
```php
$routes->get('jadwal/import', 'Admin\\JadwalController::import');
$routes->post('jadwal/process-import', 'Admin\\JadwalController::processImport');
$routes->get('jadwal/download-template', 'Admin\\JadwalController::downloadTemplate');
```

## Cara Penggunaan

### A. Persiapan Data

1. **Login sebagai Admin**
2. Buka menu **Jadwal Mengajar**
3. Klik tombol **"Import"** (warna purple)

### B. Download Template

1. Di halaman import, klik **"Download Template"**
2. Buka file Excel yang terdownload
3. File memiliki 2 sheet:
   - **Template Import Jadwal**: Isi data di sini
   - **Petunjuk**: Panduan pengisian

### C. Isi Data di Excel

Contoh data:
```
| HARI   | JAM MULAI | JAM SELESAI | GURU_ID | MATA_PELAJARAN_ID | KELAS_ID | SEMESTER | TAHUN AJARAN |
|--------|-----------|-------------|---------|-------------------|----------|----------|--------------|
| Senin  | 07:00:00  | 08:30:00    | 1       | 1                 | 1        | Ganjil   | 2023/2024    |
| Senin  | 08:30:00  | 10:00:00    | 2       | 2                 | 1        | Ganjil   | 2023/2024    |
| Selasa | 07:00:00  | 08:30:00    | 1       | 1                 | 2        | Ganjil   | 2023/2024    |
```

**Tips:**
- Cek ID Guru di menu **Guru** (lihat kolom ID)
- Cek ID Mata Pelajaran di menu **Mata Pelajaran**
- Cek ID Kelas di menu **Kelas**
- Pastikan format jam benar: **HH:MM:SS**
- Hari harus exact match: **Senin** (bukan senin atau SENIN)

### D. Upload File

1. Klik area upload atau drag file ke area tersebut
2. Pilih file Excel yang sudah diisi
3. File akan divalidasi otomatis (size & format)
4. Nama file akan muncul di bawah area upload

### E. Pilih Opsi Import

- ☑ **Lewati jadwal konflik**: Direkomendasikan untuk dicentang
  - Data yang konflik akan dilewati
  - Tidak akan membatalkan seluruh import

### F. Proses Import

1. Klik tombol **"Proses Import"**
2. Konfirmasi dengan klik **OK**
3. Tunggu proses selesai
4. Akan muncul notifikasi hasil import

### G. Cek Hasil

**Jika Berhasil:**
```
Import selesai. Berhasil: 10, Gagal: 0
```

**Jika Ada Error:**
```
Import selesai. Berhasil: 8, Gagal: 2

Detail Error:
- Baris 3: Guru Budi Santoso sudah memiliki jadwal di waktu yang sama (dilewati)
- Baris 5: Kelas X RPL sudah memiliki jadwal di waktu yang sama (dilewati)
```

## Validasi & Error Handling

### 1. **Validasi File**
| Error | Penjelasan |
|-------|------------|
| Format file harus Excel | File bukan .xlsx/.xls |
| Ukuran file terlalu besar | Lebih dari 5MB |
| File tidak valid | File corrupt/rusak |

### 2. **Validasi Data**
| Error | Penjelasan |
|-------|------------|
| Data tidak lengkap | Ada kolom yang kosong |
| Hari tidak valid | Bukan nama hari yang valid |
| Guru ID X tidak ditemukan | ID guru tidak ada di database |
| Mata Pelajaran ID X tidak ditemukan | ID mapel tidak ada di database |
| Kelas ID X tidak ditemukan | ID kelas tidak ada di database |

### 3. **Validasi Konflik**
| Error | Penjelasan |
|-------|------------|
| Guru X sudah memiliki jadwal di waktu yang sama | Guru mengajar di kelas lain di waktu yang sama |
| Kelas X sudah memiliki jadwal di waktu yang sama | Kelas sudah ada pelajaran lain di waktu yang sama |

## Keunggulan Fitur

1. ✅ **Bulk Insert**: Import ratusan jadwal sekaligus
2. ✅ **Validasi Lengkap**: Cek data sebelum masuk database
3. ✅ **Cek Konflik Otomatis**: Mencegah bentrok jadwal
4. ✅ **Template Terstruktur**: Format jelas dengan contoh
5. ✅ **Laporan Detail**: Tahu persis data mana yang gagal
6. ✅ **User Friendly**: UI intuitif dengan drag & drop
7. ✅ **Transaction Safe**: Setiap row dalam transaction terpisah
8. ✅ **Skip Duplicate**: Bisa melanjutkan meski ada error

## Security

- ✅ File MIME type validation (tidak hanya extension)
- ✅ File size limit (5MB)
- ✅ CSRF protection
- ✅ Role-based access (hanya admin)
- ✅ Database transaction (data integrity)
- ✅ Input sanitization (prevent injection)

## Troubleshooting

### Problem: Import gagal semua
**Solution:**
1. Pastikan PhpSpreadsheet terinstall
2. Cek format Excel (gunakan template)
3. Pastikan semua ID valid (guru, mapel, kelas)

### Problem: Konflik jadwal terus muncul
**Solution:**
1. Cek jadwal yang sudah ada
2. Pastikan jam tidak overlap
3. Centang "Lewati jadwal konflik"

### Problem: File tidak bisa diupload
**Solution:**
1. Cek ukuran file (max 5MB)
2. Pastikan format .xlsx atau .xls
3. Cek permission folder writable/

### Problem: ID guru/mapel/kelas tidak ditemukan
**Solution:**
1. Login ke menu Guru/Mata Pelajaran/Kelas
2. Cek ID di kolom tabel (biasanya kolom pertama)
3. Gunakan ID yang benar di Excel

## Update Log

**Version 1.0 - 2026-01-11**
- ✅ Fitur import Excel jadwal mengajar
- ✅ Template download dengan 2 sheet
- ✅ Validasi lengkap (file, data, konflik)
- ✅ Laporan import detail
- ✅ UI user-friendly dengan drag & drop

## Requirement

- PHP >= 7.4
- CodeIgniter 4.x
- PhpSpreadsheet library
- Extension: php_zip, php_xml, php_gd

## Testing Checklist

- [x] Syntax error check (semua file)
- [x] Route registration
- [x] Controller methods
- [x] View rendering
- [x] File validation (MIME, size)
- [x] Data validation (required fields)
- [x] Conflict detection (guru & kelas)
- [x] Transaction handling
- [x] Error reporting
- [x] Success notification

## Contact

Untuk bug report atau feature request, hubungi tim development.
