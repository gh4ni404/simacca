<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mingguan Absensi Siswa - <?= esc($guru['nama_lengkap']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            body { font-size: 10pt; }
            .no-print { display: none !important; }
            .print-break { page-break-before: always; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-print { border-bottom: 3px solid #4f46e5; }
    </style>
</head>
<body class="bg-white p-8">
    <!-- Print Button -->
    <div class="no-print mb-6 text-center">
        <button onclick="window.print()" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:bg-indigo-700 transition-colors">
            <i class="fas fa-print mr-2"></i> Cetak Laporan
        </button>
        <button onclick="window.close()" class="ml-4 bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:bg-gray-600 transition-colors">
            <i class="fas fa-times mr-2"></i> Tutup
        </button>
    </div>

    <!-- Header -->
    <div class="text-center mb-8 header-print pb-6">
        <h1 class="text-2xl font-bold text-gray-800">LAPORAN MINGGUAN ABSENSI SISWA</h1>
        <h2 class="text-lg text-gray-600 mt-1">Periode: <?= date('d M Y', strtotime($weekStart)) ?> - <?= date('d M Y', strtotime($weekEnd)) ?></h2>
        <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-left max-w-md mx-auto bg-gray-50 p-4 rounded-lg">
            <div>
                <span class="font-semibold text-gray-700">Guru:</span>
                <span class="text-gray-800"><?= esc($guru['nama_lengkap']) ?></span>
            </div>
            <div>
                <span class="font-semibold text-gray-700">NIP:</span>
                <span class="text-gray-800"><?= esc($guru['nip']) ?></span>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Mata Pelajaran:</span>
                <span class="text-gray-800">
                    <?php 
                    $selectedMapelName = 'Semua Mata Pelajaran';
                    if ($selectedMapelId) {
                        foreach ($subjectsList as $subject) {
                            if ($subject['id'] == $selectedMapelId) {
                                $selectedMapelName = $subject['nama_mapel'];
                                break;
                            }
                        }
                    }
                    ?>
                    <?= esc($selectedMapelName) ?>
                </span>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Tahun Ajaran:</span>
                <span class="text-gray-800"><?= get_active_tahun_ajaran() ?></span>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <?php if (!empty($reportBySubject)): ?>
        <?php 
        $totalReports = count($reportBySubject);
        $currentReportIdx = 0;
        foreach ($reportBySubject as $mapelId => $report): 
            $currentReportIdx++;
            $isLastReport = ($currentReportIdx === $totalReports);
        ?>
            <div class="mb-8 <?= !$isLastReport ? 'print-break' : '' ?>">
                <h3 class="text-lg font-bold text-indigo-700 bg-indigo-50 p-3 rounded-t-lg border border-indigo-200 border-b-0">
                    <i class="fas fa-book-open mr-2"></i> <?= esc($report['nama_mapel']) ?>
                </h3>

                <div class="border border-indigo-200 rounded-b-lg overflow-hidden mb-4">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-300">
                                <th class="px-3 py-2 text-left border-r border-gray-300">No</th>
                                <th class="px-3 py-2 text-left border-r border-gray-300">Tanggal</th>
                                <th class="px-3 py-2 text-left border-r border-gray-300">Hari/Jam</th>
                                <th class="px-3 py-2 text-left border-r border-gray-300">Kelas</th>
                                <th class="px-3 py-2 text-center border-r border-gray-300">Pertemuan</th>
                                <th class="px-3 py-2 text-center border-r border-gray-300">Hadir</th>
                                <th class="px-3 py-2 text-center border-r border-gray-300">Sakit</th>
                                <th class="px-3 py-2 text-center border-r border-gray-300">Izin</th>
                                <th class="px-3 py-2 text-center">Alpa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['sessions'] as $idx => $session): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-3 py-2 border-r border-gray-200"><?= $idx + 1 ?></td>
                                    <td class="px-3 py-2 border-r border-gray-200 font-medium">
                                        <?= date('d/m/Y', strtotime($session['tanggal'])) ?>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200">
                                        <?= $session['hari'] ?>, <?= $session['jam'] ?>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200"><?= esc($session['kelas_nama']) ?></td>
                                    <td class="px-3 py-2 text-center border-r border-gray-200">Pert. <?= $session['pertemuan_ke'] ?></td>
                                    <td class="px-3 py-2 text-center border-r border-gray-200 font-semibold text-green-700"><?= $session['hadir'] ?></td>
                                    <td class="px-3 py-2 text-center border-r border-gray-200 font-semibold text-yellow-700"><?= $session['sakit'] ?></td>
                                    <td class="px-3 py-2 text-center border-r border-gray-200 font-semibold text-blue-700"><?= $session['izin'] ?></td>
                                    <td class="px-3 py-2 text-center font-semibold text-red-700"><?= $session['alpa'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 font-bold border-t-2 border-gray-300">
                                <td colspan="5" class="px-3 py-2 text-right border-r border-gray-300">Total</td>
                                <td class="px-3 py-2 text-center border-r border-gray-300 text-green-700"><?= $report['summary']['total_hadir'] ?></td>
                                <td class="px-3 py-2 text-center border-r border-gray-300 text-yellow-700"><?= $report['summary']['total_sakit'] ?></td>
                                <td class="px-3 py-2 text-center border-r border-gray-300 text-blue-700"><?= $report['summary']['total_izin'] ?></td>
                                <td class="px-3 py-2 text-center text-red-700"><?= $report['summary']['total_alpa'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Detail List (Collapsed in print but shows summary) -->
                <div class="text-xs text-gray-500 italic mb-6">
                    Total Pertemuan: <?= $report['summary']['total_pertemuan'] ?> | 
                    Total Siswa Tercatat: <?= $report['summary']['total_siswa'] ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-12 text-gray-500">
            <div class="text-4xl mb-4">📭</div>
            <p class="text-lg">Tidak ada data absensi untuk periode ini</p>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="mt-12 pt-6 border-t border-gray-300 text-xs text-gray-500 text-center">
        <p>Dicetak pada: <?= date('d M Y H:i:s') ?></p>
        <p>Sistem Manajemen Absensi dan Catatan Aktivitas (SIMACCA)</p>
    </div>
</body>
</html>
