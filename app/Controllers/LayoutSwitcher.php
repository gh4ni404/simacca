<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * Layout Switcher Controller
 * 
 * Allows users to manually switch between desktop and mobile layouts
 */
class LayoutSwitcher extends BaseController
{
    /**
     * Switch to desktop layout
     */
    public function desktop()
    {
        set_layout_preference('templates/desktop_layout');
        
        return redirect()->back()->with('success', 'Switched to Desktop Layout');
    }
    
    /**
     * Switch to mobile layout
     */
    public function mobile()
    {
        set_layout_preference('templates/mobile_layout');
        
        return redirect()->back()->with('success', 'Switched to Mobile Layout');
    }
    
    /**
     * Auto-detect layout based on device
     */
    public function auto()
    {
        clear_layout_preference();
        
        $deviceType = get_device_type();
        $message = "Auto-detection enabled. Detected device: " . ucfirst($deviceType);
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Get current device info (JSON)
     */
    public function deviceInfo()
    {
        $request = $this->request;
        $userAgent = $request->getUserAgent();
        
        $data = [
            'device_type' => get_device_type(),
            'is_mobile' => is_mobile_device(),
            'is_tablet' => is_tablet_device(),
            'current_layout' => session()->get('layout_preference') ?? 'auto',
            'user_agent' => $userAgent->getAgentString(),
            'browser' => $userAgent->getBrowser(),
            'platform' => $userAgent->getPlatform(),
            'version' => $userAgent->getVersion()
        ];
        
        return $this->response->setJSON($data);
    }
}
