<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('get_simacca_temp_dir')) {
    /**
     * Mendapatkan direktori temporary SIMACCA.
     * Memprioritaskan direktori tmp yang sejajar dengan SIMACCA (../tmp atau /home/user/tmp),
     * atau direktori tmp internal SIMACCA (writable/tmp).
     *
     * @return string
     */
    function get_simacca_temp_dir(): string
    {
        // 1. Cek folder tmp sejajar dengan simacca (../tmp)
        $siblingTmp = defined('ROOTPATH')
            ? rtrim(dirname(rtrim(ROOTPATH, '/\\')), '/\\') . DIRECTORY_SEPARATOR . 'tmp'
            : dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'tmp';
        if (is_dir($siblingTmp) && is_writable($siblingTmp)) {
            $real = realpath($siblingTmp);
            return $real ? $real : str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $siblingTmp);
        }

        // 2. Cek folder tmp internal simacca (writable/tmp)
        $simaccaTmp = defined('WRITEPATH') ? WRITEPATH . 'tmp' : __DIR__ . '/../writable/tmp';
        if (is_dir($simaccaTmp) || @mkdir($simaccaTmp, 0777, true)) {
            $real = realpath($simaccaTmp);
            return $real ? $real : str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $simaccaTmp);
        }

        // 3. Fallback ke direktori temp sistem operasi
        return sys_get_temp_dir();
    }
}

if (!function_exists('get_simacca_upload_tmp_dir')) {
    /**
     * Mendapatkan nilai upload_tmp_dir dari konfigurasi PHP / .user.ini atau fallback ke direktori temporary SIMACCA.
     *
     * @return string
     */
    function get_simacca_upload_tmp_dir(): string
    {
        $iniVal = ini_get('upload_tmp_dir');
        if (!empty($iniVal)) {
            $real = realpath($iniVal);
            return $real ? $real : $iniVal;
        }

        return get_simacca_temp_dir();
    }
}
