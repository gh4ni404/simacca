<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Setup Command
 * 
 * One-command setup untuk SIMACCA
 * Jalankan: php spark setup
 * 
 * @package App\Commands
 * @author SIMACCA Team
 * @version 1.1.0
 */
class Setup extends BaseCommand
{
    protected $group       = 'SIMACCA';
    protected $name        = 'setup';
    protected $description = 'Setup SIMACCA dengan satu perintah (migration + seeding)';
    protected $usage       = 'setup [options]';
    protected $arguments   = [];
    protected $options     = [
        '--with-dummy' => 'Include dummy data untuk testing',
        '--force'      => 'Force setup (akan drop semua tabel existing)',
    ];

    public function run(array $params)
    {
        CLI::write('╔════════════════════════════════════════════════════╗', 'cyan');
        CLI::write('║       SIMACCA - Setup Wizard v1.1.0                ║', 'cyan');
        CLI::write('║  Sistem Monitoring Absensi dan Catatan Cara Ajar  ║', 'cyan');
        CLI::write('╚════════════════════════════════════════════════════╝', 'cyan');
        CLI::newLine();

        // Check database configuration
        if (!$this->checkDatabaseConfig()) {
            return;
        }

        // Ask for confirmation
        if (!array_key_exists('force', $params)) {
            CLI::write('Setup ini akan:', 'yellow');
            CLI::write('  1. Menjalankan semua migrations (membuat tabel database)', 'white');
            CLI::write('  2. Membuat user admin default', 'white');
            if (array_key_exists('with-dummy', $params)) {
                CLI::write('  3. Membuat data dummy untuk testing', 'white');
            }
            CLI::newLine();

            $confirm = CLI::prompt('Lanjutkan?', ['y', 'n'], 'required');
            if ($confirm !== 'y') {
                CLI::error('Setup dibatalkan.');
                return;
            }
        }

        CLI::newLine();

        // Step 1: Run migrations
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::write('STEP 1: Running Migrations...', 'yellow');
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::newLine();

        try {
            $migrate = \Config\Services::migrations();
            $migrate->setNamespace('App');
            
            if (array_key_exists('force', $params)) {
                CLI::write('Dropping existing tables...', 'yellow');
                $migrate->regress(0);
            }
            
            $migrate->latest();
            CLI::write('✓ Migrations completed successfully!', 'green');
        } catch (\Throwable $e) {
            CLI::error('✗ Migration failed: ' . $e->getMessage());
            CLI::write('Silakan cek konfigurasi database Anda di file .env', 'yellow');
            return;
        }

        CLI::newLine();

        // Step 2: Seed admin user
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::write('STEP 2: Creating Admin User...', 'yellow');
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::newLine();

        try {
            $seeder = \Config\Database::seeder();
            $seeder->call('AdminSeeder');
            CLI::write('✓ Admin user created successfully!', 'green');
            CLI::newLine();
            CLI::write('Default Admin Credentials:', 'yellow');
            CLI::write('  Username: admin', 'white');
            CLI::write('  Password: admin123', 'white');
            CLI::write('  ⚠️  PENTING: Ganti password setelah login pertama!', 'red');
        } catch (\Throwable $e) {
            CLI::error('✗ Admin seeder failed: ' . $e->getMessage());
        }

        CLI::newLine();

        // Step 3: Seed dummy data (optional)
        if (array_key_exists('with-dummy', $params)) {
            CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
            CLI::write('STEP 3: Creating Dummy Data...', 'yellow');
            CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
            CLI::newLine();

            try {
                $seeder = \Config\Database::seeder();
                $seeder->call('DummyDataSeeder');
                CLI::write('✓ Dummy data created successfully!', 'green');
            } catch (\Throwable $e) {
                CLI::error('✗ Dummy data seeder failed: ' . $e->getMessage());
            }

            CLI::newLine();
        }

        // Success summary
        CLI::write('╔════════════════════════════════════════════════════╗', 'green');
        CLI::write('║              SETUP COMPLETED! ✓                    ║', 'green');
        CLI::write('╚════════════════════════════════════════════════════╝', 'green');
        CLI::newLine();

        CLI::write('Next Steps:', 'yellow');
        CLI::write('  1. Jalankan server: php spark serve', 'white');
        CLI::write('  2. Buka browser: http://localhost:8080', 'white');
        CLI::write('  3. Login dengan credentials admin di atas', 'white');
        CLI::write('  4. Ganti password admin di menu Profile', 'white');
        CLI::newLine();

        CLI::write('Dokumentasi lengkap: README.md', 'cyan');
        CLI::newLine();
    }

    /**
     * Check database configuration
     */
    private function checkDatabaseConfig(): bool
    {
        CLI::write('Checking database configuration...', 'yellow');
        
        $db = \Config\Database::connect();
        
        try {
            // Try to connect
            $db->connect();
            CLI::write('✓ Database connection successful!', 'green');
            CLI::newLine();
            
            // Show database info
            CLI::write('Database Info:', 'cyan');
            CLI::write('  Host: ' . $db->hostname, 'white');
            CLI::write('  Database: ' . $db->database, 'white');
            CLI::write('  Driver: ' . $db->DBDriver, 'white');
            CLI::newLine();
            
            return true;
        } catch (\Throwable $e) {
            // Coba buat database otomatis jika masalahnya adalah database belum ada di MySQL Server
            $errorMsg = strtolower($e->getMessage());
            if (str_contains($errorMsg, 'unknown database') || str_contains($errorMsg, '1049')) {
                try {
                    CLI::write("Database '{$db->database}' belum ada. Mencoba membuat secara otomatis...", 'yellow');
                    $customConfig = config('Database')->default;
                    $targetDbName = $customConfig['database'];
                    $customConfig['database'] = '';
                    $tempDb = \CodeIgniter\Database\Config::connect($customConfig);
                    $tempForge = \CodeIgniter\Database\Config::forge($tempDb);
                    if ($tempForge->createDatabase($targetDbName, true)) {
                        CLI::write("✓ Database '{$targetDbName}' berhasil dibuat secara otomatis!", 'green');
                        CLI::newLine();
                        $db->reconnect();
                        return true;
                    }
                } catch (\Throwable $createEx) {
                    // Fallback to error output
                }
            }

            CLI::error('✗ Database connection failed!');
            CLI::newLine();
            CLI::write('Error: ' . $e->getMessage(), 'red');
            CLI::newLine();
            CLI::write('Silakan cek konfigurasi di file .env:', 'yellow');
            CLI::write('  database.default.hostname = ' . ($db->hostname ?: 'localhost'), 'white');
            CLI::write('  database.default.database = ' . ($db->database ?: 'simacca_db'), 'white');
            CLI::write('  database.default.username = ' . ($db->username ?: 'root'), 'white');
            CLI::write('  database.default.password = ' . ($db->password ? '******' : '(kosong)'), 'white');
            CLI::newLine();
            CLI::write('Pastikan service database berjalan dan kredensial sudah sesuai.', 'yellow');
            CLI::newLine();
            
            return false;
        }
    }
}
