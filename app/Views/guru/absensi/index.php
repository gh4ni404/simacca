<?php
/**
 * Guru Absensi Index - Device Router
 * 
 * This file acts as a router to load device-specific views.
 * - Mobile devices (smartphones) → index_mobile.php
 * - Desktop/Tablet devices → index_desktop.php
 * 
 * @see app/Views/guru/absensi/index_mobile.php - Mobile optimized layout
 * @see app/Views/guru/absensi/index_desktop.php - Desktop optimized layout
 */

// Auto-detect device and load appropriate view (respects manual layout override)
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');

if ($isMobile) {
    echo view('guru/absensi/index_mobile', get_defined_vars());
} else {
    echo view('guru/absensi/index_desktop', get_defined_vars());
}
