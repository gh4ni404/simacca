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

    // CSRF Token endpoint for AJAX requests
    $routes->get('csrf-token', static function () {
        return service('response')->setJSON([
            'tokenName'  => config('Security')->tokenName,
            'tokenValue' => csrf_hash(),
        ]);
    });
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
    $routes->post('guru/update-role/(:num)', 'Admin\GuruController::updateRoles/$1', ['filter' => 'role:admin']);
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
    $routes->get('siswa/check-nis-batch', 'Admin\SiswaController::checkNisBatch', ['filter' => 'role:admin']);
    $routes->post('siswa/check-username', 'Admin\SiswaController::checkUsername', ['filter' => 'role:admin']);
    $routes->get('siswa/export', 'Admin\SiswaController::export', ['filter' => 'role:admin']);
    $routes->get('siswa/import', 'Admin\SiswaController::import', ['filter' => 'role:admin']);
    $routes->post('siswa/process-import', 'Admin\SiswaController::processImport', ['filter' => 'role:admin']);
    $routes->get('siswa/download-template', 'Admin\SiswaController::downloadTemplate', ['filter' => 'role:admin']);
    $routes->post('siswa/bulk-action', 'Admin\SiswaController::bulkAction', ['filter' => 'role:admin']);
    $routes->get('siswa/get-all-ids', 'Admin\SiswaController::getAllIds', ['filter' => 'role:admin']);

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
    $routes->get('jadwal', 'Admin\\JadwalController::index', ['filter' => 'role:admin']);
    $routes->get('jadwal/tambah', 'Admin\\JadwalController::create', ['filter' => 'role:admin']);
    $routes->post('jadwal/simpan', 'Admin\\JadwalController::store', ['filter' => 'role:admin']);
    $routes->get('jadwal/edit/(:num)', 'Admin\\JadwalController::edit/$1', ['filter' => 'role:admin']);
    $routes->post('jadwal/update/(:num)', 'Admin\\JadwalController::update/$1', ['filter' => 'role:admin']);
    $routes->get('jadwal/hapus/(:num)', 'Admin\\JadwalController::delete/$1', ['filter' => 'role:admin']);
    $routes->post('jadwal/checkConflict', 'Admin\\JadwalController::checkConflict', ['filter' => 'role:admin']);
    $routes->get('jadwal/import', 'Admin\\JadwalController::import', ['filter' => 'role:admin']);
    $routes->post('jadwal/process-import', 'Admin\\JadwalController::processImport', ['filter' => 'role:admin']);
    $routes->get('jadwal/download-template', 'Admin\\JadwalController::downloadTemplate', ['filter' => 'role:admin']);
    $routes->get('jadwal/export', 'Admin\\JadwalController::export', ['filter' => 'role:admin']);

    // Absensi Management (Unlock Feature)
    $routes->get('absensi', 'Admin\AbsensiController::index', ['filter' => 'role:admin']);
    $routes->get('absensi/unlock/(:num)', 'Admin\AbsensiController::unlock/$1', ['filter' => 'role:admin']);
    $routes->post('absensi/bulk-unlock', 'Admin\AbsensiController::bulkUnlock', ['filter' => 'role:admin']);

    // Absensi Guru Management
    $routes->get('absensi-guru', 'Admin\AbsensiGuruController::index', ['filter' => 'role:admin']);
    $routes->get('absensi-guru/laporan', 'Admin\AbsensiGuruController::laporan', ['filter' => 'role:admin']);
    $routes->get('absensi-guru/detail/(:num)', 'Admin\AbsensiGuruController::detail/$1', ['filter' => 'role:admin']);
    $routes->post('absensi-guru/update-status', 'Admin\AbsensiGuruController::updateStatus', ['filter' => 'role:admin']);
    $routes->get('absensi-guru/export-excel', 'Admin\AbsensiGuruController::exportExcel', ['filter' => 'role:admin']);

    // Pembimbing PKL
    $routes->get('pembimbing-pkl', 'Admin\PembimbingPklController::index', ['filter' => 'role:admin']);
    $routes->get('pembimbing-pkl/tambah', 'Admin\PembimbingPklController::create', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/simpan', 'Admin\PembimbingPklController::store', ['filter' => 'role:admin']);
    $routes->get('pembimbing-pkl/edit/(:num)', 'Admin\PembimbingPklController::edit/$1', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/update/(:num)', 'Admin\PembimbingPklController::update/$1', ['filter' => 'role:admin']);
    $routes->get('pembimbing-pkl/hapus/(:num)', 'Admin\PembimbingPklController::delete/$1', ['filter' => 'role:admin']);

    // Tempat PKL
    $routes->get('pembimbing-pkl/tempat-pkl', 'Admin\PembimbingPklController::tempatPkl', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/tempat-pkl/simpan', 'Admin\PembimbingPklController::storeTempatPkl', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/tempat-pkl/update/(:num)', 'Admin\PembimbingPklController::updateTempatPkl/$1', ['filter' => 'role:admin']);
    $routes->get('pembimbing-pkl/tempat-pkl/hapus/(:num)', 'Admin\PembimbingPklController::deleteTempatPkl/$1', ['filter' => 'role:admin']);

    // Siswa PKL
    $routes->get('pembimbing-pkl/siswa-pkl', 'Admin\PembimbingPklController::siswaPkl', ['filter' => 'role:admin']);
    $routes->get('pembimbing-pkl/siswa-pkl/tambah', 'Admin\PembimbingPklController::siswaPklCreate', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/siswa-pkl/simpan', 'Admin\PembimbingPklController::siswaPklStore', ['filter' => 'role:admin']);
    $routes->get('pembimbing-pkl/siswa-pkl/hapus/(:num)', 'Admin\PembimbingPklController::siswaPklDelete/$1', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/siswa-pkl/bulk-hapus', 'Admin\PembimbingPklController::siswaPklBulkDelete', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/siswa-pkl/batch-simpan', 'Admin\PembimbingPklController::siswaPklBatchStore', ['filter' => 'role:admin']);

    // AJAX
    $routes->post('pembimbing-pkl/get-pembimbing-by-tempat-pkl', 'Admin\PembimbingPklController::getPembimbingByTempatPkl', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/get-siswa-pkl-by-tempat-pkl', 'Admin\PembimbingPklController::getSiswaPklByTempatPkl', ['filter' => 'role:admin']);
    $routes->post('pembimbing-pkl/get-siswa-xii-unplaced', 'Admin\PembimbingPklController::getSiswaXIIUnplaced', ['filter' => 'role:admin']);

    // Pengaturan
    $routes->get('pengaturan', 'Admin\PengaturanController::index', ['filter' => 'role:admin']);
    $routes->post('pengaturan/update', 'Admin\PengaturanController::update', ['filter' => 'role:admin']);
    $routes->post('pengaturan/rollover', 'Admin\PengaturanController::rollover', ['filter' => 'role:admin']);
    $routes->post('pengaturan/revert', 'Admin\PengaturanController::revert', ['filter' => 'role:admin']);
    $routes->post('pengaturan/update-jurnal-pkl-start', 'Admin\PengaturanController::updateJurnalPklStart', ['filter' => 'role:admin']);
    $routes->post('pengaturan/update-jurnal-pkl-period', 'Admin\PengaturanController::updateJurnalPklPeriod', ['filter' => 'role:admin']);

    // Arsip Jurnal PKL (Task-Oriented)
    $routes->get('jurnal-pkl-archive', 'Admin\PklArchiveController::index', ['filter' => 'role:admin']);

    // Master Task PKL
    $routes->get('pkl-task', 'Admin\PklTaskController::index', ['filter' => 'role:admin']);
    $routes->post('pkl-task/nonaktifkan/(:num)', 'Admin\PklTaskController::nonaktifkan/$1', ['filter' => 'role:admin']);
    $routes->post('pkl-task/aktifkan/(:num)', 'Admin\PklTaskController::aktifkan/$1', ['filter' => 'role:admin']);
    $routes->post('pkl-task/hapus/(:num)', 'Admin\PklTaskController::hapus/$1', ['filter' => 'role:admin']);
    $routes->post('pkl-task/bulk-action', 'Admin\PklTaskController::bulkAction', ['filter' => 'role:admin']);

    // Kategori PKL
    $routes->get('pkl-categories', 'Admin\PklCategoryController::index', ['filter' => 'role:admin']);
    $routes->post('pkl-categories/simpan', 'Admin\PklCategoryController::store', ['filter' => 'role:admin']);
    $routes->post('pkl-categories/update/(:num)', 'Admin\PklCategoryController::update/$1', ['filter' => 'role:admin']);
    $routes->post('pkl-categories/hapus/(:num)', 'Admin\PklCategoryController::delete/$1', ['filter' => 'role:admin']);

    // Mapping Kategori PKL
    $routes->get('kategori-pkl-mapping', 'Admin\KategoriPklMappingController::index', ['filter' => 'role:admin']);
    $routes->post('kategori-pkl-mapping/simpan', 'Admin\KategoriPklMappingController::store', ['filter' => 'role:admin']);
    $routes->post('kategori-pkl-mapping/get-mapped', 'Admin\KategoriPklMappingController::getMappedKategori', ['filter' => 'role:admin']);
    $routes->get('kategori-pkl-mapping/summary', 'Admin\KategoriPklMappingController::getMappingSummary', ['filter' => 'role:admin']);

    // Absensi PKL
    $routes->get('absensi-pkl', 'Admin\AbsensiPklController::index', ['filter' => 'role:admin']);
    $routes->get('absensi-pkl/detail/(:num)', 'Admin\AbsensiPklController::show/$1', ['filter' => 'role:admin']);
    $routes->get('absensi-pkl/rekap/(:num)', 'Admin\AbsensiPklController::rekap/$1', ['filter' => 'role:admin']);

    // Laporan
    $routes->get('laporan/absensi', 'Admin\LaporanController::absensi', ['filter' => 'role:admin']);
    $routes->get('laporan/absensi-detail', 'Admin\LaporanController::absensiDetail', ['filter' => 'role:admin']);
    $routes->get('laporan/absensi-detail/print', 'Admin\LaporanController::printAbsensiDetail', ['filter' => 'role:admin']);
    $routes->get('laporan/statistik', 'Admin\LaporanController::statistik', ['filter' => 'role:admin']);
});

// Guru Routes (accessible by guru_mapel and wakakur who teach)
$routes->group('guru', ['filter' => 'role:guru_mapel,wakakur'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Guru\DashboardController::index', ['as' => 'guru.dashboard']);
    $routes->post('dashboard/quick-action', 'Guru\DashboardController::quickAction');
    
    // Jadwal
    $routes->get('jadwal', 'Guru\JadwalController::index', ['as' => 'guru.jadwal']);
    
    // Absensi Routes
    $routes->get('absensi', 'Guru\AbsensiController::index', ['as' => 'guru.absensi']);
    $routes->get('absensi/kelas/(:num)', 'Guru\AbsensiController::kelas/$1');
    $routes->get('absensi/tambah', 'Guru\AbsensiController::create');
    $routes->post('absensi/simpan', 'Guru\AbsensiController::store');
    $routes->get('absensi/show/(:num)', 'Guru\AbsensiController::show/$1');
    $routes->get('absensi/edit/(:num)', 'Guru\AbsensiController::edit/$1');
    $routes->post('absensi/update/(:num)', 'Guru\AbsensiController::update/$1');
    $routes->get('absensi/delete/(:num)', 'Guru\AbsensiController::delete/$1');
    $routes->get('absensi/print/(:num)', 'Guru\AbsensiController::print/$1');
    $routes->get('absensi/getSiswaByKelas', 'Guru\AbsensiController::getSiswaByKelas');
    $routes->get('absensi/getJadwalByHari', 'Guru\AbsensiController::getJadwalByHari');
    $routes->get('absensi/getNextPertemuanByJadwal', 'Guru\AbsensiController::getNextPertemuanByJadwal');

    // Jurnal Routes
    $routes->get('jurnal', 'Guru\JurnalController::index', ['as' => 'guru.jurnal']);
    $routes->get('jurnal/preview/(:num)/(:num)', 'Guru\JurnalController::preview/$1/$2');
    $routes->get('jurnal/tambah/(:num)', 'Guru\JurnalController::create/$1');
    $routes->post('jurnal/simpan', 'Guru\JurnalController::store');
    $routes->get('jurnal/show/(:num)', 'Guru\JurnalController::show/$1');
    $routes->get('jurnal/print/(:num)', 'Guru\JurnalController::print/$1');
    $routes->get('jurnal/edit/(:num)', 'Guru\\JurnalController::edit/$1');
    $routes->match(['POST', 'PUT'], 'jurnal/update/(:num)', 'Guru\\JurnalController::update/$1');
    
    // Laporan Routes
    $routes->get('laporan', 'Guru\LaporanController::index', ['as' => 'guru.laporan']);
    $routes->get('laporan/print', 'Guru\LaporanController::print');
    
    // Jurnal PKL (Verifikasi Pembimbing) - Task-Oriented
    $routes->get('jurnal-pkl', 'Guru\PklController::index', ['as' => 'guru.jurnal_pkl']);
    $routes->post('jurnal-pkl/verify/(:num)', 'Guru\PklController::verify/$1');
    $routes->post('jurnal-pkl/batal-verifikasi/(:num)', 'Guru\PklController::cancelVerification/$1');
    $routes->get('jurnal-pkl/detail/(:num)', 'Guru\PklController::detail/$1');
    $routes->get('jurnal-pkl/tasks/(:num)', 'Guru\PklController::getTasksBySiswa/$1');
    $routes->get('jurnal-pkl/filtered-progress/(:num)', 'Guru\PklController::getFilteredProgress/$1');
    $routes->get('jurnal-pkl/week-info', 'Guru\PklController::getWeekInfo');

    // Absensi PKL (Pembimbing)
    $routes->get('absensi-pkl', 'Guru\AbsensiPklController::index');
    $routes->get('absensi-pkl/tambah', 'Guru\AbsensiPklController::create');
    $routes->post('absensi-pkl/simpan', 'Guru\AbsensiPklController::store');
    $routes->get('absensi-pkl/show/(:num)', 'Guru\AbsensiPklController::show/$1');
    $routes->get('absensi-pkl/edit/(:num)', 'Guru\AbsensiPklController::edit/$1');
    $routes->post('absensi-pkl/update/(:num)', 'Guru\AbsensiPklController::update/$1');
    $routes->get('absensi-pkl/hapus/(:num)', 'Guru\AbsensiPklController::delete/$1');
    $routes->get('absensi-pkl/get-siswa-by-pembimbing', 'Guru\AbsensiPklController::getSiswaByPembimbing');

    // Absensi Guru (Self Check-in/Check-out)
    $routes->get('absensi-guru', 'Guru\AbsensiGuruController::index', ['as' => 'guru.absensi_guru']);
    $routes->post('absensi-guru/check-in', 'Guru\AbsensiGuruController::checkIn');
    $routes->post('absensi-guru/check-out', 'Guru\AbsensiGuruController::checkOut');
    $routes->get('absensi-guru/history', 'Guru\AbsensiGuruController::history');
    $routes->get('absensi-guru/show/(:num)', 'Guru\AbsensiGuruController::show/$1');
    $routes->get('absensi-guru/camera', 'Guru\AbsensiGuruController::camera');
});

// Wali Kelas Routes
$routes->group('walikelas', ['filter' => 'role:wali_kelas'], function ($routes) {
    $routes->get('dashboard', 'WaliKelas\DashboardController::index', ['as' => 'walikelas.dashboard']);
    $routes->get('siswa', 'WaliKelas\SiswaController::index', ['as' => 'walikelas.siswa']);
    $routes->get('absensi', 'WaliKelas\AbsensiController::index', ['as' => 'walikelas.absensi']);
    $routes->get('izin', 'WaliKelas\IzinController::index', ['as' => 'walikelas.izin']);
    $routes->post('izin/setujui/(:num)', 'WaliKelas\IzinController::approve/$1');
    $routes->post('izin/tolak/(:num)', 'WaliKelas\IzinController::reject/$1');
    $routes->get('laporan', 'WaliKelas\LaporanController::index', ['as' => 'walikelas.laporan']);
});

// Wakakur Routes (Unique administrative features only)
// Note: Wakakur can access Guru routes (/guru/*) for teaching features (absensi, jurnal, jadwal)
$routes->group('wakakur', ['filter' => 'role:wakakur'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Wakakur\DashboardController::index', ['as' => 'wakakur.dashboard']);
    
    // Student Management (school-wide access)
    $routes->get('siswa', 'Wakakur\SiswaController::index', ['as' => 'wakakur.siswa']);
    
    // Permission Management (school-wide access)
    $routes->get('izin', 'Wakakur\IzinController::index', ['as' => 'wakakur.izin']);
    $routes->post('izin/setujui/(:num)', 'Wakakur\IzinController::approve/$1');
    $routes->post('izin/tolak/(:num)', 'Wakakur\IzinController::reject/$1');
    
    // Detailed Reports (school-wide administrative reports)
    $routes->get('laporan', 'Wakakur\LaporanController::index', ['as' => 'wakakur.laporan']);
    $routes->get('laporan/print', 'Wakakur\LaporanController::print');
    
    // Absensi Guru Monitoring
    $routes->get('absensi-guru', 'Wakakur\AbsensiGuruController::index', ['as' => 'wakakur.absensi_guru']);
    $routes->get('absensi-guru/laporan', 'Wakakur\AbsensiGuruController::laporan');
    $routes->get('absensi-guru/detail/(:num)', 'Wakakur\AbsensiGuruController::detail/$1');
    $routes->get('absensi-guru/export-excel', 'Wakakur\AbsensiGuruController::exportExcel');
});

// Siswa Routes
$routes->group('siswa', ['filter' => 'role:siswa'], function ($routes) {
    $routes->get('dashboard', 'Siswa\DashboardController::index', ['as' => 'siswa.dashboard']);
    $routes->get('jadwal', 'Siswa\JadwalController::index', ['as' => 'siswa.jadwal']);
    $routes->get('absensi', 'Siswa\AbsensiController::index', ['as' => 'siswa.absensi']);
    $routes->get('izin', 'Siswa\IzinController::index', ['as' => 'siswa.izin']);
    $routes->get('izin/tambah', 'Siswa\IzinController::create');
    $routes->post('izin/simpan', 'Siswa\IzinController::store');
    $routes->get('profil', 'Siswa\ProfilController::index', ['as' => 'siswa.profil']);
    $routes->post('profil/update', 'Siswa\ProfilController::update');
    $routes->post('profil/change-password', 'Siswa\ProfilController::changePassword');

    // Jurnal PKL (Task-Oriented) - Hanya untuk Siswa Kelas 12
    $routes->group('jurnal-pkl', ['filter' => 'kelas12'], function ($routes) {
        $routes->get('', 'Siswa\PklController::index', ['as' => 'siswa.jurnal_pkl']);
        $routes->get('tambah', 'Siswa\PklController::create');
        $routes->post('simpan', 'Siswa\PklController::store');
        $routes->get('get-task-langkah-kerja', 'Siswa\PklController::getTaskLangkahKerja');
        $routes->get('get-template-langkah-kerja', 'Siswa\PklController::getTemplateLangkahKerja');
        $routes->get('task/(:num)', 'Siswa\PklController::taskDetail/$1');
        $routes->get('hari/(:any)', 'Siswa\PklController::dayDetail/$1');
        $routes->post('kirim/(:num)', 'Siswa\PklController::submitProgress/$1');
        $routes->get('edit-progress/(:num)', 'Siswa\PklController::editProgress/$1');
        $routes->post('update-progress/(:num)', 'Siswa\PklController::updateProgressData/$1');
        $routes->post('hapus-progress/(:num)', 'Siswa\PklController::deleteProgress/$1');
        $routes->get('cetak-jurnal/(:num)/(:num)', 'Siswa\PklController::printJurnal/$1/$2');
        $routes->get('cetak-catatan/(:any)/(:num)', 'Siswa\PklController::printCatatan/$1/$2');
        $routes->get('cetak-catatan/(:any)', 'Siswa\PklController::printCatatan/$1');
        $routes->get('week-readiness', 'Siswa\PklController::getWeeksReadiness');
        $routes->post('selesaikan-task/(:num)', 'Siswa\PklController::selesaikanTask/$1');
    });

    // Absensi PKL (Siswa)
    $routes->get('absensi-pkl', 'Siswa\AbsensiPklController::index');
    $routes->get('absensi-pkl/detail/(:num)', 'Siswa\AbsensiPklController::detail/$1');
    $routes->get('absensi-pkl/cetak-rekap', 'Siswa\AbsensiPklController::printRekap');
    $routes->get('absensi-pkl/cetak-rekap/(:segment)', 'Siswa\AbsensiPklController::printRekap/$1');
});

// Instruktur PKL Routes
$routes->group('instruktur', ['filter' => 'role:instruktur'], function ($routes) {
    $routes->get('dashboard', 'Instruktur\DashboardController::index', ['as' => 'instruktur.dashboard']);
    $routes->get('jurnal-pkl', 'Instruktur\JurnalPklController::index');
    $routes->get('jurnal-pkl/siswa/(:num)', 'Instruktur\JurnalPklController::siswaDetail/$1');
    $routes->get('jurnal-pkl/task/(:num)', 'Instruktur\JurnalPklController::taskDetail/$1');
    $routes->get('jurnal-pkl/pending', 'Instruktur\JurnalPklController::pendingReview');
    $routes->post('jurnal-pkl/catatan/(:num)', 'Instruktur\JurnalPklController::addCatatan/$1');
    $routes->post('jurnal-pkl/verifikasi-progress/(:num)', 'Instruktur\JurnalPklController::verifyProgress/$1');
    $routes->post('jurnal-pkl/batal-verifikasi-progress/(:num)', 'Instruktur\JurnalPklController::cancelVerifikasiProgress/$1');
    $routes->get('jurnal-pkl/semua-progress', 'Instruktur\JurnalPklController::allProgress');
    $routes->get('jurnal-pkl/tasks/(:num)', 'Instruktur\JurnalPklController::getTasksBySiswa/$1');
    $routes->get('jurnal-pkl/filtered-progress/(:num)', 'Instruktur\JurnalPklController::getFilteredProgress/$1');
    $routes->get('jurnal-pkl/week-info', 'Instruktur\JurnalPklController::getWeekInfo');

    // Task Template (Master Task)
    $routes->get('task-template', 'Instruktur\TaskTemplateController::index');
    $routes->post('task-template/simpan', 'Instruktur\TaskTemplateController::store');
    $routes->post('task-template/update/(:num)', 'Instruktur\TaskTemplateController::update/$1');
    $routes->get('task-template/hapus/(:num)', 'Instruktur\TaskTemplateController::delete/$1');
});

// Ketua Jurusan Routes (read-only PKL monitoring)
$routes->group('ketua-jurusan', ['filter' => 'role:ketua_jurusan'], function ($routes) {
    $routes->get('dashboard', 'KetuaJurusan\DashboardController::index', ['as' => 'ketuajurusan.dashboard']);

    // Jurnal PKL Monitoring
    $routes->get('jurnal-pkl', 'KetuaJurusan\JurnalPklController::index', ['as' => 'ketuajurusan.jurnal_pkl']);
    $routes->get('jurnal-pkl/detail/(:num)', 'KetuaJurusan\JurnalPklController::detail/$1');

    // Siswa PKL Monitoring
    $routes->get('siswa-pkl', 'KetuaJurusan\SiswaPklController::index', ['as' => 'ketuajurusan.siswa_pkl']);
    $routes->get('siswa-pkl/detail/(:num)', 'KetuaJurusan\SiswaPklController::detail/$1');

    // Absensi PKL Monitoring
    $routes->get('absensi-pkl', 'KetuaJurusan\AbsensiPklController::index', ['as' => 'ketuajurusan.absensi_pkl']);
    $routes->get('absensi-pkl/rekap/(:num)', 'KetuaJurusan\AbsensiPklController::rekap/$1');
});

// Profile Routes (for all roles)
$routes->group('profile', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProfileController::index');
    $routes->post('update', 'ProfileController::update');
    $routes->post('upload-photo', 'ProfileController::uploadPhoto');
    $routes->post('delete-photo', 'ProfileController::deletePhoto');
});

// File Routes (for serving uploaded files)
$routes->get('files/jurnal/(:segment)', 'FileController::jurnalFoto/$1');
$routes->get('files/jurnal-pkl/(:segment)', 'FileController::jurnalPklFoto/$1');
$routes->get('files/pkl-progress/(:segment)', 'FileController::pklProgressFoto/$1');
$routes->get('files/absensi-guru/(:segment)/(:segment)/(:segment)/(:segment)', 'FileController::absensiGuruFoto/$1/$2/$3/$4');
$routes->get('profile-photo/(:segment)', 'FileController::profilePhoto/$1');

// Layout Switcher Routes (for testing and manual switching)
$routes->group('layout', function ($routes) {
    $routes->get('desktop', 'LayoutSwitcher::desktop');
    $routes->get('mobile', 'LayoutSwitcher::mobile');
    $routes->get('auto', 'LayoutSwitcher::auto');
    $routes->get('device-info', 'LayoutSwitcher::deviceInfo');
    $routes->get('example', function() {
        return view('examples/layout_example', ['title' => 'Layout Example']);
    });
});

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
