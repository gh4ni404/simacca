<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Services\AbsensiPklService;
use App\Models\GuruModel;
use App\Models\PembimbingPklModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class AbsensiPklController extends BaseController
{
    protected $absensiPklService;
    protected $guruModel;
    protected $pembimbingPklModel;
    protected $session;

    public function __construct()
    {
        $this->absensiPklService = new AbsensiPklService();
        $this->guruModel = new GuruModel();
        $this->pembimbingPklModel = new PembimbingPklModel();
        $this->session = session();
    }

    /**
     * List absensi PKL
     */
    public function index()
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login');
        }

        $tanggal = $this->request->getGet('tanggal');

        $result = $this->absensiPklService->getByGuru($guru['id'], $tanggal);
        $absensi = $result['data'] ?? [];

        // Group by tempat_pkl
        $grouped = [];
        foreach ($absensi as $item) {
            $key = $item['tempat_pkl_id'] ?? $item['pembimbing_pkl_id'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'nama_perusahaan' => $item['nama_perusahaan'],
                    'kota'            => $item['kota'] ?? '-',
                    'nama_pembimbing' => $item['nama_pembimbing'],
                    'absensi'         => [],
                ];
            }
            $grouped[$key]['absensi'][] = $item;
        }

        $data = [
            'title'        => 'Absensi PKL',
            'pageTitle'    => 'Absensi PKL',
            'pageDescription' => 'Kelola absensi kehadiran siswa PKL',
            'absensi'      => $absensi,
            'grouped'      => $grouped,
            'guru'         => $guru,
            'tanggal'      => $tanggal,
        ];

        return view('guru/absensi-pkl/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login');
        }

        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        // Get siswa by pembimbing
        $siswaResult = $this->absensiPklService->getSiswaByPembimbing($guru['id']);
        $siswaData = $siswaResult['data'] ?? [];
        $siswaList = $siswaData['siswa'] ?? [];
        $pembimbingList = $siswaData['pembimbing_list'] ?? [];

        // Group siswa by tempat_pkl for display
        $groupedSiswa = [];
        foreach ($siswaList as $s) {
            $key = $s['pembimbing_pkl_id'];
            if (!isset($groupedSiswa[$key])) {
                $groupedSiswa[$key] = [
                    'pembimbing_pkl_id' => $key,
                    'nama_perusahaan'   => $s['nama_perusahaan'],
                    'siswa'             => [],
                ];
            }
            $groupedSiswa[$key]['siswa'][] = $s;
        }

        $statusOptions = [
            'hadir'  => ['label' => 'Hadir',  'color' => 'green',  'icon' => 'fa-check-circle'],
            'izin'   => ['label' => 'Izin',   'color' => 'blue',   'icon' => 'fa-file-alt'],
            'sakit'  => ['label' => 'Sakit',  'color' => 'yellow', 'icon' => 'fa-medkit'],
            'alpa'   => ['label' => 'Alpa',   'color' => 'red',    'icon' => 'fa-times-circle'],
            'dispen' => ['label' => 'Dispen', 'color' => 'purple', 'icon' => 'fa-id-badge'],
        ];

        $data = [
            'title'           => 'Input Absensi PKL',
            'pageTitle'       => 'Input Absensi PKL',
            'pageDescription' => 'Catat kehadiran siswa PKL',
            'guru'            => $guru,
            'tanggal'         => $tanggal,
            'siswaList'       => $siswaList,
            'groupedSiswa'    => $groupedSiswa,
            'pembimbingList'  => $pembimbingList,
            'statusOptions'   => $statusOptions,
        ];

        return view('guru/absensi-pkl/create', $data);
    }

    /**
     * Store new absensi
     */
    public function store()
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login');
        }

        $pembimbingPklId = $this->request->getPost('pembimbing_pkl_id');
        $tanggal = $this->request->getPost('tanggal');
        $keteranganUmum = $this->request->getPost('keterangan_umum');
        $siswaRaw = $this->request->getPost('siswa');

        // Parse siswa data
        $siswa = [];
        if (is_string($siswaRaw)) {
            $siswa = json_decode($siswaRaw, true) ?? [];
        } elseif (is_array($siswaRaw)) {
            $siswa = $siswaRaw;
        }

        $result = $this->absensiPklService->createAbsensiPkl([
            'pembimbing_pkl_id' => (int) $pembimbingPklId,
            'tanggal'           => $tanggal,
            'keterangan_umum'   => $keteranganUmum,
            'created_by'        => $userId,
            'siswa'             => $siswa,
        ]);

        if ($result['success']) {
            return redirect()->to('/guru/absensi-pkl')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Show detail
     */
    public function show($id)
    {
        $result = $this->absensiPklService->getAbsensiDetail((int) $id);

        if (!$result['success']) {
            return redirect()->to('/guru/absensi-pkl')
                ->with('error', $result['message']);
        }

        $data = [
            'title'           => 'Detail Absensi PKL',
            'pageTitle'       => 'Detail Absensi PKL',
            'pageDescription' => 'Detail kehadiran siswa PKL',
            'absensi'         => $result['data']['absensi'],
            'details'         => $result['data']['details'],
            'statistics'      => $result['data']['statistics'],
        ];

        return view('guru/absensi-pkl/show', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login');
        }

        $result = $this->absensiPklService->getAbsensiDetail((int) $id);

        if (!$result['success']) {
            return redirect()->to('/guru/absensi-pkl')
                ->with('error', $result['message']);
        }

        $absensi = $result['data']['absensi'];
        $details = $result['data']['details'];

        // Get siswa by pembimbing
        $siswaResult = $this->absensiPklService->getSiswaByPembimbing($guru['id']);
        $siswaData = $siswaResult['data'] ?? [];
        $siswaList = $siswaData['siswa'] ?? [];

        // Merge existing detail data into siswa list
        $detailMap = [];
        foreach ($details as $d) {
            $detailMap[$d['siswa_id']] = $d;
        }

        $mergedSiswa = [];
        foreach ($siswaList as $s) {
            if ($s['pembimbing_pkl_id'] == $absensi['pembimbing_pkl_id']) {
                $s['status'] = $detailMap[$s['siswa_id']]['status'] ?? 'alpa';
                $s['keterangan'] = $detailMap[$s['siswa_id']]['keterangan'] ?? '';
                $mergedSiswa[] = $s;
            }
        }

        $statusOptions = [
            'hadir'  => ['label' => 'Hadir',  'color' => 'green',  'icon' => 'fa-check-circle'],
            'izin'   => ['label' => 'Izin',   'color' => 'blue',   'icon' => 'fa-file-alt'],
            'sakit'  => ['label' => 'Sakit',  'color' => 'yellow', 'icon' => 'fa-medkit'],
            'alpa'   => ['label' => 'Alpa',   'color' => 'red',    'icon' => 'fa-times-circle'],
            'dispen' => ['label' => 'Dispen', 'color' => 'purple', 'icon' => 'fa-id-badge'],
        ];

        $data = [
            'title'           => 'Edit Absensi PKL',
            'pageTitle'       => 'Edit Absensi PKL',
            'pageDescription' => 'Perbarui data kehadiran siswa PKL',
            'absensi'         => $absensi,
            'details'         => $details,
            'siswaList'       => $mergedSiswa,
            'statusOptions'   => $statusOptions,
        ];

        return view('guru/absensi-pkl/edit', $data);
    }

    /**
     * Update absensi
     */
    public function update($id)
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login');
        }

        $tanggal = $this->request->getPost('tanggal');
        $keteranganUmum = $this->request->getPost('keterangan_umum');
        $siswaRaw = $this->request->getPost('siswa');

        $siswa = [];
        if (is_string($siswaRaw)) {
            $siswa = json_decode($siswaRaw, true) ?? [];
        } elseif (is_array($siswaRaw)) {
            $siswa = $siswaRaw;
        }

        $result = $this->absensiPklService->updateAbsensiPkl((int) $id, [
            'tanggal'         => $tanggal,
            'keterangan_umum' => $keteranganUmum,
            'siswa'           => $siswa,
        ]);

        if ($result['success']) {
            return redirect()->to('/guru/absensi-pkl/show/' . $id)
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Delete absensi
     */
    public function delete($id)
    {
        $result = $this->absensiPklService->deleteAbsensiPkl((int) $id);

        if ($result['success']) {
            return redirect()->to('/guru/absensi-pkl')
                ->with('success', $result['message']);
        }

        return redirect()->to('/guru/absensi-pkl')
            ->with('error', $result['message']);
    }

    /**
     * AJAX: Get siswa by pembimbing
     */
    public function getSiswaByPembimbing()
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data guru tidak ditemukan',
            ]);
        }

        $result = $this->absensiPklService->getSiswaByPembimbing($guru['id']);
        return $this->response->setJSON($result);
    }
}
