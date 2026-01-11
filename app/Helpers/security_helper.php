<?php

/**
 * Security Helper Functions
 * Additional security utilities for SIMACCA
 */

if (!function_exists('validate_file_upload')) {
    /**
     * Validate file upload with comprehensive checks
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param array $allowedTypes Array of allowed MIME types
     * @param int $maxSize Maximum file size in bytes (default 5MB)
     * @return array ['valid' => bool, 'error' => string|null]
     */
    function validate_file_upload($file, array $allowedTypes, int $maxSize = 5242880): array
    {
        // Check if file is valid
        if (!$file->isValid()) {
            $error = $file->getErrorString();
            return [
                'valid' => false, 
                'error' => 'File tidak dapat diupload. ' . ($error ? 'Detail: ' . $error : 'Pastikan file yang dipilih valid.')
            ];
        }

        // Check file size
        if ($file->getSize() > $maxSize) {
            $maxSizeMB = round($maxSize / 1048576, 2);
            $actualSizeMB = round($file->getSize() / 1048576, 2);
            return [
                'valid' => false, 
                'error' => "Ukuran file terlalu besar ({$actualSizeMB}MB). Maksimal yang diizinkan adalah {$maxSizeMB}MB."
            ];
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedTypes)) {
            // Build friendly type list
            $friendlyTypes = [];
            foreach ($allowedTypes as $type) {
                if (strpos($type, 'image/') === 0) {
                    $friendlyTypes[] = strtoupper(str_replace('image/', '', $type));
                } elseif (strpos($type, 'application/') === 0) {
                    if (strpos($type, 'excel') !== false) {
                        $friendlyTypes[] = 'Excel';
                    } elseif (strpos($type, 'pdf') !== false) {
                        $friendlyTypes[] = 'PDF';
                    }
                }
            }
            $typeList = implode(', ', array_unique($friendlyTypes));
            
            return [
                'valid' => false, 
                'error' => "Tipe file tidak didukung. Hanya file {$typeList} yang diperbolehkan."
            ];
        }

        // Additional check: verify file extension matches MIME type
        $extension = $file->getExtension();
        $validExtensions = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
            'application/vnd.ms-excel' => ['xls'],
            'application/pdf' => ['pdf'],
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif']
        ];

        if (isset($validExtensions[$mimeType]) && !in_array(strtolower($extension), $validExtensions[$mimeType])) {
            return [
                'valid' => false, 
                'error' => 'File tidak sesuai. Extension file (.' . $extension . ') tidak cocok dengan tipe file sebenarnya.'
            ];
        }

        return ['valid' => true, 'error' => null];
    }
}

if (!function_exists('sanitize_filename')) {
    /**
     * Sanitize filename to prevent directory traversal and other attacks
     * 
     * @param string $filename
     * @return string
     */
    function sanitize_filename(string $filename): string
    {
        // Remove any path info
        $filename = basename($filename);
        
        // Remove special characters except dot, dash, underscore
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple dots (except the extension dot)
        $filename = preg_replace('/\.+/', '.', $filename);
        
        return $filename;
    }
}

if (!function_exists('safe_redirect')) {
    /**
     * Safe redirect - prevents open redirect vulnerabilities
     * 
     * @param string $url
     * @param string $default Default URL if validation fails
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    function safe_redirect(string $url, string $default = '/')
    {
        // Only allow relative URLs or URLs from the same domain
        $parsedUrl = parse_url($url);
        
        if (isset($parsedUrl['host'])) {
            $currentHost = parse_url(base_url(), PHP_URL_HOST);
            if ($parsedUrl['host'] !== $currentHost) {
                return redirect()->to($default);
            }
        }
        
        return redirect()->to($url);
    }
}

if (!function_exists('log_security_event')) {
    /**
     * Log security-related events
     * 
     * @param string $event Event description
     * @param array $context Additional context
     * @return void
     */
    function log_security_event(string $event, array $context = []): void
    {
        $logData = [
            'event' => $event,
            'user_id' => session()->get('user_id'),
            'username' => session()->get('username'),
            'ip_address' => service('request')->getIPAddress(),
            'user_agent' => service('request')->getUserAgent()->getAgentString(),
            'timestamp' => date('Y-m-d H:i:s'),
            'context' => $context
        ];
        
        log_message('warning', '[SECURITY] ' . $event . ' - ' . json_encode($logData));
    }
}

if (!function_exists('safe_error_message')) {
    /**
     * Generate safe error message for users (hide sensitive details)
     * 
     * @param \Exception $e
     * @param string $userMessage User-friendly message
     * @return string
     */
    function safe_error_message(\Exception $e, string $userMessage = 'Terjadi kesalahan sistem'): string
    {
        // Log the detailed error with timestamp
        log_message('error', '[ERROR] ' . $userMessage);
        log_message('error', '[EXCEPTION] ' . $e->getMessage());
        log_message('error', '[TRACE] ' . $e->getTraceAsString());
        
        // Return generic message to user (don't expose internals)
        if (ENVIRONMENT === 'development') {
            return '⚠️ ' . $userMessage . "\n\nDetail (Dev Mode):\n" . $e->getMessage();
        }
        
        return '⚠️ ' . $userMessage . '.\n\nJika masalah terus terjadi, silakan hubungi tim support dengan kode error: ERR-' . date('YmdHis');
    }
}
