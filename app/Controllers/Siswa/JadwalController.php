<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class JadwalController extends BaseController
{
    public function index()
    {
        return view('siswa/jadwal/index');
    }
}
