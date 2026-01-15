<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>

<!-- Print Header - Only visible when printing -->
<div class="print-header hidden">
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold mb-1">LAPORAN ABSENSI PEMBELAJARAN</h2>
        <h3 class="text-lg font-semibold mb-2">SISTEM INFORMASI AKADEMIK</h3>
        <div class="border-t-2 border-b-2 border-black py-1 inline-block px-8">
            <p class="text-sm">Tanggal: <?= date('d/m/Y', strtotime($tanggal)); ?></p>
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
    <form class="grid grid-cols-1 md:grid-cols-3 gap-4" method="get" action="<?= base_url('admin/laporan/absensi-detail'); ?>">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="<?= esc($tanggal); ?>" class="w-full border rounded-lg px-3 py-2" required>
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
            <a href="<?= base_url('admin/laporan/absensi-detail/print') . '?tanggal=' . $tanggal . ($kelasId ? '&kelas_id=' . $kelasId : ''); ?>" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
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
            <span class="text-sm text-gray-500">Tanggal: <?= date('d/m/Y', strtotime($tanggal)); ?></span>
        </div>
        <?php if (!empty($laporanPerHari)): ?>
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <p class="text-xs text-green-600 font-medium">Sudah Isi</p>
                    <p class="text-2xl font-bold text-green-700"><?= $totalStats['jadwal_sudah_isi']; ?></p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <p class="text-xs text-red-600 font-medium">Belum Isi</p>
                    <p class="text-2xl font-bold text-red-700"><?= $totalStats['jadwal_belum_isi']; ?></p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-600 font-medium">Total Jadwal</p>
                    <p class="text-2xl font-bold text-blue-700"><?= $totalStats['total_jadwal']; ?></p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                    <p class="text-xs text-purple-600 font-medium">% Pengisian</p>
                    <p class="text-2xl font-bold text-purple-700"><?= $totalStats['percentage_isi']; ?>%</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto print-overflow-visible">
        <table class="min-w-full divide-y divide-gray-200 print-table">
            <thead class="bg-gray-50 print-thead">
                <tr>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">No</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Kelas</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Jam</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Guru Mapel</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Mata Pelajaran</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Kegiatan Pembelajaran</th>
                    <!-- <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Wali Kelas</th> -->
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">H</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">S</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">I</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">A</th>
                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase border border-gray-300">Foto</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase border border-gray-300">Pengganti</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($laporanPerHari)): ?>
                    <?php $globalNo = 1; ?>
                    <?php foreach ($laporanPerHari as $hariData): ?>
                        <?php foreach ($hariData['jadwal_list'] as $jadwal): ?>
                            <?php 
                            $belumIsi = empty($jadwal['absensi_id']);
                            $rowClass = $belumIsi ? 'bg-red-50' : 'hover:bg-gray-50';
                            ?>
                            <tr class="<?= $rowClass; ?> print-border">
                                <td class="px-2 py-2 text-sm <?= $belumIsi ? 'text-red-700 font-semibold' : 'text-gray-900'; ?> border border-gray-300"><?= $globalNo++; ?></td>
                                <td class="px-2 py-2 text-sm <?= $belumIsi ? 'text-red-700 font-semibold' : 'text-gray-900'; ?> border border-gray-300">
                                    <?= esc($jadwal['nama_kelas']); ?>
                                </td>
                                <td class="px-2 py-2 text-sm <?= $belumIsi ? 'text-red-700 font-semibold' : 'text-gray-900'; ?> border border-gray-300 print-nowrap">
                                    <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?>
                                </td>
                                <td class="px-2 py-2 text-sm <?= $belumIsi ? 'text-red-700 font-semibold' : 'text-gray-900'; ?> border border-gray-300">
                                    <?= esc($jadwal['nama_guru']); ?>
                                </td>
                                <td class="px-2 py-2 text-sm <?= $belumIsi ? 'text-red-700 font-semibold' : 'text-gray-900'; ?> border border-gray-300">
                                    <?= esc($jadwal['nama_mapel']); ?>
                                </td>

                                <!-- hilangkan tag phpnya jika dibutuhkan -->
                               <?php /**
                                *  <td class="px-2 py-2 text-sm <?= $belumIsi ? 'text-red-700' : 'text-gray-900'; ?> border border-gray-300">
                                *    <?= $jadwal['nama_wali_kelas'] ? esc($jadwal['nama_wali_kelas']) : '-'; ?>
                                *  </td>
                                */ ?>
                                
                                <?php if ($belumIsi): ?>
                                    <td class="px-2 py-2 text-center text-sm text-red-700 border border-gray-300">-</td>
                                    <td class="px-2 py-2 text-center border border-gray-300" colspan="4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-600 text-white">
                                            BELUM ISI
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-center text-sm text-red-700 border border-gray-300">-</td>
                                    <td class="px-2 py-2 text-center text-sm text-red-700 border border-gray-300">-</td>
                                <?php else: ?>
                                    <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300">
                                        <?php if (!empty($jadwal['kegiatan_pembelajaran'])): ?>
                                            <div class="max-w-xs">
                                                <p class="text-gray-700 text-xs text-left" title="<?= esc($jadwal['kegiatan_pembelajaran']); ?>">
                                                    <?= esc(strlen($jadwal['kegiatan_pembelajaran']) > 50 ? substr($jadwal['kegiatan_pembelajaran'], 0, 50) . '...' : $jadwal['kegiatan_pembelajaran']); ?>
                                                </p>
                                            </div>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-green-800">
                                            <?= (int)$jadwal['jumlah_hadir']; ?>
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-yellow-800">
                                            <?= (int)$jadwal['jumlah_sakit']; ?>
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-blue-800">
                                            <?= (int)$jadwal['jumlah_izin']; ?>
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-red-800">
                                            <?= (int)$jadwal['jumlah_alpa']; ?>
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-center text-sm border border-gray-300">
                                        <?php if (!empty($jadwal['foto_dokumentasi'])): ?>
                                            <img src="<?= base_url('files/jurnal/' . esc($jadwal['foto_dokumentasi'])) ?>"
                                                alt="Foto"
                                                class="w-36 h-36 object-cover rounded mx-auto cursor-pointer hover:scale-110 transition-transform"
                                                onclick="showImageModal('<?= base_url('files/jurnal/' . esc($jadwal['foto_dokumentasi'])) ?>')">
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">
                                                <i class="fas fa-image"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-2 text-sm text-gray-900 border border-gray-300">
                                        <?php if (!empty($jadwal['nama_guru_pengganti'])): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <?= esc($jadwal['nama_guru_pengganti']); ?>
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13" class="px-6 py-8 text-center text-gray-500 border border-gray-300">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada jadwal untuk tanggal ini.</p>
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
            max-width: 100px !important;
            max-height: 100px !important;
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
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        
        if (modal && modalImage) {
            modalImage.src = imageUrl;
            modal.classList.remove('hidden');
        }
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    // Close modal when clicking outside the image
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageModal();
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>