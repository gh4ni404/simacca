<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Jadwal Mengajar</h1>
        <p class="text-gray-600">Edit data jadwal mengajar</p>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?= base_url('admin/jadwal/update/' . $jadwal['id']); ?>" method="post" id="jadwalForm">
            <?= csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Guru -->
                <div>
                    <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Guru *
                    </label>
                    <select id="guru_id"
                        name="guru_id"
                        class="w-full px-4 py-2 border <?= session('errors.guru_id') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">Pilih Guru</option>
                        <?php foreach ($guruOptions as $id => $nama): ?>
                            <option value="<?= $id; ?>" <?= (old('guru_id', $jadwal['guru_id']) == $id) ? 'selected' : ''; ?>>
                                <?= $nama; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.guru_id')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.guru_id'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label for="mata_pelajaran_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Mata Pelajaran *
                    </label>
                    <select id="mata_pelajaran_id"
                        name="mata_pelajaran_id"
                        class="w-full px-4 py-2 border <?= session('errors.mata_pelajaran_id') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">Pilih Mata Pelajaran</option>
                        <?php foreach ($mapelOptions as $id => $nama): ?>
                            <option value="<?= $id; ?>" <?= (old('mata_pelajaran_id', $jadwal['mata_pelajaran_id']) == $id) ? 'selected' : ''; ?>>
                                <?= $nama; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.mata_pelajaran_id')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.mata_pelajaran_id'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Kelas -->
                <div>
                    <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kelas *
                    </label>
                    <select id="kelas_id"
                        name="kelas_id"
                        class="w-full px-4 py-2 border <?= session('errors.kelas_id') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">Pilih Kelas</option>
                        <?php foreach ($kelasOptions as $id => $nama): ?>
                            <option value="<?= $id; ?>" <?= (old('kelas_id', $jadwal['kelas_id']) == $id) ? 'selected' : ''; ?>>
                                <?= $nama; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.kelas_id')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.kelas_id'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Hari -->
                <div>
                    <label for="hari" class="block text-sm font-medium text-gray-700 mb-2">
                        Hari *
                    </label>
                    <select id="hari"
                        name="hari"
                        class="w-full px-4 py-2 border <?= session('errors.hari') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">Pilih Hari</option>
                        <?php foreach ($hariList as $key => $value): ?>
                            <option value="<?= $key; ?>" <?= (old('hari', $jadwal['hari']) == $key) ? 'selected' : ''; ?>>
                                <?= $value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.hari')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.hari'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Jam Mulai -->
                <div>
                    <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                        Jam Mulai *
                    </label>
                    <input type="text"
                        id="jam_mulai"
                        name="jam_mulai"
                        value="<?= old('jam_mulai', $jadwal['jam_mulai']); ?>"
                        class="timepicker w-full px-4 py-2 border <?= session('errors.jam_mulai') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <?php if (session('errors.jam_mulai')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.jam_mulai'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Jam Selesai -->
                <div>
                    <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                        Jam Selesai *
                    </label>
                    <input type="text"
                        id="jam_selesai"
                        name="jam_selesai"
                        value="<?= old('jam_selesai', $jadwal['jam_selesai']); ?>"
                        class="timepicker w-full px-4 py-2 border <?= session('errors.jam_selesai') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <?php if (session('errors.jam_selesai')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.jam_selesai'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Semester -->
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester *
                    </label>
                    <select id="semester"
                        name="semester"
                        class="w-full px-4 py-2 border <?= session('errors.semester') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">Pilih Semester</option>
                        <?php foreach ($semesterList as $key => $value): ?>
                            <option value="<?= $key; ?>" <?= (old('semester', $jadwal['semester']) == $key) ? 'selected' : ''; ?>>
                                <?= $value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.semester')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.semester'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Tahun Ajaran -->
                <div>
                    <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun Ajaran *
                    </label>
                    <input type="text"
                        id="tahun_ajaran"
                        name="tahun_ajaran"
                        value="<?= old('tahun_ajaran', $jadwal['tahun_ajaran']); ?>"
                        class="w-full px-4 py-2 border <?= session('errors.tahun_ajaran') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="2024/2025"
                        required>
                    <?php if (session('errors.tahun_ajaran')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.tahun_ajaran'); ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">Format: 2024/2025</p>
                </div>
            </div>

            <!-- Konflik Check -->
            <div id="conflictAlert" class="hidden mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Konflik Jadwal Ditemukan!</strong>
                    <span class="block sm:inline" id="conflictMessage"></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/jadwal'); ?>"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center"
                    id="submitBtn">
                    <i class="fas fa-save mr-2"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const conflictAlert = document.getElementById('conflictAlert');
        const conflictMessage = document.getElementById('conflictMessage');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('jadwalForm');

        // Fungsi untuk check konflik
        function checkConflict() {
            const guruId = document.getElementById('guru_id').value;
            const kelasId = document.getElementById('kelas_id').value;
            const hari = document.getElementById('hari').value;
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = document.getElementById('jam_selesai').value;

            if (!guruId || !kelasId || !hari || !jamMulai || !jamSelesai) {
                return;
            }

            // Kirim AJAX request untuk check konflik
            fetch('<?= base_url("admin/jadwal/checkConflict"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        'guru_id': guruId,
                        'kelas_id': kelasId,
                        'hari': hari,
                        'jam_mulai': jamMulai,
                        'jam_selesai': jamSelesai,
                        'exclude_id': '<?= $jadwal["id"] ?>',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.conflict_guru || data.conflict_kelas) {
                        let messages = [];

                        if (data.conflict_guru) {
                            messages.push('Guru sudah memiliki jadwal pada waktu yang sama.');
                        }

                        if (data.conflict_kelas) {
                            messages.push('Kelas sudah memiliki jadwal pada waktu yang sama.');
                        }

                        conflictMessage.textContent = messages.join(' ');
                        conflictAlert.classList.remove('hidden');
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        conflictAlert.classList.add('hidden');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                })
                .catch(error => {
                    console.error('Error checking conflict:', error);
                });
        }

        // Event listeners untuk field yang mempengaruhi konflik
        ['guru_id', 'kelas_id', 'hari', 'jam_mulai', 'jam_selesai'].forEach(fieldId => {
            document.getElementById(fieldId).addEventListener('change', checkConflict);
            document.getElementById(fieldId).addEventListener('input', checkConflict);
        });

        // Validasi jam
        document.getElementById('jam_selesai').addEventListener('change', function() {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = this.value;

            if (jamMulai && jamSelesai && jamSelesai <= jamMulai) {
                alert('Jam selesai harus lebih besar dari jam mulai!');
                this.value = '';
            }
        });

        // Validasi tahun ajaran
        document.getElementById('tahun_ajaran').addEventListener('input', function(e) {
            const value = e.target.value;
            const pattern = /^\d{4}\/\d{4}$/;

            if (!pattern.test(value)) {
                e.target.setCustomValidity('Format tahun ajaran harus: 2024/2025');
            } else {
                e.target.setCustomValidity('');
            }
        });
    });
</script>
<?= $this->endSection() ?>