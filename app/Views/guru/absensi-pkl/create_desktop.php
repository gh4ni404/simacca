<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-clipboard-check text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Input Absensi PKL</span>
                </h1>
                <p class="text-gray-600 flex items-center mt-1">
                    <i class="fas fa-info-circle mr-2 text-blue-500 text-sm"></i>
                    <span class="text-sm">Catat kehadiran siswa bimbingan PKL</span>
                </p>
            </div>
        </div>
    </div>

    <?= render_flash_message() ?>

    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form action="<?= base_url('guru/absensi-pkl/simpan'); ?>" method="post" id="absensiPklForm">
            <?= csrf_field(); ?>

            <?php if (empty($pembimbingList)): ?>
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-yellow-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Pembimbingan PKL</h3>
                <p class="text-gray-600 mb-4">Anda belum ditugaskan sebagai pembimbing PKL tahun ini.</p>
                <a href="<?= base_url('guru/absensi-pkl'); ?>" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-xl transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
            <?php else: ?>

            <!-- Pembimbing & Tanggal Selection -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg mr-3">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Informasi Absensi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="pembimbing_pkl_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-tie mr-2 text-indigo-500"></i> Pembimbing PKL <span class="text-red-500">*</span>
                        </label>
                        <select id="pembimbing_pkl_id" name="pembimbing_pkl_id" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
        <option value="">Pilih Tempat PKL</option>
        <?php foreach ($pembimbingList as $p): ?>
        <option value="<?= $p['id'] ?>" data-tempat="<?= esc($p['tempat_pkl_id']) ?>">
            <?= esc($p['nama_perusahaan'] ?? 'Tempat PKL') ?>
        </option>
        <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal" name="tanggal" value="<?= esc($tanggal) ?>" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="mt-4">
                    <label for="keterangan_umum" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2 text-yellow-500"></i> Keterangan Umum (Opsional)
                    </label>
                    <textarea id="keterangan_umum" name="keterangan_umum" rows="2"
                              placeholder="Catatan umum untuk hari ini..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"></textarea>
                </div>
            </div>

            <!-- Siswa List -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg mr-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Daftar Siswa Bimbingan</h3>
                    </div>
                    <div id="progressCounter" class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <span class="text-sm text-gray-600">Terisi: <span id="filledCount" class="font-bold text-blue-700">0</span> / <span id="totalCount" class="font-bold">0</span></span>
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
                            <button type="button" onclick="setAllStatus('libur')" class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                                <i class="fas fa-umbrella-beach mr-1"></i> Semua Libur
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
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status Kehadiran</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jam Masuk</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jam Pulang</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-4 bg-gray-100 rounded-full mb-3">
                                                <i class="fas fa-hand-pointer text-gray-400 text-3xl"></i>
                                            </div>
                                            <p class="text-gray-600 font-medium">Pilih Pembimbing PKL terlebih dahulu</p>
                                            <p class="text-gray-400 text-sm mt-1">Daftar siswa akan muncul setelah pemilihan</p>
                                        </div>
                                    </td>
                                </tr>
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
                    <i class="fas fa-save mr-2"></i> Simpan Absensi
                </button>
            </div>

            <?php endif; ?>
        </form>
    </div>
</div>

<script>
const siswaData = <?= json_encode($siswaList) ?>;
const statusOptions = <?= json_encode($statusOptions) ?>;
const groupedSiswa = <?= json_encode($groupedSiswa) ?>;

const statusStyles = {
    'hadir':  { active: 'bg-green-500 text-white border-green-600 shadow-md',  inactive: 'bg-white text-green-700 border-green-300 hover:bg-green-50', icon: 'fa-check-circle' },
    'izin':   { active: 'bg-blue-500 text-white border-blue-600 shadow-md',   inactive: 'bg-white text-blue-700 border-blue-300 hover:bg-blue-50',  icon: 'fa-file-alt' },
    'sakit':  { active: 'bg-yellow-500 text-white border-yellow-600 shadow-md', inactive: 'bg-white text-yellow-700 border-yellow-300 hover:bg-yellow-50', icon: 'fa-medkit' },
    'alpa':   { active: 'bg-red-500 text-white border-red-600 shadow-md',     inactive: 'bg-white text-red-700 border-red-300 hover:bg-red-50',    icon: 'fa-times-circle' },
    'libur':  { active: 'bg-purple-500 text-white border-purple-600 shadow-md', inactive: 'bg-white text-purple-700 border-purple-300 hover:bg-purple-50', icon: 'fa-umbrella-beach' }
};

const DESKTOP_BTN_BASE = 'status-btn px-3 py-1.5 border-2 rounded-lg font-semibold text-xs transition-all';

// On pembimbing change, filter siswa
document.getElementById('pembimbing_pkl_id')?.addEventListener('change', function() {
    const selectedId = this.value;
    if (!selectedId) {
        renderSiswaTable([]);
        return;
    }
    const filtered = siswaData.filter(s => String(s.pembimbing_pkl_id) === String(selectedId));
    renderSiswaTable(filtered);
});

function renderSiswaTable(siswaList) {
    const tbody = document.getElementById('siswaTableBody');
    if (!siswaList || siswaList.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-12 text-center text-gray-500">
            <i class="fas fa-users-slash text-3xl mb-3 block text-gray-300"></i>
            <p class="font-medium">Tidak ada siswa untuk pembimbing ini</p></td></tr>`;
        document.getElementById('filledCount').textContent = '0';
        document.getElementById('totalCount').textContent = '0';
        return;
    }

    let html = '';
    siswaList.forEach((siswa, idx) => {
        const sid = siswa.siswa_id;
        html += `<tr class="hover:bg-gray-50" data-siswa-id="${sid}">
            <td class="px-4 py-4 text-sm text-gray-500">${idx + 1}</td>
            <td class="px-4 py-4 text-sm text-gray-900 font-medium">${siswa.nis || '-'}</td>
            <td class="px-4 py-4 text-sm font-medium text-gray-900">${siswa.nama_lengkap}</td>
            <td class="px-4 py-4 text-sm text-gray-600">${siswa.nama_kelas || '-'}</td>
            <td class="px-4 py-4">
                <input type="hidden" name="siswa[${sid}][status]" value="hadir" class="status-input" data-siswa-id="${sid}">
                <div class="flex gap-1 flex-wrap">`;

        Object.entries(statusOptions).forEach(([value, opt]) => {
            const s = statusStyles[value];
            const isSelected = value === 'hadir';
            html += `<button type="button"
                class="${DESKTOP_BTN_BASE} ${isSelected ? s.active : s.inactive}"
                data-siswa-id="${sid}" data-status="${value}"
                onclick="selectStatus('${sid}', '${value}')">
                <i class="fas ${s.icon} mr-1"></i>${opt.label}
            </button>`;
        });

        html += `</div></td>
            <td class="px-4 py-4">
                <div class="flex items-center gap-1.5">
                    <input type="time" name="siswa[${sid}][waktu_absen]" id="waktu_absen_${sid}"
                           class="px-2 py-1.5 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all time-input"
                           data-siswa-id="${sid}">
                    <button type="button" onclick="setTimeNow('${sid}', 'waktu_absen')"
                            class="p-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all shadow-sm flex items-center justify-center btn-time"
                            data-siswa-id="${sid}" title="Set Waktu Sekarang">
                        <i class="fas fa-clock text-xs"></i>
                    </button>
                </div>
            </td>
            <td class="px-4 py-4">
                <div class="flex items-center gap-1.5">
                    <input type="time" name="siswa[${sid}][waktu_pulang]" id="waktu_pulang_${sid}"
                           class="px-2 py-1.5 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all time-input"
                           data-siswa-id="${sid}">
                    <button type="button" onclick="setTimeNow('${sid}', 'waktu_pulang')"
                            class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm flex items-center justify-center btn-time"
                            data-siswa-id="${sid}" title="Set Waktu Sekarang">
                        <i class="fas fa-clock text-xs"></i>
                    </button>
                </div>
            </td>
            <td class="px-4 py-4">
                <input type="text" name="siswa[${sid}][keterangan]" placeholder="Opsional"
                       class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </td></tr>`;
    });

    tbody.innerHTML = html;
    document.getElementById('totalCount').textContent = siswaList.length;
    document.getElementById('filledCount').textContent = '0';
}

function setTimeNow(siswaId, field) {
    const input = document.getElementById(`${field}_${siswaId}`);
    if (input) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        input.value = `${hours}:${minutes}`;
    }
}

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
        btn.className = DESKTOP_BTN_BASE + ' ' + (btnStatus === status ? s.active : s.inactive);
    });

    const waktuAbsenInput = document.getElementById(`waktu_absen_${siswaId}`);
    const waktuPulangInput = document.getElementById(`waktu_pulang_${siswaId}`);
    const timeButtons = document.querySelectorAll(`button[data-siswa-id="${siswaId}"].btn-time`);
    if (waktuAbsenInput && waktuPulangInput) {
        if (status === 'hadir') {
            waktuAbsenInput.disabled = false;
            waktuPulangInput.disabled = false;
            timeButtons.forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        } else {
            waktuAbsenInput.value = '';
            waktuPulangInput.value = '';
            waktuAbsenInput.disabled = true;
            waktuPulangInput.disabled = true;
            timeButtons.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });
        }
    }

    updateProgress();
}

function setAllStatus(status) {
    document.querySelectorAll('.status-input').forEach(input => {
        const siswaId = input.getAttribute('data-siswa-id');
        selectStatus(siswaId, status);
    });

    const labels = { hadir: 'Hadir', izin: 'Izin', sakit: 'Sakit', alpa: 'Alpa', libur: 'Libur' };
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

// Auto-select pembimbing if only one
(function() {
    const sel = document.getElementById('pembimbing_pkl_id');
    if (sel && sel.options.length === 2) {
        sel.value = sel.options[1].value;
        sel.dispatchEvent(new Event('change'));
    }
})();
</script>
<?= $this->endSection() ?>
