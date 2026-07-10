<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="w-full space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl shadow-lg p-4 md:p-6 text-white">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 md:w-14 md:h-14 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-cog text-xl md:text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold"><?= $pageTitle ?></h1>
                <p class="text-indigo-200 text-xs md:text-sm mt-0.5"><?= $pageDescription ?></p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Card: Tahun Ajaran Aktif -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5 md:gap-3">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-alt text-indigo-600 text-sm md:text-base"></i>
            </div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate">Tahun Ajaran Aktif</h3>
                <p class="text-xs text-gray-500 truncate">Data siswa, jadwal, kegiatan</p>
            </div>
        </div>
        <div class="p-4 md:p-6 flex-1 flex flex-col">
            <form action="<?= base_url('admin/pengaturan/update') ?>" method="post" class="flex-1 flex flex-col">
                <?= csrf_field() ?>

                <div class="mb-4 flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Ajaran</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-calendar fa-sm"></i>
                        </div>
                        <input type="text" name="tahun_ajaran" value="<?= old('tahun_ajaran', $activeTahunAjaran) ?>"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="2028/2029" maxlength="9">
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">Format: <strong>YYYY/YYYY</strong> (contoh: 2028/2029)</p>
                    <?php if (session()->getFlashdata('errors')): ?>
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <p class="text-red-600 text-xs mt-1"><?= esc($err) ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="submit" class="self-start inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <!-- Card: Rollover Siswa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5 md:gap-3">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-arrow-up text-amber-600 text-sm md:text-base"></i>
            </div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate">Rollover Siswa</h3>
                <p class="text-xs text-gray-500 truncate">Naikkan kelas otomatis</p>
            </div>
        </div>
        <div class="p-4 md:p-6 flex-1 flex flex-col">
            <div class="flex-1 flex flex-col">
                <div class="grid grid-cols-3 gap-2 md:gap-3 mb-4 md:mb-5">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-xl p-3 md:p-4 text-center border border-blue-200/50">
                        <div class="text-lg md:text-2xl font-bold text-blue-700">X</div>
                        <div class="text-[10px] md:text-xs text-blue-600 mt-0.5 md:mt-1">
                            <i class="fas fa-arrow-right mr-0.5"></i>Naik <strong>XI</strong>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100/50 rounded-xl p-3 md:p-4 text-center border border-green-200/50">
                        <div class="text-lg md:text-2xl font-bold text-green-700">XI</div>
                        <div class="text-[10px] md:text-xs text-green-600 mt-0.5 md:mt-1">
                            <i class="fas fa-arrow-right mr-0.5"></i>Naik <strong>XII</strong>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 rounded-xl p-3 md:p-4 text-center border border-purple-200/50">
                        <div class="text-lg md:text-2xl font-bold text-purple-700">XII</div>
                        <div class="text-[10px] md:text-xs text-purple-600 mt-0.5 md:mt-1">
                            <i class="fas fa-arrow-right mr-0.5"></i><strong>Lulus</strong>
                        </div>
                    </div>
                </div>

                <?php if ($rolloverBackup && !empty($rolloverBackup['changes'])): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 md:p-5">
                        <div class="flex items-start gap-2.5 md:gap-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-history text-yellow-600 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs md:text-sm font-medium text-yellow-800 mb-0.5">Rollover Terakhir Tersimpan</p>
                                <p class="text-[10px] md:text-xs text-yellow-700 mb-2.5 md:mb-3">
                                    <?= esc($rolloverBackup['created_at'] ?? '') ?> &mdash;
                                    <?= count($rolloverBackup['changes']) ?> siswa
                                </p>
                                <form action="<?= base_url('admin/pengaturan/revert') ?>" method="post" onsubmit="return confirm('Yakin akan revert rollover? Semua perubahan akan dikembalikan.')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="inline-flex items-center px-3 md:px-4 py-1.5 md:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-xs md:text-sm font-medium">
                                        <i class="fas fa-undo mr-1.5"></i> Revert
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="<?= base_url('admin/pengaturan/rollover') ?>" method="post" onsubmit="return confirm('Yakin akan menjalankan rollover? Data kelas siswa akan berubah. Backup otomatis dibuat untuk revert.')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="tahun_ajaran" value="<?= $activeTahunAjaran ?>">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors text-sm font-medium">
                            <i class="fas fa-arrow-up mr-2"></i> Jalankan Rollover
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card: Pengaturan Jurnal PKL -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5 md:gap-3">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-book text-emerald-600 text-sm md:text-base"></i>
            </div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate">Pengaturan Jurnal PKL</h3>
                <p class="text-xs text-gray-500 truncate">Atur penomoran minggu untuk jurnal PKL siswa</p>
            </div>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8">
                <!-- Form -->
                <div>
                    <form action="<?= base_url('admin/pengaturan/update-jurnal-pkl-start') ?>" method="post">
                        <?= csrf_field() ?>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai Minggu ke-1</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-day fa-sm"></i>
                            </div>
                            <input type="date" name="jurnal_pkl_start_date" value="<?= old('jurnal_pkl_start_date', $jurnalPklStartDate ?? '') ?>"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">
                            <?php if ($jurnalPklStartDate): ?>
                                Saat ini: <strong><?= date('d M Y', strtotime($jurnalPklStartDate)) ?></strong>. Kosongkan + Simpan untuk menggunakan ISO week.
                            <?php else: ?>
                                Kosongkan untuk menggunakan ISO week number (Senin pekan pertama tahun).
                            <?php endif; ?>
                        </p>

                        <div class="flex items-center gap-3 mt-4">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors text-sm font-medium">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                            <?php if ($jurnalPklStartDate): ?>
                            <button type="submit" name="clear" value="1"
                                    class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium border border-gray-200"
                                    onclick="return confirm('Hapus pengaturan tanggal mulai minggu ke-1? Sistem akan kembali menggunakan ISO week number.')">
                                <i class="fas fa-undo mr-2"></i> Reset
                            </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Preview Kalender -->
                <div>
                    <?php if ($jurnalPklStartDate): ?>
                    <?php
                        $today = date('Y-m-d');
                        $weekNumber = get_week_number($today, $jurnalPklStartDate);
                        $weekBase = get_jurnal_pkl_week_base();
                        $weekDays = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                    ?>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2.5">Preview Penomoran Minggu</p>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-3 md:p-4">
                        <div class="grid grid-cols-7 gap-1 md:gap-1.5 text-center">
                            <?php foreach ($weekDays as $i => $day): ?>
                                <div class="text-[10px] md:text-xs font-medium text-gray-400 py-1"><?= $day ?></div>
                            <?php endforeach; ?>

                            <?php
                            $baseDt = new DateTime($weekBase);
                            $startDt = new DateTime($jurnalPklStartDate);

                            for ($w = 1; $w <= 4; $w++):
                                $weekStart = clone $baseDt;
                                $weekStart->modify('+' . (($w - 1) * 7) . ' days');
                                $isFirstWeek = ($w === 1);

                                for ($d = 0; $d < 7; $d++):
                                    $dayDt = clone $weekStart;
                                    $dayDt->modify('+' . $d . ' days');
                                    $dayStr = $dayDt->format('Y-m-d');
                                    $dayNum = $dayDt->format('j');

                                    $isBeforeStart = $isFirstWeek && $dayDt < $startDt;
                                    $isToday = ($dayStr === $today);
                                    $isCurrentWeek = ($w === $weekNumber);
                            ?>
                                <div data-date="<?= $dayStr ?>" onclick="setJurnalPklDate(this)"
                                     class="relative px-0.5 md:px-1 py-1 md:py-1.5 rounded-lg text-[10px] md:text-xs font-medium cursor-pointer transition-colors
                                        <?= $isBeforeStart ? 'text-gray-300' : ($isToday ? 'bg-emerald-500 text-white shadow-sm' : ($isCurrentWeek ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'text-gray-600 hover:bg-emerald-100')) ?>">
                                    <?= $dayNum ?>
                                    <?php if ($d === 0 && !$isBeforeStart): ?>
                                        <div class="absolute -top-2.5 md:-top-3 left-1/2 -translate-x-1/2 whitespace-nowrap">
                                            <span class="text-[8px] md:text-[10px] font-semibold <?= $isCurrentWeek ? 'text-emerald-600' : 'text-gray-400' ?>">M-<?= $w ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php
                                endfor;
                            endfor;
                            ?>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-x-4 md:gap-x-6 gap-y-1 mt-2.5 md:mt-3 text-[10px] md:text-xs text-gray-500">
                        <span><span class="inline-block w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-emerald-500 align-middle mr-1"></span> Hari ini</span>
                        <span><span class="inline-block w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-emerald-50 ring-1 ring-emerald-200 align-middle mr-1"></span> Minggu ini (M-<?= $weekNumber ?>)</span>
                        <span><span class="inline-block w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-gray-200 align-middle mr-1"></span> Sebelum PKL</span>
                    </div>
                    <?php else: ?>
                    <div class="h-full flex items-center justify-center">
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-calendar-day text-3xl md:text-4xl mb-3"></i>
                            <p class="text-sm font-medium">Preview Kalender</p>
                            <p class="text-xs mt-1">Atur tanggal mulai untuk melihat preview</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function setJurnalPklDate(el) {
        const date = el.dataset.date;
        if (!date) return;
        document.querySelector('input[name="jurnal_pkl_start_date"]').value = date;
        // Highlight clicked
        document.querySelectorAll('[data-date]').forEach(d => d.classList.remove('ring-2', 'ring-emerald-400'));
        el.classList.add('ring-2', 'ring-emerald-400');
    }
    </script>

    <!-- Rollover Result -->
    <?php $rolloverResult = session()->getFlashdata('rollover_result'); ?>
    <?php if ($rolloverResult): ?>
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5 md:gap-3">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-sm md:text-base"></i>
            </div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate">Hasil Rollover</h3>
                <p class="text-xs text-gray-500 truncate">Ringkasan proses rollover siswa</p>
            </div>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-3 gap-3 md:gap-4 mb-4 md:mb-6">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 md:p-5 text-center">
                    <div class="text-2xl md:text-3xl font-bold text-green-600"><?= $rolloverResult['naik_kelas'] ?></div>
                    <div class="flex items-center justify-center gap-1 mt-0.5">
                        <i class="fas fa-arrow-up text-green-500 text-[10px] md:text-xs"></i>
                        <p class="text-xs md:text-sm text-green-700 font-medium">Naik Kelas</p>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 md:p-5 text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-600"><?= $rolloverResult['lulus'] ?></div>
                    <div class="flex items-center justify-center gap-1 mt-0.5">
                        <i class="fas fa-graduation-cap text-blue-500 text-[10px] md:text-xs"></i>
                        <p class="text-xs md:text-sm text-blue-700 font-medium">Lulus</p>
                    </div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 md:p-5 text-center">
                    <div class="text-2xl md:text-3xl font-bold text-gray-600"><?= count($rolloverResult['skipped']) ?></div>
                    <div class="flex items-center justify-center gap-1 mt-0.5">
                        <i class="fas fa-minus-circle text-gray-400 text-[10px] md:text-xs"></i>
                        <p class="text-xs md:text-sm text-gray-600 font-medium">Dilewati</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if (!empty($rolloverResult['updated'])): ?>
                <details class="group">
                    <summary class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer hover:text-gray-900">
                        <i class="fas fa-chevron-right text-xs group-open:rotate-90 transition-transform"></i>
                        Detail Perubahan (<?= count($rolloverResult['updated']) ?> siswa)
                    </summary>
                    <div class="mt-3 max-h-48 overflow-y-auto bg-gray-50 rounded-xl p-3 border border-gray-200">
                        <?php foreach ($rolloverResult['updated'] as $item): ?>
                            <p class="text-xs text-gray-600 py-0.5 px-2"><?= esc($item) ?></p>
                        <?php endforeach; ?>
                    </div>
                </details>
                <?php endif; ?>

                <?php if (!empty($rolloverResult['skipped'])): ?>
                <details class="group">
                    <summary class="flex items-center gap-2 text-sm font-medium text-red-700 cursor-pointer hover:text-red-800">
                        <i class="fas fa-chevron-right text-xs group-open:rotate-90 transition-transform"></i>
                        Siswa Dilewati (<?= count($rolloverResult['skipped']) ?> siswa)
                    </summary>
                    <div class="mt-3 max-h-40 overflow-y-auto bg-red-50 rounded-xl p-3 border border-red-200">
                        <?php foreach ($rolloverResult['skipped'] as $item): ?>
                            <p class="text-xs text-red-600 py-0.5 px-2"><?= esc($item) ?></p>
                        <?php endforeach; ?>
                    </div>
                </details>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>