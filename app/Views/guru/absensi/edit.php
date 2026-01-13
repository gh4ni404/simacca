<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Edit Absensi
                    </span>
                </h1>
                <p class="text-gray-600 text-sm mt-1">
                    <i class="fas fa-info-circle mr-2 text-blue-500 text-sm"></i>
                    <span class="text-sm">Perbarui data absensi siswa</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Absensi Info Card -->
    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex items-center mb-4">
            <div class="p-2 bg-blue-500 rounded-lg mr-3">
                <i class="fas fa-info-circle text-white"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-800">Informasi Absensi</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="flex items-center bg-white rounded-lg p-4 shadow-sm">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="fas fa-calendar-day text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Tanggal</p>
                    <p class="text-sm font-bold text-gray-800"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-4 shadow-sm">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="fas fa-book text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Mata Pelajaran</p>
                    <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_mapel'] ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-4 shadow-sm">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="fas fa-school text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Kelas</p>
                    <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_kelas'] ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-4 shadow-sm">
                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                    <i class="fas fa-calendar-week text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Hari</p>
                    <p class="text-sm font-bold text-gray-800"><?= $absensi['hari'] ?? '-' ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="<?= base_url('guru/absensi/update/' . $absensi['id']) ?>" method="POST" id="formEditAbsensi">
        <?= csrf_field() ?>

        <!-- Card: Edit Data Absensi -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-400 to-purple-500 px-6 py-4">
                <div class="flex items-center">
                    <i class="fas fa-edit text-white text-lg mr-3"></i>
                    <h2 class="text-lg font-bold text-white">Edit Data Absensi</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pertemuan Ke -->
                    <div>
                        <label for="pertemuan_ke" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-hashtag mr-2 text-indigo-500"></i>
                            Pertemuan Ke
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="number"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            id="pertemuan_ke"
                            name="pertemuan_ke"
                            value="<?= old('pertemuan_ke', $absensi['pertemuan_ke']) ?>"
                            required
                            min="1">
                    </div>

                    <!-- Tanggal (Editable) -->
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Tanggal
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="date"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            id="tanggal"
                            name="tanggal"
                            value="<?= old('tanggal', $absensi['tanggal']) ?>"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tanggal dapat diubah sesuai kebutuhan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Daftar Kehadiran Siswa -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-500 rounded-lg mr-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Daftar Kehadiran Siswa</h2>
                    </div>
                    <button type="button"
                        onclick="setAllStatus('Hadir')"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-check mr-2"></i> Semua Hadir
                    </button>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($siswaList)): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                            <p class="text-yellow-800 font-medium">Tidak ada siswa dalam kelas ini.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-100 to-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-16">NO</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-32">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NAMA SISWA</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">STATUS</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $no = 1;
                                // Create array of existing absensi details
                                $existingDetails = [];
                                foreach ($absensiDetails as $detail) {
                                    $existingDetails[$detail['siswa_id']] = $detail;
                                }

                                foreach ($siswaList as $siswa):
                                    $detail = $existingDetails[$siswa['id']] ?? null;
                                    $currentStatus = $detail ? $detail['status'] : 'Hadir';
                                    $currentKeterangan = $detail ? $detail['keterangan'] : '';
                                ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-900"><?= $no++ ?></td>
                                        <td class="px-4 py-4 text-sm text-gray-700 font-medium"><?= $siswa['nis'] ?></td>
                                        <td class="px-4 py-4 text-sm text-gray-900 font-medium"><?= $siswa['nama_lengkap'] ?></td>
                                        <td class="px-4 py-4 text-sm">
                                            <select class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all status-select"
                                                name="siswa[<?= $siswa['id'] ?>][status]"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                required>
                                                <option value="Hadir" <?= $currentStatus == 'Hadir' ? 'selected' : '' ?>>✅ Hadir</option>
                                                <option value="Izin" <?= $currentStatus == 'Izin' ? 'selected' : '' ?>>📝 Izin</option>
                                                <option value="Sakit" <?= $currentStatus == 'Sakit' ? 'selected' : '' ?>>🤒 Sakit</option>
                                                <option value="Alpha" <?= $currentStatus == 'Alpha' ? 'selected' : '' ?>>❌ Alpha</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <input type="text"
                                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all keterangan-input"
                                                name="siswa[<?= $siswa['id'] ?>][keterangan]"
                                                id="keterangan_<?= $siswa['id'] ?>"
                                                value="<?= $currentKeterangan ?>"
                                                placeholder="Keterangan (opsional)">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t-2 border-gray-200">
                <a href="<?= base_url('guru/absensi') ?>"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <button type="submit"
                        name="next_action"
                        value="list"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5"
                        id="btnSubmit">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                    <button type="submit"
                        name="next_action"
                        value="jurnal"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-book mr-2"></i> Lanjut isi dokumentasi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Set all students status
    function setAllStatus(status) {
        const selects = document.querySelectorAll('.status-select');
        selects.forEach(select => {
            select.value = status;
        });
    }

    // Auto focus keterangan when status is not hadir
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const siswaId = this.dataset.siswaId;
            const keteranganInput = document.getElementById('keterangan_' + siswaId);

            if (this.value !== 'Hadir' && keteranganInput) {
                keteranganInput.focus();
            }
        });
    });

    // Form validation
    document.getElementById('formEditAbsensi').addEventListener('submit', function(e) {
        const btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
    });
</script>

<?= $this->endSection() ?>