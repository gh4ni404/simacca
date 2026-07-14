<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PklCategoryModel;

class PklCategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new PklCategoryModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Kategori PKL',
            'categories' => $this->categoryModel->orderBy('nama', 'ASC')->findAll(),
        ];

        return view('admin/pkl_category/index', $data);
    }

    public function store()
    {
        $nama = $this->request->getPost('nama');

        if (empty($nama)) {
            session()->setFlashdata('error', 'Nama kategori wajib diisi');
            return redirect()->back();
        }

        $exists = $this->categoryModel->where('nama', $nama)->first();
        if ($exists) {
            session()->setFlashdata('error', 'Kategori sudah ada');
            return redirect()->back();
        }

        $this->categoryModel->insert(['nama' => $nama]);
        session()->setFlashdata('success', 'Kategori berhasil ditambahkan');
        return redirect()->back();
    }

    public function update(int $id)
    {
        $nama = $this->request->getPost('nama');

        if (empty($nama)) {
            session()->setFlashdata('error', 'Nama kategori wajib diisi');
            return redirect()->back();
        }

        $exists = $this->categoryModel->where('nama', $nama)->where('id !=', $id)->first();
        if ($exists) {
            session()->setFlashdata('error', 'Nama kategori sudah digunakan');
            return redirect()->back();
        }

        $this->categoryModel->update($id, ['nama' => $nama]);
        session()->setFlashdata('success', 'Kategori berhasil diupdate');
        return redirect()->back();
    }

    public function delete(int $id)
    {
        $this->categoryModel->delete($id);
        session()->setFlashdata('success', 'Kategori berhasil dihapus');
        return redirect()->back();
    }
}
