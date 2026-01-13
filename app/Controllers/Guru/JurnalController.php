<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JurnalKbmModel;
use App\Models\AbsensiModel;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;

class JurnalController extends BaseController
{
    protected $jurnalModel;
    protected $absensiModel;
    protected $guruModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->jurnalModel = new JurnalKbmModel();
        $this->absensiModel = new AbsensiModel();
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
    }

    public function index()
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get filter dari request
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Get jurnal by guru (grouped by kelas)
        $jurnalRaw = $this->jurnalModel->getByGuru($guru['id'], $startDate, $endDate);
        
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
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get absensi detail
        $absensi = $this->absensiModel->getAbsensiWithDetail($absensiId);

        if (!$absensi) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data absensi tidak ditemukan');
        }

        // Cek apakah sudah ada jurnal untuk absensi ini
        // Jika sudah ada, redirect ke edit jurnal
        $existingJurnal = $this->jurnalModel->getByAbsensi($absensiId);
        if ($existingJurnal) {
            return redirect()->to('/guru/jurnal/edit/' . $existingJurnal['id'])
                ->with('info', 'Jurnal untuk pertemuan ini sudah ada. Anda dapat mengeditnya di sini.');
        }

        // Cek apakah guru yang login adalah pembuat absensi (created_by)
        // Ini mencakup both scenarios:
        // 1. Guru mengajar jadwal sendiri (normal mode)
        // 2. Guru pengganti yang input absensi (substitute mode)
        if ($absensi['created_by'] != $userId) {
            return redirect()->to('/guru/jurnal')->with('error', 'Anda tidak memiliki akses ke absensi ini');
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
            'foto_dokumentasi' => 'permit_empty|uploaded[foto_dokumentasi]|max_size[foto_dokumentasi,5120]|is_image[foto_dokumentasi]'
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
        $existingJurnal = $this->jurnalModel->getByAbsensi($absensiId);
        if ($existingJurnal) {
            session()->setFlashdata('info', 'Jurnal pertemuan ini udah ada nih. Edit aja ya! 📝');
            return redirect()->to('/guru/jurnal/edit/' . $existingJurnal['id']);
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
            
            $validation = validate_file_upload($file, $allowedTypes, 5242880); // 5MB
            
            if (!$validation['valid']) {
                session()->setFlashdata('error', '📁 ' . $validation['error']);
                return redirect()->back()->withInput();
            }
            
            // Generate unique filename
            $fotoName = 'jurnal_' . time() . '_' . uniqid() . '.' . $file->getExtension();
            
            // Move file to uploads directory
            try {
                $file->move(WRITEPATH . 'uploads/jurnal', $fotoName);
            } catch (\Exception $e) {
                log_message('error', 'Failed to upload jurnal foto: ' . $e->getMessage());
                
                $userMessage = '📷 Gagal menyimpan foto dokumentasi. ';
                if (ENVIRONMENT === 'development') {
                    $userMessage .= 'Detail: ' . $e->getMessage();
                } else {
                    $userMessage .= 'Silakan coba lagi atau gunakan foto yang berbeda.';
                }
                
                session()->setFlashdata('error', $userMessage);
                return redirect()->back()->withInput();
            }
        }

        // Insert jurnal
        $data = [
            'absensi_id' => $absensiId,
            'tujuan_pembelajaran' => $this->request->getPost('tujuan_pembelajaran') ?? '-',
            'kegiatan_pembelajaran' => $this->request->getPost('kegiatan_pembelajaran'),
            'media_alat' => $this->request->getPost('media_ajar') ?? '-',
            'penilaian' => $this->request->getPost('penilaian') ?? '-',
            'catatan_khusus' => $this->request->getPost('catatan_khusus') ?? '-',
            'foto_dokumentasi' => $fotoName,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($this->jurnalModel->insert($data)) {
                session()->setFlashdata('success', 'Yeay! Jurnal tersimpan. Good job! 📚✨');
                return redirect()->to('/guru/jurnal');
            } else {
                // Delete uploaded file if database insert fails
                if ($fotoName && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                    unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
                }
                
                $modelErrors = $this->jurnalModel->errors();
                if (!empty($modelErrors)) {
                    $errorList = '<ul class="list-disc ml-4">';
                    foreach ($modelErrors as $field => $error) {
                        $errorList .= '<li>' . $error . '</li>';
                    }
                    $errorList .= '</ul>';
                    session()->setFlashdata('error', '❌ Gagal menyimpan jurnal KBM:' . $errorList);
                } else {
                    session()->setFlashdata('error', '❌ Gagal menyimpan jurnal KBM. Silakan coba lagi atau hubungi administrator.');
                }
                
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            // Delete uploaded file if error occurs
            if ($fotoName && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
            }
            
            $safeMessage = safe_error_message($e, '❌ Gagal menyimpan jurnal KBM');
            session()->setFlashdata('error', $safeMessage);
            return redirect()->back()->withInput();
        }
    }

    public function edit($jurnalId)
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get jurnal with detail including kelas_id
        $jurnal = $this->jurnalModel->select('jurnal_kbm.*,
                                            absensi.tanggal,
                                            absensi.pertemuan_ke,
                                            absensi.materi_pembelajaran,
                                            jadwal_mengajar.jam_mulai,
                                            jadwal_mengajar.jam_selesai,
                                            guru.nama_lengkap as nama_guru,
                                            guru.nip,
                                            mata_pelajaran.nama_mapel,
                                            kelas.id as kelas_id,
                                            kelas.nama_kelas')
                    ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
                    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                    ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                    ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
                    ->where('jurnal_kbm.id', $jurnalId)
                    ->first();

        if (!$jurnal) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data jurnal tidak ditemukan');
        }

        // Cek apakah jurnal dibuat oleh guru yang login
        // Check via absensi's created_by to support substitute teacher mode
        $absensi = $this->absensiModel->find($jurnal['absensi_id']);
        if ($absensi && $absensi['created_by'] != $userId) {
            return redirect()->to('/guru/jurnal')->with('error', 'Anda tidak memiliki akses ke jurnal ini');
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
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get all jurnal for this kelas
        $jurnalList = $this->jurnalModel->getByGuruAndKelas($guru['id'], $kelasId);

        if (empty($jurnalList)) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data jurnal tidak ditemukan untuk kelas ini');
        }

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
            session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $errors));
            return redirect()->back()->withInput();
        }

        log_message('info', '[JURNAL UPDATE] Validation passed');

        // Cek apakah jurnal ada
        $jurnal = $this->jurnalModel->find($jurnalId);
        if (!$jurnal) {
            log_message('error', '[JURNAL UPDATE] Jurnal not found: ' . $jurnalId);
            session()->setFlashdata('error', 'Data jurnal tidak ditemukan');
            return redirect()->to('/guru/jurnal');
        }

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
            if ($file->getSize() > 5242880) {
                $sizeMB = round($file->getSize() / 1048576, 2);
                log_message('error', '[JURNAL UPDATE] File too large: ' . $file->getSize());
                session()->setFlashdata('error', '📦 Ukuran file terlalu besar (' . $sizeMB . 'MB). Maksimal yang diperbolehkan adalah 5MB. Silakan kompres atau pilih file yang lebih kecil.');
                return redirect()->back()->withInput();
            }
            
            // Validate file with security helper
            $allowedTypes = [
                'image/jpeg',
                'image/jpg', 
                'image/png',
                'image/gif'
            ];
            
            $validation = validate_file_upload($file, $allowedTypes, 5242880); // 5MB
            
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
                $data['foto_dokumentasi'] = $fotoName;
                
                log_message('info', '[JURNAL UPDATE] Foto uploaded successfully: ' . $fotoName);
            } catch (\Exception $e) {
                log_message('error', '[JURNAL UPDATE] Failed to upload foto: ' . $e->getMessage());
                log_message('error', '[JURNAL UPDATE] Stack trace: ' . $e->getTraceAsString());
                
                $userMessage = '📷 Gagal menyimpan foto dokumentasi. ';
                if (ENVIRONMENT === 'development') {
                    $userMessage .= 'Detail: ' . $e->getMessage();
                } else {
                    $userMessage .= 'Silakan coba lagi atau gunakan foto yang berbeda.';
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

        // Update jurnal
        try {
            $updateResult = $this->jurnalModel->update($jurnalId, $data);
            log_message('info', '[JURNAL UPDATE] Update result: ' . ($updateResult ? 'success' : 'failed'));
            
            if ($updateResult) {
                log_message('info', '[JURNAL UPDATE] Jurnal updated successfully');
                
                // Get kelas_id for redirect
                $jurnalDetail = $this->jurnalModel->select('jurnal_kbm.*, jadwal_mengajar.kelas_id')
                    ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
                    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                    ->where('jurnal_kbm.id', $jurnalId)
                    ->first();
                    
                $kelasId = $jurnalDetail['kelas_id'] ?? null;
                
                session()->setFlashdata('success', '✅ Jurnal KBM berhasil diperbarui! Perubahan telah disimpan.');
                
                if ($kelasId) {
                    return redirect()->to('/guru/jurnal/show/' . $kelasId);
                }
                return redirect()->to('/guru/jurnal');
            } else {
                // Get model errors
                $modelErrors = $this->jurnalModel->errors();
                log_message('error', '[JURNAL UPDATE] Model update failed: ' . json_encode($modelErrors));
                
                // Rollback: delete uploaded file if database update fails
                if (isset($fotoName) && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                    unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
                    log_message('info', '[JURNAL UPDATE] Rolled back foto upload');
                }
                
                if (!empty($modelErrors)) {
                    $errorList = '<ul class="list-disc ml-4">';
                    foreach ($modelErrors as $field => $error) {
                        $errorList .= '<li>' . $error . '</li>';
                    }
                    $errorList .= '</ul>';
                    session()->setFlashdata('error', '❌ Gagal memperbarui jurnal KBM:' . $errorList);
                } else {
                    session()->setFlashdata('error', '❌ Gagal memperbarui jurnal KBM. Silakan coba lagi atau hubungi administrator.');
                }
                
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', '[JURNAL UPDATE] Exception occurred: ' . $e->getMessage());
            log_message('error', '[JURNAL UPDATE] Stack trace: ' . $e->getTraceAsString());
            
            // Rollback: delete uploaded file if error occurs
            if (isset($fotoName) && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
                log_message('info', '[JURNAL UPDATE] Rolled back foto upload after exception');
            }
            
            $safeMessage = safe_error_message($e, '❌ Gagal memperbarui jurnal KBM');
            session()->setFlashdata('error', $safeMessage);
            return redirect()->back()->withInput();
        }
    }

    public function print($kelasId = null)
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
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

        // Get jurnal list based on kelas
        if ($kelasId) {
            $jurnalList = $this->jurnalModel->select('jurnal_kbm.*,
                                    absensi.tanggal,
                                    absensi.pertemuan_ke,
                                    absensi.materi_pembelajaran,
                                    mata_pelajaran.nama_mapel,
                                    kelas.nama_kelas')
                ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
                ->where('jadwal_mengajar.guru_id', $guru['id'])
                ->where('kelas.id', $kelasId);

            if ($startDate && $endDate) {
                $jurnalList->where('absensi.tanggal >=', $startDate)
                           ->where('absensi.tanggal <=', $endDate);
            }

            $jurnalList = $jurnalList->orderBy('absensi.tanggal', 'ASC')->findAll();
            
            // Get kelas info
            $kelasModel = new \App\Models\KelasModel();
            $kelasInfo = $kelasModel->find($kelasId);
            $mapelInfo = !empty($jurnalList) ? ['nama_mapel' => $jurnalList[0]['nama_mapel']] : null;
        } else {
            // Redirect to jurnal index if no kelas specified
            return redirect()->to('/guru/jurnal')->with('error', 'Pilih kelas untuk mencetak jurnal');
        }

        if (empty($jurnalList)) {
            return redirect()->to('/guru/jurnal')->with('error', 'Tidak ada data jurnal untuk dicetak');
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
