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

                <!-- Tombol Rollover (selalu tampil) -->
                <form action="<?= base_url('admin/pengaturan/rollover') ?>" method="post" onsubmit="return confirm('Yakin akan menjalankan rollover? Data kelas siswa akan berubah. Backup otomatis dibuat untuk revert.')">
                    <?= csrf_field() ?>
                    <?php
                    $partsTA = explode('/', $activeTahunAjaran);
                    $nextTahunAjaran = ($partsTA[0] + 1) . '/' . ($partsTA[1] + 1);
                    ?>
                    <input type="hidden" name="tahun_ajaran" value="<?= $nextTahunAjaran ?>">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors text-sm font-medium">
                        <i class="fas fa-arrow-up mr-2"></i> Jalankan Rollover
                    </button>
                </form>

                <!-- History Rollover -->
                <?php if (!empty($rolloverHistory)): ?>
                <div class="mt-5 md:mt-6">
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2.5">Riwayat Rollover</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-2 px-2 text-gray-500 font-medium">Waktu</th>
                                    <th class="text-left py-2 px-2 text-gray-500 font-medium">Dari</th>
                                    <th class="text-left py-2 px-2 text-gray-500 font-medium">Ke</th>
                                    <th class="text-center py-2 px-2 text-gray-500 font-medium">Naik</th>
                                    <th class="text-center py-2 px-2 text-gray-500 font-medium">Lulus</th>
                                    <th class="text-center py-2 px-2 text-gray-500 font-medium">Status</th>
                                    <th class="text-center py-2 px-2 text-gray-500 font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rolloverHistory as $index => $history): ?>
                                <tr class="border-b border-gray-50 <?= $history['reverted_at'] ? 'opacity-50' : '' ?>">
                                    <td class="py-2 px-2 text-gray-600 whitespace-nowrap">
                                        <?= date('d M Y H:i', strtotime($history['created_at'])) ?>
                                    </td>
                                    <td class="py-2 px-2 text-gray-700 font-medium"><?= esc($history['from_year']) ?></td>
                                    <td class="py-2 px-2 text-gray-700 font-medium"><?= esc($history['to_year']) ?></td>
                                    <td class="py-2 px-2 text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-700 rounded-full text-[10px] font-bold">
                                            <?= $history['naik_kelas'] ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-2 text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold">
                                            <?= $history['lulus'] ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-2 text-center">
                                        <?php if ($history['reverted_at']): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-[10px] font-medium">
                                                <i class="fas fa-undo mr-1"></i>Reverted
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-medium">
                                                <i class="fas fa-check mr-1"></i>Aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2 px-2 text-center">
                                        <?php if (!$history['reverted_at'] && in_array($history['id'], $historyIdsWithBackup)): ?>
                                            <form action="<?= base_url('admin/pengaturan/revert') ?>" method="post" class="inline" onsubmit="return confirm('Yakin akan revert rollover dari <?= esc($history['from_year']) ?> ke <?= esc($history['to_year']) ?>? Semua perubahan akan dikembalikan.')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="history_id" value="<?= $history['id'] ?>">
                                                <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded text-[10px] font-medium transition-colors">
                                                    <i class="fas fa-undo mr-1"></i>Revert
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-gray-300 text-[10px]">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
                <p class="text-xs text-gray-500 truncate">Atur periode dan penomoran minggu jurnal PKL siswa</p>
            </div>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-8">
                <!-- Kiri: Form -->
                <form action="<?= base_url('admin/pengaturan/update-jurnal-pkl-period') ?>" method="post" id="formJurnalPkl" class="flex flex-col">
                    <?= csrf_field() ?>

                    <!-- Tanggal Mulai -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-day fa-sm"></i>
                            </div>
                            <input type="date" name="jurnal_pkl_start_date" value="<?= old('jurnal_pkl_start_date', $jurnalPklStartDate ?? '') ?>"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">
                            <?php if ($jurnalPklStartDate): ?>
                                Basis minggu ke-1. Kosongkan untuk ISO week.
                            <?php else: ?>
                                Basis penomoran minggu ke-1. Kosongkan = ISO week.
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Tanggal Akhir -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Akhir <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-check fa-sm"></i>
                            </div>
                            <input type="date" name="jurnal_pkl_end_date" value="<?= old('jurnal_pkl_end_date', $jurnalPklEndDate ?? '') ?>"
                                   min="<?= $jurnalPklStartDate ?? '' ?>"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">Batas akhir periode PKL untuk keperluan laporan.</p>
                    </div>

                    <!-- Hari Wajib per Minggu -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hari Wajib per Minggu</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-week fa-sm"></i>
                            </div>
                            <input type="number" name="jurnal_pkl_required_days" value="<?= old('jurnal_pkl_required_days', $jurnalPklRequiredDays ?? 5) ?>"
                                   min="1" max="7"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">Jumlah hari kerja wajib per minggu (contoh: 5 = Senin&ndash;Jumat). Digunakan untuk indikator kesiapan cetak jurnal.</p>
                    </div>

                    <!-- Info Ringkas -->
                    <?php if ($jurnalPklStartDate || $jurnalPklEndDate): ?>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-gray-500 bg-gray-50 rounded-lg px-3.5 py-2.5 border border-gray-100 mb-5">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-play-circle text-emerald-500"></i>
                            Mulai: <strong class="text-gray-700"><?= $jurnalPklStartDate ? date('d M', strtotime($jurnalPklStartDate)) . ' ' . date('Y', strtotime($jurnalPklStartDate)) : '—' ?></strong>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-stop-circle text-rose-400"></i>
                            Akhir: <strong class="text-gray-700"><?= $jurnalPklEndDate ? date('d M', strtotime($jurnalPklEndDate)) . ' ' . date('Y', strtotime($jurnalPklEndDate)) : '—' ?></strong>
                        </span>
                        <?php if ($jurnalPklDurationDays): ?>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-hourglass-half text-amber-500"></i>
                            <strong class="text-emerald-700"><?= $jurnalPklDurationDays ?> hari</strong> (<?= round($jurnalPklDurationDays / 7, 1) ?> mgg)
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Spacer -->
                    <div class="flex-1"></div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors text-sm font-medium shadow-sm">
                            <i class="fas fa-save mr-2"></i> Simpan Periode
                        </button>
                        <?php if ($jurnalPklStartDate || $jurnalPklEndDate): ?>
                        <button type="submit" name="clear" value="1"
                                class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium border border-gray-200"
                                onclick="return confirm('Reset semua pengaturan periode jurnal PKL?')">
                            <i class="fas fa-undo mr-2"></i> Reset
                        </button>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Kanan: Preview Kalender -->
                <div class="flex flex-col">
                    <div id="calendarPreviewWrapper" class="flex-1"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var today = '<?= date('Y-m-d') ?>';
        var weekDays = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        var input = document.querySelector('input[name="jurnal_pkl_start_date"]');
        var endDateInput = document.querySelector('input[name="jurnal_pkl_end_date"]');
        var wrapper = document.getElementById('calendarPreviewWrapper');
        var viewOffset = 0;

        function toMonday(dateStr) {
            var dt = new Date(dateStr + 'T00:00:00');
            var dow = dt.getDay();
            if (dow === 0) dow = 7;
            if (dow > 1) dt.setDate(dt.getDate() - (dow - 1));
            return dt;
        }

        function fmt(d) {
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var dd = String(d.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + dd;
        }

        function navigateWeeks(step) {
            viewOffset += step;
            renderPreview();
        }

        function goToToday() {
            viewOffset = 0;
            renderPreview();
        }

        function renderPreview() {
            var startDate = input.value || null;
            var endDate = (endDateInput && endDateInput.value) || null;
            if (!startDate) {
                wrapper.innerHTML = '<div class="h-full flex items-center justify-center">'
                    + '<div class="text-center text-gray-400 py-8">'
                    + '<i class="fas fa-calendar-day text-3xl md:text-4xl mb-3"></i>'
                    + '<p class="text-sm font-medium">Preview Kalender</p>'
                    + '<p class="text-xs mt-1">Atur tanggal mulai untuk melihat preview</p>'
                    + '</div></div>';
                return;
            }

            var weekBase = toMonday(startDate);
            var startDt = new Date(startDate + 'T00:00:00');
            var endDt = endDate ? new Date(endDate + 'T23:59:59') : null;

            var calStart = toMonday(today);
            calStart.setDate(calStart.getDate() + viewOffset * 7);

            var calStartStr = fmt(calStart);
            var calEnd = new Date(calStart);
            calEnd.setDate(calEnd.getDate() + 27);
            var calEndStr = fmt(calEnd);

            var showTodayBtn = viewOffset !== 0;

            var html = '<p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2.5">Preview Penomoran Minggu</p>'
                + '<div class="bg-gray-50 rounded-xl border border-gray-200 p-3 md:p-4">'

                + '<div class="flex items-center justify-between mb-2">'
                + '<button onclick="navigateWeeks(-4)" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-colors text-xs" title="4 minggu sebelumnya">'
                + '<i class="fas fa-chevron-left"></i>'
                + '</button>'
                + '<span class="text-[10px] md:text-xs text-gray-400 font-medium">' + calStartStr + ' — ' + calEndStr + '</span>'
                + '<button onclick="navigateWeeks(4)" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-colors text-xs" title="4 minggu selanjutnya">'
                + '<i class="fas fa-chevron-right"></i>'
                + '</button>'
                + '</div>'

                + '<div class="grid grid-cols-7 gap-1 md:gap-1.5 text-center">';

            weekDays.forEach(function (day) {
                html += '<div class="text-[10px] md:text-xs font-medium text-gray-400 py-1">' + day + '</div>';
            });

            for (var w = 0; w < 4; w++) {
                var weekStart = new Date(calStart);
                weekStart.setDate(weekStart.getDate() + w * 7);

                var weekNum = Math.floor((weekStart - weekBase) / 604800000) + 1;
                var isValidWeek = weekNum >= 1;

                for (var d = 0; d < 7; d++) {
                    var dayDt = new Date(weekStart);
                    dayDt.setDate(weekStart.getDate() + d);
                    var dayStr = fmt(dayDt);
                    var dayNum = dayDt.getDate();

                    var isToday = (dayStr === today);
                    var isBeforePKL = (dayDt < startDt);
                    var isAfterPKL = endDt && (dayDt > endDt);

                    var cls = 'relative px-0.5 md:px-1 py-1 md:py-1.5 rounded-lg text-[10px] md:text-xs font-medium cursor-pointer transition-colors ';
                    if (isToday) {
                        cls += 'bg-emerald-500 text-white shadow-sm';
                    } else if (isBeforePKL) {
                        cls += 'text-gray-300';
                    } else if (isAfterPKL) {
                        cls += 'text-gray-300 bg-gray-100/50';
                    } else {
                        cls += 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200';
                    }

                    html += '<div data-date="' + dayStr + '" onclick="setJurnalPklDate(this)" class="' + cls + '">';
                    html += dayNum;

                    if (d === 0 && isValidWeek) {
                        html += '<div class="absolute -top-2.5 md:-top-3 left-1/2 -translate-x-1/2 whitespace-nowrap">'
                            + '<span class="text-[8px] md:text-[10px] font-semibold text-emerald-600">M-' + weekNum + '</span>'
                            + '</div>';
                    }

                    html += '</div>';
                }
            }

            html += '</div></div>';

            html += '<div class="flex flex-wrap items-center gap-x-4 md:gap-x-6 gap-y-1 mt-2.5 md:mt-3 text-[10px] md:text-xs text-gray-500">'
                + '<span><span class="inline-block w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-emerald-500 align-middle mr-1"></span> Hari ini</span>'
                + '<span><span class="inline-block w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-emerald-50 ring-1 ring-emerald-200 align-middle mr-1"></span> Periode PKL</span>'
                + '<span><span class="inline-block w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-gray-200 align-middle mr-1"></span> Sebelum/Sesudah</span>';

            if (showTodayBtn) {
                html += '<button onclick="goToToday()" class="ml-auto inline-flex items-center px-2 py-0.5 text-emerald-600 hover:text-emerald-700 font-medium transition-colors">'
                    + '<i class="fas fa-crosshairs mr-1"></i> Hari ini'
                    + '</button>';
            }

            html += '</div>';

            wrapper.innerHTML = html;
        }

        window.setJurnalPklDate = function (el) {
            var date = el.dataset.date;
            if (!date) return;
            input.value = date;
            viewOffset = 0;
            renderPreview();
        };

        window.navigateWeeks = navigateWeeks;
        window.goToToday = goToToday;

        input.addEventListener('change', function () { viewOffset = 0; renderPreview(); });
        input.addEventListener('input', function () { viewOffset = 0; renderPreview(); });
        if (endDateInput) {
            endDateInput.addEventListener('change', function () { renderPreview(); });
            endDateInput.addEventListener('input', function () { renderPreview(); });
        }

        renderPreview();
    })();
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