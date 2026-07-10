<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-6">
    <div class="mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-check-double mr-2 text-blue-600"></i>
                Verifikasi Jurnal PKL
            </h1>
            <p class="text-gray-600 mt-1">Verifikasi jurnal PKL siswa bimbingan Anda</p>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($pendingData)): ?>
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        <i class="fas fa-inbox text-6xl mb-4"></i>
        <p class="text-lg">Tidak ada jurnal yang perlu diverifikasi</p>
        <p class="text-sm mt-2">Semua jurnal PKL siswa bimbingan Anda sudah diverifikasi</p>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($pendingData as $jurnal): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= esc($jurnal['nama_siswa']); ?></div>
                        <div class="text-sm text-gray-500">NIS: <?= esc($jurnal['nis']); ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <?= esc($jurnal['nama_kelas']); ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900"><?= esc($jurnal['nama_kegiatan']); ?></div>
                        <div class="text-sm text-gray-500 truncate max-w-xs"><?= esc($jurnal['deskripsi']); ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <?= date('d M Y', strtotime($jurnal['tanggal'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <?php if ($jurnal['status'] == 'pending'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                        <?php elseif ($jurnal['status'] == 'disetujui'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Disetujui
                        </span>
                        <?php elseif ($jurnal['status'] == 'revisi'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <i class="fas fa-edit mr-1"></i>Revisi
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>Ditolak
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <button onclick="openVerifyModal(<?= $jurnal['id']; ?>, '<?= esc($jurnal['nama_siswa']); ?>', '<?= esc($jurnal['nama_kegiatan']); ?>', '<?= $jurnal['foto'] ? base_url('files/jurnal-pkl/' . $jurnal['foto']) : '' ?>')"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-xs font-medium">
                            <i class="fas fa-check mr-1"></i>
                            Verifikasi
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Verifikasi -->
<div id="verifyModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-40" onclick="closeVerifyModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-check-circle mr-2 text-blue-600"></i>
                    Verifikasi Jurnal
                </h3>
                <button onclick="closeVerifyModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="verifyInfo" class="mb-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm"><span class="font-medium">Siswa:</span> <span id="verifySiswa"></span></p>
                <p class="text-sm"><span class="font-medium">Kegiatan:</span> <span id="verifyKegiatan"></span></p>
            </div>

            <div id="verifyFotoContainer" class="mb-4 hidden">
                <p class="text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-camera mr-2 text-yellow-500"></i>
                    Foto Dokumentasi:
                </p>
                <img id="verifyFoto" src="" class="max-h-64 rounded-lg shadow mx-auto">
            </div>

            <form id="verifyForm" method="POST">
                <?= csrf_field(); ?>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Verifikasi <span class="text-red-500">*</span></label>
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-green-50 transition-colors">
                            <input type="radio" name="status" value="disetujui" class="mr-3" required>
                            <div>
                                <span class="font-medium text-green-700">Disetujui</span>
                                <p class="text-xs text-gray-500">Jurnal sudah sesuai dan dapat dicetak</p>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-orange-50 transition-colors">
                            <input type="radio" name="status" value="revisi" class="mr-3">
                            <div>
                                <span class="font-medium text-orange-700">Revisi</span>
                                <p class="text-xs text-gray-500">Jurnal perlu diperbaiki siswa</p>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 transition-colors">
                            <input type="radio" name="status" value="ditolak" class="mr-3">
                            <div>
                                <span class="font-medium text-red-700">Ditolak</span>
                                <p class="text-xs text-gray-500">Jurnal tidak sesuai dan harus diganti</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment mr-2 text-blue-500"></i>
                        Catatan (Opsional)
                    </label>
                    <textarea id="catatan" name="catatan" rows="3"
                              placeholder="Berikan catatan untuk siswa jika diperlukan"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-check mr-2"></i>
                        Simpan Verifikasi
                    </button>
                    <button type="button"
                            onclick="closeVerifyModal()"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openVerifyModal(id, siswa, kegiatan, foto) {
    document.getElementById('verifyModal').classList.remove('hidden');
    document.getElementById('verifySiswa').textContent = siswa;
    document.getElementById('verifyKegiatan').textContent = kegiatan;
    document.getElementById('verifyForm').action = '<?= base_url('guru/jurnal-pkl/verify/'); ?>' + id;

    // Show foto if available
    const fotoContainer = document.getElementById('verifyFotoContainer');
    const fotoImg = document.getElementById('verifyFoto');
    if (foto) {
        fotoImg.src = foto;
        fotoContainer.classList.remove('hidden');
    } else {
        fotoImg.src = '';
        fotoContainer.classList.add('hidden');
    }

    // Reset form
    document.querySelectorAll('input[name="status"]').forEach(r => r.checked = false);
    document.getElementById('catatan').value = '';
}

function closeVerifyModal() {
    document.getElementById('verifyModal').classList.add('hidden');
}

document.getElementById('verifyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVerifyModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVerifyModal();
    }
});
</script>
<?= $this->endSection() ?>
