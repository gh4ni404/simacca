<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Services\PklService;
use App\Models\SiswaModel;
use App\Models\PklTaskTemplateModel;
use App\Models\SiswaPklModel;

class PklController extends BaseController
{
    protected $siswaModel;
    protected $pklService;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->pklService = new PklService();
    }

    private function getSiswa()
    {
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);
        if (!$siswa) {
            return null;
        }
        return $siswa;
    }

    public function index()
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $todayResult = $this->pklService->getTodayProgress($siswa['id']);
        $todayProgress = $todayResult['success'] ? $todayResult['data'] : [];

        $timelineResult = $this->pklService->getTimeline($siswa['id']);
        $timeline = $timelineResult['success'] ? $timelineResult['data'] : [];

        $statsResult = $this->pklService->getStatistics($siswa['id']);
        $stats = $statsResult['success'] ? $statsResult['data'] : [
            'total_tasks' => 0, 'total_progress' => 0,
            'draft' => 0, 'submitted' => 0, 'approved' => 0, 'revision' => 0,
        ];

        $tasksResult = $this->pklService->getActiveTasksBySiswa($siswa['id']);
        $tasks = $tasksResult['success'] ? $tasksResult['data'] : [];

        $allTasksResult = $this->pklService->getAllTasksBySiswa($siswa['id']);
        $allTasks = $allTasksResult['success'] ? $allTasksResult['data'] : [];

        $data = [
            'title' => 'Jurnal PKL',
            'siswa' => $siswa,
            'todayProgress' => $todayProgress,
            'timeline' => $timeline,
            'stats' => $stats,
            'tasks' => $tasks,
            'allTasks' => $allTasks,
        ];

        return view('siswa/pkl/index', $data);
    }

    public function create()
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $tasksResult = $this->pklService->getActiveTasksBySiswa($siswa['id']);
        $tasks = $tasksResult['success'] ? $tasksResult['data'] : [];

        $categoriesResult = $this->pklService->getCategories();
        $categories = $categoriesResult['success'] ? $categoriesResult['data'] : [];

        // Fetch task templates from instruktur for this siswa's tempat_pkl
        $taskTemplates = [];
        $siswaPklModel = new SiswaPklModel();
        $siswaPkl = $siswaPklModel->getBySiswaAndTahun($siswa['id'], $siswa['tahun_ajaran']);
        if ($siswaPkl && !empty($siswaPkl['tempat_pkl_id'])) {
            $templateModel = new PklTaskTemplateModel();
            $taskTemplates = $templateModel->getByTempatPkl($siswaPkl['tempat_pkl_id']);

            $kategoriMappingModel = new \App\Models\KategoriPklMappingModel();
            $mappedIds = $kategoriMappingModel->getMappedKategoriIds($siswaPkl['tempat_pkl_id']);
            if (!empty($mappedIds)) {
                $categories = array_filter($categories, fn($cat) => in_array($cat['id'], $mappedIds));
                $categories = array_values($categories);
            }
        }

        $data = [
            'title' => 'Tambah Aktivitas PKL',
            'siswa' => $siswa,
            'tasks' => $tasks,
            'categories' => $categories,
            'taskTemplates' => $taskTemplates,
        ];

        return view('siswa/pkl/create', $data);
    }

    public function getTaskLangkahKerja()
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $taskId = (int) $this->request->getGet('task_id');
        if (!$taskId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Task ID tidak valid']);
        }

        $taskResult = $this->pklService->getTaskById($taskId);
        if (!$taskResult['success'] || $taskResult['data']['siswa_id'] != $siswa['id']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Task tidak ditemukan']);
        }

        $langkahKerja = $taskResult['data']['langkah_kerja'] ?? null;
        $steps = $langkahKerja ? json_decode($langkahKerja, true) : [];

        return $this->response->setJSON(['success' => true, 'data' => $steps]);
    }

    public function getTemplateLangkahKerja()
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $templateId = (int) $this->request->getGet('template_id');
        if (!$templateId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Template ID tidak valid']);
        }

        $templateModel = new PklTaskTemplateModel();
        $template = $templateModel->find($templateId);
        if (!$template) {
            return $this->response->setJSON(['success' => false, 'message' => 'Template tidak ditemukan']);
        }

        $siswaPklModel = new SiswaPklModel();
        $siswaPkl = $siswaPklModel->getBySiswaAndTahun($siswa['id'], $siswa['tahun_ajaran']);
        if (!$siswaPkl || $siswaPkl['tempat_pkl_id'] != $template['tempat_pkl_id']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Template tidak valid']);
        }

        $langkahKerja = $template['langkah_kerja'] ?? null;
        $steps = $langkahKerja ? json_decode($langkahKerja, true) : [];

        return $this->response->setJSON(['success' => true, 'data' => $steps]);
    }

    public function store()
    {
        helper('security');

        $siswa = $this->getSiswa();
        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->back();
        }

        $taskChoice = $this->request->getPost('task_choice');

        if ($taskChoice === 'new') {
            $rules = [
                'judul' => 'required|min_length[3]|max_length[255]',
            ];
            if (!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
                $errorList = '<ul class="list-disc ml-4">';
                foreach ($errors as $error) {
                    $errorList .= '<li>' . $error . '</li>';
                }
                $errorList .= '</ul>';
                session()->setFlashdata('error', 'Lengkapi datanya: ' . $errorList);
                return redirect()->back()->withInput();
            }

            $taskLangkahKerja = null;
            $langkahKerjaInput = $this->request->getPost('langkah_kerja');
            if (is_array($langkahKerjaInput)) {
                $filtered = array_values(array_filter(array_map('trim', $langkahKerjaInput)));
                if (!empty($filtered)) {
                    $taskLangkahKerja = json_encode($filtered, JSON_UNESCAPED_UNICODE);
                }
            }

            $kategoriId = $this->request->getPost('kategori_id') ?: null;
            $judul = $this->request->getPost('judul');
            $estimasi = $this->request->getPost('estimasi') ?: null;

            $pklTaskModel = new \App\Models\PklTaskModel();
            $existingTask = $pklTaskModel->getInactiveOrDeletedBySiswaAndKategori($siswa['id'], $kategoriId);

            if ($existingTask) {
                $updateData = [
                    'judul' => $judul,
                    'kategori_id' => $kategoriId,
                    'estimasi' => $estimasi,
                    'langkah_kerja' => $taskLangkahKerja,
                    'status' => 'active',
                    'deleted_at' => null,
                ];

                $db = \Config\Database::connect();
                $db->table('pkl_tasks')
                    ->where('id', $existingTask['id'])
                    ->update($updateData);

                if ($db->affectedRows() === 0 && empty($db->error())) {
                    session()->setFlashdata('error', 'Gagal mengaktifkan task');
                    return redirect()->back()->withInput();
                }
                $taskId = $existingTask['id'];
            } else {
                $taskResult = $this->pklService->createTask([
                    'siswa_id' => $siswa['id'],
                    'judul' => $judul,
                    'kategori_id' => $kategoriId,
                    'estimasi' => $estimasi,
                    'langkah_kerja' => $taskLangkahKerja,
                    'status' => 'active',
                ]);

                if (!$taskResult['success']) {
                    session()->setFlashdata('error', $taskResult['message']);
                    return redirect()->back()->withInput();
                }

                $taskId = $taskResult['data']['id'];
            }
        } elseif ($taskChoice === 'template') {
            $taskVal = $this->request->getPost('task_id');
            if (!$taskVal || !str_starts_with($taskVal, 'tpl:')) {
                session()->setFlashdata('error', 'Pilih template task terlebih dahulu');
                return redirect()->back()->withInput();
            }
            $templateId = (int) substr($taskVal, 4);

            // Verify template belongs to siswa's tempat_pkl
            $templateModel = new PklTaskTemplateModel();
            $template = $templateModel->find($templateId);
            if (!$template) {
                session()->setFlashdata('error', 'Template tidak valid');
                return redirect()->back()->withInput();
            }

            $siswaPklModel = new SiswaPklModel();
            $siswaPkl = $siswaPklModel->getBySiswaAndTahun($siswa['id'], $siswa['tahun_ajaran']);
            if (!$siswaPkl || $siswaPkl['tempat_pkl_id'] != $template['tempat_pkl_id']) {
                session()->setFlashdata('error', 'Template tidak valid untuk tempat PKL Anda');
                return redirect()->back()->withInput();
            }

            $taskLangkahKerja = null;
            $langkahKerjaInput = $this->request->getPost('langkah_kerja');
            if (is_array($langkahKerjaInput)) {
                $filtered = array_values(array_filter(array_map('trim', $langkahKerjaInput)));
                if (!empty($filtered)) {
                    $taskLangkahKerja = json_encode($filtered, JSON_UNESCAPED_UNICODE);
                }
            }

            $taskResult = $this->pklService->createTask([
                'siswa_id' => $siswa['id'],
                'judul' => $template['judul'],
                'kategori_id' => $template['kategori_id'],
                'estimasi' => $template['estimasi'],
                'langkah_kerja' => $taskLangkahKerja,
                'status' => 'active',
            ]);

            if (!$taskResult['success']) {
                session()->setFlashdata('error', $taskResult['message']);
                return redirect()->back()->withInput();
            }

            $taskId = $taskResult['data']['id'];
        } else {
            $taskId = (int) $this->request->getPost('task_id');
            if (!$taskId) {
                session()->setFlashdata('error', 'Pilih task terlebih dahulu');
                return redirect()->back()->withInput();
            }

            $taskCheck = $this->pklService->getTaskById($taskId);
            if (!$taskCheck['success'] || $taskCheck['data']['siswa_id'] != $siswa['id']) {
                session()->setFlashdata('error', 'Task tidak valid');
                return redirect()->back()->withInput();
            }
        }

        $rules = [
            'deskripsi' => 'required|min_length[3]',
        ];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorList = '<ul class="list-disc ml-4">';
            foreach ($errors as $error) {
                $errorList .= '<li>' . $error . '</li>';
            }
            $errorList .= '</ul>';
            session()->setFlashdata('error', 'Lengkapi datanya: ' . $errorList);
            return redirect()->back()->withInput();
        }

        $uploadPath = WRITEPATH . 'uploads/pkl_progress';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $foto = $this->request->getFile('foto');
        $fotoName = null;

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $validation = validate_file_upload($foto, $allowedTypes, 5242880);

            if (!$validation['valid']) {
                session()->setFlashdata('error', $validation['error']);
                return redirect()->back()->withInput();
            }

            try {
                $fotoName = 'pkl_progress_' . time() . '_' . uniqid() . '.' . $foto->getExtension();
                $foto->move($uploadPath, $fotoName);

                helper('image');
                $filePath = $uploadPath . '/' . $fotoName;
                optimize_jurnal_pkl_photo($filePath, $filePath);
            } catch (\Exception $e) {
                log_message('error', '[PKL PROGRESS] File upload failed: ' . $e->getMessage());
                session()->setFlashdata('error', 'Upload foto gagal');
                return redirect()->back()->withInput();
            }
        }

        $progressData = [
            'task_id' => $taskId,
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'langkah_kerja' => null,
            'foto' => $fotoName,
            'status' => 'draft',
        ];

        $langkahKerja = $this->request->getPost('langkah_kerja');
        if (is_array($langkahKerja)) {
            $filtered = array_values(array_filter(array_map('trim', $langkahKerja)));
            if (!empty($filtered)) {
                $progressData['langkah_kerja'] = json_encode($filtered, JSON_UNESCAPED_UNICODE);
            }
        }

        $result = $this->pklService->createProgress($progressData);

        if ($result['success']) {
            session()->setFlashdata('success', 'Aktivitas berhasil dicatat');
            return redirect()->to('/siswa/jurnal-pkl');
        } else {
            if ($fotoName && file_exists($uploadPath . '/' . $fotoName)) {
                unlink($uploadPath . '/' . $fotoName);
            }
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    public function taskDetail($id)
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $taskResult = $this->pklService->getTaskById($id);
        if (!$taskResult['success'] || $taskResult['data']['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Task tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $progressResult = $this->pklService->getProgressByTask($id);
        $progress = $progressResult['success'] ? $progressResult['data'] : [];

        $data = [
            'title' => 'Detail Task',
            'siswa' => $siswa,
            'task' => $taskResult['data'],
            'progress' => $progress,
        ];

        return view('siswa/pkl/task-detail', $data);
    }

    public function dayDetail($tanggal)
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $result = $this->pklService->getProgressByTanggal($siswa['id'], $tanggal);
        $progress = $result['success'] ? $result['data'] : [];

        $data = [
            'title' => 'Detail Hari',
            'siswa' => $siswa,
            'tanggal' => $tanggal,
            'progress' => $progress,
        ];

        return view('siswa/pkl/day-detail', $data);
    }

    public function submitProgress($id)
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $progressResult = $this->pklService->getProgressById($id);
        if (!$progressResult['success'] || $progressResult['data']['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        if ($progressResult['data']['status'] !== 'draft') {
            session()->setFlashdata('error', 'Hanya progress draft yang bisa dikirim');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $result = $this->pklService->updateProgress($id, ['status' => 'submitted']);

        if ($result['success']) {
            $taskId = $progressResult['data']['task_id'];
            $this->autoCompleteTaskIfDone($taskId);
            session()->setFlashdata('success', 'Progress berhasil dikirim untuk diverifikasi');
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/siswa/jurnal-pkl');
    }

    private function autoCompleteTaskIfDone(int $taskId): void
    {
        $db = \Config\Database::connect();

        $task = $db->table('pkl_tasks')->where('id', $taskId)->where('deleted_at IS NULL', null, false)->get()->getRowArray();
        if (!$task || $task['status'] !== 'active') {
            return;
        }

        $total = $db->table('pkl_progress')->where('task_id', $taskId)->where('deleted_at IS NULL', null, false)->countAllResults();
        $nonDraft = $db->table('pkl_progress')
            ->where('task_id', $taskId)
            ->where('status !=', 'draft')
            ->where('deleted_at IS NULL', null, false)
            ->countAllResults();

        if ($total > 0 && $total === $nonDraft) {
            $db->table('pkl_tasks')->where('id', $taskId)->update(['status' => 'completed']);
        }
    }

    public function selesaikanTask($id)
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $taskResult = $this->pklService->getTaskById($id);
        if (!$taskResult['success'] || $taskResult['data']['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Task tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        if ($taskResult['data']['status'] !== 'active') {
            session()->setFlashdata('error', 'Hanya task aktif yang bisa diselesaikan');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $db->table('pkl_tasks')
            ->where('id', $id)
            ->update(['status' => 'completed']);

        session()->setFlashdata('success', 'Task berhasil diselesaikan. Menunggu verifikasi instruktur.');
        return redirect()->to('/siswa/jurnal-pkl');
    }

    public function deleteProgress($id)
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $progressResult = $this->pklService->getProgressById($id);
        if (!$progressResult['success'] || $progressResult['data']['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        if ($progressResult['data']['status'] === 'approved') {
            session()->setFlashdata('error', 'Progress yang sudah disetujui tidak dapat dihapus');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $result = $this->pklService->deleteProgress($id);

        if ($result['success']) {
            $this->autoCompleteTaskIfDone($progressResult['data']['task_id']);
            session()->setFlashdata('success', 'Progress berhasil dihapus');
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/siswa/jurnal-pkl');
    }

    public function editProgress($id)
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $progressResult = $this->pklService->getProgressById($id);
        if (!$progressResult['success'] || $progressResult['data']['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        if ($progressResult['data']['status'] === 'approved') {
            session()->setFlashdata('error', 'Progress yang sudah disetujui tidak dapat diedit');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $pklTaskModel = new \App\Models\PklTaskModel();
        $tasks = $pklTaskModel->getActiveBySiswa($siswa['id']);
        $categories = model('App\Models\PklCategoryModel')->findAll();

        $data = [
            'title' => 'Edit Aktivitas PKL',
            'siswa' => $siswa,
            'progress' => $progressResult['data'],
            'tasks' => $tasks,
            'categories' => $categories,
        ];

        return view('siswa/pkl/edit', $data);
    }

    public function updateProgressData($id)
    {
        helper('security');

        $siswa = $this->getSiswa();
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $progressResult = $this->pklService->getProgressById($id);
        if (!$progressResult['success'] || $progressResult['data']['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        if ($progressResult['data']['status'] === 'approved') {
            session()->setFlashdata('error', 'Progress yang sudah disetujui tidak dapat diedit');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $rules = [
            'deskripsi' => 'required|min_length[3]',
        ];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorList = '<ul class="list-disc ml-4">';
            foreach ($errors as $error) {
                $errorList .= '<li>' . $error . '</li>';
            }
            $errorList .= '</ul>';
            session()->setFlashdata('error', 'Lengkapi datanya: ' . $errorList);
            return redirect()->back()->withInput();
        }

        $uploadPath = WRITEPATH . 'uploads/pkl_progress';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $foto = $this->request->getFile('foto');
        $fotoName = $progressResult['data']['foto'];

        $hapusFoto = $this->request->getPost('hapus_foto');
        if ($hapusFoto) {
            if ($fotoName && file_exists($uploadPath . '/' . $fotoName)) {
                unlink($uploadPath . '/' . $fotoName);
            }
            $fotoName = null;
        }

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $validation = validate_file_upload($foto, $allowedTypes, 5242880);

            if (!$validation['valid']) {
                session()->setFlashdata('error', $validation['error']);
                return redirect()->back()->withInput();
            }

            try {
                if ($progressResult['data']['foto'] && file_exists($uploadPath . '/' . $progressResult['data']['foto'])) {
                    unlink($uploadPath . '/' . $progressResult['data']['foto']);
                }

                $fotoName = 'pkl_progress_' . time() . '_' . uniqid() . '.' . $foto->getExtension();
                $foto->move($uploadPath, $fotoName);

                helper('image');
                $filePath = $uploadPath . '/' . $fotoName;
                optimize_jurnal_pkl_photo($filePath, $filePath);
            } catch (\Exception $e) {
                log_message('error', '[PKL PROGRESS] File upload failed: ' . $e->getMessage());
                session()->setFlashdata('error', 'Upload foto gagal');
                return redirect()->back()->withInput();
            }
        }

        $langkahKerja = $this->request->getPost('langkah_kerja');
        $langkahKerjaJson = null;
        if (is_array($langkahKerja)) {
            $filtered = array_values(array_filter(array_map('trim', $langkahKerja)));
            if (!empty($filtered)) {
                $langkahKerjaJson = json_encode($filtered, JSON_UNESCAPED_UNICODE);
            }
        }

        $updateData = [
            'deskripsi' => $this->request->getPost('deskripsi'),
            'langkah_kerja' => $langkahKerjaJson,
            'foto' => $fotoName,
        ];

        if ($progressResult['data']['status'] === 'revision') {
            $updateData['status'] = 'draft';
        }

        $result = $this->pklService->updateProgress($id, $updateData);

        if ($result['success']) {
            session()->setFlashdata('success', 'Aktivitas berhasil diperbarui');
            return redirect()->to('/siswa/jurnal-pkl');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    public function printJurnal($tahun, $minggu)
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        helper('setting');
        $startDate = get_jurnal_pkl_start_date();
        $weekBase = $startDate ? get_jurnal_pkl_week_base() : null;

        if ($startDate && $weekBase) {
            $range = get_week_range($startDate, $minggu);
            $dateStart = $range['start'];
            $dateEnd = $range['end'];
        } else {
            $d = new \DateTime();
            $d->setISODate($tahun, $minggu);
            $dateStart = $d->format('Y-m-d');
            $d->modify('+6 days');
            $dateEnd = $d->format('Y-m-d');
        }

        $jurnalResult = $this->pklService->getJurnalByTanggal($siswa['id'], $dateStart, $dateEnd);
        $jurnalData = $jurnalResult['success'] ? $jurnalResult['data'] : [];

        $siswaPklModel = new \App\Models\SiswaPklModel();
        $tempatPklModel = new \App\Models\TempatPklModel();
        $pembimbingPklModel = new \App\Models\PembimbingPklModel();
        $instrukturModel = new \App\Models\InstrukturPklModel();

        $siswaPkl = $siswaPklModel->getBySiswaAndTahun($siswa['id'], $siswa['tahun_ajaran']);
        $tempatPkl = null;
        $pembimbing = null;
        $instruktur = null;
        if ($siswaPkl && !empty($siswaPkl['tempat_pkl_id'])) {
            $tempatPkl = $tempatPklModel->find($siswaPkl['tempat_pkl_id']);
            $pembimbing = $pembimbingPklModel->getByTempatPklAndTahun($siswaPkl['tempat_pkl_id'], $siswaPkl['tahun_ajaran']);
            $instruktur = $instrukturModel->getByTempatPkl($siswaPkl['tempat_pkl_id']);
        }

        $data = [
            'title' => 'Cetak Jurnal Kegiatan PKL',
            'siswa' => $siswa,
            'jurnalData' => $jurnalData,
            'tahun' => $tahun,
            'minggu' => $minggu,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'tempatPkl' => $tempatPkl,
            'siswaPkl' => $siswaPkl,
            'pembimbing' => $pembimbing,
            'instruktur' => $instruktur,
        ];

        return view('siswa/pkl/print-jurnal', $data);
    }

    public function printCatatan($taskIds)
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $ids = array_filter(array_map('intval', explode('-', $taskIds)));
        if (empty($ids)) {
            session()->setFlashdata('error', 'Task tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $tasksData = [];
        foreach ($ids as $taskId) {
            $taskResult = $this->pklService->getTaskById($taskId);
            if (!$taskResult['success'] || $taskResult['data']['siswa_id'] != $siswa['id']) {
                continue;
            }

            $progressResult = $this->pklService->getProgressByTask($taskId);
            $progress = $progressResult['success'] ? $progressResult['data'] : [];

            if (empty($progress)) {
                continue;
            }

            $tasksData[] = [
                'task' => $taskResult['data'],
                'progress' => $progress,
            ];
        }

        if (empty($tasksData)) {
            session()->setFlashdata('error', 'Task tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $siswaPklModel = new \App\Models\SiswaPklModel();
        $tempatPklModel = new \App\Models\TempatPklModel();
        $pembimbingPklModel = new \App\Models\PembimbingPklModel();
        $instrukturModel = new \App\Models\InstrukturPklModel();

        $siswaPkl = $siswaPklModel->getBySiswaAndTahun($siswa['id'], $siswa['tahun_ajaran']);
        $tempatPkl = null;
        $pembimbing = null;
        $instruktur = null;
        if ($siswaPkl && !empty($siswaPkl['tempat_pkl_id'])) {
            $tempatPkl = $tempatPklModel->find($siswaPkl['tempat_pkl_id']);
            $pembimbing = $pembimbingPklModel->getByTempatPklAndTahun($siswaPkl['tempat_pkl_id'], $siswaPkl['tahun_ajaran']);
            $instruktur = $instrukturModel->getByTempatPkl($siswaPkl['tempat_pkl_id']);
        }

        $data = [
            'title' => 'Cetak Catatan Kegiatan PKL',
            'siswa' => $siswa,
            'tasksData' => $tasksData,
            'tempatPkl' => $tempatPkl,
            'siswaPkl' => $siswaPkl,
            'pembimbing' => $pembimbing,
            'instruktur' => $instruktur,
        ];

        return view('siswa/pkl/print-catatan', $data);
    }
}
