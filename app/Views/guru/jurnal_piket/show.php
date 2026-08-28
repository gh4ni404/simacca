<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 p-4 md:p-6 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Jurnal Piket</h1>
                <p class="text-sm text-gray-600 mt-1">Laporan kegiatan piket pada <?= date('d F Y', strtotime($jurnal['tanggal'])) ?></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('guru/jurnal-piket/cetak/' . $jurnal['id']) ?>" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-print"></i> Cetak Jurnal
                </a>
                <a href="<?= base_url('guru/jurnal-piket/edit/' . $jurnal['id']) ?>" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="<?= base_url('guru/jurnal-piket') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <?= view('components/alerts') ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Card Meta Header -->
            <div class="p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-indigo-200">
                        <?= esc(ucfirst(date_to_indo($jurnal['tanggal']))) ?>
                    </span>
                    <h2 class="text-2xl font-bold mt-0.5">
                        <?= format_tanggal_indo($jurnal['tanggal']) ?>
                    </h2>
                    <p class="text-xs text-indigo-100 mt-1">
                        Tahun Ajaran <?= esc($jurnal['tahun_ajaran']) ?> (Semester <?= esc(ucfirst($jurnal['semester'])) ?>)
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold">
                        <i class="fas fa-user-check mr-1.5"></i> <?= esc($jurnal['nama_lengkap']) ?>
                    </span>
                </div>
            </div>

            <!-- Content Details -->
            <div class="p-6 space-y-6">
                <!-- Rincian Panduan Tugas -->
                <?php if (!empty($jurnal['rincian_tugas'])): ?>
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                            <i class="fas fa-tasks mr-2 text-indigo-600"></i> Rincian Panduan Tugas
                        </h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed"><?= esc($jurnal['rincian_tugas']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Deskripsi Kegiatan -->
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                        <i class="fas fa-align-left mr-2 text-indigo-600"></i> Uraian / Deskripsi Kegiatan Piket
                    </h3>
                    <div class="p-4 rounded-xl bg-indigo-50/40 border border-indigo-100 text-sm text-gray-800 whitespace-pre-line leading-relaxed font-medium"><?= esc($jurnal['deskripsi']) ?></div>
                </div>

                <!-- Catatan Kejadian -->
                <?php if (!empty($jurnal['catatan'])): ?>
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-amber-500"></i> Catatan Kejadian Khusus
                        </h3>
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-100 text-sm text-amber-900 whitespace-pre-line"><?= esc($jurnal['catatan']) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Foto Dokumentasi -->
                <?php if (!empty($jurnal['foto_dokumentasi'])): ?>
                    <?php $fotos = explode(',', $jurnal['foto_dokumentasi']); ?>
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center">
                            <i class="fas fa-image mr-2 text-emerald-600"></i> Foto Dokumentasi Piket
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl">
                            <?php foreach ($fotos as $f): ?>
                                <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm bg-gray-50 p-1 flex items-center justify-center cursor-pointer hover:shadow-md transition-shadow">
                                    <img src="<?= base_url('files/jurnal-piket/' . trim($f)) ?>" alt="Foto Dokumentasi Piket" class="w-full h-28 object-cover rounded-lg" onclick="window.open(this.src, '_blank')">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer Meta -->
            <div class="p-4 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 flex justify-between items-center">
                <span>Dibuat: <?= date('d/m/Y H:i', strtotime($jurnal['created_at'])) ?> WITA</span>
                <?php if ($jurnal['updated_at'] && $jurnal['updated_at'] !== $jurnal['created_at']): ?>
                    <span>Diperbarui: <?= date('d/m/Y H:i', strtotime($jurnal['updated_at'])) ?> WITA</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
