<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class ProfilController extends BaseController
{
    public function index()
    {
        return view('siswa/profil/index');
    }
}
