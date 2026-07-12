<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="w-full space-y-5">

    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-600 to-teal-800 rounded-2xl shadow-lg p-4 md:p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 md:w-14 md:h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-archive text-xl md:text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold"><?= $pageTitle ?></h1>
                    <p class="text-teal-200 text-xs md:text-sm mt-0.5"><?= $pageDescription ?></p>
                </div>
            </div>
            <!-- Quick Stats in Header -->
            <div class="flex items-center gap-4 sm:gap-5 text-sm">
                <div class="text-center">
                    <div class="text-xl font-bold"><?= $stats['total_entries'] ?></div>
                    <div class="text-teal-200 text-[10px]">Entri</div>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="text-center">
                    <div class="text-xl font-bold"><?= $stats['total_siswa'] ?></div>
                    <div class="text-teal-200 text-[10px]">Siswa</div>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="text-center">
                    <?php $approvalRate = $stats['total_entries'] > 0 ? round(($stats['total_disetujui'] / $stats['total_entries']) * 100) : 0; ?>
                    <div class="text-xl font-bold"><?= $approvalRate ?>%</div>
                    <div class="text-teal-200 text-[10px]">Disetujui</div>
                </div>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <!-- Filter + Stats Row -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-4">
        <!-- Filter (3 cols) -->
        <div class="xl:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form method="get" action="<?= base_url('admin/jurnal-pkl-archive') ?>">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-sliders-h text-teal-600 text-xs"></i>
                        <span class="text-sm font-semibold text-gray-700">Filter</span>
                        <?php
                        $activeFilters = array_filter($filters, function($v) { return $v !== null && $v !== ''; });
                        if (count($activeFilters) > 0): ?>
                            <span class="inline-flex items-center justify-center w-5 h-5 bg-teal-100 text-teal-700 rounded-full text-[10px] font-bold"><?= count($activeFilters) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($activeFilters)): ?>
                        <a href="<?= base_url('admin/jurnal-pkl-archive') ?>" class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fas fa-times mr-0.5"></i> Reset
                        </a>
                    <?php endif; ?>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-day text-[10px]"></i>
                            </div>
                            <input type="date" name="start_date" value="<?= esc($filters['start_date'] ?? '') ?>"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-gray-50 focus:bg-white transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-1">Tanggal Akhir</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-check text-[10px]"></i>
                            </div>
                            <input type="date" name="end_date" value="<?= esc($filters['end_date'] ?? '') ?>"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-gray-50 focus:bg-white transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-1">Siswa</label>
                        <select name="siswa_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-gray-50 focus:bg-white transition-colors">
                            <option value="">Semua Siswa</option>
                            <?php foreach ($siswaList as $siswa): ?>
                                <option value="<?= $siswa['id'] ?>" <?= ($filters['siswa_id'] == $siswa['id']) ? 'selected' : '' ?>><?= esc($siswa['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-1">Status</label>
                        <div class="flex gap-2">
                            <select name="status" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-gray-50 focus:bg-white transition-colors">
                                <option value="">Semua</option>
                                <option value="disetujui" <?= ($filters['status'] === 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                                <option value="pending" <?= ($filters['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="revisi" <?= ($filters['status'] === 'revisi') ? 'selected' : '' ?>>Revisi</option>
                                <option value="ditolak" <?= ($filters['status'] === 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors text-sm font-medium shadow-sm flex-shrink-0">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Status Breakdown (1 col) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-center">
            <div class="text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-3">Status Entri</div>
            <?php if ($stats['total_entries'] > 0): ?>
            <div class="flex gap-1 h-3 rounded-full overflow-hidden mb-3">
                <?php
                $total = $stats['total_entries'];
                $pcts = [
                    'disetujui' => ($stats['total_disetujui'] / $total) * 100,
                    'pending' => ($stats['total_pending'] / $total) * 100,
                    'revisi' => (($stats['total_revisi'] + $stats['total_tinjau_ulang']) / $total) * 100,
                    'ditolak' => ($stats['total_ditolak'] / $total) * 100,
                ];
                $colors = ['disetujui' => 'bg-green-500', 'pending' => 'bg-yellow-400', 'revisi' => 'bg-orange-400', 'ditolak' => 'bg-red-500'];
                foreach ($pcts as $k => $v):
                    if ($v > 0): ?>
                        <div class="<?= $colors[$k] ?> rounded-full transition-all" style="width: <?= round($v) ?>%" title="<?= ucfirst($k) ?>: <?= round($v, 1) ?>%"></div>
                    <?php endif;
                endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span>Setuju</span>
                    <span class="font-semibold text-gray-700"><?= $stats['total_disetujui'] ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-yellow-400"></span>Pending</span>
                    <span class="font-semibold text-gray-700"><?= $stats['total_pending'] ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-400"></span>Revisi</span>
                    <span class="font-semibold text-gray-700"><?= $stats['total_revisi'] + $stats['total_tinjau_ulang'] ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span>Ditolak</span>
                    <span class="font-semibold text-gray-700"><?= $stats['total_ditolak'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <?php
    $tabs = [
        ['id' => 'siswa', 'label' => 'Per Siswa', 'icon' => 'fas fa-users', 'count' => count($summary)],
        ['id' => 'minggu', 'label' => 'Per Minggu', 'icon' => 'fas fa-calendar-week', 'count' => count($weeklyData)],
        ['id' => 'tempat', 'label' => 'Per Tempat PKL', 'icon' => 'fas fa-building', 'count' => count($byTempatPkl)],
        ['id' => 'pembimbing', 'label' => 'Per Pembimbing', 'icon' => 'fas fa-chalkboard-teacher', 'count' => count($byPembimbing)],
        ['id' => 'kelas', 'label' => 'Per Kelas', 'icon' => 'fas fa-school', 'count' => count($byKelas)],
    ];
    ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Tab Header -->
        <div class="border-b border-gray-100 bg-gray-50/50">
            <div class="flex overflow-x-auto scrollbar-hide">
                <?php foreach ($tabs as $i => $tab): ?>
                <button type="button" data-tab="<?= $tab['id'] ?>"
                   class="archive-tab relative flex items-center gap-2 px-4 md:px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors <?= $i === 0 ? 'text-teal-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100/50' ?>">
                    <i class="<?= $tab['icon'] ?> text-xs"></i>
                    <span class="hidden sm:inline"><?= $tab['label'] ?></span>
                    <span class="tab-badge inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold <?= $i === 0 ? 'bg-teal-100 text-teal-700' : 'bg-gray-200 text-gray-500' ?>">
                        <?= $tab['count'] ?>
                    </span>
                    <span class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 bg-teal-600 rounded-t-full <?= $i === 0 ? '' : 'hidden' ?>"></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-0">

            <!-- Tab: Per Siswa -->
            <div id="tab-siswa" class="archive-tab-content">
            <?php if (empty($summary)): ?>
                <div class="p-10 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-500 font-medium">Tidak ada data ditemukan</p>
                    <p class="text-xs text-gray-400 mt-1">Coba ubah filter pencarian</p>
                </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Siswa</th>
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Kelas</th>
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Perusahaan</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-green-500 uppercase tracking-wider">Setuju</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-yellow-500 uppercase tracking-wider">Pending</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-orange-500 uppercase tracking-wider hidden sm:table-cell">Revisi</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-red-500 uppercase tracking-wider hidden sm:table-cell">Ditolak</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider min-w-[120px]">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($summary as $row): ?>
                        <?php
                            $rate = $row['total_entry'] > 0 ? round(($row['disetujui'] / $row['total_entry']) * 100) : 0;
                            $barColor = $rate >= 80 ? 'bg-green-500' : ($rate >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                        ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-teal-700"><?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-800 text-sm truncate"><?= esc($row['nama_lengkap']) ?></div>
                                        <div class="text-[10px] text-gray-400">NIS: <?= esc($row['nis'] ?? '-') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-600 hidden md:table-cell"><?= esc($row['nama_kelas'] ?? '-') ?></td>
                            <td class="py-3 px-4 text-gray-500 text-xs hidden lg:table-cell max-w-[140px] truncate" title="<?= esc($row['nama_perusahaan'] ?? '-') ?>"><?= esc($row['nama_perusahaan'] ?? '-') ?></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold"><?= $row['total_entry'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold"><?= $row['disetujui'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><?= $row['pending'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs font-bold"><?= $row['revisi'] + $row['tinjau_ulang'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold"><?= $row['ditolak'] ?></span></td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full <?= $barColor ?> rounded-full transition-all" style="width: <?= $rate ?>%"></div>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500 w-7 text-right"><?= $rate ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            </div>

            <!-- Tab: Per Minggu -->
            <div id="tab-minggu" class="archive-tab-content hidden">
            <?php if (empty($weeklyData)): ?>
                <div class="p-10 text-center">
                    <i class="fas fa-calendar-week text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-500 font-medium">Tidak ada data mingguan</p>
                </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Minggu</th>
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Rentang Tanggal</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Siswa</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Entri</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-green-500 uppercase tracking-wider">Setuju</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-yellow-500 uppercase tracking-wider">Pending</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-orange-500 uppercase tracking-wider hidden sm:table-cell">Revisi</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-red-500 uppercase tracking-wider hidden sm:table-cell">Ditolak</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider min-w-[100px]">Tingkat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($weeklyData as $week): ?>
                        <?php $rate = $week['total_entry'] > 0 ? round(($week['disetujui'] / $week['total_entry']) * 100) : 0; ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold"><?= $week['minggu_ke'] ?></span>
                                    <span class="font-medium text-gray-700 text-sm">Minggu <?= $week['minggu_ke'] ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-500 hidden sm:table-cell whitespace-nowrap">
                                <?= date('d M', strtotime($week['tanggal_mulai'])) ?> — <?= date('d M Y', strtotime($week['tanggal_selesai'])) ?>
                            </td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><?= $week['total_siswa'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold"><?= $week['total_entry'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold"><?= $week['disetujui'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><?= $week['pending'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs font-bold"><?= $week['revisi'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold"><?= $week['ditolak'] ?></span></td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full <?= $rate >= 80 ? 'bg-green-500' : ($rate >= 50 ? 'bg-yellow-500' : 'bg-red-500') ?> rounded-full" style="width: <?= $rate ?>%"></div>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500 w-7 text-right"><?= $rate ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            </div>

            <!-- Tab: Per Tempat PKL -->
            <div id="tab-tempat" class="archive-tab-content hidden">
            <?php if (empty($byTempatPkl)): ?>
                <div class="p-10 text-center">
                    <i class="fas fa-building text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-500 font-medium">Tidak ada data tempat PKL</p>
                </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Perusahaan</th>
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Kota</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Siswa</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Entri</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-green-500 uppercase tracking-wider">Setuju</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-yellow-500 uppercase tracking-wider">Pending</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-orange-500 uppercase tracking-wider hidden sm:table-cell">Revisi</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-red-500 uppercase tracking-wider hidden sm:table-cell">Ditolak</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider min-w-[100px]">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($byTempatPkl as $row): ?>
                        <?php $rate = $row['total_entry'] > 0 ? round(($row['disetujui'] / $row['total_entry']) * 100) : 0; ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-building text-rose-500 text-xs"></i>
                                    </div>
                                    <span class="font-medium text-gray-800 text-sm truncate max-w-[200px]"><?= esc($row['nama_perusahaan']) ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-500 text-xs hidden md:table-cell"><?= esc($row['kota'] ?? '-') ?></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><?= $row['total_siswa'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold"><?= $row['total_entry'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold"><?= $row['disetujui'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><?= $row['pending'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs font-bold"><?= $row['revisi'] + $row['tinjau_ulang'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold"><?= $row['ditolak'] ?></span></td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full <?= $rate >= 80 ? 'bg-green-500' : ($rate >= 50 ? 'bg-yellow-500' : 'bg-red-500') ?> rounded-full" style="width: <?= $rate ?>%"></div>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500 w-7 text-right"><?= $rate ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            </div>

            <!-- Tab: Per Pembimbing -->
            <div id="tab-pembimbing" class="archive-tab-content hidden">
            <?php if (empty($byPembimbing)): ?>
                <div class="p-10 text-center">
                    <i class="fas fa-chalkboard-teacher text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-500 font-medium">Tidak ada data pembimbing</p>
                </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Pembimbing</th>
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">NIP</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Siswa</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Entri</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-green-500 uppercase tracking-wider">Setuju</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-yellow-500 uppercase tracking-wider">Pending</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-orange-500 uppercase tracking-wider hidden sm:table-cell">Revisi</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-red-500 uppercase tracking-wider hidden sm:table-cell">Ditolak</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider min-w-[100px]">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($byPembimbing as $row): ?>
                        <?php $rate = $row['total_entry'] > 0 ? round(($row['disetujui'] / $row['total_entry']) * 100) : 0; ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-violet-700"><?= strtoupper(substr($row['nama_pembimbing'], 0, 1)) ?></span>
                                    </div>
                                    <span class="font-medium text-gray-800 text-sm truncate"><?= esc($row['nama_pembimbing']) ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-500 text-xs hidden md:table-cell"><?= esc($row['nip'] ?? '-') ?></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><?= $row['total_siswa'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold"><?= $row['total_entry'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold"><?= $row['disetujui'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><?= $row['pending'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs font-bold"><?= $row['revisi'] + $row['tinjau_ulang'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold"><?= $row['ditolak'] ?></span></td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full <?= $rate >= 80 ? 'bg-green-500' : ($rate >= 50 ? 'bg-yellow-500' : 'bg-red-500') ?> rounded-full" style="width: <?= $rate ?>%"></div>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500 w-7 text-right"><?= $rate ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            </div>

            <!-- Tab: Per Kelas -->
            <div id="tab-kelas" class="archive-tab-content hidden">
            <?php if (empty($byKelas)): ?>
                <div class="p-10 text-center">
                    <i class="fas fa-school text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-500 font-medium">Tidak ada data kelas</p>
                </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Kelas</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Siswa</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Entri</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-green-500 uppercase tracking-wider">Setuju</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-yellow-500 uppercase tracking-wider">Pending</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-orange-500 uppercase tracking-wider hidden sm:table-cell">Revisi</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-red-500 uppercase tracking-wider hidden sm:table-cell">Ditolak</th>
                            <th class="text-center py-3 px-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider min-w-[100px]">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($byKelas as $row): ?>
                        <?php $rate = $row['total_entry'] > 0 ? round(($row['disetujui'] / $row['total_entry']) * 100) : 0; ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-graduation-cap text-amber-600 text-xs"></i>
                                    </div>
                                    <span class="font-medium text-gray-800 text-sm"><?= esc($row['nama_kelas']) ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><?= $row['total_siswa'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold"><?= $row['total_entry'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold"><?= $row['disetujui'] ?></span></td>
                            <td class="py-3 px-4 text-center"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><?= $row['pending'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs font-bold"><?= $row['revisi'] + $row['tinjau_ulang'] ?></span></td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell"><span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold"><?= $row['ditolak'] ?></span></td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full <?= $rate >= 80 ? 'bg-green-500' : ($rate >= 50 ? 'bg-yellow-500' : 'bg-red-500') ?> rounded-full" style="width: <?= $rate ?>%"></div>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500 w-7 text-right"><?= $rate ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            </div>

        </div>
    </div>

<script>
document.querySelectorAll('.archive-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        const tabId = btn.dataset.tab;

        document.querySelectorAll('.archive-tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('tab-' + tabId).classList.remove('hidden');

        document.querySelectorAll('.archive-tab').forEach(b => {
            b.classList.remove('text-teal-700');
            b.classList.add('text-gray-500');
            b.querySelector('.tab-indicator').classList.add('hidden');
            b.querySelector('.tab-badge').classList.remove('bg-teal-100', 'text-teal-700');
            b.querySelector('.tab-badge').classList.add('bg-gray-200', 'text-gray-500');
        });

        btn.classList.remove('text-gray-500');
        btn.classList.add('text-teal-700');
        btn.querySelector('.tab-indicator').classList.remove('hidden');
        btn.querySelector('.tab-badge').classList.remove('bg-gray-200', 'text-gray-500');
        btn.querySelector('.tab-badge').classList.add('bg-teal-100', 'text-teal-700');
    });
});
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
<?= $this->endSection() ?>
