<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Edit Absensi PKL</span>
                </h1>
                <p class="text-gray-600 flex items-center mt-1">
                    <i class="fas fa-info-circle mr-2 text-blue-500 text-sm"></i>
                    <span class="text-sm">Perbarui data absensi kehadiran siswa PKL</span>
                </p>
            </div>
        </div>
    </div>

    <?= render_flash_message() ?>

    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form action="<?= base_url('guru/absensi-pkl/update/' . $absensi['id']); ?>" method="post" id="absensiPklForm">
            <?= csrf_field(); ?>
            <input type="hidden" name="pembimbing_pkl_id" value="<?= esc($absensi['pembimbing_pkl_id']) ?>">

            <!-- Absensi Info Card -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-xl p-6 mb-8">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-blue-500 rounded-lg mr-3">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800">Informasi Absensi</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex items-center bg-white rounded-lg p-3 shadow-sm">
                        <div class="p-1.5 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-calendar-day text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Tanggal</p>
                            <p class="text-sm font-bold text-gray-800"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                        </div>
                    </div>
                    <div class="flex items-center bg-white rounded-lg p-3 shadow-sm">
                        <div class="p-1.5 bg-green-100 rounded-lg mr-3">
                            <i class="fas fa-building text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Tempat PKL</p>
                            <p class="text-sm font-bold text-gray-800"><?= esc($absensi['nama_perusahaan'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="flex items-center bg-white rounded-lg p-3 shadow-sm">
                        <div class="p-1.5 bg-purple-100 rounded-lg mr-3">
                            <i class="fas fa-user-tie text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Pembimbing</p>
                            <p class="text-sm font-bold text-gray-800"><?= esc($absensi['nama_pembimbing'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="flex items-center bg-white rounded-lg p-3 shadow-sm">
                        <div class="p-1.5 bg-indigo-100 rounded-lg mr-3">
                            <i class="fas fa-users text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Total Siswa</p>
                            <p class="text-sm font-bold text-gray-800"><?= count($siswaList) ?> Siswa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Tanggal & Keterangan -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg mr-3">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Edit Data Absensi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal" name="tanggal" value="<?= esc($absensi['tanggal']) ?>" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    <div class="flex items-end">
                        <div class="w-full px-4 py-3 bg-gray-100 rounded-xl border-2 border-gray-200">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">
                                <i class="fas fa-user-tie mr-1"></i> Pembimbing PKL
                            </label>
                            <p class="text-sm font-bold text-gray-800"><?= esc($absensi['nama_pembimbing'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="keterangan_umum" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2 text-yellow-500"></i> Keterangan Umum (Opsional)
                    </label>
                    <textarea id="keterangan_umum" name="keterangan_umum" rows="2"
                              placeholder="Catatan umum untuk hari ini..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"><?= esc($absensi['keterangan_umum'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Siswa List -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg mr-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Daftar Kehadiran Siswa</h3>
                    </div>
                    <div id="progressCounter" class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <span class="text-sm text-gray-600">Terisi: <span id="filledCount" class="font-bold text-blue-700"><?= count($details) ?></span> / <span id="totalCount" class="font-bold"><?= count($siswaList) ?></span></span>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-5 mb-6 shadow-sm">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-indigo-500 rounded-lg mr-3">
                                <i class="fas fa-bolt text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-indigo-900">Aksi Cepat</h4>
                                <p class="text-xs text-indigo-700">Set status untuk semua siswa sekaligus</p>
                            </div>
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            <button type="button" onclick="setAllStatus('hadir')" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                                <i class="fas fa-check-circle mr-1"></i> Semua Hadir
                            </button>
                            <button type="button" onclick="setAllStatus('izin')" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                                <i class="fas fa-file-alt mr-1"></i> Semua Izin
                            </button>
                            <button type="button" onclick="setAllStatus('sakit')" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                                <i class="fas fa-medkit mr-1"></i> Semua Sakit
                            </button>
                            <button type="button" onclick="setAllStatus('alpa')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                                <i class="fas fa-times-circle mr-1"></i> Semua Alpa
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Siswa Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIS</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status Kehadiran</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                                <?php
                                $existingDetails = [];
                                foreach ($details as $detail) {
                                    $existingDetails[$detail['siswa_id']] = $detail;
                                }

                                $no = 1;
                                foreach ($siswaList as $siswa):
                                    $sid = $siswa['siswa_id'];
                                    $detail = $existingDetails[$sid] ?? null;
                                    $currentStatus = $detail ? strtolower($detail['status']) : 'hadir';
                                    $currentKeterangan = $detail ? ($detail['keterangan'] ?? '') : '';
                                ?>
                                <tr class="hover:bg-gray-50" data-siswa-id="<?= $sid ?>">
                                    <td class="px-4 py-4 text-sm text-gray-500"><?= $no++ ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-900 font-medium"><?= esc($siswa['nis'] ?? '-') ?></td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900"><?= esc($siswa['nama_lengkap']) ?></td>
                                    <td class="px-4 py-4">
                                        <input type="hidden" name="siswa[<?= $sid ?>][status]" value="<?= $currentStatus ?>" class="status-input" data-siswa-id="<?= $sid ?>">
                                        <div class="flex gap-1 flex-wrap" data-siswa-id="<?= $sid ?>">
                                            <?php
                                            $statusOptionsArr = [
                                                'hadir'  => ['label' => 'Hadir', 'icon' => 'fa-check-circle'],
                                                'izin'   => ['label' => 'Izin', 'icon' => 'fa-file-alt'],
                                                'sakit'  => ['label' => 'Sakit', 'icon' => 'fa-medkit'],
                                                'alpa'   => ['label' => 'Alpa', 'icon' => 'fa-times-circle'],
                                            ];
                                            foreach ($statusOptionsArr as $value => $opt):
                                                $isSelected = ($currentStatus === $value);
                                            ?>
                                            <button type="button"
                                                class="status-btn px-3 py-1.5 border-2 rounded-lg font-semibold text-xs transition-all <?= $isSelected ? $statusStyles[$value]['active'] : $statusStyles[$value]['inactive'] ?>"
                                                data-siswa-id="<?= $sid ?>" data-status="<?= $value ?>"
                                                onclick="selectStatus('<?= $sid ?>', '<?= $value ?>')">
                                                <i class="fas <?= $opt['icon'] ?> mr-1"></i><?= $opt['label'] ?>
                                            </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="text" name="siswa[<?= $sid ?>][keterangan]" value="<?= esc($currentKeterangan) ?>" placeholder="Opsional"
                                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t-2 border-gray-200">
                <a href="<?= base_url('guru/absensi-pkl'); ?>"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit"
                        id="submitBtn"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const EDIT_BTN_BASE = 'status-btn px-3 py-1.5 border-2 rounded-lg font-semibold text-xs transition-all';

const statusStyles = {
    'hadir':  { active: 'bg-green-500 text-white border-green-600 shadow-md',  inactive: 'bg-white text-green-700 border-green-300 hover:bg-green-50', icon: 'fa-check-circle' },
    'izin':   { active: 'bg-blue-500 text-white border-blue-600 shadow-md',   inactive: 'bg-white text-blue-700 border-blue-300 hover:bg-blue-50',  icon: 'fa-file-alt' },
    'sakit':  { active: 'bg-yellow-500 text-white border-yellow-600 shadow-md', inactive: 'bg-white text-yellow-700 border-yellow-300 hover:bg-yellow-50', icon: 'fa-medkit' },
    'alpa':   { active: 'bg-red-500 text-white border-red-600 shadow-md',     inactive: 'bg-white text-red-700 border-red-300 hover:bg-red-50',    icon: 'fa-times-circle' }
};

function selectStatus(siswaId, status) {
    const hiddenInput = document.querySelector(`.status-input[data-siswa-id="${siswaId}"]`);
    if (hiddenInput) {
        hiddenInput.value = status;
        hiddenInput.setAttribute('data-manually-set', 'true');
    }

    const buttons = document.querySelectorAll(`.status-btn[data-siswa-id="${siswaId}"]`);
    buttons.forEach(btn => {
        const btnStatus = btn.getAttribute('data-status');
        const s = statusStyles[btnStatus];
        btn.className = EDIT_BTN_BASE + ' ' + (btnStatus === status ? s.active : s.inactive);
    });

    updateProgress();
}

function setAllStatus(status) {
    document.querySelectorAll('.status-input').forEach(input => {
        const siswaId = input.getAttribute('data-siswa-id');
        selectStatus(siswaId, status);
    });

    const labels = { hadir: 'Hadir', izin: 'Izin', sakit: 'Sakit', alpa: 'Alpa' };
    const notif = document.createElement('div');
    notif.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notif.innerHTML = `<div class="flex items-center"><i class="fas fa-check-circle mr-2"></i><span>Semua siswa di-set <strong>${labels[status]}</strong></span></div>`;
    document.body.appendChild(notif);
    setTimeout(() => { notif.style.opacity = '0'; notif.style.transition = 'opacity 0.3s'; setTimeout(() => notif.remove(), 300); }, 2000);
}

function updateProgress() {
    const inputs = document.querySelectorAll('.status-input');
    let filled = 0;
    inputs.forEach(i => { if (i.getAttribute('data-manually-set') === 'true') filled++; });
    document.getElementById('filledCount').textContent = filled;
}
</script>
<?= $this->endSection() ?>
