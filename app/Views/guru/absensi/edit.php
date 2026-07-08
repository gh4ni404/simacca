<?php
/**
 * Guru Absensi Edit - Device Router
 * 
 * This file acts as a router to load device-specific views.
 * - Mobile devices (smartphones) → edit_mobile.php
 * - Desktop/Tablet devices → edit_desktop.php
 * 
 * @see app/Views/guru/absensi/edit_mobile.php - Mobile optimized layout
 * @see app/Views/guru/absensi/edit_desktop.php - Desktop optimized layout
 */

// Auto-detect device and load appropriate view (respects manual layout override)
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');

if ($isMobile) {
    echo view('guru/absensi/edit_mobile', get_defined_vars());
} else {
    echo view('guru/absensi/edit_desktop', get_defined_vars());
}
