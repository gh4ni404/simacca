<?php if (session()->get('is_impersonating')): ?>
    <div class="mb-4 bg-gradient-to-r from-amber-600 to-amber-700 text-white px-4 py-3 rounded-2xl shadow-lg flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3 text-sm font-semibold">
            <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-amber-100">
                <i class="fas fa-user-ninja"></i>
            </span>
            <div>
                <p class="font-bold">MODE SIMULASI TESTER GURU AKTIF</p>
                <p class="text-xs text-amber-100 font-normal">Bertindak sebagai: <strong><?= esc(session()->get('nama_lengkap')) ?></strong></p>
            </div>
        </div>
        <div class="flex items-center flex-wrap gap-2">
            <a href="<?= base_url('guru/absensi-shalat') ?>" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                <i class="fas fa-mosque"></i> Portal Absensi Shalat
            </a>
            <a href="<?= base_url('guru/jurnal-piket') ?>" class="px-3 py-1.5 bg-indigo-500 hover:bg-indigo-400 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                <i class="fas fa-clipboard-list"></i> Form Jurnal Piket
            </a>
            <a href="<?= base_url('admin/simulasi-piket/stop-impersonate') ?>" class="px-3 py-1.5 bg-white hover:bg-amber-50 text-amber-800 font-extrabold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                <i class="fas fa-undo"></i> Kembali ke Admin
            </a>
        </div>
    </div>
<?php endif; ?>

<?php
// Default: only show highest priority message
$showAll = isset($showAll) ? $showAll : false;

// Render alerts with priority
echo render_alerts($showAll);
?>
