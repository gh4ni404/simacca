<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;

class AbsensiController extends BaseController
{
    public function index()
    {
        return view('walikelas/absensi/index');
    }
}
