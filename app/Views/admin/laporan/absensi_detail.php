<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>

<!-- Print Header - Only visible when printing -->
<div class="print-header hidden">
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold mb-1">LAPORAN ABSENSI PEMBELAJARAN</h2>
        <h3 class="text-lg font-semibold mb-2">SISTEM INFORMASI AKADEMIK</h3>
        <div class="border-t-2 border-b-2 border-black py-1 inline-block px-8">
            <p class="text-sm">Periode: <?= date('d/m/Y', strtotime($from)); ?> - <?= date('d/m/Y', strtotime($to)); ?></p>
            <?php if ($kelasId): ?>
                <p class="text-sm">Kelas: <?= esc($kelasList[$kelasId] ?? '-'); ?></p>
            <?php else: ?>
                <p class="text-sm">Semua Kelas</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mb-6 no-print">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan Absensi Detail</h1>
    <p class="text-gray-600">Laporan absensi lengkap dengan detail kehadiran per sesi pembelajaran</p>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow p-6 mb-6 no-print">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4" method="get" action="<?= base_url('admin/laporan/absensi-detail'); ?>">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="<?= esc($from); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="<?= esc($to); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <select name="kelas_id" class="w-full border rounded-lg px-3 py-2">
                <option value="">Semua Kelas</option>
                <?php if (!empty($kelasList)): ?>
                    <?php foreach ($kelasList as $id => $nama): ?>
                        <option value="<?= $id; ?>" <?= ($kelasId == $id ? 'selected' : ''); ?>><?= esc($nama); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-filter mr-2"></i>Terapkan
            </button>
            <a href="<?= base_url('admin/laporan/absensi-detail/print') . '?from=' . $from . '&to=' . $to . ($kelasId ? '&kelas_id=' . $kelasId : ''); ?>" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-print mr-2"></i>Cetak
            </a>
        </div>
    </form>
</div>

<!-- Tabel Laporan -->
<div class="bg-white rounded-xl shadow overflow-hidden print-no-shadow">
    <div class="p-6 border-b border-gray-200 no-print">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Detail Absensi Pembelajaran</h2>
            <span class="text-sm text-gray-500">Periode: <?= date('d/m/Y', strtotime($from)); ?> - <?= date('d/m/Y', strtotime($to)); ?></span>
        </div>
        <?php if (!empty($laporanData)): ?>
            <p class="text-sm text-gray-600 mt-2">Total: <?= count($laporanData); ?> sesi pembelajaran</p>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto print-overflow-visible">
        <table class="min-w-full divide-y divide-gray-200 print-table">
            <thead class="bg-gray-50 print-thead">
                <tr>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">No</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Tanggal</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Kelas</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Jam</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Guru Mapel</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Mata Pelajaran</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Wali Kelas</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">Hadir</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">Sakit</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">Izin</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">Alpa</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Catatan Khusus</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">Foto</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Guru Pengganti</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($laporanData)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($laporanData as $row): ?>
                        <tr class="hover:bg-gray-50 print-border">
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300"><?= $no++; ?></td>
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300 print-nowrap">
                                <?= date('d/m/Y', strtotime($row['tanggal'])); ?>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300">
                                <?= esc($row['nama_kelas']); ?>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300 print-nowrap">
                                <?= date('H:i', strtotime($row['jam_mulai'])); ?>-<?= date('H:i', strtotime($row['jam_selesai'])); ?>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300">
                                <?= esc($row['nama_guru']); ?>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300">
                                <?= esc($row['nama_mapel']); ?>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300">
                                <?= $row['nama_wali_kelas'] ? esc($row['nama_wali_kelas']) : '-'; ?>
                            </td>
                            <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 print-badge">
                                    <?= (int)$row['jumlah_hadir']; ?>
                                </span>
                            </td>
                            <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 print-badge">
                                    <?= (int)$row['jumlah_sakit']; ?>
                                </span>
                            </td>
                            <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 print-badge">
                                    <?= (int)$row['jumlah_izin']; ?>
                                </span>
                            </td>
                            <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 print-badge">
                                    <?= (int)$row['jumlah_alpa']; ?>
                                </span>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300">
                                <?php if (!empty($row['catatan_khusus'])): ?>
                                    <div class="max-w-xs print-max-w-full">
                                        <p class="text-gray-700 print-text-xs" title="<?= esc($row['catatan_khusus']); ?>">
                                            <?= esc(strlen($row['catatan_khusus']) > 50 ? substr($row['catatan_khusus'], 0, 50) . '...' : $row['catatan_khusus']); ?>
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <!-- - -->
                                    <div class="max-w-xs print-max-w-full text-center">
                                        -
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                <?php if (!empty($row['foto_dokumentasi'])): ?>
                                    <img src="<?= base_url('files/jurnal/' . esc($row['foto_dokumentasi'])) ?>"
                                        alt="Foto Dokumentasi"
                                        class="w-16 h-16 object-cover rounded-lg mx-auto cursor-pointer hover:scale-110 transition-transform"
                                        onclick="showImageModal('<?= base_url('files/jurnal/' . esc($row['foto_dokumentasi'])) ?>')">
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">
                                        <i class="fas fa-image"></i><br>Tidak ada foto
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300">
                                <?php if (!empty($row['nama_guru_pengganti'])): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 print-badge">
                                        <?= esc($row['nama_guru_pengganti']); ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="14" class="px-6 py-8 text-center text-gray-500 border border-gray-300">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada data absensi untuk periode ini.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Print Footer - Only visible when printing -->
<div class="print-footer hidden mt-8">
    <div class="flex justify-between items-start px-8">
        <div class="text-left">
            <p class="mb-1">Mengetahui,</p>
            <p class="font-semibold mb-1">Kepala Sekolah</p>
            <div class="mt-16 mb-2">
                <p class="font-semibold">_____________________</p>
                <p class="text-xs">NIP. __________________</p>
            </div>
        </div>
        <div class="text-right">
            <p class="mb-1">Dicetak tanggal: <?= date('d/m/Y H:i'); ?></p>
            <p class="font-semibold mb-1">Petugas Admin</p>
            <div class="mt-16 mb-2">
                <p class="font-semibold">_____________________</p>
                <p class="text-xs">NIP. __________________</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan foto -->
<div id="imageModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="closeImageModal()">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Foto Dokumentasi</h3>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-3 text-center">
            <img id="modalImage" src="" alt="Foto Dokumentasi" class="max-w-full h-auto rounded-lg mx-auto">
        </div>
    </div>
</div>

<style>
    /* Print styles */
    @media print {

        /* Hide non-print elements */
        .no-print {
            display: none !important;
        }

        /* Show print-only elements */
        .print-header,
        .print-footer {
            display: block !important;
        }

        .print-only-inline {
            display: inline-block !important;
        }

        /* Page setup for landscape */
        @page {
            size: A4 landscape;
            margin: 1cm 0.5cm;
        }

        /* Body and general styles */
        body {
            font-size: 8pt;
            line-height: 1.3;
            color: #000;
            background: white;
        }

        /* Remove shadows and rounded corners */
        .print-no-shadow {
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        /* Table styles */
        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            page-break-inside: auto;
        }

        .print-table th,
        .print-table td {
            padding: 3px 2px !important;
            border: 1px solid #000 !important;
            vertical-align: top;
        }

        .print-thead {
            background-color: #e5e7eb !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }

        .print-thead th {
            font-size: 7pt !important;
            font-weight: bold;
            text-align: center;
        }

        /* Table rows */
        .print-border td {
            border: 1px solid #000 !important;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Text wrapping */
        .print-nowrap {
            white-space: nowrap;
        }

        .print-max-w-full {
            max-width: 100% !important;
        }

        .print-text-xs {
            font-size: 6pt !important;
            line-height: 1.2;
        }

        /* Badge styles for print */
        .print-badge {
            background-color: transparent !important;
            color: #000 !important;
            border: 1px solid #000 !important;
            padding: 1px 3px !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Overflow handling */
        .print-overflow-visible {
            overflow: visible !important;
        }

        /* Specific column widths for better layout */
        .print-table th:nth-child(1),
        .print-table td:nth-child(1) {
            width: 3%;
        }

        /* No */
        .print-table th:nth-child(2),
        .print-table td:nth-child(2) {
            width: 7%;
        }

        /* Tanggal */
        .print-table th:nth-child(3),
        .print-table td:nth-child(3) {
            width: 6%;
        }

        /* Kelas */
        .print-table th:nth-child(4),
        .print-table td:nth-child(4) {
            width: 8%;
        }

        /* Jam */
        .print-table th:nth-child(5),
        .print-table td:nth-child(5) {
            width: 10%;
        }

        /* Guru Mapel */
        .print-table th:nth-child(6),
        .print-table td:nth-child(6) {
            width: 10%;
        }

        /* Mata Pelajaran */
        .print-table th:nth-child(7),
        .print-table td:nth-child(7) {
            width: 10%;
        }

        /* Wali Kelas */
        .print-table th:nth-child(8),
        .print-table td:nth-child(8) {
            width: 3%;
            text-align: center;
        }

        /* H */
        .print-table th:nth-child(9),
        .print-table td:nth-child(9) {
            width: 3%;
            text-align: center;
        }

        /* S */
        .print-table th:nth-child(10),
        .print-table td:nth-child(10) {
            width: 3%;
            text-align: center;
        }

        /* I */
        .print-table th:nth-child(11),
        .print-table td:nth-child(11) {
            width: 3%;
            text-align: center;
        }

        /* A */
        .print-table th:nth-child(12),
        .print-table td:nth-child(12) {
            width: 15%;
        }

        /* Catatan Khusus */
        .print-table th:nth-child(13),
        .print-table td:nth-child(13) {
            width: 8%;
            text-align: center;
        }

        /* Foto */
        .print-table th:nth-child(14),
        .print-table td:nth-child(14) {
            width: 11%;
        }

        /* Guru Pengganti */

        /* Image in print */
        .print-table img {
            max-width: 40px !important;
            max-height: 40px !important;
            object-fit: cover;
        }

        /* Remove hover effects */
        .hover\:bg-gray-50:hover {
            background-color: transparent !important;
        }
    }

    /* Screen styles for line-clamp */
    @media screen {
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            overflow: hidden;
        }
    }
</style>

<script>
    function showImageModal(imageUrl) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }
</script>
<?= $this->endSection() ?>