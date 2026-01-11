-- ========================================
-- WALI KELAS SETUP DIAGNOSTIC QUERIES
-- Run these queries in your database client
-- ========================================

-- Check 1: Guru with is_wali_kelas = 1
SELECT '=== CHECK 1: Guru as Wali Kelas ===' as info;
SELECT id, nama_lengkap, nip, is_wali_kelas 
FROM guru 
WHERE is_wali_kelas = 1;

-- Check 2: Kelas assigned to wali kelas
SELECT '=== CHECK 2: Kelas with Wali Kelas ===' as info;
SELECT k.id, k.nama_kelas, k.wali_kelas_id, g.nama_lengkap as wali_kelas_nama
FROM kelas k
LEFT JOIN guru g ON g.id = k.wali_kelas_id
WHERE k.wali_kelas_id IS NOT NULL;

-- Check 3: Siswa count per kelas
SELECT '=== CHECK 3: Siswa Count per Kelas ===' as info;
SELECT k.nama_kelas, COUNT(s.id) as jumlah_siswa
FROM kelas k
LEFT JOIN siswa s ON s.kelas_id = k.id
WHERE k.wali_kelas_id IS NOT NULL
GROUP BY k.id, k.nama_kelas;

-- Check 4: Izin submitted
SELECT '=== CHECK 4: Izin Siswa (Last 10) ===' as info;
SELECT 
    i.id,
    s.nama_lengkap as nama_siswa,
    k.nama_kelas,
    i.tanggal,
    i.jenis_izin,
    i.status,
    g.nama_lengkap as wali_kelas
FROM izin_siswa i
JOIN siswa s ON s.id = i.siswa_id
JOIN kelas k ON k.id = s.kelas_id
LEFT JOIN guru g ON g.id = k.wali_kelas_id
ORDER BY i.tanggal DESC
LIMIT 10;

-- Check 5: Complete relationship tree
SELECT '=== CHECK 5: Complete View ===' as info;
SELECT 
    g.id as guru_id,
    g.nama_lengkap as nama_guru,
    k.id as kelas_id,
    k.nama_kelas,
    COUNT(DISTINCT s.id) as total_siswa,
    COUNT(DISTINCT i.id) as total_izin,
    SUM(CASE WHEN i.status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN i.status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
    SUM(CASE WHEN i.status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
FROM guru g
LEFT JOIN kelas k ON k.wali_kelas_id = g.id
LEFT JOIN siswa s ON s.kelas_id = k.id
LEFT JOIN izin_siswa i ON i.siswa_id = s.id
WHERE g.is_wali_kelas = 1
GROUP BY g.id, k.id;

-- ========================================
-- SOLUTION QUERIES (if issues found)
-- ========================================

-- If NO guru with is_wali_kelas:
-- UPDATE guru SET is_wali_kelas = 1 WHERE id = [guru_id];

-- If NO kelas assigned:
-- UPDATE kelas SET wali_kelas_id = [guru_id] WHERE id = [kelas_id];

-- Example: Assign Pak Budi (guru_id=5) to X IPA 1 (kelas_id=10)
-- UPDATE kelas SET wali_kelas_id = 5 WHERE id = 10;
