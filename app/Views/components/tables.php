<?php
/**
 * Reusable Table Components
 * 
 * Usage:
 * <?= table_header(['No', 'Nama', 'Email', 'Aksi']) ?>
 * <?= table_row(['1', 'John Doe', 'john@example.com', '<button>Edit</button>']) ?>
 */

if (!function_exists('table_start')) {
    /**
     * Start a responsive table
     * 
     * @return string
     */
    function table_start()
    {
        return '<div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">';
    }
}

if (!function_exists('table_end')) {
    /**
     * End a table
     * 
     * @return string
     */
    function table_end()
    {
        return '</table></div>';
    }
}

if (!function_exists('table_header')) {
    /**
     * Generate table header
     * 
     * @param array $columns Column names
     * @return string
     */
    function table_header($columns = [])
    {
        $html = '<thead class="bg-gray-50"><tr>';
        
        foreach ($columns as $column) {
            $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">';
            $html .= esc($column);
            $html .= '</th>';
        }
        
        $html .= '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
        
        return $html;
    }
}

if (!function_exists('badge')) {
    /**
     * Generate status badge
     * 
     * @param string $text Badge text
     * @param string $color green|yellow|red|blue|gray
     * @return string
     */
    function badge($text, $color = 'gray')
    {
        $colors = [
            'green'  => 'bg-green-100 text-green-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'red'    => 'bg-red-100 text-red-800',
            'blue'   => 'bg-blue-100 text-blue-800',
            'gray'   => 'bg-gray-100 text-gray-800',
        ];
        
        $colorClass = $colors[$color] ?? $colors['gray'];
        
        return '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ' . $colorClass . '">' 
            . esc($text) . '</span>';
    }
}
