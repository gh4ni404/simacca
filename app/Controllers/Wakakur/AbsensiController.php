<?php

namespace App\Controllers\Wakakur;

// Wakakur can use Guru's Absensi Controller (inheritance)
// This provides full access to create, edit, view absensi like guru_mapel

class AbsensiController extends \App\Controllers\Guru\AbsensiController
{
    // Inherit all methods from Guru\AbsensiController
    // Wakakur has same access as guru_mapel for managing attendance
}
