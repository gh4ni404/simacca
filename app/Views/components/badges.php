<?php
/**
 * Badge Components for Status Display
 */

if (!function_exists('status_badge')) {
    /**
     * Generate status badge with icon
     * 
     * @param string $status Status value (hadir, sakit, izin, alpha, etc)
     * @return string
     */
    function status_badge($status)
    {
        $badges = [
            'H' => ['text' => 'Hadir', 'color' => 'green', 'icon' => 'check'],
            'Hadir' => ['text' => 'Hadir', 'color' => 'green', 'icon' => 'check'],
            'S' => ['text' => 'Sakit', 'color' => 'yellow', 'icon' => 'stethoscope'],
            'Sakit' => ['text' => 'Sakit', 'color' => 'yellow', 'icon' => 'stethoscope'],
            'I' => ['text' => 'Izin', 'color' => 'blue', 'icon' => 'file-alt'],
            'Izin' => ['text' => 'Izin', 'color' => 'blue', 'icon' => 'file-alt'],
            'A' => ['text' => 'Alpha', 'color' => 'red', 'icon' => 'times'],
            'Alpha' => ['text' => 'Alpha', 'color' => 'red', 'icon' => 'times'],
            'active' => ['text' => 'Aktif', 'color' => 'green', 'icon' => 'check-circle'],
            'inactive' => ['text' => 'Tidak Aktif', 'color' => 'red', 'icon' => 'ban'],
            'pending' => ['text' => 'Menunggu', 'color' => 'yellow', 'icon' => 'clock'],
            'approved' => ['text' => 'Disetujui', 'color' => 'green', 'icon' => 'check'],
            'rejected' => ['text' => 'Ditolak', 'color' => 'red', 'icon' => 'times-circle'],
        ];
        
        $badge = $badges[$status] ?? ['text' => $status, 'color' => 'gray', 'icon' => 'circle'];
        
        $colorClasses = [
            'green'  => 'bg-green-100 text-green-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'red'    => 'bg-red-100 text-red-800',
            'blue'   => 'bg-blue-100 text-blue-800',
            'gray'   => 'bg-gray-100 text-gray-800',
        ];
        
        $colorClass = $colorClasses[$badge['color']] ?? $colorClasses['gray'];
        
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . '">
            <i class="fas fa-' . $badge['icon'] . ' mr-1"></i>
            ' . esc($badge['text']) . '
        </span>';
    }
}
