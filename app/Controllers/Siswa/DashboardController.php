<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('siswa/dashboard');
    }
}
