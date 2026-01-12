<?php
/**
 * Reusable Button Components
 * 
 * Usage:
 * <?= button('primary', 'Save', 'fa-save', ['type' => 'submit']) ?>
 * <?= button_link('secondary', 'Cancel', 'fa-times', base_url('admin/guru')) ?>
 */

if (!function_exists('button')) {
    /**
     * Generate button element
     * 
     * @param string $variant primary|secondary|success|warning|danger|info
     * @param string $text Button text
     * @param string $icon Font Awesome icon class (without 'fa-')
     * @param array $attrs Additional HTML attributes
     * @return string
     */
    function button($variant = 'primary', $text = '', $icon = '', $attrs = [])
    {
        $colors = [
            'primary'   => 'bg-blue-600 hover:bg-blue-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'success'   => 'bg-green-600 hover:bg-green-700 text-white',
            'warning'   => 'bg-yellow-500 hover:bg-yellow-600 text-white',
            'danger'    => 'bg-red-600 hover:bg-red-700 text-white',
            'info'      => 'bg-blue-500 hover:bg-blue-600 text-white',
            'outline'   => 'border-2 border-gray-300 hover:bg-gray-100 text-gray-700',
        ];
        
        $class = $colors[$variant] ?? $colors['primary'];
        $baseClass = 'inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
        
        // Merge custom class if provided
        if (isset($attrs['class'])) {
            $class .= ' ' . $attrs['class'];
            unset($attrs['class']);
        }
        
        // Build attributes string
        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= sprintf(' %s="%s"', $key, esc($value));
        }
        
        $iconHtml = $icon ? '<i class="fas fa-' . $icon . ' mr-2"></i>' : '';
        
        return sprintf(
            '<button class="%s %s"%s>%s%s</button>',
            $baseClass,
            $class,
            $attrString,
            $iconHtml,
            esc($text)
        );
    }
}

if (!function_exists('button_link')) {
    /**
     * Generate link styled as button
     * 
     * @param string $variant primary|secondary|success|warning|danger|info
     * @param string $text Link text
     * @param string $icon Font Awesome icon class (without 'fa-')
     * @param string $href URL
     * @param array $attrs Additional HTML attributes
     * @return string
     */
    function button_link($variant = 'primary', $text = '', $icon = '', $href = '#', $attrs = [])
    {
        $colors = [
            'primary'   => 'bg-blue-600 hover:bg-blue-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'success'   => 'bg-green-600 hover:bg-green-700 text-white',
            'warning'   => 'bg-yellow-500 hover:bg-yellow-600 text-white',
            'danger'    => 'bg-red-600 hover:bg-red-700 text-white',
            'info'      => 'bg-blue-500 hover:bg-blue-600 text-white',
            'outline'   => 'border-2 border-gray-300 hover:bg-gray-100 text-gray-700',
        ];
        
        $class = $colors[$variant] ?? $colors['primary'];
        $baseClass = 'inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
        
        // Merge custom class if provided
        if (isset($attrs['class'])) {
            $class .= ' ' . $attrs['class'];
            unset($attrs['class']);
        }
        
        // Build attributes string
        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= sprintf(' %s="%s"', $key, esc($value));
        }
        
        $iconHtml = $icon ? '<i class="fas fa-' . $icon . ' mr-2"></i>' : '';
        
        return sprintf(
            '<a href="%s" class="%s %s"%s>%s%s</a>',
            $href,
            $baseClass,
            $class,
            $attrString,
            $iconHtml,
            esc($text)
        );
    }
}

if (!function_exists('icon_button')) {
    /**
     * Generate icon-only button (small)
     * 
     * @param string $icon Font Awesome icon class (without 'fa-')
     * @param string $variant primary|secondary|success|warning|danger|info
     * @param array $attrs Additional HTML attributes
     * @return string
     */
    function icon_button($icon, $variant = 'primary', $attrs = [])
    {
        $colors = [
            'primary'   => 'bg-blue-600 hover:bg-blue-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'success'   => 'bg-green-600 hover:bg-green-700 text-white',
            'warning'   => 'bg-yellow-500 hover:bg-yellow-600 text-white',
            'danger'    => 'bg-red-600 hover:bg-red-700 text-white',
            'info'      => 'bg-blue-500 hover:bg-blue-600 text-white',
        ];
        
        $class = $colors[$variant] ?? $colors['primary'];
        $baseClass = 'inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
        
        if (isset($attrs['class'])) {
            $class .= ' ' . $attrs['class'];
            unset($attrs['class']);
        }
        
        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= sprintf(' %s="%s"', $key, esc($value));
        }
        
        return sprintf(
            '<button class="%s %s"%s><i class="fas fa-%s"></i></button>',
            $baseClass,
            $class,
            $attrString,
            $icon
        );
    }
}
