<?= $this->extend('templates/main_layout'); ?>

<?= $this->section('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Statistik Kelas</h1>
    <p class="text-gray-600">Analisis data kelas dan distribusi siswa</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-600">Total Kelas</p>
        <p class="text-3xl font-bold text-gray-800"><?= count($kelasStats ?? []); ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-600">Total Jurusan</p>
        <p class="text-3xl font-bold text-gray-800"><?= count($jurusanStats ?? []); ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-600">Tingkat</p>
        <p class="text-3xl font-bold text-gray-800"><?= count($tingkatStats ?? []); ?></p>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan per Kelas</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Siswa</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% Kapasitas</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach (($kelasStats ?? []) as $k): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($k['nama_kelas']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($k['wali_kelas']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($k['tingkat']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($k['jurusan']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (int)($k['jumlah_siswa'] ?? 0); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (float)($k['persentase'] ?? 0); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribusi per Jurusan</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kelas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Siswa</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach (($jurusanStats ?? []) as $jur => $val): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($jur); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (int)$val['total_kelas']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (int)$val['total_siswa']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribusi per Tingkat</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kelas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Siswa</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach (($tingkatStats ?? []) as $tingkat => $val): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($tingkat); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (int)$val['total_kelas']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (int)$val['total_siswa']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>