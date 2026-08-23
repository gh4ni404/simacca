<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\GuruPiketModel;
use App\Models\JurnalPiketModel;
use App\Services\JurnalPiketService;
use App\Services\GuruPiketService;

class SimulasiPiketController extends BaseController
{
    protected $guruModel;
    protected $guruPiketModel;
    protected $jurnalPiketModel;
    protected $jurnalPiketService;
    protected $guruPiketService;

    public function __construct()
    {
        $this->guruModel          = new GuruModel();
        $this->guruPiketModel     = new GuruPiketModel();
        $this->jurnalPiketModel   = new JurnalPiketModel();
        $this->jurnalPiketService = new JurnalPiketService();
        $this->guruPiketService   = new GuruPiketService();

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            // Allow if currently impersonating
            if (!session()->get('original_admin_id')) {
                return redirect()->to('/access-denied');
            }
        }
    }

    /**
     * Display simulation testing portal for Admin
     */
    public function index()
    {
        $tahunAjaran = get_active_tahun_ajaran();
        $semester = $this->request->getGet('semester') ?: 'ganjil';
        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $selectedGuruId = $this->request->getGet('guru_id');

        // Fetch all active teachers
        $guruList = $this->guruModel->select('guru.id, guru.nama_lengkap, guru.nip, guru.jenis_kelamin, guru.user_id')
            ->join('users', 'users.id = guru.user_id')
            ->where('users.is_active', 1)
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();

        if (empty($selectedGuruId) && !empty($guruList)) {
            $selectedGuruId = $guruList[0]['id'];
        }

        $selectedGuru = null;
        $piketSchedules = [];
        $mappedJobdesk = null;
        $jurnalHariIni = null;
        $rincianTugasAuto = '';

        if (!empty($selectedGuruId)) {
            $selectedGuru = $this->guruModel->find($selectedGuruId);

            // Fetch piket schedules for this guru
            $piketSchedules = $this->guruPiketModel->select('guru_piket.*, master_jobdesk_piket.kode_jobdesk, master_jobdesk_piket.nama_jobdesk')
                ->join('master_jobdesk_piket', 'master_jobdesk_piket.id = guru_piket.jobdesk_id', 'left')
                ->where('guru_piket.guru_id', $selectedGuruId)
                ->where('guru_piket.tahun_ajaran', $tahunAjaran)
                ->where('guru_piket.semester', $semester)
                ->where('guru_piket.is_active', 1)
                ->findAll();

            // Fetch mapped jobdesk info
            foreach ($piketSchedules as $ps) {
                if (!empty($ps['jobdesk_id']) && !empty($ps['nama_jobdesk'])) {
                    $mappedJobdesk = [
                        'id'            => $ps['jobdesk_id'],
                        'kode_jobdesk'  => $ps['kode_jobdesk'],
                        'nama_jobdesk'  => $ps['nama_jobdesk'],
                        'rincian_tugas' => $ps['rincian_tugas'],
                    ];
                    break;
                }
            }

            // Fetch rincian tugas for current date using service
            $rincianTugasAuto = $this->jurnalPiketService->getRincianTugasForGuruAndDate((int)$selectedGuruId, $tanggal);

            // Fetch existing journal for selected date
            $jurnalHariIni = $this->jurnalPiketModel->getJurnalByGuruAndTanggal((int)$selectedGuruId, $tanggal);
        }

        $data = [
            'title'            => 'Simulasi & Test End-to-End Guru Piket',
            'pageTitle'        => 'Simulasi Test Piket Guru',
            'pageDescription'  => 'Uji coba alur tugas piket & jurnal tanpa perlu relogin akun guru',
            'user'             => $this->getUserData(),
            'guruList'         => $guruList,
            'selectedGuruId'   => $selectedGuruId,
            'selectedGuru'     => $selectedGuru,
            'tanggal'          => $tanggal,
            'tahunAjaran'      => $tahunAjaran,
            'semester'         => $semester,
            'piketSchedules'   => $piketSchedules,
            'mappedJobdesk'    => $mappedJobdesk,
            'jurnalHariIni'    => $jurnalHariIni,
            'rincianTugasAuto' => $rincianTugasAuto,
        ];

        return view('admin/simulasi_piket/index', $data);
    }

    /**
     * Store simulated Jurnal Piket entry
     */
    public function simulasiJurnal()
    {
        $guruId = $this->request->getPost('guru_id');
        $tanggal = $this->request->getPost('tanggal');
        $deskripsi = $this->request->getPost('deskripsi');
        $rincianTugas = $this->request->getPost('rincian_tugas');
        $file = $this->request->getFile('foto');

        $tahunAjaran = get_active_tahun_ajaran();
        $month = (int) date('m', strtotime($tanggal));
        $semester = ($month >= 7 && $month <= 12) ? 'ganjil' : 'genap';

        $data = [
            'guru_id'       => $guruId,
            'tanggal'       => $tanggal,
            'tahun_ajaran'  => $tahunAjaran,
            'semester'      => $semester,
            'deskripsi'     => $deskripsi,
            'rincian_tugas' => $rincianTugas,
        ];

        $result = $this->jurnalPiketService->create($data, $file);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to('/admin/simulasi-piket?guru_id=' . $guruId . '&tanggal=' . $tanggal)
            ->with('success', 'Simulasi pengisian Jurnal Piket berhasil disimpan!');
    }

    /**
     * Impersonate selected teacher (1-Click Switch Account to test teacher UI)
     */
    public function impersonate($guruId)
    {
        $guru = $this->guruModel->select('guru.*, users.username, users.email, users.role')
            ->join('users', 'users.id = guru.user_id')
            ->find($guruId);

        if (!$guru) {
            return redirect()->back()->with('error', 'Guru tidak ditemukan');
        }

        // Save original admin session
        session()->set('original_admin_id', session()->get('user_id'));
        session()->set('original_admin_username', session()->get('username'));
        session()->set('original_admin_name', session()->get('nama_lengkap'));
        session()->set('original_admin_role', session()->get('role'));
        session()->set('original_admin_all_roles', session()->get('all_roles'));

        // Set teacher session with exact user role from DB (e.g. guru_mapel)
        $teacherRole = !empty($guru['role']) ? $guru['role'] : 'guru_mapel';
        $teacherAllRoles = [$teacherRole, 'guru_mapel', 'guru_piket'];

        session()->set('isLoggedIn', true);
        session()->set('user_id', $guru['user_id']);
        session()->set('userId', $guru['user_id']);
        session()->set('guru_id', $guru['id']);
        session()->set('role', $teacherRole);
        session()->set('all_roles', $teacherAllRoles);
        session()->set('username', $guru['username']);
        session()->set('nama_lengkap', $guru['nama_lengkap']);
        session()->set('is_impersonating', true);

        return redirect()->to('/guru/jurnal-piket')->with('info', "Mode Simulasi: Anda saat ini bertindak sebagai Guru {$guru['nama_lengkap']}. Tampilan & hak akses disesuaikan.");
    }

    /**
     * Stop impersonating and return to Admin
     */
    public function stopImpersonate()
    {
        $originalAdminId = session()->get('original_admin_id');
        $originalAdminUsername = session()->get('original_admin_username');
        $originalAdminName = session()->get('original_admin_name');
        $originalAdminRole = session()->get('original_admin_role') ?: 'admin';
        $originalAdminAllRoles = session()->get('original_admin_all_roles') ?: ['admin'];

        if (!$originalAdminId) {
            return redirect()->to('/login');
        }

        session()->set('isLoggedIn', true);
        session()->set('user_id', $originalAdminId);
        session()->set('role', $originalAdminRole);
        session()->set('all_roles', $originalAdminAllRoles);
        session()->set('username', $originalAdminUsername);
        session()->set('nama_lengkap', $originalAdminName);

        session()->remove('original_admin_id');
        session()->remove('original_admin_username');
        session()->remove('original_admin_name');
        session()->remove('original_admin_role');
        session()->remove('original_admin_all_roles');
        session()->remove('guru_id');
        session()->remove('is_impersonating');

        return redirect()->to('/admin/simulasi-piket')->with('success', 'Kembali ke sesi Portal Admin.');
    }
}
