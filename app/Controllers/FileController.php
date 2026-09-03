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
     * Serve foto dokumentasi jurnal piket from writable/uploads/jurnal_piket
     */
    public function jurnalPiketFoto($filename)
    {
        $filename = basename($filename);
        $filepath = WRITEPATH . 'uploads/jurnal_piket/' . $filename;

        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Foto dokumentasi piket tidak ditemukan 🔍');
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
     * Serve foto dokumentasi jurnal guru wali from writable/uploads/jurnal_wali
     */
    public function jurnalWaliFoto($filename)
    {
        $filename = basename($filename);
        $filepath = WRITEPATH . 'uploads/jurnal_wali/' . $filename;

        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Foto dokumentasi tidak ditemukan 🔍');
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
            $files = [];
            foreach (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'] as $ext) {
                $matched = glob(WRITEPATH . 'uploads/logo/*.' . $ext);
                if (!empty($matched)) {
                    $files = array_merge($files, $matched);
                }
            }
            if (!empty($files)) {
                $filepath = $files[0];
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Logo tidak ditemukan');
            }
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

    /**
     * Serve public assets from public/assets/
     * Fallback for Docker / containerized environments where Nginx doesn't have public directory mounted
     */
    public function publicAssets(...$segments)
    {
        if (empty($segments)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Asset tidak ditemukan');
        }

        // Join all URL segments to form relative path
        $path = implode('/', $segments);

        // Sanitize path to prevent directory traversal
        $path = str_replace(['../', '..\\'], '', $path);
        $path = ltrim($path, '/\\');

        $filepath = FCPATH . 'assets/' . $path;
        $realAssetsDir = realpath(FCPATH . 'assets');
        $realFilePath = realpath($filepath);

        if (!$realFilePath || !$realAssetsDir || !str_starts_with($realFilePath, $realAssetsDir) || !is_file($realFilePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Asset tidak ditemukan: ' . esc($path));
        }

        // Map extension to correct MIME type
        $ext = strtolower(pathinfo($realFilePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'png'   => 'image/png',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'gif'   => 'image/gif',
            'webp'  => 'image/webp',
            'svg'   => 'image/svg+xml',
            'ico'   => 'image/x-icon',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
            'eot'   => 'application/vnd.ms-fontobject',
            'json'  => 'application/json',
            'map'   => 'application/json',
        ];

        if (isset($mimeTypes[$ext])) {
            $mimeType = $mimeTypes[$ext];
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $realFilePath) ?: 'application/octet-stream';
            finfo_close($finfo);
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($realFilePath));
        header('Cache-Control: public, max-age=31536000, immutable');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

        readfile($realFilePath);
        exit;
    }

    /**
     * Serve favicon.ico from public directory with fallback to school logo
     */
    public function favicon()
    {
        $filepath = FCPATH . 'favicon.ico';
        if (!is_file($filepath)) {
            $logo = function_exists('get_logo_sekolah') ? get_logo_sekolah() : null;
            if ($logo && is_file(WRITEPATH . 'uploads/logo/' . $logo)) {
                $filepath = WRITEPATH . 'uploads/logo/' . $logo;
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Favicon tidak ditemukan');
            }
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath) ?: 'image/x-icon';
        finfo_close($finfo);

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
     * Serve robots.txt from public directory
     */
    public function robots()
    {
        $filepath = FCPATH . 'robots.txt';
        if (!is_file($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Robots.txt tidak ditemukan');
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: text/plain; charset=UTF-8');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=86400');

        readfile($filepath);
        exit;
    }

    /**
     * Serve generic uploaded files from writable/uploads/
     * Fallback for uploads that are accessed via /uploads/ or /writable/uploads/
     */
    public function writableUploads(...$segments)
    {
        if (empty($segments)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan');
        }

        // Join all URL segments to form relative path
        $path = implode('/', $segments);

        // Sanitize path to prevent directory traversal
        $path = str_replace(['../', '..\\'], '', $path);
        $path = ltrim($path, '/\\');

        $filepath = WRITEPATH . 'uploads/' . $path;
        $realUploadsDir = realpath(WRITEPATH . 'uploads');
        $realFilePath = realpath($filepath);

        if (!$realFilePath || !$realUploadsDir || !str_starts_with($realFilePath, $realUploadsDir) || !is_file($realFilePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan: ' . esc($path));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $realFilePath) ?: 'application/octet-stream';
        finfo_close($finfo);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($realFilePath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

        readfile($realFilePath);
        exit;
    }
}
