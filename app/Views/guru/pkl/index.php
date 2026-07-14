<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <?php
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hariIndo = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    ?>

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/20">
                <i class="fas fa-clipboard-check text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold">Verifikasi Jurnal PKL</h1>
                <p class="text-indigo-100 text-sm mt-1">Review dan verifikasi progress siswa</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (!empty($stats) && $stats['total_siswa'] > 0): ?>
    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-800"><?= $stats['total_siswa'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Siswa</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-800"><?= $stats['total_progress'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Total</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-yellow-400">
            <div class="text-2xl font-bold text-yellow-600"><?= $stats['submitted'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Menunggu</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-green-400">
            <div class="text-2xl font-bold text-green-600"><?= $stats['approved'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Disetujui</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-orange-400">
            <div class="text-2xl font-bold text-orange-600"><?= $stats['revision'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Revisi</div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (empty($groupedData)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-inbox text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Progress</h3>
        <p class="text-gray-500 mt-1">Siswa belum mengirim progress untuk diverifikasi</p>
    </div>
    <?php else: ?>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-search text-gray-400"></i>
            <input type="text" id="searchInput" placeholder="Cari nama siswa atau NIS..."
                   class="flex-1 text-sm border-0 focus:ring-0 focus:outline-none"
                   oninput="filterStudents()">
            <button onclick="expandAll()" class="text-xs text-indigo-600 hover:text-indigo-800 whitespace-nowrap">
                <i class="fas fa-expand-alt mr-1"></i>Buka Semua
            </button>
            <button onclick="collapseAll()" class="text-xs text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-compress-alt mr-1"></i>Tutup Semua
            </button>
        </div>
    </div>

    <div id="studentsContainer" class="space-y-3">
        <?php foreach ($groupedData as $student):
            $pendingCount = $student['pending_count'];
            $totalProgress = count($student['progress']);
            $approvedCount = 0;
            foreach ($student['progress'] as $p) {
                if ($p['status'] === 'approved') $approvedCount++;
            }
            $defaultOpen = $pendingCount > 0 ? 'true' : 'false';
        ?>
        <div class="student-card bg-white rounded-xl shadow-sm overflow-hidden" data-name="<?= strtolower(esc($student['nama_siswa'])) ?>" data-nis="<?= strtolower(esc($student['nis'])) ?>">
            <!-- Accordion Header -->
            <button onclick="toggleAccordion(this)"
                    class="w-full px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <?php if ($pendingCount > 0): ?>
                    <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 text-sm font-bold">
                        <?= $pendingCount ?>
                    </span>
                    <?php else: ?>
                    <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 text-sm">
                        <i class="fas fa-check"></i>
                    </span>
                    <?php endif; ?>
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm"><?= esc($student['nama_siswa']) ?></h3>
                        <p class="text-xs text-gray-500">NIS: <?= esc($student['nis']) ?> &middot; <?= esc($student['nama_kelas']) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 text-xs">
                        <span class="text-green-600" title="Disetujui"><i class="fas fa-check-circle"></i> <?= $approvedCount ?></span>
                        <span class="text-gray-400">/</span>
                        <span class="text-gray-500" title="Total"><?= $totalProgress ?></span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform accordion-icon"></i>
                </div>
            </button>

            <!-- Accordion Content -->
            <div class="accordion-content border-t border-gray-100 <?= $defaultOpen === 'true' ? '' : 'hidden' ?>">
                <div class="divide-y divide-gray-100">
                    <?php foreach ($student['progress'] as $p):
                        $dateObj = new DateTime($p['tanggal']);
                        $statusColor = match($p['status']) {
                            'approved' => 'bg-green-100 text-green-700',
                            'submitted' => 'bg-yellow-100 text-yellow-700',
                            'revision' => 'bg-orange-100 text-orange-700',
                            default => 'bg-gray-100 text-gray-600'
                        };
                    ?>
                    <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <?php if ($p['status'] === 'approved'): ?>
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 text-xs"><i class="fas fa-check"></i></span>
                                <?php elseif ($p['status'] === 'submitted'): ?>
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-100 text-yellow-600 text-xs"><i class="fas fa-clock"></i></span>
                                <?php elseif ($p['status'] === 'revision'): ?>
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-600 text-xs"><i class="fas fa-edit"></i></span>
                                <?php else: ?>
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-500 text-xs"><i class="fas fa-pen"></i></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-800"><?= esc($p['nama_task']) ?></span>
                                    <?php if (!empty($p['kategori_nama'])): ?>
                                    <span class="text-xs text-gray-400">/</span>
                                    <span class="text-xs text-gray-500"><?= esc($p['kategori_nama']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5"><?= $dateObj->format('d/m/Y') ?></p>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2"><?= esc($p['deskripsi']) ?></p>

                                <?php if ($p['foto']): ?>
                                <a href="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" target="_blank" class="inline-flex items-center mt-2 text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-image mr-1"></i>Lihat Foto
                                </a>
                                <?php endif; ?>

                                <?php if (!empty($p['catatan_pembimbing'])): ?>
                                <div class="mt-2 bg-orange-50 border-l-2 border-orange-400 rounded-r px-2 py-1">
                                    <p class="text-xs text-orange-700"><?= esc($p['catatan_pembimbing']) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($p['status'] === 'approved'): ?>
                            <div class="flex-shrink-0">
                                <form action="<?= base_url('guru/jurnal-pkl/batal-verifikasi/' . $p['id']); ?>" method="POST">
                                    <?= csrf_field(); ?>
                                    <button type="submit" onclick="return confirm('Batalkan verifikasi progress ini?')"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 text-xs font-medium">
                                        <i class="fas fa-undo mr-1"></i>Batalkan
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <div class="flex-shrink-0">
                                <a href="<?= base_url('guru/jurnal-pkl/detail/' . $p['id']); ?>"
                                   class="inline-flex items-center px-2.5 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-xs font-medium">
                                    <i class="fas fa-eye mr-1"></i>Review
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleAccordion(btn) {
    const content = btn.nextElementSibling;
    const icon = btn.querySelector('.accordion-icon');
    content.classList.toggle('hidden');
    icon.style.transform = content.classList.contains('hidden') ? '' : 'rotate(180deg)';
}

function expandAll() {
    document.querySelectorAll('.accordion-content').forEach(el => el.classList.remove('hidden'));
    document.querySelectorAll('.accordion-icon').forEach(el => el.style.transform = 'rotate(180deg)');
}

function collapseAll() {
    document.querySelectorAll('.accordion-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.accordion-icon').forEach(el => el.style.transform = '');
}

function filterStudents() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.student-card').forEach(card => {
        const name = card.getAttribute('data-name');
        const nis = card.getAttribute('data-nis');
        card.style.display = (name.includes(q) || nis.includes(q)) ? '' : 'none';
    });
}
</script>
<?= $this->endSection() ?>
