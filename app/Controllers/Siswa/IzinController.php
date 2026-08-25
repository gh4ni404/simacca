<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Services\IzinSiswaService;
use App\Models\SiswaModel;

class IzinController extends BaseController
{
    protected $siswaModel;
    protected $izinService;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->izinService = new IzinSiswaService();
    }

    public function index()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        // Get filter status
        $status = $this->request->getGet('status') ?? null;

        // Get izin data using service
        $filters = ['siswa_id' => $siswa['id']];
        if ($status) {
            $filters['status'] = $status;
        }

        $result = $this->izinService->getAllIzin(100, $filters);
        $izinData = $result['success'] ? $result['data']['izin'] : [];

        // Get statistics
        $statsResult = $this->izinService->getIzinStatistics(['siswa_id' => $siswa['id']]);
        $stats = $statsResult['success'] ? $statsResult['data'] : [
            'pending' => 0,
            'disetujui' => 0,
            'ditolak' => 0
        ];

        $countPending = $stats['pending'];
        $countDisetujui = $stats['disetujui'];
        $countDitolak = $stats['ditolak'];

        $data = [
            'title' => 'Pengajuan Izin',
            'siswa' => $siswa,
            'izinData' => $izinData,
            'status' => $status,
            'countPending' => $countPending,
            'countDisetujui' => $countDisetujui,
            'countDitolak' => $countDitolak
        ];

        return view('siswa/izin/index', $data);
    }

    public function create()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $data = [
            'title' => 'Ajukan Izin',
            'siswa' => $siswa
        ];

        return view('siswa/izin/create', $data);
    }

    public function store()
    {
        helper('security');
        
        log_message('info', '[IZIN SISWA] Store started');
        
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            log_message('error', '[IZIN SISWA] Siswa not found for user_id: ' . $userId);
            session()->setFlashdata('error', 'Data siswa nggak ketemu 🔍');
            return redirect()->back();
        }

        log_message('info', '[IZIN SISWA] Siswa found: ' . $siswa['id']);

        // Validation
        $rules = [
            'tanggal' => 'required|valid_date',
            'jenis_izin' => 'required|in_list[Sakit,Izin]',
            'alasan' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            log_message('error', '[IZIN SISWA] Validation failed: ' . json_encode($errors));
            
            $errorList = '<ul class="list-disc ml-4">';
            foreach ($errors as $field => $error) {
                $errorList .= '<li>' . $error . '</li>';
            }
            $errorList .= '</ul>';
            session()->setFlashdata('error', 'Lengkapin dulu datanya ya 😊' . $errorList);
            return redirect()->back()->withInput();
        }

        log_message('info', '[IZIN SISWA] Validation passed');

        // Create upload directory if not exists
        $uploadPath = WRITEPATH . 'uploads/izin';
        
        // Create directory if not exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
            log_message('info', 'Created upload directory: ' . $uploadPath);
        }
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
            log_message('info', '[IZIN SISWA] Created upload directory: ' . $uploadPath);
        }

        // Handle file upload
        $berkas = $this->request->getFile('berkas');
        $berkasName = null;

        if ($berkas && $berkas->isValid() && !$berkas->hasMoved()) {
            log_message('info', '[IZIN SISWA] Processing file upload: ' . $berkas->getName());
            
            // Validate file
            $allowedTypes = [
                'image/jpeg',
                'image/jpg', 
                'image/png',
                'application/pdf'
            ];
            
            $validation = validate_file_upload($berkas, $allowedTypes, 1048576); // 1MB
            
            if (!$validation['valid']) {
                log_message('error', '[IZIN SISWA] File validation failed: ' . $validation['error']);
                session()->setFlashdata('error', '📁 ' . $validation['error']);
                return redirect()->back()->withInput();
            }

            try {
                $berkasName = 'izin_' . time() . '_' . uniqid() . '.' . $berkas->getExtension();
                $berkas->move($uploadPath, $berkasName);
                log_message('info', '[IZIN SISWA] File uploaded: ' . $berkasName);
                
                // Optimize image if it's an image file (skip PDF)
                $mimeType = $berkas->getMimeType();
                if (strpos($mimeType, 'image/') === 0) {
                    helper('image');
                    $filePath = $uploadPath . '/' . $berkasName;
                    $originalSize = filesize($filePath);
                    
                    $optimized = optimize_izin_photo($filePath, $filePath);
                    
                    if ($optimized) {
                        $newSize = filesize($filePath);
                        $savings = round((($originalSize - $newSize) / $originalSize) * 100, 2);
                        log_message('info', "[IZIN SISWA] Image optimized: {$berkasName} - {$savings}% smaller");
                    }
                }
            } catch (\Exception $e) {
                log_message('error', '[IZIN SISWA] File upload failed: ' . $e->getMessage());
                session()->setFlashdata('error', 'Upload file gagal nih 📁😬');
                return redirect()->back()->withInput();
            }
        }

        // Save izin using service
        $data = [
            'siswa_id' => $siswa['id'],
            'tanggal' => $this->request->getPost('tanggal'),
            'jenis_izin' => $this->request->getPost('jenis_izin'),
            'alasan' => $this->request->getPost('alasan'),
            'berkas' => $berkasName
        ];

        log_message('info', '[IZIN SISWA] Inserting data: ' . json_encode($data));

        $result = $this->izinService->createIzin($data);

        if ($result['success']) {
            log_message('info', '[IZIN SISWA] Insert successful');
            session()->setFlashdata('success', 'Izin dikirim! Tunggu persetujuan wali kelas ya 📨✨');
            return redirect()->to('/siswa/izin');
        } else {
            log_message('error', '[IZIN SISWA] Insert failed: ' . $result['message']);
            
            // Delete uploaded file if database insert fails
            if ($berkasName && file_exists($uploadPath . '/' . $berkasName)) {
                unlink($uploadPath . '/' . $berkasName);
                log_message('info', '[IZIN SISWA] Rolled back file upload');
            }
            
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }
}
