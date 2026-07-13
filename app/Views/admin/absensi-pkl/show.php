<?php
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');

if ($isMobile) {
    echo view('admin/absensi-pkl/show_mobile', get_defined_vars());
} else {
    echo view('admin/absensi-pkl/show_desktop', get_defined_vars());
}
