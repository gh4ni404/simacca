<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SiswaService;
use App\Models\RolloverHistoryModel;
use App\Models\RolloverBackupModel;
use App\Models\HariLiburModel;

class PengaturanController extends BaseController
{
    protected $siswaService;
    protected $rolloverHistoryModel;
    protected $hariLiburModel;

    public function __construct()
    {
        $this->siswaService         = new SiswaService();
        $this->rolloverHistoryModel = new RolloverHistoryModel();
        $this->hariLiburModel       = new HariLiburModel();

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    public function index()
    {
        $rolloverHistory = $this->rolloverHistoryModel->getAllHistory();
        $latestActive = $this->rolloverHistoryModel->getLatestActive();

        $rolloverBackupModel = new RolloverBackupModel();
        $historyIdsWithBackup = $rolloverBackupModel->getHistoryIdsWithBackup();

        $data = [
            'title' => 'Pengaturan',
            'pageTitle' => 'Pengaturan Sistem',
            'pageDescription' => 'Kelola pengaturan sistem seperti tahun ajaran aktif dan jurnal PKL',
            'user' => $this->getUserData(),
            'activeTahunAjaran' => get_active_tahun_ajaran(),
            'tahunAjaranList' => get_tahun_ajaran_list(),
            'rolloverHistory' => $rolloverHistory,
            'latestActiveRollover' => $latestActive,
            'historyIdsWithBackup' => $historyIdsWithBackup,
            'jurnalPklStartDate' => get_jurnal_pkl_start_date(),
            'jurnalPklEndDate' => get_jurnal_pkl_end_date(),
            'jurnalPklDurationDays' => get_jurnal_pkl_duration_days(),
            'jurnalPklRequiredDays' => get_jurnal_pkl_required_days(),
        ];

        $data['logoSekolah']        = get_logo_sekolah();
        $data['kepalaSekolahNama']  = get_kepala_sekolah_nama();
        $data['kepalaSekolahNip']   = get_kepala_sekolah_nip();
        $data['hariLiburList']      = $this->hariLiburModel->getAllSorted();

        return view('admin/pengaturan/index', $data);
    }

    public function update()
    {
        $tahunAjaran = $this->request->getPost('tahun_ajaran');

        if (!$tahunAjaran) {
            session()->setFlashdata('error', 'Tahun ajaran harus diisi.');
            return redirect()->to('/admin/pengaturan');
        }

        $error = validate_tahun_ajaran($tahunAjaran);
        if ($error) {
            session()->setFlashdata('errors', [$error]);
            return redirect()->to('/admin/pengaturan');
        }

        $result = set_active_tahun_ajaran($tahunAjaran);

        if ($result) {
            session()->setFlashdata('success', 'Tahun ajaran aktif berhasil diperbarui menjadi ' . $tahunAjaran);
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui tahun ajaran.');
        }

        return redirect()->to('/admin/pengaturan');
    }

    public function rollover()
    {
        $activeTahunAjaran = get_active_tahun_ajaran();
        $parts = explode('/', $activeTahunAjaran);
        $nextTahunAjaran = ($parts[0] + 1) . '/' . ($parts[1] + 1);

        $newTahunAjaran = $this->request->getPost('tahun_ajaran') ?? $nextTahunAjaran;

        if ($newTahunAjaran !== $nextTahunAjaran) {
            session()->setFlashdata('error', 'Tahun ajaran rollover harus tahun berikutnya dari tahun aktif (' . $nextTahunAjaran . ')');
            return redirect()->to('/admin/pengaturan');
        }

        $result = $this->siswaService->rolloverTahunAjaran($newTahunAjaran);

        if ($result['success']) {
            $data = $result['data'];
            $message = $data['message'];
            session()->setFlashdata('rollover_result', $data);
            session()->setFlashdata('success', $message);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/pengaturan');
    }

    public function revert()
    {
        $historyId = (int) $this->request->getPost('history_id');

        if (!$historyId) {
            session()->setFlashdata('error', 'ID rollover tidak valid.');
            return redirect()->to('/admin/pengaturan');
        }

        $result = $this->siswaService->revertRollover($historyId);

        if ($result['success']) {
            session()->setFlashdata('success', $result['data']['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/pengaturan');
    }

    public function updateJurnalPklPeriod()
    {
        $clear = $this->request->getPost('clear');
        $startDate = $this->request->getPost('jurnal_pkl_start_date');
        $endDate = $this->request->getPost('jurnal_pkl_end_date');

        if ($clear) {
            $settingModel = new \App\Models\SettingModel();
            $settingModel->setSetting('jurnal_pkl_start_date', '');
            $settingModel->setSetting('jurnal_pkl_end_date', '');
            session()->setFlashdata('success', 'Pengaturan periode jurnal PKL berhasil direset.');
            return redirect()->to('/admin/pengaturan');
        }

        if (!empty($startDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            session()->setFlashdata('error', 'Format tanggal mulai tidak valid.');
            return redirect()->to('/admin/pengaturan');
        }

        if (!empty($endDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            session()->setFlashdata('error', 'Format tanggal akhir tidak valid.');
            return redirect()->to('/admin/pengaturan');
        }

        if (!empty($startDate) && !empty($endDate) && strtotime($endDate) < strtotime($startDate)) {
            session()->setFlashdata('error', 'Tanggal akhir tidak boleh sebelum tanggal mulai.');
            return redirect()->to('/admin/pengaturan');
        }

        set_jurnal_pkl_start_date($startDate ?: '');
        set_jurnal_pkl_end_date($endDate ?: '');

        $requiredDays = (int) $this->request->getPost('jurnal_pkl_required_days');
        if ($requiredDays >= 1 && $requiredDays <= 7) {
            $settingModel = new \App\Models\SettingModel();
            $settingModel->setSetting('jurnal_pkl_required_days', (string) $requiredDays);
        }

        session()->setFlashdata('success', 'Pengaturan periode jurnal PKL berhasil disimpan.');
        return redirect()->to('/admin/pengaturan');
    }

    public function updateJurnalPklStart()
    {
        $clear = $this->request->getPost('clear');
        $startDate = $this->request->getPost('jurnal_pkl_start_date');

        if ($clear || !$startDate) {
            // Clear the setting
            $settingModel = new \App\Models\SettingModel();
            $existing = $settingModel->get('jurnal_pkl_start_date');
            if ($existing) {
                $settingModel->setSetting('jurnal_pkl_start_date', '');
            }
            session()->setFlashdata('success', 'Tanggal mulai minggu ke-1 jurnal PKL berhasil dihapus (menggunakan ISO week).');
            return redirect()->to('/admin/pengaturan');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            session()->setFlashdata('error', 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD.');
            return redirect()->to('/admin/pengaturan');
        }

        $result = set_jurnal_pkl_start_date($startDate);

        if ($result) {
            session()->setFlashdata('success', 'Tanggal mulai minggu ke-1 jurnal PKL berhasil disimpan.');
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan pengaturan.');
        }

        return redirect()->to('/admin/pengaturan');
    }

    public function uploadLogo()
    {
        $rules = [
            'logo_sekolah' => [
                'label' => 'Logo Web',
                'rules' => 'uploaded[logo_sekolah]|max_size[logo_sekolah,2048]|is_image[logo_sekolah]|mime_in[logo_sekolah,image/jpg,image/jpeg,image/png,image/svg+xml,image/webp]',
                'errors' => [
                    'uploaded' => 'Pilih file logo terlebih dahulu.',
                    'max_size' => 'Ukuran logo maksimal 2MB.',
                    'is_image' => 'File harus berupa gambar.',
                    'mime_in' => 'Format logo harus JPG, JPEG, PNG, SVG, atau WebP.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('/admin/pengaturan');
        }

        $file = $this->request->getFile('logo_sekolah');

        if ($file->isValid() && !$file->hasMoved()) {
            // Delete old logo if exists
            delete_logo_sekolah();

            // Generate filename
            $newName = 'logo_' . time() . '.' . $file->getExtension();

            $uploadPath = WRITEPATH . 'uploads/logo/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $newName);
            set_logo_sekolah($newName);

            session()->setFlashdata('success', 'Logo web berhasil diperbarui.');
        } else {
            session()->setFlashdata('error', 'Gagal mengupload logo web.');
        }

        return redirect()->to('/admin/pengaturan');
    }

    public function downloadLogo()
    {
        $logo = get_logo_sekolah();
        if (!$logo) {
            session()->setFlashdata('error', 'Tidak ada logo untuk diunduh.');
            return redirect()->to('/admin/pengaturan');
        }

        $filePath = WRITEPATH . 'uploads/logo/' . $logo;

        if (!file_exists($filePath)) {
            session()->setFlashdata('error', 'File logo tidak ditemukan.');
            return redirect()->to('/admin/pengaturan');
        }

        $mime = mime_content_type($filePath);
        $extension = pathinfo($logo, PATHINFO_EXTENSION);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="logo-sekolah.' . $extension . '"')
            ->setHeader('Content-Length', filesize($filePath))
            ->setBody(file_get_contents($filePath));
    }

    public function updateKepalaSekolah()
    {
        $nama = trim($this->request->getPost('kepala_sekolah_nama') ?? '');
        $nip  = trim($this->request->getPost('kepala_sekolah_nip') ?? '');

        if ($nama === '') {
            session()->setFlashdata('error', 'Nama Kepala Sekolah tidak boleh kosong.');
            return redirect()->to('/admin/pengaturan');
        }

        set_kepala_sekolah_nama($nama);
        set_kepala_sekolah_nip($nip);

        session()->setFlashdata('success', 'Data Kepala Sekolah berhasil diperbarui.');
        return redirect()->to('/admin/pengaturan');
    }

    public function deleteLogo()
    {
        $logo = get_logo_sekolah();
        if ($logo) {
            delete_logo_sekolah();
            session()->setFlashdata('success', 'Logo web berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Tidak ada logo yang bisa dihapus.');
        }

        return redirect()->to('/admin/pengaturan');
    }

    // ─── Hari Libur (Kalender Libur Nasional) ────────────────────────────────

    public function storeHariLibur()
    {
        $tanggal    = trim($this->request->getPost('tanggal') ?? '');
        $keterangan = trim($this->request->getPost('keterangan') ?? '');

        if (!$tanggal || !$keterangan) {
            session()->setFlashdata('error', 'Tanggal dan keterangan wajib diisi.');
            return redirect()->to('/admin/pengaturan#hari-libur');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            session()->setFlashdata('error', 'Format tanggal tidak valid.');
            return redirect()->to('/admin/pengaturan#hari-libur');
        }

        // Upsert: jika tanggal sudah ada, update keterangannya
        $existing = $this->hariLiburModel->where('tanggal', $tanggal)->first();
        if ($existing) {
            $this->hariLiburModel->update($existing['id'], [
                'keterangan' => $keterangan,
                'created_by' => session()->get('userId'),
            ]);
            session()->setFlashdata('success', 'Hari libur ' . date('d/m/Y', strtotime($tanggal)) . ' berhasil diperbarui.');
        } else {
            $this->hariLiburModel->skipValidation(false)->insert([
                'tanggal'    => $tanggal,
                'keterangan' => $keterangan,
                'created_by' => session()->get('userId'),
            ]);
            session()->setFlashdata('success', 'Hari libur ' . date('d/m/Y', strtotime($tanggal)) . ' berhasil ditambahkan.');
        }

        return redirect()->to('/admin/pengaturan#hari-libur');
    }

    public function deleteHariLibur($id)
    {
        $libur = $this->hariLiburModel->find((int) $id);
        if (!$libur) {
            session()->setFlashdata('error', 'Data hari libur tidak ditemukan.');
            return redirect()->to('/admin/pengaturan#hari-libur');
        }

        $this->hariLiburModel->delete((int) $id);
        session()->setFlashdata('success', 'Hari libur ' . date('d/m/Y', strtotime($libur['tanggal'])) . ' berhasil dihapus.');
        return redirect()->to('/admin/pengaturan#hari-libur');
    }

    public function importHariLiburNasional()
    {
        // Daftar hari libur nasional Indonesia 2026
        $hariLiburNasional = [
            ['tanggal' => '2026-01-01', 'keterangan' => 'Tahun Baru Masehi'],
            ['tanggal' => '2026-01-29', 'keterangan' => 'Tahun Baru Imlek'],
            ['tanggal' => '2026-03-04', 'keterangan' => 'Isra Miraj Nabi Muhammad SAW'],
            ['tanggal' => '2026-03-19', 'keterangan' => 'Hari Raya Nyepi'],
            ['tanggal' => '2026-03-20', 'keterangan' => 'Wafat Yesus Kristus (Good Friday)'],
            ['tanggal' => '2026-04-02', 'keterangan' => 'Hari Raya Idul Fitri 1447 H'],
            ['tanggal' => '2026-04-03', 'keterangan' => 'Hari Raya Idul Fitri 1447 H'],
            ['tanggal' => '2026-05-01', 'keterangan' => 'Hari Buruh Internasional'],
            ['tanggal' => '2026-05-14', 'keterangan' => 'Kenaikan Yesus Kristus'],
            ['tanggal' => '2026-05-23', 'keterangan' => 'Hari Raya Waisak'],
            ['tanggal' => '2026-06-01', 'keterangan' => 'Hari Lahir Pancasila'],
            ['tanggal' => '2026-06-09', 'keterangan' => 'Hari Raya Idul Adha 1447 H'],
            ['tanggal' => '2026-06-29', 'keterangan' => 'Tahun Baru Islam 1448 H'],
            ['tanggal' => '2026-08-17', 'keterangan' => 'Hari Kemerdekaan Republik Indonesia'],
            ['tanggal' => '2026-09-07', 'keterangan' => 'Maulid Nabi Muhammad SAW'],
            ['tanggal' => '2026-12-25', 'keterangan' => 'Hari Raya Natal'],
        ];

        $userId = session()->get('userId');
        $inserted = 0;
        $skipped  = 0;

        foreach ($hariLiburNasional as $libur) {
            $existing = $this->hariLiburModel->where('tanggal', $libur['tanggal'])->first();
            if (!$existing) {
                $this->hariLiburModel->insert([
                    'tanggal'    => $libur['tanggal'],
                    'keterangan' => $libur['keterangan'],
                    'created_by' => $userId,
                ]);
                $inserted++;
            } else {
                $skipped++;
            }
        }

        $msg = "Import selesai: {$inserted} hari libur ditambahkan";
        if ($skipped > 0) {
            $msg .= ", {$skipped} sudah ada (dilewati).";
        }
        session()->setFlashdata('success', $msg);
        return redirect()->to('/admin/pengaturan#hari-libur');
    }
}
