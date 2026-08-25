<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-teal-700 via-emerald-800 to-indigo-900 rounded-2xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider text-teal-100 flex items-center gap-1.5">
                        <i class="fas fa-shield-alt text-teal-200"></i> Master Data
                    </span>
                    <span class="px-3 py-1 bg-emerald-500/30 border border-emerald-300/40 backdrop-blur-md rounded-full text-xs font-bold text-emerald-100 flex items-center gap-1.5">
                        <i class="fas fa-check-circle text-emerald-300"></i> TA Aktif Sistem: <?= esc($activeTahunAjaran) ?>
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Data Guru Wali</h1>
                <p class="text-teal-100 text-sm mt-1 max-w-2xl">
                    Kelola dan petakan pembagian individu siswa ke Guru Wali (Pembimbing Personal hingga tamat sekolah).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="openPrintJurnalFilterModal()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-print"></i> Cetak Jurnal Bimbingan
                </button>
                <a href="<?= base_url('admin/guru-wali/print?tahun_ajaran=' . urlencode($tahunAjaran)) ?>" target="_blank" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-semibold text-xs rounded-xl transition-all border border-white/20 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-alt"></i> Cetak SK / Rekap
                </a>
                <a href="<?= base_url('admin/guru-wali/export?tahun_ajaran=' . urlencode($tahunAjaran)) ?>" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-semibold text-xs rounded-xl transition-all border border-white/20 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-excel"></i> Export CSV
                </a>
                <?php if (session()->get('role') === 'admin'): ?>
                <button type="button" onclick="document.getElementById('autoDistributeModal').classList.remove('hidden')" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs rounded-xl transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-magic"></i> Pembagian Otomatis
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-400">Total Siswa Aktif</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1"><?= number_format($stats['total_siswa'] ?? 0) ?></h3>
                <p class="text-xs text-gray-500 mt-1">TA <?= esc($tahunAjaran) ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-emerald-600">Sudah Memiliki Wali</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1"><?= number_format($stats['total_assigned'] ?? 0) ?></h3>
                <p class="text-xs text-emerald-600 mt-1 font-bold"><?= $stats['percentage_assigned'] ?? 0 ?>% Terpetakan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-amber-600">Belum Ada Wali</p>
                <h3 class="text-2xl font-black <?= ($stats['total_unassigned'] ?? 0) > 0 ? 'text-amber-600' : 'text-gray-800' ?> mt-1">
                    <?= number_format($stats['total_unassigned'] ?? 0) ?>
                </h3>
                <p class="text-xs text-amber-600 mt-1 font-medium"><?= ($stats['total_unassigned'] ?? 0) > 0 ? 'Perlu Penugasan' : 'Semua Terpetakan' ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl <?= ($stats['total_unassigned'] ?? 0) > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-400' ?> flex items-center justify-center text-xl font-bold">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-indigo-600">Guru Bertugas</p>
                <h3 class="text-2xl font-black text-indigo-700 mt-1"><?= number_format($stats['total_guru_wali'] ?? 0) ?> <span class="text-sm font-normal text-gray-500">/ <?= $stats['total_guru_available'] ?? 0 ?> Guru</span></h3>
                <p class="text-xs text-indigo-600 mt-1 font-medium">Rata-rata: ~<?= $stats['avg_siswa_per_guru'] ?? 0 ?> siswa/guru</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 bg-white rounded-t-xl px-4 pt-2 shadow-sm">
        <nav class="flex space-x-6">
            <a href="?tab=pemetaan&tahun_ajaran=<?= urlencode($tahunAjaran) ?>" class="py-3.5 px-1 border-b-2 font-bold text-sm flex items-center gap-2 <?= $tab === 'pemetaan' ? 'border-teal-600 text-teal-700' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                <i class="fas fa-th-list"></i> Pemetaan Siswa & Guru Wali
                <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-semibold <?= $tab === 'pemetaan' ? 'bg-teal-100 text-teal-800' : 'bg-gray-100 text-gray-600' ?>">
                    <?= count($siswaList) ?>
                </span>
            </a>
            <a href="?tab=guru&tahun_ajaran=<?= urlencode($tahunAjaran) ?>" class="py-3.5 px-1 border-b-2 font-bold text-sm flex items-center gap-2 <?= $tab === 'guru' ? 'border-teal-600 text-teal-700' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                <i class="fas fa-users-cog"></i> Daftar Guru Wali & Beban Bimbingan
                <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-semibold <?= $tab === 'guru' ? 'bg-teal-100 text-teal-800' : 'bg-gray-100 text-gray-600' ?>">
                    <?= count($teacherList) ?>
                </span>
            </a>
        </nav>
    </div>

    <?php if ($tab === 'pemetaan'): ?>
    <!-- TAB 1: PEMETAAN SISWA -->
    <div class="space-y-4">
        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="get" action="" class="space-y-4">
                <input type="hidden" name="tab" value="pemetaan">

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tahun Ajaran</label>
                        <select name="tahun_ajaran" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 p-2.5 font-bold text-gray-800">
                            <?php foreach ($tahunAjaranList as $ta): ?>
                                <option value="<?= esc($ta) ?>" <?= $tahunAjaran === $ta ? 'selected' : '' ?>>
                                    <?= esc($ta) ?><?= $ta === $activeTahunAjaran ? ' (Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tingkat</label>
                        <select name="tingkat" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 p-2.5">
                            <option value="">Semua Tingkat</option>
                            <option value="10" <?= ($filters['tingkat'] ?? '') === '10' ? 'selected' : '' ?>>Kelas 10 (X)</option>
                            <option value="11" <?= ($filters['tingkat'] ?? '') === '11' ? 'selected' : '' ?>>Kelas 11 (XI)</option>
                            <option value="12" <?= ($filters['tingkat'] ?? '') === '12' ? 'selected' : '' ?>>Kelas 12 (XII)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Jurusan</label>
                        <select name="jurusan" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 p-2.5">
                            <option value="">Semua Jurusan</option>
                            <?php foreach ($jurusanList as $jurusan): ?>
                                <option value="<?= esc($jurusan) ?>" <?= ($filters['jurusan'] ?? '') === $jurusan ? 'selected' : '' ?>><?= esc($jurusan) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Kelas</label>
                        <select name="kelas_id" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 p-2.5">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= ($filters['kelas_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                                    <?= esc($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 p-2.5">
                            <option value="">Semua Status</option>
                            <option value="assigned" <?= ($filters['status'] ?? '') === 'assigned' ? 'selected' : '' ?>>Sudah Ada Guru Wali</option>
                            <option value="unassigned" <?= ($filters['status'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Belum Ada Guru Wali</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Guru Wali</label>
                        <select name="guru_id" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 p-2.5">
                            <option value="">Semua Guru Wali</option>
                            <?php foreach ($availableGuru as $g): ?>
                                <option value="<?= $g['id'] ?>" <?= ($filters['guru_id'] ?? '') == $g['id'] ? 'selected' : '' ?>><?= esc($g['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2 border-t border-gray-100">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="search" id="searchTableInput" onkeyup="filterSiswaTable(this.value)" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari Nama Siswa, NIS, Kelas, atau Guru..." class="w-full pl-10 pr-4 text-xs rounded-xl border border-gray-200 bg-gray-50 p-2.5">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl flex items-center gap-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="?tab=pemetaan&tahun_ajaran=<?= urlencode($tahunAjaran) ?>" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-xl">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bulk Action Form Wrapper -->
        <form id="bulkForm" action="<?= base_url('admin/guru-wali/bulk-assign') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun_ajaran" value="<?= esc($tahunAjaran) ?>">

            <!-- Bulk Toolbar -->
            <?php if (session()->get('role') === 'admin'): ?>
            <div id="bulkToolbar" class="hidden bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-4 mb-4">
                <div class="flex items-center space-x-3">
                    <span class="w-7 h-7 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs font-black" id="selectedBadge">0</span>
                    <span class="text-xs font-bold">Siswa Terpilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openBulkAssignModal()" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold rounded-xl shadow">
                        <i class="fas fa-user-plus mr-1"></i> Tugaskan ke Guru Wali...
                    </button>
                    <button type="button" onclick="submitBulkUnassign()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow">
                        <i class="fas fa-user-minus mr-1"></i> Lepas Penugasan
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Table Responsive -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs" id="siswaTable">
                        <thead class="bg-slate-50 text-gray-600 uppercase font-bold tracking-wider">
                            <tr>
                                <?php if (session()->get('role') === 'admin'): ?>
                                <th class="w-10 px-4 py-3.5 text-center">
                                    <input type="checkbox" id="masterCheckbox" onchange="toggleSelectAll(this.checked)" class="rounded border-gray-300 text-teal-600 cursor-pointer">
                                </th>
                                <?php endif; ?>
                                <th class="px-4 py-3.5 text-left">No</th>
                                <th class="px-4 py-3.5 text-left">Data Siswa</th>
                                <th class="px-4 py-3.5 text-left">Kelas / Rombel</th>
                                <th class="px-4 py-3.5 text-left">Guru Wali (Pembimbing Personal)</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <?php if (session()->get('role') === 'admin'): ?>
                                <th class="px-4 py-3.5 text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php if (empty($siswaList)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <i class="fas fa-folder-open text-4xl mb-3 block text-gray-300"></i>
                                        <p class="font-medium text-gray-600">Tidak ada data siswa yang cocok.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($siswaList as $idx => $s): ?>
                                    <tr class="siswa-row hover:bg-slate-50 transition-colors <?= empty($s['guru_id']) ? 'bg-amber-50/20' : '' ?>">
                                        <?php if (session()->get('role') === 'admin'): ?>
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" name="siswa_ids[]" value="<?= $s['siswa_id'] ?>" onchange="updateSelectedCount()" class="row-checkbox rounded border-gray-300 text-teal-600 cursor-pointer">
                                        </td>
                                        <?php endif; ?>
                                        <td class="px-4 py-3 text-gray-400 font-mono"><?= $idx + 1 ?></td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                    <?= strtoupper(substr($s['nama_siswa'], 0, 2)) ?>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900"><?= esc($s['nama_siswa']) ?></p>
                                                    <p class="text-[11px] text-gray-500">NIS: <?= esc($s['nis']) ?> • <?= $s['jenis_kelamin'] === 'L' ? 'L' : 'P' ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-800">
                                                <?= esc($s['nama_kelas'] ?? '-') ?>
                                            </span>
                                            <p class="text-[11px] text-gray-500 mt-0.5"><?= esc($s['jurusan'] ?? '-') ?></p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php if (!empty($s['guru_id'])): ?>
                                                <p class="font-bold text-gray-800"><?= esc($s['nama_guru']) ?></p>
                                                <p class="text-[11px] text-gray-500 font-mono">NIP: <?= esc($s['guru_nip'] ?: '-') ?></p>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                                    Belum Ditugaskan
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?php if (!empty($s['guru_id'])): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                                    Terpetakan
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                                    Kosong
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <?php if (session()->get('role') === 'admin'): ?>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center space-x-1.5">
                                                <button type="button" onclick="openSingleAssignModal(<?= (int)$s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_siswa'])) ?>', <?= (int)($s['guru_id'] ?: 0) ?>)" class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded-lg" title="Tugaskan / Ganti">
                                                    <i class="fas fa-user-edit"></i>
                                                </button>
                                                <?php if (!empty($s['guru_id'])): ?>
                                                    <button type="button" onclick="unassignSingle(<?= (int)$s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_siswa'])) ?>')" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg" title="Lepas">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <?php else: ?>
    <!-- TAB 2: DAFTAR GURU WALI & BEBAN -->
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($teacherList as $t): ?>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between space-y-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-indigo-500 to-teal-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                            <?= strtoupper(substr($t['nama_lengkap'], 0, 2)) ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-gray-900 text-sm truncate"><?= esc($t['nama_lengkap']) ?></h4>
                            <p class="text-[11px] text-gray-500 font-mono">NIP: <?= esc($t['nip'] ?: '-') ?></p>
                            <p class="text-[11px] text-teal-600 font-semibold truncate"><?= esc($t['nama_mapel'] ?? 'Guru') ?></p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-gray-400 block">Siswa Binaan:</span>
                            <span class="text-lg font-black text-gray-800"><?= (int)$t['total_siswa_wali'] ?> Siswa</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="<?= base_url('admin/guru-wali/jurnal/cetak?guru_id=' . (int)$t['guru_id'] . '&tahun_ajaran=' . urlencode($tahunAjaran)) ?>" target="_blank" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-xl transition flex items-center gap-1" title="Cetak Jurnal Bimbingan Guru Ini">
                                <i class="fas fa-print"></i> Jurnal
                            </a>
                            <button type="button" onclick="viewTeacherMentees(<?= (int)$t['guru_id'] ?>, '<?= esc(addslashes($t['nama_lengkap'])) ?>')" class="px-3 py-1.5 bg-slate-100 hover:bg-teal-50 text-slate-700 hover:text-teal-700 text-xs font-bold rounded-xl transition-colors">
                                Siswa
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ==================== MODALS ==================== -->

<!-- Modal Single Assign -->
<div id="singleAssignModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Tugaskan Guru Wali</h3>
            <button type="button" onclick="document.getElementById('singleAssignModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="<?= base_url('admin/guru-wali/assign') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun_ajaran" value="<?= esc($tahunAjaran) ?>">
            <input type="hidden" name="siswa_id" id="singleSiswaId">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Siswa</label>
                <input type="text" id="singleSiswaNama" readonly class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 p-2.5 font-bold text-gray-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Guru Wali <span class="text-rose-500">*</span></label>
                <select name="guru_id" id="singleGuruId" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                    <option value="">Pilih Guru...</option>
                    <?php foreach ($availableGuru as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?> (<?= esc($g['nama_mapel'] ?? '-') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('singleAssignModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Bulk Assign (Target Guru) -->
<div id="bulkAssignModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Penugasan Massal Guru Wali</h3>
            <button type="button" onclick="document.getElementById('bulkAssignModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="space-y-4">
            <p class="text-xs text-gray-500">Tugaskan seluruh siswa terpilih ke satu Guru Wali berikut:</p>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Guru Wali <span class="text-rose-500">*</span></label>
                <select id="bulkGuruSelect" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                    <option value="">Pilih Guru...</option>
                    <?php foreach ($availableGuru as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('bulkAssignModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Batal</button>
                <button type="button" onclick="submitBulkAssignForm()" class="px-4 py-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow">Tugaskan Sekarang</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Auto Distribute -->
<div id="autoDistributeModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Pembagian Otomatis Guru Wali</h3>
            <button type="button" onclick="document.getElementById('autoDistributeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="<?= base_url('admin/guru-wali/auto-distribute') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun_ajaran" value="<?= esc($tahunAjaran) ?>">

            <p class="text-xs text-gray-600">
                Sistem akan membagi seluruh siswa yang <strong>belum memiliki Guru Wali</strong> secara merata ke guru yang Anda pilih di bawah:
            </p>

            <div class="max-h-56 overflow-y-auto space-y-1.5 p-2 bg-gray-50 rounded-xl border border-gray-200">
                <?php foreach ($availableGuru as $g): ?>
                    <label class="flex items-center space-x-2.5 text-xs text-gray-700 p-1.5 hover:bg-white rounded-lg cursor-pointer">
                        <input type="checkbox" name="guru_ids[]" value="<?= $g['id'] ?>" checked class="rounded border-gray-300 text-teal-600">
                        <span><?= esc($g['nama_lengkap']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('autoDistributeModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow">Bagi Otomatis</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Unassign Single Form (Hidden) -->
<form id="unassignSingleForm" action="<?= base_url('admin/guru-wali/unassign') ?>" method="POST" class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="tahun_ajaran" value="<?= esc($tahunAjaran) ?>">
    <input type="hidden" name="siswa_id" id="unassignSiswaId">
</form>

<!-- Modal Teacher Mentee List -->
<div id="teacherMenteesModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900" id="tmTeacherName">Daftar Siswa Binaan</h3>
            <button type="button" onclick="document.getElementById('teacherMenteesModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="tmListContainer" class="max-h-72 overflow-y-auto space-y-2 text-xs">
            <p class="text-gray-400 text-center py-4">Memuat data...</p>
        </div>
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
            <a id="tmPrintJurnalBtn" href="#" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                <i class="fas fa-print"></i> Cetak Jurnal Bimbingan
            </a>
            <button type="button" onclick="document.getElementById('teacherMenteesModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Filter Cetak Jurnal Guru Wali -->
<div id="printJurnalFilterModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-print text-blue-600"></i> Cetak Jurnal Bimbingan
            </h3>
            <button type="button" onclick="document.getElementById('printJurnalFilterModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="<?= base_url('admin/guru-wali/jurnal/cetak') ?>" method="GET" target="_blank" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Guru Wali</label>
                <select name="guru_id" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:border-blue-500 font-medium">
                    <option value="">-- Semua Guru Wali (Rekapitulasi) --</option>
                    <?php foreach ($availableGuru as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?> (<?= esc($g['nama_mapel'] ?? 'Guru') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:border-blue-500 font-medium">
                    <?php foreach ($tahunAjaranList as $ta): ?>
                        <option value="<?= $ta ?>" <?= $ta === $tahunAjaran ? 'selected' : '' ?>><?= $ta ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenis Bimbingan</label>
                <select name="jenis_bimbingan" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:border-blue-500">
                    <option value="">-- Semua Jenis Bimbingan --</option>
                    <option value="Pendampingan Akademik">1. Pendampingan Akademik</option>
                    <option value="Pengembangan Kompetensi">2. Pengembangan Kompetensi</option>
                    <option value="Keterampilan">3. Keterampilan</option>
                    <option value="Karakter Murid">4. Karakter Murid</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('printJurnalFilterModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" onclick="document.getElementById('printJurnalFilterModal').classList.add('hidden')" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md flex items-center gap-1.5">
                    <i class="fas fa-print"></i> Buka Cetak / PDF
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleSelectAll(checked) {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const selected = document.querySelectorAll('.row-checkbox:checked').length;
    const toolbar = document.getElementById('bulkToolbar');
    const badge = document.getElementById('selectedBadge');
    if (badge) badge.textContent = selected;
    if (toolbar) {
        if (selected > 0) toolbar.classList.remove('hidden');
        else toolbar.classList.add('hidden');
    }
}

function filterSiswaTable(query) {
    const q = (query || '').toLowerCase();
    document.querySelectorAll('.siswa-row').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}

function openSingleAssignModal(siswaId, namaSiswa, currentGuruId) {
    document.getElementById('singleSiswaId').value = siswaId;
    document.getElementById('singleSiswaNama').value = namaSiswa;
    document.getElementById('singleGuruId').value = currentGuruId || '';
    document.getElementById('singleAssignModal').classList.remove('hidden');
}

function unassignSingle(siswaId, namaSiswa) {
    if (confirm(`Lepas penugasan Guru Wali untuk siswa ${namaSiswa}?`)) {
        document.getElementById('unassignSiswaId').value = siswaId;
        document.getElementById('unassignSingleForm').submit();
    }
}

function openBulkAssignModal() {
    const count = document.querySelectorAll('.row-checkbox:checked').length;
    if (count === 0) {
        alert('Pilih minimal satu siswa.');
        return;
    }
    document.getElementById('bulkAssignModal').classList.remove('hidden');
}

function submitBulkAssignForm() {
    const guruId = document.getElementById('bulkGuruSelect').value;
    if (!guruId) {
        alert('Pilih Guru Wali terlebih dahulu.');
        return;
    }
    const form = document.getElementById('bulkForm');
    
    // Add guru_id hidden input
    let hiddenGuru = form.querySelector('input[name="guru_id"]');
    if (!hiddenGuru) {
        hiddenGuru = document.createElement('input');
        hiddenGuru.type = 'hidden';
        hiddenGuru.name = 'guru_id';
        form.appendChild(hiddenGuru);
    }
    hiddenGuru.value = guruId;
    form.action = '<?= base_url('admin/guru-wali/bulk-assign') ?>';
    form.submit();
}

function submitBulkUnassign() {
    const count = document.querySelectorAll('.row-checkbox:checked').length;
    if (count === 0) {
        alert('Pilih minimal satu siswa.');
        return;
    }
    if (confirm(`Lepas penugasan ${count} siswa terpilih dari Guru Wali?`)) {
        const form = document.getElementById('bulkForm');
        form.action = '<?= base_url('admin/guru-wali/bulk-unassign') ?>';
        form.submit();
    }
}

async function viewTeacherMentees(guruId, guruNama) {
    document.getElementById('tmTeacherName').textContent = 'Siswa Binaan: ' + guruNama;
    document.getElementById('tmPrintJurnalBtn').href = `<?= base_url('admin/guru-wali/jurnal/cetak') ?>?guru_id=${guruId}&tahun_ajaran=<?= urlencode($tahunAjaran) ?>`;
    const container = document.getElementById('tmListContainer');
    container.innerHTML = '<p class="text-gray-400 text-center py-4">Memuat data...</p>';
    document.getElementById('teacherMenteesModal').classList.remove('hidden');

    try {
        const res = await fetch(`<?= base_url('admin/guru-wali/siswa-by-guru') ?>?guru_id=${guruId}&tahun_ajaran=<?= urlencode($tahunAjaran) ?>`);
        const json = await res.json();
        if (json.success && json.data.length > 0) {
            container.innerHTML = json.data.map((s, i) => `
                <div class="p-2.5 bg-gray-50 rounded-xl flex items-center justify-between">
                    <div>
                        <p class="font-bold text-gray-900">${i + 1}. ${s.nama_siswa}</p>
                        <p class="text-[11px] text-gray-500">NIS: ${s.nis} • ${s.nama_kelas || '-'}</p>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="text-gray-400 text-center py-4">Belum ada siswa yang ditugaskan ke guru ini.</p>';
        }
    } catch (e) {
        container.innerHTML = '<p class="text-rose-500 text-center py-4">Gagal memuat data.</p>';
    }
}

function openPrintJurnalFilterModal() {
    document.getElementById('printJurnalFilterModal').classList.remove('hidden');
}
</script>
<?= $this->endSection() ?>
