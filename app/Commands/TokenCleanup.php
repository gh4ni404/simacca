<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PasswordResetTokenModel;

class TokenCleanup extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Maintenance';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'token:cleanup';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Clean up expired and used password reset tokens';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'token:cleanup';

    /**
     * Run the command
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Starting token cleanup...', 'yellow');
        
        $tokenModel = new PasswordResetTokenModel();
        
        // Clean up expired tokens
        $expiredCount = $tokenModel->cleanupExpired();
        CLI::write("Cleaned up {$expiredCount} expired token(s).", 'green');
        
        // Clean up used tokens older than 24 hours
        $usedCount = $tokenModel->cleanupUsed();
        CLI::write("Cleaned up {$usedCount} used token(s).", 'green');
        
        $totalCleaned = $expiredCount + $usedCount;
        
        CLI::write('', '');
        CLI::write("Total tokens cleaned: {$totalCleaned}", 'green');
        CLI::write('Token cleanup completed!', 'green');
    }
}
