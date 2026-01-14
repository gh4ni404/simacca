<?php

if (!function_exists('send_email')) {
    /**
     * Send email using CodeIgniter's Email service
     * 
     * @param string|array $to Recipient email(s)
     * @param string $subject Email subject
     * @param string $message Email message (HTML or text)
     * @param array $options Additional options (cc, bcc, attachments, etc.)
     * @return bool Success status
     */
    function send_email($to, string $subject, string $message, array $options = []): bool
    {
        $email = \Config\Services::email();
        
        try {
            // Get email config
            $config = config('Email');
            
            // Set From email and name
            $fromEmail = $options['from_email'] ?? $config->fromEmail;
            $fromName = $options['from_name'] ?? $config->fromName;
            
            // Validate From email
            if (empty($fromEmail)) {
                log_message('error', 'Email sending failed: No from email configured. Please set email.fromEmail in .env file.');
                return false;
            }
            
            // Set From header
            $email->setFrom($fromEmail, $fromName);
            
            // Set recipients
            $email->setTo($to);
            
            // Set CC if provided
            if (!empty($options['cc'])) {
                $email->setCC($options['cc']);
            }
            
            // Set BCC if provided
            if (!empty($options['bcc'])) {
                $email->setBCC($options['bcc']);
            }
            
            // Set subject and message
            $email->setSubject($subject);
            $email->setMessage($message);
            
            // Add attachments if provided
            if (!empty($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    $email->attach($attachment);
                }
            }
            
            // Send email
            $result = $email->send();
            
            // Log email sending result
            if (!$result) {
                $debugInfo = $email->printDebugger(['headers']);
                log_message('error', 'Email sending failed: ' . $debugInfo);
                
                // Add helpful error messages
                if (strpos($debugInfo, 'Username and Password not accepted') !== false) {
                    log_message('error', 'SMTP Authentication failed. For Gmail, use App Password instead of regular password.');
                    log_message('error', 'See: GMAIL_APP_PASSWORD_SETUP.md or run: php spark email:diagnostics');
                } elseif (strpos($debugInfo, 'Could not connect to SMTP host') !== false) {
                    log_message('error', 'SMTP Connection failed. Check SMTPHost and SMTPPort in .env');
                } elseif (strpos($debugInfo, 'STARTTLS') !== false) {
                    log_message('error', 'STARTTLS failed. Check SMTPCrypto setting in .env (should be "tls" for port 587)');
                }
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Email exception: ' . $e->getMessage());
            log_message('error', 'Run diagnostics: php spark email:diagnostics');
            return false;
        }
    }
}

if (!function_exists('send_password_reset_email')) {
    /**
     * Send password reset email
     * 
     * @param string $email Recipient email
     * @param string $token Reset token
     * @param string $username User's username
     * @return bool Success status
     */
    function send_password_reset_email(string $email, string $token, string $username): bool
    {
        $resetUrl = base_url("reset-password/{$token}");
        
        // Load email template
        $message = view('emails/password_reset', [
            'username' => $username,
            'resetUrl' => $resetUrl,
            'validUntil' => date('d F Y H:i', strtotime('+1 hour'))
        ]);
        
        $subject = 'Reset Password - SIMACCA';
        
        return send_email($email, $subject, $message);
    }
}

if (!function_exists('send_welcome_email')) {
    /**
     * Send welcome email to new user
     * 
     * @param string $email Recipient email
     * @param string $username User's username
     * @param string $temporaryPassword Temporary password
     * @param string $role User role
     * @return bool Success status
     */
    function send_welcome_email(string $email, string $username, string $temporaryPassword, string $role): bool
    {
        $loginUrl = base_url('login');
        
        // Load email template
        $message = view('emails/welcome', [
            'username' => $username,
            'temporaryPassword' => $temporaryPassword,
            'role' => $role,
            'loginUrl' => $loginUrl
        ]);
        
        $subject = 'Selamat Datang di SIMACCA';
        
        return send_email($email, $subject, $message);
    }
}

if (!function_exists('send_notification_email')) {
    /**
     * Send notification email
     * 
     * @param string $email Recipient email
     * @param string $subject Email subject
     * @param string $title Notification title
     * @param string $content Notification content
     * @return bool Success status
     */
    function send_notification_email(string $email, string $subject, string $title, string $content): bool
    {
        // Load email template
        $message = view('emails/notification', [
            'title' => $title,
            'content' => $content
        ]);
        
        return send_email($email, $subject, $message);
    }
}

if (!function_exists('send_email_change_notification')) {
    /**
     * Send email change notification
     * 
     * @param string $email Recipient email (old or new)
     * @param string $fullName User's full name
     * @param string $oldEmail Old email address
     * @param string $newEmail New email address
     * @param bool $isOldEmail Whether sending to old email address
     * @return bool Success status
     */
    function send_email_change_notification(string $email, string $fullName, string $oldEmail, string $newEmail, bool $isOldEmail = false): bool
    {
        // Get IP address
        $request = \Config\Services::request();
        $ipAddress = $request->getIPAddress();
        
        // Load email template
        $message = view('emails/email_changed', [
            'fullName' => $fullName,
            'oldEmail' => $oldEmail,
            'newEmail' => $newEmail,
            'changeTime' => date('d F Y H:i'),
            'ipAddress' => $ipAddress,
            'isOldEmail' => $isOldEmail
        ]);
        
        if ($isOldEmail) {
            $subject = 'SIMACCA - Email Akun Anda Telah Diubah';
        } else {
            $subject = 'SIMACCA - Konfirmasi Perubahan Email';
        }
        
        return send_email($email, $subject, $message);
    }
}

if (!function_exists('send_password_changed_by_self_notification')) {
    /**
     * Send notification when user changes their own password
     * 
     * @param string $email User's email
     * @param string $fullName User's full name
     * @param string $username User's username
     * @param string $newPassword New password (plain text)
     * @return bool Success status
     */
    function send_password_changed_by_self_notification(string $email, string $fullName, string $username, string $newPassword): bool
    {
        // Get IP address
        $request = \Config\Services::request();
        $ipAddress = $request->getIPAddress();
        
        // Load email template
        $message = view('emails/password_changed_by_self', [
            'fullName' => $fullName,
            'username' => $username,
            'newPassword' => $newPassword,
            'changeTime' => date('d F Y H:i'),
            'ipAddress' => $ipAddress
        ]);
        
        $subject = 'SIMACCA - Password Anda Berhasil Diubah';
        
        $result = send_email($email, $subject, $message);
        
        if ($result) {
            log_message('info', 'Self password change notification sent to: ' . $email);
        } else {
            log_message('error', 'Failed to send self password change notification to: ' . $email);
        }
        
        return $result;
    }
}

if (!function_exists('send_password_changed_by_admin_notification')) {
    /**
     * Send notification when admin changes user password
     * 
     * @param string $email User's email
     * @param string $fullName User's full name
     * @param string $username User's username
     * @param string $newPassword New password (plain text)
     * @return bool Success status
     */
    function send_password_changed_by_admin_notification(string $email, string $fullName, string $username, string $newPassword): bool
    {
        // Load email template
        $message = view('emails/password_changed_by_admin', [
            'fullName' => $fullName,
            'username' => $username,
            'newPassword' => $newPassword,
            'changeTime' => date('d F Y H:i')
        ]);
        
        $subject = 'SIMACCA - Password Anda Telah Diubah oleh Admin';
        
        $result = send_email($email, $subject, $message);
        
        if ($result) {
            log_message('info', 'Password change notification sent to: ' . $email);
        } else {
            log_message('error', 'Failed to send password change notification to: ' . $email);
        }
        
        return $result;
    }
}

if (!function_exists('test_email_configuration')) {
    /**
     * Test email configuration by sending a test email
     * 
     * @param string $to Test recipient email
     * @return array Result with status and message
     */
    function test_email_configuration(string $to): array
    {
        $testMessage = view('emails/test', [
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        $result = send_email($to, 'SIMACCA - Test Email Configuration', $testMessage);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Email test berhasil dikirim ke ' . $to
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Gagal mengirim email test. Periksa konfigurasi email Anda.'
            ];
        }
    }
}
