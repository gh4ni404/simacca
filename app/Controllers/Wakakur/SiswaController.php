<?php

namespace App\Controllers\Wakakur;

// Wakakur can use WaliKelas's Siswa Controller (inheritance)
// This provides access to view students in their wali kelas class (if applicable)

class SiswaController extends \App\Controllers\WaliKelas\SiswaController
{
    // Inherit all methods from WaliKelas\SiswaController
    // Wakakur has same access as wali_kelas for viewing students
}
