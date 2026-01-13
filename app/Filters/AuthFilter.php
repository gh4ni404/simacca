<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */

    /**
     * Before filter - check if user is logged in
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            // Save intended URL for redirect after login (only for non-AJAX requests)
            if (!$request->isAJAX()) {
                session()->set('redirect_url', current_url());
            }

            // Redirect to login page
            return redirect()->to('/login')->with('error', 'Login dulu dong 🔐');
        }

        // Update last activity time to keep session alive
        // This helps prevent unexpected logouts
        $lastActivity = session()->get('last_activity');
        $currentTime = time();
        
        // Update last activity every 5 minutes to extend session
        if (!$lastActivity || ($currentTime - $lastActivity) > 300) {
            session()->set('last_activity', $currentTime);
        }

        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */

    /**
     * After filter
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
        return $response;
    }
}
