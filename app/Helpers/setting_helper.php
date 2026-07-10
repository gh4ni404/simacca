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

if (!function_exists('get_jurnal_pkl_start_date')) {
    function get_jurnal_pkl_start_date(): ?string
    {
        $settingModel = model(SettingModel::class);
        return $settingModel->get('jurnal_pkl_start_date') ?: null;
    }
}

if (!function_exists('set_jurnal_pkl_start_date')) {
    function set_jurnal_pkl_start_date(string $date): bool
    {
        $settingModel = model(SettingModel::class);
        return $settingModel->setSetting('jurnal_pkl_start_date', $date);
    }
}

if (!function_exists('get_jurnal_pkl_week_base')) {
    function get_jurnal_pkl_week_base(): ?string
    {
        $startDate = get_jurnal_pkl_start_date();
        if (!$startDate) return null;

        $dt = new \DateTime($startDate);
        $dayOfWeek = (int) $dt->format('N');
        if ($dayOfWeek > 1) {
            $dt->modify('-' . ($dayOfWeek - 1) . ' days');
        }
        return $dt->format('Y-m-d');
    }
}

if (!function_exists('get_week_number')) {
    function get_week_number(string $dateStr, ?string $startDate = null): int
    {
        if ($startDate === null) {
            $startDate = get_jurnal_pkl_start_date();
        }
        if (!$startDate) {
            return (int) date('W', strtotime($dateStr));
        }

        $weekBase = get_jurnal_pkl_week_base();
        $base = new \DateTime($weekBase);
        $date = new \DateTime($dateStr);
        if ($date < $base) {
            return 0;
        }
        $diff = $base->diff($date);
        $days = (int) $diff->format('%a');
        return (int) floor($days / 7) + 1;
    }
}

if (!function_exists('get_week_range')) {
    function get_week_range(string $startDate, int $minggu): array
    {
        $weekBase = get_jurnal_pkl_week_base();
        if (!$weekBase) {
            return ['start' => '', 'end' => ''];
        }

        $base = new \DateTime($weekBase);
        $interval = new \DateInterval('P' . (($minggu - 1) * 7) . 'D');

        $weekStart = clone $base;
        $weekStart->add($interval);

        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');

        if ($minggu === 1) {
            $startDt = new \DateTime($startDate);
            if ($weekStart < $startDt) {
                $weekStart = $startDt;
            }
        }

        return [
            'start' => $weekStart->format('Y-m-d'),
            'end' => $weekEnd->format('Y-m-d'),
        ];
    }
}
