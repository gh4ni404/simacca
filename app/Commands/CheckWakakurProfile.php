<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckWakakurProfile extends BaseCommand
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
    protected $name = 'profile:check-wakakur';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Check and fix Wakakur profile completion timestamps';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'profile:check-wakakur [--fix]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--fix' => 'Automatically fix missing timestamps',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Checking Wakakur users profile completion status...', 'yellow');
        CLI::newLine();
        
        $query = $db->query("
            SELECT 
                id, 
                username, 
                role, 
                email,
                password_changed_at,
                email_changed_at,
                profile_photo_uploaded_at,
                profile_photo
            FROM users 
            WHERE role = 'wakakur'
            ORDER BY id
        ");
        
        $users = $query->getResultArray();
        
        if (empty($users)) {
            CLI::error('No Wakakur users found.');
            return;
        }
        
        CLI::write('Found ' . count($users) . ' Wakakur user(s):', 'green');
        CLI::write(str_repeat('=', 100));
        
        $needsFix = [];
        
        foreach ($users as $user) {
            CLI::write('ID: ' . $user['id']);
            CLI::write('Username: ' . $user['username']);
            CLI::write('Email: ' . ($user['email'] ?: 'NULL'));
            CLI::write('Password Changed At: ' . ($user['password_changed_at'] ?: 'NULL'));
            CLI::write('Email Changed At: ' . ($user['email_changed_at'] ?: 'NULL'));
            CLI::write('Profile Photo Uploaded At: ' . ($user['profile_photo_uploaded_at'] ?: 'NULL'));
            CLI::write('Profile Photo: ' . ($user['profile_photo'] ?: 'NULL'));
            
            // Check if needs profile completion
            $needsCompletion = empty($user['password_changed_at']) 
                || empty($user['email_changed_at']) 
                || empty($user['profile_photo_uploaded_at']);
            
            if ($needsCompletion) {
                CLI::write('Needs Profile Completion: YES', 'red');
                CLI::write('Missing:');
                if (empty($user['password_changed_at'])) CLI::write('  - Password Change Timestamp', 'red');
                if (empty($user['email_changed_at'])) CLI::write('  - Email Change Timestamp', 'red');
                if (empty($user['profile_photo_uploaded_at'])) CLI::write('  - Profile Photo Upload Timestamp', 'red');
                
                $needsFix[] = $user;
            } else {
                CLI::write('Needs Profile Completion: NO', 'green');
            }
            
            CLI::write(str_repeat('-', 100));
        }
        
        CLI::newLine();
        
        if (empty($needsFix)) {
            CLI::write('All Wakakur users have complete profiles!', 'green');
            return;
        }
        
        // Check if --fix option is provided
        $shouldFix = CLI::getOption('fix') !== null;
        
        if (!$shouldFix) {
            CLI::write('Found ' . count($needsFix) . ' user(s) with incomplete profiles.', 'yellow');
            CLI::write('Run with --fix option to automatically fix timestamps.', 'yellow');
            CLI::write('Example: php spark profile:check-wakakur --fix', 'cyan');
            return;
        }
        
        CLI::write('Fixing timestamps...', 'yellow');
        CLI::newLine();
        
        $userModel = new \App\Models\UserModel();
        
        foreach ($needsFix as $user) {
            $updates = [];
            
            // If email exists but no timestamp, set it
            if (!empty($user['email']) && empty($user['email_changed_at'])) {
                $updates['email_changed_at'] = date('Y-m-d H:i:s');
                CLI::write('Setting email_changed_at for user ' . $user['username'], 'cyan');
            }
            
            // If profile photo exists but no timestamp, set it
            if (!empty($user['profile_photo']) && empty($user['profile_photo_uploaded_at'])) {
                $updates['profile_photo_uploaded_at'] = date('Y-m-d H:i:s');
                CLI::write('Setting profile_photo_uploaded_at for user ' . $user['username'], 'cyan');
            }
            
            // Always set password_changed_at if missing (they must have changed it at some point)
            if (empty($user['password_changed_at'])) {
                $updates['password_changed_at'] = date('Y-m-d H:i:s');
                CLI::write('Setting password_changed_at for user ' . $user['username'], 'cyan');
            }
            
            if (!empty($updates)) {
                $userModel->update($user['id'], $updates);
                CLI::write('Updated user ' . $user['username'], 'green');
            }
        }
        
        CLI::newLine();
        CLI::write('All timestamps fixed!', 'green');
        CLI::write('Users can now access Wakakur features without profile completion prompt.', 'green');
    }
}
