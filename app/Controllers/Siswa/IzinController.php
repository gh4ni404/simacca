<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\IzinSiswaModel;

class IzinController extends BaseController
{
    protected $siswaModel;
    protected $izinSiswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->izinSiswaModel = new IzinSiswaModel();
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

        // Get izin data
        $builder = $this->izinSiswaModel
            ->select('izin_siswa.*, users.username as approved_by_username')
            ->join('users', 'users.id = izin_siswa.disetujui_oleh', 'left')
            ->where('izin_siswa.siswa_id', $siswa['id'])
            ->orderBy('izin_siswa.tanggal', 'DESC');

        if ($status) {
            $builder->where('izin_siswa.status', $status);
        }

        $izinData = $builder->findAll();

        // Count by status
        $countPending = $this->izinSiswaModel->where('siswa_id', $siswa['id'])->where('status', 'pending')->countAllResults();
        $countDisetujui = $this->izinSiswaModel->where('siswa_id', $siswa['id'])->where('status', 'disetujui')->countAllResults();
        $countDitolak = $this->izinSiswaModel->where('siswa_id', $siswa['id'])->where('status', 'ditolak')->countAllResults();

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
            session()->setFlashdata('error', '❌ Data siswa tidak ditemukan');
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
            session()->setFlashdata('error', '❌ Mohon lengkapi data berikut:' . $errorList);
            return redirect()->back()->withInput();
        }

        log_message('info', '[IZIN SISWA] Validation passed');

        // Check if already submitted for same date
        if ($this->izinSiswaModel->isIzinExist($siswa['id'], $this->request->getPost('tanggal'))) {
            log_message('warning', '[IZIN SISWA] Duplicate izin for date: ' . $this->request->getPost('tanggal'));
            session()->setFlashdata('error', '⚠️ Anda sudah mengajukan izin untuk tanggal tersebut');
            return redirect()->back()->withInput();
        }

        // Create upload directory if not exists
        $uploadPath = WRITEPATH . 'uploads/izin';
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
            
            $validation = validate_file_upload($berkas, $allowedTypes, 2097152); // 2MB
            
            if (!$validation['valid']) {
                log_message('error', '[IZIN SISWA] File validation failed: ' . $validation['error']);
                session()->setFlashdata('error', '📁 ' . $validation['error']);
                return redirect()->back()->withInput();
            }

            try {
                $berkasName = 'izin_' . time() . '_' . uniqid() . '.' . $berkas->getExtension();
                $berkas->move($uploadPath, $berkasName);
                log_message('info', '[IZIN SISWA] File uploaded: ' . $berkasName);
            } catch (\Exception $e) {
                log_message('error', '[IZIN SISWA] File upload failed: ' . $e->getMessage());
                session()->setFlashdata('error', '📁 Gagal mengupload berkas: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }

        // Save izin
        $data = [
            'siswa_id' => $siswa['id'],
            'tanggal' => $this->request->getPost('tanggal'),
            'jenis_izin' => $this->request->getPost('jenis_izin'),
            'alasan' => $this->request->getPost('alasan'),
            'berkas' => $berkasName,
            'status' => 'pending'
        ];

        log_message('info', '[IZIN SISWA] Inserting data: ' . json_encode($data));

        try {
            if ($this->izinSiswaModel->insert($data)) {
                log_message('info', '[IZIN SISWA] Insert successful');
                session()->setFlashdata('success', '✅ Izin berhasil diajukan! Menunggu persetujuan wali kelas.');
                return redirect()->to('/siswa/izin');
            } else {
                $modelErrors = $this->izinSiswaModel->errors();
                log_message('error', '[IZIN SISWA] Model insert failed: ' . json_encode($modelErrors));
                
                // Delete uploaded file if database insert fails
                if ($berkasName && file_exists($uploadPath . '/' . $berkasName)) {
                    unlink($uploadPath . '/' . $berkasName);
                    log_message('info', '[IZIN SISWA] Rolled back file upload');
                }
                
                if (!empty($modelErrors)) {
                    $errorList = '<ul class="list-disc ml-4">';
                    foreach ($modelErrors as $field => $error) {
                        $errorList .= '<li>' . $error . '</li>';
                    }
                    $errorList .= '</ul>';
                    session()->setFlashdata('error', '❌ Gagal mengajukan izin:' . $errorList);
                } else {
                    session()->setFlashdata('error', '❌ Gagal mengajukan izin. Silakan coba lagi.');
                }
                
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', '[IZIN SISWA] Exception: ' . $e->getMessage());
            log_message('error', '[IZIN SISWA] Stack trace: ' . $e->getTraceAsString());
            
            // Delete uploaded file if error occurs
            if ($berkasName && file_exists($uploadPath . '/' . $berkasName)) {
                unlink($uploadPath . '/' . $berkasName);
                log_message('info', '[IZIN SISWA] Rolled back file upload after exception');
            }
            
            $safeMessage = safe_error_message($e, '❌ Gagal mengajukan izin');
            session()->setFlashdata('error', $safeMessage);
            return redirect()->back()->withInput();
        }
    }
}
