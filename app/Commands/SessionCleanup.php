<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Session Cleanup Command
 * 
 * Run this command regularly via cron job to clean up old session files
 * Usage: php spark session:cleanup
 * Cron: 0 2 * * * cd /path/to/app && php spark session:cleanup
 */
class SessionCleanup extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'session:cleanup';
    protected $description = 'Clean up old session files (older than session expiration time)';

    public function run(array $params)
    {
        $sessionPath = WRITEPATH . 'session';
        $sessionConfig = config('Session');
        $expiration = $sessionConfig->expiration; // 8 hours = 28800 seconds

        if (!is_dir($sessionPath)) {
            CLI::error('Session directory not found: ' . $sessionPath);
            return;
        }

        CLI::write('Starting session cleanup...', 'yellow');
        CLI::write('Session path: ' . $sessionPath);
        CLI::write('Expiration time: ' . $expiration . ' seconds (' . ($expiration / 3600) . ' hours)');

        $deleted = 0;
        $kept = 0;
        $totalSize = 0;

        // Get all session files
        $files = scandir($sessionPath);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'index.html' || $file === '.htaccess') {
                continue;
            }

            $filepath = $sessionPath . DIRECTORY_SEPARATOR . $file;
            
            if (!is_file($filepath)) {
                continue;
            }

            $fileTime = filemtime($filepath);
            $currentTime = time();
            $fileAge = $currentTime - $fileTime;

            // Delete if older than expiration time
            if ($fileAge > $expiration) {
                $filesize = filesize($filepath);
                if (unlink($filepath)) {
                    $deleted++;
                    $totalSize += $filesize;
                    CLI::write("Deleted: {$file} (age: " . round($fileAge / 3600, 1) . " hours)", 'green');
                } else {
                    CLI::error("Failed to delete: {$file}");
                }
            } else {
                $kept++;
            }
        }

        CLI::newLine();
        CLI::write('═══════════════════════════════════════', 'cyan');
        CLI::write('Session Cleanup Summary:', 'yellow');
        CLI::write('═══════════════════════════════════════', 'cyan');
        CLI::write("Files deleted: {$deleted}", 'green');
        CLI::write("Files kept: {$kept}", 'blue');
        CLI::write("Space freed: " . $this->formatBytes($totalSize), 'green');
        CLI::write('═══════════════════════════════════════', 'cyan');
        CLI::newLine();

        if ($deleted > 0) {
            CLI::write('✓ Session cleanup completed successfully!', 'green');
        } else {
            CLI::write('No old session files to clean up.', 'blue');
        }

        // Cleanup expired remember tokens
        CLI::newLine();
        CLI::write('Cleaning up expired remember tokens...', 'yellow');
        $rememberModel = new \App\Models\RememberTokenModel();
        $deletedTokens = $rememberModel->cleanupExpired();
        CLI::write("Expired remember tokens deleted: {$deletedTokens}", 'green');
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
