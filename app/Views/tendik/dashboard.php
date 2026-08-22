<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-r from-teal-700 via-emerald-700 to-indigo-800 rounded-2xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 flex items-center justify-center">
            <i class="fas fa-id-card text-9xl"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center space-x-3 mb-2">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold uppercase tracking-wider text-teal-100">
                    <i class="fas fa-id-badge mr-1"></i> Portal Tenaga Pendidik / Staf
                </span>
                <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-teal-100">
                    TA <?= esc($activeTA) ?>
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                <?= get_greeting() ?>, <?= esc($user['nama_lengkap'] ?? $user['username']) ?>
            </h1>
            <p class="text-teal-100 text-sm mt-1 max-w-2xl">
                Selamat datang di Portal Layanan Tenaga Pendidik & Staf Tata Usaha SIMACCA.
            </p>
        </div>
    </div>

    <!-- Quick Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Total Siswa Aktif</p>
                <h3 class="text-3xl font-extrabold text-gray-800 mt-1"><?= number_format($stats['total_siswa']) ?></h3>
                <p class="text-xs text-teal-600 mt-1 font-medium"><i class="fas fa-user-graduate mr-1"></i> Terdaftar di TA <?= esc($activeTA) ?></p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Total Kelas</p>
                <h3 class="text-3xl font-extrabold text-gray-800 mt-1"><?= number_format($stats['total_kelas']) ?></h3>
                <p class="text-xs text-indigo-600 mt-1 font-medium"><i class="fas fa-door-open mr-1"></i> Rombongan Belajar</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-school"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Status Staf</p>
                <h3 class="text-lg font-bold text-emerald-600 mt-1">Aktif</h3>
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-check-circle mr-1"></i> Terverifikasi Sistem</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
