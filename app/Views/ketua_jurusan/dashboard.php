<?php
/**
 * Ketua Jurusan Dashboard - Device Router
 */
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');

if ($isMobile) {
    echo view('ketua_jurusan/dashboard_mobile', get_defined_vars());
} else {
    echo view('ketua_jurusan/dashboard_desktop', get_defined_vars());
}
