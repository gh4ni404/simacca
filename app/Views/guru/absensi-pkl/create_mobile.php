<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 mb-4 rounded-lg mx-4 shadow-md">
        <h1 class="text-xl font-bold mb-1">Input Absensi PKL</h1>
        <p class="text-sm opacity-90 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Catat kehadiran siswa bimbingan PKL
        </p>
    </div>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <div class="px-4">
        <?php if (empty($pembimbingList)): ?>
            <!-- Empty State - No Pembimbing -->
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-yellow-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Pembimbingan PKL</h3>
                <p class="text-gray-600 text-sm mb-4">Anda belum ditugaskan sebagai pembimbing PKL tahun ini.</p>
                <a href="<?= base_url('guru/absensi-pkl'); ?>" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-xl transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        <?php else: ?>

        <!-- Main Form Container -->
        <div class="bg-white rounded-2xl shadow-xl p-4 mb-4">
            <form action="<?= base_url('guru/absensi-pkl/simpan'); ?>" method="post" id="absensiPklForm">
                <?= csrf_field(); ?>

                <!-- Pembimbing & Tanggal Selection -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg mr-3">
                            <i class="fas fa-calendar-check text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Informasi Absensi</h3>
                    </div>

                    <div class="space-y-4">
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
                        <div>
                            <label for="keterangan_umum" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sticky-note mr-2 text-yellow-500"></i> Keterangan Umum (Opsional)
                            </label>
                            <textarea id="keterangan_umum" name="keterangan_umum" rows="2"
                                      placeholder="Catatan umum untuk hari ini..."
                                      class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Siswa Section -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg mr-3">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Daftar Siswa Bimbingan</h3>
                </div>
                <div id="mobile-progress-counter" class="px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg">
                    <span class="text-xs text-gray-600 font-semibold">0 / 0 Terisi</span>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-4 mb-4 shadow-sm">
                <div class="flex items-center mb-3">
                    <div class="p-2 bg-indigo-500 rounded-lg mr-3">
                        <i class="fas fa-bolt text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-indigo-900">Aksi Cepat</h4>
                        <p class="text-xs text-indigo-700">Set status untuk semua siswa sekaligus</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="setAllStatus('hadir')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md transition-all active:scale-95">
                        <i class="fas fa-check-circle"></i> Semua Hadir
                    </button>
                    <button type="button" onclick="setAllStatus('izin')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition-all active:scale-95">
                        <i class="fas fa-file-alt"></i> Semua Izin
                    </button>
                    <button type="button" onclick="setAllStatus('sakit')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow-md transition-all active:scale-95">
                        <i class="fas fa-medkit"></i> Semua Sakit
                    </button>
                    <button type="button" onclick="setAllStatus('alpa')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-md transition-all active:scale-95">
                        <i class="fas fa-times-circle"></i> Semua Alpa
                    </button>
                    <button type="button" onclick="setAllStatus('dispen')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg shadow-md transition-all active:scale-95 col-span-2">
                        <i class="fas fa-id-badge"></i> Semua Dispen
                    </button>
                </div>
            </div>

            <!-- Siswa Cards Container -->
            <div class="space-y-3" id="siswaCardsContainer">
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="p-4 bg-gray-100 rounded-full mb-3">
                        <i class="fas fa-hand-pointer text-gray-400 text-3xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">Pilih Pembimbing PKL terlebih dahulu</p>
                    <p class="text-gray-400 text-sm mt-1">Daftar siswa akan muncul setelah pemilihan</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-3 pb-6">
            <a href="<?= base_url('guru/absensi-pkl'); ?>"
               class="w-full inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button type="submit"
                    form="absensiPklForm"
                    id="submitBtn"
                    class="w-full inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i> Simpan Absensi
            </button>
        </div>

        <?php endif; ?>
    </div>
</div>

<script>
const siswaData = <?= json_encode($siswaList) ?>;
const statusOptions = <?= json_encode($statusOptions) ?>;
const groupedSiswa = <?= json_encode($groupedSiswa) ?>;

const statusStyles = {
    'hadir':  { active: 'bg-green-500 text-white border-green-500',  inactive: 'bg-white text-gray-700 border-gray-300', icon: 'fa-check-circle' },
    'izin':   { active: 'bg-blue-500 text-white border-blue-500',   inactive: 'bg-white text-gray-700 border-gray-300',  icon: 'fa-file-alt' },
    'sakit':  { active: 'bg-yellow-500 text-white border-yellow-500', inactive: 'bg-white text-gray-700 border-gray-300', icon: 'fa-medkit' },
    'alpa':   { active: 'bg-red-500 text-white border-red-500',     inactive: 'bg-white text-gray-700 border-gray-300',    icon: 'fa-times-circle' },
    'dispen': { active: 'bg-purple-500 text-white border-purple-500', inactive: 'bg-white text-gray-700 border-gray-300', icon: 'fa-id-badge' }
};

const MOBILE_BTN_BASE = 'status-btn flex flex-col items-center justify-center py-2.5 border-2 rounded-xl transition-all active:scale-95';

// On pembimbing change, filter siswa
document.getElementById('pembimbing_pkl_id')?.addEventListener('change', function() {
    const selectedId = this.value;
    if (!selectedId) {
        renderSiswaCards([]);
        return;
    }
    const filtered = siswaData.filter(s => String(s.pembimbing_pkl_id) === String(selectedId));
    renderSiswaCards(filtered);
});

function renderSiswaCards(siswaList) {
    const container = document.getElementById('siswaCardsContainer');
    if (!siswaList || siswaList.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                <i class="fas fa-users-slash text-4xl mb-3"></i>
                <p class="font-medium">Tidak ada siswa untuk pembimbing ini</p>
            </div>`;
        updateProgressCounters(0, 0);
        return;
    }

    let html = '';
    siswaList.forEach((siswa, idx) => {
        const sid = siswa.siswa_id;
        html += `
        <div class="bg-white rounded-2xl shadow-md p-4 border-2 border-transparent transition-all student-card" data-student-id="${sid}">
            <!-- Student Info -->
            <div class="flex items-center gap-3 mb-3">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                        ${siswa.nama_lengkap.charAt(0).toUpperCase()}
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-base text-gray-900">${siswa.nama_lengkap}</h3>
                    <p class="text-xs text-gray-600">NIS: ${siswa.nis || '-'} &bull; Kelas: ${siswa.nama_kelas || '-'}</p>
                </div>
                <span class="text-xs text-gray-400 font-medium">#${idx + 1}</span>
            </div>

            <!-- Hidden Input -->
            <input type="hidden" name="siswa[${sid}][status]" value="hadir" class="status-input" data-siswa-id="${sid}">

            <!-- Status Buttons -->
            <div class="grid grid-cols-5 gap-1.5 mb-3">`;

        Object.entries(statusOptions).forEach(([value, opt]) => {
            const s = statusStyles[value];
            const isSelected = value === 'hadir';
            html += `<button type="button"
                class="${MOBILE_BTN_BASE} ${isSelected ? s.active : s.inactive}"
                data-siswa-id="${sid}" data-status="${value}"
                onclick="selectStatus('${sid}', '${value}')">
                <i class="fas ${s.icon} text-lg mb-1"></i>
                <span class="text-xs font-semibold">${opt.label}</span>
            </button>`;
        });

        html += `</div>

            <!-- Notes Field -->
            <textarea name="siswa[${sid}][keterangan]"
                      class="w-full px-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                      rows="2"
                      placeholder="Keterangan (opsional)"></textarea>
        </div>`;
    });

    container.innerHTML = html;
    updateProgressCounters(0, siswaList.length);
}

function selectStatus(siswaId, status) {
    const hiddenInput = document.querySelector(`.status-input[data-siswa-id="${siswaId}"]`);
    if (!hiddenInput) return;
    hiddenInput.value = status;
    hiddenInput.setAttribute('data-manually-set', 'true');

    const buttons = document.querySelectorAll(`.status-btn[data-siswa-id="${siswaId}"]`);
    buttons.forEach(btn => {
        const btnStatus = btn.getAttribute('data-status');
        const s = statusStyles[btnStatus];
        btn.className = MOBILE_BTN_BASE + ' ' + (btnStatus === status ? s.active : s.inactive);
    });

    // Visual feedback on card
    const card = document.querySelector(`.student-card[data-student-id="${siswaId}"]`);
    if (card) {
        card.classList.add('border-green-500', 'bg-green-50');
        setTimeout(() => card.classList.remove('bg-green-50'), 300);
    }

    updateProgress();
}

function setAllStatus(status) {
    document.querySelectorAll('.status-input').forEach(input => {
        const siswaId = input.getAttribute('data-siswa-id');
        selectStatus(siswaId, status);
    });

    const labels = { hadir: 'Hadir', izin: 'Izin', sakit: 'Sakit', alpa: 'Alpa', dispen: 'Dispen' };
    showToast(`Semua siswa di-set ${labels[status]}`);
}

function updateProgress() {
    const inputs = document.querySelectorAll('.status-input');
    let filled = 0;
    inputs.forEach(i => { if (i.getAttribute('data-manually-set') === 'true') filled++; });
    updateProgressCounters(filled, inputs.length);
}

function updateProgressCounters(filled, total) {
    const counter = document.getElementById('mobile-progress-counter');
    if (counter) {
        counter.innerHTML = `<span class="text-xs text-gray-600 font-semibold">${filled} / ${total} Terisi</span>`;
    }
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
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
