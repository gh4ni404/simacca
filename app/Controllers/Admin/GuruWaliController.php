<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\GuruWaliService;

class GuruWaliController extends BaseController
{
    protected $guruWaliService;

    public function __construct()
    {
        $this->guruWaliService = new GuruWaliService();

        // Check authentication and authorized roles
        $role = session()->get('role');
        if (!session()->get('isLoggedIn') || !in_array($role, ['admin', 'kepala_sekolah'])) {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Display the Master Guru Wali dashboard
     */
    public function index()
    {
        $tahunAjaran = $this->request->getGet('tahun_ajaran') ?: get_active_tahun_ajaran();
        
        $filters = [
            'kelas_id' => $this->request->getGet('kelas_id') ?: null,
            'tingkat'  => $this->request->getGet('tingkat') ?: null,
            'jurusan'  => $this->request->getGet('jurusan') ?: null,
            'guru_id'  => $this->request->getGet('guru_id') ?: null,
            'status'   => $this->request->getGet('status') ?: null,
            'search'   => $this->request->getGet('search') ?: null,
        ];

        $tab = $this->request->getGet('tab') ?: 'pemetaan'; // 'pemetaan' or 'guru'

        $result = $this->guruWaliService->getOverviewData($tahunAjaran, $filters);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        }

        $overviewData = $result['data'] ?? [];

        $activeTahunAjaran = get_active_tahun_ajaran();
        $tahunAjaranList   = get_tahun_ajaran_list();

        $data = [
            'title'             => 'Data Guru Wali',
            'pageTitle'         => 'Data Guru Wali (Pembimbing Siswa)',
            'pageDescription'   => 'Kelola dan petakan pembagian individu siswa ke Guru Wali (Pembimbing Personal)',
            'user'              => $this->getUserData(),
            'tahunAjaran'       => $tahunAjaran,
            'activeTahunAjaran' => $activeTahunAjaran,
            'tahunAjaranList'   => $tahunAjaranList,
            'filters'           => $filters,
            'tab'               => $tab,
            'stats'             => $overviewData['stats'] ?? [],
            'siswaList'         => $overviewData['siswaList'] ?? [],
            'teacherList'       => $overviewData['teacherList'] ?? [],
            'kelasList'         => $overviewData['kelasList'] ?? [],
            'availableGuru'     => $overviewData['availableGuru'] ?? [],
            'jurusanList'       => $overviewData['jurusanList'] ?? [],
        ];

        return view('admin/guru_wali/index', $data);
    }

    /**
     * Assign a single student to a Guru Wali
     */
    public function assign()
    {
        // Admin only action
        if (session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya Admin yang memiliki hak akses mengubah penugasan.']);
        }

        $siswaId     = (int) $this->request->getPost('siswa_id');
        $guruId      = (int) $this->request->getPost('guru_id');
        $tahunAjaran = $this->request->getPost('tahun_ajaran') ?: get_active_tahun_ajaran();
        $keterangan  = $this->request->getPost('keterangan');

        if (!$siswaId || !$guruId) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Siswa dan Guru Wali harus dipilih.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $result = $this->guruWaliService->assignSiswa($siswaId, $guruId, $tahunAjaran, $keterangan);
        $result['csrf_token'] = csrf_token();
        $result['csrf_hash']  = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->back();
    }

    /**
     * Bulk assign multiple students to a Guru Wali
     */
    public function bulkAssign()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Hanya Admin yang memiliki hak akses mengubah penugasan.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $siswaIds    = $this->request->getPost('siswa_ids');
        $guruId      = (int) $this->request->getPost('guru_id');
        $tahunAjaran = $this->request->getPost('tahun_ajaran') ?: get_active_tahun_ajaran();

        if (empty($siswaIds) || !is_array($siswaIds)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Pilih minimal satu siswa',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        if (!$guruId) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Pilih Guru Wali tujuan',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $result = $this->guruWaliService->bulkAssignSiswa($siswaIds, $guruId, $tahunAjaran);
        $result['csrf_token'] = csrf_token();
        $result['csrf_hash']  = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->back();
    }

    /**
     * Auto distribute unassigned students evenly across selected teachers
     */
    public function autoDistribute()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Hanya Admin yang memiliki hak akses mengubah penugasan.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $siswaIds    = $this->request->getPost('siswa_ids') ?: [];
        $guruIds     = $this->request->getPost('guru_ids') ?: [];
        $tahunAjaran = $this->request->getPost('tahun_ajaran') ?: get_active_tahun_ajaran();

        if (empty($guruIds) || !is_array($guruIds)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Pilih minimal satu Guru Wali untuk pembagian',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $result = $this->guruWaliService->autoDistribute($siswaIds, $guruIds, $tahunAjaran);
        $result['csrf_token'] = csrf_token();
        $result['csrf_hash']  = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->back();
    }

    /**
     * Unassign a student from their Guru Wali
     */
    public function unassign($siswaId)
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Hanya Admin yang memiliki hak akses mengubah penugasan.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $tahunAjaran = $this->request->getPost('tahun_ajaran') ?: ($this->request->getGet('tahun_ajaran') ?: get_active_tahun_ajaran());
        $result = $this->guruWaliService->unassignSiswa((int) $siswaId, $tahunAjaran);
        $result['csrf_token'] = csrf_token();
        $result['csrf_hash']  = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->back();
    }

    /**
     * Bulk unassign multiple students
     */
    public function bulkUnassign()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Hanya Admin yang memiliki hak akses mengubah penugasan.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $siswaIds    = $this->request->getPost('siswa_ids');
        $tahunAjaran = $this->request->getPost('tahun_ajaran') ?: get_active_tahun_ajaran();

        if (empty($siswaIds) || !is_array($siswaIds)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Pilih minimal satu siswa',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $result = $this->guruWaliService->bulkUnassignSiswa($siswaIds, $tahunAjaran);
        $result['csrf_token'] = csrf_token();
        $result['csrf_hash']  = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->back();
    }

    /**
     * AJAX endpoint to get list of students for a specific Guru Wali
     */
    public function getSiswaByGuru($guruId)
    {
        $tahunAjaran = $this->request->getGet('tahun_ajaran') ?: get_active_tahun_ajaran();
        $result = $this->guruWaliService->getSiswaByGuru((int) $guruId, $tahunAjaran);
        return $this->response->setJSON($result);
    }

    /**
     * Official Printable Page for Guru Wali & Siswa Binaan
     */
    public function print()
    {
        $tahunAjaran = $this->request->getGet('tahun_ajaran') ?: get_active_tahun_ajaran();
        $guruId      = $this->request->getGet('guru_id') ? (int) $this->request->getGet('guru_id') : null;

        $result = $this->guruWaliService->getPrintData($tahunAjaran, $guruId);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/admin/guru-wali');
        }

        $settingModel = new \App\Models\SettingModel();
        $sekolahInfo = [
            'nama_sekolah'        => $settingModel->get('nama_sekolah') ?: 'SMK NEGERI 1 SIMACCA',
            'alamat'              => $settingModel->get('alamat_sekolah') ?: 'Jl. Pendidikan No. 1',
            'telepon'             => $settingModel->get('telepon_sekolah') ?: '-',
            'email'               => $settingModel->get('email_sekolah') ?: '-',
            'website'             => $settingModel->get('website_sekolah') ?: 'https://simacca.sch.id',
            'kepala_sekolah'      => $settingModel->get('kepala_sekolah_nama') ?: 'Kepala Sekolah',
            'nip_kepala_sekolah'  => $settingModel->get('kepala_sekolah_nip') ?: '-',
            'kota'                => $settingModel->get('kota_sekolah') ?: 'Kota',
        ];

        $data = [
            'title'        => 'Cetak Daftar Guru Wali & Siswa Binaan',
            'tahunAjaran'  => $tahunAjaran,
            'stats'        => $result['data']['stats'] ?? [],
            'guruWaliList' => $result['data']['guruWaliList'] ?? [],
            'sekolahInfo'  => $sekolahInfo,
        ];

        return view('admin/guru_wali/print', $data);
    }

    /**
     * Export Guru Wali mapping to CSV
     */
    public function export()
    {
        $tahunAjaran = $this->request->getGet('tahun_ajaran') ?: get_active_tahun_ajaran();
        $result = $this->guruWaliService->getOverviewData($tahunAjaran, []);

        $siswaList = $result['data']['siswaList'] ?? [];

        $filename = 'Data_Guru_Wali_Siswa_' . str_replace('/', '-', $tahunAjaran) . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // Add BOM for proper UTF-8 Excel display
        fputs($output, "\xEF\xBB\xBF");

        // CSV Header
        fputcsv($output, ['No', 'NIS', 'Nama Siswa', 'L/P', 'Kelas', 'Tingkat', 'Jurusan', 'NIP Guru Wali', 'Nama Guru Wali', 'Mata Pelajaran', 'Tahun Ajaran', 'Keterangan']);

        $no = 1;
        foreach ($siswaList as $row) {
            fputcsv($output, [
                $no++,
                $row['nis'],
                $row['nama_siswa'],
                $row['jenis_kelamin'],
                $row['nama_kelas'] ?? '-',
                $row['tingkat'] ?? '-',
                $row['jurusan'] ?? '-',
                $row['guru_nip'] ?? '-',
                $row['nama_guru'] ?? 'Belum Ditugaskan',
                $row['nama_mapel'] ?? '-',
                $row['tahun_ajaran'],
                $row['mapping_keterangan'] ?? '-',
            ]);
        }

        fclose($output);
        exit;
    }
}
