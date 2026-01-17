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
     * @param string $footer Optional footer text with icon (e.g., '<i class="fas fa-clock mr-1"></i>5 hari ini')
     * @param string $size normal|compact (for mobile optimization)
     * @return string
     */
    function stat_card($label, $value, $icon = '', $color = 'blue', $link = '', $footer = '', $size = 'normal')
    {
        $colorClasses = [
            'blue'   => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600', 'bg-light' => 'bg-blue-100'],
            'green'  => ['bg' => 'bg-green-500', 'text' => 'text-green-600', 'bg-light' => 'bg-green-100'],
            'yellow' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-600', 'bg-light' => 'bg-yellow-100'],
            'red'    => ['bg' => 'bg-red-500', 'text' => 'text-red-600', 'bg-light' => 'bg-red-100'],
            'purple' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-600', 'bg-light' => 'bg-purple-100'],
            'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'bg-light' => 'bg-indigo-100'],
            'gray'   => ['bg' => 'bg-gray-500', 'text' => 'text-gray-600', 'bg-light' => 'bg-gray-100'],
        ];

        $colors = $colorClasses[$color] ?? $colorClasses['blue'];

        // Size-specific classes
        if ($size === 'compact') {
            // Mobile/Compact version
            $padding = 'p-3';
            $iconSize = 'text-lg';
            $iconPadding = 'p-2';
            $labelSize = 'text-xs';
            $valueSize = 'text-xl';
            $footerSize = 'text-xs';
            $shadow = 'shadow-sm';
        } else {
            // Desktop/Normal version
            $padding = 'p-4';
            $iconSize = 'text-xl';
            $iconPadding = 'p-3';
            $labelSize = 'text-sm';
            $valueSize = 'text-2xl';
            $footerSize = 'text-xs';
            $shadow = 'shadow';
        }

        $iconHtml = $icon ? '<i class="fas fa-' . $icon . ' ' . $iconSize . '"></i>' : '';
        $footerHtml = $footer ? '<div class="mt-2 ' . $footerSize . ' text-gray-500">' . $footer . '</div>' : '';

        $wrapperClass = 'bg-white rounded-lg ' . $shadow . ' hover:shadow-lg transition-shadow';
        if ($link) {
            $wrapperStart = '<a href="' . $link . '" class="block ' . $wrapperClass . '">';
            $wrapperEnd = '</a>';
        } else {
            $wrapperStart = '<div class="' . $wrapperClass . '">';
            $wrapperEnd = '</div>';
        }

        // Build the card
        $html = $wrapperStart . '
            <div class="' . $padding . '">
                <div class="flex items-center';

        // For compact, use vertical space efficiently
        if ($size === 'compact') {
            $html .= ' justify-between mb-2">
                    <div class="' . $iconPadding . ' rounded-lg ' . $colors['bg-light'] . ' ' . $colors['text'] . '">
                        ' . $iconHtml . '
                    </div>
                </div>
                <p class="' . $labelSize . ' text-gray-500">' . esc($label) . '</p>
                <p class="' . $valueSize . ' font-bold text-gray-900">' . esc($value) . '</p>';
        } else {
            // Normal desktop layout
            $html .= '">
                    <div class="' . $iconPadding . ' rounded-full ' . $colors['bg-light'] . ' ' . $colors['text'] . ' mr-4">
                        ' . $iconHtml . '
                    </div>
                    <div>
                        <p class="' . $labelSize . ' text-gray-500">' . esc($label) . '</p>
                        <p class="' . $valueSize . ' font-bold">' . esc($value) . '</p>
                    </div>
                </div>';
        }

        $html .= $footerHtml . '
            </div>
        ' . $wrapperEnd;

        return $html;
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
