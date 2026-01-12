<?php
/**
 * Component Helper
 * 
 * Helper untuk load reusable components
 * Load di app/Config/Autoload.php atau per controller
 */

if (!function_exists('load_components')) {
    /**
     * Load component files
     * 
     * @param array $components Component names to load
     * @return void
     */
    function load_components($components = [])
    {
        if (empty($components)) {
            // Load all components by default
            $components = ['alerts', 'buttons', 'cards', 'forms', 'modals', 'tables', 'badges'];
        }
        
        foreach ($components as $component) {
            $path = APPPATH . 'Views/components/' . $component . '.php';
            if (file_exists($path)) {
                require_once $path;
            }
        }
    }
}

// Auto-load all components when this helper is loaded
load_components();
