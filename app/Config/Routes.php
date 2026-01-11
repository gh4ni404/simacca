<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;
use Config\Services;


// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}
/**
 * -----------------------------------------------------------------
 * Router Setup
 * -----------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// if you don't want to define all routes, pleaseuser the Auto Routing (Improved).
// Set `$routes->setAutoRoute(false)` to disable `Auto Routing (legacy)
$routes->setAutoRoute(false);

/**
 * -----------------------------------------------------------------
 * Route Definitions
 * -----------------------------------------------------------------
 */

// we  get a performance increase by specifying the default
// route since we don't have to scan directories.

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('health', static function () {
    return 'OK';
});

// Auth Routes
$routes->group('', ['filter' => 'guest'], function ($routes) {
    $routes->get('/login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login/process', 'AuthController::processLogin');
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('forgot-password/process', 'AuthController::processForgotPassword');
    $routes->get('reset-password/(:any)', 'AuthController::resetPassword/$1');
    $routes->post('reset-password/process', 'AuthController::processResetPassword');
});

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('logout', 'AuthController::logout', ['as' => 'logout']);
    $routes->get('change-password', 'AuthController::changePassword');
    $routes->post('change-password/process', 'AuthController::processChangePassword');
    $routes->get('access-denied', 'AuthController::accessDenied');
});

// Admin Routes
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index', ['filter' => 'role:admin', 'as' => 'admin.dashboard']);
    $routes->post('dashboard/quick-action', 'Admin\DashboardController::quickActions', ['filter' => 'role:admin']);

    // Guru Management
    // Dalam group 'admin'
    $routes->get('guru', 'Admin\GuruController::index', ['filter' => 'role:admin']);
    $routes->get('guru/tambah', 'Admin\GuruController::create', ['filter' => 'role:admin']);
    $routes->post('guru/simpan', 'Admin\GuruController::store', ['filter' => 'role:admin']);
    $routes->get('guru/edit/(:num)', 'Admin\GuruController::edit/$1', ['filter' => 'role:admin']);
    $routes->post('guru/update/(:num)', 'Admin\GuruController::update/$1', ['filter' => 'role:admin']);
    $routes->get('guru/hapus/(:num)', 'Admin\GuruController::delete/$1', ['filter' => 'role:admin']);
    $routes->get('guru/detail/(:num)', 'Admin\GuruController::show/$1', ['filter' => 'role:admin']);
    $routes->get('guru/nonaktifkan/(:num)', 'Admin\GuruController::changeStatus/$1', ['filter' => 'role:admin']);
    $routes->get('guru/aktifkan/(:num)', 'Admin\GuruController::changeStatus/$1', ['filter' => 'role:admin']);
    $routes->post('guru/check-nip', 'Admin\GuruController::checkNip', ['filter' => 'role:admin']);
    $routes->post('guru/check-username', 'Admin\GuruController::checkUsername', ['filter' => 'role:admin']);
    $routes->get('guru/export', 'Admin\GuruController::export', ['filter' => 'role:admin']);
    $routes->get('guru/import', 'Admin\GuruController::import', ['filter' => 'role:admin']);
    $routes->post('guru/process-import', 'Admin\GuruController::processImport', ['filter' => 'role:admin']);
    $routes->get('guru/download-template', 'Admin\GuruController::downloadTemplate', ['filter' => 'role:admin']);

    // Siswa Management
    // Dalam group 'admin'
    $routes->get('siswa', 'Admin\SiswaController::index', ['filter' => 'role:admin']);
    $routes->get('siswa/tambah', 'Admin\SiswaController::create', ['filter' => 'role:admin']);
    $routes->post('siswa/simpan', 'Admin\SiswaController::store', ['filter' => 'role:admin']);
    $routes->get('siswa/edit/(:num)', 'Admin\SiswaController::edit/$1', ['filter' => 'role:admin']);
    $routes->post('siswa/update/(:num)', 'Admin\SiswaController::update/$1', ['filter' => 'role:admin']);
    $routes->get('siswa/hapus/(:num)', 'Admin\SiswaController::delete/$1', ['filter' => 'role:admin']);
    $routes->get('siswa/detail/(:num)', 'Admin\SiswaController::show/$1', ['filter' => 'role:admin']);
    $routes->get('siswa/nonaktifkan/(:num)', 'Admin\SiswaController::changeStatus/$1', ['filter' => 'role:admin']);
    $routes->get('siswa/aktifkan/(:num)', 'Admin\SiswaController::changeStatus/$1', ['filter' => 'role:admin']);
    $routes->post('siswa/check-nis', 'Admin\SiswaController::checkNis', ['filter' => 'role:admin']);
    $routes->post('siswa/check-username', 'Admin\SiswaController::checkUsername', ['filter' => 'role:admin']);
    $routes->get('siswa/export', 'Admin\SiswaController::export', ['filter' => 'role:admin']);
    $routes->get('siswa/import', 'Admin\SiswaController::import', ['filter' => 'role:admin']);
    $routes->post('siswa/process-import', 'Admin\SiswaController::processImport', ['filter' => 'role:admin']);
    $routes->get('siswa/download-template', 'Admin\SiswaController::downloadTemplate', ['filter' => 'role:admin']);
    $routes->post('siswa/bulk-action', 'Admin\SiswaController::bulkAction', ['filter' => 'role:admin']);

    // Kelas Management
    $routes->get('kelas', 'Admin\KelasController::index', ['filter' => 'role:admin']);
    $routes->get('kelas/tambah', 'Admin\KelasController::create', ['filter' => 'role:admin']);
    $routes->post('kelas/simpan', 'Admin\KelasController::store', ['filter' => 'role:admin']);
    $routes->get('kelas/edit/(:num)', 'Admin\KelasController::edit/$1', ['filter' => 'role:admin']);
    $routes->post('kelas/update/(:num)', 'Admin\KelasController::update/$1', ['filter' => 'role:admin']);
    $routes->get('kelas/hapus/(:num)', 'Admin\KelasController::delete/$1', ['filter' => 'role:admin']);
    $routes->get('kelas/detail/(:num)', 'Admin\KelasController::show/$1', ['filter' => 'role:admin']);
    $routes->post('kelas/assign-wali-kelas/(:num)', 'Admin\KelasController::assignWaliKelas/$1', ['filter' => 'role:admin']);
    $routes->post('kelas/remove-wali-kelas/(:num)', 'Admin\KelasController::removeWaliKelas/$1', ['filter' => 'role:admin']);
    $routes->post('kelas/move-siswa/(:num)', 'Admin\KelasController::moveSiswa/$1', ['filter' => 'role:admin']);
    $routes->get('kelas/export', 'Admin\KelasController::export', ['filter' => 'role:admin']);
    $routes->get('kelas/statistics', 'Admin\KelasController::statistics', ['filter' => 'role:admin']);

    // Mata Pelajaran Management
    $routes->get('mata-pelajaran', 'Admin\MataPelajaranController::index', ['filter' => 'role:admin']);
    $routes->get('mata-pelajaran/tambah', 'Admin\MataPelajaranController::create', ['filter' => 'role:admin']);
    $routes->post('mata-pelajaran/simpan', 'Admin\MataPelajaranController::store', ['filter' => 'role:admin']);
    $routes->get('mata-pelajaran/edit/(:num)', 'Admin\MataPelajaranController::edit/$1', ['filter' => 'role:admin']);
    $routes->post('mata-pelajaran/update/(:num)', 'Admin\MataPelajaranController::update/$1', ['filter' => 'role:admin']);
    $routes->get('mata-pelajaran/hapus/(:num)', 'Admin\MataPelajaranController::delete/$1', ['filter' => 'role:admin']);

    // Jadwal Management
    $routes->get('jadwal', 'Admin\JadwalController::index', ['filter' => 'role:admin']);
    $routes->get('jadwal/tambah', 'Admin\JadwalController::create', ['filter' => 'role:admin']);
    $routes->post('jadwal/simpan', 'Admin\JadwalController::store', ['filter' => 'role:admin']);
    $routes->get('jadwal/edit/(:num)', 'Admin\JadwalController::edit/$1', ['filter' => 'role:admin']);
    $routes->post('jadwal/update/(:num)', 'Admin\JadwalController::update/$1', ['filter' => 'role:admin']);
    $routes->get('jadwal/hapus/(:num)', 'Admin\JadwalController::delete/$1', ['filter' => 'role:admin']);
    $routes->post('jadwal/checkConflict', 'Admin\JadwalController::checkConflict', ['filter' => 'role:admin']);
    $routes->get('jadwal/export', 'Admin\JadwalController::export', ['filter' => 'role:admin']);

    // Laporan
    $routes->get('laporan/absensi', 'Admin\LaporanController::absensi', ['filter' => 'role:admin']);
    $routes->get('laporan/statistik', 'Admin\LaporanController::statistik', ['filter' => 'role:admin']);
});

// Guru Routes
$routes->group('guru', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Guru\DashboardController::index', ['filter' => 'role:guru_mapel', 'as' => 'guru.dashboard']);
    $routes->post('dashboard/quick-action', 'Guru\DashboardController::quickAction', ['filter' => 'role:guru_mapel']);
    $routes->get('jadwal', 'Guru\JadwalController::index', ['filter' => 'role:guru_mapel']);
    // Absensi Routes
    $routes->get('absensi', 'Guru\AbsensiController::index', ['filter' => 'role:guru_mapel']);
    $routes->get('absensi/tambah', 'Guru\AbsensiController::create', ['filter' => 'role:guru_mapel']);
    $routes->post('absensi/simpan', 'Guru\AbsensiController::store', ['filter' => 'role:guru_mapel']);
    $routes->get('absensi/show/(:num)', 'Guru\AbsensiController::show/$1', ['filter' => 'role:guru_mapel']);
    $routes->get('absensi/edit/(:num)', 'Guru\AbsensiController::edit/$1', ['filter' => 'role:guru_mapel']);
    $routes->post('absensi/update/(:num)', 'Guru\AbsensiController::update/$1', ['filter' => 'role:guru_mapel']);
    $routes->get('absensi/delete/(:num)', 'Guru\AbsensiController::delete/$1', ['filter' => 'role:guru_mapel']);
    $routes->get('absensi/print/(:num)', 'Guru\AbsensiController::print/$1', ['filter' => 'role:guru_mapel']);
    $routes->get('absensi/getSiswaByKelas', 'Guru\AbsensiController::getSiswaByKelas', ['filter' => 'role:guru_mapel']);
    $routes->get('absensi/getJadwalByHari', 'Guru\AbsensiController::getJadwalByHari', ['filter' => 'role:guru_mapel']);

    $routes->get('jurnal', 'Guru\JurnalController::index', ['filter' => 'role:guru_mapel']);
    $routes->get('jurnal/tambah/(:num)', 'Guru\JurnalController::create/$1', ['filter' => 'role:guru_mapel']);
    $routes->post('jurnal/simpan', 'Guru\JurnalController::store', ['filter' => 'role:guru_mapel']);
    $routes->get('jurnal/show/(:num)', 'Guru\JurnalController::show/$1', ['filter' => 'role:guru_mapel']);
    $routes->get('jurnal/print/(:num)', 'Guru\JurnalController::print/$1', ['filter' => 'role:guru_mapel']);
    $routes->get('jurnal/edit/(:num)', 'Guru\JurnalController::edit/$1', ['filter' => 'role:guru_mapel']);
    $routes->post('jurnal/update/(:num)', 'Guru\JurnalController::update/$1', ['filter' => 'role:guru_mapel']);
    $routes->get('laporan', 'Guru\LaporanController::index', ['filter' => 'role:guru_mapel']);
});

// Wali Kelas Routes
$routes->group('walikelas', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'WaliKelas\DashboardController::index', ['filter' => 'role:wali_kelas', 'as' => 'walikelas.dashboard']);
    $routes->get('siswa', 'WaliKelas\SiswaController::index', ['filter' => 'role:wali_kelas']);
    $routes->get('absensi', 'WaliKelas\AbsensiController::index', ['filter' => 'role:wali_kelas']);
    $routes->get('izin', 'WaliKelas\IzinController::index', ['filter' => 'role:wali_kelas']);
    $routes->post('izin/setujui/(:num)', 'WaliKelas\IzinController::approve/$1', ['filter' => 'role:wali_kelas']);
    $routes->post('izin/tolak/(:num)', 'WaliKelas\IzinController::reject/$1', ['filter' => 'role:wali_kelas']);
    // $routes->get('absensi/edit/(:num)', 'WaliKelas\AbsensiController::edit/$1', ['filter'=> 'role:wali_kelas']);
    // $routes->post('absensi/update/(:num)', 'WaliKelas\AbsensiController::update/$1', ['filter'=> 'role:wali_kelas']);
    // $routes->get('jurnal', 'WaliKelas\JurnalController::index', ['filter'=> 'role:wali_kelas']);
    // $routes->get('jurnal/tambah/(:num)', 'WaliKelas\JurnalController::create/$1', ['filter'=> 'role:wali_kelas']);
    // $routes->post('jurnal/simpan', 'WaliKelas\JurnalController::store', ['filter'=> 'role:wali_kelas']);
    // $routes->get('jurnal/edit/(:num)', 'WaliKelas\JurnalController::edit/$1', ['filter'=> 'role:wali_kelas']);
    // $routes->post('jurnal/update/(:num)', 'WaliKelas\JurnalController::update/$1', ['filter'=> 'role:wali_kelas']);
    $routes->get('laporan', 'WaliKelas\LaporanController::index', ['filter' => 'role:wali_kelas']);
});

// Siswa Routes
$routes->group('siswa', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Siswa\DashboardController::index', ['filter' => 'role:siswa', 'as' => 'siswa.dashboard']);
    $routes->get('jadwal', 'Siswa\JadwalController::index', ['filter' => 'role:siswa']);
    $routes->get('absensi', 'Siswa\AbsensiController::index', ['filter' => 'role:siswa']);
    $routes->get('izin', 'Siswa\IzinController::index', ['filter' => 'role:siswa']);
    $routes->get('izin/tambah', 'Siswa\IzinController::create', ['filter' => 'role:siswa']);
    $routes->post('izin/simpan', 'Siswa\IzinController::store', ['filter' => 'role:siswa']);
    // $routes->get('absensi/edit/(:num)', 'Siswa\AbsensiController::edit/$1', ['filter'=> 'role:siswa']);
    // $routes->post('absensi/update/(:num)', 'Siswa\AbsensiController::update/$1', ['filter'=> 'role:siswa']);
    // $routes->get('jurnal', 'Siswa\JurnalController::index', ['filter'=> 'role:siswa']);
    // $routes->get('jurnal/tambah/(:num)', 'Siswa\JurnalController::create/$1', ['filter'=> 'role:siswa']);
    // $routes->post('jurnal/simpan', 'Siswa\JurnalController::store', ['filter'=> 'role:siswa']);
    // $routes->get('jurnal/edit/(:num)', 'Siswa\JurnalController::edit/$1', ['filter'=> 'role:siswa']);
    // $routes->post('jurnal/update/(:num)', 'Siswa\JurnalController::update/$1', ['filter'=> 'role:siswa']);
    $routes->get('profil', 'Siswa\\ProfilController::index', ['filter' => 'role:siswa']);
    $routes->post('profil/update', 'Siswa\\ProfilController::update', ['filter' => 'role:siswa']);
    $routes->post('profil/change-password', 'Siswa\\ProfilController::changePassword', ['filter' => 'role:siswa']);
});

// Profile Routes (for all roles)
$routes->group('profile', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProfileController::index');
    $routes->post('/update', 'ProfileController::update');
});

// File Routes (for serving uploaded files)
$routes->get('files/jurnal/(:segment)', 'FileController::jurnalFoto/$1');

/**
 * -----------------------------------------------------------------
 * Additional Routing
 * -----------------------------------------------------------------
 * 
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 * 
 * you will have acces to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
