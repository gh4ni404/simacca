<?php
/**
 * Reusable Form Components
 * 
 * Usage:
 * <?= form_input('username', 'Username', old('username'), ['required' => true]) ?>
 * <?= form_select('role', 'Role', $roles, old('role')) ?>
 */

if (!function_exists('form_input')) {
    /**
     * Generate text input field
     * 
     * @param string $name Input name
     * @param string $label Input label
     * @param string $value Input value
     * @param array $attrs Additional attributes (type, required, placeholder, etc)
     * @return string
     */
    function form_input($name, $label, $value = '', $attrs = [])
    {
        $validation = \Config\Services::validation();
        $type = $attrs['type'] ?? 'text';
        $required = isset($attrs['required']) && $attrs['required'];
        $placeholder = $attrs['placeholder'] ?? $label;
        
        unset($attrs['type'], $attrs['required'], $attrs['placeholder']);
        
        $attrString = '';
        foreach ($attrs as $key => $val) {
            $attrString .= sprintf(' %s="%s"', $key, esc($val));
        }
        
        $requiredMark = $required ? '<span class="text-red-500">*</span>' : '';
        $hasError = $validation->hasError($name);
        $errorClass = $hasError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500';
        
        $html = '<div class="mb-4">
            <label for="' . $name . '" class="block text-sm font-semibold text-gray-700 mb-2">
                ' . esc($label) . ' ' . $requiredMark . '
            </label>
            <input 
                type="' . $type . '" 
                id="' . $name . '" 
                name="' . $name . '" 
                value="' . esc($value) . '" 
                placeholder="' . esc($placeholder) . '"
                class="w-full px-4 py-2 border ' . $errorClass . ' rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                ' . ($required ? 'required' : '') . '
                ' . $attrString . '
            >';
        
        if ($hasError) {
            $html .= '<p class="mt-1 text-sm text-red-600">
                <i class="fas fa-exclamation-circle mr-1"></i>' . $validation->getError($name) . '
            </p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_textarea')) {
    /**
     * Generate textarea field
     * 
     * @param string $name Input name
     * @param string $label Input label
     * @param string $value Input value
     * @param array $attrs Additional attributes (rows, required, placeholder, etc)
     * @return string
     */
    function form_textarea($name, $label, $value = '', $attrs = [])
    {
        $validation = \Config\Services::validation();
        $rows = $attrs['rows'] ?? 4;
        $required = isset($attrs['required']) && $attrs['required'];
        $placeholder = $attrs['placeholder'] ?? $label;
        
        unset($attrs['rows'], $attrs['required'], $attrs['placeholder']);
        
        $attrString = '';
        foreach ($attrs as $key => $val) {
            $attrString .= sprintf(' %s="%s"', $key, esc($val));
        }
        
        $requiredMark = $required ? '<span class="text-red-500">*</span>' : '';
        $hasError = $validation->hasError($name);
        $errorClass = $hasError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500';
        
        $html = '<div class="mb-4">
            <label for="' . $name . '" class="block text-sm font-semibold text-gray-700 mb-2">
                ' . esc($label) . ' ' . $requiredMark . '
            </label>
            <textarea 
                id="' . $name . '" 
                name="' . $name . '" 
                rows="' . $rows . '"
                placeholder="' . esc($placeholder) . '"
                class="w-full px-4 py-2 border ' . $errorClass . ' rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                ' . ($required ? 'required' : '') . '
                ' . $attrString . '
            >' . esc($value) . '</textarea>';
        
        if ($hasError) {
            $html .= '<p class="mt-1 text-sm text-red-600">
                <i class="fas fa-exclamation-circle mr-1"></i>' . $validation->getError($name) . '
            </p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_select')) {
    /**
     * Generate select dropdown
     * 
     * @param string $name Input name
     * @param string $label Input label
     * @param array $options Array of options [value => label]
     * @param string $selected Selected value
     * @param array $attrs Additional attributes
     * @return string
     */
    function form_select($name, $label, $options = [], $selected = '', $attrs = [])
    {
        $validation = \Config\Services::validation();
        $required = isset($attrs['required']) && $attrs['required'];
        $placeholder = $attrs['placeholder'] ?? '-- Pilih ' . $label . ' --';
        
        unset($attrs['required'], $attrs['placeholder']);
        
        $attrString = '';
        foreach ($attrs as $key => $val) {
            $attrString .= sprintf(' %s="%s"', $key, esc($val));
        }
        
        $requiredMark = $required ? '<span class="text-red-500">*</span>' : '';
        $hasError = $validation->hasError($name);
        $errorClass = $hasError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500';
        
        $html = '<div class="mb-4">
            <label for="' . $name . '" class="block text-sm font-semibold text-gray-700 mb-2">
                ' . esc($label) . ' ' . $requiredMark . '
            </label>
            <select 
                id="' . $name . '" 
                name="' . $name . '"
                class="w-full px-4 py-2 border ' . $errorClass . ' rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                ' . ($required ? 'required' : '') . '
                ' . $attrString . '
            >
                <option value="">' . esc($placeholder) . '</option>';
        
        foreach ($options as $value => $optionLabel) {
            $isSelected = (string)$value === (string)$selected ? 'selected' : '';
            $html .= '<option value="' . esc($value) . '" ' . $isSelected . '>' . esc($optionLabel) . '</option>';
        }
        
        $html .= '</select>';
        
        if ($hasError) {
            $html .= '<p class="mt-1 text-sm text-red-600">
                <i class="fas fa-exclamation-circle mr-1"></i>' . $validation->getError($name) . '
            </p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_file')) {
    /**
     * Generate file input
     * 
     * @param string $name Input name
     * @param string $label Input label
     * @param array $attrs Additional attributes (accept, required, etc)
     * @return string
     */
    function form_file($name, $label, $attrs = [])
    {
        $validation = \Config\Services::validation();
        $required = isset($attrs['required']) && $attrs['required'];
        $accept = $attrs['accept'] ?? '*';
        $helpText = $attrs['help'] ?? '';
        
        unset($attrs['required'], $attrs['accept'], $attrs['help']);
        
        $attrString = '';
        foreach ($attrs as $key => $val) {
            $attrString .= sprintf(' %s="%s"', $key, esc($val));
        }
        
        $requiredMark = $required ? '<span class="text-red-500">*</span>' : '';
        $hasError = $validation->hasError($name);
        $errorClass = $hasError ? 'border-red-500' : 'border-gray-300';
        
        $html = '<div class="mb-4">
            <label for="' . $name . '" class="block text-sm font-semibold text-gray-700 mb-2">
                ' . esc($label) . ' ' . $requiredMark . '
            </label>
            <input 
                type="file" 
                id="' . $name . '" 
                name="' . $name . '" 
                accept="' . $accept . '"
                class="w-full px-4 py-2 border ' . $errorClass . ' rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                ' . ($required ? 'required' : '') . '
                ' . $attrString . '
            >';
        
        if ($helpText) {
            $html .= '<p class="mt-1 text-xs text-gray-500">' . $helpText . '</p>';
        }
        
        if ($hasError) {
            $html .= '<p class="mt-1 text-sm text-red-600">
                <i class="fas fa-exclamation-circle mr-1"></i>' . $validation->getError($name) . '
            </p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_checkbox')) {
    /**
     * Generate checkbox input
     * 
     * @param string $name Input name
     * @param string $label Input label
     * @param bool $checked Checked state
     * @param string $value Checkbox value
     * @return string
     */
    function form_checkbox($name, $label, $checked = false, $value = '1')
    {
        $checkedAttr = $checked ? 'checked' : '';
        
        return '<div class="mb-4">
            <label class="flex items-center cursor-pointer">
                <input 
                    type="checkbox" 
                    id="' . $name . '" 
                    name="' . $name . '" 
                    value="' . esc($value) . '"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                    ' . $checkedAttr . '
                >
                <span class="ml-2 text-sm text-gray-700">' . esc($label) . '</span>
            </label>
        </div>';
    }
}
