<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class IzinController extends BaseController
{
    public function index()
    {
        return view('siswa/izin/index');
    }

    public function create()
    {
        return view('siswa/izin/create');
    }

    public function store()
    {
        return $this->response->setJSON(['status' => 'ok']);
    }
}
