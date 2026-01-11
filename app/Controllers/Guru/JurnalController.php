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

        // Get jurnal by guru
        $jurnal = $this->jurnalModel->getByGuru($guru['id'], $startDate, $endDate);

        $data = [
            'title' => 'Jurnal KBM',
            'guru' => $guru,
            'jurnal' => $jurnal,
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
        if ($this->jurnalModel->isJurnalExist($absensiId)) {
            return redirect()->to('/guru/jurnal')->with('error', 'Jurnal untuk absensi ini sudah dibuat');
        }

        // Cek apakah absensi milik guru yang login
        if ($absensi['nama_guru'] !== $guru['nama_lengkap']) {
            return redirect()->to('/guru/jurnal')->with('error', 'Anda tidak memiliki akses ke absensi ini');
        }

        $data = [
            'title' => 'Tambah Jurnal KBM',
            'guru' => $guru,
            'absensi' => $absensi
        ];

        return view('guru/jurnal/create_simple', $data);
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
            session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        // Cek apakah sudah ada jurnal untuk absensi ini
        $absensiId = $this->request->getPost('absensi_id');
        if ($this->jurnalModel->isJurnalExist($absensiId)) {
            session()->setFlashdata('error', 'Jurnal untuk absensi ini sudah dibuat');
            return redirect()->to('/guru/jurnal');
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
                session()->setFlashdata('error', $validation['error']);
                return redirect()->back()->withInput();
            }
            
            // Generate unique filename
            $fotoName = 'jurnal_' . time() . '_' . uniqid() . '.' . $file->getExtension();
            
            // Move file to uploads directory
            try {
                $file->move(WRITEPATH . 'uploads/jurnal', $fotoName);
            } catch (\Exception $e) {
                log_message('error', 'Failed to upload jurnal foto: ' . $e->getMessage());
                session()->setFlashdata('error', 'Gagal mengupload foto dokumentasi');
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
                session()->setFlashdata('success', 'Jurnal KBM berhasil disimpan');
                return redirect()->to('/guru/jurnal');
            } else {
                // Delete uploaded file if database insert fails
                if ($fotoName && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                    unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
                }
                
                session()->setFlashdata('error', 'Gagal menyimpan jurnal KBM');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            // Delete uploaded file if error occurs
            if ($fotoName && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
            }
            
            $safeMessage = safe_error_message($e, 'Gagal menyimpan jurnal KBM');
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

        // Get jurnal with detail
        $jurnal = $this->jurnalModel->getJurnalWithDetail($jurnalId);

        if (!$jurnal) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data jurnal tidak ditemukan');
        }

        // Cek apakah jurnal milik guru yang login
        if ($jurnal['nama_guru'] !== $guru['nama_lengkap']) {
            return redirect()->to('/guru/jurnal')->with('error', 'Anda tidak memiliki akses ke jurnal ini');
        }

        $data = [
            'title' => 'Edit Jurnal KBM',
            'guru' => $guru,
            'jurnal' => $jurnal
        ];

        return view('guru/jurnal/edit_simple', $data);
    }

    public function show($jurnalId)
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get jurnal with detail
        $jurnal = $this->jurnalModel->getJurnalWithDetail($jurnalId);

        if (!$jurnal) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data jurnal tidak ditemukan');
        }

        // Cek apakah jurnal milik guru yang login
        if ($jurnal['nama_guru'] !== $guru['nama_lengkap']) {
            return redirect()->to('/guru/jurnal')->with('error', 'Anda tidak memiliki akses ke jurnal ini');
        }

        $data = [
            'title' => 'Preview Jurnal KBM',
            'guru' => $guru,
            'jurnal' => $jurnal
        ];

        return view('guru/jurnal/show_simple', $data);
    }

    public function update($jurnalId)
    {
        helper('security');
        
        // Validasi input - only validate text fields first
        $rules = [
            'kegiatan_pembelajaran' => 'required',
            'catatan_khusus' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        // Cek apakah jurnal ada
        $jurnal = $this->jurnalModel->find($jurnalId);
        if (!$jurnal) {
            session()->setFlashdata('error', 'Data jurnal tidak ditemukan');
            return redirect()->to('/guru/jurnal');
        }

        // Prepare update data
        $data = [
            'kegiatan_pembelajaran' => $this->request->getPost('kegiatan_pembelajaran'),
            'catatan_khusus' => $this->request->getPost('catatan_khusus') ?: '-',
        ];

        // Handle foto deletion
        $removeFoto = $this->request->getPost('remove_foto');
        if ($removeFoto == '1' && !empty($jurnal['foto_dokumentasi'])) {
            // Delete old photo file
            $oldPhotoPath = WRITEPATH . 'uploads/jurnal/' . $jurnal['foto_dokumentasi'];
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
            $data['foto_dokumentasi'] = null;
        }

        // Handle foto upload/replace
        $file = $this->request->getFile('foto_dokumentasi');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Additional validation for file size and type
            if ($file->getSize() > 5242880) {
                session()->setFlashdata('error', 'Ukuran file terlalu besar. Maksimal 5MB');
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
                session()->setFlashdata('error', $validation['error']);
                return redirect()->back()->withInput();
            }
            
            // Delete old photo if exists
            if (!empty($jurnal['foto_dokumentasi'])) {
                $oldPhotoPath = WRITEPATH . 'uploads/jurnal/' . $jurnal['foto_dokumentasi'];
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }
            
            // Generate unique filename
            $fotoName = 'jurnal_' . time() . '_' . uniqid() . '.' . $file->getExtension();
            
            // Move file to uploads directory
            try {
                $file->move(WRITEPATH . 'uploads/jurnal', $fotoName);
                $data['foto_dokumentasi'] = $fotoName;
                
                log_message('info', 'Jurnal foto uploaded successfully: ' . $fotoName);
            } catch (\Exception $e) {
                log_message('error', 'Failed to upload jurnal foto: ' . $e->getMessage());
                session()->setFlashdata('error', 'Gagal mengupload foto dokumentasi: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }

        // Update jurnal
        try {
            if ($this->jurnalModel->update($jurnalId, $data)) {
                session()->setFlashdata('success', 'Jurnal KBM berhasil diperbarui');
                return redirect()->to('/guru/jurnal');
            } else {
                // Rollback: delete uploaded file if database update fails
                if (isset($fotoName) && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                    unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
                }
                
                session()->setFlashdata('error', 'Gagal memperbarui jurnal KBM');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            // Rollback: delete uploaded file if error occurs
            if (isset($fotoName) && file_exists(WRITEPATH . 'uploads/jurnal/' . $fotoName)) {
                unlink(WRITEPATH . 'uploads/jurnal/' . $fotoName);
            }
            
            $safeMessage = safe_error_message($e, 'Gagal memperbarui jurnal KBM');
            session()->setFlashdata('error', $safeMessage);
            return redirect()->back()->withInput();
        }
    }

    public function print($jurnalId)
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get jurnal with detail
        $jurnal = $this->jurnalModel->getJurnalWithDetail($jurnalId);

        if (!$jurnal) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data jurnal tidak ditemukan');
        }

        // Cek apakah jurnal milik guru yang login
        if ($jurnal['nama_guru'] !== $guru['nama_lengkap']) {
            return redirect()->to('/guru/jurnal')->with('error', 'Anda tidak memiliki akses ke jurnal ini');
        }

        $data = [
            'title' => 'Print Jurnal KBM',
            'guru' => $guru,
            'jurnal' => $jurnal,
            'request' => $this->request
        ];

        return view('guru/jurnal/print', $data);
    }
}
