<?php

namespace App\Controllers\Wakakur;

// Wakakur can use WaliKelas's Izin Controller (inheritance)
// This provides access to approve/reject student leave requests

class IzinController extends \App\Controllers\WaliKelas\IzinController
{
    // Inherit all methods from WaliKelas\IzinController
    // Wakakur has same access as wali_kelas for managing izin
}
