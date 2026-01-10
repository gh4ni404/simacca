<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;

class SiswaController extends BaseController
{
    public function index()
    {
        return view('walikelas/siswa/index');
    }
}
