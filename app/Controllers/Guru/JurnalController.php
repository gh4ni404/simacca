<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;

class JurnalController extends BaseController
{
    public function index()
    {
        return view('guru/jurnal/index');
    }

    public function create($id)
    {
        return view('guru/jurnal/create', ['id' => $id]);
    }

    public function store()
    {
        return $this->response->setJSON(['status' => 'ok']);
    }

    public function edit($id)
    {
        return view('guru/jurnal/edit', ['id' => $id]);
    }

    public function update($id)
    {
        return $this->response->setJSON(['status' => 'ok']);
    }
}
