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
        // Check if user is logged in and has guru role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'guru_mapel') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan.');
            return redirect()->to('/login');
        }

        $guruId = $guru['id'];
        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search');
        $tanggal = $this->request->getGet('tanggal');
        $kelasId = $this->request->getGet('kelas_id');

        // Get absensi by guru
        $absensi = $this->absensiModel->getByGuru($guruId, $tanggal);
        var_dump($absensi);

        $absensiId = $absensi['id'] ?? null;
        // Get all classes taught by this teacher
        $kelasOptions = $this->getKelasOptions($guruId);

        $data = [
            'title' => 'Manajemen Absensi',
            'pageTitle' => 'Data Absensi',
            'pageDescription' => 'Kelola data absensi siswa',
            'absensi' => $absensi,
            'search' => $search,
            'tanggal' => $tanggal,
            'kelasId' => $kelasId,
            'kelasOptions' => $kelasOptions,
            'guru' => $guru,
            'stats' => $this->getAbsensiStats($guruId, $tanggal),
            'detailStats' => $this->absensiDetailModel->getDetailStats($absensiId)
        ];

        return view('guru/absensi/index', $data);
    }

    /**
     * Show form for creating new absensi
     */
    public function create()
    {
        // Check if user is logged in and has guru role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'guru_mapel') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan.');
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
            if (!$jadwal || $jadwal['guru_id'] != $guruId) {
                $this->session->setFlashdata('error', 'Jadwal tidak ditemukan atau tidak memiliki akses.');
                return redirect()->to('/guru/absensi/tambah');
            }
        } else {
            $jadwal = null;
        }

        // Check if absensi already exists for this schedule and date
        if ($jadwal && $this->absensiModel->isAlreadyAbsen($jadwal['id'], $tanggal)) {
            $existingAbsensi = $this->absensiModel->getByJadwalAndTanggal($jadwal['id'], $tanggal);
            $this->session->setFlashdata('info', 'Absensi untuk jadwal ini pada tanggal ' . $tanggal . ' sudah ada.');
            return redirect()->to('/guru/absensi/edit/' . $existingAbsensi['id']);
        }

        // Get next pertemuan number
        $pertemuanKe = $this->getNextPertemuan($guruId, $jadwal && isset($jadwal['kelas_id']) ? $jadwal['kelas_id'] : null);

        // Get approved izin for this date and class
        $approvedIzin = [];
        if ($jadwal && isset($jadwal['kelas_id'])) {
            $approvedIzin = $this->izinModel->getApprovedIzinByDate($tanggal, $jadwal['kelas_id']);
        }

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
            'approvedIzin' => $approvedIzin
        ];

        return view('guru/absensi/create', $data);
    }

    /**
     * Store new absensi
     */
    public function store()
    {
        // Check if user is logged in and has guru role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'guru_mapel') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan.');
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

        // Verify jadwal belongs to this teacher
        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal || $jadwal['guru_id'] != $guru['id']) {
            $this->session->setFlashdata('error', 'Jadwal tidak valid.');
            return redirect()->back()->withInput();
        }

        // Check if absensi already exists
        if ($this->absensiModel->isAlreadyAbsen($jadwalId, $tanggal)) {
            $this->session->setFlashdata('error', 'Absensi untuk jadwal ini pada tanggal tersebut sudah ada.');
            return redirect()->back()->withInput();
        }

        // Prepare absensi data
        $absensiData = [
            'jadwal_mengajar_id' => $jadwalId,
            'tanggal' => $tanggal,
            'pertemuan_ke' => $this->request->getPost('pertemuan_ke'),
            'materi_pembelajaran' => $this->request->getPost('materi_pembelajaran'),
            'created_by' => $userId,
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

            $this->session->setFlashdata('success', 'Absensi berhasil disimpan!');

            // Ask if want to fill jurnal
            if ($this->request->getPost('next_action') == 'jurnal') {
                return redirect()->to('/guru/jurnal/tambah?absensi_id=' . $absensiId);
            } else {
                return redirect()->to('/guru/absensi/detail/' . $absensiId);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display absensi detail
     */
    public function show($id)
    {
        // Check if user is logged in and has guru role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'guru_mapel') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan.');
            return redirect()->to('/login');
        }

        // Get absensi with details
        $absensi = $this->absensiModel->getAbsensiWithDetail($id);

        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify the absensi belongs to this teacher
        if ($absensi['created_by'] != $userId) {
            $this->session->setFlashdata('error', 'Anda tidak memiliki akses ke absensi ini.');
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
        // Check if user is logged in and has guru role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'guru_mapel') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan.');
            return redirect()->to('/login');
        }

        // Get absensi with details
        $absensi = $this->absensiModel->getAbsensiWithDetail($id);

        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify the absensi belongs to this teacher
        if ($absensi['created_by'] != $userId) {
            $this->session->setFlashdata('error', 'Anda tidak memiliki akses ke absensi ini.');
            return redirect()->to('/guru/absensi');
        }

        // Check if absensi is editable (within 24 hours)
        if (!$this->isAbsensiEditable($absensi)) {
            $this->session->setFlashdata('error', 'Absensi ini sudah tidak dapat diedit (lebih dari 24 jam).');
            return redirect()->to('/guru/absensi/detail/' . $id);
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
            'approvedIzin' => $approvedIzin,
            'guru' => $guru,
            'statusOptions' => $this->getStatusOptions()
        ];

        return view('guru/absensi/edit', $data);
    }

    /**
     * Update absensi
     */
    public function update($id)
    {
        // Check if user is logged in and has guru role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'guru_mapel') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan.');
            return redirect()->to('/login');
        }

        // Check if absensi exists
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify the absensi belongs to this teacher
        if ($absensi['created_by'] != $userId) {
            $this->session->setFlashdata('error', 'Anda tidak memiliki akses ke absensi ini.');
            return redirect()->to('/guru/absensi');
        }

        // Check if absensi is editable
        if (!$this->isAbsensiEditable($absensi)) {
            $this->session->setFlashdata('error', 'Absensi ini sudah tidak dapat diedit (lebih dari 24 jam).');
            return redirect()->to('/guru/absensi/detail/' . $id);
        }

        // Validate input
        $rules = [
            'pertemuan_ke' => 'required|numeric|greater_than[0]',
            'materi_pembelajaran' => 'permit_empty',
            'siswa' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update absensi data
        $absensiData = [
            'id' => $id,
            'pertemuan_ke' => $this->request->getPost('pertemuan_ke'),
            'materi_pembelajaran' => $this->request->getPost('materi_pembelajaran')
        ];

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update absensi
            $this->absensiModel->save($absensiData);

            // Update absensi details
            $siswaData = $this->request->getPost('siswa');

            foreach ($siswaData as $siswaId => $data) {
                $existing = $this->absensiDetailModel
                    ->where('absensi_id', $id)
                    ->where('siswa_id', $siswaId)
                    ->first();

                if ($existing) {
                    // Update existing record
                    $this->absensiDetailModel->update($existing['id'], [
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null
                    ]);
                } else {
                    // Insert new record
                    $this->absensiDetailModel->insert([
                        'absensi_id' => $id,
                        'siswa_id' => $siswaId,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'waktu_absen' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal memperbarui data absensi.');
            }

            $this->session->setFlashdata('success', 'Absensi berhasil diperbarui!');
            return redirect()->to('/guru/absensi/detail/' . $id);
        } catch (\Exception $e) {
            $db->transRollback();
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete absensi
     */
    public function delete($id)
    {
        // Check if user is logged in and has guru role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'guru_mapel') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan.');
            return redirect()->to('/login');
        }

        // Check if absensi exists
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify the absensi belongs to this teacher
        if ($absensi['created_by'] != $userId) {
            $this->session->setFlashdata('error', 'Anda tidak memiliki akses ke absensi ini.');
            return redirect()->to('/guru/absensi');
        }

        // Check if absensi is editable
        if (!$this->isAbsensiEditable($absensi)) {
            $this->session->setFlashdata('error', 'Absensi ini sudah tidak dapat dihapus (lebih dari 24 jam).');
            return redirect()->to('/guru/absensi/detail/' . $id);
        }

        // Delete absensi (cascade will delete absensi_detail)
        if ($this->absensiModel->delete($id)) {
            $this->session->setFlashdata('success', 'Absensi berhasil dihapus!');
        } else {
            $this->session->setFlashdata('error', 'Gagal menghapus absensi.');
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

        $jadwal = $this->jadwalModel->getByGuru($guru['id'], $hari);

        return $this->response->setJSON([
            'success' => true,
            'jadwal' => $jadwal
        ]);
    }

    /**
     * Print absensi
     */
    public function print($id)
    {
        // Check if user is logged in and has guru role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'guru_mapel') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan.');
            return redirect()->to('/login');
        }

        // Get absensi with details
        $absensi = $this->absensiModel->getAbsensiWithDetail($id);

        if (!$absensi) {
            throw new PageNotFoundException('Data absensi tidak ditemukan.');
        }

        // Verify the absensi belongs to this teacher
        if ($absensi['created_by'] != $userId) {
            $this->session->setFlashdata('error', 'Anda tidak memiliki akses ke absensi ini.');
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
            'Saturday' => 'Sabtu'
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

    private function getNextPertemuan($guruId, $kelasId = null)
    {
        $builder = $this->absensiModel
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.guru_id', $guruId);

        if ($kelasId) {
            $builder->where('jadwal_mengajar.kelas_id', $kelasId);
        }

        $lastAbsensi = $builder->orderBy('pertemuan_ke', 'DESC')
            ->first();

        return $lastAbsensi ? ($lastAbsensi['pertemuan_ke'] + 1) : 1;
    }

    private function getHariList()
    {
        return [
            'Senin' => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu' => 'Rabu',
            'Kamis' => 'Kamis',
            'Jumat' => 'Jumat',
            'Sabtu' => 'Sabtu'
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
