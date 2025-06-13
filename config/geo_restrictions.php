<?php
/**
 * Geo IP Restrictions for Ted Rubin Project Management
 * Restricts access to US and Canada only
 */

class GeoRestrictions {
    private $allowedCountries = ['US', 'CA']; // United States and Canada only
    private $enabled = true; // Set to false to disable restrictions
    private $cacheFile = 'cache/geo_cache.json';
    private $cacheTimeout = 86400; // 24 hours
    
    public function __construct() {
        // Create cache directory if it doesn't exist
        $cacheDir = dirname($this->cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
    }
    
    /**
     * Check if current visitor is allowed based on location
     */
    public function checkAccess() {
        if (!$this->enabled) {
            return true; // Restrictions disabled
        }
        
        $userIP = $this->getUserIP();
        
        // Skip restrictions for admin panel and local IPs
        if ($this->isLocalIP($userIP) || $this->isAdminArea()) {
            return true;
        }
        
        $countryCode = $this->getCountryCode($userIP);
        
        if (!in_array($countryCode, $this->allowedCountries)) {
            $this->handleBlockedAccess($userIP, $countryCode);
            return false;
        }
        
        return true;
    }
    
    /**
     * Get user's real IP address
     */
    private function getUserIP() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Handle comma-separated IPs (X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Check if IP is local/private
     */
    private function isLocalIP($ip) {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
    
    /**
     * Check if current request is for admin area
     */
    private function isAdminArea() {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($requestUri, '/admin') !== false;
    }
    
    /**
     * Get country code for IP address
     */
    private function getCountryCode($ip) {
        // Check cache first
        $cached = $this->getCachedResult($ip);
        if ($cached !== null) {
            return $cached;
        }
        
        $countryCode = 'UNKNOWN';
        
        // Try multiple free geolocation services
        $services = [
            'http://ip-api.com/json/' . $ip . '?fields=countryCode',
            'https://ipapi.co/' . $ip . '/country_code/',
            'https://ipinfo.io/' . $ip . '/country'
        ];
        
        foreach ($services as $service) {
            try {
                $response = $this->makeRequest($service);
                if ($response) {
                    if (strpos($service, 'ip-api.com') !== false) {
                        $data = json_decode($response, true);
                        if (isset($data['countryCode'])) {
                            $countryCode = $data['countryCode'];
                            break;
                        }
                    } else {
                        $countryCode = trim($response);
                        if (strlen($countryCode) === 2) {
                            break;
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Geo IP service error: " . $e->getMessage());
                continue;
            }
        }
        
        // Cache the result
        $this->cacheResult($ip, $countryCode);
        
        return $countryCode;
    }
    
    /**
     * Make HTTP request to geolocation service
     */
    private function makeRequest($url) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Mozilla/5.0 (compatible; TedRubinPM/1.0)',
                'header' => 'Accept: application/json'
            ]
        ]);
        
        return @file_get_contents($url, false, $context);
    }
    
    /**
     * Get cached country code for IP
     */
    private function getCachedResult($ip) {
        if (!file_exists($this->cacheFile)) {
            return null;
        }
        
        $cache = json_decode(file_get_contents($this->cacheFile), true);
        if (!$cache) {
            return null;
        }
        
        $key = md5($ip);
        if (isset($cache[$key])) {
            $entry = $cache[$key];
            if (time() - $entry['timestamp'] < $this->cacheTimeout) {
                return $entry['country'];
            }
        }
        
        return null;
    }
    
    /**
     * Cache country code result
     */
    private function cacheResult($ip, $countryCode) {
        $cache = [];
        if (file_exists($this->cacheFile)) {
            $cache = json_decode(file_get_contents($this->cacheFile), true) ?: [];
        }
        
        $key = md5($ip);
        $cache[$key] = [
            'country' => $countryCode,
            'timestamp' => time()
        ];
        
        // Clean old entries (keep only last 1000)
        if (count($cache) > 1000) {
            $cache = array_slice($cache, -1000, 1000, true);
        }
        
        file_put_contents($this->cacheFile, json_encode($cache));
    }
    
    /**
     * Handle blocked access
     */
    private function handleBlockedAccess($ip, $countryCode) {
        // Log the blocked attempt
        $logEntry = date('Y-m-d H:i:s') . " - Blocked access from {$ip} ({$countryCode})" . PHP_EOL;
        file_put_contents('logs/geo_blocks.log', $logEntry, FILE_APPEND | LOCK_EX);
        
        // Show blocked page
        $this->showBlockedPage($countryCode);
        exit;
    }
    
    /**
     * Display blocked access page
     */
    private function showBlockedPage($countryCode) {
        http_response_code(403);
        
        $countryNames = [
            'UNKNOWN' => 'Unknown Location',
            'GB' => 'United Kingdom',
            'FR' => 'France',
            'DE' => 'Germany',
            'AU' => 'Australia',
            'JP' => 'Japan',
            'CN' => 'China',
            'IN' => 'India',
            'BR' => 'Brazil',
            'MX' => 'Mexico',
            'RU' => 'Russia'
        ];
        
        $countryName = $countryNames[$countryCode] ?? $countryCode;
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Access Restricted - Ted Rubin Project Management</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
            <style>
                body {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    font-family: 'Inter', sans-serif;
                }
                .restriction-card {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(10px);
                    border-radius: 15px;
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    max-width: 600px;
                    width: 100%;
                }
                .restriction-header {
                    background: linear-gradient(135deg, #dc3545, #c82333);
                    color: white;
                    border-radius: 15px 15px 0 0;
                    padding: 2rem;
                    text-align: center;
                }
                .restriction-body {
                    padding: 2rem;
                }
                .contact-box {
                    background: #f8f9fa;
                    border-radius: 8px;
                    padding: 1.5rem;
                    margin-top: 1.5rem;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="restriction-card mx-auto">
                            <div class="restriction-header">
                                <i class="uil uil-globe display-4 mb-3"></i>
                                <h2 class="mb-2">Service Area Restricted</h2>
                                <p class="mb-0 opacity-75">Currently serving US and Canada only</p>
                            </div>
                            
                            <div class="restriction-body">
                                <div class="text-center mb-4">
                                    <i class="uil uil-map-marker text-muted display-6"></i>
                                    <h5 class="mt-2">Detected Location: <?php echo htmlspecialchars($countryName); ?></h5>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="uil uil-info-circle me-2"></i>
                                    <strong>Notice:</strong> Ted Rubin Project Management currently provides services exclusively to clients in the United States and Canada.
                                </div>
                                
                                <p class="text-muted">
                                    I appreciate your interest in my project management consulting services. At this time, 
                                    I'm focused on serving businesses within the United States and Canada to ensure the 
                                    highest quality of service and support.
                                </p>
                                
                                <div class="contact-box">
                                    <h6 class="fw-bold mb-3">
                                        <i class="uil uil-envelope me-2 text-primary"></i>
                                        Interested in Future Expansion?
                                    </h6>
                                    <p class="mb-3">
                                        If you're interested in my services and would like to be notified when I expand 
                                        to serve your region, please feel free to reach out.
                                    </p>
                                    <a href="mailto:ted@example.com?subject=International Service Inquiry" 
                                       class="btn btn-primary">
                                        <i class="uil uil-envelope me-1"></i>Contact About Future Availability
                                    </a>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <small class="text-muted">
                                        <i class="uil uil-shield-check me-1"></i>
                                        This restriction is based on your IP location and is automatically applied
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Admin method to bypass restrictions
     */
    public function setBypass($enabled = true) {
        $_SESSION['geo_bypass'] = $enabled;
    }
    
    /**
     * Check if bypass is enabled
     */
    private function hasBypass() {
        return isset($_SESSION['geo_bypass']) && $_SESSION['geo_bypass'] === true;
    }
    
    /**
     * Get visitor statistics
     */
    public function getStats() {
        $logFile = 'logs/geo_blocks.log';
        if (!file_exists($logFile)) {
            return ['total_blocks' => 0, 'recent_blocks' => []];
        }
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES);
        $totalBlocks = count($lines);
        $recentBlocks = array_slice($lines, -20); // Last 20 blocks
        
        return [
            'total_blocks' => $totalBlocks,
            'recent_blocks' => $recentBlocks
        ];
    }
    
    /**
     * Clear cache
     */
    public function clearCache() {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }
    
    /**
     * Enable/disable restrictions
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
    
    /**
     * Check if restrictions are enabled
     */
    public function isEnabled() {
        return $this->enabled;
    }
}

// Initialize and check access
if (!defined('SKIP_GEO_CHECK')) {
    $geoRestrictions = new GeoRestrictions();
    $geoRestrictions->checkAccess();
}
?>