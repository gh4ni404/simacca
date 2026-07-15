<?php

namespace App\Controllers\Instruktur;

use App\Controllers\BaseController;
use App\Models\InstrukturPklModel;
use App\Models\PklTaskTemplateModel;
use App\Models\PklCategoryModel;

class TaskTemplateController extends BaseController
{
    protected $instrukturPklModel;
    protected $templateModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->instrukturPklModel = new InstrukturPklModel();
        $this->templateModel = new PklTaskTemplateModel();
        $this->categoryModel = new PklCategoryModel();
    }

    private function getInstruktur()
    {
        $instrukturId = session()->get('instruktur_id');
        return $this->instrukturPklModel->find($instrukturId);
    }

    public function index()
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
        }

        $templates = $this->templateModel->getByTempatPkl($instruktur['tempat_pkl_id']);

        $data = [
            'title'     => 'Master Task PKL',
            'pageTitle' => 'Master Task',
            'instruktur' => $instruktur,
            'templates' => $templates,
        ];

        return view('instruktur/task_template/index', $data);
    }

    public function store()
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
        }

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

        $langkahKerja = null;
        $langkahKerjaInput = $this->request->getPost('langkah_kerja');
        if (is_array($langkahKerjaInput)) {
            $filtered = array_values(array_filter(array_map('trim', $langkahKerjaInput)));
            if (!empty($filtered)) {
                $langkahKerja = json_encode($filtered, JSON_UNESCAPED_UNICODE);
            }
        }

        $result = $this->templateModel->insert([
            'tempat_pkl_id' => $instruktur['tempat_pkl_id'],
            'judul' => $this->request->getPost('judul'),
            'kategori_id' => $this->request->getPost('kategori_id') ?: null,
            'estimasi' => $this->request->getPost('estimasi') ?: null,
            'langkah_kerja' => $langkahKerja,
        ]);

        if ($result) {
            session()->setFlashdata('success', 'Template task berhasil ditambahkan');
        } else {
            $errors = $this->templateModel->errors();
            session()->setFlashdata('error', 'Gagal menyimpan: ' . implode(', ', $errors));
        }

        return redirect()->to('/instruktur/task-template');
    }

    public function update($id)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
        }

        $template = $this->templateModel->find($id);
        if (!$template || $template['tempat_pkl_id'] != $instruktur['tempat_pkl_id']) {
            session()->setFlashdata('error', 'Template tidak ditemukan');
            return redirect()->to('/instruktur/task-template');
        }

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

        $langkahKerja = null;
        $langkahKerjaInput = $this->request->getPost('langkah_kerja');
        if (is_array($langkahKerjaInput)) {
            $filtered = array_values(array_filter(array_map('trim', $langkahKerjaInput)));
            if (!empty($filtered)) {
                $langkahKerja = json_encode($filtered, JSON_UNESCAPED_UNICODE);
            }
        }

        $success = $this->templateModel->update($id, [
            'judul' => $this->request->getPost('judul'),
            'kategori_id' => $this->request->getPost('kategori_id') ?: null,
            'estimasi' => $this->request->getPost('estimasi') ?: null,
            'langkah_kerja' => $langkahKerja,
        ]);

        if ($success) {
            session()->setFlashdata('success', 'Template task berhasil diupdate');
        } else {
            $errors = $this->templateModel->errors();
            session()->setFlashdata('error', 'Gagal mengupdate: ' . implode(', ', $errors));
        }

        return redirect()->to('/instruktur/task-template');
    }

    public function delete($id)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
        }

        $template = $this->templateModel->find($id);
        if (!$template || $template['tempat_pkl_id'] != $instruktur['tempat_pkl_id']) {
            session()->setFlashdata('error', 'Template tidak ditemukan');
            return redirect()->to('/instruktur/task-template');
        }

        $success = $this->templateModel->delete($id);

        if ($success) {
            session()->setFlashdata('success', 'Template task berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus template');
        }

        return redirect()->to('/instruktur/task-template');
    }
}
