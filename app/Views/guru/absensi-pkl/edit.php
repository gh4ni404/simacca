<?php
$layout = get_device_layout();
$isMobile = ($layout === 'templates/mobile_layout');

if ($isMobile) {
    echo view('guru/absensi-pkl/edit_mobile', get_defined_vars());
} else {
    echo view('guru/absensi-pkl/edit_desktop', get_defined_vars());
}
