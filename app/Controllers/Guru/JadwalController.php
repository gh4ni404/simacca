<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;

class JadwalController extends BaseController
{
    public function index()
    {
        return view('guru/jadwal/index');
    }
}
