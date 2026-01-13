<?php
/**
 * Component Helper
 * 
 * Helper untuk render reusable components
 * Load di app/Config/Autoload.php atau per controller
 */

if (!function_exists('render_alerts')) {
    /**
     * Render alert messages from session
     * 
     * @return string HTML for alerts
     */
    function render_alerts()
    {
        if (!function_exists('session')) {
            return '';
        }
        
        $output = '';
        $session = session();
        
        // Success alert
        if ($session->has('success')) {
            $output .= '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-md mb-4 flex items-start" role="alert">';
            $output .= '<i class="fas fa-check-circle text-xl mr-3 mt-0.5"></i>';
            $output .= '<div class="flex-1">';
            $output .= '<p class="font-semibold">Berhasil!</p>';
            $output .= '<p class="text-sm">' . esc($session->get('success')) . '</p>';
            $output .= '</div>';
            $output .= '<button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900 ml-4">';
            $output .= '<i class="fas fa-times"></i>';
            $output .= '</button>';
            $output .= '</div>';
        }
        
        // Error alert
        if ($session->has('error')) {
            $output .= '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-md mb-4 flex items-start" role="alert">';
            $output .= '<i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>';
            $output .= '<div class="flex-1">';
            $output .= '<p class="font-semibold">Terjadi Kesalahan!</p>';
            $output .= '<p class="text-sm">' . esc($session->get('error')) . '</p>';
            $output .= '</div>';
            $output .= '<button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 ml-4">';
            $output .= '<i class="fas fa-times"></i>';
            $output .= '</button>';
            $output .= '</div>';
        }
        
        // Warning alert
        if ($session->has('warning')) {
            $output .= '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-r-lg shadow-md mb-4 flex items-start" role="alert">';
            $output .= '<i class="fas fa-exclamation-triangle text-xl mr-3 mt-0.5"></i>';
            $output .= '<div class="flex-1">';
            $output .= '<p class="font-semibold">Perhatian!</p>';
            $output .= '<p class="text-sm">' . esc($session->get('warning')) . '</p>';
            $output .= '</div>';
            $output .= '<button onclick="this.parentElement.remove()" class="text-yellow-700 hover:text-yellow-900 ml-4">';
            $output .= '<i class="fas fa-times"></i>';
            $output .= '</button>';
            $output .= '</div>';
        }
        
        // Info alert
        if ($session->has('info')) {
            $output .= '<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-r-lg shadow-md mb-4 flex items-start" role="alert">';
            $output .= '<i class="fas fa-info-circle text-xl mr-3 mt-0.5"></i>';
            $output .= '<div class="flex-1">';
            $output .= '<p class="font-semibold">Informasi</p>';
            $output .= '<p class="text-sm">' . esc($session->get('info')) . '</p>';
            $output .= '</div>';
            $output .= '<button onclick="this.parentElement.remove()" class="text-blue-700 hover:text-blue-900 ml-4">';
            $output .= '<i class="fas fa-times"></i>';
            $output .= '</button>';
            $output .= '</div>';
        }
        
        // Errors array
        if ($session->has('errors')) {
            $output .= '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-md mb-4" role="alert">';
            $output .= '<div class="flex items-start">';
            $output .= '<i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>';
            $output .= '<div class="flex-1">';
            $output .= '<p class="font-semibold mb-2">Terjadi beberapa kesalahan:</p>';
            $output .= '<ul class="list-disc list-inside text-sm space-y-1">';
            foreach ($session->get('errors') as $error) {
                $output .= '<li>' . esc($error) . '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
            $output .= '<button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900 ml-4">';
            $output .= '<i class="fas fa-times"></i>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= '</div>';
        }
        
        return $output;
    }
}

if (!function_exists('load_component')) {
    /**
     * Load a single component file
     * 
     * @param string $component Component name
     * @return void
     */
    function load_component($component)
    {
        $path = APPPATH . 'Views/components/' . $component . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
}
