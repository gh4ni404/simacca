<?php

namespace App\Controllers\Wakakur;

use App\Controllers\BaseController;
use App\Services\AbsensiGuruService;
use App\Models\GuruModel;
use CodeIgniter\I18n\Time;

class AbsensiGuruController extends BaseController
{
    protected $absensiGuruService;
    protected $guruModel;

    public function __construct()
    {
        $this->absensiGuruService = new AbsensiGuruService();
        $this->guruModel = new GuruModel();
    }

    /**
     * Display monitoring absensi guru (real-time today)
     */
    public function index()
    {
        // Get today's summary
        $summaryResult = $this->absensiGuruService->getTodaySummary();
        $summary = $summaryResult['success'] ? $summaryResult['data'] : [];

        // Get today's absensi list
        $filters = [
            'tanggal' => $this->request->getGet('tanggal') ?? Time::today()->toDateString(),
            'status' => $this->request->getGet('status'),
            'guru_id' => $this->request->getGet('guru_id'),
            'per_page' => 50
        ];

        $result = $this->absensiGuruService->getAllAbsensiForAdmin($filters);
        $absensiList = $result['success'] ? $result['data']['data'] : [];
        $pager = $result['success'] ? $result['data']['pager'] : null;

        // Check if this is an AJAX request for auto-refresh
        if ($this->request->isAJAX() || $this->request->getGet('ajax')) {
            return $this->response->setJSON([
                'success' => true,
                'summary' => $summary,
                'absensiList' => $absensiList
            ]);
        }

        // Get all guru for filter dropdown
        $guruList = $this->guruModel
            ->select('guru.id, guru.nama_lengkap as nama, guru.nip')
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Monitoring Absensi Guru',
            'summary' => $summary,
            'absensiList' => $absensiList,
            'guruList' => $guruList,
            'pager' => $pager,
            'filters' => $filters
        ];

        return view('wakakur/absensi_guru/index', $data);
    }

    /**
     * Display laporan absensi guru (historical data)
     */
    public function laporan()
    {
        // Get filter parameters
        $filters = [
            'bulan' => $this->request->getGet('bulan') ?? Time::now()->month,
            'tahun' => $this->request->getGet('tahun') ?? Time::now()->year,
            'status' => $this->request->getGet('status'),
            'guru_id' => $this->request->getGet('guru_id'),
            'per_page' => 50
        ];

        // Get absensi data
        $result = $this->absensiGuruService->getAllAbsensiForAdmin($filters);
        $absensiList = $result['success'] ? $result['data']['data'] : [];
        $pager = $result['success'] ? $result['data']['pager'] : null;

        // Get all guru for filter dropdown
        $guruList = $this->guruModel
            ->select('guru.id, guru.nama_lengkap as nama, guru.nip')
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();

        // Calculate statistics for the period
        $allData = $this->absensiGuruService->getAllAbsensiForAdmin(array_merge($filters, ['per_page' => 10000]));
        $stats = $this->calculateStats($allData['success'] ? $allData['data']['data'] : []);

        $data = [
            'title' => 'Laporan Absensi Guru',
            'absensiList' => $absensiList,
            'guruList' => $guruList,
            'pager' => $pager,
            'filters' => $filters,
            'stats' => $stats
        ];

        return view('wakakur/absensi_guru/laporan', $data);
    }

    /**
     * Show detail absensi guru
     */
    public function detail($guruId)
    {
        // Get guru info
        $guru = $this->guruModel->find($guruId);
        if (!$guru) {
            return redirect()->to('/wakakur/absensi-guru')->with('error', 'Data guru tidak ditemukan');
        }

        // Get filter parameters
        $filters = [
            'bulan' => $this->request->getGet('bulan') ?? Time::now()->month,
            'tahun' => $this->request->getGet('tahun') ?? Time::now()->year,
            'status' => $this->request->getGet('status'),
            'per_page' => 31
        ];

        // Get absensi history
        $result = $this->absensiGuruService->getHistory($guruId, $filters);
        $absensiList = $result['success'] ? $result['data']['data'] : [];
        $pager = $result['success'] ? $result['data']['pager'] : null;

        // Get monthly stats
        $statsResult = $this->absensiGuruService->getMonthlyStats($guruId, $filters['bulan'], $filters['tahun']);
        $stats = $statsResult['success'] ? $statsResult['data'] : [];

        $data = [
            'title' => 'Detail Absensi Guru - ' . $guru['nama'],
            'guru' => $guru,
            'absensiList' => $absensiList,
            'pager' => $pager,
            'filters' => $filters,
            'stats' => $stats
        ];

        return view('wakakur/absensi_guru/detail', $data);
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // Get filter parameters
        $filters = [
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'bulan' => $this->request->getGet('bulan'),
            'tahun' => $this->request->getGet('tahun'),
            'status' => $this->request->getGet('status'),
            'guru_id' => $this->request->getGet('guru_id'),
        ];

        // Generate laporan
        $result = $this->absensiGuruService->generateLaporan($filters);
        
        if (!$result['success']) {
            return redirect()->back()->with('error', 'Gagal generate laporan');
        }

        $data = $result['data'];

        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'NIP');
        $sheet->setCellValue('D1', 'Nama Guru');
        $sheet->setCellValue('E1', 'Jam Masuk');
        $sheet->setCellValue('F1', 'Jam Keluar');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'Keterangan Masuk');
        $sheet->setCellValue('I1', 'Keterangan Keluar');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        $no = 1;
        foreach ($data as $absensi) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $absensi['tanggal']);
            $sheet->setCellValue('C' . $row, $absensi['nip'] ?? '-');
            $sheet->setCellValue('D' . $row, $absensi['nama_guru']);
            $sheet->setCellValue('E' . $row, $absensi['check_in'] ?? '-');
            $sheet->setCellValue('F' . $row, $absensi['check_out'] ?? '-');
            $sheet->setCellValue('G' . $row, ucfirst($absensi['status']));
            $sheet->setCellValue('H' . $row, $absensi['catatan'] ?? '-');
            $sheet->setCellValue('I' . $row, $absensi['early_checkout_reason'] ?? '-');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate filename
        $filename = 'Laporan_Absensi_Guru_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Calculate statistics from absensi data
     */
    private function calculateStats(array $data): array
    {
        $stats = [
            'total_records' => count($data),
            'total_hadir' => 0,
            'total_terlambat' => 0,
            'total_izin' => 0,
            'total_sakit' => 0,
            'total_alpha' => 0,
            'total_checkout' => 0,
        ];

        foreach ($data as $record) {
            switch ($record['status']) {
                case 'hadir':
                    $stats['total_hadir']++;
                    break;
                case 'terlambat':
                    $stats['total_terlambat']++;
                    break;
                case 'izin':
                    $stats['total_izin']++;
                    break;
                case 'sakit':
                    $stats['total_sakit']++;
                    break;
                case 'alpha':
                    $stats['total_alpha']++;
                    break;
            }

            if (!empty($record['check_out'])) {
                $stats['total_checkout']++;
            }
        }

        return $stats;
    }
}
