<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Services\AbsensiGuruService;
use App\Models\GuruModel;
use CodeIgniter\I18n\Time;

class AbsensiGuruController extends BaseController
{
    protected $absensiGuruService;
    protected $guruModel;

    public function __construct()
    {
        $this->absensiGuruService = new AbsensiGuruService();
        $this->guruModel = new GuruModel();
    }

    /**
     * Display absensi guru page (check-in/check-out interface)
     */
    public function index()
    {
        $userId = session()->get('user_id');
        
        // Get guru data
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get today's absensi status
        $todayAbsensi = $this->absensiGuruService->getTodayAbsensi($guru['id']);
        
        // Get recent history (last 7 days)
        $historyResult = $this->absensiGuruService->getHistory($guru['id'], ['per_page' => 7]);
        $recentHistory = $historyResult['success'] ? $historyResult['data']['data'] : [];

        // Get monthly stats
        $statsResult = $this->absensiGuruService->getMonthlyStats($guru['id']);
        $monthlyStats = $statsResult['success'] ? $statsResult['data'] : [];

        $data = [
            'title' => 'Absensi Guru',
            'guru' => $guru,
            'todayAbsensi' => $todayAbsensi,
            'recentHistory' => $recentHistory,
            'monthlyStats' => $monthlyStats,
            'hasCheckedIn' => $todayAbsensi !== null,
            'hasCheckedOut' => $todayAbsensi !== null && $todayAbsensi['jam_keluar'] !== null,
        ];

        return view('guru/absensi_guru/index', $data);
    }

    /**
     * Handle check-in submission (AJAX)
     */
    public function checkIn()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data guru tidak ditemukan'
            ]);
        }

        // Validation rules
        $rules = [
            'foto' => [
                'rules' => 'uploaded[foto]|max_size[foto,2048]|is_image[foto]',
                'errors' => [
                    'uploaded' => 'Foto wajib diupload',
                    'max_size' => 'Ukuran foto maksimal 2MB',
                    'is_image' => 'File harus berupa gambar'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Prepare data
        $data = [
            'tanggal' => Time::today()->toDateString(),
            'check_in' => Time::now()->toTimeString(),
            'foto' => $this->request->getFile('foto'),
            'catatan' => $this->request->getPost('keterangan_masuk'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
        ];

        // Perform check-in
        $result = $this->absensiGuruService->checkIn($guru['id'], $data);

        return $this->response->setJSON($result);
    }

    /**
     * Handle check-out submission (AJAX)
     */
    public function checkOut()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data guru tidak ditemukan'
            ]);
        }

        // Validation rules (foto optional for check-out)
        $rules = [
            'foto' => [
                'rules' => 'max_size[foto,2048]|is_image[foto]',
                'errors' => [
                    'max_size' => 'Ukuran foto maksimal 2MB',
                    'is_image' => 'File harus berupa gambar'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Prepare data
        $data = [
            'check_out' => Time::now()->toTimeString(),
            'keterangan_keluar' => $this->request->getPost('keterangan_keluar'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
        ];

        // Add foto if uploaded
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid()) {
            $data['foto'] = $foto;
        }

        // Perform check-out
        $result = $this->absensiGuruService->checkOut($guru['id'], $data);

        return $this->response->setJSON($result);
    }

    /**
     * Display history page
     */
    public function history()
    {
        $userId = session()->get('user_id');
        
        // Get guru data
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get filter parameters
        $filters = [
            'bulan' => $this->request->getGet('bulan') ?? Time::now()->month,
            'tahun' => $this->request->getGet('tahun') ?? Time::now()->year,
            'status' => $this->request->getGet('status'),
            'per_page' => 31
        ];

        // Get absensi history
        $result = $this->absensiGuruService->getHistory($guru['id'], $filters);
        $absensiList = $result['success'] ? $result['data']['data'] : [];
        $pager = $result['success'] ? $result['data']['pager'] : null;

        // Get monthly stats
        $statsResult = $this->absensiGuruService->getMonthlyStats($guru['id'], $filters['bulan'], $filters['tahun']);
        $monthlyStats = $statsResult['success'] ? $statsResult['data'] : [];

        $data = [
            'title' => 'Riwayat Absensi',
            'guru' => $guru,
            'absensiList' => $absensiList,
            'pager' => $pager,
            'filters' => $filters,
            'monthlyStats' => $monthlyStats
        ];

        return view('guru/absensi_guru/history', $data);
    }

    /**
     * Show detail of specific absensi record
     */
    public function show($id)
    {
        $userId = session()->get('user_id');
        
        // Get guru data
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get absensi record
        $absensiGuruModel = new \App\Models\AbsensiGuruModel();
        $absensi = $absensiGuruModel
            ->where('id', $id)
            ->where('guru_id', $guru['id'])
            ->first();

        if (!$absensi) {
            return redirect()->to('/guru/absensi-guru/history')->with('error', 'Data absensi tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Absensi',
            'guru' => $guru,
            'absensi' => $absensi
        ];

        return view('guru/absensi_guru/show', $data);
    }

    /**
     * Capture photo using webcam (returns camera interface)
     */
    public function camera()
    {
        $type = $this->request->getGet('type') ?? 'check-in'; // check-in or check-out
        
        $data = [
            'title' => 'Ambil Foto',
            'type' => $type
        ];

        return view('guru/absensi_guru/camera', $data);
    }
}
