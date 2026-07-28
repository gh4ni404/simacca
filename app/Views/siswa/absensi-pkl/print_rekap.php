<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= esc($siswa['nama_lengkap']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 15mm 12mm 15mm 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
            background: white;
            padding: 10px;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
        }

        /* ========== KOP SURAT ========== */
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 4px double #000;
            padding-bottom: 10px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 0 10px;
        }

        .logo {
            width: 70px;
            height: 70px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .header-text {
            text-align: center;
            flex: 1;
        }

        .header-text h3 {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1px;
        }

        .header-text h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .header-text p {
            font-size: 8pt;
            margin-bottom: 1px;
            font-style: italic;
        }

        /* ========== DOCUMENT TITLE ========== */
        .title-section {
            text-align: center;
            margin: 18px 0 15px 0;
        }

        .title-section h1 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .title-section h2 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ========== INFO FIELDS ========== */
        .info-section {
            margin-bottom: 20px;
        }

        .info-table {
            width: 60%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: middle;
            font-size: 10pt;
        }

        .info-table td.label-cell {
            width: 160px;
            white-space: nowrap;
        }

        .info-table td.colon-cell {
            width: 15px;
            text-align: center;
        }

        .info-table td.value-cell {
            min-width: 180px;
        }

        /* ========== MONTHLY BLOCK ========== */
        .monthly-block {
            margin-bottom: 15px;
        }

        .month-label {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 6px;
        }

        /* ========== ATTENDANCE TABLE ========== */
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .attendance-table th,
        .attendance-table td {
            border: 1px solid #000;
            padding: 7px 5px;
            font-size: 9.5pt;
            vertical-align: middle;
        }

        .attendance-table th {
            text-align: center;
            font-weight: bold;
            background-color: #fff;
        }

        .attendance-table td.center {
            text-align: center;
        }

        .col-no {
            width: 35px;
            text-align: center;
        }

        .col-hari {
            width: 90px;
            text-align: center;
        }

        .col-tanggal {
            width: 130px;
            text-align: center;
        }

        .col-jam {
            width: 70px;
            text-align: center;
        }

        .col-catatan {
            text-align: left;
        }

        /* ========== SUMMARY + SIGNATURE GROUP ========== */
        .footer-group {
            page-break-inside: avoid;
        }

        /* ========== SUMMARY TABLE ========== */
        .summary-section {
            width: 55%;
            margin-bottom: 25px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .summary-table th {
            font-weight: bold;
            text-align: left;
        }

        .summary-table td.label-cell {
            width: 130px;
        }

        .summary-table td.colon-cell {
            width: 20px;
            text-align: center;
        }

        .summary-table td.value-cell {
            width: 60px;
            text-align: center;
        }

        .summary-table td.unit-cell {
            text-align: left;
            padding-left: 6px;
        }

        /* ========== SIGNATURE ========== */
        .signature-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
            min-width: 200px;
        }

        .signature-box p {
            font-size: 10pt;
            margin-bottom: 55px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 160px;
            margin: 0 auto;
            padding-top: 4px;
            font-size: 10pt;
        }

        /* ========== PRINT CONTROLS ========== */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
        }

        .print-btn, .close-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 10pt;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .print-btn {
            background-color: #007bff;
            color: #fff;
        }

        .print-btn:hover { background-color: #0056b3; }

        .close-btn {
            background-color: #6c757d;
            color: #fff;
        }

        .close-btn:hover { background-color: #5a6268; }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 12mm; }
        }
    </style>
</head>

<body>
    <!-- Print Controls -->
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak</button>
        <button class="close-btn" onclick="window.close()">✕ Tutup</button>
    </div>

    <div class="container">

        <!-- ===== KOP SURAT ===== -->
        <div class="header">
            <div class="header-content">
                <div class="logo">
                    <img src="<?= base_url('assets/images/provinsi.png') ?>" alt="Logo Provinsi">
                </div>
                <div class="header-text">
                    <h3>PEMERINTAH PROVINSI SULAWESI SELATAN</h3>
                    <h3>DINAS PENDIDIKAN</h3>
                    <h2>SMKN 8 BONE</h2>
                    <p>Alamat: Jln. Poros Bone – Sengkang Welado Kec. Ajangale Kode Pos 92755</p>
                    <p>Email: smkn8bone@gmail.com</p>
                </div>
                <div class="logo">
                    <img src="<?= base_url('assets/images/sekolah.png') ?>" alt="Logo Sekolah">
                </div>
            </div>
        </div>

        <!-- ===== DOCUMENT TITLE ===== -->
        <div class="title-section">
            <h1>DAFTAR HADIR PKL</h1>
            <?php if (!empty($siswa['nama_kelas'])): ?>
            <h2><?= esc($siswa['nama_kelas']) ?></h2>
            <?php endif; ?>
        </div>

        <!-- ===== INFO FIELDS ===== -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label-cell">Nama Peserta Didik</td>
                    <td class="colon-cell">:</td>
                    <td class="value-cell"><strong><?= esc($siswa['nama_lengkap']) ?></strong></td>
                </tr>
                <tr>
                    <td class="label-cell">Dunia Kerja Tempat PKL</td>
                    <td class="colon-cell">:</td>
                    <td class="value-cell"><?= esc($tempatPkl['nama_perusahaan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Nama Instruktur</td>
                    <td class="colon-cell">:</td>
                    <td class="value-cell"><?= esc($instruktur['nama_lengkap'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Nama Guru Mapel PKL</td>
                    <td class="colon-cell">:</td>
                    <td class="value-cell"><?= esc($pembimbing['nama_guru'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <!-- ===== MONTHLY ATTENDANCE TABLES ===== -->
        <?php
        $totalSakit = 0;
        $totalIzin  = 0;
        $totalAlpa  = 0;
        $rowNum = 0;
        ?>

        <?php foreach ($weeks as $week): ?>
            <div class="monthly-block">
                <div class="month-label">
                    BULAN : <?= esc($week['week_label']) ?>
                </div>

                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th class="col-no">NO</th>
                            <th class="col-hari">HARI</th>
                            <th class="col-tanggal">TANGGAL</th>
                            <th class="col-jam">JAM<br>DATANG</th>
                            <th class="col-jam">JAM<br>PULANG</th>
                            <th class="col-catatan">Catatan*</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($week['days'] as $day): ?>
                            <?php
                            $dateStr         = $day['date_str'];
                            $absensi         = $attendanceLookup[$dateStr] ?? null;
                            $status          = $absensi['status'] ?? '';
                            $keterangan      = $absensi['keterangan'] ?? '';
                            $keteranganUmum  = $absensi['keterangan_umum'] ?? '';
                            $waktuAbsen      = $absensi['waktu_absen'] ?? '';

                            // Count totals
                            if ($status === 'sakit') $totalSakit++;
                            elseif ($status === 'izin') $totalIzin++;
                            elseif ($status === 'alpa') $totalAlpa++;

                            $rowNum++;

                            // Jam datang only when hadir
                            $jamDatang = ($waktuAbsen && $status === 'hadir')
                                ? date('H:i', strtotime($waktuAbsen))
                                : '';
                            $waktuPulang = $absensi['waktu_pulang'] ?? '';
                            $jamPulang = ($waktuPulang && $status === 'hadir')
                                ? date('H:i', strtotime($waktuPulang))
                                : '';

                            // Catatan priority:
                            // 1. absensi_pkl.keterangan_umum
                            // 2. absensi_pkl_detail.keterangan (hanya jika sakit/izin/alpa)
                            if (!empty($keteranganUmum)) {
                                $catatanDisplay = $keteranganUmum;
                            } elseif (in_array($status, ['sakit', 'izin', 'alpa']) && !empty($keterangan)) {
                                $catatanDisplay = $keterangan;
                            } else {
                                $catatanDisplay = '';
                            }
                            ?>
                            <tr>
                                <td class="center"><?= $rowNum ?></td>
                                <td class="center"><?= esc($day['day_name']) ?></td>
                                <td class="center"><?= esc($day['display_date']) ?></td>
                                <td class="center"><?= esc($jamDatang) ?></td>
                                <td class="center"><?= esc($jamPulang) ?></td>
                                <td><?= esc($catatanDisplay) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <!-- ===== KEHADIRAN SUMMARY + SIGNATURE (satu halaman) ===== -->
        <div class="footer-group">
            <div class="summary-section">
                <table class="summary-table">
                    <tr>
                        <th colspan="4">Kehadiran</th>
                    </tr>
                    <tr>
                        <td class="label-cell">Sakit</td>
                        <td class="colon-cell">:</td>
                        <td class="value-cell"><?= $totalSakit ?></td>
                        <td class="unit-cell">Hari</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Ijin</td>
                        <td class="colon-cell">:</td>
                        <td class="value-cell"><?= $totalIzin ?></td>
                        <td class="unit-cell">Hari</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Tanpa Keterangan</td>
                        <td class="colon-cell">:</td>
                        <td class="value-cell"><?= $totalAlpa ?></td>
                        <td class="unit-cell">Hari</td>
                    </tr>
                </table>
            </div>

            <!-- ===== INSTRUCTOR SIGNATURE ===== -->
            <div class="signature-section">
                <div class="signature-box">
                    <p>Tanda Tangan Instruktur</p>
                    <div class="signature-line">
                        <?= esc($instruktur['nama_lengkap'] ?? '') ?>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.container -->
</body>

</html>
