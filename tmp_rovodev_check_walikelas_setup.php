<?php
/**
 * Temporary Script to Check Wali Kelas Setup
 * Run: php tmp_rovodev_check_walikelas_setup.php
 */

require 'vendor/autoload.php';

// Load CodeIgniter
$app = require_once FCPATH . '../app/Config/App.php';
$paths = new \Config\Paths();
$app = \Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "\n╔════════════════════════════════════════════════╗\n";
echo "║  WALI KELAS SETUP CHECKER                      ║\n";
echo "╚════════════════════════════════════════════════╝\n\n";

// Check 1: Guru with is_wali_kelas
echo "📋 Check 1: Guru with is_wali_kelas = 1\n";
echo "─────────────────────────────────────────────────\n";
$query = $db->query("
    SELECT id, nama_lengkap, nip, is_wali_kelas 
    FROM guru 
    WHERE is_wali_kelas = 1
");
$results = $query->getResultArray();

if (empty($results)) {
    echo "❌ NO guru marked as wali_kelas!\n\n";
    echo "Solution: Run this SQL:\n";
    echo "UPDATE guru SET is_wali_kelas = 1 WHERE id = [guru_id];\n\n";
} else {
    echo "✅ Found " . count($results) . " wali kelas:\n";
    foreach ($results as $guru) {
        echo "   • ID: {$guru['id']} - {$guru['nama_lengkap']} (NIP: {$guru['nip']})\n";
    }
    echo "\n";
}

// Check 2: Kelas assigned to wali kelas
echo "📋 Check 2: Kelas with wali_kelas_id assigned\n";
echo "─────────────────────────────────────────────────\n";
$query = $db->query("
    SELECT k.id, k.nama_kelas, k.wali_kelas_id, g.nama_lengkap
    FROM kelas k
    LEFT JOIN guru g ON g.id = k.wali_kelas_id
    WHERE k.wali_kelas_id IS NOT NULL
");
$results = $query->getResultArray();

if (empty($results)) {
    echo "❌ NO kelas assigned to wali_kelas!\n\n";
    echo "Solution: Run this SQL:\n";
    echo "UPDATE kelas SET wali_kelas_id = [guru_id] WHERE id = [kelas_id];\n\n";
} else {
    echo "✅ Found " . count($results) . " kelas with wali kelas:\n";
    foreach ($results as $kelas) {
        echo "   • Kelas: {$kelas['nama_kelas']} (ID: {$kelas['id']}) → Wali: {$kelas['nama_lengkap']}\n";
    }
    echo "\n";
}

// Check 3: Siswa in those kelas
echo "📋 Check 3: Siswa in kelas with wali kelas\n";
echo "─────────────────────────────────────────────────\n";
$query = $db->query("
    SELECT k.nama_kelas, COUNT(s.id) as jumlah_siswa
    FROM kelas k
    LEFT JOIN siswa s ON s.kelas_id = k.id
    WHERE k.wali_kelas_id IS NOT NULL
    GROUP BY k.id
");
$results = $query->getResultArray();

if (empty($results)) {
    echo "❌ NO siswa in kelas!\n\n";
} else {
    echo "✅ Siswa count per kelas:\n";
    foreach ($results as $row) {
        echo "   • {$row['nama_kelas']}: {$row['jumlah_siswa']} siswa\n";
    }
    echo "\n";
}

// Check 4: Izin submitted by siswa
echo "📋 Check 4: Izin siswa yang sudah diajukan\n";
echo "─────────────────────────────────────────────────\n";
$query = $db->query("
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
    LIMIT 10
");
$results = $query->getResultArray();

if (empty($results)) {
    echo "❌ NO izin submitted yet!\n\n";
    echo "Solution: Siswa must submit izin from /siswa/izin/create\n\n";
} else {
    echo "✅ Found " . count($results) . " izin (showing last 10):\n";
    foreach ($results as $izin) {
        echo "   • ID: {$izin['id']} - {$izin['nama_siswa']} ({$izin['nama_kelas']})\n";
        echo "     Tanggal: {$izin['tanggal']} | Jenis: {$izin['jenis_izin']} | Status: {$izin['status']}\n";
        echo "     Wali Kelas: " . ($izin['wali_kelas'] ?? 'NOT ASSIGNED') . "\n";
    }
    echo "\n";
}

// Check 5: Detailed check per wali kelas
echo "📋 Check 5: Detailed View - Wali Kelas → Kelas → Siswa → Izin\n";
echo "─────────────────────────────────────────────────\n";
$query = $db->query("
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
    GROUP BY g.id, k.id
");
$results = $query->getResultArray();

if (empty($results)) {
    echo "❌ NO data found!\n\n";
} else {
    foreach ($results as $row) {
        echo "Wali Kelas: {$row['nama_guru']} (ID: {$row['guru_id']})\n";
        if ($row['kelas_id']) {
            echo "  └─ Kelas: {$row['nama_kelas']} (ID: {$row['kelas_id']})\n";
            echo "     ├─ Total Siswa: {$row['total_siswa']}\n";
            echo "     └─ Total Izin: {$row['total_izin']}\n";
            if ($row['total_izin'] > 0) {
                echo "        ├─ Pending: {$row['pending']}\n";
                echo "        ├─ Disetujui: {$row['disetujui']}\n";
                echo "        └─ Ditolak: {$row['ditolak']}\n";
            }
        } else {
            echo "  └─ ❌ NO KELAS ASSIGNED\n";
        }
        echo "\n";
    }
}

echo "═════════════════════════════════════════════════\n";
echo "✅ Check completed! Review the results above.\n\n";
echo "If issues found, run the suggested SQL commands.\n";
echo "═════════════════════════════════════════════════\n\n";
