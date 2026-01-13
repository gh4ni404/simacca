<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use DateTimeInterface;

class Cookie extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Cookie Prefix
     * --------------------------------------------------------------------------
     *
     * Set a cookie name prefix if you need to avoid collisions.
     */
    public string $prefix = '';

    /**
     * --------------------------------------------------------------------------
     * Cookie Expires Timestamp
     * --------------------------------------------------------------------------
     *
     * Default expires timestamp for cookies. Setting this to `0` will mean the
     * cookie will not have the `Expires` attribute and will behave as a session
     * cookie.
     *
     * @var DateTimeInterface|int|string
     */
    public $expires = 0;

    /**
     * --------------------------------------------------------------------------
     * Cookie Path
     * --------------------------------------------------------------------------
     *
     * Typically will be a forward slash.
     */
    public string $path = '/';

    /**
     * --------------------------------------------------------------------------
     * Cookie Domain
     * --------------------------------------------------------------------------
     *
     * Set to `.your-domain.com` for site-wide cookies.
     * 
     * AUTO-DETECTION: Automatically sets domain based on environment
     * - Production: Uses actual domain from HTTP_HOST
     * - Development: Empty (works for localhost)
     */
    public string $domain = '';

    /**
     * --------------------------------------------------------------------------
     * Cookie Secure
     * --------------------------------------------------------------------------
     *
     * Cookie will only be set if a secure HTTPS connection exists.
     * 
     * AUTO-DETECTION: Automatically detects HTTPS connection
     */
    public bool $secure = false;
    
    public function __construct()
    {
        parent::__construct();
        
        // Auto-detect HTTPS for secure cookies
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $this->secure = true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            // Behind reverse proxy/load balancer
            $this->secure = true;
        }
        
        // Auto-detect domain for production environment
        if (ENVIRONMENT === 'production' && isset($_SERVER['HTTP_HOST'])) {
            // Extract domain from HTTP_HOST
            $host = $_SERVER['HTTP_HOST'];
            
            // Remove port if present
            $host = explode(':', $host)[0];
            
            // For subdomain cookie sharing, use root domain
            // e.g., simacca.smkn8bone.sch.id -> .smkn8bone.sch.id
            $parts = explode('.', $host);
            if (count($parts) > 2) {
                // Has subdomain, set to root domain for sharing
                $this->domain = '.' . implode('.', array_slice($parts, -2));
            } else {
                // No subdomain, use as is
                $this->domain = '';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------
     * Cookie HTTPOnly
     * --------------------------------------------------------------------------
     *
     * Cookie will only be accessible via HTTP(S) (no JavaScript).
     */
    public bool $httponly = true;

    /**
     * --------------------------------------------------------------------------
     * Cookie SameSite
     * --------------------------------------------------------------------------
     *
     * Configure cookie SameSite setting. Allowed values are:
     * - None
     * - Lax
     * - Strict
     * - ''
     *
     * Alternatively, you can use the constant names:
     * - `Cookie::SAMESITE_NONE`
     * - `Cookie::SAMESITE_LAX`
     * - `Cookie::SAMESITE_STRICT`
     *
     * Defaults to `Lax` for compatibility with modern browsers. Setting `''`
     * (empty string) means default SameSite attribute set by browsers (`Lax`)
     * will be set on cookies. If set to `None`, `$secure` must also be set.
     *
     * @var ''|'Lax'|'None'|'Strict'
     */
    public string $samesite = 'Lax';

    /**
     * --------------------------------------------------------------------------
     * Cookie Raw
     * --------------------------------------------------------------------------
     *
     * This flag allows setting a "raw" cookie, i.e., its name and value are
     * not URL encoded using `rawurlencode()`.
     *
     * If this is set to `true`, cookie names should be compliant of RFC 2616's
     * list of allowed characters.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#attributes
     * @see https://tools.ietf.org/html/rfc2616#section-2.2
     */
    public bool $raw = false;
}
