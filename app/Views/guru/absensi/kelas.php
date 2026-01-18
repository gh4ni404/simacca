<?php
/**
 * Guru Absensi Kelas View - Device Router
 * Routes to appropriate view based on device type
 */

// Detect device type
$isMobile = is_mobile_device();

// Route to appropriate view
if ($isMobile) {
    echo view('guru/absensi/kelas_mobile', $data ?? []);
} else {
    echo view('guru/absensi/kelas_desktop', $data ?? []);
}
