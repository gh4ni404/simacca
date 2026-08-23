<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
        <span class="text-sm font-medium"><?= session()->getFlashdata('success'); ?></span>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
        <span class="text-sm font-medium"><?= session()->getFlashdata('error'); ?></span>
    </div>
<?php endif; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-mosque text-green-600"></i>Laporan Absensi Shalat
        </h1>
        <p class="text-gray-600 text-sm mt-1">Rekapitulasi absensi shalat sekolah per periode</p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-2">
        <a href="<?= base_url('admin/laporan/absensi-shalat/print?from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&type=semua'); ?>" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm flex items-center gap-2 transition">
            <i class="fas fa-print"></i> Cetak Laporan Lengkap
        </a>
    </div>
</div>

<!-- ⚡ QUICK TEST & SIMULATION PANEL -->
<div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200/80 p-5 mb-6 shadow-sm">
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-amber-200/60">
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg uppercase tracking-wider shadow-xs">
                <i class="fas fa-bolt"></i> Quick Test Mode
            </span>
            <h2 class="text-sm font-bold text-amber-900">Pengujian & Simulasi Cepat Portal Guru & Cetak</h2>
        </div>
        <form method="post" action="<?= base_url('admin/laporan/absensi-shalat/generate-test-data'); ?>" onsubmit="return confirm('Buat data absensi simulasi untuk hari ini?');">
            <?= csrf_field(); ?>
            <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shadow-xs transition flex items-center gap-1.5">
                <i class="fas fa-magic"></i> Generate Data Simulasi Hari Ini
            </button>
        </form>
    </div>

    <p class="text-xs text-amber-800 mb-4">
        Uji dan lihat langsung tampilan Portal Guru serta cetak laporan tanpa perlu keluar atau login ulang dengan akun guru.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-6">
            <label class="block text-xs font-semibold text-amber-900 mb-1">Simulasikan Sebagai Guru:</label>
            <select id="quickTestGuruId" class="w-full border border-amber-300 rounded-lg px-3 py-2 text-sm bg-white text-gray-800 focus:ring-2 focus:ring-amber-500">
                <?php if (!empty($guruList)): ?>
                    <?php foreach ($guruList as $g): ?>
                        <option value="<?= $g['id']; ?>">
                            <?= esc($g['nama_lengkap']); ?> <?= $g['nip'] ? ' (NIP: ' . esc($g['nip']) . ')' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">Tidak ada data guru</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="md:col-span-6 flex flex-wrap gap-2">
            <button type="button" onclick="runQuickTest('preview')" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs transition flex items-center gap-1.5">
                <i class="fas fa-eye"></i> Pratinjau Portal Guru
            </button>
            <button type="button" onclick="runQuickTest('print_personal')" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg shadow-xs transition flex items-center gap-1.5">
                <i class="fas fa-print"></i> Cetak Presensi Guru
            </button>
            <button type="button" onclick="runQuickTest('print_piket')" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition flex items-center gap-1.5">
                <i class="fas fa-file-pdf"></i> Cetak Piket Guru
            </button>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4" method="get" action="<?= base_url('admin/laporan/absensi-shalat'); ?>">
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
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Kelas</label>
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Sesi Shalat</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalSessions; ?></p>
            </div>
            <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Siswa Hadir</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalSiswaHadir; ?></p>
            </div>
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Guru Hadir</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalGuruHadir; ?></p>
            </div>
            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Periode</p>
                <p class="text-sm font-bold text-gray-800"><?= date('d/m/Y', strtotime($from)); ?> - <?= date('d/m/Y', strtotime($to)); ?></p>
            </div>
            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
    </div>
</div>

<!-- Nav Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-6 overflow-x-auto">
        <a href="<?= base_url('admin/laporan/absensi-shalat?from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&tab=siswa'); ?>"
           class="<?= ($tab === 'siswa' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?> whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
            <i class="fas fa-user-graduate text-blue-600"></i>
            Rekapan Siswa
            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 font-semibold"><?= $totalSiswaHadir; ?></span>
        </a>
        <a href="<?= base_url('admin/laporan/absensi-shalat?from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&tab=guru'); ?>"
           class="<?= ($tab === 'guru' ? 'border-purple-600 text-purple-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?> whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
            <i class="fas fa-chalkboard-teacher text-purple-600"></i>
            Rekapan Guru
            <span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700 font-semibold"><?= $totalGuruHadir; ?></span>
        </a>
        <a href="<?= base_url('admin/laporan/absensi-shalat?from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&tab=harian'); ?>"
           class="<?= ($tab === 'harian' ? 'border-orange-600 text-orange-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?> whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
            <i class="fas fa-calendar-day text-orange-600"></i>
            Log Sesi Harian
            <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700 font-semibold"><?= count($rekapHarian); ?> Hari</span>
        </a>
    </nav>
</div>

<!-- Tab Contents -->

<?php if ($tab === 'siswa'): ?>
    <!-- TAB 1: Rekap Siswa -->
    <!-- Ringkasan Per Kelas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-school text-indigo-500"></i>
                Ringkasan Siswa Per Kelas
            </h2>
            <a href="<?= base_url('admin/laporan/absensi-shalat/print?from=' . $from . '&to=' . $to . '&kelas_id=' . $kelasId . '&type=siswa'); ?>" target="_blank" class="text-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded-md font-semibold transition flex items-center gap-1">
                <i class="fas fa-print"></i> Cetak Laporan Siswa
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] font-semibold">
                    <tr>
                        <th class="px-6 py-3 text-left">Nama Kelas</th>
                        <th class="px-6 py-3 text-center">Siswa Hadir</th>
                        <th class="px-6 py-3 text-center">Total Sesi Diikuti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php if (!empty($rekapKelas)): ?>
                        <?php foreach ($rekapKelas as $row): ?>
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3.5 font-medium text-gray-900"><?= esc($row['nama_kelas']); ?></td>
                                <td class="px-6 py-3.5 text-center text-emerald-700 font-semibold"><?= $row['total_siswa_hadir']; ?></td>
                                <td class="px-6 py-3.5 text-center text-gray-700"><?= $row['total_sesi']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>Belum ada data untuk periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Per Siswa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-user-graduate text-blue-500"></i>
            Detail Kehadiran Per Siswa
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] font-semibold">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">NIS</th>
                        <th class="px-6 py-3 text-left">Nama Siswa</th>
                        <th class="px-6 py-3 text-left">Kelas</th>
                        <th class="px-6 py-3 text-center">Kehadiran</th>
                        <th class="px-6 py-3 text-center">Waktu Pertama</th>
                        <th class="px-6 py-3 text-center">Waktu Terakhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php if (!empty($rekapSiswa)): ?>
                        <?php foreach ($rekapSiswa as $i => $row): ?>
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3.5 text-gray-500"><?= $i + 1; ?></td>
                                <td class="px-6 py-3.5 text-gray-700"><?= esc($row['nis']); ?></td>
                                <td class="px-6 py-3.5 font-medium text-gray-900"><?= esc($row['nama_lengkap']); ?></td>
                                <td class="px-6 py-3.5 text-gray-700"><?= esc($row['nama_kelas']); ?></td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <?= $row['total_hadir']; ?> kali
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center text-gray-500 font-mono text-xs">
                                    <?= $row['waktu_pertama'] ? date('d/m H:i', strtotime($row['waktu_pertama'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-3.5 text-center text-gray-500 font-mono text-xs">
                                    <?= $row['waktu_terakhir'] ? date('d/m H:i', strtotime($row['waktu_terakhir'])) : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>Belum ada data presensi siswa untuk periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($tab === 'guru'): ?>
    <!-- TAB 2: Detail Per Guru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-purple-600"></i>
                Detail Presensi Guru Shalat
            </h2>
            <a href="<?= base_url('admin/laporan/absensi-shalat/print?from=' . $from . '&to=' . $to . '&type=guru'); ?>" target="_blank" class="text-xs bg-purple-50 text-purple-700 hover:bg-purple-100 px-3 py-1.5 rounded-md font-semibold transition flex items-center gap-1">
                <i class="fas fa-print"></i> Cetak Laporan Guru
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] font-semibold">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">NIP</th>
                        <th class="px-6 py-3 text-left">Nama Guru</th>
                        <th class="px-6 py-3 text-center">Kehadiran</th>
                        <th class="px-6 py-3 text-center">Waktu Pertama</th>
                        <th class="px-6 py-3 text-center">Waktu Terakhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php if (!empty($rekapGuru)): ?>
                        <?php foreach ($rekapGuru as $i => $row): ?>
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3.5 text-gray-500"><?= $i + 1; ?></td>
                                <td class="px-6 py-3.5 text-gray-700"><?= esc($row['nip'] ?: '-'); ?></td>
                                <td class="px-6 py-3.5 font-medium text-gray-900"><?= esc($row['nama_lengkap']); ?></td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        <?= $row['total_hadir']; ?> kali
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center text-gray-500 font-mono text-xs">
                                    <?= $row['waktu_pertama'] ? date('d/m H:i', strtotime($row['waktu_pertama'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-3.5 text-center text-gray-500 font-mono text-xs">
                                    <?= $row['waktu_terakhir'] ? date('d/m H:i', strtotime($row['waktu_terakhir'])) : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>Belum ada data presensi guru untuk periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($tab === 'harian'): ?>
    <!-- TAB 3: Rekap Harian -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-calendar-day text-orange-500"></i>
            Log Sesi Shalat Harian
        </h2>
        <?php if (!empty($rekapHarian)): ?>
            <?php foreach ($rekapHarian as $date => $sessions): ?>
                <div class="mb-5 last:mb-0 border border-gray-100 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 font-semibold text-xs text-gray-700 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-orange-500"></i>
                        <?= date('l, d F Y', strtotime($date)); ?>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/50 text-gray-500 uppercase text-[11px] font-semibold">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">Waktu Sesi</th>
                                    <th class="px-4 py-2.5 text-left">Guru Piket</th>
                                    <th class="px-4 py-2.5 text-center">Detail Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <?php foreach ($sessions as $sess): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 text-gray-700 font-medium font-mono text-xs">
                                            <?= date('H:i', strtotime($sess['created_at'])); ?> WITA
                                        </td>
                                        <td class="px-4 py-3 text-gray-800 font-medium">
                                            <?= esc($sess['nama_guru']); ?>
                                        </td>
                                        <td class="px-4 py-3 text-center flex justify-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                Siswa: <?= $sess['jumlah_siswa'] ?? 0; ?>
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                Guru: <?= $sess['jumlah_guru'] ?? 0; ?>
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                Total: <?= $sess['jumlah_hadir']; ?>
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
                <p class="text-gray-500 text-sm">Belum ada sesi shalat harian untuk periode ini.</p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
function runQuickTest(action) {
    const guruId = document.getElementById('quickTestGuruId').value;
    if (!guruId) {
        alert('Silakan pilih guru terlebih dahulu');
        return;
    }

    const from = "<?= esc($from); ?>";
    const to = "<?= esc($to); ?>";
    const kelasId = "<?= esc($kelasId); ?>";

    let url = "";
    if (action === 'preview') {
        url = "<?= base_url('admin/laporan/absensi-shalat/preview-guru'); ?>?guru_id=" + guruId + "&from=" + from + "&to=" + to + "&kelas_id=" + kelasId;
    } else if (action === 'print_personal') {
        url = "<?= base_url('admin/laporan/absensi-shalat/preview-guru-print'); ?>?guru_id=" + guruId + "&from=" + from + "&to=" + to + "&type=personal";
    } else if (action === 'print_piket') {
        url = "<?= base_url('admin/laporan/absensi-shalat/preview-guru-print'); ?>?guru_id=" + guruId + "&from=" + from + "&to=" + to + "&kelas_id=" + kelasId + "&type=piket_semua";
    }

    window.open(url, '_blank');
}
</script>

<?= $this->endSection() ?>
