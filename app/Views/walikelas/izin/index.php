<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-envelope mr-2 text-yellow-600"></i>
                    Persetujuan Izin Siswa
                </h1>
                <p class="text-gray-600 mt-1">Kelola izin siswa kelas <?= esc($kelas['nama_kelas']); ?></p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-school mr-1"></i>
                    Kelas: <span class="font-semibold"><?= esc($kelas['nama_kelas']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold"><?= $countPending; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-2xl font-bold"><?= $countDisetujui; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ditolak</p>
                    <p class="text-2xl font-bold"><?= $countDitolak; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="<?= base_url('walikelas/izin'); ?>" 
                   class="<?= !$status ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-list mr-2"></i>
                    Semua Izin
                    <span class="ml-2 bg-gray-200 text-gray-700 py-0.5 px-2 rounded-full text-xs">
                        <?= count($izinData); ?>
                    </span>
                </a>
                <a href="<?= base_url('walikelas/izin?status=pending'); ?>" 
                   class="<?= $status == 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-clock mr-2"></i>
                    Pending
                    <span class="ml-2 bg-yellow-200 text-yellow-700 py-0.5 px-2 rounded-full text-xs">
                        <?= $countPending; ?>
                    </span>
                </a>
                <a href="<?= base_url('walikelas/izin?status=disetujui'); ?>" 
                   class="<?= $status == 'disetujui' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    Disetujui
                    <span class="ml-2 bg-green-200 text-green-700 py-0.5 px-2 rounded-full text-xs">
                        <?= $countDisetujui; ?>
                    </span>
                </a>
                <a href="<?= base_url('walikelas/izin?status=ditolak'); ?>" 
                   class="<?= $status == 'ditolak' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-times-circle mr-2"></i>
                    Ditolak
                    <span class="ml-2 bg-red-200 text-red-700 py-0.5 px-2 rounded-full text-xs">
                        <?= $countDitolak; ?>
                    </span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Izin List -->
    <div class="space-y-4">
        <?php if (empty($izinData)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-6xl mb-4"></i>
            <p class="text-lg">Tidak ada data izin</p>
            <p class="text-sm mt-2">
                <?php if ($status == 'pending'): ?>
                    Tidak ada izin yang menunggu persetujuan
                <?php elseif ($status == 'disetujui'): ?>
                    Belum ada izin yang disetujui
                <?php elseif ($status == 'ditolak'): ?>
                    Belum ada izin yang ditolak
                <?php else: ?>
                    Belum ada siswa yang mengajukan izin
                <?php endif; ?>
            </p>
        </div>
        <?php else: ?>
            <?php foreach ($izinData as $izin): ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                        <div class="flex-1">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800"><?= esc($izin['nama_lengkap']); ?></h3>
                                        <p class="text-sm text-gray-600">NIS: <?= esc($izin['nis']); ?></p>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($izin['status'] == 'pending'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                    <?php elseif ($izin['status'] == 'disetujui'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Disetujui
                                    </span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Ditolak
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                        <span class="font-medium">Tanggal Izin:</span>
                                    </p>
                                    <p class="text-sm text-gray-800 ml-6">
                                        <?= date('d F Y', strtotime($izin['tanggal'])); ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <i class="fas fa-tag mr-2 text-purple-500"></i>
                                        <span class="font-medium">Jenis Izin:</span>
                                    </p>
                                    <p class="text-sm text-gray-800 ml-6 capitalize">
                                        <?= esc($izin['jenis_izin']); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">
                                    <i class="fas fa-comment-alt mr-2 text-green-500"></i>
                                    <span class="font-medium">Alasan:</span>
                                </p>
                                <p class="text-sm text-gray-800 ml-6 bg-gray-50 p-3 rounded-lg">
                                    <?= esc($izin['alasan']); ?>
                                </p>
                            </div>

                            <?php if (!empty($izin['berkas'])): ?>
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">
                                    <i class="fas fa-paperclip mr-2 text-yellow-500"></i>
                                    <span class="font-medium">Dokumen Pendukung:</span>
                                </p>
                                <a href="<?= base_url('uploads/izin/' . $izin['berkas']); ?>" target="_blank" 
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 ml-6">
                                    <i class="fas fa-file mr-2"></i>
                                    Lihat Dokumen
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($izin['status'] != 'pending' && !empty($izin['catatan'])): ?>
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">
                                    <i class="fas fa-sticky-note mr-2 text-orange-500"></i>
                                    <span class="font-medium">Catatan Wali Kelas:</span>
                                </p>
                                <p class="text-sm text-gray-800 ml-6 bg-blue-50 p-3 rounded-lg">
                                    <?= esc($izin['catatan']); ?>
                                </p>
                            </div>
                            <?php endif; ?>

                            <div class="text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                Diajukan pada: <?= date('d F Y H:i', strtotime($izin['created_at'])); ?>
                            </div>
                        </div>

                        <!-- Actions -->
                        <?php if ($izin['status'] == 'pending'): ?>
                        <div class="mt-4 md:mt-0 md:ml-6 flex md:flex-col gap-2">
                            <button onclick="showApproveModal(<?= $izin['id']; ?>, '<?= esc($izin['nama_lengkap']); ?>')" 
                                    class="flex-1 md:w-32 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>
                                Setujui
                            </button>
                            <button onclick="showRejectModal(<?= $izin['id']; ?>, '<?= esc($izin['nama_lengkap']); ?>')" 
                                    class="flex-1 md:w-32 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Tolak
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Izin dengan status <span class="font-medium text-yellow-700">Pending</span> memerlukan persetujuan Anda</li>
                    <li>Anda dapat menambahkan catatan saat menyetujui atau menolak izin</li>
                    <li>Siswa akan menerima notifikasi status persetujuan izin mereka</li>
                    <li>Pastikan untuk memeriksa dokumen pendukung jika tersedia</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mx-auto">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mt-4">Setujui Izin</h3>
            <p class="text-sm text-gray-500 text-center mt-2" id="approveModalText">
                Apakah Anda yakin ingin menyetujui izin siswa <span id="approveNamaSiswa" class="font-semibold"></span>?
            </p>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea id="approveCatatan" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            <div class="flex gap-3 mt-6">
                <button onclick="closeApproveModal()" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button onclick="processApprove()" 
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mx-auto">
                <i class="fas fa-times text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mt-4">Tolak Izin</h3>
            <p class="text-sm text-gray-500 text-center mt-2" id="rejectModalText">
                Apakah Anda yakin ingin menolak izin siswa <span id="rejectNamaSiswa" class="font-semibold"></span>?
            </p>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea id="rejectCatatan" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-3 mt-6">
                <button onclick="closeRejectModal()" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button onclick="processReject()" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentIzinId = null;

function showApproveModal(izinId, namaSiswa) {
    currentIzinId = izinId;
    document.getElementById('approveNamaSiswa').textContent = namaSiswa;
    document.getElementById('approveCatatan').value = '';
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    currentIzinId = null;
}

function showRejectModal(izinId, namaSiswa) {
    currentIzinId = izinId;
    document.getElementById('rejectNamaSiswa').textContent = namaSiswa;
    document.getElementById('rejectCatatan').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    currentIzinId = null;
}

function processApprove() {
    if (!currentIzinId) return;

    const catatan = document.getElementById('approveCatatan').value;
    
    fetch('<?= base_url('walikelas/izin/setujui/'); ?>' + currentIzinId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'catatan=' + encodeURIComponent(catatan)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Izin berhasil disetujui!');
            location.reload();
        } else {
            alert('Gagal menyetujui izin: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyetujui izin');
    });
}

function processReject() {
    if (!currentIzinId) return;

    const catatan = document.getElementById('rejectCatatan').value;
    
    if (!catatan.trim()) {
        alert('Alasan penolakan harus diisi!');
        return;
    }
    
    fetch('<?= base_url('walikelas/izin/tolak/'); ?>' + currentIzinId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'catatan=' + encodeURIComponent(catatan)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Izin berhasil ditolak!');
            location.reload();
        } else {
            alert('Gagal menolak izin: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menolak izin');
    });
}
</script>
<?= $this->endSection() ?>
