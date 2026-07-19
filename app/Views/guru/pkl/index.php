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
<?= $this->extend(get_device_layout()) ?>

<?= $this->section('actions') ?>
<!-- Top Bar Statistics and Date -->
<div class="hidden md:flex items-center gap-3 bg-gray-50 border border-gray-200 px-4 py-1.5 rounded-full shadow-sm">
    <div class="flex flex-col items-end border-r border-gray-200 pr-3">
        <span class="text-[10px] text-gray-500 uppercase font-bold"><?= $hariIndo[date('l')] ?? date('l') ?></span>
        <span class="text-xs font-semibold text-gray-800"><?= date('d') ?> <?= $bulanIndo[(int)date('m')] ?> <?= date('Y') ?></span>
    </div>
    <div class="flex gap-4 text-center">
        <div>
            <span class="block text-xs font-bold text-blue-600"><?= $stats['total_progress'] ?? 0 ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Total</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-green-600"><?= $stats['approved'] ?? 0 ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Setuju</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-yellow-600"><?= ($stats['submitted'] ?? 0) + ($stats['verified_by_instruktur'] ?? 0) ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Menunggu</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-orange-600"><?= $stats['revision'] ?? 0 ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Revisi</span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="h-full">
    <?= view('components/alerts') ?>

    <?php if (empty($groupedData)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-200">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
            <i class="fas fa-inbox text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Progress</h3>
        <p class="text-gray-500 mt-1">Siswa belum mengirim progress untuk diverifikasi</p>
    </div>
    <?php else: ?>

    <!-- Master-Detail Container -->
    <div id="master-detail-container" class="flex flex-col lg:flex-row gap-6 lg:h-[calc(100vh-12rem)] lg:overflow-hidden">
        
        <!-- Left Panel: Student List (Master) -->
        <div id="list-panel" class="w-full lg:w-80 bg-white rounded-2xl border border-gray-200 flex flex-col overflow-hidden shadow-sm flex-shrink-0 animate-fade-in">
            <!-- Search bar -->
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" id="searchInput" oninput="filterStudents()" 
                           class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 placeholder:text-gray-400 transition-all shadow-sm" 
                           placeholder="Cari siswa...">
                </div>
            </div>
            
            <!-- Student list -->
            <div class="flex-grow overflow-y-auto divide-y divide-gray-100 custom-scrollbar">
                <?php foreach ($groupedData as $student): 
                    $pendingCount = $student['pending_count'];
                    $totalProgress = count($student['progress']);
                    $approvedCount = 0;
                    foreach ($student['progress'] as $p) {
                        if ($p['status'] === 'approved') $approvedCount++;
                    }
                ?>
                <button type="button" onclick="selectStudent(<?= $student['siswa_id'] ?>)" 
                        id="student-btn-<?= $student['siswa_id'] ?>"
                        class="student-item w-full px-4 py-3.5 flex items-center gap-3 hover:bg-gray-50/85 transition-all text-left border-l-4 border-transparent"
                        data-name="<?= strtolower(esc($student['nama_siswa'])) ?>" 
                        data-nis="<?= strtolower(esc($student['nis'])) ?>">
                    
                    <!-- Avatar -->
                    <div class="relative flex-shrink-0">
                        <?php if ($student['profile_photo']): ?>
                            <img src="<?= base_url('profile-photo/' . esc($student['profile_photo'])); ?>" 
                                 class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm" 
                                 alt="<?= esc($student['nama_siswa']) ?>">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-sm shadow-sm">
                                <?= strtoupper(substr(esc($student['nama_siswa']), 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Status dot -->
                        <?php if ($pendingCount > 0): ?>
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-yellow-400 border-2 border-white rounded-full"></span>
                        <?php else: ?>
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Student details -->
                    <div class="flex-grow min-w-0">
                        <h4 class="font-semibold text-gray-800 text-sm truncate leading-snug"><?= esc($student['nama_siswa']) ?></h4>
                        <p class="text-xs text-gray-500 truncate mt-0.5"><?= esc($student['nama_kelas']) ?></p>
                    </div>
                    
                    <!-- Pending Badge / Stats -->
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <?php if ($pendingCount > 0): ?>
                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-[10px] font-bold">
                                <?= $pendingCount ?>
                            </span>
                        <?php endif; ?>
                        <span class="text-[10px] text-gray-400 font-medium">
                            <?= $approvedCount ?>/<?= $totalProgress ?>
                        </span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 text-xs ml-1 flex-shrink-0"></i>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Right Panel: Journal Content (Detail) -->
        <div id="detail-panel" class="flex-grow bg-white rounded-2xl border border-gray-200 flex flex-col overflow-hidden shadow-sm lg:h-full min-h-[400px]">
            
            <!-- Empty state (shown initially on desktop when no student is selected) -->
            <div id="empty-state" class="flex-grow flex flex-col items-center justify-center p-12 text-center text-gray-500">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100 shadow-inner">
                    <i class="fas fa-user-friends text-2xl text-gray-400 animate-pulse"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-700">Pilih Siswa</h3>
                <p class="text-sm mt-1 text-gray-500">Pilih siswa di sebelah kiri untuk melihat dan memverifikasi jurnal PKL</p>
            </div>
            
            <!-- Student Detail Containers -->
            <?php foreach ($groupedData as $student): 
                $totalProgress = count($student['progress']);
                $approvedCount = 0;
                foreach ($student['progress'] as $p) {
                    if ($p['status'] === 'approved') $approvedCount++;
                }
            ?>
            <div id="student-detail-<?= $student['siswa_id'] ?>" class="student-detail-panel hidden flex flex-col h-full overflow-hidden">
                
                <!-- Panel Header -->
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 flex-shrink-0">
                    <div class="flex items-center gap-3.5">
                        <!-- Back button for mobile -->
                        <button type="button" onclick="backToList()" 
                                class="lg:hidden inline-flex items-center justify-center p-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-gray-900 shadow-sm transition-all hover:bg-gray-50">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        
                        <!-- Avatar -->
                        <?php if ($student['profile_photo']): ?>
                            <img src="<?= base_url('profile-photo/' . esc($student['profile_photo'])); ?>" 
                                 class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-md" 
                                 alt="<?= esc($student['nama_siswa']) ?>">
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg border-2 border-white shadow-md">
                                <?= strtoupper(substr(esc($student['nama_siswa']), 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <h3 class="text-base font-bold text-gray-900 leading-tight"><?= esc($student['nama_siswa']) ?></h3>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-1 text-xs text-gray-500">
                                <span class="font-medium text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded">NIS: <?= esc($student['nis']) ?></span>
                                <span class="text-gray-300">&bull;</span>
                                <span class="flex items-center gap-1 font-medium text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded"><i class="fas fa-school text-[10px]"></i> <?= esc($student['nama_kelas']) ?></span>
                                <?php if (!empty($student['nama_perusahaan'])): ?>
                                    <span class="text-gray-300">&bull;</span>
                                    <span class="flex items-center gap-1 text-gray-600"><i class="fas fa-building text-[10px] text-gray-400"></i> <?= esc($student['nama_perusahaan']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-xl font-semibold border border-blue-100 flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-check-circle"></i>
                            Progress: <?= $approvedCount ?>/<?= $totalProgress ?> Disetujui
                        </div>
                    </div>
                </div>
                
                <!-- Panel Content: Scrollable list of entries -->
                <div class="flex-grow overflow-y-auto p-6 bg-gray-50/40 space-y-5 custom-scrollbar">
                    
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2 mb-2">
                        <i class="fas fa-calendar-day text-xs"></i> Riwayat Progress Harian
                    </h4>
                    
                    <?php foreach ($student['progress'] as $p): 
                        $dateObj = new DateTime($p['tanggal']);
                        $dayName = $hariIndo[$dateObj->format('l')] ?? $dateObj->format('l');
                        $dateStr = $dateObj->format('d') . ' ' . $bulanIndo[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');
                        
                        $statusBadge = match($p['status']) {
                            'approved' => ['bg' => 'bg-green-50 text-green-700 border-green-200', 'label' => 'Disetujui', 'icon' => 'fa-check-circle'],
                            'verified_by_instruktur' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Verified Instruktur', 'icon' => 'fa-check-double'],
                            'submitted' => ['bg' => 'bg-yellow-50 text-yellow-700 border-yellow-200', 'label' => 'Menunggu', 'icon' => 'fa-clock'],
                            'revision' => ['bg' => 'bg-orange-50 text-orange-700 border-orange-200', 'label' => 'Revisi', 'icon' => 'fa-edit'],
                            default => ['bg' => 'bg-gray-50 text-gray-600 border-gray-200', 'label' => 'Draft', 'icon' => 'fa-pen']
                        };
                    ?>
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-4 hover:shadow-md transition-all duration-200">
                        
                        <!-- Entry Header -->
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                                    <i class="fas fa-clipboard-list text-base"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-bold text-gray-900 leading-snug"><?= esc($p['nama_task']) ?></h5>
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-1 text-xs text-gray-500">
                                        <?php if (!empty($p['kategori_nama'])): ?>
                                            <span class="font-medium text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded"><?= esc($p['kategori_nama']) ?></span>
                                            <span class="text-gray-300">&bull;</span>
                                        <?php endif; ?>
                                        <span class="flex items-center gap-1"><i class="far fa-calendar text-gray-400"></i> <?= $dayName ?>, <?= $dateStr ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status badge -->
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border <?= $statusBadge['bg'] ?>">
                                <i class="fas <?= $statusBadge['icon'] ?> text-[10px]"></i>
                                <?= $statusBadge['label'] ?>
                            </span>
                        </div>
                        
                        <!-- Description -->
                        <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap pl-1 font-normal">
                            <?= esc($p['deskripsi']) ?>
                        </div>
                        
                        <!-- Photo attachment if available -->
                        <?php if ($p['foto']): ?>
                        <div class="pl-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Dokumentasi</p>
                            <a href="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" target="_blank" 
                               class="group relative inline-block overflow-hidden rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
                                <img src="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" 
                                     class="max-h-40 object-cover transition-transform duration-300 group-hover:scale-105" 
                                     alt="Dokumentasi">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                                    <i class="fas fa-search-plus"></i> Lihat Foto
                                </div>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Feedback history (from Instructor & Advisor) -->
                        <?php if (!empty($p['catatan_instruktur']) || (!empty($p['catatan_pembimbing']) && $p['status'] !== 'approved')): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-1">
                            <?php if (!empty($p['catatan_instruktur'])): ?>
                            <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-3 shadow-sm">
                                <p class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider flex items-center gap-1"><i class="fas fa-building text-[8px]"></i> Catatan Instruktur</p>
                                <p class="text-xs text-indigo-900 mt-1"><?= esc($p['catatan_instruktur']) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($p['catatan_pembimbing']) && $p['status'] !== 'approved'): ?>
                            <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-3 shadow-sm">
                                <p class="text-[10px] font-bold text-orange-700 uppercase tracking-wider flex items-center gap-1"><i class="fas fa-user-tie text-[8px]"></i> Catatan Pembimbing</p>
                                <p class="text-xs text-orange-900 mt-1"><?= esc($p['catatan_pembimbing']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Form Section (Verify / Undo) -->
                        <div class="pt-3 border-t border-gray-100">
                            <?php if ($p['status'] === 'approved'): ?>
                            <!-- Undo verification form -->
                            <div class="flex items-center justify-between gap-4 bg-green-50/40 border border-green-100 rounded-xl p-3.5 pl-4 shadow-inner">
                                <div class="flex items-center gap-2.5 text-xs text-green-800">
                                    <i class="fas fa-check-circle text-base text-green-500 flex-shrink-0"></i>
                                    <div class="min-w-0">
                                        <span class="font-bold">Progress disetujui</span>
                                        <?php if (!empty($p['catatan_pembimbing'])): ?>
                                            <p class="text-green-700 mt-0.5 italic break-words">"<?= esc($p['catatan_pembimbing']) ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <form action="<?= base_url('guru/jurnal-pkl/batal-verifikasi/' . $p['id']); ?>" method="POST" onsubmit="saveActiveSiswa(<?= $student['siswa_id'] ?>)">
                                    <?= csrf_field(); ?>
                                    <button type="submit" onclick="return confirm('Batalkan verifikasi progress ini?')" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-orange-200 text-orange-700 font-semibold rounded-xl hover:bg-orange-50 hover:border-orange-300 text-xs shadow-sm transition-all active:scale-95 whitespace-nowrap">
                                        <i class="fas fa-undo text-[10px]"></i> Batalkan
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <!-- Verification form -->
                            <form action="<?= base_url('guru/jurnal-pkl/verify/' . $p['id']); ?>" method="POST" onsubmit="saveActiveSiswa(<?= $student['siswa_id'] ?>)" class="space-y-3">
                                <?= csrf_field(); ?>
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 shadow-inner">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Catatan Pembimbing (Opsional)</label>
                                    <textarea name="catatan" rows="2" 
                                              class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none shadow-sm" 
                                              placeholder="Tulis catatan revisi atau catatan persetujuan..."><?= esc($p['catatan_pembimbing'] ?? '') ?></textarea>
                                    
                                    <div class="flex justify-end gap-2.5 mt-3">
                                        <button type="submit" name="status" value="revision" onclick="return confirm('Minta revisi progress ini?')"
                                                class="px-4 py-1.5 border border-orange-200 text-orange-700 font-bold text-xs hover:bg-orange-50 hover:border-orange-300 bg-white rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-1.5">
                                            <i class="fas fa-edit text-[10px]"></i> Minta Revisi
                                        </button>
                                        <button type="submit" name="status" value="approved" onclick="return confirm('Setujui progress ini?')"
                                                class="px-4 py-1.5 bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-1.5">
                                            <i class="fas fa-check text-[10px]"></i> Setujui
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
    </div>
    <?php endif; ?>
</div>

<style>
/* Custom scrollbar helper */
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #E5E7EB;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #D1D5DB;
}

/* Sidebar item active state */
.student-item.active {
    background-color: #EFF6FF;
    border-color: #3B82F6;
}
.student-item.active h4 {
    color: #1D4ED8;
}
</style>

<script>
// Save selected student to localStorage on submit
function saveActiveSiswa(siswaId) {
    localStorage.setItem('selected_siswa_id', siswaId);
}

// Select a student and show their details
function selectStudent(siswaId) {
    // Save selection
    localStorage.setItem('selected_siswa_id', siswaId);
    
    // Manage active state in list
    document.querySelectorAll('.student-item').forEach(item => {
        item.classList.remove('active');
    });
    const selectedBtn = document.getElementById('student-btn-' + siswaId);
    if (selectedBtn) {
        selectedBtn.classList.add('active');
    }
    
    // Hide empty state
    const emptyState = document.getElementById('empty-state');
    if (emptyState) emptyState.classList.add('hidden');
    
    // Show correct detail panel
    document.querySelectorAll('.student-detail-panel').forEach(panel => {
        panel.classList.add('hidden');
    });
    const activePanel = document.getElementById('student-detail-' + siswaId);
    if (activePanel) {
        activePanel.classList.remove('hidden');
    }
    
    // Handle mobile view toggling
    if (window.innerWidth < 1024) {
        document.getElementById('list-panel').classList.add('hidden');
        document.getElementById('detail-panel').classList.remove('hidden');
    }
}

// Back to student list in mobile view
function backToList() {
    document.getElementById('list-panel').classList.remove('hidden');
    document.getElementById('detail-panel').classList.add('hidden');
}

// Search and filter students in list
function filterStudents() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.student-item').forEach(card => {
        const name = card.getAttribute('data-name');
        const nis = card.getAttribute('data-nis');
        if (name.includes(q) || nis.includes(q)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Auto-run on page load
document.addEventListener('DOMContentLoaded', () => {
    const savedSiswaId = localStorage.getItem('selected_siswa_id');
    
    // Check if we should default to list view on mobile
    if (window.innerWidth < 1024) {
        document.getElementById('detail-panel').classList.add('hidden');
        document.getElementById('list-panel').classList.remove('hidden');
    }
    
    if (savedSiswaId) {
        const targetBtn = document.getElementById('student-btn-' + savedSiswaId);
        // Verify if student still exists in list (for case when they have no tasks left or deleted)
        if (targetBtn && targetBtn.style.display !== 'none') {
            selectStudent(savedSiswaId);
            return;
        }
    }
    
    // Default to select first student if on desktop
    if (window.innerWidth >= 1024) {
        const firstBtn = document.querySelector('.student-item');
        if (firstBtn) {
            const id = firstBtn.id.replace('student-btn-', '');
            selectStudent(id);
        }
    }
});
</script>
<?= $this->endSection() ?>
