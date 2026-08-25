<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between transition-all">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                <p class="text-sm font-medium text-emerald-800"><?= session()->getFlashdata('success') ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between transition-all">
            <div class="flex items-center space-x-3">
                <i class="fas fa-exclamation-circle text-rose-500 text-xl"></i>
                <p class="text-sm font-medium text-rose-800"><?= session()->getFlashdata('error') ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-teal-700 via-emerald-800 to-indigo-900 rounded-2xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 flex items-center justify-center pointer-events-none">
            <i class="fas fa-user-friends text-9xl"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider text-teal-100 flex items-center gap-1.5">
                        <i class="fas fa-shield-alt text-teal-200"></i> Master Data
                    </span>
                    <span class="px-3 py-1 bg-emerald-500/30 border border-emerald-300/40 backdrop-blur-md rounded-full text-xs font-bold text-emerald-100 flex items-center gap-1.5">
                        <i class="fas fa-check-circle text-emerald-300"></i> TA Aktif Sistem: <?= esc($activeTahunAjaran) ?>
                    </span>
                    <?php if ($tahunAjaran !== $activeTahunAjaran): ?>
                        <span class="px-3 py-1 bg-amber-500/30 border border-amber-300/40 backdrop-blur-md rounded-full text-xs font-bold text-amber-200 flex items-center gap-1.5">
                            <i class="fas fa-history text-amber-300"></i> Menampilkan Data TA: <?= esc($tahunAjaran) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Data Guru Wali</h1>
                <p class="text-teal-100 text-sm mt-1 max-w-2xl">
                    Kelola dan petakan pembagian individu siswa ke Guru Wali (Pembimbing Personal / Asuh Akademik).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="<?= base_url('admin/guru-wali/print?tahun_ajaran=' . urlencode($tahunAjaran)) ?>" target="_blank" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-semibold text-xs rounded-xl transition-all border border-white/20 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-print"></i> Cetak Rekap
                </a>
                <a href="<?= base_url('admin/guru-wali/export?tahun_ajaran=' . urlencode($tahunAjaran)) ?>" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-semibold text-xs rounded-xl transition-all border border-white/20 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-excel"></i> Export CSV
                </a>
                <?php if (session()->get('role') === 'admin'): ?>
                <button type="button" onclick="openAutoDistributeModal()" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs rounded-xl transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-magic"></i> Pembagian Otomatis
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Statistics Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Siswa -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-400 tracking-wider">Total Siswa Aktif</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1"><?= number_format($stats['total_siswa'] ?? 0) ?></h3>
                <p class="text-xs text-gray-500 mt-1">TA <?= esc($tahunAjaran) ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>

        <!-- Siswa Sudah Ada Guru Wali -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs uppercase font-semibold text-emerald-600 tracking-wider">Sudah Memiliki Wali</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1"><?= number_format($stats['total_assigned'] ?? 0) ?></h3>
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-16 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= $stats['percentage_assigned'] ?? 0 ?>%"></div>
                    </div>
                    <span class="text-xs text-emerald-600 font-bold"><?= $stats['percentage_assigned'] ?? 0 ?>%</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <!-- Siswa Belum Ada Guru Wali -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs uppercase font-semibold text-amber-600 tracking-wider">Belum Ada Wali</p>
                <h3 class="text-2xl font-black <?= ($stats['total_unassigned'] ?? 0) > 0 ? 'text-amber-600' : 'text-gray-800' ?> mt-1">
                    <?= number_format($stats['total_unassigned'] ?? 0) ?>
                </h3>
                <p class="text-xs text-amber-600 mt-1 font-medium">
                    <?= ($stats['total_unassigned'] ?? 0) > 0 ? '<i class="fas fa-exclamation-triangle mr-1"></i> Perlu Penugasan' : '<i class="fas fa-check-double mr-1"></i> Semua Terpetakan' ?>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl <?= ($stats['total_unassigned'] ?? 0) > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-400' ?> flex items-center justify-center text-xl font-bold">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>

        <!-- Guru Wali Aktif -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs uppercase font-semibold text-indigo-600 tracking-wider">Guru Bertugas</p>
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
            <a href="?tab=pemetaan&tahun_ajaran=<?= urlencode($tahunAjaran) ?>" class="py-3.5 px-1 border-b-2 font-bold text-sm flex items-center gap-2 <?= $tab === 'pemetaan' ? 'border-teal-600 text-teal-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                <i class="fas fa-th-list"></i> Pemetaan Siswa & Guru Wali
                <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-semibold <?= $tab === 'pemetaan' ? 'bg-teal-100 text-teal-800' : 'bg-gray-100 text-gray-600' ?>">
                    <?= count($siswaList) ?>
                </span>
            </a>
            <a href="?tab=guru&tahun_ajaran=<?= urlencode($tahunAjaran) ?>" class="py-3.5 px-1 border-b-2 font-bold text-sm flex items-center gap-2 <?= $tab === 'guru' ? 'border-teal-600 text-teal-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
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

                <!-- Dropdown Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
                    <!-- Tahun Ajaran -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">
                            <i class="fas fa-calendar text-teal-600 text-[10px]"></i> Tahun Ajaran
                        </label>
                        <div class="relative">
                            <select name="tahun_ajaran" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-bold text-gray-800 transition-all shadow-sm cursor-pointer">
                                <?php foreach ($tahunAjaranList as $ta): ?>
                                    <option value="<?= esc($ta) ?>" <?= $tahunAjaran === $ta ? 'selected' : '' ?>>
                                        <?= esc($ta) ?><?= $ta === $activeTahunAjaran ? ' (Aktif)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tingkat -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">
                            <i class="fas fa-layer-group text-teal-600 text-[10px]"></i> Tingkat
                        </label>
                        <div class="relative">
                            <select name="tingkat" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-medium text-gray-700 transition-all shadow-sm cursor-pointer">
                                <option value="">Semua Tingkat</option>
                                <option value="10" <?= ($filters['tingkat'] ?? '') === '10' ? 'selected' : '' ?>>Kelas 10 (X)</option>
                                <option value="11" <?= ($filters['tingkat'] ?? '') === '11' ? 'selected' : '' ?>>Kelas 11 (XI)</option>
                                <option value="12" <?= ($filters['tingkat'] ?? '') === '12' ? 'selected' : '' ?>>Kelas 12 (XII)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Jurusan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">
                            <i class="fas fa-graduation-cap text-teal-600 text-[10px]"></i> Jurusan
                        </label>
                        <div class="relative">
                            <select name="jurusan" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-medium text-gray-700 transition-all shadow-sm cursor-pointer">
                                <option value="">Semua Jurusan</option>
                                <?php foreach ($jurusanList as $jurusan): ?>
                                    <option value="<?= esc($jurusan) ?>" <?= ($filters['jurusan'] ?? '') === $jurusan ? 'selected' : '' ?>><?= esc($jurusan) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">
                            <i class="fas fa-door-open text-teal-600 text-[10px]"></i> Rombel / Kelas
                        </label>
                        <div class="relative">
                            <select name="kelas_id" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-medium text-gray-700 transition-all shadow-sm cursor-pointer">
                                <option value="">Semua Kelas</option>
                                <?php foreach ($kelasList as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= ($filters['kelas_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                                        <?= esc($k['nama_kelas']) ?> (<?= esc($k['jurusan']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Status Penugasan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">
                            <i class="fas fa-tasks text-teal-600 text-[10px]"></i> Status Penugasan
                        </label>
                        <div class="relative">
                            <select name="status" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-medium text-gray-700 transition-all shadow-sm cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="assigned" <?= ($filters['status'] ?? '') === 'assigned' ? 'selected' : '' ?>>Sudah Ada Guru Wali</option>
                                <option value="unassigned" <?= ($filters['status'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Belum Ada Guru Wali</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Guru Wali -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">
                            <i class="fas fa-user-tie text-teal-600 text-[10px]"></i> Guru Wali
                        </label>
                        <div class="relative">
                            <select name="guru_id" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-medium text-gray-700 transition-all shadow-sm cursor-pointer">
                                <option value="">Semua Guru Wali</option>
                                <?php foreach ($availableGuru as $g): ?>
                                    <option value="<?= $g['id'] ?>" <?= ($filters['guru_id'] ?? '') == $g['id'] ? 'selected' : '' ?>><?= esc($g['nama_lengkap']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Action Row -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2 border-t border-gray-100">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="search" id="liveSearchSiswa" value="<?= esc($filters['search'] ?? '') ?>" oninput="onLiveSearchSiswa(this.value)" placeholder="Ketik untuk langsung mencari Nama Siswa, NIS, Kelas, atau Guru Wali..." class="w-full pl-10 pr-24 text-xs rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 py-2.5 transition-all shadow-sm">
                        
                        <!-- Search result count badge & clear button -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 space-x-1.5">
                            <span id="liveSearchCount" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 animate-fadeIn">
                                0 hasil
                            </span>
                            <button type="button" id="clearSearchBtn" onclick="clearLiveSearch()" class="hidden text-gray-400 hover:text-gray-600 p-1 transition-colors" title="Hapus kata kunci">
                                <i class="fas fa-times-circle text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-initial px-5 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm hover:shadow flex items-center justify-center gap-2">
                            <i class="fas fa-filter"></i> Terapkan Filter
                        </button>
                        <a href="?tab=pemetaan&tahun_ajaran=<?= urlencode($tahunAjaran) ?>" onclick="clearStoredSelectedIds()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1.5" title="Reset Semua Filter & Pilihan">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Batch Action Toolbar (Hidden by default, shown when checkboxes selected) -->
        <?php if (session()->get('role') === 'admin'): ?>
        <div id="batchActionToolbar" class="hidden bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-4 transition-all duration-300 animate-fadeIn sticky top-4 z-30 border border-slate-800">
            <div class="flex items-center space-x-3">
                <span class="w-7 h-7 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs font-black shadow-inner" id="selectedCount">0</span>
                <div>
                    <span class="text-sm font-bold block">Siswa Terpilih</span>
                    <span class="text-[10px] text-teal-300">Pilihan tersimpan lintas pencarian & filter</span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="openBulkAssignModal()" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5 shadow">
                    <i class="fas fa-user-plus"></i> Tugaskan ke Guru Wali...
                </button>
                <button type="button" onclick="confirmBulkUnassign()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5 shadow">
                    <i class="fas fa-user-minus"></i> Lepas Penugasan
                </button>
                <button type="button" onclick="deselectAll()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-gray-300 hover:text-white text-xs font-semibold rounded-xl transition-colors">
                    Bersihkan Pilihan
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- 1. DESKTOP VIEW: Table Siswa & Guru Wali (Preserved for md and above) -->
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-slate-50 text-gray-600 uppercase font-bold tracking-wider">
                        <tr>
                            <?php if (session()->get('role') === 'admin'): ?>
                            <th class="w-10 px-4 py-3.5 text-center">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="master-select-all rounded border-gray-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
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
                    <tbody class="divide-y divide-gray-100 bg-white" id="desktopSiswaTbody">
                        <!-- Desktop Live Search Empty State -->
                        <tr id="desktopSearchEmptyRow" class="hidden">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-search text-3xl mb-2 text-gray-300 block"></i>
                                <p class="font-bold text-gray-700 text-sm">Tidak ada siswa yang cocok dengan pencarian.</p>
                                <p class="text-xs text-gray-400 mt-0.5">Coba kata kunci lain atau bersihkan pencarian.</p>
                                <button type="button" onclick="clearLiveSearch()" class="mt-2.5 px-3.5 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs rounded-xl transition-colors border border-teal-200">
                                    <i class="fas fa-times mr-1"></i> Hapus Kata Kunci
                                </button>
                            </td>
                        </tr>
                        <?php if (empty($siswaList)): ?>
                            <tr id="desktopEmptyRow">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-4xl mb-3 block text-gray-300"></i>
                                    <p class="font-medium text-gray-600">Tidak ada data siswa yang cocok dengan kriteria filter.</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba ubah atau reset filter di atas.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($siswaList as $idx => $s): ?>
                                <tr class="desktop-siswa-row hover:bg-slate-50/80 transition-colors <?= !empty($s['guru_id']) ? '' : 'bg-amber-50/30' ?>" data-search="<?= strtolower(esc($s['nama_siswa'] . ' ' . $s['nis'] . ' ' . ($s['nama_kelas'] ?? '') . ' ' . ($s['jurusan'] ?? '') . ' ' . ($s['nama_guru'] ?? '') . ' ' . ($s['guru_nip'] ?? '') . ' ' . ($s['nama_mapel'] ?? ''))) ?>">
                                    <?php if (session()->get('role') === 'admin'): ?>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" value="<?= $s['siswa_id'] ?>" class="siswa-checkbox desktop-siswa-cb rounded border-gray-300 text-teal-600 focus:ring-teal-500 cursor-pointer" onchange="onSiswaCheckboxChange(this)">
                                    </td>
                                    <?php endif; ?>
                                    <td class="px-4 py-3 text-gray-400 font-mono"><?= $idx + 1 ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs flex-shrink-0 overflow-hidden border border-teal-200">
                                                <?php if (!empty($s['siswa_foto'])): ?>
                                                    <img src="<?= base_url('profile-photo/' . $s['siswa_foto']) ?>" alt="" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <?= strtoupper(substr($s['nama_siswa'], 0, 2)) ?>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900"><?= esc($s['nama_siswa']) ?></p>
                                                <div class="flex items-center gap-2 text-[11px] text-gray-500">
                                                    <span class="font-mono">NIS: <?= esc($s['nis']) ?></span>
                                                    <span>•</span>
                                                    <span class="px-1.5 py-0.2 rounded text-[10px] <?= $s['jenis_kelamin'] === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' ?>">
                                                        <?= $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                            <?= esc($s['nama_kelas'] ?? 'Belum ada kelas') ?>
                                        </span>
                                        <p class="text-[11px] text-gray-500 mt-0.5"><?= esc($s['jurusan'] ?? '-') ?></p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if (!empty($s['guru_id'])): ?>
                                            <div class="flex items-center space-x-2.5">
                                                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0 overflow-hidden border border-indigo-200">
                                                    <?php if (!empty($s['guru_foto'])): ?>
                                                        <img src="<?= base_url('profile-photo/' . $s['guru_foto']) ?>" alt="" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <?= strtoupper(substr($s['nama_guru'], 0, 2)) ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-800"><?= esc($s['nama_guru']) ?></p>
                                                    <p class="text-[11px] text-gray-500 font-mono">NIP: <?= esc($s['guru_nip'] ?: '-') ?> <?= $s['nama_mapel'] ? '• ' . esc($s['nama_mapel']) : '' ?></p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Belum Ditugaskan
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <?php if (!empty($s['guru_id'])): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                                <i class="fas fa-check-circle mr-1"></i> Terpetakan
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                                <i class="fas fa-times-circle mr-1"></i> Kosong
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (session()->get('role') === 'admin'): ?>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <button type="button" onclick="openSingleAssignModal(<?= $s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_siswa'])) ?>', '<?= esc($s['nis']) ?>', '<?= esc($s['nama_kelas']) ?>', <?= $s['guru_id'] ?: 'null' ?>)" class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded-lg transition-colors" title="<?= !empty($s['guru_id']) ? 'Ganti Guru Wali' : 'Tugaskan Guru Wali' ?>">
                                                <i class="fas fa-user-edit text-sm"></i>
                                            </button>
                                            <?php if (!empty($s['guru_id'])): ?>
                                            <button type="button" onclick="confirmUnassign(<?= $s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_siswa'])) ?>')" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors" title="Lepas Penugasan">
                                                <i class="fas fa-user-minus text-sm"></i>
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

        <!-- 2. MOBILE VIEW: Touch-friendly Card List (Screen < md) -->
        <div class="block md:hidden space-y-3">
            <?php if (session()->get('role') === 'admin' && !empty($siswaList)): ?>
            <!-- Mobile Select All Bar -->
            <div class="bg-white rounded-2xl p-3.5 shadow-sm border border-gray-100 flex items-center justify-between">
                <label class="flex items-center space-x-2.5 text-xs font-bold text-gray-700 cursor-pointer">
                    <input type="checkbox" id="mobileSelectAllCheckbox" onchange="toggleSelectAll(this)" class="master-select-all rounded border-gray-300 text-teal-600 focus:ring-teal-500 w-4 h-4">
                    <span>Pilih Semua Siswa</span>
                </label>
                <span class="text-xs text-gray-500 font-semibold bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                    <?= count($siswaList) ?> Siswa
                </span>
            </div>
            <?php endif; ?>

            <!-- Mobile Live Search Empty State -->
            <div id="mobileSearchEmptyRow" class="hidden bg-white rounded-2xl p-8 text-center text-gray-400 border border-gray-100 shadow-sm">
                <i class="fas fa-search text-3xl mb-2 text-gray-300 block"></i>
                <p class="font-bold text-gray-700 text-sm">Tidak ada siswa yang cocok dengan pencarian.</p>
                <p class="text-xs text-gray-400 mt-0.5">Coba kata kunci lain atau bersihkan pencarian.</p>
                <button type="button" onclick="clearLiveSearch()" class="mt-2.5 px-3.5 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs rounded-xl transition-colors border border-teal-200">
                    <i class="fas fa-times mr-1"></i> Hapus Kata Kunci
                </button>
            </div>

            <?php if (empty($siswaList)): ?>
                <div class="bg-white rounded-2xl p-8 text-center text-gray-400 border border-gray-100 shadow-sm">
                    <i class="fas fa-folder-open text-4xl mb-3 block text-gray-300"></i>
                    <p class="font-medium text-gray-600 text-sm">Tidak ada data siswa yang cocok.</p>
                    <p class="text-xs text-gray-400 mt-1">Coba ubah atau reset filter di atas.</p>
                </div>
            <?php else: ?>
                <?php foreach ($siswaList as $idx => $s): ?>
                    <div class="mobile-siswa-card bg-white rounded-2xl shadow-sm border <?= !empty($s['guru_id']) ? 'border-gray-100' : 'border-amber-200/80 bg-amber-50/10' ?> p-4 space-y-3" data-search="<?= strtolower(esc($s['nama_siswa'] . ' ' . $s['nis'] . ' ' . ($s['nama_kelas'] ?? '') . ' ' . ($s['jurusan'] ?? '') . ' ' . ($s['nama_guru'] ?? '') . ' ' . ($s['guru_nip'] ?? '') . ' ' . ($s['nama_mapel'] ?? ''))) ?>">
                        <!-- Top Row: Checkbox, Avatar, Name & Class Badge -->
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-start space-x-3 min-w-0 flex-1">
                                <?php if (session()->get('role') === 'admin'): ?>
                                <div class="pt-0.5">
                                    <input type="checkbox" value="<?= $s['siswa_id'] ?>" class="siswa-checkbox mobile-siswa-cb rounded border-gray-300 text-teal-600 focus:ring-teal-500 w-4 h-4 cursor-pointer" onchange="onSiswaCheckboxChange(this)">
                                </div>
                                <?php endif; ?>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-teal-500 to-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 overflow-hidden shadow-sm">
                                    <?php if (!empty($s['siswa_foto'])): ?>
                                        <img src="<?= base_url('profile-photo/' . $s['siswa_foto']) ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($s['nama_siswa'], 0, 2)) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-gray-900 text-sm leading-snug truncate"><?= esc($s['nama_siswa']) ?></h4>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1 text-[11px]">
                                        <span class="font-mono bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded text-[10px] font-semibold">NIS: <?= esc($s['nis']) ?></span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold <?= $s['jenis_kelamin'] === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' ?>">
                                            <?= $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200 flex-shrink-0 text-right">
                                <?= esc($s['nama_kelas'] ?? 'Tanpa Kelas') ?>
                            </span>
                        </div>

                        <!-- Middle Row: Guru Wali Status -->
                        <div class="rounded-xl p-3 <?= !empty($s['guru_id']) ? 'bg-slate-50 border border-slate-200/80' : 'bg-amber-50/80 border border-amber-200' ?>">
                            <div class="flex items-center justify-between text-[11px] mb-1.5">
                                <span class="font-bold text-gray-500 uppercase tracking-wider text-[10px]">Guru Wali:</span>
                                <?php if (!empty($s['guru_id'])): ?>
                                    <span class="text-emerald-700 font-bold text-[11px] flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        <i class="fas fa-check-circle"></i> Terpetakan
                                    </span>
                                <?php else: ?>
                                    <span class="text-amber-700 font-bold text-[11px] flex items-center gap-1 bg-amber-100/80 px-2 py-0.5 rounded-full border border-amber-200">
                                        <i class="fas fa-exclamation-circle"></i> Belum Ditugaskan
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($s['guru_id'])): ?>
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0 overflow-hidden border border-indigo-200">
                                        <?php if (!empty($s['guru_foto'])): ?>
                                            <img src="<?= base_url('profile-photo/' . $s['guru_foto']) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?= strtoupper(substr($s['nama_guru'], 0, 2)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-gray-800 text-xs truncate"><?= esc($s['nama_guru']) ?></p>
                                        <p class="text-[10px] text-gray-500 truncate font-mono">NIP: <?= esc($s['guru_nip'] ?: '-') ?> <?= $s['nama_mapel'] ? '• ' . esc($s['nama_mapel']) : '' ?></p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-xs text-amber-800 font-medium">Siswa ini belum memiliki pembimbing personal.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Bottom Row: Action Buttons (Admin Only) -->
                        <?php if (session()->get('role') === 'admin'): ?>
                        <div class="flex items-center gap-2 pt-1 border-t border-gray-100">
                            <button type="button" onclick="openSingleAssignModal(<?= $s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_siswa'])) ?>', '<?= esc($s['nis']) ?>', '<?= esc($s['nama_kelas']) ?>', <?= $s['guru_id'] ?: 'null' ?>)" class="flex-1 py-2.5 px-3 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs rounded-xl transition-colors border border-teal-200 flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
                                <i class="fas fa-user-edit"></i> <?= !empty($s['guru_id']) ? 'Ganti Guru Wali' : 'Tugaskan Guru Wali' ?>
                            </button>
                            <?php if (!empty($s['guru_id'])): ?>
                            <button type="button" onclick="confirmUnassign(<?= $s['siswa_id'] ?>, '<?= esc(addslashes($s['nama_siswa'])) ?>')" class="py-2.5 px-3.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs rounded-xl transition-colors border border-rose-200 flex items-center justify-center gap-1 shadow-sm active:scale-95" title="Lepas Penugasan">
                                <i class="fas fa-user-minus"></i> Lepas
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    <!-- TAB 2: DAFTAR GURU WALI & BEBAN BIMBINGAN -->
    <div class="space-y-4">
        <!-- Search Teacher Bar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <form method="get" action="" class="flex flex-col sm:flex-row items-center gap-3">
                <input type="hidden" name="tab" value="guru">
                
                <!-- Tahun Ajaran Filter in Tab 2 -->
                <div class="w-full sm:w-64">
                    <div class="relative">
                        <select name="tahun_ajaran" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-bold text-gray-800 transition-all shadow-sm cursor-pointer">
                            <?php foreach ($tahunAjaranList as $ta): ?>
                                <option value="<?= esc($ta) ?>" <?= $tahunAjaran === $ta ? 'selected' : '' ?>>
                                    <?= esc($ta) ?><?= $ta === $activeTahunAjaran ? ' (Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>

                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" name="search" id="liveSearchGuru" value="<?= esc($filters['search'] ?? '') ?>" oninput="onLiveSearchGuru(this.value)" placeholder="Ketik untuk langsung mencari Nama Guru, NIP, atau Mata Pelajaran..." class="w-full pl-10 pr-24 text-xs rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 py-2.5 transition-all shadow-sm">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 space-x-1.5">
                        <span id="liveSearchGuruCount" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 animate-fadeIn">
                            0 hasil
                        </span>
                        <button type="button" id="clearGuruSearchBtn" onclick="clearLiveSearchGuru()" class="hidden text-gray-400 hover:text-gray-600 p-1 transition-colors" title="Hapus kata kunci">
                            <i class="fas fa-times-circle text-xs"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-initial px-4 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="?tab=guru&tahun_ajaran=<?= urlencode($tahunAjaran) ?>" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-xl transition-colors" title="Reset">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Teachers Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="teachersGridContainer">
            <!-- Live Teacher Search Empty State -->
            <div id="teacherSearchEmpty" class="hidden col-span-3 bg-white rounded-2xl p-12 text-center text-gray-400 border border-gray-100 shadow-sm">
                <i class="fas fa-search text-4xl mb-2 text-gray-300 block"></i>
                <p class="font-bold text-gray-700 text-sm">Tidak ada guru yang cocok dengan pencarian.</p>
                <p class="text-xs text-gray-400 mt-0.5">Coba kata kunci nama guru, NIP, atau mata pelajaran lain.</p>
                <button type="button" onclick="clearLiveSearchGuru()" class="mt-3 px-3.5 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs rounded-xl transition-colors border border-teal-200">
                    <i class="fas fa-times mr-1"></i> Hapus Kata Kunci
                </button>
            </div>

            <?php if (empty($teacherList)): ?>
                <div class="col-span-3 bg-white rounded-xl p-12 text-center text-gray-400 border border-gray-100 shadow-sm">
                    <i class="fas fa-users-slash text-4xl mb-3 block text-gray-300"></i>
                    <p class="font-medium text-gray-600">Tidak ada data guru ditemukan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($teacherList as $t): ?>
                    <div class="teacher-grid-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between hover:shadow-md transition-all" data-search="<?= strtolower(esc($t['nama_lengkap'] . ' ' . ($t['nip'] ?? '') . ' ' . ($t['nama_mapel'] ?? ''))) ?>">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-teal-500 to-indigo-600 text-white flex items-center justify-center font-black text-sm overflow-hidden flex-shrink-0 shadow-sm border-2 border-white">
                                        <?php if (!empty($t['profile_photo'])): ?>
                                            <img src="<?= base_url('profile-photo/' . $t['profile_photo']) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?= strtoupper(substr($t['nama_lengkap'], 0, 2)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm"><?= esc($t['nama_lengkap']) ?></h4>
                                        <p class="text-xs text-gray-500 font-mono">NIP: <?= esc($t['nip'] ?: '-') ?></p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-black <?= (int)$t['total_siswa_wali'] > 0 ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-gray-100 text-gray-500' ?>">
                                    <?= (int)$t['total_siswa_wali'] ?> Siswa
                                </span>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                <span><i class="fas fa-book text-gray-400 mr-1"></i> <?= esc($t['nama_mapel'] ?: 'Guru Umum') ?></span>
                                <span><i class="fas fa-calendar-alt text-gray-400 mr-1"></i> TA <?= esc($tahunAjaran) ?></span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2">
                            <button type="button" onclick="viewSiswaModal(<?= $t['guru_id'] ?>, '<?= esc(addslashes($t['nama_lengkap'])) ?>')" class="flex-1 px-3 py-2 bg-slate-50 hover:bg-teal-50 text-teal-700 font-bold text-xs rounded-lg transition-colors border border-slate-200 hover:border-teal-200 flex items-center justify-center gap-1.5">
                                <i class="fas fa-list-ul"></i> Lihat Siswa Binaan
                            </button>
                            <a href="<?= base_url('admin/guru-wali/print?tahun_ajaran=' . urlencode($tahunAjaran) . '&guru_id=' . $t['guru_id']) ?>" target="_blank" class="p-2 text-gray-500 hover:text-gray-800 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors" title="Cetak Rekap Guru Ini">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ==================== MODALS ==================== -->

<!-- 1. Single Assign Modal -->
<div id="singleAssignModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-scaleUp">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-edit text-teal-600"></i> Penugasan Guru Wali
            </h3>
            <button type="button" onclick="closeSingleAssignModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>

        <form id="singleAssignForm" onsubmit="submitSingleAssign(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="siswa_id" id="assignSiswaId">
            <input type="hidden" name="tahun_ajaran" value="<?= esc($tahunAjaran) ?>">

            <!-- Siswa Info Card -->
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/80 text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Siswa:</span>
                    <span class="font-bold text-gray-800" id="assignSiswaNama">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">NIS / Kelas:</span>
                    <span class="font-mono text-gray-700" id="assignSiswaInfo">-</span>
                </div>
            </div>

            <div class="space-y-3 mt-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                        <span>Pilih Guru Wali *</span>
                        <span class="text-[11px] text-gray-400 font-normal">Gunakan pencarian di bawah</span>
                    </label>
                    <div class="space-y-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </span>
                            <input type="text" id="singleGuruSearch" oninput="filterGuruDropdown('assignGuruId', this.value)" placeholder="Ketik untuk mencari nama guru / NIP..." class="w-full pl-9 pr-3 text-xs rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 py-2 transition-all">
                        </div>
                        <div class="relative">
                            <select name="guru_id" id="assignGuruId" required class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-medium text-gray-800 shadow-sm cursor-pointer">
                                <option value="">-- Pilih Guru Wali --</option>
                                <?php foreach ($availableGuru as $g): ?>
                                    <option value="<?= $g['id'] ?>" data-search="<?= strtolower(esc($g['nama_lengkap'] . ' ' . ($g['nip'] ?? '') . ' ' . ($g['nama_mapel'] ?? ''))) ?>">
                                        <?= esc($g['nama_lengkap']) ?> (<?= esc($g['nip'] ?: 'NIP -') ?> <?= $g['nama_mapel'] ? '• ' . esc($g['nama_mapel']) : '' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Catatan / Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="assignKeterangan" rows="2" placeholder="Catatan khusus pembimbingan personal..." class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2 transition-all"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
                <button type="button" onclick="closeSingleAssignModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" id="btnSaveSingle" class="px-4 py-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Simpan Penugasan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Bulk Assign Modal -->
<div id="bulkAssignModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-scaleUp">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users-cog text-teal-600"></i> Penugasan Massal Guru Wali
            </h3>
            <button type="button" onclick="closeBulkAssignModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>

        <form id="bulkAssignForm" onsubmit="submitBulkAssign(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun_ajaran" value="<?= esc($tahunAjaran) ?>">

            <div class="bg-teal-50 border border-teal-200 rounded-xl p-3.5 text-xs text-teal-900 flex items-center space-x-3">
                <i class="fas fa-info-circle text-teal-600 text-lg"></i>
                <p>Anda akan menugaskan <strong id="bulkSelectedCount">0</strong> siswa terpilih ke Guru Wali yang dipilih di bawah ini.</p>
            </div>

            <div class="space-y-3 mt-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                        <span>Pilih Guru Wali Tujuan *</span>
                        <span class="text-[11px] text-gray-400 font-normal">Gunakan pencarian di bawah</span>
                    </label>
                    <div class="space-y-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </span>
                            <input type="text" id="bulkGuruSearch" oninput="filterGuruDropdown('bulkGuruId', this.value)" placeholder="Ketik untuk mencari nama guru / NIP..." class="w-full pl-9 pr-3 text-xs rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 py-2 transition-all">
                        </div>
                        <div class="relative">
                            <select name="guru_id" id="bulkGuruId" required class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 px-3 py-2.5 appearance-none pr-8 font-medium text-gray-800 shadow-sm cursor-pointer">
                                <option value="">-- Pilih Guru Wali Tujuan --</option>
                                <?php foreach ($availableGuru as $g): ?>
                                    <option value="<?= $g['id'] ?>" data-search="<?= strtolower(esc($g['nama_lengkap'] . ' ' . ($g['nip'] ?? '') . ' ' . ($g['nama_mapel'] ?? ''))) ?>">
                                        <?= esc($g['nama_lengkap']) ?> (<?= esc($g['nip'] ?: 'NIP -') ?> <?= $g['nama_mapel'] ? '• ' . esc($g['nama_mapel']) : '' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
                <button type="button" onclick="closeBulkAssignModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" id="btnSaveBulk" class="px-4 py-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-check-circle"></i> Tugaskan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Auto Distribute Modal -->
<div id="autoDistributeModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 animate-scaleUp">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-magic text-emerald-600"></i> Pembagian Otomatis Guru Wali
            </h3>
            <button type="button" onclick="closeAutoDistributeModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>

        <form id="autoDistributeForm" onsubmit="submitAutoDistribute(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun_ajaran" value="<?= esc($tahunAjaran) ?>">

            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-3.5 text-xs text-indigo-900 space-y-1">
                <p class="font-bold flex items-center gap-1.5"><i class="fas fa-robot text-indigo-600"></i> Algoritma Pembagian Cerdas (Round-Robin):</p>
                <p class="text-indigo-700">Siswa yang <strong>belum memiliki Guru Wali</strong> (<?= $stats['total_unassigned'] ?? 0 ?> siswa) akan dibagikan secara adil dan merata ke Guru-guru yang Anda centang di bawah ini.</p>
            </div>

            <div class="space-y-2 mt-4">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-gray-700">Pilih Guru yang Dilibatkan:</label>
                    <div class="space-x-2 text-xs">
                        <button type="button" onclick="toggleAllDistributeTeachers(true)" class="text-teal-600 hover:underline font-semibold">Pilih Semua</button>
                        <span>•</span>
                        <button type="button" onclick="toggleAllDistributeTeachers(false)" class="text-gray-500 hover:underline">Hapus Semua</button>
                    </div>
                </div>

                <div class="relative mb-2">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" oninput="filterDistributeTeachers(this.value)" placeholder="Filter guru dalam daftar di bawah..." class="w-full pl-9 pr-3 text-xs rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 py-2 transition-all">
                </div>

                <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-xl p-2 space-y-1 divide-y divide-gray-100 bg-slate-50/50">
                    <?php foreach ($availableGuru as $g): ?>
                        <label class="distribute-teacher-item flex items-center space-x-3 p-2 hover:bg-white rounded-lg transition-colors cursor-pointer text-xs" data-search="<?= strtolower(esc($g['nama_lengkap'] . ' ' . ($g['nip'] ?? '') . ' ' . ($g['nama_mapel'] ?? ''))) ?>">
                            <input type="checkbox" name="guru_ids[]" value="<?= $g['id'] ?>" class="distribute-teacher-cb rounded border-gray-300 text-teal-600 focus:ring-teal-500" checked>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800"><?= esc($g['nama_lengkap']) ?></p>
                                <p class="text-[11px] text-gray-500 font-mono">NIP: <?= esc($g['nip'] ?: '-') ?> <?= $g['nama_mapel'] ? '• ' . esc($g['nama_mapel']) : '' ?></p>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
                <button type="button" onclick="closeAutoDistributeModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" id="btnSaveAuto" class="px-4 py-2 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl transition-all shadow-md flex items-center gap-1.5">
                    <i class="fas fa-play"></i> Mulai Pembagian Otomatis
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 4. View Siswa Binaan Drawer / Modal -->
<div id="viewSiswaModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 animate-scaleUp">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users text-teal-600"></i> Siswa Binaan Guru Wali
                </h3>
                <p class="text-xs text-gray-500 mt-0.5" id="viewGuruNama">-</p>
            </div>
            <button type="button" onclick="closeViewSiswaModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <div id="viewSiswaLoading" class="py-12 text-center text-gray-400">
                <i class="fas fa-spinner fa-spin text-3xl mb-2 text-teal-500"></i>
                <p class="text-xs">Memuat data siswa binaan...</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs hidden" id="viewSiswaTable">
                    <thead class="bg-slate-50 font-bold text-gray-600 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">No</th>
                            <th class="px-3 py-2 text-left">Siswa</th>
                            <th class="px-3 py-2 text-left">Kelas</th>
                            <th class="px-3 py-2 text-left">Tanggal Penugasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="viewSiswaTbody">
                    </tbody>
                </table>
            </div>
            <div id="viewSiswaEmpty" class="py-8 text-center text-gray-400 hidden">
                <i class="fas fa-user-slash text-3xl mb-2 text-gray-300"></i>
                <p class="text-xs">Belum ada siswa yang ditugaskan ke Guru Wali ini.</p>
            </div>
        </div>

        <div class="flex items-center justify-end pt-3 border-t border-gray-100">
            <button type="button" onclick="closeViewSiswaModal()" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Tutup</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ==================== CSRF TOKEN MANAGEMENT ====================
const CSRF_TOKEN = '<?= csrf_token() ?>';
let csrfHash = '<?= csrf_hash() ?>';

function updateCsrf(newHash) {
    if (newHash) {
        csrfHash = newHash;
        document.querySelectorAll(`input[name="${CSRF_TOKEN}"]`).forEach(el => el.value = newHash);
    }
}

// ==================== PERSISTENT CHECKBOX & BATCH SELECTION ====================
const STORAGE_KEY = 'simacca_guru_wali_selected_' + <?= json_encode($tahunAjaran) ?>;

function getStoredSelectedIds() {
    try {
        const val = sessionStorage.getItem(STORAGE_KEY);
        return val ? JSON.parse(val) : [];
    } catch (e) {
        return [];
    }
}

function setStoredSelectedIds(ids) {
    try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
    } catch (e) {}
}

function clearStoredSelectedIds() {
    try {
        sessionStorage.removeItem(STORAGE_KEY);
    } catch (e) {}
}

function getSelectedSiswaIds() {
    return getStoredSelectedIds();
}

function onSiswaCheckboxChange(checkbox) {
    const id = parseInt(checkbox.value, 10);
    const checked = checkbox.checked;
    const currentIds = new Set(getStoredSelectedIds());

    if (checked) {
        currentIds.add(id);
    } else {
        currentIds.delete(id);
    }

    setStoredSelectedIds(Array.from(currentIds));

    // Sync any corresponding desktop & mobile checkbox with same value
    document.querySelectorAll(`.siswa-checkbox[value="${id}"]`).forEach(cb => {
        cb.checked = checked;
    });

    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedIds = getSelectedSiswaIds();
    const count = selectedIds.length;
    const toolbar = document.getElementById('batchActionToolbar');
    const countSpan = document.getElementById('selectedCount');
    const bulkCountSpan = document.getElementById('bulkSelectedCount');

    if (countSpan) countSpan.textContent = count;
    if (bulkCountSpan) bulkCountSpan.textContent = count;

    if (toolbar) {
        if (count > 0) {
            toolbar.classList.remove('hidden');
        } else {
            toolbar.classList.add('hidden');
        }
    }

    // Sync desktop master checkbox based on ALL rows in table
    const allDesktopCbs = Array.from(document.querySelectorAll('.desktop-siswa-cb'));
    const masterDesktop = document.getElementById('selectAllCheckbox');
    if (masterDesktop) {
        if (allDesktopCbs.length > 0) {
            const checkedCount = allDesktopCbs.filter(cb => cb.checked).length;
            masterDesktop.checked = (checkedCount === allDesktopCbs.length);
            masterDesktop.indeterminate = (checkedCount > 0 && checkedCount < allDesktopCbs.length);
        } else {
            masterDesktop.checked = false;
            masterDesktop.indeterminate = false;
        }
    }

    // Sync mobile master checkbox based on ALL cards
    const allMobileCbs = Array.from(document.querySelectorAll('.mobile-siswa-cb'));
    const masterMobile = document.getElementById('mobileSelectAllCheckbox');
    if (masterMobile) {
        if (allMobileCbs.length > 0) {
            const checkedCount = allMobileCbs.filter(cb => cb.checked).length;
            masterMobile.checked = (checkedCount === allMobileCbs.length);
            masterMobile.indeterminate = (checkedCount > 0 && checkedCount < allMobileCbs.length);
        } else {
            masterMobile.checked = false;
            masterMobile.indeterminate = false;
        }
    }
}

function toggleSelectAll(masterCb) {
    const isChecked = masterCb.checked;
    const currentIds = new Set(getStoredSelectedIds());

    // 1. Toggle ALL desktop rows (select all students)
    document.querySelectorAll('.desktop-siswa-cb').forEach(cb => {
        cb.checked = isChecked;
        const id = parseInt(cb.value, 10);
        if (isChecked) {
            currentIds.add(id);
        } else {
            currentIds.delete(id);
        }
    });

    // 2. Toggle ALL mobile cards
    document.querySelectorAll('.mobile-siswa-cb').forEach(cb => {
        cb.checked = isChecked;
        const id = parseInt(cb.value, 10);
        if (isChecked) {
            currentIds.add(id);
        } else {
            currentIds.delete(id);
        }
    });

    // 3. Save updated selection to persistent store
    setStoredSelectedIds(Array.from(currentIds));

    // 4. Update master checkboxes state
    const masterDesktop = document.getElementById('selectAllCheckbox');
    const masterMobile = document.getElementById('mobileSelectAllCheckbox');
    if (masterDesktop) {
        masterDesktop.checked = isChecked;
        masterDesktop.indeterminate = false;
    }
    if (masterMobile) {
        masterMobile.checked = isChecked;
        masterMobile.indeterminate = false;
    }

    updateSelectedCount();
}

function selectOnlySearchResults(isChecked = true) {
    const currentIds = new Set(getStoredSelectedIds());

    document.querySelectorAll('.desktop-siswa-row').forEach(row => {
        if (row.style.display !== 'none') {
            const cb = row.querySelector('.desktop-siswa-cb');
            if (cb) {
                cb.checked = isChecked;
                const id = parseInt(cb.value, 10);
                if (isChecked) currentIds.add(id);
                else currentIds.delete(id);
            }
        }
    });

    document.querySelectorAll('.mobile-siswa-card').forEach(card => {
        if (card.style.display !== 'none') {
            const cb = card.querySelector('.mobile-siswa-cb');
            if (cb) {
                cb.checked = isChecked;
                const id = parseInt(cb.value, 10);
                if (isChecked) currentIds.add(id);
                else currentIds.delete(id);
            }
        }
    });

    setStoredSelectedIds(Array.from(currentIds));
    updateSelectedCount();
}

function deselectAll() {
    clearStoredSelectedIds();
    document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = false);
    const masterDesktop = document.getElementById('selectAllCheckbox');
    const masterMobile = document.getElementById('mobileSelectAllCheckbox');
    if (masterDesktop) {
        masterDesktop.checked = false;
        masterDesktop.indeterminate = false;
    }
    if (masterMobile) {
        masterMobile.checked = false;
        masterMobile.indeterminate = false;
    }
    updateSelectedCount();
}

// ==================== INSTANT CLIENT-SIDE LIVE SEARCH ====================
function onLiveSearchSiswa(keyword) {
    const term = (keyword || '').toLowerCase().trim();
    const words = term.split(/\s+/).filter(Boolean);
    const countBadge = document.getElementById('liveSearchCount');
    const clearBtn = document.getElementById('clearSearchBtn');
    const desktopEmptyRow = document.getElementById('desktopSearchEmptyRow');
    const mobileEmptyRow = document.getElementById('mobileSearchEmptyRow');

    if (term.length > 0) {
        if (clearBtn) clearBtn.classList.remove('hidden');
    } else {
        if (clearBtn) clearBtn.classList.add('hidden');
    }

    // 1. Filter desktop rows
    const desktopRows = document.querySelectorAll('.desktop-siswa-row');
    let desktopMatches = 0;
    desktopRows.forEach(row => {
        const text = (row.getAttribute('data-search') || row.textContent).toLowerCase();
        const match = words.length === 0 || words.every(w => text.includes(w));
        row.style.display = match ? '' : 'none';
        if (match) desktopMatches++;
    });

    if (desktopEmptyRow) {
        if (term.length > 0 && desktopMatches === 0 && desktopRows.length > 0) {
            desktopEmptyRow.classList.remove('hidden');
        } else {
            desktopEmptyRow.classList.add('hidden');
        }
    }

    // 2. Filter mobile cards
    const mobileCards = document.querySelectorAll('.mobile-siswa-card');
    let mobileMatches = 0;
    mobileCards.forEach(card => {
        const text = (card.getAttribute('data-search') || card.textContent).toLowerCase();
        const match = words.length === 0 || words.every(w => text.includes(w));
        card.style.display = match ? '' : 'none';
        if (match) mobileMatches++;
    });

    if (mobileEmptyRow) {
        if (term.length > 0 && mobileMatches === 0 && mobileCards.length > 0) {
            mobileEmptyRow.classList.remove('hidden');
        } else {
            mobileEmptyRow.classList.add('hidden');
        }
    }

    // Update count badge
    const totalVisible = Math.max(desktopMatches, mobileMatches);
    if (countBadge) {
        if (term.length > 0) {
            countBadge.textContent = `${totalVisible} hasil`;
            countBadge.classList.remove('hidden');
        } else {
            countBadge.classList.add('hidden');
        }
    }

    updateSelectedCount();
}

function clearLiveSearch() {
    const input = document.getElementById('liveSearchSiswa');
    if (input) {
        input.value = '';
        onLiveSearchSiswa('');
        input.focus();
    }
}

// ==================== TEACHER LIVE SEARCH (TAB 2) ====================
function onLiveSearchGuru(keyword) {
    const term = (keyword || '').toLowerCase().trim();
    const words = term.split(/\s+/).filter(Boolean);
    const countBadge = document.getElementById('liveSearchGuruCount');
    const clearBtn = document.getElementById('clearGuruSearchBtn');
    const emptyState = document.getElementById('teacherSearchEmpty');

    if (term.length > 0) {
        if (clearBtn) clearBtn.classList.remove('hidden');
    } else {
        if (clearBtn) clearBtn.classList.add('hidden');
    }

    const cards = document.querySelectorAll('.teacher-grid-card');
    let matches = 0;
    cards.forEach(card => {
        const text = (card.getAttribute('data-search') || card.textContent).toLowerCase();
        const match = words.length === 0 || words.every(w => text.includes(w));
        card.style.display = match ? 'flex' : 'none';
        if (match) matches++;
    });

    if (emptyState) {
        if (term.length > 0 && matches === 0 && cards.length > 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    if (countBadge) {
        if (term.length > 0) {
            countBadge.textContent = `${matches} hasil`;
            countBadge.classList.remove('hidden');
        } else {
            countBadge.classList.add('hidden');
        }
    }
}

function clearLiveSearchGuru() {
    const input = document.getElementById('liveSearchGuru');
    if (input) {
        input.value = '';
        onLiveSearchGuru('');
        input.focus();
    }
}

// Restore saved checkboxes on page load
document.addEventListener('DOMContentLoaded', function() {
    const storedIds = new Set(getStoredSelectedIds());
    if (storedIds.size > 0) {
        document.querySelectorAll('.siswa-checkbox').forEach(cb => {
            const id = parseInt(cb.value, 10);
            if (storedIds.has(id)) {
                cb.checked = true;
            }
        });
    }
    updateSelectedCount();
});

// ==================== SINGLE ASSIGN MODAL ====================
function openSingleAssignModal(siswaId, nama, nis, kelas, currentGuruId) {
    document.getElementById('assignSiswaId').value = siswaId;
    document.getElementById('assignSiswaNama').textContent = nama;
    document.getElementById('assignSiswaInfo').textContent = `NIS: ${nis} • Kelas: ${kelas || '-'}`;
    document.getElementById('assignGuruId').value = currentGuruId || '';
    document.getElementById('assignKeterangan').value = '';
    document.getElementById('singleAssignModal').classList.remove('hidden');
}

function closeSingleAssignModal() {
    document.getElementById('singleAssignModal').classList.add('hidden');
}

async function submitSingleAssign(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSaveSingle');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const formData = new FormData(document.getElementById('singleAssignForm'));
    formData.set(CSRF_TOKEN, csrfHash);

    try {
        const response = await fetch('<?= base_url('admin/guru-wali/assign') ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        });
        const res = await response.json();
        if (res.csrf_hash) updateCsrf(res.csrf_hash);

        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Gagal menyimpan penugasan');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan Penugasan';
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Penugasan';
    }
}

// ==================== BULK ASSIGN MODAL ====================
function openBulkAssignModal() {
    const ids = getSelectedSiswaIds();
    if (ids.length === 0) {
        alert('Pilih minimal satu siswa terlebih dahulu.');
        return;
    }
    document.getElementById('bulkSelectedCount').textContent = ids.length;
    document.getElementById('bulkGuruId').value = '';
    document.getElementById('bulkAssignModal').classList.remove('hidden');
}

function closeBulkAssignModal() {
    document.getElementById('bulkAssignModal').classList.add('hidden');
}

async function submitBulkAssign(e) {
    e.preventDefault();
    const ids = getSelectedSiswaIds();
    const guruId = document.getElementById('bulkGuruId').value;
    if (!guruId) {
        alert('Pilih Guru Wali tujuan');
        return;
    }

    const btn = document.getElementById('btnSaveBulk');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

    const formData = new FormData();
    formData.append(CSRF_TOKEN, csrfHash);
    formData.append('tahun_ajaran', '<?= esc($tahunAjaran) ?>');
    formData.append('guru_id', guruId);
    ids.forEach(id => formData.append('siswa_ids[]', id));

    try {
        const response = await fetch('<?= base_url('admin/guru-wali/bulk-assign') ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        });
        const res = await response.json();
        if (res.csrf_hash) updateCsrf(res.csrf_hash);

        if (res.success) {
            clearStoredSelectedIds();
            window.location.reload();
        } else {
            alert(res.message || 'Gagal melakukan penugasan massal');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Tugaskan Sekarang';
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Tugaskan Sekarang';
    }
}

// ==================== AUTO DISTRIBUTE MODAL ====================
function openAutoDistributeModal() {
    document.getElementById('autoDistributeModal').classList.remove('hidden');
}

function closeAutoDistributeModal() {
    document.getElementById('autoDistributeModal').classList.add('hidden');
}

function toggleAllDistributeTeachers(checked) {
    document.querySelectorAll('.distribute-teacher-cb').forEach(cb => cb.checked = checked);
}

async function submitAutoDistribute(e) {
    e.preventDefault();
    const selectedTeachers = Array.from(document.querySelectorAll('.distribute-teacher-cb:checked')).map(cb => cb.value);
    if (selectedTeachers.length === 0) {
        alert('Pilih minimal satu Guru Wali untuk dilibatkan.');
        return;
    }

    if (!confirm('Apakah Anda yakin ingin membagikan siswa yang belum memiliki Guru Wali secara otomatis ke ' + selectedTeachers.length + ' guru terpilih?')) {
        return;
    }

    const btn = document.getElementById('btnSaveAuto');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membagikan...';

    const formData = new FormData();
    formData.append(CSRF_TOKEN, csrfHash);
    formData.append('tahun_ajaran', '<?= esc($tahunAjaran) ?>');
    selectedTeachers.forEach(id => formData.append('guru_ids[]', id));

    // Optional: if specific students were checked, pass them; otherwise pass empty to auto-distribute all unassigned
    const checkedSiswa = getSelectedSiswaIds();
    if (checkedSiswa.length > 0) {
        checkedSiswa.forEach(id => formData.append('siswa_ids[]', id));
    }

    try {
        const response = await fetch('<?= base_url('admin/guru-wali/auto-distribute') ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        });
        const res = await response.json();
        if (res.csrf_hash) updateCsrf(res.csrf_hash);

        if (res.success) {
            clearStoredSelectedIds();
            window.location.reload();
        } else {
            alert(res.message || 'Gagal melakukan pembagian otomatis');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Mulai Pembagian Otomatis';
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Mulai Pembagian Otomatis';
    }
}

// ==================== UNASSIGN HANDLERS ====================
async function confirmUnassign(siswaId, namaSiswa) {
    if (!confirm(`Apakah Anda yakin ingin melepas penugasan Guru Wali untuk siswa "${namaSiswa}"?`)) {
        return;
    }

    const formData = new FormData();
    formData.append(CSRF_TOKEN, csrfHash);
    formData.append('tahun_ajaran', '<?= esc($tahunAjaran) ?>');

    try {
        const response = await fetch(`<?= base_url('admin/guru-wali/unassign') ?>/${siswaId}`, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        });
        const res = await response.json();
        if (res.csrf_hash) updateCsrf(res.csrf_hash);

        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Gagal melepas penugasan');
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan: ' + err.message);
    }
}

async function confirmBulkUnassign() {
    const ids = getSelectedSiswaIds();
    if (ids.length === 0) return;

    if (!confirm(`Apakah Anda yakin ingin melepas penugasan Guru Wali untuk ${ids.length} siswa terpilih?`)) {
        return;
    }

    const formData = new FormData();
    formData.append(CSRF_TOKEN, csrfHash);
    formData.append('tahun_ajaran', '<?= esc($tahunAjaran) ?>');
    ids.forEach(id => formData.append('siswa_ids[]', id));

    try {
        const response = await fetch('<?= base_url('admin/guru-wali/bulk-unassign') ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        });
        const res = await response.json();
        if (res.csrf_hash) updateCsrf(res.csrf_hash);

        if (res.success) {
            clearStoredSelectedIds();
            window.location.reload();
        } else {
            alert(res.message || 'Gagal melepas penugasan');
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan: ' + err.message);
    }
}

// ==================== VIEW SISWA BINAAN MODAL ====================
async function viewSiswaModal(guruId, guruNama) {
    document.getElementById('viewGuruNama').textContent = `Guru Wali: ${guruNama}`;
    document.getElementById('viewSiswaLoading').classList.remove('hidden');
    document.getElementById('viewSiswaTable').classList.add('hidden');
    document.getElementById('viewSiswaEmpty').classList.add('hidden');
    document.getElementById('viewSiswaModal').classList.remove('hidden');

    try {
        const response = await fetch(`<?= base_url('admin/guru-wali/siswa-by-guru') ?>/${guruId}?tahun_ajaran=<?= urlencode($tahunAjaran) ?>`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const res = await response.json();
        document.getElementById('viewSiswaLoading').classList.add('hidden');

        if (res.success && res.data.siswaList && res.data.siswaList.length > 0) {
            const tbody = document.getElementById('viewSiswaTbody');
            tbody.innerHTML = '';
            res.data.siswaList.forEach((s, i) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50';
                tr.innerHTML = `
                    <td class="px-3 py-2 font-mono text-gray-400">${i + 1}</td>
                    <td class="px-3 py-2 font-bold text-gray-800">${s.nama_siswa} <span class="font-normal font-mono text-gray-400">(${s.nis})</span></td>
                    <td class="px-3 py-2 text-gray-600">${s.nama_kelas || '-'}</td>
                    <td class="px-3 py-2 text-gray-400 font-mono text-[11px]">${s.assigned_at ? s.assigned_at.substring(0, 10) : '-'}</td>
                `;
                tbody.appendChild(tr);
            });
            document.getElementById('viewSiswaTable').classList.remove('hidden');
        } else {
            document.getElementById('viewSiswaEmpty').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('viewSiswaLoading').classList.add('hidden');
        alert('Gagal mengambil data siswa binaan: ' + err.message);
    }
}

function closeViewSiswaModal() {
    document.getElementById('viewSiswaModal').classList.add('hidden');
}

// ==================== LIVE SEARCH DROPDOWN FILTERS ====================
function filterGuruDropdown(selectId, keyword) {
    const select = document.getElementById(selectId);
    if (!select) return;
    const filter = keyword.toLowerCase().trim();
    const options = select.querySelectorAll('option');
    options.forEach((opt, idx) => {
        if (idx === 0) return; // Keep placeholder
        const text = opt.getAttribute('data-search') || opt.textContent.toLowerCase();
        opt.style.display = text.includes(filter) ? '' : 'none';
    });
}

function filterDistributeTeachers(keyword) {
    const filter = keyword.toLowerCase().trim();
    const items = document.querySelectorAll('.distribute-teacher-item');
    items.forEach(item => {
        const text = item.getAttribute('data-search') || item.textContent.toLowerCase();
        item.style.display = text.includes(filter) ? 'flex' : 'none';
    });
}
</script>
<?= $this->endSection() ?>
