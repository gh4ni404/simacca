<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;
use App\Models\GuruModel;
use App\Models\AbsensiGuruModel;

/**
 * Auto-mark alpha for teachers who haven't checked in by 10:00 AM
 * 
 * Usage:
 *   php spark absensi:mark-alpha
 * 
 * Cron Schedule (Run daily at 10:05 AM):
 *   5 10 * * * cd /path/to/project && php spark absensi:mark-alpha
 */
class MarkAlphaAbsensiGuru extends BaseCommand
{
    protected $group       = 'Absensi';
    protected $name        = 'absensi:mark-alpha';
    protected $description = 'Auto-mark alpha status for teachers who haven\'t checked in by 10:00 AM';
    protected $usage       = 'absensi:mark-alpha [options]';
    protected $arguments   = [];
    protected $options     = [
        '--date' => 'Specific date to process (Y-m-d format). Default: today',
        '--dry-run' => 'Show what would be done without actually doing it',
    ];

    public function run(array $params)
    {
        CLI::write('=== Auto-Mark Alpha for Absensi Guru ===', 'yellow');
        CLI::newLine();

        // Get options
        $date = $params['date'] ?? date('Y-m-d');
        $dryRun = isset($params['dry-run']);

        if ($dryRun) {
            CLI::write('DRY RUN MODE - No changes will be made', 'yellow');
            CLI::newLine();
        }

        // Validate date format
        if (!$this->isValidDate($date)) {
            CLI::error("Invalid date format. Use Y-m-d format (e.g., 2026-02-12)");
            return;
        }

        CLI::write("Processing date: {$date}", 'cyan');
        CLI::newLine();

        // Initialize models
        $guruModel = new GuruModel();
        $absensiGuruModel = new AbsensiGuruModel();

        // Get all active teachers
        $allGuru = $guruModel->findAll();
        $totalGuru = count($allGuru);

        CLI::write("Total teachers: {$totalGuru}", 'white');

        // Get teachers who already have attendance record for this date
        $existingAbsensi = $absensiGuruModel
            ->select('guru_id')
            ->where('tanggal', $date)
            ->findAll();

        $existingGuruIds = array_column($existingAbsensi, 'guru_id');

        CLI::write("Teachers with existing records: " . count($existingGuruIds), 'white');
        CLI::newLine();

        // Find teachers without attendance record
        $absentGuruIds = [];
        foreach ($allGuru as $guru) {
            if (!in_array($guru['id'], $existingGuruIds)) {
                $absentGuruIds[] = $guru['id'];
            }
        }

        $totalAbsent = count($absentGuruIds);

        if ($totalAbsent === 0) {
            CLI::write('✅ All teachers have attendance records. No alpha marking needed.', 'green');
            return;
        }

        CLI::write("Teachers to mark as alpha: {$totalAbsent}", 'yellow');
        CLI::newLine();

        // Display list of teachers to be marked
        CLI::write('Teachers who will be marked as alpha:', 'cyan');
        foreach ($absentGuruIds as $guruId) {
            $guru = $guruModel->find($guruId);
            if ($guru) {
                CLI::write("  - [{$guru['nip']}] {$guru['nama_lengkap']}", 'white');
            }
        }
        CLI::newLine();

        if ($dryRun) {
            CLI::write('✅ DRY RUN COMPLETE - No changes made', 'green');
            return;
        }

        // Confirm before proceeding (if not in quiet mode)
        $quietMode = CLI::getOption('quiet');
        if (!$quietMode) {
            CLI::write("Proceed with marking {$totalAbsent} teachers as alpha? (y/n): ", 'yellow', false);
            $handle = fopen('php://stdin', 'r');
            $confirm = trim(fgets($handle));
            fclose($handle);
            
            if (strtolower($confirm) !== 'y') {
                CLI::write('Cancelled by user', 'yellow');
                return;
            }
        }

        CLI::newLine();
        CLI::write('Marking alpha status...', 'cyan');

        // Mark as alpha
        $successCount = 0;
        $failCount = 0;

        foreach ($absentGuruIds as $guruId) {
            $guru = $guruModel->find($guruId);
            
            $alphaData = [
                'guru_id' => $guruId,
                'tanggal' => $date,
                'status' => 'alpha',
                'keterangan_masuk' => 'Auto-marked alpha (tidak check-in sebelum 10:00)',
                'set_by_wakakur' => 0, // Auto by system
                'created_by' => 1, // System user
                'created_at' => Time::now()->toDateTimeString(),
            ];

            try {
                if ($absensiGuruModel->insert($alphaData)) {
                    $successCount++;
                    CLI::write("  ✓ {$guru['nama_lengkap']} marked as alpha", 'green');
                } else {
                    $failCount++;
                    CLI::write("  ✗ Failed to mark {$guru['nama_lengkap']}", 'red');
                    $errors = $absensiGuruModel->errors();
                    if (!empty($errors)) {
                        CLI::write("    Errors: " . json_encode($errors), 'red');
                    }
                }
            } catch (\Exception $e) {
                $failCount++;
                CLI::write("  ✗ Exception for {$guru['nama_lengkap']}: {$e->getMessage()}", 'red');
            }
        }

        CLI::newLine();
        CLI::write('=== SUMMARY ===', 'yellow');
        CLI::write("Total processed: {$totalAbsent}", 'white');
        CLI::write("Success: {$successCount}", 'green');
        CLI::write("Failed: {$failCount}", $failCount > 0 ? 'red' : 'white');
        CLI::newLine();

        if ($successCount > 0) {
            CLI::write("✅ Auto-alpha marking completed successfully!", 'green');
        } else {
            CLI::write("⚠️ No records were created", 'yellow');
        }
    }

    /**
     * Validate date format
     */
    protected function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
