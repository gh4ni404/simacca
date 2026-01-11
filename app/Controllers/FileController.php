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
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan');
        }
        
        // Get file info
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        // Verify it's an image
        if (!str_starts_with($mimeType, 'image/')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File bukan gambar');
        }
        
        // Set headers
        $this->response->setHeader('Content-Type', $mimeType);
        $this->response->setHeader('Content-Length', filesize($filepath));
        $this->response->setHeader('Cache-Control', 'public, max-age=31536000'); // Cache for 1 year
        $this->response->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        
        // Output file
        $this->response->setBody(file_get_contents($filepath));
        
        return $this->response;
    }
}
