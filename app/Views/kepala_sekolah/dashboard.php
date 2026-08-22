<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 rounded-2xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 flex items-center justify-center">
            <i class="fas fa-school text-9xl"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center space-x-3 mb-2">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold uppercase tracking-wider text-blue-100">
                    <i class="fas fa-crown mr-1"></i> Executive Portal
                </span>
                <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-blue-100">
                    Tahun Ajaran <?= esc($activeTA) ?>
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                <?= get_greeting() ?>, <?= esc($user['nama_lengkap'] ?? $kepalaSekolahNama ?? 'Kepala Sekolah') ?>
            </h1>
            <p class="text-blue-100 text-sm mt-1 max-w-2xl">
                Selamat datang di Executive Monitoring Dashboard SIMACCA. Ringkasan kehadiran, statistik pembelajaran, dan integritas sekolah.
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Total Siswa Aktif</p>
                <h3 class="text-3xl font-extrabold text-gray-800 mt-1"><?= number_format($stats['total_siswa']) ?></h3>
                <p class="text-xs text-emerald-600 mt-1 font-medium"><i class="fas fa-check-circle mr-1"></i> TA <?= esc($activeTA) ?></p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Total Tenaga Pendidik</p>
                <h3 class="text-3xl font-extrabold text-gray-800 mt-1"><?= number_format($stats['total_guru']) ?></h3>
                <p class="text-xs text-indigo-600 mt-1 font-medium"><i class="fas fa-chalkboard-teacher mr-1"></i> Guru & Staf</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-users-cog"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Rombongan Belajar</p>
                <h3 class="text-3xl font-extrabold text-gray-800 mt-1"><?= number_format($stats['total_kelas']) ?></h3>
                <p class="text-xs text-purple-600 mt-1 font-medium"><i class="fas fa-door-open mr-1"></i> Kelas Terdaftar</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-school"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Siswa Hadir Hari Ini</p>
                <h3 class="text-3xl font-extrabold text-gray-800 mt-1"><?= number_format($stats['siswa_hadir_today']) ?></h3>
                <p class="text-xs text-emerald-600 mt-1 font-medium"><i class="fas fa-calendar-check mr-1"></i> Presepsi Real-time</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-clipboard-check"></i>
            </div>
        </div>
    </div>

    <!-- Quick Executive Actions & Features -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-indigo-600 mr-2"></i> Ringkasan Eksekutif Sekolah
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
                    <span class="text-gray-600">Nama Kepala Sekolah</span>
                    <span class="font-bold text-gray-800"><?= esc($kepalaSekolahNama ?: $user['nama_lengkap']) ?></span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
                    <span class="text-gray-600">NIP Kepala Sekolah</span>
                    <span class="font-mono text-gray-800"><?= esc($kepalaSekolahNip ?: '-') ?></span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
                    <span class="text-gray-600">Tahun Ajaran Aktif</span>
                    <span class="font-bold text-indigo-600"><?= esc($activeTA) ?></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-rocket text-indigo-600 mr-2"></i> Navigasi Eksekutif
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="<?= base_url('admin/guru') ?>" class="p-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-900 rounded-lg flex items-center space-x-3 transition-colors">
                    <i class="fas fa-chalkboard-teacher text-indigo-600 text-lg"></i>
                    <span class="text-xs font-semibold">Data Tenaga Pendidik</span>
                </a>
                <a href="<?= base_url('admin/siswa') ?>" class="p-3 bg-blue-50 hover:bg-blue-100 text-blue-900 rounded-lg flex items-center space-x-3 transition-colors">
                    <i class="fas fa-user-graduate text-blue-600 text-lg"></i>
                    <span class="text-xs font-semibold">Data Peserta Didik</span>
                </a>
                <a href="<?= base_url('admin/laporan') ?>" class="p-3 bg-purple-50 hover:bg-purple-100 text-purple-900 rounded-lg flex items-center space-x-3 transition-colors">
                    <i class="fas fa-file-alt text-purple-600 text-lg"></i>
                    <span class="text-xs font-semibold">Laporan Kehadiran</span>
                </a>
                <a href="<?= base_url('admin/pengaturan') ?>" class="p-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 rounded-lg flex items-center space-x-3 transition-colors">
                    <i class="fas fa-cogs text-emerald-600 text-lg"></i>
                    <span class="text-xs font-semibold">Pengaturan Sekolah</span>
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
