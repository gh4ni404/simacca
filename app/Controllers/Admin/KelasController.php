<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;

class KelasController extends BaseController
{
    protected $kelasModel;
    protected $guruModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();

        // Cek role admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Kelas',
            'pageTitle' => 'Data Kelas',
            'pageDescription' => 'Kelola data kelas dan wali kelas',
            'user' => $this->getUserData(),
            'kelas' => $this->kelasModel->getKelasWithJumlahSiswa(),
            'kelasTanpaWali' => $this->kelasModel->getKelasWithoutWali(),
            'guruTersedia' => $this->guruModel->getGuruNonWali(),
            'jurusanList' => $this->getJurusanList(),
            'tingkatList' => $this->getTingkatList()
        ];

        return view('admin/kelas/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Kelas Baru',
            'pageTitle' => 'Tambah Data Kelas',
            'pageDescription' => 'Form untuk menambahkan kelas baru',
            'user' => $this->getUserData(),
            'guruList' => $this->guruModel->getGuruNonWali(),
            'validation' => \Config\Services::validation(),
            'jurusanList' => $this->getJurusanList(),
            'tingkatList' => $this->getTingkatList()
        ];

        return view('admin/kelas/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'nama_kelas' => 'required|is_unique[kelas.nama_kelas]',
            'tingkat' => 'required|in_list[10,11,12]',
            'jurusan' => 'required',
            'wali_kelas_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'nama_kelas' => $this->request->getPost('nama_kelas'),
                'tingkat' => $this->request->getPost('tingkat'),
                'jurusan' => $this->request->getPost('jurusan'),
                'wali_kelas_id' => $this->request->getPost('wali_kelas_id') ?: null
            ];

            $this->kelasModel->insert($data);

            // Jika wali kelas dipilih, update status guru
            if ($this->request->getPost('wali_kelas_id')) {
                $guruId = $this->request->getPost('wali_kelas_id');
                $kelasId = $this->kelasModel->getInsertID();
                
                // Update guru menjadi wali kelas
                $this->guruModel->update($guruId, [
                    'is_wali_kelas' => 1,
                    'kelas_id' => $kelasId
                ]);
            }

            session()->setFlashdata('success', 'Yeay! Kelas baru sudah dibuat.');
            return redirect()->to('/admin/kelas');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $kelas = $this->kelasModel->find($id);
        
        if (!$kelas) {
            session()->setFlashdata('error', 'Wah, kelas ini nggak ketemu ??');
            return redirect()->to('/admin/kelas');
        }

        // Get wali kelas data if exists
        $waliKelas = null;
        if ($kelas['wali_kelas_id']) {
            $waliKelas = $this->guruModel->find($kelas['wali_kelas_id']);
        }

        $data = [
            'title' => 'Edit Data Kelas',
            'pageTitle' => 'Edit Data Kelas',
            'pageDescription' => 'Form untuk mengubah data kelas',
            'user' => $this->getUserData(),
            'kelas' => $kelas,
            'waliKelas' => $waliKelas,
            'guruList' => $this->getAvailableGuruForWali($kelas['wali_kelas_id']),
            'validation' => \Config\Services::validation(),
            'jurusanList' => $this->getJurusanList(),
            'tingkatList' => $this->getTingkatList(),
            'siswaCount' => $this->siswaModel->where('kelas_id', $id)->countAllResults()
        ];

        return view('admin/kelas/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $kelas = $this->kelasModel->find($id);
        
        if (!$kelas) {
            session()->setFlashdata('error', 'Wah, kelas ini nggak ketemu ??');
            return redirect()->to('/admin/kelas');
        }

        // Validation rules
        $rules = [
            'nama_kelas' => "required|is_unique[kelas.nama_kelas,id,{$id}]",
            'tingkat' => 'required|in_list[10,11,12]',
            'jurusan' => 'required',
            'wali_kelas_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $oldWaliKelasId = $kelas['wali_kelas_id'];
            $newWaliKelasId = $this->request->getPost('wali_kelas_id') ?: null;

            // 1. Update kelas data
            $data = [
                'nama_kelas' => $this->request->getPost('nama_kelas'),
                'tingkat' => $this->request->getPost('tingkat'),
                'jurusan' => $this->request->getPost('jurusan'),
                'wali_kelas_id' => $newWaliKelasId
            ];

            $this->kelasModel->update($id, $data);

            // 2. Handle wali kelas changes
            if ($oldWaliKelasId != $newWaliKelasId) {
                // Remove old wali kelas
                if ($oldWaliKelasId) {
                    $this->guruModel->update($oldWaliKelasId, [
                        'is_wali_kelas' => 0,
                        'kelas_id' => null
                    ]);
                }

                // Assign new wali kelas
                if ($newWaliKelasId) {
                    $this->guruModel->update($newWaliKelasId, [
                        'is_wali_kelas' => 1,
                        'kelas_id' => $id
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal mengupdate data');
            }

            session()->setFlashdata('success', 'Oke! Data kelas sudah diperbarui ??');
            return redirect()->to('/admin/kelas');

        } catch (\Exception $e) {
            $db->transRollback();
            session()->setFlashdata('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $kelas = $this->kelasModel->find($id);
        
        if (!$kelas) {
            session()->setFlashdata('error', 'Wah, kelas ini nggak ketemu ??');
            return redirect()->to('/admin/kelas');
        }

        // Check if kelas has siswa
        $siswaCount = $this->siswaModel->where('kelas_id', $id)->countAllResults();
        if ($siswaCount > 0) {
            session()->setFlashdata('error', "Kelas masih ada {$siswaCount} siswa nih. Pindahkan dulu ya ??");
            return redirect()->to('/admin/kelas');
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Remove wali kelas assignment if exists
            if ($kelas['wali_kelas_id']) {
                $this->guruModel->update($kelas['wali_kelas_id'], [
                    'is_wali_kelas' => 0,
                    'kelas_id' => null
                ]);
            }

            // Delete kelas
            $this->kelasModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menghapus data');
            }

            session()->setFlashdata('success', 'Kelas berhasil dihapus ?');
            return redirect()->to('/admin/kelas');

        } catch (\Exception $e) {
            $db->transRollback();
            session()->setFlashdata('error', $e->getMessage());
            return redirect()->to('/admin/kelas');
        }
    }

    /**
     * Show detail of specified resource.
     */
    public function show($id)
    {
        $kelas = $this->kelasModel->getKelasWithJumlahSiswa($id);
        
        if (!$kelas) {
            session()->setFlashdata('error', 'Wah, kelas ini nggak ketemu ??');
            return redirect()->to('/admin/kelas');
        }

        // Get wali kelas data
        $waliKelas = null;
        if ($kelas['wali_kelas_id']) {
            $waliKelas = $this->guruModel->find($kelas['wali_kelas_id']);
        }

        // Get siswa list in this kelas
        $siswa = $this->siswaModel->getByKelas($id);

        // Get absensi statistics for this kelas
        $absensiModel = new \App\Models\AbsensiModel();
        $absensiStats = $absensiModel->getByKelas($id, date('Y-m-01'), date('Y-m-t'));

        $data = [
            'title' => 'Detail Kelas',
            'pageTitle' => 'Detail Kelas ' . $kelas['nama_kelas'],
            'pageDescription' => 'Informasi lengkap data kelas dan siswa',
            'user' => $this->getUserData(),
            'kelas' => $kelas,
            'waliKelas' => $waliKelas,
            'siswa' => $siswa,
            'absensiStats' => $absensiStats,
            'totalSiswa' => count($siswa)
        ];

        return view('admin/kelas/show', $data);
    }

    /**
     * Assign wali kelas to kelas
     */
    public function assignWaliKelas($kelasId)
    {
        $kelas = $this->kelasModel->find($kelasId);
        
        if (!$kelas) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kelas tidak ditemukan']);
        }

        $guruId = $this->request->getPost('guru_id');
        
        if (!$guruId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Guru harus dipilih']);
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Remove old wali kelas if exists
            if ($kelas['wali_kelas_id']) {
                $this->guruModel->update($kelas['wali_kelas_id'], [
                    'is_wali_kelas' => 0,
                    'kelas_id' => null
                ]);
            }

            // Assign new wali kelas
            $this->guruModel->update($guruId, [
                'is_wali_kelas' => 1,
                'kelas_id' => $kelasId
            ]);

            // Update kelas
            $this->kelasModel->update($kelasId, ['wali_kelas_id' => $guruId]);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menugaskan wali kelas');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Wali kelas berhasil ditugaskan'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove wali kelas assignment
     */
    public function removeWaliKelas($kelasId)
    {
        $kelas = $this->kelasModel->find($kelasId);
        
        if (!$kelas) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kelas tidak ditemukan']);
        }

        if (!$kelas['wali_kelas_id']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kelas ini tidak memiliki wali kelas']);
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Remove wali kelas status from guru
            $this->guruModel->update($kelas['wali_kelas_id'], [
                'is_wali_kelas' => 0,
                'kelas_id' => null
            ]);

            // Remove wali kelas from kelas
            $this->kelasModel->update($kelasId, ['wali_kelas_id' => null]);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menghapus wali kelas');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Wali kelas berhasil dihapus ✓'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Move siswa to another kelas
     */
    public function moveSiswa($siswaId)
    {
        $siswa = $this->siswaModel->find($siswaId);
        
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Siswa tidak ditemukan']);
        }

        $newKelasId = $this->request->getPost('kelas_id');
        
        if (!$newKelasId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kelas tujuan harus dipilih']);
        }

        try {
            $this->siswaModel->update($siswaId, ['kelas_id' => $newKelasId]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Siswa berhasil dipindahkan'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get list of available jurusan
     */
    private function getJurusanList()
    {
        return [
            'Agribisnis Tanaman' => 'Agribisnis Tanaman (AT)',
            'Manajemen Perkantoran dan Layanan Bisnis' => 'Manajemen Perkantoran dan Layanan Bisnis (MPLB)',
            'Desain Komunikasi Visual' => 'Desain Komunikasi Visual (DKV)',
            // 'Akuntansi' => 'Akuntansi',
            // 'Administrasi Perkantoran' => 'Administrasi Perkantoran',
            // 'Pemasaran' => 'Pemasaran',
            // 'Tata Boga' => 'Tata Boga',
            // 'Tata Busana' => 'Tata Busana'
        ];
    }

    /**
     * Get list of tingkat
     */
    private function getTingkatList()
    {
        return [
            '10' => 'Kelas 10',
            '11' => 'Kelas 11',
            '12' => 'Kelas 12'
        ];
    }

    /**
     * Get available guru for wali kelas
     */
    private function getAvailableGuruForWali($currentWaliId = null)
    {
        // Get all guru who are not wali kelas, plus current wali (if exists)
        $guruNonWali = $this->guruModel->getGuruNonWali();
        
        $availableGuru = [];
        
        // Add current wali if exists
        if ($currentWaliId) {
            $currentWali = $this->guruModel->find($currentWaliId);
            if ($currentWali) {
                $availableGuru[$currentWaliId] = $currentWali['nama_lengkap'] . ' (Wali saat ini)';
            }
        }
        
        // Add other available guru
        foreach ($guruNonWali as $guru) {
            if ($guru['id'] != $currentWaliId) {
                $availableGuru[$guru['id']] = $guru['nama_lengkap'] . ' - ' . ($guru['nama_mapel'] ?? '-');
            }
        }
        
        return $availableGuru;
    }

    /**
     * Export data kelas to Excel
     */
    public function export()
    {
        $kelas = $this->kelasModel->getAllKelas();

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NAMA KELAS');
        $sheet->setCellValue('C1', 'TINGKAT');
        $sheet->setCellValue('D1', 'JURUSAN');
        $sheet->setCellValue('E1', 'WALI KELAS');
        $sheet->setCellValue('F1', 'JUMLAH SISWA');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0']
            ]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        $no = 1;
        foreach ($kelas as $k) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $k['nama_kelas']);
            $sheet->setCellValue('C' . $row, $k['tingkat']);
            $sheet->setCellValue('D' . $row, $k['jurusan']);
            $sheet->setCellValue('E' . $row, $k['nama_wali_kelas'] ?? '-');
            
            // Get jumlah siswa
            $jumlahSiswa = $this->siswaModel->where('kelas_id', $k['id'])->countAllResults();
            $sheet->setCellValue('F' . $row, $jumlahSiswa);

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'data-kelas-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    /**
     * Get kelas statistics
     */
    public function statistics()
    {
        $data = [
            'title' => 'Statistik Kelas',
            'pageTitle' => 'Statistik Kelas',
            'pageDescription' => 'Analisis data kelas dan distribusi siswa',
            'user' => $this->getUserData(),
            'kelasStats' => $this->getKelasStatistics(),
            'jurusanStats' => $this->getJurusanStatistics(),
            'tingkatStats' => $this->getTingkatStatistics()
        ];

        return view('admin/kelas/statistics', $data);
    }

    /**
     * Get detailed kelas statistics
     */
    private function getKelasStatistics()
    {
        $kelas = $this->kelasModel->getKelasWithJumlahSiswa();
        $stats = [];

        foreach ($kelas as $k) {
            $stats[] = [
                'nama_kelas' => $k['nama_kelas'],
                'jumlah_siswa' => $k['jumlah_siswa'] ?? 0,
                'wali_kelas' => $k['nama_wali_kelas'] ?? 'Belum ada',
                'tingkat' => $k['tingkat'],
                'jurusan' => $k['jurusan'],
                'kapasitas_ideal' => 36, // Default ideal capacity
                'persentase' => round(($k['jumlah_siswa'] ?? 0) / 36 * 100, 2)
            ];
        }

        return $stats;
    }

    /**
     * Get jurusan statistics
     */
    private function getJurusanStatistics()
    {
        $kelas = $this->kelasModel->findAll();
        $jurusanStats = [];

        foreach ($kelas as $k) {
            $jurusan = $k['jurusan'];
            if (!isset($jurusanStats[$jurusan])) {
                $jurusanStats[$jurusan] = [
                    'total_kelas' => 0,
                    'total_siswa' => 0
                ];
            }
            $jurusanStats[$jurusan]['total_kelas']++;
            
            // Get jumlah siswa per kelas
            $jumlahSiswa = $this->siswaModel->where('kelas_id', $k['id'])->countAllResults();
            $jurusanStats[$jurusan]['total_siswa'] += $jumlahSiswa;
        }

        return $jurusanStats;
    }

    /**
     * Get tingkat statistics
     */
    private function getTingkatStatistics()
    {
        $kelas = $this->kelasModel->findAll();
        $tingkatStats = [];

        foreach ($kelas as $k) {
            $tingkat = $k['tingkat'];
            if (!isset($tingkatStats[$tingkat])) {
                $tingkatStats[$tingkat] = [
                    'total_kelas' => 0,
                    'total_siswa' => 0
                ];
            }
            $tingkatStats[$tingkat]['total_kelas']++;
            
            // Get jumlah siswa per kelas
            $jumlahSiswa = $this->siswaModel->where('kelas_id', $k['id'])->countAllResults();
            $tingkatStats[$tingkat]['total_siswa'] += $jumlahSiswa;
        }

        // Sort by tingkat
        ksort($tingkatStats);

        return $tingkatStats;
    }
}