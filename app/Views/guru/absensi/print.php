<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Cetak Absensi' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16pt;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 10pt;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .attendance-table th,
        .attendance-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .attendance-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .attendance-table td.center {
            text-align: center;
        }

        .statistics {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item .label {
            font-size: 10pt;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-item .value {
            font-size: 20pt;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-box p {
            margin-bottom: 60px;
        }

        .signature-box .name {
            border-top: 1px solid #333;
            padding-top: 5px;
            display: inline-block;
            min-width: 200px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10pt;
            font-weight: bold;
        }

        .status-hadir {
            background-color: #d4edda;
            color: #155724;
        }

        .status-izin {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-sakit {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-alpa {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 20mm;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14pt;
        }

        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Cetak
    </button>

    <div class="header">
        <h1>DAFTAR HADIR SISWA</h1>
        <h2>SMK NEGERI 8 BONE</h2>
        <p>Jl. Contoh No. 123, Bone, Sulawesi Selatan</p>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>Mata Pelajaran</td>
                <td>: <?= $absensi['nama_mapel'] ?></td>
                <td>Kelas</td>
                <td>: <?= $absensi['nama_kelas'] ?></td>
            </tr>
            <tr>
                <td>Guru Pengampu</td>
                <td>: <?= $absensi['nama_guru'] ?></td>
                <td>Hari/Tanggal</td>
                <td>: <?= $absensi['hari'] ?? '-' ?> / <?= date('d F Y', strtotime($absensi['tanggal'])) ?></td>
            </tr>
            <tr>
                <td>Pertemuan Ke</td>
                <td>: <?= $absensi['pertemuan_ke'] ?></td>
                <td>Jam</td>
                <td>: <?= date('H:i', strtotime($absensi['created_at'])) ?> WIB</td>
            </tr>
        </table>
    </div>

    <div class="statistics">
        <div class="stat-item">
            <div class="label">Total Siswa</div>
            <div class="value"><?= count($absensiDetails) ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Hadir</div>
            <div class="value" style="color: #28a745;"><?= $statistics['hadir'] ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Izin</div>
            <div class="value" style="color: #17a2b8;"><?= $statistics['izin'] ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Sakit</div>
            <div class="value" style="color: #ffc107;"><?= $statistics['sakit'] ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Alpa</div>
            <div class="value" style="color: #dc3545;"><?= $statistics['alpa'] ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Persentase Hadir</div>
            <div class="value" style="color: #28a745;"><?= $statistics['percentage'] ?>%</div>
        </div>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th width="40">No</th>
                <th width="100">NIS</th>
                <th>Nama Siswa</th>
                <th width="100">Status</th>
                <th>Keterangan</th>
                <th width="150">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($absensiDetails)): ?>
            <tr>
                <td colspan="6" class="center">Tidak ada data</td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($absensiDetails as $detail): ?>
                <tr>
                    <td class="center"><?= $no++ ?></td>
                    <td><?= $detail['nis'] ?></td>
                    <td><?= $detail['nama_lengkap'] ?></td>
                    <td class="center">
                        <span class="status-badge status-<?= $detail['status'] ?>">
                            <?= strtoupper($detail['status']) ?>
                        </span>
                    </td>
                    <td><?= $detail['keterangan'] ?? '-' ?></td>
                    <td></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="name">
                (....................................)
            </div>
            <p style="margin-top: 5px; font-size: 10pt;">NIP. ...................................</p>
        </div>
        <div class="signature-box">
            <p>Bone, <?= date('d F Y', strtotime($absensi['tanggal'])) ?><br>Guru Mata Pelajaran</p>
            <div class="name">
                <?= $guru['nama_lengkap'] ?>
            </div>
            <p style="margin-top: 5px; font-size: 10pt;">NIP. <?= $guru['nip'] ?? '-' ?></p>
        </div>
    </div>

    <script>
        // Auto print when loaded
        window.onload = function() {
            // Optional: auto print
            // window.print();
        }
    </script>
</body>
</html>
