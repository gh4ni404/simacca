<?= $this->extend('templates/desktop_layout') ?>

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

<!-- Enhanced Page Header -->
<div class="bg-gradient-to-r from-green-600 to-emerald-500 rounded-2xl shadow-lg p-6 mb-6 text-white no-print">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Laporan Absensi Detail</h1>
            <p class="text-green-100 flex items-center">
                <i class="fas fa-chart-bar mr-2"></i>
                Laporan detail seluruh absensi pembelajaran
            </p>
        </div>
        <div class="hidden md:block">
            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-file-chart-line text-4xl"></i>
            </div>
        </div>
    </div>
</div>

<?= render_flash_message() ?>

<!-- Enhanced Filter Section -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 no-print">
    <div class="bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-4">
        <h5 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-filter mr-3"></i>
            Filter Laporan
        </h5>
    </div>
    <div class="p-6">
        <form method="GET" action="<?= base_url('wakakur/laporan') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>
                    Tanggal
                </label>
                <input type="date" name="tanggal" value="<?= esc($tanggal); ?>" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                       required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-school text-green-500 mr-1"></i>
                    Kelas
                </label>
                <select name="kelas_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Semua Kelas</option>
                    <?php if (!empty($kelasList)): ?>
                        <?php foreach ($kelasList as $id => $nama): ?>
                            <option value="<?= $id; ?>" <?= ($kelasId == $id ? 'selected' : ''); ?>><?= esc($nama); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Terapkan
                </button>
                <a href="<?= base_url('wakakur/laporan/print') . '?tanggal=' . $tanggal . ($kelasId ? '&kelas_id=' . $kelasId : ''); ?>" 
                   target="_blank" 
                   class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center">
                    <i class="fas fa-print mr-2"></i>
                    Cetak
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<?php if (!empty($laporanPerHari)): ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 no-print">
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-4 text-white">
        <p class="text-xs font-medium text-green-100 uppercase tracking-wider mb-1">Sudah Isi</p>
        <p class="text-3xl font-bold"><?= $totalStats['jadwal_sudah_isi']; ?></p>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-xl shadow-lg p-4 text-white">
        <p class="text-xs font-medium text-red-100 uppercase tracking-wider mb-1">Belum Isi</p>
        <p class="text-3xl font-bold"><?= $totalStats['jadwal_belum_isi']; ?></p>
    </div>
    <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg p-4 text-white">
        <p class="text-xs font-medium text-blue-100 uppercase tracking-wider mb-1">Total Jadwal</p>
        <p class="text-3xl font-bold"><?= $totalStats['total_jadwal']; ?></p>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-4 text-white">
        <p class="text-xs font-medium text-purple-100 uppercase tracking-wider mb-1">% Pengisian</p>
        <p class="text-3xl font-bold"><?= $totalStats['percentage_isi']; ?>%</p>
    </div>
</div>
<?php endif; ?>

<!-- Tabel Laporan -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden print-no-shadow">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-500 to-indigo-600 no-print">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-table mr-3"></i>
                Detail Absensi Pembelajaran
            </h2>
            <span class="text-sm text-purple-100">Tanggal: <?= date('d/m/Y', strtotime($tanggal)); ?></span>
        </div>
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
                        <td colspan="12" class="px-6 py-8 text-center text-gray-500 border border-gray-300">
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
            <p class="font-semibold mb-1">Wakil Kepala Kurikulum</p>
            <div class="mt-16 mb-2">
                <p class="font-semibold"><?= esc($guru['nama_lengkap']); ?></p>
                <p class="text-xs">NIP. <?= esc($guru['nip'] ?? '__________________'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan foto -->
<div id="imageModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 no-print" onclick="closeImageModal()">
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
        .no-print {
            display: none !important;
        }

        .print-header,
        .print-footer {
            display: block !important;
        }

        @page {
            size: A4 portrait;
            margin: 1.5cm 1cm;
        }

        body {
            font-size: 9pt;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .print-no-shadow {
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            page-break-inside: auto;
        }

        .print-table th,
        .print-table td {
            padding: 5px 4px !important;
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
            font-size: 8pt !important;
        }

        .print-border td {
            border: 1px solid #000 !important;
        }

        tr {
            page-break-inside: avoid;
        }

        .print-nowrap {
            white-space: nowrap;
        }

        .print-overflow-visible {
            overflow: visible !important;
        }

        .print-table img {
            max-width: 80px !important;
            max-height: 80px !important;
            object-fit: cover;
        }

        /* Column widths for portrait layout */
        .print-table th:nth-child(1),
        .print-table td:nth-child(1) {
            width: 4%;
        }

        .print-table th:nth-child(2),
        .print-table td:nth-child(2) {
            width: 8%;
        }

        .print-table th:nth-child(3),
        .print-table td:nth-child(3) {
            width: 7%;
        }

        .print-table th:nth-child(4),
        .print-table td:nth-child(4) {
            width: 15%;
        }

        .print-table th:nth-child(5),
        .print-table td:nth-child(5) {
            width: 12%;
        }

        .print-table th:nth-child(6),
        .print-table td:nth-child(6) {
            width: 18%;
        }

        .print-table th:nth-child(7),
        .print-table td:nth-child(7) {
            width: 4%;
            text-align: center;
        }

        .print-table th:nth-child(8),
        .print-table td:nth-child(8) {
            width: 4%;
            text-align: center;
        }

        .print-table th:nth-child(9),
        .print-table td:nth-child(9) {
            width: 4%;
            text-align: center;
        }

        .print-table th:nth-child(10),
        .print-table td:nth-child(10) {
            width: 4%;
            text-align: center;
        }

        .print-table th:nth-child(11),
        .print-table td:nth-child(11) {
            width: 8%;
            text-align: center;
        }

        .print-table th:nth-child(12),
        .print-table td:nth-child(12) {
            width: 12%;
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
