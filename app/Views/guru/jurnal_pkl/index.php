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

    <!-- Desktop Table View -->
    <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
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
                        <?php elseif ($jurnal['status'] == 'tinjau_ulang'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-rotate mr-1"></i>Tinjau Ulang
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>Ditolak
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <?php if ($jurnal['status'] === 'disetujui'): ?>
                        <form action="<?= base_url('guru/jurnal-pkl/batal-verifikasi/' . $jurnal['id']); ?>" method="POST" class="inline">
                            <?= csrf_field(); ?>
                            <button type="submit"
                                    onclick="return confirm('Batalkan verifikasi jurnal ini? Status akan kembali ke Pending.')"
                                    title="Batalkan Verifikasi"
                                    class="inline-flex items-center justify-center w-9 h-9 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all shadow-sm hover:shadow active:scale-[0.97]">
                                <i class="fas fa-rotate-left"></i>
                            </button>
                        </form>
                        <?php else: ?>
                        <button onclick="openVerifyModal(<?= $jurnal['id']; ?>, '<?= esc($jurnal['nama_siswa']); ?>', '<?= esc($jurnal['nama_kegiatan']); ?>', '<?= $jurnal['foto'] ? base_url('files/jurnal-pkl/' . $jurnal['foto']) : '' ?>')"
                                title="Tinjau Jurnal Ini"
                                class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-lg hover:from-teal-600 hover:to-cyan-600 transition-all shadow-sm hover:shadow active:scale-[0.97]">
                            <i class="fas fa-file-pen"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-4">
        <?php foreach ($pendingData as $jurnal): ?>
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <!-- Card Header: Nama + Status -->
            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                <div class="flex items-center min-w-0">
                    <div class="bg-blue-100 text-blue-700 w-9 h-9 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate"><?= esc($jurnal['nama_siswa']); ?></p>
                        <p class="text-xs text-gray-500">NIS: <?= esc($jurnal['nis']); ?></p>
                    </div>
                </div>
                <?php if ($jurnal['status'] == 'pending'): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 flex-shrink-0 ml-2">
                    <i class="fas fa-clock mr-1"></i>Pending
                </span>
                <?php elseif ($jurnal['status'] == 'disetujui'): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 flex-shrink-0 ml-2">
                    <i class="fas fa-check-circle mr-1"></i>Disetujui
                </span>
                <?php elseif ($jurnal['status'] == 'revisi'): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 flex-shrink-0 ml-2">
                    <i class="fas fa-edit mr-1"></i>Revisi
                </span>
                <?php elseif ($jurnal['status'] == 'tinjau_ulang'): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 flex-shrink-0 ml-2">
                    <i class="fas fa-rotate mr-1"></i>Tinjau Ulang
                </span>
                <?php else: ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 flex-shrink-0 ml-2">
                    <i class="fas fa-times-circle mr-1"></i>Ditolak
                </span>
                <?php endif; ?>
            </div>

            <!-- Card Body: Info Grid -->
            <div class="px-4 py-3 space-y-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Kelas</p>
                        <p class="font-medium text-gray-900"><?= esc($jurnal['nama_kelas']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="font-medium text-gray-900"><?= date('d M Y', strtotime($jurnal['tanggal'])); ?></p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Kegiatan</p>
                    <p class="font-medium text-gray-900"><?= esc($jurnal['nama_kegiatan']); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Deskripsi</p>
                    <p class="text-sm text-gray-700 line-clamp-2"><?= esc($jurnal['deskripsi']); ?></p>
                </div>
                <?php if ($jurnal['foto']): ?>
                <div class="flex items-center text-xs text-blue-600">
                    <i class="fas fa-camera mr-1.5"></i>
                    <span>Ada foto dokumentasi</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Card Footer: Action Button -->
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                <?php if ($jurnal['status'] === 'disetujui'): ?>
                <form action="<?= base_url('guru/jurnal-pkl/batal-verifikasi/' . $jurnal['id']); ?>" method="POST">
                    <?= csrf_field(); ?>
                    <button type="submit"
                            onclick="return confirm('Batalkan verifikasi jurnal ini? Status akan kembali ke Pending.')"
                            class="w-full flex items-center justify-center px-4 py-3 bg-orange-500 text-white rounded-xl active:bg-orange-600 transition-all text-sm font-semibold shadow-sm touch-manipulation active:scale-[0.98]">
                        <i class="fas fa-rotate-left mr-2"></i>
                        Batalkan Verifikasi
                    </button>
                </form>
                <?php else: ?>
                <button onclick="openVerifyModal(<?= $jurnal['id']; ?>, '<?= esc($jurnal['nama_siswa']); ?>', '<?= esc($jurnal['nama_kegiatan']); ?>', '<?= $jurnal['foto'] ? base_url('files/jurnal-pkl/' . $jurnal['foto']) : '' ?>')"
                        class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-xl active:from-teal-600 active:to-cyan-600 transition-all text-sm font-semibold shadow-sm touch-manipulation active:scale-[0.98]">
                    <i class="fas fa-file-pen mr-2"></i>
                    Tinjau Jurnal
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<!-- Modal Verifikasi -->
<div id="verifyModal" class="fixed inset-0 z-[60] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeVerifyModal()"></div>

        <!-- Modal Panel -->
        <form id="verifyForm" method="POST" class="relative bg-white w-11/12 sm:w-full sm:max-w-lg max-h-[85vh] sm:max-h-[90vh] flex flex-col rounded-2xl shadow-2xl overflow-hidden">
            <?= csrf_field(); ?>

            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-white flex-shrink-0">
                <h3 class="text-base sm:text-lg font-bold text-gray-800">
                    <i class="fas fa-check-circle mr-2 text-blue-600"></i>
                    Verifikasi Jurnal
                </h3>
                <button type="button" onclick="closeVerifyModal()" class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors text-gray-500">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Scrollable Body -->
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">

                <!-- Info Siswa -->
                <div id="verifyInfo" class="p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <p class="text-sm"><span class="font-semibold text-blue-800">Siswa:</span> <span id="verifySiswa" class="text-blue-900"></span></p>
                    <p class="text-sm mt-1"><span class="font-semibold text-blue-800">Kegiatan:</span> <span id="verifyKegiatan" class="text-blue-900"></span></p>
                </div>

                <!-- Foto Dokumentasi -->
                <div id="verifyFotoContainer" class="hidden">
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-camera mr-2 text-yellow-500"></i>
                        Foto Dokumentasi
                    </p>
                    <img id="verifyFoto" src="" class="w-full max-h-48 object-contain rounded-xl border border-gray-200 bg-gray-50">
                </div>

                <!-- Status Verifikasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2.5">Status Verifikasi <span class="text-red-500">*</span></label>

                    <!-- Segmented Control -->
                    <div class="grid grid-cols-3 gap-1.5 p-1 bg-gray-100 rounded-xl">

                        <!-- Disetujui -->
                        <label class="relative cursor-pointer rounded-lg text-gray-500 has-[:checked]:bg-green-500 has-[:checked]:shadow-md has-[:checked]:text-white hover:bg-green-100 hover:text-green-700 transition-all duration-200 active:scale-[0.97]">
                            <input type="radio" name="status" value="disetujui" class="sr-only" required>
                            <div class="flex flex-col items-center justify-center py-3 px-1 sm:py-3.5 sm:px-3 gap-1">
                                <i class="fas fa-check-circle text-lg sm:text-xl transition-colors"></i>
                                <span class="text-xs sm:text-sm font-bold transition-colors">Setuju</span>
                                <span class="text-[10px] sm:text-xs opacity-60 transition-colors hidden sm:block">Dapat dicetak</span>
                            </div>
                        </label>

                        <!-- Revisi -->
                        <label class="relative cursor-pointer rounded-lg text-gray-500 has-[:checked]:bg-orange-500 has-[:checked]:shadow-md has-[:checked]:text-white hover:bg-orange-100 hover:text-orange-700 transition-all duration-200 active:scale-[0.97]">
                            <input type="radio" name="status" value="revisi" class="sr-only">
                            <div class="flex flex-col items-center justify-center py-3 px-1 sm:py-3.5 sm:px-3 gap-1">
                                <i class="fas fa-pen-to-square text-lg sm:text-xl transition-colors"></i>
                                <span class="text-xs sm:text-sm font-bold transition-colors">Revisi</span>
                                <span class="text-[10px] sm:text-xs opacity-60 transition-colors hidden sm:block">Perlu perbaikan</span>
                            </div>
                        </label>

                        <!-- Ditolak -->
                        <label class="relative cursor-pointer rounded-lg text-gray-500 has-[:checked]:bg-red-500 has-[:checked]:shadow-md has-[:checked]:text-white hover:bg-red-100 hover:text-red-700 transition-all duration-200 active:scale-[0.97]">
                            <input type="radio" name="status" value="ditolak" class="sr-only">
                            <div class="flex flex-col items-center justify-center py-3 px-1 sm:py-3.5 sm:px-3 gap-1">
                                <i class="fas fa-xmark-circle text-lg sm:text-xl transition-colors"></i>
                                <span class="text-xs sm:text-sm font-bold transition-colors">Tolak</span>
                                <span class="text-[10px] sm:text-xs opacity-60 transition-colors hidden sm:block">Harus diganti</span>
                            </div>
                        </label>

                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-comment mr-2 text-blue-500"></i>
                        Catatan <span class="font-normal text-gray-400">(Opsional)</span>
                    </label>
                    <textarea id="catatan" name="catatan" rows="3"
                              placeholder="Berikan catatan untuk siswa jika diperlukan"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-5 py-4 sm:rounded-b-2xl">
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button"
                            onclick="closeVerifyModal()"
                            class="w-full sm:w-auto px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl transition-colors font-semibold text-sm active:scale-[0.98]">
                        Batal
                    </button>
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl transition-all font-semibold text-sm shadow-lg hover:shadow-xl active:scale-[0.98]">
                        <i class="fas fa-check mr-2"></i>
                        Simpan Verifikasi
                    </button>
                </div>
            </div>
        </form>
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
