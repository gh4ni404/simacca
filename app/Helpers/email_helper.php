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
                log_message('error', 'Email sending failed: ' . $email->printDebugger(['headers']));
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Email exception: ' . $e->getMessage());
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
