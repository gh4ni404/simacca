<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-slate-50/70 p-4 md:p-6 lg:p-8 space-y-6">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- ==================== HEADER ==================== -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 text-lg">
                        <i class="fas fa-hands-helping"></i>
                    </span>
                    Jurnal Guru Wali
                </h1>
                <p class="text-xs md:text-sm text-gray-500 mt-1">
                    Pendampingan dan pencatatan bimbingan personal siswa binaan hingga tamat sekolah
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?= base_url('guru/jurnal-wali/cetak' . (!empty($filters['siswa_id']) ? '?siswa_id=' . (int)$filters['siswa_id'] : '')) ?>" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-xl border border-gray-300 shadow-sm transition-all hover:border-gray-400">
                    <i class="fas fa-print mr-2 text-blue-600"></i> Preview & Cetak PDF
                </a>
            </div>
        </div>

        <!-- Alerts -->
        <?= view('components/alerts') ?>

        <!-- ==================== TOP ROW: 2 PANELS ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- PANEL KIRI: SISWA BIMBINGAN (4 Cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-5 shadow-sm border border-gray-100 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Siswa bimbingan</h2>
                        <p class="text-xs text-gray-400">Total <?= count($siswaBinaan) ?> siswa binaan aktif</p>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-full border border-blue-100">
                        <?= count($siswaBinaan) ?> Siswa
                    </span>
                </div>

                <!-- Live Search Siswa Binaan -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" id="searchSiswaInput" oninput="filterMenteeList(this.value)" placeholder="Cari nama siswa atau NIS..." class="w-full pl-9 pr-3 text-xs rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 py-2 transition-all">
                </div>

                <!-- List Siswa Binaan -->
                <div class="space-y-2 max-h-[460px] overflow-y-auto pr-1" id="menteeListContainer">
                    <?php if (empty($siswaBinaan)): ?>
                        <div class="text-center py-10 text-gray-400 bg-slate-50/50 rounded-xl p-4 border border-dashed border-gray-200">
                            <i class="fas fa-user-friends text-3xl mb-2 text-gray-300 block"></i>
                            <p class="text-xs font-semibold text-gray-600">Belum ada siswa binaan</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Admin belum menugaskan siswa ke Anda sebagai Guru Wali.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($siswaBinaan as $idx => $mb): ?>
                            <div class="mentee-card p-3 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50/30 transition-all cursor-pointer flex items-center justify-between gap-3 group" 
                                 data-id="<?= $mb['siswa_id'] ?>" 
                                 data-name="<?= esc($mb['nama_siswa']) ?>"
                                 data-search="<?= strtolower(esc($mb['nama_siswa'] . ' ' . $mb['nis'] . ' ' . ($mb['nama_kelas'] ?? ''))) ?>"
                                 onclick="selectMenteeForForm(<?= $mb['siswa_id'] ?>)">
                                <div class="flex items-center space-x-3 min-w-0 flex-1">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-sm overflow-hidden">
                                        <?php if (!empty($mb['siswa_foto'])): ?>
                                            <img src="<?= base_url('uploads/profile/' . $mb['siswa_foto']) ?>" class="w-full h-full object-cover" alt="Foto">
                                        <?php else: ?>
                                            <?= strtoupper(substr($mb['nama_siswa'], 0, 2)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                                            <?= esc($mb['nama_siswa']) ?>
                                        </p>
                                        <p class="text-[11px] text-gray-500 truncate mt-0.5">
                                            <?= esc($mb['nama_kelas'] ?? 'Kelas -') ?> • NIS <?= esc($mb['nis']) ?>
                                        </p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-500 text-xs transition-transform group-hover:translate-x-0.5"></i>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PANEL KANAN: CATAT KEGIATAN BIMBINGAN (8 Cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-gray-100 space-y-5">
                <div class="pb-3 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Catat kegiatan bimbingan</h2>
                    <p class="text-xs text-gray-400">Dokumentasikan sesi bimbingan individual siswa secara berkala</p>
                </div>

                <form id="createJurnalForm" onsubmit="submitCreateJurnal(event)" class="space-y-4">
                    <?= csrf_field() ?>

                    <!-- Row 1: Tanggal & Siswa -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Tanggal <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" name="tanggal" id="inputTanggal" required value="<?= date('Y-m-d') ?>" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 font-medium text-gray-800 shadow-sm transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Siswa <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="siswa_id" id="inputSiswaId" required class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-3.5 py-2.5 appearance-none pr-8 font-medium text-gray-800 shadow-sm cursor-pointer">
                                    <option value="">Pilih siswa</option>
                                    <?php foreach ($siswaBinaan as $mb): ?>
                                        <option value="<?= $mb['siswa_id'] ?>">
                                            <?= esc($mb['nama_siswa']) ?> (<?= esc($mb['nama_kelas'] ?? '-') ?> • <?= esc($mb['nis']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Jenis Bimbingan & Catatan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Jenis Bimbingan <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="jenis_bimbingan" id="inputJenis" required class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-3.5 py-2.5 appearance-none pr-8 font-medium text-gray-800 shadow-sm cursor-pointer">
                                    <option value="">Pilih jenis</option>
                                    <option value="Akademik" selected>Akademik</option>
                                    <option value="Kedisiplinan">Kedisiplinan & Tata Tertib</option>
                                    <option value="Pribadi">Pribadi</option>
                                    <option value="Sosial">Sosial & Pertemanan</option>
                                    <option value="Karir">Karir & Minat Bakat</option>
                                    <option value="Kehadiran">Kehadiran & Absensi</option>
                                    <option value="Prestasi">Pengembangan Prestasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Catatan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="catatan" id="inputCatatan" rows="3" required placeholder="Catatan hasil diskusi, observasi, atau kendala siswa..." class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-3.5 py-2.5 transition-all shadow-sm resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Row 3: Tindak Lanjut -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Tindak Lanjut
                        </label>
                        <textarea name="tindak_lanjut" id="inputTindakLanjut" rows="2" placeholder="Rencana aksi, arahan, atau solusi yang disepakati bersama siswa..." class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-3.5 py-2.5 transition-all shadow-sm resize-none"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" id="btnSubmitJurnal" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-600/20 transition-all flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Jurnal Bimbingan
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- ==================== BOTTOM ROW: RIWAYAT BIMBINGAN SISWA ==================== -->
        <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-gray-100 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pb-3 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Riwayat bimbingan siswa</h2>
                    <p class="text-xs text-gray-400">Histori seluruh sesi bimbingan personal yang telah Anda catat</p>
                </div>

                <!-- Filter Controls -->
                <form method="GET" action="<?= base_url('guru/jurnal-wali') ?>" class="flex flex-wrap items-center gap-2">
                    <select name="siswa_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-gray-200 bg-slate-50 focus:bg-white focus:border-blue-500 px-3 py-1.5">
                        <option value="">Semua Siswa</option>
                        <?php foreach ($siswaBinaan as $mb): ?>
                            <option value="<?= $mb['siswa_id'] ?>" <?= (!empty($filters['siswa_id']) && $filters['siswa_id'] == $mb['siswa_id']) ? 'selected' : '' ?>>
                                <?= esc($mb['nama_siswa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="jenis_bimbingan" onchange="this.form.submit()" class="text-xs rounded-xl border border-gray-200 bg-slate-50 focus:bg-white focus:border-blue-500 px-3 py-1.5">
                        <option value="">Semua Jenis</option>
                        <option value="Akademik" <?= (!empty($filters['jenis_bimbingan']) && $filters['jenis_bimbingan'] == 'Akademik') ? 'selected' : '' ?>>Akademik</option>
                        <option value="Kedisiplinan" <?= (!empty($filters['jenis_bimbingan']) && $filters['jenis_bimbingan'] == 'Kedisiplinan') ? 'selected' : '' ?>>Kedisiplinan</option>
                        <option value="Pribadi" <?= (!empty($filters['jenis_bimbingan']) && $filters['jenis_bimbingan'] == 'Pribadi') ? 'selected' : '' ?>>Pribadi</option>
                        <option value="Sosial" <?= (!empty($filters['jenis_bimbingan']) && $filters['jenis_bimbingan'] == 'Sosial') ? 'selected' : '' ?>>Sosial</option>
                        <option value="Karir" <?= (!empty($filters['jenis_bimbingan']) && $filters['jenis_bimbingan'] == 'Karir') ? 'selected' : '' ?>>Karir</option>
                        <option value="Kehadiran" <?= (!empty($filters['jenis_bimbingan']) && $filters['jenis_bimbingan'] == 'Kehadiran') ? 'selected' : '' ?>>Kehadiran</option>
                        <option value="Prestasi" <?= (!empty($filters['jenis_bimbingan']) && $filters['jenis_bimbingan'] == 'Prestasi') ? 'selected' : '' ?>>Prestasi</option>
                    </select>

                    <?php if (!empty($filters['siswa_id']) || !empty($filters['jenis_bimbingan']) || !empty($filters['search'])): ?>
                        <a href="<?= base_url('guru/jurnal-wali') ?>" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-xl transition-colors">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table Riwayat -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-gray-500 font-bold uppercase tracking-wider text-left">
                            <th class="px-4 py-3 rounded-l-xl">Tanggal</th>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3 text-center">Jenis</th>
                            <th class="px-4 py-3">Catatan</th>
                            <th class="px-4 py-3">Tindak Lanjut</th>
                            <th class="px-4 py-3 text-center rounded-r-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 font-normal text-gray-800">
                        <?php if (empty($jurnalList)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                    <i class="fas fa-book-open text-3xl mb-2 text-gray-300 block"></i>
                                    <p class="font-semibold text-gray-600">Belum ada riwayat bimbingan.</p>
                                    <p class="text-gray-400 text-xs mt-0.5">Gunakan formulir di atas untuk mulai mencatat bimbingan siswa.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($jurnalList as $j): ?>
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-4 py-3.5 font-mono text-gray-600 whitespace-nowrap">
                                        <?= date('Y-m-d', strtotime($j['tanggal'])) ?>
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-gray-900 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">
                                                <?= strtoupper(substr($j['nama_siswa'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p><?= esc($j['nama_siswa']) ?></p>
                                                <p class="text-[10px] text-gray-400 font-normal"><?= esc($j['nama_kelas'] ?? '-') ?> • <?= esc($j['nis']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <?php
                                        $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                                        if ($j['jenis_bimbingan'] === 'Kedisiplinan') $badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                                        elseif ($j['jenis_bimbingan'] === 'Prestasi') $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        elseif ($j['jenis_bimbingan'] === 'Karir') $badgeBg = 'bg-purple-50 text-purple-700 border-purple-200';
                                        elseif ($j['jenis_bimbingan'] === 'Pribadi') $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                                        ?>
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $badgeBg ?>">
                                            <?= esc($j['jenis_bimbingan']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 max-w-xs text-gray-700">
                                        <p class="line-clamp-2" title="<?= esc($j['catatan']) ?>"><?= esc($j['catatan']) ?></p>
                                    </td>
                                    <td class="px-4 py-3.5 max-w-xs text-gray-600">
                                        <p class="line-clamp-2" title="<?= esc($j['tindak_lanjut'] ?: '-') ?>"><?= esc($j['tindak_lanjut'] ?: '-') ?></p>
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <button type="button" 
                                                    onclick="openEditJurnalModal(<?= htmlspecialchars(json_encode($j), ENT_QUOTES, 'UTF-8') ?>)" 
                                                    class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Jurnal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" 
                                                    onclick="deleteJurnal(<?= $j['id'] ?>)" 
                                                    class="px-2.5 py-1 text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 font-bold rounded-lg transition-colors border border-rose-200" title="Hapus Jurnal">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ==================== MODAL EDIT JURNAL ==================== -->
<div id="editJurnalModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 animate-scaleUp">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Jurnal Bimbingan
            </h3>
            <button type="button" onclick="closeEditJurnalModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editJurnalForm" onsubmit="submitUpdateJurnal(event)" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" id="editJurnalId" name="id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="editTanggal" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Siswa</label>
                    <select name="siswa_id" id="editSiswaId" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                        <?php foreach ($siswaBinaan as $mb): ?>
                            <option value="<?= $mb['siswa_id'] ?>"><?= esc($mb['nama_siswa']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenis Bimbingan</label>
                <select name="jenis_bimbingan" id="editJenis" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                    <option value="Akademik">Akademik</option>
                    <option value="Kedisiplinan">Kedisiplinan & Tata Tertib</option>
                    <option value="Pribadi">Pribadi</option>
                    <option value="Sosial">Sosial & Pertemanan</option>
                    <option value="Karir">Karir & Minat Bakat</option>
                    <option value="Kehadiran">Kehadiran & Absensi</option>
                    <option value="Prestasi">Pengembangan Prestasi</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan</label>
                <textarea name="catatan" id="editCatatan" rows="3" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tindak Lanjut</label>
                <textarea name="tindak_lanjut" id="editTindakLanjut" rows="2" class="w-full text-xs rounded-xl border border-gray-200 p-2.5"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeEditJurnalModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" id="btnUpdateJurnal" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ==================== CSRF MANAGEMENT ====================
const CSRF_TOKEN = '<?= csrf_token() ?>';
let csrfHash = '<?= csrf_hash() ?>';

function updateCsrf(newHash) {
    if (newHash) {
        csrfHash = newHash;
        document.querySelectorAll(`input[name="${CSRF_TOKEN}"]`).forEach(el => el.value = newHash);
    }
}

// ==================== MENTEE SELECTION & SEARCH ====================
function filterMenteeList(query) {
    const term = (query || '').toLowerCase().trim();
    const cards = document.querySelectorAll('.mentee-card');
    cards.forEach(card => {
        const search = card.getAttribute('data-search') || '';
        card.style.display = (!term || search.includes(term)) ? 'flex' : 'none';
    });
}

function selectMenteeForForm(siswaId) {
    const select = document.getElementById('inputSiswaId');
    if (select) {
        select.value = siswaId;
        
        // Highlight active card
        document.querySelectorAll('.mentee-card').forEach(c => {
            c.classList.remove('bg-blue-50/90', 'border-blue-300', 'ring-2', 'ring-blue-500/20');
        });
        const activeCard = document.querySelector(`.mentee-card[data-id="${siswaId}"]`);
        if (activeCard) {
            activeCard.classList.add('bg-blue-50/90', 'border-blue-300', 'ring-2', 'ring-blue-500/20');
        }

        // Focus catatan
        document.getElementById('inputCatatan')?.focus();
    }
}

// ==================== SUBMIT CREATE JURNAL ====================
async function submitCreateJurnal(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitJurnal');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const form = document.getElementById('createJurnalForm');
    const formData = new FormData(form);
    formData.set(CSRF_TOKEN, csrfHash);

    try {
        const response = await fetch('<?= base_url('guru/jurnal-wali/simpan') ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        });
        const res = await response.json();
        if (res.csrf_hash) updateCsrf(res.csrf_hash);

        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Gagal menyimpan jurnal bimbingan');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan Jurnal Bimbingan';
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Jurnal Bimbingan';
    }
}

// ==================== EDIT JURNAL MODAL ====================
function openEditJurnalModal(jurnal) {
    document.getElementById('editJurnalId').value = jurnal.id;
    document.getElementById('editTanggal').value = jurnal.tanggal;
    document.getElementById('editSiswaId').value = jurnal.siswa_id;
    document.getElementById('editJenis').value = jurnal.jenis_bimbingan;
    document.getElementById('editCatatan').value = jurnal.catatan;
    document.getElementById('editTindakLanjut').value = jurnal.tindak_lanjut || '';
    document.getElementById('editJurnalModal').classList.remove('hidden');
}

function closeEditJurnalModal() {
    document.getElementById('editJurnalModal').classList.add('hidden');
}

async function submitUpdateJurnal(e) {
    e.preventDefault();
    const id = document.getElementById('editJurnalId').value;
    const btn = document.getElementById('btnUpdateJurnal');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const form = document.getElementById('editJurnalForm');
    const formData = new FormData(form);
    formData.set(CSRF_TOKEN, csrfHash);

    try {
        const response = await fetch(`<?= base_url('guru/jurnal-wali/update') ?>/${id}`, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        });
        const res = await response.json();
        if (res.csrf_hash) updateCsrf(res.csrf_hash);

        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Gagal memperbarui jurnal');
            btn.disabled = false;
            btn.innerHTML = 'Simpan Perubahan';
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = 'Simpan Perubahan';
    }
}

// ==================== DELETE JURNAL ====================
async function deleteJurnal(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus catatan bimbingan ini?')) {
        return;
    }

    const formData = new FormData();
    formData.append(CSRF_TOKEN, csrfHash);

    try {
        const response = await fetch(`<?= base_url('guru/jurnal-wali/hapus') ?>/${id}`, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        });
        const res = await response.json();
        if (res.csrf_hash) updateCsrf(res.csrf_hash);

        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Gagal menghapus jurnal');
        }
    } catch (err) {
        alert('Terjadi kesalahan: ' + err.message);
    }
}
</script>
<?= $this->endSection() ?>
