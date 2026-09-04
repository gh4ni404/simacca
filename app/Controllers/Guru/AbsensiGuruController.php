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
     * Combined with history for better UX
     */
    public function index()
    {
        $userId = session()->get('user_id');
        
        // Get guru data
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get today's absensi status
        $todayAbsensi = $this->absensiGuruService->getTodayAbsensi($guru['id']);
        
        // Get recent history (last 7 days)
        $historyResult = $this->absensiGuruService->getHistory($guru['id'], ['per_page' => 7]);
        $recentHistory = $historyResult['success'] ? $historyResult['data']['data'] : [];

        // Get filter parameters for history tab
        $filters = [
            'bulan' => $this->request->getGet('bulan') ?? \CodeIgniter\I18n\Time::now()->month,
            'tahun' => $this->request->getGet('tahun') ?? \CodeIgniter\I18n\Time::now()->year,
            'status' => $this->request->getGet('status'),
            'per_page' => 20
        ];

        // Get full history for history tab
        $fullHistoryResult = $this->absensiGuruService->getHistory($guru['id'], $filters);
        $absensiList = $fullHistoryResult['success'] ? $fullHistoryResult['data']['data'] : [];
        $pager = $fullHistoryResult['success'] ? $fullHistoryResult['data']['pager'] : null;

        // Get monthly stats
        $statsResult = $this->absensiGuruService->getMonthlyStats($guru['id'], $filters['bulan'], $filters['tahun']);
        $monthlyStats = $statsResult['success'] ? $statsResult['data'] : [];

        $data = [
            'title' => 'Absensi Guru',
            'guru' => $guru,
            'todayAbsensi' => $todayAbsensi,
            'recentHistory' => $recentHistory,
            'monthlyStats' => $monthlyStats,
            'hasCheckedIn' => $todayAbsensi !== null,
            'hasCheckedOut' => $todayAbsensi !== null && $todayAbsensi['check_out'] !== null,
            // History tab data
            'absensiList' => $absensiList,
            'pager' => $pager,
            'filters' => $filters,
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
                'message' => 'Request nggak valid nih 🤔'
            ]);
        }

        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data guru nggak ketemu nih 🤔'
            ]);
        }

        // Get photo file from request (both camera capture and file upload send as multipart 'foto')
        $fotoFile = $this->request->getFile('foto');
        
        // Validate that photo is uploaded and valid
        if (!$fotoFile || !$fotoFile->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Foto wajib diupload atau diambil pake kamera ya 📷'
            ]);
        }

        // Prepare data
        $data = [
            'tanggal'   => Time::today()->toDateString(),
            'check_in'  => Time::now()->toTimeString(),
            'catatan'   => $this->request->getPost('keterangan_masuk'),
            'latitude'  => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'foto'      => $fotoFile,
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
                'message' => 'Request nggak valid nih 🤔'
            ]);
        }

        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data guru nggak ketemu nih 🤔'
            ]);
        }

        // Prepare data
        $data = [
            'check_out'         => Time::now()->toTimeString(),
            'keterangan_keluar' => $this->request->getPost('keterangan_keluar'),
            'latitude'          => $this->request->getPost('latitude'),
            'longitude'         => $this->request->getPost('longitude'),
        ];

        // Handle photo (optional for check-out)
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid()) {
            $data['foto'] = $fotoFile;
        }

        // Perform check-out
        $result = $this->absensiGuruService->checkOut($guru['id'], $data);

        return $this->response->setJSON($result);
    }

    /**
     * Display history page
     * @deprecated Redirects to main page with history tab
     */
    public function history()
    {
        // Redirect to the unified interface with history tab
        $params = $this->request->getGet();
        $params['tab'] = 'history';
        $queryString = http_build_query($params);
        
        return redirect()->to('/guru/absensi-guru' . ($queryString ? '?' . $queryString : '?tab=history'));
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
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get absensi record
        $absensiGuruModel = new \App\Models\AbsensiGuruModel();
        $absensi = $absensiGuruModel
            ->where('id', $id)
            ->where('guru_id', $guru['id'])
            ->first();

        if (!$absensi) {
            return redirect()->to('/guru/absensi-guru/history')->with('error', 'Data absensi nggak ketemu 🤔');
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
