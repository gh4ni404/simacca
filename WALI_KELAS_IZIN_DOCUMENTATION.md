# Dokumentasi Fitur Persetujuan Izin Siswa oleh Wali Kelas

## 📋 Deskripsi
Fitur ini memungkinkan wali kelas untuk melihat, menyetujui, atau menolak pengajuan izin dari siswa di kelasnya. Wali kelas memiliki wewenang penuh untuk mengelola izin siswa yang berada di kelasnya.

## 🔧 Perbaikan yang Dilakukan

### 1. **Penambahan Kolom Timestamp**
- Menambahkan kolom `created_at` dan `updated_at` pada tabel `izin_siswa`
- Mengaktifkan auto timestamps di model `IzinSiswaModel`
- Membuat migration baru: `2026-01-11-222000_AddTimestampsToIzinSiswa.php`

### 2. **File yang Dimodifikasi**
- ✅ `app/Database/Migrations/2026-01-06-163357_CreateIzinSiswaTable.php` - Ditambahkan kolom timestamp
- ✅ `app/Models/IzinSiswaModel.php` - Mengaktifkan `useTimestamps = true`
- ✅ `app/Database/Migrations/2026-01-11-222000_AddTimestampsToIzinSiswa.php` - Migration baru untuk database existing

## 🎯 Fitur yang Tersedia untuk Wali Kelas

### 1. **Melihat Daftar Izin Siswa**
- **URL**: `/walikelas/izin`
- **Fungsi**: Menampilkan semua pengajuan izin dari siswa di kelas yang diajar
- **Data yang ditampilkan**:
  - Nama siswa dan NIS
  - Tanggal izin
  - Jenis izin (Sakit/Izin)
  - Alasan
  - Dokumen pendukung (jika ada)
  - Status (Pending/Disetujui/Ditolak)
  - Waktu pengajuan

### 2. **Filter Berdasarkan Status**
Wali kelas dapat memfilter izin berdasarkan:
- **Semua Izin** - Menampilkan semua izin
- **Pending** - Izin yang menunggu persetujuan
- **Disetujui** - Izin yang sudah disetujui
- **Ditolak** - Izin yang sudah ditolak

### 3. **Menyetujui Izin**
- **Endpoint**: `POST /walikelas/izin/setujui/{id}`
- **Fungsi**: Menyetujui pengajuan izin siswa
- **Parameter**:
  - `catatan` (opsional) - Catatan tambahan dari wali kelas
- **Proses**:
  1. Klik tombol "Setujui" pada izin yang pending
  2. Masukkan catatan (opsional)
  3. Konfirmasi persetujuan
  4. Status izin berubah menjadi "Disetujui"
  5. Data `disetujui_oleh` diisi dengan user_id wali kelas

### 4. **Menolak Izin**
- **Endpoint**: `POST /walikelas/izin/tolak/{id}`
- **Fungsi**: Menolak pengajuan izin siswa
- **Parameter**:
  - `catatan` (wajib) - Alasan penolakan
- **Proses**:
  1. Klik tombol "Tolak" pada izin yang pending
  2. Masukkan alasan penolakan (wajib)
  3. Konfirmasi penolakan
  4. Status izin berubah menjadi "Ditolak"
  5. Data `disetujui_oleh` diisi dengan user_id wali kelas

### 5. **Statistik Izin**
Dashboard menampilkan ringkasan:
- Jumlah izin pending (menunggu persetujuan)
- Jumlah izin yang sudah disetujui
- Jumlah izin yang ditolak

## 🔐 Keamanan dan Validasi

### Authorization
- Hanya guru dengan status `is_wali_kelas = 1` yang dapat mengakses fitur ini
- Wali kelas hanya dapat melihat dan mengelola izin siswa di kelasnya sendiri
- Route dilindungi dengan filter `role:wali_kelas`

### Validasi Data
- Wali kelas harus terdaftar sebagai wali kelas (`is_wali_kelas = 1`)
- Wali kelas harus memiliki kelas yang ditugaskan
- Hanya izin dengan status "pending" yang dapat disetujui/ditolak
- Alasan penolakan wajib diisi saat menolak izin

## 📊 Struktur Database

### Tabel `izin_siswa`
```sql
CREATE TABLE izin_siswa (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT(11) UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    jenis_izin ENUM('sakit', 'izin', 'lainnya') NOT NULL,
    alasan TEXT NOT NULL,
    berkas VARCHAR(255) NULL,
    status ENUM('pending', 'disetujui', 'ditolak') DEFAULT 'pending',
    disetujui_oleh INT(11) UNSIGNED NULL,
    catatan TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (disetujui_oleh) REFERENCES users(id) ON DELETE SET NULL
);
```

## 🚀 Cara Menggunakan

### Persiapan Database
Jika Anda memiliki database existing, jalankan migration untuk menambahkan kolom timestamp:

```bash
php spark migrate
```

Atau jalankan SQL manual:
```sql
ALTER TABLE izin_siswa 
ADD COLUMN created_at DATETIME NULL AFTER catatan,
ADD COLUMN updated_at DATETIME NULL AFTER created_at;

-- Update data existing
UPDATE izin_siswa SET created_at = NOW() WHERE created_at IS NULL;
```

### Flow Penggunaan

#### Sisi Siswa:
1. Login sebagai siswa
2. Akses menu "Izin" atau URL `/siswa/izin`
3. Klik "Ajukan Izin Baru"
4. Isi form:
   - Tanggal izin
   - Jenis izin (Sakit/Izin)
   - Alasan (minimal 10 karakter)
   - Upload dokumen pendukung (opsional, maks 2MB)
5. Submit form
6. Status izin akan "Pending" menunggu persetujuan wali kelas

#### Sisi Wali Kelas:
1. Login sebagai wali kelas
2. Akses menu "Izin Siswa" atau URL `/walikelas/izin`
3. Lihat daftar pengajuan izin dari siswa di kelas Anda
4. Untuk menyetujui:
   - Klik tombol "Setujui"
   - Tambahkan catatan (opsional)
   - Konfirmasi
5. Untuk menolak:
   - Klik tombol "Tolak"
   - Masukkan alasan penolakan (wajib)
   - Konfirmasi

## 🎨 Tampilan UI

### Dashboard Izin Wali Kelas
- **Header**: Judul dan nama kelas
- **Statistik Cards**: 3 cards menampilkan jumlah Pending, Disetujui, Ditolak
- **Filter Tabs**: Tab untuk filter berdasarkan status
- **List Izin**: Card untuk setiap izin dengan:
  - Avatar dan nama siswa
  - Badge status (Pending/Disetujui/Ditolak)
  - Detail izin (tanggal, jenis, alasan)
  - Link dokumen pendukung
  - Catatan wali kelas (jika ada)
  - Tombol aksi (untuk izin pending)
- **Info Footer**: Informasi tentang cara menggunakan fitur

### Modal Approval
- Modal konfirmasi dengan form catatan
- Design sesuai dengan action (hijau untuk approve, merah untuk reject)
- Validasi client-side

## 📱 API Endpoints

### 1. GET `/walikelas/izin`
Menampilkan halaman daftar izin siswa

**Query Parameters:**
- `status` (optional): Filter berdasarkan status (pending/disetujui/ditolak)

**Response:** HTML View

---

### 2. POST `/walikelas/izin/setujui/{id}`
Menyetujui izin siswa

**Parameters:**
- `id`: ID izin siswa

**Body:**
- `catatan` (optional): Catatan dari wali kelas

**Response:**
```json
{
    "status": "success",
    "message": "✅ Izin berhasil disetujui"
}
```

---

### 3. POST `/walikelas/izin/tolak/{id}`
Menolak izin siswa

**Parameters:**
- `id`: ID izin siswa

**Body:**
- `catatan` (required): Alasan penolakan

**Response:**
```json
{
    "status": "success",
    "message": "⚠️ Izin berhasil ditolak"
}
```

## 🔍 Logging

Sistem mencatat setiap aktivitas untuk debugging:
```
[WALI KELAS IZIN] Index started
[WALI KELAS IZIN] User ID: 123
[WALI KELAS IZIN] Guru found: 45
[WALI KELAS IZIN] Kelas found: 12 - X RPL 1
[WALI KELAS IZIN] Filter status: pending
[WALI KELAS IZIN] Total izin found: 5
[WALI KELAS IZIN] Count - Pending: 3, Disetujui: 1, Ditolak: 1
[WALI KELAS IZIN] Approve started - ID: 10
[WALI KELAS IZIN] Approve successful
```

## 🧪 Testing

### Manual Testing Checklist
- [ ] Login sebagai siswa dan ajukan izin
- [ ] Verifikasi izin masuk ke database dengan status "pending"
- [ ] Login sebagai wali kelas
- [ ] Verifikasi dapat melihat izin siswa di kelasnya
- [ ] Verifikasi tidak dapat melihat izin siswa di kelas lain
- [ ] Test approve izin dengan catatan
- [ ] Test approve izin tanpa catatan
- [ ] Test reject izin dengan alasan
- [ ] Test reject izin tanpa alasan (harus gagal)
- [ ] Verifikasi filter status bekerja dengan baik
- [ ] Verifikasi statistik menampilkan angka yang benar
- [ ] Verifikasi siswa dapat melihat status persetujuan
- [ ] Verifikasi dokumen pendukung dapat diakses

### SQL Testing
Gunakan file `tmp_rovodev_test_walikelas_izin.sql` untuk testing database.

## 🐛 Troubleshooting

### Error: "Column 'created_at' not found"
**Solusi**: Jalankan migration atau SQL manual untuk menambahkan kolom timestamp.

### Error: "Anda bukan wali kelas"
**Solusi**: 
1. Pastikan guru memiliki `is_wali_kelas = 1` di tabel `guru`
2. Pastikan ada kelas yang ditugaskan (`wali_kelas_id` di tabel `kelas`)

### Izin tidak muncul di dashboard wali kelas
**Solusi**:
1. Pastikan siswa berada di kelas yang dikelola wali kelas
2. Cek join table di query `getByKelas()`
3. Lihat log untuk debugging

### Tombol approve/reject tidak berfungsi
**Solusi**:
1. Periksa console browser untuk error JavaScript
2. Pastikan CSRF token valid
3. Periksa route sudah terdaftar dengan benar

## 📝 Catatan Penting

1. **Timestamp Required**: Kolom `created_at` diperlukan untuk menampilkan waktu pengajuan izin
2. **One Wali Kelas Per Kelas**: Satu kelas hanya bisa punya satu wali kelas
3. **File Upload**: Dokumen pendukung disimpan di `writable/uploads/izin/`
4. **Status Flow**: Izin hanya bisa disetujui/ditolak jika statusnya "pending"
5. **Soft Delete**: Jika siswa dihapus, izinnya juga akan dihapus (CASCADE)
6. **Audit Trail**: Field `disetujui_oleh` menyimpan siapa yang menyetujui/menolak

## 🔄 Integrasi dengan Fitur Lain

### Dashboard Wali Kelas
- Menampilkan jumlah izin pending di card statistik
- Quick link ke halaman izin

### Absensi
- Izin yang disetujui dapat digunakan untuk menandai siswa izin di absensi
- Guru dapat melihat siswa yang memiliki izin saat mengisi absensi

### Laporan
- Izin siswa masuk dalam laporan kehadiran
- Statistik izin ditampilkan di laporan wali kelas

## 📞 Support

Jika menemukan bug atau ingin menambahkan fitur:
1. Periksa log di `writable/logs/`
2. Cek dokumentasi CodeIgniter 4
3. Review kode di controller dan model terkait

---

**Dibuat**: 2026-01-11  
**Versi**: 1.0  
**Status**: ✅ Selesai dan Siap Digunakan
