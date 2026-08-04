<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class FileController extends BaseController
{
    /**
     * Serve jurnal foto from writable/uploads/jurnal
     * This controller provides secure access to uploaded files
     */
    public function jurnalFoto($filename)
    {
        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        
        // Build file path
        $filepath = WRITEPATH . 'uploads/jurnal/' . $filename;
        
        // Check if file exists
        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File nggak ketemu 🔍');
        }
        
        // Get file info
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        // Verify it's an image
        if (!str_starts_with($mimeType, 'image/')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File bukan gambar');
        }
        
        // Clear any output buffers to prevent whitespace corruption
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Set headers
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        
        // Output file directly
        readfile($filepath);
        exit;
    }

    /**
     * Serve profile photo from writable/uploads/profile
     * This controller provides secure access to profile photos
     */
    public function profilePhoto($filename)
    {
        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        
        // Build file path
        $filepath = WRITEPATH . 'uploads/profile/' . $filename;
        
        // Check if file exists
        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Foto profil tidak ditemukan 🔍');
        }
        
        // Get file info
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        // Verify it's an image
        if (!str_starts_with($mimeType, 'image/')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File bukan gambar');
        }
        
        // Clear any output buffers to prevent whitespace corruption
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Set headers
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        
        // Output file directly
        readfile($filepath);
        exit;
    }

    /**
     * Serve jurnal PKL foto from writable/uploads/jurnal_pkl
     */
    public function jurnalPklFoto($filename)
    {
        $filename = basename($filename);

        $filepath = WRITEPATH . 'uploads/jurnal_pkl/' . $filename;

        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        if (!str_starts_with($mimeType, 'image/')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File bukan gambar');
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

        readfile($filepath);
        exit;
    }

    /**
     * Serve PKL progress foto from writable/uploads/pkl_progress
     */
    public function pklProgressFoto($filename)
    {
        $filename = basename($filename);

        $filepath = WRITEPATH . 'uploads/pkl_progress/' . $filename;

        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        if (!str_starts_with($mimeType, 'image/')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File bukan gambar');
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

        readfile($filepath);
        exit;
    }

    /**
     * Serve logo sekolah from writable/uploads/logo
     */
    public function logoSekolah($filename)
    {
        $filename = basename($filename);

        $filepath = WRITEPATH . 'uploads/logo/' . $filename;

        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Logo tidak ditemukan');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        if (!str_starts_with($mimeType, 'image/')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File bukan gambar');
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

        readfile($filepath);
        exit;
    }

    /**
     * Serve absensi guru photo from writable/uploads/absensi_guru
     * This controller provides secure access to uploaded attendance photos
     * Supports nested directory structure: YYYY/MM/DD/filename.jpg
     */
    public function absensiGuruFoto($year, $month, $day, $filename)
    {
        // Sanitize inputs to prevent directory traversal
        $year = basename($year);
        $month = basename($month);
        $day = basename($day);
        $filename = basename($filename);
        
        // Build file path
        $filepath = WRITEPATH . 'uploads/absensi_guru/' . $year . '/' . $month . '/' . $day . '/' . $filename;
        
        // Check if file exists
        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Foto absensi tidak ditemukan 🔍');
        }
        
        // Get file info
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        // Verify it's an image
        if (!str_starts_with($mimeType, 'image/')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File bukan gambar');
        }
        
        // Clear any output buffers to prevent whitespace corruption
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Set headers
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        
        // Output file directly
        readfile($filepath);
        exit;
    }
}
