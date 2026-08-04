<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SiswaService;
use App\Models\RolloverHistoryModel;
use App\Models\RolloverBackupModel;

class PengaturanController extends BaseController
{
    protected $siswaService;
    protected $rolloverHistoryModel;

    public function __construct()
    {
        $this->siswaService = new SiswaService();
        $this->rolloverHistoryModel = new RolloverHistoryModel();

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

        $data['logoSekolah'] = get_logo_sekolah();

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
}
