<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 p-4 md:p-6 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Jurnal Piket Guru</h1>
                <p class="text-sm text-gray-600 mt-1">Laporan piket harian oleh <?= esc($jurnal['nama_lengkap']) ?></p>
            </div>
            <a href="<?= base_url('admin/jurnal-piket') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                <i class="fas fa-arrow-left mr-1.5"></i> Kembali
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Banner -->
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
                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/20">
                    <div class="text-xs text-indigo-200 font-medium">Guru Piket</div>
                    <div class="text-base font-bold text-white"><?= esc($jurnal['nama_lengkap']) ?></div>
                    <div class="text-xs text-indigo-100">NIP: <?= esc($jurnal['nip'] ?: '-') ?></div>
                </div>
            </div>

            <!-- Details -->
            <div class="p-6 space-y-6">
                <?php if (!empty($jurnal['rincian_tugas'])): ?>
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                            <i class="fas fa-tasks mr-2 text-indigo-600"></i> Rincian Panduan Tugas
                        </h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed"><?= esc($jurnal['rincian_tugas']) ?></p>
                    </div>
                <?php endif; ?>

                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                        <i class="fas fa-align-left mr-2 text-indigo-600"></i> Uraian / Deskripsi Kegiatan Piket
                    </h3>
                    <div class="p-4 rounded-xl bg-indigo-50/40 border border-indigo-100 text-sm text-gray-800 whitespace-pre-line leading-relaxed font-medium"><?= esc($jurnal['deskripsi']) ?></div>
                </div>

                <?php if (!empty($jurnal['catatan'])): ?>
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-amber-500"></i> Catatan Kejadian Khusus
                        </h3>
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-100 text-sm text-amber-900 whitespace-pre-line"><?= esc($jurnal['catatan']) ?></div>
                    </div>
                <?php endif; ?>

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
