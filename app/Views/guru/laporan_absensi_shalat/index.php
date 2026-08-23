<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>

<?php if (!empty($isQuickTest)): ?>
    <div class="mb-6 bg-gradient-to-r from-amber-500 to-orange-600 text-white px-5 py-3.5 rounded-xl shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-bolt text-2xl"></i>
            <div>
                <p class="font-bold text-sm uppercase tracking-wider">⚡ Quick Test Admin Mode</p>
                <p class="text-xs opacity-90">Pratinjau tampilan portal guru sebagai: <strong><?= esc($guru['nama_lengkap']); ?></strong> (NIP: <?= esc($guru['nip'] ?: '-'); ?>)</p>
            </div>
        </div>
        <button onclick="window.close()" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1">
            <i class="fas fa-times"></i> Tutup Pratinjau
        </button>
    </div>
<?php endif; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-mosque text-green-600"></i>Laporan Absensi Shalat
        </h1>
        <p class="text-gray-600 text-sm mt-1">Rekapitulasi kehadiran shalat pribadi guru dan hasil pengawasan piket shalat</p>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-2">
        <a href="<?= base_url('guru/laporan/absensi-shalat/print?from=' . $from . '&to=' . $to . '&type=personal'); ?>" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm flex items-center gap-2 transition">
            <i class="fas fa-print"></i> Cetak Presensi Saya
        </a>
        <a href="<?= base_url('guru/laporan/absensi-shalat/print?from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&type=piket_semua'); ?>" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm flex items-center gap-2 transition">
            <i class="fas fa-file-invoice"></i> Cetak Laporan Piket
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4" method="get" action="<?= base_url(!empty($isQuickTest) ? 'admin/laporan/absensi-shalat/preview-guru' : 'guru/laporan/absensi-shalat'); ?>">
        <?php if (!empty($isQuickTest)): ?>
            <input type="hidden" name="guru_id" value="<?= esc($guru['id']); ?>">
        <?php endif; ?>
        <input type="hidden" name="tab" value="<?= esc($tab); ?>">
        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="<?= esc($from); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="<?= esc($to); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Kelas (Piket)</label>
            <select name="kelas_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Semua Kelas</option>
                <?php if (!empty($kelasList)): ?>
                    <?php foreach ($kelasList as $id => $nama): ?>
                        <option value="<?= $id; ?>" <?= ($kelasId == $id ? 'selected' : ''); ?>><?= esc($nama); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 font-medium text-sm transition flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-sm p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider font-medium text-emerald-100 mb-1">Presensi Saya</p>
                <p class="text-2xl font-bold"><?= $totalPersonalHadir; ?> <span class="text-xs font-normal opacity-90">Kali</span></p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Piket: Siswa Hadir</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalPiketSiswaHadir; ?></p>
            </div>
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Piket: Guru Hadir</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalPiketGuruHadir; ?></p>
            </div>
            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-amber-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Sesi Shalat</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalSessions; ?></p>
            </div>
            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

<!-- Nav Tabs -->
<?php $baseUrlTab = !empty($isQuickTest) ? base_url('admin/laporan/absensi-shalat/preview-guru') . '?guru_id=' . $guru['id'] . '&' : base_url('guru/laporan/absensi-shalat') . '?'; ?>
<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-6 overflow-x-auto">
        <a href="<?= $baseUrlTab . 'from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&tab=personal'; ?>"
           class="<?= ($tab === 'personal' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?> whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
            <i class="fas fa-user-check text-emerald-600"></i>
            Presensi Shalat Saya
            <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700 font-semibold"><?= $totalPersonalHadir; ?></span>
        </a>
        <a href="<?= $baseUrlTab . 'from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&tab=piket_siswa'; ?>"
           class="<?= ($tab === 'piket_siswa' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?> whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
            <i class="fas fa-user-graduate text-blue-600"></i>
            Hasil Piket: Siswa Hadir
            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 font-semibold"><?= $totalPiketSiswaHadir; ?></span>
        </a>
        <a href="<?= $baseUrlTab . 'from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&tab=piket_guru'; ?>"
           class="<?= ($tab === 'piket_guru' ? 'border-purple-600 text-purple-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?> whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
            <i class="fas fa-chalkboard-teacher text-purple-600"></i>
            Hasil Piket: Guru Lain Hadir
            <span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700 font-semibold"><?= $totalPiketGuruHadir; ?></span>
        </a>
    </nav>
</div>

<!-- Tab Content -->

<?php if ($tab === 'personal'): ?>
    <!-- TAB 1: Presensi Saya -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-emerald-600"></i>
                Riwayat Presensi Shalat Pribadi
            </h2>
            <a href="<?= base_url(!empty($isQuickTest) ? 'admin/laporan/absensi-shalat/preview-guru-print?guru_id=' . $guru['id'] . '&type=personal' : 'guru/laporan/absensi-shalat/print?type=personal') . '&from=' . $from . '&to=' . $to; ?>" target="_blank" class="text-xs bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-3 py-1.5 rounded-md font-semibold transition flex items-center gap-1">
                <i class="fas fa-print"></i> Cetak Presensi Saya
            </a>
        </div>
        
        <?php if (!empty($rekapPersonal)): ?>
            <?php foreach ($rekapPersonal as $date => $rows): ?>
                <div class="mb-4 last:mb-0 border border-gray-100 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-100 font-semibold text-xs text-gray-700 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-emerald-600"></i>
                        <?= date('l, d F Y', strtotime($date)); ?>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/50 text-gray-500 uppercase text-[11px] font-semibold">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">No</th>
                                    <th class="px-4 py-2.5 text-left">Waktu Sesi</th>
                                    <th class="px-4 py-2.5 text-left">Guru Piket Penanggung Jawab</th>
                                    <th class="px-4 py-2.5 text-center">Waktu Scan / Absen</th>
                                    <th class="px-4 py-2.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($rows as $i => $row): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 text-gray-500"><?= $i + 1; ?></td>
                                        <td class="px-4 py-3 font-medium text-gray-800"><?= date('H:i', strtotime($row['waktu_sesi'])); ?> WITA</td>
                                        <td class="px-4 py-3 text-gray-700"><?= esc($row['nama_guru_piket'] ?: 'Guru Piket'); ?></td>
                                        <td class="px-4 py-3 text-center text-gray-600 font-mono"><?= date('H:i:s', strtotime($row['waktu_absen'])); ?></td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                <i class="fas fa-check-circle mr-1 text-[10px]"></i> Hadir
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-10">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500 text-sm">Belum ada data presensi shalat pribadi untuk periode ini.</p>
            </div>
        <?php endif; ?>
    </div>

<?php elseif ($tab === 'piket_siswa'): ?>
    <!-- TAB 2: Piket Siswa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-graduate text-blue-600"></i>
                Rekapan Kehadiran Siswa (Saat Piket Shalat)
            </h2>
            <a href="<?= base_url(!empty($isQuickTest) ? 'admin/laporan/absensi-shalat/preview-guru-print?guru_id=' . $guru['id'] . '&type=piket_siswa' : 'guru/laporan/absensi-shalat/print?type=piket_siswa') . '&from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId; ?>" target="_blank" class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded-md font-semibold transition flex items-center gap-1">
                <i class="fas fa-print"></i> Cetak Data Siswa
            </a>
        </div>

        <?php if (!empty($rekapPiketSiswa)): ?>
            <?php foreach ($rekapPiketSiswa as $date => $rows): ?>
                <div class="mb-4 last:mb-0 border border-gray-100 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-100 font-semibold text-xs text-gray-700 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-blue-600"></i>
                        <?= date('l, d F Y', strtotime($date)); ?>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/50 text-gray-500 uppercase text-[11px] font-semibold">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">No</th>
                                    <th class="px-4 py-2.5 text-left">NIS</th>
                                    <th class="px-4 py-2.5 text-left">Nama Siswa</th>
                                    <th class="px-4 py-2.5 text-left">Kelas</th>
                                    <th class="px-4 py-2.5 text-center">Waktu Absen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($rows as $i => $row): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 text-gray-500"><?= $i + 1; ?></td>
                                        <td class="px-4 py-3 text-gray-700"><?= esc($row['nis'] ?: '-'); ?></td>
                                        <td class="px-4 py-3 font-medium text-gray-900"><?= esc($row['nama_lengkap']); ?></td>
                                        <td class="px-4 py-3 text-gray-700">
                                            <span class="px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-700 rounded">
                                                <?= esc($row['unit'] ?: '-'); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 font-mono"><?= date('H:i:s', strtotime($row['waktu_absen'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-10">
                <i class="fas fa-user-slash text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500 text-sm">Belum ada siswa yang hadir pada sesi piket Anda untuk periode ini.</p>
            </div>
        <?php endif; ?>
    </div>

<?php elseif ($tab === 'piket_guru'): ?>
    <!-- TAB 3: Piket Guru Lain -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-purple-600"></i>
                Rekapan Kehadiran Guru Lain (Saat Piket Shalat)
            </h2>
            <a href="<?= base_url(!empty($isQuickTest) ? 'admin/laporan/absensi-shalat/preview-guru-print?guru_id=' . $guru['id'] . '&type=piket_guru' : 'guru/laporan/absensi-shalat/print?type=piket_guru') . '&from=' . $from . '&to=' . $to; ?>" target="_blank" class="text-xs bg-purple-50 text-purple-700 hover:bg-purple-100 px-3 py-1.5 rounded-md font-semibold transition flex items-center gap-1">
                <i class="fas fa-print"></i> Cetak Data Guru
            </a>
        </div>

        <?php if (!empty($rekapPiketGuru)): ?>
            <?php foreach ($rekapPiketGuru as $date => $rows): ?>
                <div class="mb-4 last:mb-0 border border-gray-100 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-100 font-semibold text-xs text-gray-700 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-purple-600"></i>
                        <?= date('l, d F Y', strtotime($date)); ?>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/50 text-gray-500 uppercase text-[11px] font-semibold">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">No</th>
                                    <th class="px-4 py-2.5 text-left">NIP</th>
                                    <th class="px-4 py-2.5 text-left">Nama Guru</th>
                                    <th class="px-4 py-2.5 text-center">Waktu Absen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($rows as $i => $row): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 text-gray-500"><?= $i + 1; ?></td>
                                        <td class="px-4 py-3 text-gray-700"><?= esc($row['identifier'] ?: '-'); ?></td>
                                        <td class="px-4 py-3 font-medium text-gray-900"><?= esc($row['nama_lengkap']); ?></td>
                                        <td class="px-4 py-3 text-center text-gray-600 font-mono"><?= date('H:i:s', strtotime($row['waktu_absen'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-10">
                <i class="fas fa-user-slash text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500 text-sm">Belum ada guru lain yang hadir pada sesi piket Anda untuk periode ini.</p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
