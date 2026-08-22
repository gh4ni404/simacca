<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">
        <i class="fas fa-mosque text-green-600 mr-2"></i>Laporan Absensi Shalat
    </h1>
    <p class="text-gray-600">Rekapitulasi absensi shalat per periode</p>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4" method="get" action="<?= base_url('admin/laporan/absensi-shalat'); ?>">
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
        <div class="flex items-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-filter mr-1"></i>Terapkan
            </button>
        </div>
    </form>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Sesi Shalat</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalSessions; ?></p>
            </div>
            <i class="fas fa-clock text-green-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Siswa Hadir</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalSiswaHadir; ?></p>
            </div>
            <i class="fas fa-user-graduate text-blue-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Guru Hadir</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalGuruHadir ?? 0; ?></p>
            </div>
            <i class="fas fa-chalkboard-teacher text-purple-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Periode</p>
                <p class="text-sm font-bold text-gray-800"><?= date('d/m/Y', strtotime($from)); ?> - <?= date('d/m/Y', strtotime($to)); ?></p>
            </div>
            <i class="fas fa-calendar text-orange-500 text-2xl"></i>
        </div>
    </div>
</div>

<!-- Ringkasan Per Kelas -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-school text-indigo-500 mr-2"></i>Ringkasan Siswa Per Kelas
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Siswa Hadir</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Sesi Diikuti</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($rekapKelas)): ?>
                    <?php foreach ($rekapKelas as $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($row['nama_kelas']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-700 font-semibold"><?= $row['total_siswa_hadir']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700"><?= $row['total_sesi']; ?></td>
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

<!-- Detail Per Guru -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-chalkboard-teacher text-purple-600 mr-2"></i>Detail Presensi Guru Shalat
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Guru</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kehadiran</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Waktu Pertama</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Waktu Terakhir</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($rekapGuru)): ?>
                    <?php foreach ($rekapGuru as $i => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $i + 1; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= esc($row['nip'] ?: '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($row['nama_lengkap']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    <?= $row['total_hadir']; ?> kali
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                <?= $row['waktu_pertama'] ? date('d/m H:i', strtotime($row['waktu_pertama'])) : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
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

<!-- Detail Per Siswa -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-user-graduate text-blue-500 mr-2"></i>Detail Per Siswa
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kehadiran</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Waktu Pertama</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Waktu Terakhir</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($rekapSiswa)): ?>
                    <?php foreach ($rekapSiswa as $i => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $i + 1; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= esc($row['nis']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($row['nama_lengkap']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= esc($row['nama_kelas']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <?= $row['total_hadir']; ?> kali
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                <?= $row['waktu_pertama'] ? date('d/m H:i', strtotime($row['waktu_pertama'])) : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                <?= $row['waktu_terakhir'] ? date('d/m H:i', strtotime($row['waktu_terakhir'])) : '-'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>Belum ada data untuk periode ini.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Rekap Harian -->
<?php if (!empty($rekapHarian)): ?>
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-calendar-day text-orange-500 mr-2"></i>Rekap Harian
    </h2>
    <?php foreach ($rekapHarian as $date => $sessions): ?>
        <div class="mb-4 last:mb-0">
            <h3 class="text-sm font-semibold text-gray-600 mb-2">
                <i class="fas fa-chevron-right text-xs mr-1"></i>
                <?= date('l, d F Y', strtotime($date)); ?>
            </h3>
            <div class="overflow-x-auto ml-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Waktu Sesi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Guru Piket</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Detail Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($sessions as $sess): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                    <?= date('H:i', strtotime($sess['created_at'])); ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                    <?= esc($sess['nama_guru']); ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center flex justify-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        Siswa: <?= $sess['jumlah_siswa'] ?? 0; ?>
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        Guru: <?= $sess['jumlah_guru'] ?? 0; ?>
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
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
</div>
<?php endif; ?>

<?= $this->endSection() ?>
