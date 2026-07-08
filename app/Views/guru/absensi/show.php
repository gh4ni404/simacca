<?php
/**
 * Guru Absensi Show - Device Router
 * This file acts as a router to load device-specific views.
 * - Mobile devices (smartphones) → show_mobile.php
 * - Desktop/Tablet devices → show_desktop.php
 */

// Auto-detect device and load appropriate view (respects manual layout override)
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');
$viewData = get_defined_vars();

if ($isMobile) {
    echo view('guru/absensi/show_mobile', $viewData);
} else {
    echo view('guru/absensi/show_desktop', $viewData);
}