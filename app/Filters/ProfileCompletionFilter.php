<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

/**
 * Profile Completion Filter
 * 
 * Checks if user has completed their profile (changed password, email, and uploaded photo)
 * Redirects to profile page if not completed
 */
class ProfileCompletionFilter implements FilterInterface
{
    /**
     * Before filter - check if user has completed profile
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $request;
        }

        $userId = session()->get('userId');
        
        // Skip check if already on profile page or logout
        $currentUrl = uri_string();
        if (strpos($currentUrl, 'profile') !== false || strpos($currentUrl, 'logout') !== false) {
            return $request;
        }

        // Check if profile needs completion
        $userModel = new UserModel();
        if ($userModel->needsProfileCompletion($userId)) {
            // Set flash message to inform user
            session()->setFlashdata('warning', 'Lengkapi profil kamu dulu ya! Ganti password, isi email, dan upload foto profil 📝✨');
            
            // Redirect to profile page
            return redirect()->to('/profile');
        }

        return $request;
    }

    /**
     * After filter
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
