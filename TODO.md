# TODO: Missing Controllers Based on Routes.php

## Admin Controllers

- [x] Admin/LaporanController.php (routes: /admin/laporan/absensi, /admin/laporan/statistik)

## Guru Controllers

- [x] Guru/JadwalController.php (routes: /guru/jadwal)
- [x] Guru/JurnalController.php (routes: /guru/jurnal/tambah/:num, /guru/jurnal/simpan, /guru/jurnal/edit/:num, /guru/jurnal/update/:num)
- [x] Guru/LaporanController.php (routes: /guru/laporan)

## Wali Kelas Controllers

- [x] WaliKelas/DashboardController.php (routes: /walikelas/dashboard)
- [x] WaliKelas/SiswaController.php (routes: /walikelas/siswa)
- [x] WaliKelas/AbsensiController.php (routes: /walikelas/absensi)
- [x] WaliKelas/IzinController.php (routes: /walikelas/izin, /walikelas/izin/setujui/:num, /walikelas/izin/tolak/:num)
- [x] WaliKelas/LaporanController.php (routes: /walikelas/laporan)

## Siswa Controllers

- [x] Siswa/DashboardController.php (routes: /siswa/dashboard)
- [x] Siswa/JadwalController.php (routes: /siswa/jadwal)
- [x] Siswa/AbsensiController.php (routes: /siswa/absensi)
- [x] Siswa/IzinController.php (routes: /siswa/izin, /siswa/izin/tambah, /siswa/izin/simpan)
- [x] Siswa/ProfilController.php (routes: /siswa/profil)

## Other Controllers

- [ ] ProfileController.php (routes: /profile/, /profile/update)

## Notes

- All controllers need to extend BaseController
- Include proper authentication checks using session
- Create corresponding view files
- Test all routes after creation
