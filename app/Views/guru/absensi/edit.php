<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="<?= base_url('guru/dashboard') ?>" class="text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="<?= base_url('guru/absensi') ?>" class="text-blue-600 hover:text-blue-800 font-medium">Absensi</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="<?= base_url('guru/absensi/show/' . $absensi['id']) ?>" class="text-blue-600 hover:text-blue-800 font-medium">Detail</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Edit</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">
                        Edit Absensi
                    </span>
                </h1>
                <p class="text-gray-600 text-sm mt-1">Perbarui data absensi siswa</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
            <p class="text-red-800 font-medium"><?= session()->getFlashdata('error') ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($errors)): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-1"></i>
            <div class="flex-1">
                <p class="text-red-800 font-bold mb-2">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

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

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                <h2 class="text-white font-bold text-lg flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Data Absensi
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="pertemuan_ke" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-hashtag mr-2 text-indigo-500"></i>
                            Pertemuan Ke
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="number" 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all" 
                               id="pertemuan_ke" 
                               name="pertemuan_ke" 
                               value="<?= old('pertemuan_ke', $absensi['pertemuan_ke']) ?>" 
                               required 
                               min="1">
                    </div>
                    <div>
                        <label for="materi_pembelajaran" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-book-open mr-2 text-green-500"></i>
                            Materi Pembelajaran
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all" 
                               id="materi_pembelajaran" 
                               name="materi_pembelajaran" 
                               value="<?= old('materi_pembelajaran', $absensi['materi_pembelajaran']) ?>"
                               placeholder="Isi materi pembelajaran...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Siswa -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-users mr-2 text-blue-500"></i>
                        Daftar Kehadiran Siswa
                    </h2>
                    <button type="button" 
                            onclick="setAllStatus('hadir')"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-check mr-2"></i> Semua Hadir
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($siswaList)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Tidak ada siswa dalam kelas ini.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th width="100">NIS</th>
                                <th>Nama Siswa</th>
                                <th width="150">Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Create array of existing absensi details
                            $existingDetails = [];
                            foreach ($absensiDetails as $detail) {
                                $existingDetails[$detail['siswa_id']] = $detail;
                            }
                            
                            foreach ($siswaList as $siswa): 
                                $detail = $existingDetails[$siswa['id']] ?? null;
                                $currentStatus = $detail ? $detail['status'] : 'hadir';
                                $currentKeterangan = $detail ? $detail['keterangan'] : '';
                                
                                // Check if student has approved izin
                                $hasIzin = false;
                                foreach ($approvedIzin as $izin) {
                                    if ($izin['siswa_id'] == $siswa['id']) {
                                        $hasIzin = true;
                                        break;
                                    }
                                }
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= $siswa['nis'] ?></td>
                                <td>
                                    <?= $siswa['nama_lengkap'] ?>
                                    <?php if ($hasIzin): ?>
                                    <span class="badge bg-info ms-2">
                                        <i class="fas fa-envelope me-1"></i> Ada Izin
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm status-select" 
                                            name="siswa[<?= $siswa['id'] ?>][status]" 
                                            data-siswa-id="<?= $siswa['id'] ?>"
                                            required>
                                        <option value="hadir" <?= $currentStatus == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="izin" <?= $currentStatus == 'izin' ? 'selected' : '' ?>>Izin</option>
                                        <option value="sakit" <?= $currentStatus == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                        <option value="alpa" <?= $currentStatus == 'alpa' ? 'selected' : '' ?>>Alpa</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" 
                                           class="form-control form-control-sm keterangan-input" 
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
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <a href="<?= base_url('guru/absensi/show/' . $absensi['id']) ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5" 
                        id="btnSubmit">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
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
        
        if (this.value !== 'hadir' && keteranganInput) {
            keteranganInput.focus();
        }
    });
});

// Form validation
document.getElementById('formEditAbsensi').addEventListener('submit', function(e) {
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
});
</script>

<?= $this->endSection() ?>
