<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\JadwalMengajarModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\IzinSiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class AbsensiController extends BaseController
{
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $jadwalModel;
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $izinModel;
    protected $session;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
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
        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search');
        $tanggal = $this->request->getGet('tanggal');
        $kelasId = $this->request->getGet('kelas_id');

        // Get absensi by guru
        $absensi = $this->absensiModel->getByGuru($guruId, $tanggal);

        // Add can_edit and can_delete flags to each absensi
        foreach ($absensi as &$item) {
            $item['can_edit'] = $this->isAbsensiEditable($item);
            $item['can_delete'] = $this->isAbsensiEditable($item);
        }
        unset($item); // CRITICAL: Break reference to avoid bugs in next foreach!

        // Group absensi by kelas and mata pelajaran to handle multiple sessions per day
        $kelasSummary = [];
        foreach ($absensi as $item) {
            $kelasId = $item['kelas_id'];
            $kelasName = $item['nama_kelas'];
            $mapelName = $item['nama_mapel'];
            
            // Create unique key: kelas_id + mata_pelajaran to separate different subjects
            $summaryKey = $kelasId . '_' . $mapelName;
            
            if (!isset($kelasSummary[$summaryKey])) {
                $kelasSummary[$summaryKey] = [
                    'kelas_id' => $kelasId,
                    'kelas_nama' => $kelasName,
                    'mata_pelajaran' => $mapelName,
                    'total_pertemuan' => 0,
                    'total_hadir' => 0,
                    'total_siswa' => 0,
                    'avg_kehadiran' => 0,
                    'last_absensi' => null
                ];
            }
            
            // Accumulate data
            $kelasSummary[$summaryKey]['total_pertemuan']++;
            $kelasSummary[$summaryKey]['total_hadir'] += $item['hadir'] ?? 0;
            $kelasSummary[$summaryKey]['total_siswa'] = max($kelasSummary[$summaryKey]['total_siswa'], $item['total_siswa'] ?? 0);
            
            // Track latest absensi date
            if (!$kelasSummary[$summaryKey]['last_absensi'] || $item['tanggal'] > $kelasSummary[$summaryKey]['last_absensi']) {
                $kelasSummary[$summaryKey]['last_absensi'] = $item['tanggal'];
            }
        }
        
        // Calculate average kehadiran for each kelas
        foreach ($kelasSummary as &$summary) {
            $totalExpected = $summary['total_pertemuan'] * $summary['total_siswa'];
            if ($totalExpected > 0) {
                $summary['avg_kehadiran'] = round(($summary['total_hadir'] / $totalExpected) * 100, 1);
            }
        }
        unset($summary); // Break reference to avoid bugs in next operations
        
        // Sort by kelas name (use uasort to preserve associative keys)
        uasort($kelasSummary, function($a, $b) {
            return strcmp($a['kelas_nama'], $b['kelas_nama']);
        });

        $absensiId = $this->request->getGet('absensi_id');
        // Get all classes taught by this teacher
        $kelasOptions = $this->getKelasOptions($guruId);

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
            'stats' => $this->getAbsensiStats($guruId, $tanggal),
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

        // Get today's schedule
        $jadwalHariIni = $this->getJadwalHariIni($guruId);

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
        if ($jadwal && $this->absensiModel->isAlreadyAbsen($jadwal['id'], $tanggal)) {
            $existingAbsensi = $this->absensiModel->getByJadwalAndTanggal($jadwal['id'], $tanggal);

            // Check if this is a substitute teacher trying to access already-filled absensi
            $isSubstituteMode = ($jadwal['guru_id'] != $guru['id']);

            if ($isSubstituteMode) {
                // Get the original teacher's name who filled the absensi
                $absensiDetail = $this->absensiModel->getAbsensiWithDetail($existingAbsensi['id']);
                $namaGuruAsli = $absensiDetail['nama_guru'] ?? 'guru asli';

                // Show friendly message for substitute teacher
                $this->session->setFlashdata('success_custom', [
                    'title' => 'Sudah Beres! ⚡',
                    'message' => "Ternyata absen sudah diisi <strong>{$namaGuruAsli}</strong>. Bapak/Ibu tidak perlu input ulang. Terima kasih bantuannya!"
                ]);
                return redirect()->to('/guru/absensi');
            }

            // For original teacher, allow editing
            $this->session->setFlashdata('info', 'Absen di tanggal ' . $tanggal . ' udah diisi sebelumnya 📝');
            return redirect()->to('/guru/absensi/edit/' . $existingAbsensi['id']);
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

        // Validate input
        $rules = [
            'jadwal_mengajar_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'pertemuan_ke' => 'required|numeric|greater_than[0]',
            'materi_pembelajaran' => 'permit_empty',
            'siswa' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jadwalId = $this->request->getPost('jadwal_mengajar_id');
        $tanggal = $this->request->getPost('tanggal');

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
            // Substitute mode: current teacher is the substitute
            $guruPenggantiId = $guru['id'];
        } else {
            // Normal mode: can optionally have a substitute (from form input)
            $guruPenggantiId = $this->request->getPost('guru_pengganti_id') ?: null;
        }

        // Check if absensi already exists
        if ($this->absensiModel->isAlreadyAbsen($jadwalId, $tanggal)) {
            $this->session->setFlashdata('error', 'Absen di tanggal ini udah diisi sebelumnya.');
            return redirect()->back()->withInput();
        }

        // Prepare absensi data
        $absensiData = [
            'jadwal_mengajar_id' => $jadwalId,
            'tanggal' => $tanggal,
            'pertemuan_ke' => $this->request->getPost('pertemuan_ke'),
            'materi_pembelajaran' => $this->request->getPost('materi_pembelajaran'),
            'created_by' => $userId,
            'guru_pengganti_id' => $guruPenggantiId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert absensi
            $absensiId = $this->absensiModel->insert($absensiData);

            if (!$absensiId) {
                throw new \Exception('Gagal menyimpan data absensi.');
            }

            // Insert absensi details
            $siswaData = $this->request->getPost('siswa');
            $batchData = [];

            foreach ($siswaData as $siswaId => $data) {
                $batchData[] = [
                    'absensi_id' => $absensiId,
                    'siswa_id' => $siswaId,
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'waktu_absen' => date('Y-m-d H:i:s')
                ];
            }

            if (!empty($batchData)) {
                $this->absensiDetailModel->insertBatch($batchData);
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menyimpan data detail absensi.');
            }

            $this->session->setFlashdata('success', 'Mantap! Absen tersimpan.');

            // Check next action from form
            $nextAction = $this->request->getPost('next_action');

            if ($nextAction === 'jurnal') {
                // Redirect to jurnal create with absensi_id
                return redirect()->to('/guru/jurnal/tambah/' . $absensiId);
            }

            // Default: Redirect to absensi index
            return redirect()->to('/guru/absensi');
        } catch (\Exception $e) {
            $db->transRollback();
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
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

        // Get kelas info
        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            throw new PageNotFoundException('Data kelas tidak ditemukan.');
        }

        // Verify this teacher teaches this class
        $teachesThisClass = $this->jadwalModel
            ->where('guru_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->countAllResults() > 0;

        if (!$teachesThisClass) {
            $this->session->setFlashdata('error', 'Sorry, kamu tidak mengajar di kelas ini.');
            return redirect()->to('/guru/absensi');
        }

        // Get absensi for this kelas
        $absensiList = $this->absensiModel->getByGuruAndKelas($guruId, $kelasId, $tanggal);
        
        // Add can_edit and can_delete flags
        foreach ($absensiList as &$item) {
            $item['can_edit'] = $this->isAbsensiEditable($item);
            $item['can_delete'] = $this->isAbsensiEditable($item);
        }
        unset($item); // CRITICAL: Break reference to avoid bugs!

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

        $data = [
            'title' => 'Absensi ' . $kelas['nama_kelas'],
            'pageTitle' => 'Absensi ' . $kelas['nama_kelas'],
            'pageDescription' => 'Daftar pertemuan kelas ' . $kelas['nama_kelas'],
            'kelas' => $kelas,
            'absensiList' => $absensiList,
            'kelasStats' => $kelasStats,
            'guru' => $guru,
            'tanggal' => $tanggal
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

        // Get absensi with details
        $absensi = $this->absensiModel->getAbsensiWithDetail($id);

        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify access: Allow if:
        // 1. User created the absensi
        // 2. Schedule belongs to this teacher (original teacher)
        // 3. This teacher is the substitute teacher for this absensi
        $jadwal = $this->jadwalModel->find($absensi['jadwal_mengajar_id']);
        $hasAccess = ($absensi['created_by'] == $userId)
            || ($jadwal && $jadwal['guru_id'] == $guru['id'])
            || ($absensi['guru_pengganti_id'] == $guru['id']);

        if (!$hasAccess) {
            $this->session->setFlashdata('error', 'Sorry, ini bukan jadwal kamu.');
            return redirect()->to('/guru/absensi');
        }

        // Get absensi details
        $absensiDetails = $this->absensiDetailModel->getByAbsensi($id);

        // Calculate statistics
        $statistics = $this->calculateStatistics($absensiDetails);

        $data = [
            'title' => 'Detail Absensi',
            'pageTitle' => 'Detail Absensi',
            'pageDescription' => 'Lihat detail data absensi',
            'absensi' => $absensi,
            'absensiDetails' => $absensiDetails,
            'statistics' => $statistics,
            'guru' => $guru,
            'isEditable' => $this->isAbsensiEditable($absensi)
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

        // Get absensi with details
        $absensi = $this->absensiModel->getAbsensiWithDetail($id);

        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify access: Allow if:
        // 1. User created the absensi
        // 2. Schedule belongs to this teacher (original teacher)
        // 3. This teacher is the substitute teacher for this absensi
        $jadwal = $this->jadwalModel->find($absensi['jadwal_mengajar_id']);
        $hasAccess = ($absensi['created_by'] == $userId)
            || ($jadwal && $jadwal['guru_id'] == $guru['id'])
            || ($absensi['guru_pengganti_id'] == $guru['id']);

        if (!$hasAccess) {
            $this->session->setFlashdata('error', 'Sorry, ini bukan jadwal kamu.');
            return redirect()->to('/guru/absensi');
        }

        // Check if absensi is editable (within 24 hours)
        if (!$this->isAbsensiEditable($absensi)) {
            $this->session->setFlashdata('error', 'Absen ini udah lewat 24 jam, nggak bisa diedit lagi ya ?');
            return redirect()->to('/guru/absensi/show/' . $id);
        }

        // Get absensi details
        $absensiDetails = $this->absensiDetailModel->getByAbsensi($id);

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

        // Check if absensi exists
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify access: Allow if:
        // 1. User created the absensi
        // 2. Schedule belongs to this teacher (original teacher)
        // 3. This teacher is the substitute teacher for this absensi
        $jadwal = $this->jadwalModel->find($absensi['jadwal_mengajar_id']);
        $hasAccess = ($absensi['created_by'] == $userId)
            || ($jadwal && $jadwal['guru_id'] == $guru['id'])
            || ($absensi['guru_pengganti_id'] == $guru['id']);

        if (!$hasAccess) {
            $this->session->setFlashdata('error', 'Sorry, ini bukan jadwal kamu.');
            return redirect()->to('/guru/absensi');
        }

        // Check if absensi is editable
        if (!$this->isAbsensiEditable($absensi)) {
            $this->session->setFlashdata('error', 'Absen ini udah lewat 24 jam, nggak bisa diedit lagi ya ?');
            return redirect()->to('/guru/absensi/show/' . $id);
        }

        // Validate input
        $rules = [
            'tanggal' => 'required|valid_date',
            'pertemuan_ke' => 'required|numeric|greater_than[0]',
            'siswa' => 'required'
        ];

        if (!$this->validate($rules)) {
            // Log validation errors for debugging
            log_message('error', 'Validation failed in AbsensiController::update: ' . json_encode($this->validator->getErrors()));
            $this->session->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get siswa data and validate
        $siswaData = $this->request->getPost('siswa');
        if (empty($siswaData) || !is_array($siswaData)) {
            log_message('error', 'No siswa data received in AbsensiController::update');
            $this->session->setFlashdata('error', 'Data siswa tidak ditemukan. Mohon refresh halaman dan coba lagi.');
            return redirect()->back()->withInput();
        }

        // Log for debugging
        log_message('info', 'Updating absensi ID: ' . $id . ' with ' . count($siswaData) . ' students');

        // Update absensi data
        $absensiData = [
            'id' => $id,
            'tanggal' => $this->request->getPost('tanggal'),
            'pertemuan_ke' => $this->request->getPost('pertemuan_ke'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update absensi
            if (!$this->absensiModel->save($absensiData)) {
                throw new \Exception('Gagal mengupdate data absensi utama.');
            }

            // Update absensi details
            $updateCount = 0;
            $insertCount = 0;

            foreach ($siswaData as $siswaId => $data) {
                // Validate siswa_id
                if (!is_numeric($siswaId)) {
                    log_message('warning', 'Invalid siswa_id: ' . $siswaId);
                    continue;
                }

                // Validate status
                if (!isset($data['status']) || empty($data['status'])) {
                    log_message('warning', 'Empty status for siswa_id: ' . $siswaId);
                    continue;
                }

                // Normalize status to lowercase and handle Alpha -> alpa conversion
                $status = strtolower(trim($data['status']));
                if ($status === 'alpha') {
                    $status = 'alpa';
                }

                // Validate status value
                $validStatuses = ['hadir', 'izin', 'sakit', 'alpa'];
                if (!in_array($status, $validStatuses)) {
                    log_message('warning', 'Invalid status "' . $data['status'] . '" for siswa_id: ' . $siswaId);
                    continue;
                }

                $existing = $this->absensiDetailModel
                    ->where('absensi_id', $id)
                    ->where('siswa_id', $siswaId)
                    ->first();

                if ($existing) {
                    // Update existing record
                    $updateResult = $this->absensiDetailModel->update($existing['id'], [
                        'status' => $status,
                        'keterangan' => $data['keterangan'] ?? null
                    ]);

                    if ($updateResult) {
                        $updateCount++;
                        log_message('debug', 'Updated siswa_id: ' . $siswaId . ' with status: ' . $status);
                    }
                } else {
                    // Insert new record
                    $insertResult = $this->absensiDetailModel->insert([
                        'absensi_id' => $id,
                        'siswa_id' => $siswaId,
                        'status' => $status,
                        'keterangan' => $data['keterangan'] ?? null,
                        'waktu_absen' => date('Y-m-d H:i:s')
                    ]);

                    if ($insertResult) {
                        $insertCount++;
                        log_message('debug', 'Inserted siswa_id: ' . $siswaId . ' with status: ' . $status);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal memperbarui data absensi.');
            }

            // Log success
            log_message('info', 'Absensi updated successfully. Updated: ' . $updateCount . ', Inserted: ' . $insertCount);

            $this->session->setFlashdata('success', 'Nice! Absen sudah diupdate, (Diubah: ' . $updateCount . ', Ditambah: ' . $insertCount . ')');

            // Check next action from form
            $nextAction = $this->request->getPost('next_action');

            if ($nextAction === 'jurnal') {
                // Redirect to jurnal create with absensi_id
                return redirect()->to('/guru/jurnal/tambah/' . $id);
            }

            // Default: Redirect to absensi index
            return redirect()->to('/guru/absensi');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating absensi: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Gagal menyimpan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
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

        // Check if absensi exists
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify access: Allow if:
        // 1. User created the absensi
        // 2. Schedule belongs to this teacher (original teacher)
        // 3. This teacher is the substitute teacher for this absensi
        $jadwal = $this->jadwalModel->find($absensi['jadwal_mengajar_id']);
        $hasAccess = ($absensi['created_by'] == $userId)
            || ($jadwal && $jadwal['guru_id'] == $guru['id'])
            || ($absensi['guru_pengganti_id'] == $guru['id']);

        if (!$hasAccess) {
            $this->session->setFlashdata('error', 'Sorry, ini bukan jadwal kamu.');
            return redirect()->to('/guru/absensi');
        }

        // Check if absensi is editable
        if (!$this->isAbsensiEditable($absensi)) {
            $this->session->setFlashdata('error', 'Absen udah lewat 24 jam nih, udah nggak bisa dihapus ya 🙏');
            return redirect()->to('/guru/absensi/show/' . $id);
        }

        // Delete absensi (cascade will delete absensi_detail)
        if ($this->absensiModel->delete($id)) {
            $this->session->setFlashdata('success', 'Sip, Absen sudah dihapus ya!');
        } else {
            $this->session->setFlashdata('error', 'Hmm, gagal hapus absen 🤔');
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

        if (!$kelasId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kelas ID diperlukan']);
        }

        // Get students in the class
        $siswaList = $this->siswaModel->getByKelas($kelasId);

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
            return $this->response->setJSON(['success' => false, 'message' => 'Data guru tidak ditemukan']);
        }

        $hari = $this->request->getGet('hari');
        if (!$hari) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hari diperlukan']);
        }

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
                ->orderBy('jam_mulai', 'ASC')
                ->findAll();
        } else {
            // Get only this teacher's schedules
            $jadwal = $this->jadwalModel->getByGuru($guru['id'], $hari);
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

        // Get next pertemuan number for this jadwal
        $lastAbsensi = $this->absensiModel
            ->where('jadwal_mengajar_id', $jadwalId)
            ->orderBy('pertemuan_ke', 'DESC')
            ->first();

        $nextPertemuan = $lastAbsensi ? ($lastAbsensi['pertemuan_ke'] + 1) : 1;

        return $this->response->setJSON([
            'success' => true,
            'pertemuan_ke' => $nextPertemuan
        ]);
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

        // Get absensi with details
        $absensi = $this->absensiModel->getAbsensiWithDetail($id);

        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify access: Allow if:
        // 1. User created the absensi
        // 2. Schedule belongs to this teacher (original teacher)
        // 3. This teacher is the substitute teacher for this absensi
        $jadwal = $this->jadwalModel->find($absensi['jadwal_mengajar_id']);
        $hasAccess = ($absensi['created_by'] == $userId)
            || ($jadwal && $jadwal['guru_id'] == $guru['id'])
            || ($absensi['guru_pengganti_id'] == $guru['id']);

        if (!$hasAccess) {
            $this->session->setFlashdata('error', 'Sorry, ini bukan jadwal kamu.');
            return redirect()->to('/guru/absensi');
        }

        // Get absensi details
        $absensiDetails = $this->absensiDetailModel->getByAbsensi($id);

        // Calculate statistics
        $statistics = $this->calculateStatistics($absensiDetails);

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

    private function getJadwalHariIni($guruId)
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

        return $this->jadwalModel->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', $guruId)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai', 'ASC')
            ->findAll();
    }

    private function getKelasOptions($guruId)
    {
        $kelasList = $this->jadwalModel->select('kelas.*')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', $guruId)
            ->groupBy('kelas.id')
            ->orderBy('kelas.tingkat, kelas.nama_kelas')
            ->findAll();

        $options = ['' => 'Semua Kelas'];
        foreach ($kelasList as $kelas) {
            $options[$kelas['id']] = $kelas['nama_kelas'] . ' - ' . $kelas['jurusan'];
        }

        return $options;
    }

    private function getAbsensiStats($guruId, $tanggal = null)
    {
        $stats = [
            'total' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0
        ];

        $builder = $this->absensiDetailModel
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.guru_id', $guruId);

        if ($tanggal) {
            $builder->where('absensi.tanggal', $tanggal);
        }

        $details = $builder->select('absensi_detail.status, COUNT(*) as jumlah')
            ->groupBy('absensi_detail.status')
            ->findAll();

        foreach ($details as $detail) {
            $stats[$detail['status']] = $detail['jumlah'];
            $stats['total'] += $detail['jumlah'];
        }

        return $stats;
    }

    private function getNextPertemuan($guruId, $kelasId = null, $jadwalId = null)
    {
        // If jadwal_id is provided, use it to get the last pertemuan for that specific schedule
        // This ensures each jadwal has its own pertemuan counter
        if ($jadwalId) {
            $lastAbsensi = $this->absensiModel
                ->where('jadwal_mengajar_id', $jadwalId)
                ->orderBy('pertemuan_ke', 'DESC')
                ->first();

            return $lastAbsensi ? ($lastAbsensi['pertemuan_ke'] + 1) : 1;
        }

        // If jadwal_id not provided, always return 1 for initial form load
        // User will select jadwal first, then AJAX will get the correct pertemuan number
        return 1;
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

    private function calculateStatistics($absensiDetails)
    {
        $total = count($absensiDetails);
        $statistics = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'percentage' => 0
        ];

        foreach ($absensiDetails as $detail) {
            if (isset($statistics[$detail['status']])) {
                $statistics[$detail['status']]++;
            }
        }

        if ($total > 0) {
            $hadir = $statistics['hadir'];
            $statistics['percentage'] = round(($hadir / $total) * 100, 2);
        }

        return $statistics;
    }
}
