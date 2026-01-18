<?php

namespace App\Controllers\Wakakur;

// Wakakur can use Guru's Jurnal Controller (inheritance)
// This provides full access to create, edit, view jurnal KBM

class JurnalController extends \App\Controllers\Guru\JurnalController
{
    // Inherit all methods from Guru\JurnalController
    // Wakakur has same access as guru_mapel for managing jurnal
}
