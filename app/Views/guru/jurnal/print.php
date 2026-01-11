<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Jurnal KBM - <?= esc($jurnal['nama_guru']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header dengan Logo */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            position: relative;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 10px;
        }

        .logo {
            width: 80px;
            height: 80px;
        }

        .header-text {
            text-align: center;
            flex: 1;
        }

        .header-text h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .header-text h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-text h3 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-text p {
            font-size: 10pt;
            margin: 2px 0;
        }

        /* Title */
        .document-title {
            text-align: center;
            margin: 30px 0 20px 0;
        }

        .document-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .document-title h3 {
            font-size: 13pt;
            font-weight: bold;
        }

        /* Student Info */
        .student-info {
            margin: 20px 0;
            padding-left: 50px;
        }

        .student-info table {
            border: none;
            margin-bottom: 20px;
        }

        .student-info td {
            padding: 5px 10px;
            border: none;
        }

        .student-info .label {
            width: 150px;
            font-weight: normal;
        }

        .student-info .colon {
            width: 20px;
        }

        .student-info .value {
            font-weight: normal;
        }

        /* Table */
        .jurnal-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .jurnal-table th,
        .jurnal-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .jurnal-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .jurnal-table .col-no {
            width: 5%;
            text-align: center;
        }

        .jurnal-table .col-tanggal {
            width: 15%;
        }

        .jurnal-table .col-jenis {
            width: 18%;
        }

        .jurnal-table .col-deskripsi {
            width: 32%;
        }

        .jurnal-table .col-dokumentasi {
            width: 20%;
            text-align: center;
        }

        .jurnal-table .col-paraf {
            width: 10%;
        }

        .jurnal-table td.center {
            text-align: center;
        }

        .dokumentasi-img {
            max-width: 100%;
            height: auto;
            max-height: 100px;
            border: 1px solid #ddd;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding: 0 50px;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-box p {
            margin-bottom: 80px;
        }

        .signature-box .name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
        }

        .signature-box .nip {
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14pt;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background: #2563eb;
        }

        .print-button i {
            margin-right: 8px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .print-button {
                display: none;
            }

            .container {
                padding: 0;
            }

            @page {
                margin: 15mm;
            }
        }

        .location-date {
            text-align: right;
            margin: 20px 50px 40px 0;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Dokumen
    </button>

    <div class="container">
        <!-- Header dengan Logo -->
        <div class="header">
            <div class="header-content">
                <div class="logo">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="45" fill="#4f46e5" stroke="#000" stroke-width="2"/>
                        <text x="50" y="60" font-size="40" font-weight="bold" text-anchor="middle" fill="#fff">S</text>
                    </svg>
                </div>
                <div class="header-text">
                    <h1>PEMERINTAH PROPINSI SULAWESI SELATAN</h1>
                    <h2>DINAS PENDIDIKAN</h2>
                    <h3>CABANG DINAS PENDIDIKAN WILAYAH III</h3>
                    <h2>UPT SMKN 8 BONE</h2>
                    <p><em>Alamat : Jln. Poros Bone – Sengkang Welado Kec. Ajangale Kode Pos 92755</em></p>
                    <p><em>Email : smkn8bone@gmail.com</em></p>
                </div>
                <div class="logo">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="45" fill="#dc2626" stroke="#000" stroke-width="2"/>
                        <text x="50" y="60" font-size="40" font-weight="bold" text-anchor="middle" fill="#fff">B</text>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="document-title">
            <h2>JURNAL KEGIATAN BELAJAR MENGAJAR</h2>
            <h3>SMKN 8 BONE</h3>
        </div>

        <!-- Student/Teacher Info -->
        <div class="student-info">
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($jurnal['nama_guru']) ?></td>
                </tr>
                <tr>
                    <td class="label">Mata Pelajaran</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($jurnal['nama_mapel']) ?></td>
                </tr>
                <tr>
                    <td class="label">Kelas</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($jurnal['nama_kelas']) ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="colon">:</td>
                    <td class="value"><?= date('d F Y', strtotime($jurnal['tanggal'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Pertemuan Ke</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($jurnal['pertemuan_ke']) ?></td>
                </tr>
            </table>
        </div>

        <!-- Jurnal Table -->
        <table class="jurnal-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-tanggal">Komponen</th>
                    <th class="col-deskripsi" colspan="2">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td><strong>Materi Pembelajaran</strong></td>
                    <td colspan="2"><?= esc($jurnal['materi_pembelajaran']) ?></td>
                </tr>
                <tr>
                    <td class="center">2</td>
                    <td><strong>Tujuan Pembelajaran</strong></td>
                    <td colspan="2"><?= nl2br(esc($jurnal['tujuan_pembelajaran'])) ?></td>
                </tr>
                <tr>
                    <td class="center">3</td>
                    <td><strong>Kegiatan Pembelajaran</strong></td>
                    <td colspan="2"><?= nl2br(esc($jurnal['kegiatan_pembelajaran'])) ?></td>
                </tr>
                <tr>
                    <td class="center">4</td>
                    <td><strong>Media & Alat</strong></td>
                    <td colspan="2"><?= !empty($jurnal['media_alat']) ? nl2br(esc($jurnal['media_alat'])) : '<em>Tidak ada data</em>' ?></td>
                </tr>
                <tr>
                    <td class="center">5</td>
                    <td><strong>Penilaian</strong></td>
                    <td colspan="2"><?= !empty($jurnal['penilaian']) ? nl2br(esc($jurnal['penilaian'])) : '<em>Tidak ada data</em>' ?></td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td><strong>Catatan Khusus</strong></td>
                    <td colspan="2"><?= !empty($jurnal['catatan_khusus']) ? nl2br(esc($jurnal['catatan_khusus'])) : '<em>Tidak ada catatan khusus</em>' ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Location and Date -->
        <div class="location-date">
            <p>Bone, <?= date('d F Y') ?></p>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,<br>Kepala Sekolah</p>
                <p class="name">........................................</p>
                <p class="nip">NIP. ......................................</p>
            </div>
            <div class="signature-box">
                <p>Guru Mata Pelajaran</p>
                <p class="name"><?= esc($jurnal['nama_guru']) ?></p>
                <p class="nip">NIP. ......................................</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Sistem KBM SMKN 8 BONE | Dicetak: <?= date('Y-m-d h:i:s A') ?></p>
        </div>
    </div>

    <script>
        // Auto print when loaded (optional)
        window.onload = function() {
            <?php if ($request->getGet('auto') == 'true'): ?>
            window.print();
            <?php endif; ?>
        }
    </script>
</body>
</html>
