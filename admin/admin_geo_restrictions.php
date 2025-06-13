<?php
// Skip geo check for admin area
define('SKIP_GEO_CHECK', true);

require_once '../config/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();

// Include geo restrictions class
require_once '../config/geo-restrictions.php';
$geoRestrictions = new GeoRestrictions();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_cache'])) {
        $geoRestrictions->clearCache();
        $message = 'Geo IP cache cleared successfully!';
    } elseif (isset($_POST['toggle_restrictions'])) {
        $enabled = $_POST['enabled'] === '1';
        $geoRestrictions->setEnabled($enabled);
        $message = $enabled ? 'Geo restrictions enabled!' : 'Geo restrictions disabled!';
    } elseif (isset($_POST['test_ip'])) {
        $testIP = trim($_POST['test_ip']);
        if (filter_var($testIP, FILTER_VALIDATE_IP)) {
            // This would require modifying the class to accept IP parameter
            $message = "Test feature coming soon for IP: " . htmlspecialchars($testIP);
        } else {
            $error = 'Please enter a valid IP address.';
        }
    }
}

// Get restriction stats
$stats = $geoRestrictions->getStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Geo Restrictions - Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            background: linear-gradient(135deg, #2f55d4, #4d6ce8);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 1.5rem;
            color: white;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav a {
            display: block;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 0;
        }
        .top-bar {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 2rem;
        }
        .content-area {
            padding: 2rem;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        .log-entry {
            font-family: monospace;
            font-size: 0.875rem;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }
        .country-flag {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <a href="index.php" class="sidebar-brand">
            <h5 class="mb-0">
                <i class="uil uil-rocket me-2"></i>
                Admin Panel
            </h5>
        </a>
        
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="uil uil-dashboard me-2"></i>Dashboard</a></li>
            <li><a href="portfolio.php"><i class="uil uil-folder me-2"></i>Portfolio</a></li>
            <li><a href="contacts.php"><i class="uil uil-envelope me-2"></i>Contact Messages</a></li>
            <li><a href="settings.php"><i class="uil uil-setting me-2"></i>Settings</a></li>
            <li><a href="geo-restrictions.php" class="active"><i class="uil uil-globe me-2"></i>Geo Restrictions</a></li>
            <li><a href="../index.php" target="_blank"><i class="uil uil-external-link-alt me-2"></i>View Website</a></li>
        </ul>
        
        <div class="mt-auto p-3" style="position: absolute; bottom: 0; width: 100%;">
            <div class="text-center">
                <small class="text-white-50">Logged in as<br>
                <strong class="text-white"><?php echo htmlspecialchars($user['full_name']); ?></strong></small>
                <div class="mt-2">
                    <a href="logout.php" class="btn btn-outline-light btn-sm">
                        <i class="uil uil-signout me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none me-2" onclick="toggleSidebar()">
                    <i class="uil uil-bars"></i>
                </button>
                <h4 class="mb-0">Geo IP Restrictions</h4>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="uil uil-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="uil uil-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Current Status -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="uil uil-globe display-6 text-primary"></i>
                            </div>
                            <h6 class="mb-1">Restriction Status</h6>
                            <span class="badge <?php echo $geoRestrictions->isEnabled() ? 'bg-success' : 'bg-warning'; ?> fs-6">
                                <?php echo $geoRestrictions->isEnabled() ? 'ACTIVE' : 'DISABLED'; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="uil uil-shield-check display-6 text-success"></i>
                            </div>
                            <h6 class="mb-1">Allowed Regions</h6>
                            <div>
                                <span class="country-flag">🇺🇸</span>United States<br>
                                <span class="country-flag">🇨🇦</span>Canada
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="uil uil-ban display-6 text-danger"></i>
                            </div>
                            <h6 class="mb-1">Total Blocks</h6>
                            <h3 class="mb-0 text-danger"><?php echo number_format($stats['total_blocks']); ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="uil uil-clock display-6 text-info"></i>
                            </div>
                            <h6 class="mb-1">Your Location</h6>
                            <small class="text-muted">
                                IP: <?php echo $_SERVER['REMOTE_ADDR']; ?><br>
                                Status: <span class="text-success">Admin Bypass</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Control Panel -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="mb-0"><i class="uil uil-setting me-2"></i>Control Panel</h6>
                        </div>
                        <div class="card-body">
                            <!-- Enable/Disable Restrictions -->
                            <form method="POST" class="mb-3">
                                <input type="hidden" name="toggle_restrictions" value="1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Geo Restrictions</h6>
                                        <small class="text-muted">Block access from outside US/Canada</small>
                                    </div>
                                    <div>
                                        <button type="submit" 
                                                name="enabled" 
                                                value="<?php echo $geoRestrictions->isEnabled() ? '0' : '1'; ?>"
                                                class="btn <?php echo $geoRestrictions->isEnabled() ? 'btn-danger' : 'btn-success'; ?> btn-sm">
                                            <?php echo $geoRestrictions->isEnabled() ? 'Disable' : 'Enable'; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <hr>
                            
                            <!-- Clear Cache -->
                            <form method="POST" class="mb-3">
                                <input type="hidden" name="clear_cache" value="1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Clear IP Cache</h6>
                                        <small class="text-muted">Reset all cached IP locations</small>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="uil uil-trash me-1"></i>Clear Cache
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <hr>
                            
                            <!-- Test IP -->
                            <form method="POST">
                                <input type="hidden" name="test_ip" value="1">
                                <h6 class="mb-2">Test IP Address</h6>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="test_ip" 
                                           placeholder="Enter IP address to test"
                                           pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="uil uil-search"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Test if an IP would be blocked</small>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Recent Blocks -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="uil uil-list-ul me-2"></i>Recent Blocked Attempts</h6>
                            <small class="text-muted">Last 20 blocks</small>
                        </div>
                        <div class="card-body">
                            <?php if (empty($stats['recent_blocks'])): ?>
                                <div class="text-center py-4">
                                    <i class="uil uil-shield-check display-4 text-success mb-3"></i>
                                    <h6 class="text-muted">No Recent Blocks</h6>
                                    <p class="text-muted mb-0">No blocked access attempts recorded yet.</p>
                                </div>
                            <?php else: ?>
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <?php foreach (array_reverse($stats['recent_blocks']) as $block): ?>
                                        <div class="log-entry">
                                            <?php echo htmlspecialchars($block); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Info -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="mb-0"><i class="uil uil-info-circle me-2"></i>Configuration Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">How It Works</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="uil uil-check-circle text-success me-2"></i>Automatically detects visitor IP addresses</li>
                                        <li><i class="uil uil-check-circle text-success me-2"></i>Uses multiple geolocation services for accuracy</li>
                                        <li><i class="uil uil-check-circle text-success me-2"></i>Caches results for 24 hours for performance</li>
                                        <li><i class="uil uil-check-circle text-success me-2"></i>Admin panel is always accessible</li>
                                        <li><i class="uil uil-check-circle text-success me-2"></i>Local/private IPs are always allowed</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary">Allowed Countries</h6>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="country-flag">🇺🇸</span>
                                        <strong>United States (US)</strong>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="country-flag">🇨🇦</span>
                                        <strong>Canada (CA)</strong>
                                    </div>
                                    
                                    <h6 class="text-primary">Bypass Conditions</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="uil uil-shield text-warning me-2"></i>Admin panel access (/admin/*)</li>
                                        <li><i class="uil uil-shield text-warning me-2"></i>Local IP addresses</li>
                                        <li><i class="uil uil-shield text-warning me-2"></i>When restrictions are disabled</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }

        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 30000);

        // Auto-hide sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('[onclick="toggleSidebar()"]');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
</body>
</html>