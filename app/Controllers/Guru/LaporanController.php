<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;

class LaporanController extends BaseController
{
    public function index()
    {
        return view('guru/laporan/index');
    }
}
