<?php
/**
 * Reusable Card Components
 * 
 * Usage:
 * <?= card_start('Card Title', 'fa-users') ?>
 *     Your content here
 * <?= card_end() ?>
 * 
 * Or for stat cards:
 * <?= stat_card('Total Siswa', '250', 'fa-users', 'blue') ?>
 */

if (!function_exists('card_start')) {
    /**
     * Start a card with header
     * 
     * @param string $title Card title
     * @param string $icon Font Awesome icon (without 'fa-')
     * @param array $actions Array of action buttons (HTML strings)
     * @return string
     */
    function card_start($title = '', $icon = '', $actions = [])
    {
        $iconHtml = $icon ? '<i class="fas fa-' . $icon . ' mr-2"></i>' : '';
        $actionsHtml = !empty($actions) ? '<div class="flex gap-2">' . implode('', $actions) . '</div>' : '';
        
        $html = '<div class="bg-white rounded-lg shadow-md overflow-hidden">';
        
        if ($title) {
            $html .= '<div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">';
            $html .= '<h3 class="text-lg font-semibold text-gray-800">' . $iconHtml . esc($title) . '</h3>';
            $html .= $actionsHtml;
            $html .= '</div>';
        }
        
        $html .= '<div class="p-6">';
        
        return $html;
    }
}

if (!function_exists('card_end')) {
    /**
     * End a card
     * 
     * @return string
     */
    function card_end()
    {
        return '</div></div>';
    }
}

if (!function_exists('stat_card')) {
    /**
     * Generate a stat card
     * 
     * @param string $label Stat label
     * @param string|int $value Stat value
     * @param string $icon Font Awesome icon (without 'fa-')
     * @param string $color blue|green|yellow|red|purple|indigo
     * @param string $link Optional link URL
     * @return string
     */
    function stat_card($label, $value, $icon = '', $color = 'blue', $link = '')
    {
        $colorClasses = [
            'blue'   => 'bg-blue-500',
            'green'  => 'bg-green-500',
            'yellow' => 'bg-yellow-500',
            'red'    => 'bg-red-500',
            'purple' => 'bg-purple-500',
            'indigo' => 'bg-indigo-500',
            'gray'   => 'bg-gray-500',
        ];
        
        $bgColor = $colorClasses[$color] ?? $colorClasses['blue'];
        $iconHtml = $icon ? '<i class="fas fa-' . $icon . ' text-3xl"></i>' : '';
        
        $wrapperClass = 'bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200';
        if ($link) {
            $wrapperStart = '<a href="' . $link . '" class="block ' . $wrapperClass . '">';
            $wrapperEnd = '</a>';
        } else {
            $wrapperStart = '<div class="' . $wrapperClass . '">';
            $wrapperEnd = '</div>';
        }
        
        return $wrapperStart . '
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">' . esc($label) . '</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">' . esc($value) . '</p>
                    </div>
                    <div class="flex-shrink-0 ' . $bgColor . ' bg-opacity-10 p-4 rounded-lg">
                        <div class="' . $bgColor . ' text-white rounded-lg p-3">
                            ' . $iconHtml . '
                        </div>
                    </div>
                </div>
            </div>
        ' . $wrapperEnd;
    }
}

if (!function_exists('empty_state')) {
    /**
     * Generate empty state component
     * 
     * @param string $icon Font Awesome icon (without 'fa-')
     * @param string $title Empty state title
     * @param string $description Empty state description
     * @param string $actionText Optional action button text
     * @param string $actionUrl Optional action button URL
     * @return string
     */
    function empty_state($icon = 'inbox', $title = 'Tidak ada data', $description = '', $actionText = '', $actionUrl = '')
    {
        $iconHtml = '<i class="fas fa-' . $icon . ' text-6xl text-gray-300 mb-4"></i>';
        $descHtml = $description ? '<p class="text-gray-500 mb-4">' . esc($description) . '</p>' : '';
        $actionHtml = '';
        
        if ($actionText && $actionUrl) {
            $actionHtml = '<a href="' . $actionUrl . '" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-colors">
                <i class="fas fa-plus mr-2"></i>' . esc($actionText) . '
            </a>';
        }
        
        return '<div class="text-center py-12">
            ' . $iconHtml . '
            <h3 class="text-xl font-semibold text-gray-700 mb-2">' . esc($title) . '</h3>
            ' . $descHtml . '
            ' . $actionHtml . '
        </div>';
    }
}

if (!function_exists('info_card')) {
    /**
     * Generate info card with icon and content
     * 
     * @param string $icon Font Awesome icon (without 'fa-')
     * @param string $title Card title
     * @param string $content Card content
     * @param string $color blue|green|yellow|red|purple|indigo
     * @return string
     */
    function info_card($icon, $title, $content, $color = 'blue')
    {
        $colorClasses = [
            'blue'   => 'border-blue-500 bg-blue-50',
            'green'  => 'border-green-500 bg-green-50',
            'yellow' => 'border-yellow-500 bg-yellow-50',
            'red'    => 'border-red-500 bg-red-50',
            'purple' => 'border-purple-500 bg-purple-50',
            'indigo' => 'border-indigo-500 bg-indigo-50',
        ];
        
        $colorClass = $colorClasses[$color] ?? $colorClasses['blue'];
        
        return '<div class="border-l-4 ' . $colorClass . ' p-4 rounded-r-lg">
            <div class="flex items-start">
                <i class="fas fa-' . $icon . ' text-xl mr-3 mt-0.5"></i>
                <div class="flex-1">
                    <h4 class="font-semibold mb-1">' . esc($title) . '</h4>
                    <p class="text-sm">' . $content . '</p>
                </div>
            </div>
        </div>';
    }
}
