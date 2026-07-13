<?php
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');

if ($isMobile) {
    echo view('admin/absensi-pkl/index_mobile', get_defined_vars());
} else {
    echo view('admin/absensi-pkl/index_desktop', get_defined_vars());
}
