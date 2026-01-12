<?php
/**
 * Reusable Modal Components
 * 
 * Usage:
 * <?= modal_start('myModal', 'Modal Title') ?>
 *     Your modal content here
 * <?= modal_end() ?>
 * 
 * Or use confirm_modal for delete confirmations
 */

if (!function_exists('modal_start')) {
    /**
     * Start a modal
     * 
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param string $size sm|md|lg|xl
     * @return string
     */
    function modal_start($id, $title = '', $size = 'md')
    {
        $sizeClasses = [
            'sm' => 'max-w-sm',
            'md' => 'max-w-md',
            'lg' => 'max-w-lg',
            'xl' => 'max-w-xl',
            '2xl' => 'max-w-2xl',
        ];
        
        $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
        
        $html = '
        <!-- Modal: ' . $id . ' -->
        <div id="' . $id . '" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="' . $id . '-title" role="dialog" aria-modal="true">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal(\'' . $id . '\')"></div>
            
            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl ' . $sizeClass . ' w-full transform transition-all">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <h3 id="' . $id . '-title" class="text-xl font-semibold text-gray-900">
                            ' . esc($title) . '
                        </h3>
                        <button onclick="closeModal(\'' . $id . '\')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Modal Body -->
                    <div class="p-6">';
        
        return $html;
    }
}

if (!function_exists('modal_end')) {
    /**
     * End a modal
     * 
     * @param array $buttons Array of button HTML strings
     * @return string
     */
    function modal_end($buttons = [])
    {
        $html = '</div>';
        
        // Modal Footer (if buttons provided)
        if (!empty($buttons)) {
            $html .= '<div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200">';
            $html .= implode('', $buttons);
            $html .= '</div>';
        }
        
        $html .= '
                </div>
            </div>
        </div>';
        
        return $html;
    }
}

if (!function_exists('confirm_modal')) {
    /**
     * Generate a confirmation modal (e.g., for delete actions)
     * 
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param string $message Confirmation message
     * @param string $confirmText Confirm button text
     * @param string $cancelText Cancel button text
     * @return string
     */
    function confirm_modal($id = 'confirmModal', $title = 'Konfirmasi', $message = 'Apakah Anda yakin?', $confirmText = 'Ya, Lanjutkan', $cancelText = 'Batal')
    {
        return '
        <!-- Confirm Modal: ' . $id . ' -->
        <div id="' . $id . '" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="' . $id . '-title" role="dialog" aria-modal="true">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal(\'' . $id . '\')"></div>
            
            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full transform transition-all">
                    
                    <!-- Icon -->
                    <div class="flex items-center justify-center pt-6">
                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-100">
                            <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6 text-center">
                        <h3 id="' . $id . '-title" class="text-xl font-semibold text-gray-900 mb-2">
                            ' . esc($title) . '
                        </h3>
                        <p class="text-gray-600 mb-6">
                            ' . esc($message) . '
                        </p>
                        
                        <!-- Buttons -->
                        <div class="flex gap-3 justify-center">
                            <button onclick="closeModal(\'' . $id . '\')" 
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition-colors">
                                ' . esc($cancelText) . '
                            </button>
                            <button id="' . $id . '-confirm" 
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
                                ' . esc($confirmText) . '
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <script>
        // Handle confirm action for ' . $id . '
        document.getElementById("' . $id . '-confirm").addEventListener("click", function() {
            // You can dispatch a custom event or call a callback
            const event = new CustomEvent("confirmed", { detail: { modalId: "' . $id . '" } });
            document.dispatchEvent(event);
            closeModal("' . $id . '");
        });
        </script>';
    }
}

if (!function_exists('modal_scripts')) {
    /**
     * Generate modal helper scripts
     * Include this once in your layout
     * 
     * @return string
     */
    function modal_scripts()
    {
        return '
        <script>
        // Modal helper functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove("hidden");
                document.body.style.overflow = "hidden";
            }
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add("hidden");
                document.body.style.overflow = "auto";
            }
        }
        
        // Close modal on ESC key
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                const modals = document.querySelectorAll("[role=\'dialog\']:not(.hidden)");
                modals.forEach(modal => {
                    modal.classList.add("hidden");
                    document.body.style.overflow = "auto";
                });
            }
        });
        </script>';
    }
}
