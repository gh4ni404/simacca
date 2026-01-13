<?php
/**
 * Component Helper
 * 
 * Helper untuk render reusable components
 * Load di app/Config/Autoload.php atau per controller
 */

if (!function_exists('render_alerts')) {
    /**
     * Render alert messages from session with priority
     * Only shows ONE alert at a time (the highest priority)
     * Priority order: errors > error > warning > success_custom > success > info
     * 
     * @param bool $showAll If true, shows all alerts. Default false (only highest priority)
     * @return string HTML for alert(s)
     */
    function render_alerts($showAll = false)
    {
        if (!function_exists('session')) {
            return '';
        }
        
        // Prevent double rendering using a static flag
        static $rendered = false;
        if ($rendered) {
            return ''; // Already rendered, return empty
        }
        
        $session = session();
        
        // Priority order for single alert display
        $priorities = [
            'errors',
            'error', 
            'warning',
            'success_custom',
            'success',
            'info'
        ];
        
        // If showAll is false, find the first available message and show only that
        if (!$showAll) {
            foreach ($priorities as $type) {
                // Use getFlashdata() to properly read flashdata
                $data = $session->getFlashdata($type);
                if ($data !== null) {
                    $rendered = true; // Mark as rendered
                    return render_single_alert($type, $data);
                }
            }
            return '';
        }
        
        // If showAll is true, render all available alerts
        $output = '';
        foreach ($priorities as $type) {
            // Use getFlashdata() to properly read flashdata
            $data = $session->getFlashdata($type);
            if ($data !== null) {
                $output .= render_single_alert($type, $data);
            }
        }
        
        if ($output !== '') {
            $rendered = true; // Mark as rendered
        }
        
        return $output;
    }
}

if (!function_exists('render_single_alert')) {
    /**
     * Render a single alert based on type
     * 
     * @param string $type Alert type (success, error, warning, info, errors, success_custom)
     * @param mixed $data Alert data (string or array)
     * @return string HTML for the alert
     */
    function render_single_alert($type, $data)
    {
        $output = '';
        
        switch ($type) {
            case 'success_custom':
                // Custom success with title and message
                $title = is_array($data) ? ($data['title'] ?? 'Berhasil!') : 'Berhasil!';
                $message = is_array($data) ? ($data['message'] ?? '') : $data;
                
                $output .= '<div class="bg-gradient-to-r from-green-50 to-blue-50 border-l-4 border-green-500 p-6 rounded-lg shadow-lg mb-6 animate-fade-in" role="alert">';
                $output .= '<div class="flex items-start">';
                $output .= '<div class="flex-shrink-0">';
                $output .= '<div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center">';
                $output .= '<i class="fas fa-check-circle text-white text-2xl"></i>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="ml-4 flex-1">';
                $output .= '<h3 class="text-xl font-bold text-gray-800 mb-2">' . $title . '</h3>';
                $output .= '<p class="text-gray-700">' . $message . '</p>';
                $output .= '</div>';
                $output .= '<button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700 ml-4">';
                $output .= '<i class="fas fa-times text-xl"></i>';
                $output .= '</button>';
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'success':
                $output .= '<div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm mb-6 animate-fade-in" role="alert">';
                $output .= '<div class="flex items-center">';
                $output .= '<div class="flex-shrink-0">';
                $output .= '<i class="fas fa-check-circle text-green-500 text-xl"></i>';
                $output .= '</div>';
                $output .= '<div class="ml-3 flex-1">';
                $output .= '<p class="text-green-800 font-medium">' . esc($data) . '</p>';
                $output .= '</div>';
                $output .= '<button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 ml-4">';
                $output .= '<i class="fas fa-times"></i>';
                $output .= '</button>';
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'error':
                $output .= '<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm mb-6 animate-fade-in" role="alert">';
                $output .= '<div class="flex items-center">';
                $output .= '<div class="flex-shrink-0">';
                $output .= '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>';
                $output .= '</div>';
                $output .= '<div class="ml-3 flex-1">';
                $output .= '<p class="text-red-800 font-medium">' . esc($data) . '</p>';
                $output .= '</div>';
                $output .= '<button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-4">';
                $output .= '<i class="fas fa-times"></i>';
                $output .= '</button>';
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'warning':
                $output .= '<div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm mb-6 animate-fade-in" role="alert">';
                $output .= '<div class="flex items-center">';
                $output .= '<div class="flex-shrink-0">';
                $output .= '<i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>';
                $output .= '</div>';
                $output .= '<div class="ml-3 flex-1">';
                $output .= '<p class="text-yellow-800 font-medium">' . esc($data) . '</p>';
                $output .= '</div>';
                $output .= '<button onclick="this.parentElement.parentElement.remove()" class="text-yellow-600 hover:text-yellow-800 ml-4">';
                $output .= '<i class="fas fa-times"></i>';
                $output .= '</button>';
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'info':
                $output .= '<div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm mb-6 animate-fade-in" role="alert">';
                $output .= '<div class="flex items-center">';
                $output .= '<div class="flex-shrink-0">';
                $output .= '<i class="fas fa-info-circle text-blue-500 text-xl"></i>';
                $output .= '</div>';
                $output .= '<div class="ml-3 flex-1">';
                $output .= '<p class="text-blue-800 font-medium">' . esc($data) . '</p>';
                $output .= '</div>';
                $output .= '<button onclick="this.parentElement.parentElement.remove()" class="text-blue-600 hover:text-blue-800 ml-4">';
                $output .= '<i class="fas fa-times"></i>';
                $output .= '</button>';
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'errors':
                $output .= '<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm mb-6 animate-fade-in" role="alert">';
                $output .= '<div class="flex items-start">';
                $output .= '<div class="flex-shrink-0">';
                $output .= '<i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>';
                $output .= '</div>';
                $output .= '<div class="ml-3 flex-1">';
                $output .= '<p class="text-red-800 font-semibold mb-2">Terjadi beberapa kesalahan:</p>';
                $output .= '<ul class="list-disc list-inside text-sm text-red-700 space-y-1">';
                foreach ($data as $error) {
                    $output .= '<li>' . esc($error) . '</li>';
                }
                $output .= '</ul>';
                $output .= '</div>';
                $output .= '<button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-4">';
                $output .= '<i class="fas fa-times"></i>';
                $output .= '</button>';
                $output .= '</div>';
                $output .= '</div>';
                break;
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

if (!function_exists('modal_scripts')) {
    /**
     * Render modal JavaScript for handling modal interactions
     * 
     * @return string JavaScript code for modals
     */
    function modal_scripts()
    {
        return <<<'HTML'
<script>
    // Modal helper functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    // Auto-attach close handlers to modal close buttons
    document.addEventListener('DOMContentLoaded', function() {
        // Close modal when clicking close button
        document.querySelectorAll('[data-modal-close]').forEach(button => {
            button.addEventListener('click', function() {
                const modalId = this.getAttribute('data-modal-close');
                closeModal(modalId);
            });
        });

        // Close modal when clicking overlay
        document.querySelectorAll('[data-modal-overlay]').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    const modalId = this.getAttribute('data-modal-overlay');
                    closeModal(modalId);
                }
            });
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });
    });
</script>
HTML;
    }
}
