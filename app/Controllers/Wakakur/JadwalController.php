<?php

namespace App\Controllers\Wakakur;

// Wakakur can use Guru's Jadwal Controller (inheritance)
// This provides access to view teaching schedules

class JadwalController extends \App\Controllers\Guru\JadwalController
{
    // Inherit all methods from Guru\JadwalController
    // Wakakur has same access as guru_mapel for viewing schedules
}
