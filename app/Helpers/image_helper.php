<?php

/**
 * Image Helper Functions
 * 
 * Provides utilities for image optimization, compression, and manipulation.
 * 
 * @package App\Helpers
 * @author SIMACCA Team
 * @version 1.0.0
 */

if (!function_exists('optimize_image')) {
    /**
     * Optimize and compress an image file without losing visible quality
     * 
     * This function:
     * - Maintains aspect ratio
     * - Compresses to optimal quality (85% for JPEG, 9 for PNG)
     * - Resizes large images to reasonable dimensions
     * - Preserves original file format
     * - Removes EXIF data for privacy and size reduction
     * 
     * @param string $sourcePath Full path to source image file
     * @param string $destPath Full path to destination (can be same as source)
     * @param int $maxWidth Maximum width (default: 1920px)
     * @param int $maxHeight Maximum height (default: 1920px)
     * @param int $quality Quality for JPEG (1-100, default: 85)
     * @return bool True on success, false on failure
     */
    function optimize_image(string $sourcePath, string $destPath, int $maxWidth = 1920, int $maxHeight = 1920, int $quality = 85): bool
    {
        // Check if GD library is available
        if (!extension_loaded('gd')) {
            log_message('error', 'GD library not available for image optimization');
            return false;
        }

        // Verify source file exists
        if (!file_exists($sourcePath)) {
            log_message('error', 'Source image not found: ' . $sourcePath);
            return false;
        }

        // Get image information
        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            log_message('error', 'Invalid image file: ' . $sourcePath);
            return false;
        }

        // Capture original file size early so logging is correct even when source and dest are the same file.
        $originalFileSize = filesize($sourcePath);

        list($originalWidth, $originalHeight, $imageType) = $imageInfo;
        $mimeType = $imageInfo['mime'];

        // Create image resource from source
        $sourceImage = null;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = imagecreatefromwebp($sourcePath);
                }
                break;
            default:
                log_message('error', 'Unsupported image type: ' . $mimeType);
                return false;
        }

        if ($sourceImage === false || $sourceImage === null) {
            log_message('error', 'Failed to create image resource from: ' . $sourcePath);
            return false;
        }

        // Auto-rotate JPEG images based on EXIF Orientation metadata.
        // Many mobile cameras store the orientation in EXIF instead of rotating pixels.
        if ($imageType === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            try {
                $exif = @exif_read_data($sourcePath);
                $orientation = isset($exif['Orientation']) ? (int) $exif['Orientation'] : 1;

                if ($orientation !== 1) {
                    $rotated = null;

                    // Handle flips first (orientation 2,4,5,7)
                    if (in_array($orientation, [2, 4, 5, 7], true) && function_exists('imageflip')) {
                        switch ($orientation) {
                            case 2: // Mirror horizontal
                                imageflip($sourceImage, IMG_FLIP_HORIZONTAL);
                                break;
                            case 4: // Mirror vertical
                                imageflip($sourceImage, IMG_FLIP_VERTICAL);
                                break;
                            case 5: // Mirror horizontal and rotate 270 CW
                                imageflip($sourceImage, IMG_FLIP_HORIZONTAL);
                                $rotated = imagerotate($sourceImage, 90, 0);
                                break;
                            case 7: // Mirror horizontal and rotate 90 CW
                                imageflip($sourceImage, IMG_FLIP_HORIZONTAL);
                                $rotated = imagerotate($sourceImage, -90, 0);
                                break;
                        }
                    }

                    // Handle rotations (orientation 3,6,8) and also 5/7 when imageflip not available.
                    if ($rotated === null) {
                        switch ($orientation) {
                            case 3:
                                $rotated = imagerotate($sourceImage, 180, 0);
                                break;
                            case 6:
                                $rotated = imagerotate($sourceImage, -90, 0); // 90 CW
                                break;
                            case 8:
                                $rotated = imagerotate($sourceImage, 90, 0); // 90 CCW
                                break;
                            case 5:
                                // Best effort without flip support
                                $rotated = imagerotate($sourceImage, 90, 0);
                                break;
                            case 7:
                                $rotated = imagerotate($sourceImage, -90, 0);
                                break;
                        }
                    }

                    if ($rotated !== null && $rotated !== false) {
                        imagedestroy($sourceImage);
                        $sourceImage = $rotated;

                        // Update dimensions after rotation.
                        $originalWidth = imagesx($sourceImage);
                        $originalHeight = imagesy($sourceImage);
                    }
                }
            } catch (\Throwable $e) {
                // Never fail optimization due to EXIF parsing issues.
                log_message('debug', 'EXIF auto-rotate skipped: ' . $e->getMessage());
            }
        }

        // Calculate new dimensions while maintaining aspect ratio
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;

        if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = round($originalWidth * $ratio);
            $newHeight = round($originalHeight * $ratio);
        }

        // Create new image with calculated dimensions
        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 0, 0, 0, 127);
            imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resample image (high quality resize)
        imagecopyresampled(
            $destImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Save optimized image
        $success = false;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                // Quality: 85 is optimal balance between size and quality
                $success = imagejpeg($destImage, $destPath, $quality);
                break;
            case IMAGETYPE_PNG:
                // Compression level: 9 is maximum compression (0-9)
                $success = imagepng($destImage, $destPath, 9);
                break;
            case IMAGETYPE_GIF:
                $success = imagegif($destImage, $destPath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    $success = imagewebp($destImage, $destPath, $quality);
                }
                break;
        }

        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($destImage);

        if ($success) {
            // Set proper file permissions
            chmod($destPath, 0644);
            
            // Log compression results
            $newSize = filesize($destPath);
            $originalSize = $originalFileSize > 0 ? $originalFileSize : $newSize;
            $savings = $originalSize > 0 ? round((($originalSize - $newSize) / $originalSize) * 100, 2) : 0;
            
            log_message('info', sprintf(
                'Image optimized: %s → %s (%.2f%% smaller, %dx%d → %dx%d)',
                basename($sourcePath),
                basename($destPath),
                $savings,
                $originalWidth,
                $originalHeight,
                $newWidth,
                $newHeight
            ));
        } else {
            log_message('error', 'Failed to save optimized image: ' . $destPath);
        }

        return $success;
    }
}

if (!function_exists('optimize_profile_photo')) {
    /**
     * Optimize profile photo with specific settings
     * Profile photos are smaller and need different optimization
     * 
     * @param string $sourcePath Full path to source image
     * @param string $destPath Full path to destination
     * @return bool True on success, false on failure
     */
    function optimize_profile_photo(string $sourcePath, string $destPath): bool
    {
        // Profile photos: max 800x800, quality 85
        return optimize_image($sourcePath, $destPath, 800, 800, 85);
    }
}

if (!function_exists('optimize_jurnal_photo')) {
    /**
     * Optimize jurnal photo with specific settings
     * Jurnal photos may need higher resolution for documentation
     * 
     * @param string $sourcePath Full path to source image
     * @param string $destPath Full path to destination
     * @return bool True on success, false on failure
     */
    function optimize_jurnal_photo(string $sourcePath, string $destPath): bool
    {
        // Jurnal photos: max 1920x1920, quality 85
        return optimize_image($sourcePath, $destPath, 1920, 1920, 85);
    }
}

if (!function_exists('optimize_izin_photo')) {
    /**
     * Optimize izin (permission) photo with specific settings
     * Izin photos need to be readable but can be compressed
     * 
     * @param string $sourcePath Full path to source image
     * @param string $destPath Full path to destination
     * @return bool True on success, false on failure
     */
    function optimize_izin_photo(string $sourcePath, string $destPath): bool
    {
        // Izin photos: max 1920x1920, quality 85
        return optimize_image($sourcePath, $destPath, 1920, 1920, 85);
    }
}

if (!function_exists('get_image_dimensions')) {
    /**
     * Get image dimensions without loading the entire image
     * 
     * @param string $imagePath Full path to image file
     * @return array|false Array with 'width' and 'height' or false on failure
     */
    function get_image_dimensions(string $imagePath)
    {
        if (!file_exists($imagePath)) {
            return false;
        }

        $info = getimagesize($imagePath);
        if ($info === false) {
            return false;
        }

        return [
            'width' => $info[0],
            'height' => $info[1],
            'type' => $info[2],
            'mime' => $info['mime']
        ];
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size in human-readable format
     * 
     * @param int $bytes File size in bytes
     * @param int $precision Decimal precision
     * @return string Formatted file size (e.g., "1.5 MB")
     */
    function format_file_size(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
