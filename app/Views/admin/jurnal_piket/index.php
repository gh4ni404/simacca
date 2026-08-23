<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            <i class="fas fa-book-reader mr-3"></i>
                            Monitoring Jurnal Piket Guru
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-1">Rekapitulasi dan verifikasi laporan kegiatan piket harian seluruh guru</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('admin/jurnal-piket/print?start_date=' . esc($startDate) . '&end_date=' . esc($endDate) . '&guru_id=' . esc($selectedGuruId ?? '')) ?>" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-md transition">
                        <i class="fas fa-print mr-2"></i> Cetak Laporan
                    </a>
                </div>
            </div>
        </div>

        <?= view('components/alerts') ?>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <form method="GET" action="<?= base_url('admin/jurnal-piket') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="<?= esc($startDate ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="<?= esc($endDate ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Filter Guru</label>
                    <select name="guru_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">-- Semua Guru --</option>
                        <?php foreach ($guruList as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= ($selectedGuruId == $g['id']) ? 'selected' : '' ?>>
                                <?= esc($g['nama_lengkap']) ?> (<?= esc($g['nip']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl text-sm transition">
                        <i class="fas fa-search mr-1.5"></i> Cari
                    </button>
                    <?php if (!empty($startDate) || !empty($endDate) || !empty($selectedGuruId)): ?>
                        <a href="<?= base_url('admin/jurnal-piket') ?>" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition flex items-center justify-center">
                            <i class="fas fa-redo"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table / List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list mr-2 text-indigo-600"></i> Data Jurnal Piket
                </h2>
                <span class="text-xs text-gray-500">Total: <?= count($jurnalList) ?> laporan</span>
            </div>

            <?php if (empty($jurnalList)): ?>
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm font-medium">Tidak ada data jurnal piket untuk kriteria pencarian ini.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-4">No</th>
                                <th class="px-5 py-4">Tanggal</th>
                                <th class="px-5 py-4">Nama Guru</th>
                                <th class="px-5 py-4">Uraian Kegiatan</th>
                                <th class="px-5 py-4 text-center">Dokumentasi</th>
                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $no = 1; foreach ($jurnalList as $j): ?>
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-5 py-4 font-medium text-gray-800"><?= $no++ ?></td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-800"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></div>
                                        <div class="text-xs text-gray-400"><?= esc(ucfirst(date_to_indo($j['tanggal']))) ?></div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900"><?= esc($j['nama_lengkap']) ?></div>
                                        <div class="text-xs text-gray-400">NIP: <?= esc($j['nip'] ?: '-') ?></div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="line-clamp-2 max-w-md font-medium text-gray-800">
                                            <?= esc($j['deskripsi']) ?>
                                        </div>
                                        <?php if (!empty($j['catatan'])): ?>
                                            <span class="inline-block mt-1 text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-100">
                                                Catatan: <?= esc($j['catatan']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <?php if (!empty($j['foto_dokumentasi'])): ?>
                                            <a href="<?= base_url('files/jurnal-piket/' . $j['foto_dokumentasi']) ?>" target="_blank" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                                <i class="fas fa-image mr-1"></i> Lihat Foto
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 font-medium">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <a href="<?= base_url('admin/jurnal-piket/detail/' . $j['id']) ?>" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-lg transition">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
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
<?= $this->endSection() ?>
