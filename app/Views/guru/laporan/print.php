<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - <?= esc($namaKelas) ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Header Section */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            margin: 2px 0;
        }

        /* Title Section */
        .title {
            text-align: center;
            margin: 20px 0;
        }

        .title h3 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .title p {
            font-size: 11pt;
            font-style: italic;
        }

        /* Info Section */
        .info-section {
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }

        .info-table td:nth-child(2) {
            width: 10px;
        }

        /* Statistics Box */
        .statistics {
            display: table;
            width: 100%;
            border: 2px solid #000;
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .stat-row {
            display: table-row;
        }

        .stat-cell {
            display: table-cell;
            padding: 8px 12px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .stat-cell.header {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .stat-cell.value {
            font-size: 16pt;
            font-weight: bold;
        }

        /* Main Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            page-break-inside: auto;
        }

        .data-table thead {
            background-color: #e0e0e0;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
        }

        .data-table th {
            font-weight: bold;
            font-size: 10pt;
        }

        .data-table td {
            font-size: 10pt;
        }

        .data-table td.left {
            text-align: left;
        }

        .data-table td.number {
            text-align: center;
        }

        .data-table tr.total {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .data-table tbody tr {
            page-break-inside: avoid;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-box p {
            margin: 5px 0;
        }

        .signature-space {
            height: 60px;
            margin: 10px 0;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 9pt;
            text-align: center;
            font-style: italic;
        }

        /* Notes */
        .notes {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #000;
            background-color: #f9f9f9;
            page-break-inside: avoid;
        }

        .notes h4 {
            font-size: 11pt;
            margin-bottom: 5px;
        }

        .notes ul {
            margin-left: 20px;
        }

        .notes li {
            font-size: 10pt;
            margin: 3px 0;
        }

        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
            }
        }

        /* Button untuk print */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #4338CA;
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Print Dokumen
    </button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>SIMACCA</h1>
            <h2>Sistem Monitoring Absensi dan Catatan Cara Ajar</h2>
            <p>Alamat: [Alamat Sekolah] | Telp: [Nomor Telepon] | Email: [Email Sekolah]</p>
        </div>

        <!-- Title -->
        <div class="title">
            <h3>LAPORAN REKAPITULASI ABSENSI SISWA</h3>
            <p>Periode: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></p>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td><?= esc($namaKelas) ?></td>
                </tr>
                <tr>
                    <td>Guru Pengampu</td>
                    <td>:</td>
                    <td><?= esc($guru['nama_lengkap']) ?></td>
                </tr>
                <tr>
                    <td>Total Siswa</td>
                    <td>:</td>
                    <td><?= $rekap['total_siswa'] ?> siswa</td>
                </tr>
                <tr>
                    <td>Total Pertemuan</td>
                    <td>:</td>
                    <td><?= $rekap['total_pertemuan'] ?> kali</td>
                </tr>
                <tr>
                    <td>Tanggal Cetak</td>
                    <td>:</td>
                    <td><?= date('d F Y, H:i') ?> WIB</td>
                </tr>
            </table>
        </div>

        <!-- Statistics -->
        <div class="statistics">
            <div class="stat-row">
                <div class="stat-cell header">STATISTIK KEHADIRAN</div>
                <div class="stat-cell header">HADIR</div>
                <div class="stat-cell header">SAKIT</div>
                <div class="stat-cell header">IZIN</div>
                <div class="stat-cell header">ALPA</div>
                <div class="stat-cell header">PERSENTASE HADIR</div>
            </div>
            <div class="stat-row">
                <div class="stat-cell">TOTAL</div>
                <div class="stat-cell value"><?= $rekap['total_hadir'] ?></div>
                <div class="stat-cell value"><?= $rekap['total_sakit'] ?></div>
                <div class="stat-cell value"><?= $rekap['total_izin'] ?></div>
                <div class="stat-cell value"><?= $rekap['total_alpa'] ?></div>
                <div class="stat-cell value"><?= $rekap['persentase_hadir'] ?? 0 ?>%</div>
            </div>
        </div>

        <!-- Main Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 30px;">NO</th>
                    <th rowspan="2" style="width: 80px;">NIS</th>
                    <th rowspan="2" style="width: 200px;">NAMA SISWA</th>
                    <th colspan="4">JUMLAH</th>
                    <th rowspan="2" style="width: 80px;">PERSENTASE<br>HADIR (%)</th>
                </tr>
                <tr>
                    <th style="width: 60px;">Hadir</th>
                    <th style="width: 60px;">Sakit</th>
                    <th style="width: 60px;">Izin</th>
                    <th style="width: 60px;">Alpa</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($laporan as $row): ?>
                <tr>
                    <td class="number"><?= $no++ ?></td>
                    <td class="number"><?= esc($row['siswa']['nis']) ?></td>
                    <td class="left"><?= esc($row['siswa']['nama_lengkap']) ?></td>
                    <td class="number"><?= $row['hadir'] ?></td>
                    <td class="number"><?= $row['sakit'] ?></td>
                    <td class="number"><?= $row['izin'] ?></td>
                    <td class="number"><?= $row['alpa'] ?></td>
                    <td class="number">
                        <?php 
                        $persentase = $row['total'] > 0 ? round(($row['hadir'] / $row['total']) * 100, 1) : 0;
                        echo $persentase;
                        ?>%
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="total">
                    <td colspan="3" style="text-align: center;">TOTAL</td>
                    <td class="number"><?= $rekap['total_hadir'] ?></td>
                    <td class="number"><?= $rekap['total_sakit'] ?></td>
                    <td class="number"><?= $rekap['total_izin'] ?></td>
                    <td class="number"><?= $rekap['total_alpa'] ?></td>
                    <td class="number"><?= $rekap['persentase_hadir'] ?? 0 ?>%</td>
                </tr>
            </tbody>
        </table>

        <!-- Notes -->
        <div class="notes">
            <h4>KETERANGAN:</h4>
            <ul>
                <li><strong>Hadir:</strong> Siswa hadir mengikuti pembelajaran</li>
                <li><strong>Sakit:</strong> Siswa tidak hadir karena sakit (dengan surat keterangan)</li>
                <li><strong>Izin:</strong> Siswa tidak hadir dengan izin yang sah</li>
                <li><strong>Alpa:</strong> Siswa tidak hadir tanpa keterangan</li>
                <li><strong>Persentase Hadir:</strong> Dihitung dari total kehadiran dibagi total pertemuan</li>
            </ul>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>Kepala Sekolah</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">(_______________________)</p>
                <p>NIP: ___________________</p>
            </div>
            <div class="signature-box">
                <p>[Tempat], <?= date('d F Y') ?></p>
                <p><strong>Guru Pengampu</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name"><?= esc($guru['nama_lengkap']) ?></p>
                <p>NIP: <?= esc($guru['nip']) ?></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah.</p>
            <p>Dicetak pada: <?= date('d F Y, H:i:s') ?> WIB</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
