<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class AbsensiController extends BaseController
{
    public function index()
    {
        return view('siswa/absensi/index');
    }
}
