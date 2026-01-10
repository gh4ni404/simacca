<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;

class IzinController extends BaseController
{
    public function index()
    {
        return view('walikelas/izin/index');
    }

    public function approve($id)
    {
        return $this->response->setJSON(['status' => 'approved', 'id' => $id]);
    }

    public function reject($id)
    {
        return $this->response->setJSON(['status' => 'rejected', 'id' => $id]);
    }
}
