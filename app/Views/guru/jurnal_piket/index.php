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
                            <i class="fas fa-clipboard-list mr-3"></i>
                            Jurnal Piket Guru
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-1">Dokumentasi dan laporan kegiatan tugas piket harian</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('guru/jurnal-piket/cetak' . (!empty($startDate) || !empty($endDate) ? '?start_date=' . esc($startDate) . '&end_date=' . esc($endDate) : '')) ?>" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium rounded-xl shadow-sm transition">
                        <i class="fas fa-print mr-2 text-indigo-600"></i> Cetak Laporan
                    </a>
                    <a href="<?= base_url('guru/jurnal-piket/tambah') ?>" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg hover:shadow-indigo-500/25 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-2"></i> Isi Jurnal Piket
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Component -->
        <?= view('components/alerts') ?>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <form method="GET" action="<?= base_url('guru/jurnal-piket') ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="<?= esc($startDate ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="<?= esc($endDate ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl text-sm transition">
                        <i class="fas fa-filter mr-1.5"></i> Filter
                    </button>
                    <?php if (!empty($startDate) || !empty($endDate)): ?>
                        <a href="<?= base_url('guru/jurnal-piket') ?>" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition flex items-center justify-center">
                            <i class="fas fa-redo mr-1"></i> Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Journal List -->
        <?php if (empty($jurnalList)): ?>
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Belum ada jurnal piket</h3>
                <p class="text-gray-500 text-sm mt-1 mb-6">Silakan isi jurnal kegiatan piket harian Anda.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($jurnalList as $row): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col justify-between">
                        <div>
                            <!-- Card Header -->
                            <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">
                                        <?= esc(ucfirst(date_to_indo($row['tanggal']))) ?>
                                    </div>
                                    <div class="text-sm font-bold text-gray-800 mt-0.5">
                                        <?= format_tanggal_indo($row['tanggal']) ?>
                                    </div>
                                </div>
                                <div>
                                    <?php if (!empty($row['foto_dokumentasi'])): ?>
                                        <?php 
                                        $fotos = explode(',', $row['foto_dokumentasi']);
                                        $count = count($fotos);
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-camera mr-1"></i> <?= $count > 1 ? $count . ' Foto' : 'Foto' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <i class="fas fa-file-text mr-1"></i> Teks
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 space-y-3">
                                <?php if (!empty($row['rincian_tugas'])): ?>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rincian Tugas:</span>
                                        <p class="text-xs text-gray-700 mt-0.5 line-clamp-2 italic">
                                            <?= nl2br(esc($row['rincian_tugas'])) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi Kegiatan:</span>
                                    <p class="text-sm text-gray-800 font-medium mt-1 line-clamp-3">
                                        <?= esc($row['deskripsi']) ?>
                                    </p>
                                </div>

                                <?php if (!empty($row['catatan'])): ?>
                                    <div class="p-2.5 rounded-lg bg-amber-50/70 border border-amber-100 text-xs text-amber-800">
                                        <i class="fas fa-sticky-note mr-1 text-amber-600"></i>
                                        <?= esc($row['catatan']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Footer / Actions -->
                        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2">
                            <a href="<?= base_url('guru/jurnal-piket/detail/' . $row['id']) ?>" class="px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 rounded-lg transition inline-flex items-center gap-1">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <div class="flex items-center gap-1">
                                <a href="<?= base_url('guru/jurnal-piket/cetak/' . $row['id']) ?>" target="_blank" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg text-xs transition" title="Cetak Lembar Jurnal">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="<?= base_url('guru/jurnal-piket/edit/' . $row['id']) ?>" class="p-2 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg text-xs transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" onclick="confirmDelete('<?= base_url('guru/jurnal-piket/hapus/' . $row['id']) ?>')" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg text-xs transition" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(url) {
    Swal.fire({
        title: 'Hapus Jurnal Piket?',
        text: 'Data laporan jurnal piket ini akan dihapus secara permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-2xl shadow-2xl border border-gray-100',
            confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm',
            cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

<?php if (session()->getFlashdata('success')): ?>
document.addEventListener('DOMContentLoaded', function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    Toast.fire({
        icon: 'success',
        title: <?= json_encode(session()->getFlashdata('success')) ?>
    });
});
<?php endif; ?>
</script>
<?= $this->endSection() ?>
