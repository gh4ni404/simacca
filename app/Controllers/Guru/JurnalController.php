<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Services\JurnalKbmService;
use App\Models\AbsensiModel;
use App\Models\GuruModel;

class JurnalController extends BaseController
{
    protected $jurnalService;
    protected $absensiModel;
    protected $guruModel;

    public function __construct()
    {
        $this->jurnalService = new JurnalKbmService();
        $this->absensiModel = new AbsensiModel();
        $this->guruModel = new GuruModel();
    }

    public function index()
    {
        // Get guru data from session
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get filter dari request
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Get jurnal by guru using service
        $result = $this->jurnalService->getJurnalByGuru($guru['id'], $startDate, $endDate);
        
        if (!$result['success']) {
            return redirect()->to('/guru/dashboard')->with('error', $result['message']);
        }

        $jurnalRaw = $result['data'];
        
        // Group by kelas
        $kelasList = [];
        foreach ($jurnalRaw as $j) {
            $kelasId = $j['kelas_id'];
            if (!isset($kelasList[$kelasId])) {
                $kelasList[$kelasId] = [
                    'kelas_id' => $kelasId,
                    'nama_kelas' => $j['nama_kelas'],
                    'nama_mapel' => $j['nama_mapel'],
                    'mapel_id' => $j['mapel_id'],
                    'total_pertemuan' => 0,
                    'jurnal' => []
                ];
            }
            $kelasList[$kelasId]['total_pertemuan']++;
            $kelasList[$kelasId]['jurnal'][] = $j;
        }

        $data = [
            'title' => 'Jurnal KBM',
            'guru' => $guru,
            'kelasList' => array_values($kelasList),
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('guru/jurnal/index', $data);
    }

    public function create($absensiId)
    {
        // Get guru data from session
        // Support both 'user_id' and 'userId' for backward compatibility
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get absensi detail
        $absensi = $this->absensiModel->getAbsensiWithDetail($absensiId);

        if (!$absensi) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data absensi nggak ketemu 🤔');
        }

        // Cek apakah sudah ada jurnal untuk absensi ini
        if ($this->jurnalService->isJurnalExist($absensiId)) {
            $existingResult = $this->jurnalService->getJurnalByAbsensi($absensiId);
            if ($existingResult['success']) {
                return redirect()->to('/guru/jurnal/edit/' . $existingResult['data']['id'])
                    ->with('info', 'Jurnal pertemuan ini udah ada nih. Edit aja ya! 📝');
            }
        }

        // Cek apakah guru yang login adalah pembuat absensi (created_by)
        // Ini mencakup both scenarios:
        // 1. Guru mengajar jadwal sendiri (normal mode)
        // 2. Guru pengganti yang input absensi (substitute mode)
        if ($absensi['created_by'] != $userId) {
            return redirect()->to('/guru/jurnal')->with('error', 'Kamu nggak punya akses ke absensi ini 🔐');
        }

        $data = [
            'title' => 'Tambah Jurnal KBM',
            'guru' => $guru,
            'absensi' => $absensi
        ];

        return view('guru/jurnal/create', $data);
    }

    public function store()
    {
        helper('security');
        
        // Validasi input
        $rules = [
            'absensi_id' => 'required|numeric',
            'kegiatan_pembelajaran' => 'required',
            'foto_dokumentasi' => 'permit_empty|uploaded[foto_dokumentasi]|max_size[foto_dokumentasi,1024]|is_image[foto_dokumentasi]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorList = '<ul class="list-disc ml-4">';
            foreach ($errors as $field => $error) {
                $errorList .= '<li>' . $error . '</li>';
            }
            $errorList .= '</ul>';
            session()->setFlashdata('error', 'Isi dulu dong yang lengkap 😊' . $errorList);
            return redirect()->back()->withInput();
        }

        // Cek apakah sudah ada jurnal untuk absensi ini
        $absensiId = $this->request->getPost('absensi_id');
        if ($this->jurnalService->isJurnalExist($absensiId)) {
            $existingResult = $this->jurnalService->getJurnalByAbsensi($absensiId);
            if ($existingResult['success']) {
                session()->setFlashdata('info', 'Jurnal pertemuan ini udah ada nih. Edit aja ya! 📝');
                return redirect()->to('/guru/jurnal/edit/' . $existingResult['data']['id']);
            }
        }

        // Handle foto dokumentasi upload
        $fotoName = null;
        $file = $this->request->getFile('foto_dokumentasi');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validate file with security helper
            $allowedTypes = [
                'image/jpeg',
                'image/jpg', 
                'image/png',
                'image/gif'
            ];
            
            $validation = validate_file_upload($file, $allowedTypes, 1048576); // 1MB
            
            if (!$validation['valid']) {
                session()->setFlashdata('error', '📁 ' . $validation['error']);
                return redirect()->back()->withInput();
            }
            
            // Generate unique filename
            $fotoName = 'jurnal_' . time() . '_' . uniqid() . '.' . $file->getExtension();
            
            // Move file to uploads directory
            try {
                $file->move(WRITEPATH . 'uploads/jurnal', $fotoName);
                
                // Optimize image (compress without losing visible quality)
                helper('image');
                $filePath = WRITEPATH . 'uploads/jurnal/' . $fotoName;
                $originalSize = filesize($filePath);
                
                $optimized = optimize_jurnal_photo($filePath, $filePath);
                
                if ($optimized) {
                    $newSize = filesize($filePath);
                    $savings = round((($originalSize - $newSize) / $originalSize) * 100, 2);
                    log_message('info', "Jurnal photo optimized: {$fotoName} - {$savings}% smaller");
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to upload jurnal foto: ' . $e->getMessage());
                
                $userMessage = '📷 Gagal simpan foto nih 😅 ';
                if (ENVIRONMENT === 'development') {
                    $userMessage .= 'Detail: ' . $e->getMessage();
                } else {
                    $userMessage .= 'Coba lagi ya atau pakai foto lain.';
                }
                
                session()->setFlashdata('error', $userMessage);
                return redirect()->back()->withInput();
            }
        }

        // Insert jurnal using service
        $data = [
            'absensi_id' => $absensiId,
            'tujuan_pembelajaran' => $this->request->getPost('tujuan_pembelajaran') ?? '-',
            'kegiatan_pembelajaran' => $this->request->getPost('kegiatan_pembelajaran'),
            'media_alat' => $this->request->getPost('media_ajar') ?? '-',
            'penilaian' => $this->request->getPost('penilaian') ?? '-',
            'catatan_khusus' => $this->request->getPost('catatan_khusus') ?? '-',
            'foto_dokumentasi' => $fotoName
        ];

        $result = $this->jurnalService->createJurnal($data);

        if ($result['success']) {
            session()->setFlashdata('success', 'Yeay! Jurnal tersimpan. Good job! 📚✨');
            return redirect()->to('/guru/jurnal');
        } else {
            // Delete uploaded file if database insert fails
            if ($fotoName && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
            }
            
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    public function edit($jurnalId)
    {
        // Get guru data from session
        // Support both 'user_id' and 'userId' for backward compatibility
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get jurnal using service
        $result = $this->jurnalService->getJurnalById($jurnalId);

        if (!$result['success']) {
            return redirect()->to('/guru/jurnal')->with('error', $result['message']);
        }

        $jurnal = $result['data'];

        // Cek apakah jurnal dibuat oleh guru yang login
        // Check via absensi's created_by to support substitute teacher mode
        $absensi = $this->absensiModel->find($jurnal['absensi_id']);
        if ($absensi && $absensi['created_by'] != $userId) {
            return redirect()->to('/guru/jurnal')->with('error', 'Kamu nggak punya akses ke jurnal ini 🔐');
        }

        $data = [
            'title' => 'Edit Jurnal KBM',
            'guru' => $guru,
            'jurnal' => $jurnal
        ];

        return view('guru/jurnal/edit', $data);
    }

    public function show($kelasId)
    {
        // Get guru data from session
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get jurnal for this kelas AND this guru only (security-safe)
        $result = $this->jurnalService->getJurnalByGuruAndKelas($guru['id'], $kelasId);

        if (!$result['success'] || empty($result['data'])) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data jurnal nggak ketemu untuk kelas ini 🤔');
        }

        $jurnalList = $result['data'];

        $data = [
            'title' => 'Daftar Pertemuan - ' . $jurnalList[0]['nama_kelas'],
            'guru' => $guru,
            'jurnalList' => $jurnalList,
            'kelas' => [
                'id' => $kelasId,
                'nama_kelas' => $jurnalList[0]['nama_kelas'],
                'nama_mapel' => $jurnalList[0]['nama_mapel']
            ]
        ];

        return view('guru/jurnal/show', $data);
    }

    public function update($jurnalId)
    {
        helper('security');
        
        log_message('info', '[JURNAL UPDATE] Started - ID: ' . $jurnalId);
        log_message('info', '[JURNAL UPDATE] POST data: ' . json_encode($this->request->getPost()));
        
        // Validasi input - only validate text fields first
        $rules = [
            'kegiatan_pembelajaran' => 'required',
            'catatan_khusus' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            log_message('error', '[JURNAL UPDATE] Validation failed: ' . json_encode($errors));
            session()->setFlashdata('error', 'Validasi gagal nih 😅 ' . implode(', ', $errors));
            return redirect()->back()->withInput();
        }

        log_message('info', '[JURNAL UPDATE] Validation passed');

        // Get existing jurnal
        $jurnalResult = $this->jurnalService->getJurnalById($jurnalId);
        if (!$jurnalResult['success']) {
            log_message('error', '[JURNAL UPDATE] Jurnal not found: ' . $jurnalId);
            session()->setFlashdata('error', $jurnalResult['message']);
            return redirect()->to('/guru/jurnal');
        }

        $jurnal = $jurnalResult['data'];
        log_message('info', '[JURNAL UPDATE] Jurnal found: ' . json_encode($jurnal));

        // Prepare update data
        $data = [
            'kegiatan_pembelajaran' => $this->request->getPost('kegiatan_pembelajaran'),
            'catatan_khusus' => $this->request->getPost('catatan_khusus') ?: '-',
        ];

        log_message('info', '[JURNAL UPDATE] Prepared data: ' . json_encode($data));

        // Handle foto deletion
        $removeFoto = $this->request->getPost('remove_foto');
        if ($removeFoto == '1' && !empty($jurnal['foto_dokumentasi'])) {
            log_message('info', '[JURNAL UPDATE] Removing foto: ' . $jurnal['foto_dokumentasi']);
            // Delete old photo file
            $oldPhotoPath = WRITEPATH . 'uploads/jurnal/' . $jurnal['foto_dokumentasi'];
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
                log_message('info', '[JURNAL UPDATE] Old foto deleted');
            }
            $data['foto_dokumentasi'] = null;
        }

        // Handle foto upload/replace
        $file = $this->request->getFile('foto_dokumentasi');
        
        log_message('info', '[JURNAL UPDATE] File check - isValid: ' . ($file && $file->isValid() ? 'yes' : 'no'));
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            log_message('info', '[JURNAL UPDATE] Processing file upload - Name: ' . $file->getName() . ', Size: ' . $file->getSize() . ', Type: ' . $file->getMimeType());
            
            // Additional validation for file size and type
            if ($file->getSize() > 1048576) {
                $sizeMB = round($file->getSize() / 1048576, 2);
                log_message('error', '[JURNAL UPDATE] File too large: ' . $file->getSize());
                session()->setFlashdata('error', '📦 File kegedean nih (' . $sizeMB . 'MB). Maks 1MB ya. Kompres atau pilih file lain 😅');
                return redirect()->back()->withInput();
            }
            
            // Validate file with security helper
            $allowedTypes = [
                'image/jpeg',
                'image/jpg', 
                'image/png',
                'image/gif'
            ];
            
            $validation = validate_file_upload($file, $allowedTypes, 1048576); // 1MB
            
            if (!$validation['valid']) {
                log_message('error', '[JURNAL UPDATE] File validation failed: ' . $validation['error']);
                session()->setFlashdata('error', $validation['error']);
                return redirect()->back()->withInput();
            }
            
            log_message('info', '[JURNAL UPDATE] File validation passed');
            
            // Delete old photo if exists
            if (!empty($jurnal['foto_dokumentasi'])) {
                $oldPhotoPath = WRITEPATH . 'uploads/jurnal/' . $jurnal['foto_dokumentasi'];
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                    log_message('info', '[JURNAL UPDATE] Old foto replaced: ' . $jurnal['foto_dokumentasi']);
                }
            }
            
            // Generate unique filename
            $fotoName = 'jurnal_' . time() . '_' . uniqid() . '.' . $file->getExtension();
            
            log_message('info', '[JURNAL UPDATE] Generated filename: ' . $fotoName);
            
            // Move file to uploads directory
            try {
                $file->move(WRITEPATH . 'uploads/jurnal', $fotoName);
                
                // Optimize image (compress without losing visible quality)
                helper('image');
                $filePath = WRITEPATH . 'uploads/jurnal/' . $fotoName;
                $originalSize = filesize($filePath);
                
                $optimized = optimize_jurnal_photo($filePath, $filePath);
                
                if ($optimized) {
                    $newSize = filesize($filePath);
                    $savings = round((($originalSize - $newSize) / $originalSize) * 100, 2);
                    log_message('info', "[JURNAL UPDATE] Photo optimized: {$fotoName} - {$savings}% smaller");
                }
                
                $data['foto_dokumentasi'] = $fotoName;
                
                log_message('info', '[JURNAL UPDATE] Foto uploaded successfully: ' . $fotoName);
            } catch (\Exception $e) {
                log_message('error', '[JURNAL UPDATE] Failed to upload foto: ' . $e->getMessage());
                log_message('error', '[JURNAL UPDATE] Stack trace: ' . $e->getTraceAsString());
                
                $userMessage = '📷 Gagal simpan foto nih 😅 ';
                if (ENVIRONMENT === 'development') {
                    $userMessage .= 'Detail: ' . $e->getMessage();
                } else {
                    $userMessage .= 'Coba lagi ya atau pakai foto lain.';
                }
                
                session()->setFlashdata('error', $userMessage);
                return redirect()->back()->withInput();
            }
        } else {
            if ($file) {
                $error = $file->getErrorString() . ' (' . $file->getError() . ')';
                log_message('info', '[JURNAL UPDATE] File not valid or already moved: ' . $error);
            }
        }

        log_message('info', '[JURNAL UPDATE] Final data for update: ' . json_encode($data));

        // Update jurnal using service
        $updateResult = $this->jurnalService->updateJurnal($jurnalId, $data);
        log_message('info', '[JURNAL UPDATE] Update result: ' . ($updateResult['success'] ? 'success' : 'failed'));
        
        if ($updateResult['success']) {
            log_message('info', '[JURNAL UPDATE] Jurnal updated successfully');
            
            // Get kelas_id for redirect from jurnal data
            $kelasId = $jurnal['kelas_id'] ?? null;
            
            session()->setFlashdata('success', '✅ Jurnal KBM udah diperbarui! Perubahan tersimpan 👍');
            
            if ($kelasId) {
                return redirect()->to('/guru/jurnal/show/' . $kelasId);
            }
            return redirect()->to('/guru/jurnal');
        } else {
            log_message('error', '[JURNAL UPDATE] Update failed: ' . $updateResult['message']);
            
            // Rollback: delete uploaded file if database update fails
            if (isset($fotoName) && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
                log_message('info', '[JURNAL UPDATE] Rolled back foto upload');
            }
            
            session()->setFlashdata('error', $updateResult['message']);
            return redirect()->back()->withInput();
        }
    }

    public function print($kelasId = null)
    {
        // Get guru data from session
        // Support both 'user_id' and 'userId' for backward compatibility
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get filters from query params or URL segment
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');

        // Set date range if month and year provided
        $startDate = null;
        $endDate = null;
        if ($bulan && $tahun) {
            $startDate = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
            $endDate = date('Y-m-t', strtotime($startDate));
        }

        // Validate kelas parameter
        if (!$kelasId) {
            return redirect()->to('/guru/jurnal')->with('error', 'Pilih kelas dulu ya buat cetak jurnal 😊');
        }

        // SECURITY FIX: Get jurnal filtered by BOTH guru AND kelas
        // First get all jurnal by this guru
        $result = $this->jurnalService->getJurnalByGuru($guru['id'], $startDate, $endDate);
        
        if (!$result['success']) {
            return redirect()->to('/guru/jurnal')->with('error', $result['message']);
        }

        // Filter by the specific kelas
        $allJurnal = $result['data'];
        $jurnalList = array_filter($allJurnal, function($jurnal) use ($kelasId) {
            return $jurnal['kelas_id'] == $kelasId;
        });

        // Reset array keys
        $jurnalList = array_values($jurnalList);
        
        // Get kelas info
        $kelasModel = new \App\Models\KelasModel();
        $kelasInfo = $kelasModel->find($kelasId);
        $mapelInfo = !empty($jurnalList) ? ['nama_mapel' => $jurnalList[0]['nama_mapel']] : null;

        if (empty($jurnalList)) {
            return redirect()->to('/guru/jurnal')->with('error', 'Nggak ada data jurnal yang bisa dicetak 🤔');
        }

        $data = [
            'title' => 'Print Jurnal KBM',
            'guru' => $guru,
            'jurnalList' => $jurnalList,
            'kelasInfo' => $kelasInfo,
            'mapelInfo' => $mapelInfo,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'request' => $this->request
        ];

        return view('guru/jurnal/print', $data);
    }
}
