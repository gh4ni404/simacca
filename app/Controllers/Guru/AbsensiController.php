<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Services\AbsensiService;
use App\Models\JadwalMengajarModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\IzinSiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class AbsensiController extends BaseController
{
    protected $absensiService;
    protected $jadwalModel;
    protected $guruModel;
    protected $siswaModel;
    protected $izinModel;
    protected $session;

    public function __construct()
    {
        $this->absensiService = new AbsensiService();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->izinModel = new IzinSiswaModel();
        $this->session = session();
    }

    /**
     * Display list of absensi
     */
    public function index()
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        $guruId = $guru['id'];
        $search = $this->request->getGet('search');
        $tanggal = $this->request->getGet('tanggal');
        $kelasId = $this->request->getGet('kelas_id');
        $tahunAjaran = get_active_tahun_ajaran();

        // Get absensi by guru using service
        $absensiResult = $this->absensiService->getByGuru($guruId, $tanggal, $tahunAjaran);
        $absensi = $absensiResult['data'] ?? [];

        // Get kelas summary
        $kelasSummary = $this->absensiService->getKelasSummary($absensi);

        // Get stats
        $statsResult = $this->absensiService->getAbsensiStats($guruId, $tanggal, $tahunAjaran);
        $stats = $statsResult['data'] ?? [];

        // Get all classes taught by this teacher
        $kelasOptions = $this->getKelasOptions($guruId, $tahunAjaran);

        $data = [
            'title' => 'Manajemen Absensi',
            'pageTitle' => 'Data Absensi',
            'pageDescription' => 'Kelola data absensi siswa',
            'kelasSummary' => $kelasSummary,
            'search' => $search,
            'tanggal' => $tanggal,
            'kelasId' => $kelasId,
            'kelasOptions' => $kelasOptions,
            'guru' => $guru,
            'stats' => $stats,
        ];

        return view('guru/absensi/index', $data);
    }

    /**
     * Show form for creating new absensi
     */
    public function create()
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        $guruId = $guru['id'];
        $jadwalId = $this->request->getGet('jadwal_id');
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $tahunAjaran = get_active_tahun_ajaran();

        // Get today's schedule
        $jadwalHariIni = $this->getJadwalHariIni($guruId, $tahunAjaran);

        // If jadwal_id is provided, use that
        if ($jadwalId) {
            $jadwal = $this->jadwalModel->getJadwalWithDetail($jadwalId);
            if (!$jadwal) {
                $this->session->setFlashdata('error', 'Jadwal nggak ada nih ');
                return redirect()->to('/guru/absensi/tambah');
            }
            // Allow access if:
            // 1. Jadwal belongs to current teacher (normal mode)
            // 2. Jadwal belongs to another teacher (substitute mode)
            // Both are valid scenarios
        } else {
            $jadwal = null;
        }

        // Check if absensi already exists for this schedule and date
        if ($jadwal) {
            $checkResult = $this->absensiService->checkAbsensiExists($jadwal['id'], $tanggal);
            
            if ($checkResult['success'] && $checkResult['data']['exists']) {
                $existingAbsensi = $checkResult['data']['absensi'];

                // Check if this is a substitute teacher trying to access already-filled absensi
                $isSubstituteMode = ($jadwal['guru_id'] != $guru['id']);

                if ($isSubstituteMode) {
                    // Get the original teacher's name
                    $detailResult = $this->absensiService->getAbsensiDetail($existingAbsensi['id']);
                    $namaGuruAsli = $detailResult['data']['absensi']['nama_guru'] ?? 'guru asli';

                    // Show friendly message for substitute teacher
                    $this->session->setFlashdata('success_custom', [
                        'title' => 'Udah Beres Nih! ⚡',
                        'message' => "Ternyata absen udah diisi sama <strong>{$namaGuruAsli}</strong>. Nggak perlu input ulang kok. Makasih ya udah mau bantu! 🙏"
                    ]);
                    return redirect()->to('/guru/absensi');
                }

                // For original teacher, allow editing
                $this->session->setFlashdata('info', 'Absen di tanggal ' . $tanggal . ' udah diisi sebelumnya 📝');
                return redirect()->to('/guru/absensi/edit/' . $existingAbsensi['id']);
            }
        }

        // Get next pertemuan number
        // Pass jadwal_id to ensure correct pertemuan numbering for substitute teachers
        $pertemuanKe = $this->getNextPertemuan(
            $guruId,
            $jadwal && isset($jadwal['kelas_id']) ? $jadwal['kelas_id'] : null,
            $jadwal ? $jadwal['id'] : null
        );
        
        // Get approved izin for this date and class
        $approvedIzin = [];
        if ($jadwal && isset($jadwal['kelas_id'])) {
            $approvedIzin = $this->izinModel->getApprovedIzinByDate($tanggal, $jadwal['kelas_id']);
        }

        // Get all teachers for substitute teacher dropdown
        $guruList = $this->guruModel->select('id, nama_lengkap, nip')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Input Absensi',
            'pageTitle' => 'Input Absensi',
            'pageDescription' => 'Isi absensi siswa',
            'validation' => \Config\Services::validation(),
            'guru' => $guru,
            'jadwal' => $jadwal,
            'jadwalHariIni' => $jadwalHariIni,
            'tanggal' => $tanggal,
            'pertemuanKe' => $pertemuanKe,
            'hariList' => $this->getHariList(),
            'statusOptions' => $this->getStatusOptions(),
            'approvedIzin' => $approvedIzin,
            'guruList' => $guruList
        ];

        return view('guru/absensi/create', $data);
    }

    /**
     * Store new absensi
     */
    public function store()
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        $jadwalId = $this->request->getPost('jadwal_mengajar_id');
        
        // Verify jadwal exists
        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal) {
            $this->session->setFlashdata('error', 'Jadwal ini nggak valid.');
            return redirect()->back()->withInput();
        }

        // Determine if this is substitute mode
        $isSubstituteMode = ($jadwal['guru_id'] != $guru['id']);

        // Set guru_pengganti_id based on mode
        $guruPenggantiId = null;
        if ($isSubstituteMode) {
            $guruPenggantiId = $guru['id'];
        } else {
            $guruPenggantiId = $this->request->getPost('guru_pengganti_id') ?: null;
        }

        // Prepare data for service
        $data = [
            'jadwal_mengajar_id' => $jadwalId,
            'tanggal' => $this->request->getPost('tanggal'),
            'pertemuan_ke' => $this->request->getPost('pertemuan_ke'),
            'materi_pembelajaran' => $this->request->getPost('materi_pembelajaran'),
            'created_by' => $userId,
            'guru_pengganti_id' => $guruPenggantiId,
            'siswa' => $this->request->getPost('siswa')
        ];

        // Create absensi using service
        $result = $this->absensiService->createAbsensi($data);

        if (!$result['success']) {
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        $this->session->setFlashdata('success', 'Mantap! Absen tersimpan.');

        // Check next action from form
        $nextAction = $this->request->getPost('next_action');

        if ($nextAction === 'jurnal') {
            // Redirect to jurnal create with absensi_id
            return redirect()->to('/guru/jurnal/tambah/' . $result['data']['absensi_id']);
        }

        // Default: Redirect to absensi index
        return redirect()->to('/guru/absensi');
    }

    /**
     * Display absensi list by kelas
     */
    public function kelas($kelasId)
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        $guruId = $guru['id'];
        $tanggal = $this->request->getGet('tanggal');
        $mapelId = $this->request->getGet('mapel_id');
        $tahunAjaran = get_active_tahun_ajaran();

        // Verify this teacher teaches this class
        $teachesThisClass = $this->jadwalModel
            ->where('guru_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->countAllResults() > 0;

        if (!$teachesThisClass) {
            $this->session->setFlashdata('error', 'Sorry, kamu nggak mengajar di kelas ini 🤔');
            return redirect()->to('/guru/absensi');
        }

        // Get absensi for this kelas using service (filtered by mapel if provided)
        $absensiResult = $this->absensiService->getByGuruAndKelas($guruId, $kelasId, $tanggal, $tahunAjaran, $mapelId);
        $absensiList = $absensiResult['data'] ?? [];

        // Get mata pelajaran name if mapel_id is provided
        $namaMapel = null;
        if ($mapelId) {
            $mapelModel = new \App\Models\MataPelajaranModel();
            $mapel = $mapelModel->find($mapelId);
            $namaMapel = $mapel ? $mapel['nama_mapel'] : null;
        }

        // Calculate stats for this kelas
        $kelasStats = [
            'total_pertemuan' => count($absensiList),
            'total_hadir' => 0,
            'total_siswa' => 0,
            'avg_kehadiran' => 0
        ];

        foreach ($absensiList as $item) {
            $kelasStats['total_hadir'] += $item['hadir'] ?? 0;
            $kelasStats['total_siswa'] = max($kelasStats['total_siswa'], $item['total_siswa'] ?? 0);
        }

        if ($kelasStats['total_pertemuan'] > 0 && $kelasStats['total_siswa'] > 0) {
            $totalExpected = $kelasStats['total_pertemuan'] * $kelasStats['total_siswa'];
            $kelasStats['avg_kehadiran'] = round(($kelasStats['total_hadir'] / $totalExpected) * 100, 1);
        }

        // Get kelas info (we still need KelasModel for this)
        $kelasModel = new \App\Models\KelasModel();
        $kelas = $kelasModel->find($kelasId);
        if (!$kelas) {
            throw new PageNotFoundException('Data kelas tidak ditemukan.');
        }

        $data = [
            'title' => 'Absensi ' . $kelas['nama_kelas'],
            'pageTitle' => 'Absensi ' . $kelas['nama_kelas'],
            'pageDescription' => 'Daftar pertemuan kelas ' . $kelas['nama_kelas'],
            'kelas' => $kelas,
            'absensiList' => $absensiList,
            'kelasStats' => $kelasStats,
            'guru' => $guru,
            'tanggal' => $tanggal,
            'namaMapel' => $namaMapel,
            'mapelId' => $mapelId
        ];

        return view('guru/absensi/kelas', $data);
    }

    /**
     * Display absensi detail
     */
    public function show($id)
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        // Get absensi detail using service
        $result = $this->absensiService->getAbsensiDetail($id);

        if (!$result['success']) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify access
        $accessResult = $this->absensiService->verifyAccess(
            $result['data']['absensi'], 
            $userId, 
            $guru['id']
        );

        if (!$accessResult['success']) {
            $this->session->setFlashdata('error', 'Sorry, ini bukan jadwal kamu.');
            return redirect()->to('/guru/absensi');
        }

        $data = [
            'title' => 'Detail Absensi',
            'pageTitle' => 'Detail Absensi',
            'pageDescription' => 'Lihat detail data absensi',
            'absensi' => $result['data']['absensi'],
            'absensiDetails' => $result['data']['absensiDetails'],
            'statistics' => $result['data']['statistics'],
            'guru' => $guru,
            'isEditable' => $result['data']['isEditable']
        ];

        return view('guru/absensi/show', $data);
    }

    /**
     * Show form for editing absensi
     */
    public function edit($id)
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        // Get absensi detail using service
        $result = $this->absensiService->getAbsensiDetail($id);

        if (!$result['success']) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        $absensi = $result['data']['absensi'];
        $absensiDetails = $result['data']['absensiDetails'];

        // Verify access
        $accessResult = $this->absensiService->verifyAccess($absensi, $userId, $guru['id']);

        if (!$accessResult['success']) {
            $this->session->setFlashdata('error', 'Sorry, ini bukan jadwal kamu.');
            return redirect()->to('/guru/absensi');
        }

        // Check if editable
        if (!$result['data']['isEditable']) {
            $this->session->setFlashdata('error', 'Absen ini udah lewat 24 jam, nggak bisa diedit lagi ya ?');
            return redirect()->to('/guru/absensi/show/' . $id);
        }

        // Get students in the class
        $kelasId = $absensi['kelas_id'] ?? null;
        $siswaList = $kelasId ? $this->siswaModel->getByKelas($kelasId) : [];

        // Get approved izin for this date and class
        $approvedIzin = [];
        if ($kelasId && isset($absensi['tanggal'])) {
            $approvedIzin = $this->izinModel->getApprovedIzinByDate($absensi['tanggal'], $kelasId);
        }

        $data = [
            'title' => 'Edit Absensi',
            'pageTitle' => 'Edit Absensi',
            'pageDescription' => 'Edit data absensi',
            'validation' => \Config\Services::validation(),
            'absensi' => $absensi,
            'absensiDetails' => $absensiDetails,
            'siswaList' => $siswaList,
            'guru' => $guru,
            'statusOptions' => $this->getStatusOptions(),
            'approvedIzin' => $approvedIzin
        ];

        return view('guru/absensi/edit', $data);
    }

    /**
     * Update absensi
     */
    public function update($id)
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        // Prepare data for service
        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'pertemuan_ke' => $this->request->getPost('pertemuan_ke'),
            'materi_pembelajaran' => $this->request->getPost('materi_pembelajaran'),
            'siswa' => $this->request->getPost('siswa')
        ];

        // Update using service
        $result = $this->absensiService->updateAbsensi($id, $data);

        if (!$result['success']) {
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        $updateCount = $result['data']['updated'] ?? 0;
        $insertCount = $result['data']['inserted'] ?? 0;

        $this->session->setFlashdata('success', 'Keren! Absen udah diupdate nih (Diubah: ' . $updateCount . ', Ditambah: ' . $insertCount . ') 📝');

        // Check next action from form
        $nextAction = $this->request->getPost('next_action');

        if ($nextAction === 'jurnal') {
            return redirect()->to('/guru/jurnal/tambah/' . $id);
        }

        return redirect()->to('/guru/absensi');
    }

    /**
     * Delete absensi
     */
    public function delete($id)
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        // Delete using service
        $result = $this->absensiService->deleteAbsensi($id);

        if (!$result['success']) {
            $this->session->setFlashdata('error', $result['message']);
        } else {
            $this->session->setFlashdata('success', 'Sip, Absen sudah dihapus ya!');
        }

        return redirect()->to('/guru/absensi');
    }

    /**
     * Get siswa by kelas (AJAX)
     */
    public function getSiswaByKelas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $kelasId = $this->request->getGet('kelas_id');
        $tanggal = $this->request->getGet('tanggal');
        $tahunAjaran = get_active_tahun_ajaran();

        if (!$kelasId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kelas ID diperlukan']);
        }

        // Get students in the class (filtered by tahun_ajaran)
        $siswaList = $this->siswaModel->getByKelas($kelasId, $tahunAjaran);

        // Get approved izin for this date
        $approvedIzin = [];
        if ($tanggal) {
            $approvedIzin = $this->izinModel->getApprovedIzinByDate($tanggal, $kelasId);
        }

        // Prepare response
        $response = [
            'success' => true,
            'siswa' => $siswaList,
            'approvedIzin' => $approvedIzin,
            'statusOptions' => $this->getStatusOptions()
        ];

        return $this->response->setJSON($response);
    }

    /**
     * Get jadwal by guru and hari (AJAX)
     */
    public function getJadwalByHari()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data guru nggak ketemu nih 🤔']);
        }

        $hari = $this->request->getGet('hari');
        if (!$hari) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hari diperlukan']);
        }

        $tahunAjaran = get_active_tahun_ajaran();

        // Check if this is for substitute teacher mode
        $isSubstitute = $this->request->getGet('substitute') === 'true';

        if ($isSubstitute) {
            // Get ALL schedules for this day (for substitute teachers)
            $jadwal = $this->jadwalModel->select('jadwal_mengajar.*, 
                                                mata_pelajaran.nama_mapel, 
                                                kelas.nama_kelas,
                                                guru.nama_lengkap as nama_guru')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
                ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
                ->where('hari', $hari)
                ->where('jadwal_mengajar.tahun_ajaran', $tahunAjaran)
                ->orderBy('jam_mulai', 'ASC')
                ->findAll();
        } else {
            // Get only this teacher's schedules
            $jadwal = $this->jadwalModel->getByGuru($guru['id'], $hari, $tahunAjaran);
        }

        return $this->response->setJSON([
            'success' => true,
            'jadwal' => $jadwal,
            'isSubstitute' => $isSubstitute
        ]);
    }

    /**
     * Get next pertemuan number by jadwal_id (AJAX)
     */
    public function getNextPertemuanByJadwal()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $jadwalId = $this->request->getGet('jadwal_id');
        if (!$jadwalId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jadwal ID diperlukan']);
        }

        // Get next pertemuan number using service
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);
        
        if (!$guru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data guru nggak ketemu nih 🤔']);
        }
        
        $result = $this->absensiService->getNextPertemuan($guru['id'], null, $jadwalId);

        return $this->response->setJSON($result['data']);
    }

    /**
     * Print absensi
     */
    public function print($id)
    {
        // Note: Auth check handled by AuthFilter and RoleFilter

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Hmm, data guru nggak ketemu 🤔');
            return redirect()->to('/login');
        }

        // Get absensi detail using service
        $result = $this->absensiService->getAbsensiDetail($id);

        if (!$result['success']) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        $absensi = $result['data']['absensi'];
        $absensiDetails = $result['data']['absensiDetails'];
        $statistics = $result['data']['statistics'];

        // Verify access
        $accessResult = $this->absensiService->verifyAccess($absensi, $userId, $guru['id']);

        if (!$accessResult['success']) {
            $this->session->setFlashdata('error', 'Sorry, ini bukan jadwal kamu.');
            return redirect()->to('/guru/absensi');
        }

        $data = [
            'title' => 'Cetak Absensi',
            'absensi' => $absensi,
            'absensiDetails' => $absensiDetails,
            'statistics' => $statistics,
            'guru' => $guru
        ];

        return view('guru/absensi/print', $data);
    }

    /**
     * Helper Methods
     */

    private function getJadwalHariIni($guruId, $tahunAjaran = null)
    {
        $hariIndonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
        ];

        $hariInggris = date('l');
        $hariIni = $hariIndonesia[$hariInggris] ?? null;

        if (!$hariIni) {
            return [];
        }

        $builder = $this->jadwalModel->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', $guruId)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai', 'ASC');

        if ($tahunAjaran) {
            $builder->where('jadwal_mengajar.tahun_ajaran', $tahunAjaran);
        }

        return $builder->findAll();
    }

    private function getKelasOptions($guruId, $tahunAjaran = null)
    {
        $builder = $this->jadwalModel->select('kelas.*')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', $guruId)
            ->groupBy('kelas.id')
            ->orderBy('kelas.tingkat, kelas.nama_kelas');

        if ($tahunAjaran) {
            $builder->where('jadwal_mengajar.tahun_ajaran', $tahunAjaran);
        }

        $kelasList = $builder->findAll();

        $options = ['' => 'Semua Kelas'];
        foreach ($kelasList as $kelas) {
            $options[$kelas['id']] = $kelas['nama_kelas'] . ' - ' . $kelas['jurusan'];
        }

        return $options;
    }

    private function getNextPertemuan($guruId, $kelasId = null, $jadwalId = null)
    {
        // Use service to get next pertemuan
        $result = $this->absensiService->getNextPertemuan($guruId, $kelasId, $jadwalId);
        return $result['data']['pertemuan_ke'] ?? 1;
    }

    private function getHariList()
    {
        return [
            'Senin' => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu' => 'Rabu',
            'Kamis' => 'Kamis',
            'Jumat' => 'Jumat',
        ];
    }

    private function getStatusOptions()
    {
        return [
            'hadir' => ['label' => 'Hadir', 'color' => 'bg-green-100 text-green-800'],
            'izin' => ['label' => 'Izin', 'color' => 'bg-blue-100 text-blue-800'],
            'sakit' => ['label' => 'Sakit', 'color' => 'bg-yellow-100 text-yellow-800'],
            'alpa' => ['label' => 'Alpa', 'color' => 'bg-red-100 text-red-800']
        ];
    }

}
