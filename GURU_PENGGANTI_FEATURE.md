# Fitur Guru Pengganti (Piket Pengganti)

## Deskripsi
Fitur ini memungkinkan guru untuk mencatat guru pengganti ketika mengisi absensi. Guru pengganti yang dipilih akan tercatat sebagai "Piket Pengganti" dalam laporan absensi detail.

## Implementasi

### 1. Database
- Field `guru_pengganti_id` sudah tersedia di tabel `absensi` (migrasi: `2026-01-11-183700_AddGuruPenggantiToAbsensi.php`)
- Field ini bersifat opsional (nullable)
- Foreign key ke tabel `guru` dengan ON DELETE SET NULL

### 2. Controller (app/Controllers/Guru/AbsensiController.php)
- **create()**: Menambahkan `$guruList` ke data view
- **store()**: Menyimpan `guru_pengganti_id` dari form input
- **edit()**: Menambahkan `$guruList` ke data view
- **update()**: Memperbarui `guru_pengganti_id` dari form input

### 3. Model (app/Models/AbsensiModel.php)
- **getAbsensiWithDetail()**: Menambahkan join dengan tabel guru untuk mendapatkan `nama_guru_pengganti`
- **getLaporanAbsensiLengkap()**: Sudah include `nama_guru_pengganti`
- **getLaporanAbsensiPerHari()**: Sudah include `nama_guru_pengganti`

### 4. Views
#### Form Input Absensi (app/Views/guru/absensi/create.php)
- Menambahkan dropdown "Guru Pengganti (Opsional)"
- Dropdown berisi list semua guru dengan format: "Nama Lengkap (NIP)"
- Option pertama: "-- Tidak ada pengganti --" (value kosong)

#### Form Edit Absensi (app/Views/guru/absensi/edit.php)
- Menambahkan dropdown "Guru Pengganti (Opsional)"
- Dropdown akan menampilkan guru pengganti yang sudah dipilih sebelumnya (jika ada)

#### Detail Absensi (app/Views/guru/absensi/show.php)
- Menampilkan informasi guru pengganti jika ada
- Ditampilkan dengan badge "Piket Pengganti"
- Icon: user-plus dengan background purple

## Cara Penggunaan

### 1. Input Absensi Baru
1. Guru masuk ke menu Absensi > Tambah Absensi
2. Pilih jadwal mengajar
3. Pada bagian "Detail Absensi", terdapat dropdown "Guru Pengganti (Opsional)"
4. Jika ada guru yang menggantikan, pilih dari dropdown
5. Jika tidak ada, biarkan kosong (pilih "-- Tidak ada pengganti --")
6. Lanjutkan mengisi absensi siswa seperti biasa

### 2. Edit Absensi
1. Buka detail absensi yang ingin diedit
2. Klik tombol "Edit"
3. Pada form edit, dropdown "Guru Pengganti" akan menampilkan pilihan yang sudah dipilih sebelumnya
4. Guru bisa mengubah atau menghapus guru pengganti
5. Simpan perubahan

### 3. Melihat Detail Absensi
- Jika ada guru pengganti, informasi akan ditampilkan di bagian "Informasi Absensi"
- Ditampilkan dengan label "Guru Pengganti" dan badge "Piket Pengganti"

### 4. Laporan Absensi Detail (Admin)
- Guru pengganti akan tercatat dalam laporan absensi detail
- Informasi ini berguna untuk monitoring dan administrasi sekolah

## Keuntungan Fitur
1. **Transparansi**: Tercatat dengan jelas siapa guru pengganti dalam setiap pertemuan
2. **Administrasi**: Memudahkan pencatatan untuk keperluan administrasi dan pelaporan
3. **Monitoring**: Admin dapat memonitor guru yang sering menjadi pengganti
4. **Opsional**: Field bersifat opsional, tidak memaksa guru untuk mengisi jika tidak ada pengganti

## Catatan
- Field guru pengganti bersifat **opsional**, tidak wajib diisi
- Guru pengganti hanya bisa dipilih dari daftar guru yang terdaftar di sistem
- Data guru pengganti akan muncul di laporan absensi detail yang diakses oleh admin
- Jika guru pengganti dihapus dari sistem, field akan otomatis menjadi NULL (tidak error)

## Testing
Untuk menguji fitur ini:
1. Login sebagai guru
2. Buat absensi baru dan pilih guru pengganti
3. Lihat detail absensi, pastikan guru pengganti muncul
4. Edit absensi dan ubah guru pengganti
5. Login sebagai admin dan lihat laporan absensi detail, pastikan guru pengganti tercatat

## File yang Dimodifikasi
1. `app/Controllers/Guru/AbsensiController.php`
2. `app/Models/AbsensiModel.php`
3. `app/Views/guru/absensi/create.php`
4. `app/Views/guru/absensi/edit.php`
5. `app/Views/guru/absensi/show.php`

## Database Migration
- Migration file: `app/Database/Migrations/2026-01-11-183700_AddGuruPenggantiToAbsensi.php`
- Pastikan migration sudah dijalankan dengan: `php spark migrate`
