<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <div class="flex items-start gap-4 mb-6">
        <a href="<?= base_url('instruktur/dashboard') ?>"
           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-all active:scale-95 flex-shrink-0 mt-0.5">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rekapan Progress Siswa</h1>
            <p class="text-sm text-gray-500"><?= count($siswaRekap) ?> siswa &middot; <?= $total ?> total progress</p>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <?php if (empty($siswaRekap)): ?>
        <div class="text-center py-16">
            <i class="fas fa-clipboard-list text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-sm">Belum ada progress</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Disetujui</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Menunggu</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Revisi</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Aktivitas Terakhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $no = 1; ?>
                    <?php foreach ($siswaRekap as $s): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500"><?= $no++ ?></td>
                        <td class="px-4 py-3">
                            <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $s['siswa_id']) ?>"
                               class="font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                                <?= esc($s['nama_lengkap']) ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= esc($s['nama_kelas'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-900"><?= (int) $s['total_progress'] ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $s['approved'] > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' ?>">
                                <?= (int) $s['approved'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= ($s['submitted'] + $s['verified']) > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-400' ?>">
                                <?= (int) ($s['submitted'] + $s['verified']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $s['revision'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-400' ?>">
                                <?= (int) $s['revision'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                            <?= $s['last_activity'] ? date('d M Y', strtotime($s['last_activity'])) : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>