# Perbaikan Error "Uncaught SyntaxError: Unexpected token '<'"

## Masalah
Error ini terjadi karena browser mencoba memuat resource (favicon.ico, images) menggunakan absolute path (`/favicon.ico`) yang menyebabkan 404 error dan mengembalikan HTML error page, bukan file yang diharapkan.

## Penyebab
- Path absolut `/favicon.ico` dan `/assets/images/*.png` tidak kompatibel dengan baseURL aplikasi
- Ketika aplikasi diakses dari subdirectory atau dengan baseURL tertentu, path absolut tidak resolve dengan benar

## Solusi yang Diterapkan
Mengubah semua absolute path menjadi menggunakan helper `base_url()`:

### File yang diperbaiki:
1. `app/Views/templates/main_layout.php` - favicon path
2. `app/Views/templates/auth_layout.php` - favicon path  
3. `app/Views/welcome_message.php` - favicon path
4. `app/Views/guru/laporan/print.php` - logo sekolah & provinsi
5. `app/Views/guru/jurnal/print.php` - logo sekolah & provinsi
6. `app/Views/admin/laporan/print_absensi_detail.php` - logo sekolah & provinsi

### Perubahan:
```php
// SEBELUM
<link rel="shortcut icon" href="/favicon.ico">
<img src="<?= base_url('/assets/images/sekolah.png') ?>">

// SESUDAH  
<link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
<img src="<?= base_url('assets/images/sekolah.png') ?>">
```

## Cara Menjalankan Aplikasi
```bash
# Dari root project
php spark serve

# Atau jika menggunakan Apache/Nginx, pastikan document root mengarah ke folder 'public'
```

## Verifikasi
- Buka browser console (F12) dan pastikan tidak ada error "Unexpected token '<'"
- Pastikan favicon muncul di tab browser
- Pastikan logo muncul saat print laporan
