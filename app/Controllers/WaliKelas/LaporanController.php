<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;

class LaporanController extends BaseController
{
    public function index()
    {
        return view('walikelas/laporan/index');
    }
}
