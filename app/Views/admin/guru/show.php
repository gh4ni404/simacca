<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
            <p class="text-gray-600"><?= $pageDescription ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?= base_url('admin/guru/edit/' . $guru['id']) ?>" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="<?= base_url('admin/guru') ?>" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-8 text-white mb-6">
        <div class="flex flex-col md:flex-row items-center">
            <!-- Avatar -->
            <div class="mb-6 md:mb-0 md:mr-8">
                <?php if (!empty($userData['profile_photo'])): ?>
                    <img src="<?= base_url('profile-photo/' . esc($userData['profile_photo'])); ?>" 
                         alt="<?= esc($guru['nama_lengkap']); ?>"
                         class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg">
                <?php else: ?>
                    <div class="h-32 w-32 rounded-full bg-white/20 flex items-center justify-center border-4 border-white shadow-lg">
                        <?php if ($guru['jenis_kelamin'] == 'L'): ?>
                            <i class="fas fa-male text-5xl"></i>
                        <?php else: ?>
                            <i class="fas fa-female text-5xl"></i>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Profile Info -->
            <div class="flex-1">
                <h3 class="text-2xl font-bold"><?= esc($guru['nama_lengkap']) ?></h3>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <p class="text-sm opacity-80">NIP</p>
                        <p class="font-medium"><?= esc($guru['nip']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Role</p>
                        <p class="font-medium"><?= $guru['is_wali_kelas'] ? 'Wali Kelas' : 'Guru Mata Pelajaran' ?></p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Jenis Kelamin</p>
                        <p class="font-medium"><?= $guru['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Status</p>
                        <p class="font-medium"><?= $userData['is_active'] ? 'Aktif' : 'Nonaktif' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Account Information -->
        <div class="border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user-circle mr-2 text-gray-600"></i> Informasi Akun
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Username</span>
                    <span class="font-medium text-gray-800"><?= esc($userData['username']) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Email</span>
                    <span class="font-medium text-gray-800"><?= esc($userData['email'] ?? '-') ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Terdaftar Sejak</span>
                    <span class="font-medium text-gray-800">
                        <?= $userData['created_at'] ? date('d M Y', strtotime($userData['created_at'])) : '-' ?>
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Role Sistem</span>
                    <span class="font-medium text-gray-800"><?= get_role_name_from_role($userData['role']) ?></span>
                </div>
            </div>
        </div>

        <!-- Teaching Information -->
        <div class="border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chalkboard-teacher mr-2 text-gray-600"></i> Informasi Mengajar
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Mata Pelajaran</span>
                    <span class="font-medium text-gray-800"><?= esc($guru['nama_mapel'] ?? '-') ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Status Wali Kelas</span>
                    <span class="font-medium text-gray-800">
                        <?= $guru['is_wali_kelas'] ? 'Ya' : 'Tidak' ?>
                    </span>
                </div>
                <?php if ($guru['is_wali_kelas'] && $guru['kelas_id'] && $kelas): ?>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Kelas yang Diampu</span>
                        <span class="font-medium text-gray-800"><?= esc($kelas['nama_kelas']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Data Dibuat</span>
                    <span class="font-medium text-gray-800">
                        <?= $guru['created_at'] ? date('d M Y H:i', strtotime($guru['created_at'])) : '-' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 border border-gray-200 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php if ($userData['is_active']): ?>
                <a href="<?= base_url('admin/guru/nonaktifkan/' . $guru['id']) ?>" 
                   class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100"
                   onclick="return confirm('Nonaktifkan guru ini?')">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <p class="font-medium text-yellow-800">Nonaktifkan</p>
                        <p class="text-sm text-yellow-600">Sementara nonaktifkan akun</p>
                    </div>
                </a>
            <?php else: ?>
                <a href="<?= base_url('admin/guru/aktifkan/' . $guru['id']) ?>" 
                   class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100"
                   onclick="return confirm('Aktifkan guru ini?')">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p class="font-medium text-green-800">Aktifkan</p>
                        <p class="text-sm text-green-600">Aktifkan kembali akun</p>
                    </div>
                </a>
            <?php endif; ?>
            
            <a href="<?= base_url('admin/guru/edit/' . $guru['id']) ?>" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <p class="font-medium text-blue-800">Edit Data</p>
                    <p class="text-sm text-blue-600">Ubah informasi guru</p>
                </div>
            </a>
            
            <button onclick="confirmDelete(<?= $guru['id'] ?>, '<?= esc($guru['nama_lengkap']) ?>')" 
                    class="flex items-center p-4 bg-red-50 rounded-lg hover:bg-red-100">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-trash"></i>
                </div>
                <div>
                    <p class="font-medium text-red-800 text-left">Hapus Data</p>
                    <p class="text-sm text-red-600">Hapus permanen data guru</p>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus data guru <span id="guruName" class="font-semibold"></span>?
                </p>
                <p class="text-xs text-red-500 mt-2">Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="GET">
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Ya, Hapus
                    </button>
                </form>
                <button onclick="closeModal()" 
                        class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Delete confirmation
    function confirmDelete(id, name) {
        document.getElementById('guruName').textContent = name;
        document.getElementById('deleteForm').action = `<?= base_url('admin/guru/hapus/') ?>${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<?php 
// Helper function to get role name
function get_role_name_from_role($role) {
    $roleNames = [
        'admin' => 'Administrator',
        'guru_mapel' => 'Guru Mata Pelajaran',
        'wali_kelas' => 'Wali Kelas',
        'wakakur' => 'Wakil Kepala Kurikulum',
        'siswa' => 'Siswa'
    ];
    return $roleNames[$role] ?? 'Unknown';
}
?>
<?= $this->endSection() ?>