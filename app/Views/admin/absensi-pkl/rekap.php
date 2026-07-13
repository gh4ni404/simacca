<?php
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');

if ($isMobile) {
    echo view('admin/absensi-pkl/rekap_mobile', get_defined_vars());
} else {
    echo view('admin/absensi-pkl/rekap_desktop', get_defined_vars());
}
