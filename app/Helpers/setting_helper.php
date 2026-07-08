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
        for ($i = -3; $i <= 5; $i++) {
            $year = $currentYear + $i;
            $years[] = ($year - 1) . '/' . $year;
        }
        return $years;
    }
}

if (!function_exists('validate_tahun_ajaran')) {
    function validate_tahun_ajaran(string $tahunAjaran): ?string
    {
        if (!preg_match('/^\d{4}\/\d{4}$/', $tahunAjaran)) {
            return 'Format tahun ajaran harus YYYY/YYYY (contoh: 2028/2029)';
        }
        $parts = explode('/', $tahunAjaran);
        $tahun1 = (int) $parts[0];
        $tahun2 = (int) $parts[1];
        if ($tahun2 !== $tahun1 + 1) {
            return 'Tahun ajaran tidak valid: ' . $tahun1 . '/' . $tahun2 . '. Harus berurutan (contoh: 2027/2028)';
        }
        return null;
    }
}
