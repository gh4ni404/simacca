<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('walikelas/dashboard');
    }
}
