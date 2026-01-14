<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EmailTest extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Email';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'email:test';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'email:test [recipient_email]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'recipient_email' => 'Email address to send test email to'
    ];

    /**
     * Run the command
     *
     * @param array $params
     */
    public function run(array $params)
    {
        helper('email');
        
        // Get recipient email
        $email = $params[0] ?? CLI::prompt('Enter recipient email address');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            CLI::error('Invalid email address!');
            return;
        }
        
        CLI::write('Sending test email...', 'yellow');
        CLI::write('Recipient: ' . $email, 'cyan');
        
        // Send test email
        $result = test_email_configuration($email);
        
        if ($result['status'] === 'success') {
            CLI::write('', '');
            CLI::write('✓ ' . $result['message'], 'green');
            CLI::write('', '');
            CLI::write('Email configuration is working correctly!', 'green');
        } else {
            CLI::write('', '');
            CLI::error('✗ ' . $result['message']);
            CLI::write('', '');
            CLI::write('Please check your email configuration in .env file:', 'yellow');
            CLI::write('  - email.SMTPHost', 'cyan');
            CLI::write('  - email.SMTPUser', 'cyan');
            CLI::write('  - email.SMTPPass', 'cyan');
            CLI::write('  - email.SMTPPort', 'cyan');
            CLI::write('  - email.SMTPCrypto', 'cyan');
        }
    }
}
