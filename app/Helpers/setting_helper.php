<?php

use App\Models\SettingModel;

if (!function_exists('get_active_tahun_ajaran')) {
    function get_active_tahun_ajaran(): string
    {
        $settingModel = model(SettingModel::class);
        $tahunAjaran = $settingModel->get('tahun_ajaran_aktif');
        return $tahunAjaran ?? (date('Y') - 1) . '/' . date('Y');
    }
}

if (!function_exists('set_active_tahun_ajaran')) {
    function set_active_tahun_ajaran(string $tahunAjaran): bool
    {
        $settingModel = model(SettingModel::class);
        return $settingModel->setSetting('tahun_ajaran_aktif', $tahunAjaran);
    }
}

if (!function_exists('get_tahun_ajaran_list')) {
    function get_tahun_ajaran_list(): array
    {
        $currentYear = date('Y');
        $years = [];
        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = ($year - 1) . '/' . $year;
        }
        return $years;
    }
}
