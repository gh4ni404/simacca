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

if (!function_exists('get_jurnal_pkl_end_date')) {
    function get_jurnal_pkl_end_date(): ?string
    {
        $settingModel = model(SettingModel::class);
        return $settingModel->get('jurnal_pkl_end_date') ?: null;
    }
}

if (!function_exists('set_jurnal_pkl_end_date')) {
    function set_jurnal_pkl_end_date(string $date): bool
    {
        $settingModel = model(SettingModel::class);
        return $settingModel->setSetting('jurnal_pkl_end_date', $date);
    }
}

if (!function_exists('get_jurnal_pkl_duration_days')) {
    function get_jurnal_pkl_duration_days(): ?int
    {
        $startDate = get_jurnal_pkl_start_date();
        $endDate = get_jurnal_pkl_end_date();
        if (!$startDate || !$endDate) return null;

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        if ($end < $start) return null;

        return (int) $start->diff($end)->days + 1;
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

if (!function_exists('get_jurnal_pkl_required_days')) {
    function get_jurnal_pkl_required_days(): int
    {
        $settingModel = model(SettingModel::class);
        $days = $settingModel->get('jurnal_pkl_required_days');
        return $days ? (int) $days : 5;
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

if (!function_exists('get_pkl_progress_display_status')) {
    function get_pkl_progress_display_status(array $progress): string
    {
        $status = $progress['status'] ?? 'draft';
        if (in_array($status, ['draft', 'submitted', 'revision'])) return $status;

        $pembimbingOk = $status === 'approved'
            && !empty($progress['verified_by'])
            && !empty($progress['catatan_pembimbing']);
        $instrukturOk = !empty($progress['instruktur_verified_by'])
            && !empty($progress['catatan_instruktur']);

        if ($pembimbingOk && $instrukturOk) return 'completed';
        if ($pembimbingOk) return 'pending_instruktur';
        if ($status === 'verified_by_instruktur') return 'pending_pembimbing';

        return $status;
    }
}

if (!function_exists('get_pkl_status_style')) {
    function get_pkl_status_style(string $displayStatus): array
    {
        return match ($displayStatus) {
            'completed' => [
                'label' => 'Selesai',
                'icon' => 'fa-check',
                'color' => 'text-green-500',
                'bg' => 'bg-green-100 text-green-700',
                'icon_bg' => 'bg-green-100 text-green-600',
                'badge_icon' => null,
            ],
            'approved', 'pending_instruktur' => [
                'label' => 'Proses Instruktur',
                'icon' => 'fa-circle-check',
                'color' => 'text-orange-500',
                'bg' => 'bg-orange-100 text-orange-700',
                'icon_bg' => 'bg-orange-100 text-orange-600',
                'badge_icon' => 'fa-industry',
            ],
            'verified_by_instruktur', 'pending_pembimbing' => [
                'label' => 'Proses Pembimbing',
                'icon' => 'fa-check-double',
                'color' => 'text-blue-500',
                'bg' => 'bg-blue-100 text-blue-700',
                'icon_bg' => 'bg-blue-100 text-blue-600',
                'badge_icon' => 'fa-chalkboard',
            ],
            'submitted' => [
                'label' => 'Menunggu',
                'icon' => 'fa-clock',
                'color' => 'text-yellow-500',
                'bg' => 'bg-yellow-100 text-yellow-700',
                'icon_bg' => 'bg-yellow-100 text-yellow-600',
                'badge_icon' => null,
            ],
            'revision' => [
                'label' => 'Revisi',
                'icon' => 'fa-edit',
                'color' => 'text-red-500',
                'bg' => 'bg-red-100 text-red-700',
                'icon_bg' => 'bg-orange-100 text-orange-600',
                'badge_icon' => null,
            ],
            default => [
                'label' => 'Draft',
                'icon' => 'fa-pen',
                'color' => 'text-gray-500',
                'bg' => 'bg-gray-100 text-gray-600',
                'icon_bg' => 'bg-gray-100 text-gray-500',
                'badge_icon' => null,
            ],
        };
    }
}

