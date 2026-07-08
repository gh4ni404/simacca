<?php
/**
 * Guru Dashboard - Device Router
 * 
 * This file acts as a router to load device-specific dashboard views.
 * - Mobile devices (smartphones) → dashboard_mobile.php
 * - Desktop/Tablet devices → dashboard_desktop.php
 * 
 * @see app/Views/guru/dashboard_mobile.php - Mobile optimized layout
 * @see app/Views/guru/dashboard_desktop.php - Desktop optimized layout
 */

// Auto-detect device and load appropriate view (respects manual layout override)
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');

if ($isMobile) {
    echo view('guru/dashboard_mobile', get_defined_vars());
} else {
    echo view('guru/dashboard_desktop', get_defined_vars());
}
