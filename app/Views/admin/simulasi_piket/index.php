<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-indigo-200 mb-3 border border-white/10">
                    <i class="fas fa-flask text-amber-400"></i> Admin Test Portal & End-to-End Simulator
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Simulasi & Test Piket Guru</h1>
                <p class="text-indigo-200 text-sm mt-1 max-w-2xl">
                    Uji coba alur tugas piket (Absensi Shalat & Pengisian Jurnal Piket) pada berbagai profil guru tanpa perlu relogin keluar dari akun Admin.
                </p>
            </div>
            <div class="absolute right-0 top-0 bottom-0 opacity-10 flex items-center pr-10 pointer-events-none">
                <i class="fas fa-vial text-9xl"></i>
            </div>
        </div>

        <?= view('components/alerts') ?>

        <!-- Selection Filter Box -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" action="<?= base_url('admin/simulasi-piket') ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="guru_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Guru untuk Diuji <span class="text-red-500">*</span></label>
                    <select name="guru_id" id="guru_id" onchange="this.form.submit()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 text-sm font-semibold text-gray-800">
                        <?php foreach ($guruList as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $selectedGuruId == $g['id'] ? 'selected' : '' ?>>
                                <?= esc($g['nama_lengkap']) ?> (NIP: <?= esc($g['nip'] ?: '-') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="tanggal" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Simulasi Piket</label>
                    <input type="date" name="tanggal" id="tanggal" value="<?= esc($tanggal) ?>" onchange="this.form.submit()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 text-sm font-medium">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-sync-alt"></i> Refresh Data
                    </button>
                    <?php if ($selectedGuru): ?>
                        <a href="<?= base_url('admin/simulasi-piket/impersonate/' . $selectedGuru['id']) ?>" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2" title="Switch tampilan menjadi guru ini">
                            <i class="fas fa-user-ninja"></i> Login Mode Guru
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if ($selectedGuru): ?>
            <!-- Teacher Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Card 1: Status Piket Shalat (Hari) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                                <i class="fas fa-mosque"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-base">Jadwal Piket Absensi Shalat</h3>
                                <p class="text-xs text-gray-500">Penentuan hari piket shalat berjamaah</p>
                            </div>
                        </div>
                        <?php
                        $dayEnglish = date('l', strtotime($tanggal));
                        $dayMap = ['Monday' => 'senin', 'Tuesday' => 'selasa', 'Wednesday' => 'rabu', 'Thursday' => 'kamis', 'Friday' => 'jumat', 'Saturday' => 'sabtu', 'Sunday' => 'minggu'];
                        $hariSimulasi = $dayMap[$dayEnglish] ?? 'senin';

                        $isScheduledToday = false;
                        foreach ($piketSchedules as $ps) {
                            if ($ps['hari'] === $hariSimulasi) {
                                $isScheduledToday = true;
                                break;
                            }
                        }
                        ?>
                        <?php if ($isScheduledToday): ?>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                                <i class="fas fa-check-circle mr-1"></i> Piket Hari Ini (<?= ucfirst($hariSimulasi) ?>)
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full border border-amber-200">
                                Libur Piket Shalat Hari Ini (<?= ucfirst($hariSimulasi) ?>)
                            </span>
                        <?php endif; ?>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Hari Bertugas Piket Shalat Guru Ini:</p>
                        <?php if (empty($piketSchedules)): ?>
                            <div class="p-3 bg-red-50 text-red-700 rounded-xl text-xs font-medium border border-red-100 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-base"></i>
                                Belum diatur jadwal piket shalat pada semester ini.
                            </div>
                        <?php else: ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($piketSchedules as $ps): ?>
                                    <span class="px-3 py-1.5 bg-indigo-50 text-indigo-700 font-bold rounded-xl text-xs border border-indigo-100 flex items-center gap-1.5">
                                        <i class="fas fa-calendar-day"></i> Hari <?= ucfirst($ps['hari']) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-gray-500">Uji Portal Presensi Shalat:</span>
                        <a href="<?= base_url('admin/simulasi-piket/impersonate/' . $selectedGuru['id']) ?>" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                            <i class="fas fa-play mr-1"></i> Test Portal Absensi Shalat
                        </a>
                    </div>
                </div>

                <!-- Card 2: Status Master Jobdesk -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-base">Master Jobdesk Piket</h3>
                                <p class="text-xs text-gray-500">Standing duty rincian panduan piket</p>
                            </div>
                        </div>
                        <?php if ($mappedJobdesk): ?>
                            <span class="px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full border border-purple-200">
                                Mapped (<?= esc($mappedJobdesk['kode_jobdesk']) ?>)
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                Tanpa Jobdesk
                            </span>
                        <?php endif; ?>
                    </div>

                    <div>
                        <?php if ($mappedJobdesk): ?>
                            <div class="bg-purple-50/50 p-3 rounded-xl border border-purple-100 space-y-1">
                                <p class="text-xs font-bold text-purple-900"><?= esc($mappedJobdesk['nama_jobdesk']) ?></p>
                                <p class="text-[11px] text-gray-600 whitespace-pre-line leading-relaxed"><?= esc($mappedJobdesk['rincian_tugas']) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-gray-50 text-gray-600 rounded-xl text-xs font-medium border border-gray-200">
                                Guru ini belum dipetakan ke Master Jobdesk di menu <a href="<?= base_url('admin/master-jobdesk') ?>" class="text-indigo-600 font-bold underline">Master Jobdesk</a>.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-gray-500">Kelola Jobdesk:</span>
                        <a href="<?= base_url('admin/master-jobdesk') ?>" class="text-indigo-600 font-semibold hover:underline flex items-center gap-1">
                            Master Jobdesk Admin <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Simulation Test Form: Jurnal Piket -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-edit mr-2 text-indigo-600"></i> Form Test Simulasi Jurnal Piket
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Uji coba pengisian Jurnal Piket atas nama <strong><?= esc($selectedGuru['nama_lengkap']) ?></strong> untuk tanggal <strong><?= esc(date('d F Y', strtotime($tanggal))) ?></strong></p>
                    </div>
                    <?php if ($jurnalHariIni): ?>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
                            <i class="fas fa-check mr-1"></i> Jurnal Sudah Diisi
                        </span>
                    <?php endif; ?>
                </div>

                <form action="<?= base_url('admin/simulasi-piket/simpan-jurnal') ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <?= csrf_field() ?>
                    <input type="hidden" name="guru_id" value="<?= esc($selectedGuru['id']) ?>">
                    <input type="hidden" name="tanggal" value="<?= esc($tanggal) ?>">

                    <!-- Panduan Rincian Tugas Auto-Loaded -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Rincian Panduan Tugas (Auto-Loaded dari Master Jobdesk / Service)
                        </label>
                        <textarea name="rincian_tugas" rows="4" readonly class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 text-xs font-mono leading-relaxed cursor-not-allowed"><?= esc($rincianTugasAuto ?: 'Belum ada rincian jobdesk piket yang dipetakan untuk guru ini.') ?></textarea>
                    </div>

                    <!-- Deskripsi Kegiatan -->
                    <div>
                        <label for="deskripsi" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Deskripsi Laporan / Catatan Kegiatan Piket <span class="text-red-500">*</span>
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" required placeholder="Tuliskan catatan pelaksanaan piket hari ini..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 text-sm"><?= esc($jurnalHariIni['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <!-- Foto Dokumentasi -->
                    <div>
                        <label for="foto" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Upload Foto Bukti / Dokumentasi Piket</label>
                        <input type="file" id="foto" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        <?php if ($jurnalHariIni && !empty($jurnalHariIni['foto'])): ?>
                            <div class="mt-3 flex items-center gap-3 bg-gray-50 p-2 rounded-xl border border-gray-200 w-fit">
                                <img src="<?= base_url('files/jurnal-piket/' . esc($jurnalHariIni['foto'])) ?>" alt="Foto Bukti" class="h-16 w-16 object-cover rounded-lg">
                                <div>
                                    <p class="text-xs font-bold text-gray-700">Foto Terupload saat ini</p>
                                    <a href="<?= base_url('files/jurnal-piket/' . esc($jurnalHariIni['foto'])) ?>" target="_blank" class="text-[11px] text-indigo-600 font-semibold hover:underline">Lihat Foto Ukuran Penuh</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i> <?= $jurnalHariIni ? 'Perbarui Simulasi Jurnal' : 'Simpan Simulasi Jurnal' ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>
