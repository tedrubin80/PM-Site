<?php
require_once '../config/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$settingsManager = new SettingsManager();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST as $key => $value) {
            if ($key !== 'submit') {
                $settingsManager->setSetting($key, $value);
            }
        }
        $message = 'Settings updated successfully!';
    } catch (Exception $e) {
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

// Get all current settings
$settings = $settingsManager->getAllSettings();
$settingsArray = [];
foreach ($settings as $setting) {
    $settingsArray[$setting['setting_key']] = $setting['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings - Admin</title>
    
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
        .setting-group {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .setting-group h6 {
            color: #2f55d4;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e9ecef;
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
            <li><a href="settings.php" class="active"><i class="uil uil-setting me-2"></i>Settings</a></li>
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
                <h4 class="mb-0">Site Settings</h4>
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

            <form method="POST" action="">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- General Settings -->
                        <div class="setting-group">
                            <h6><i class="uil uil-setting me-2"></i>General Settings</h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="site_title" class="form-label">Site Title</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="site_title" 
                                           name="site_title" 
                                           value="<?php echo htmlspecialchars($settingsArray['site_title'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contact_email" class="form-label">Contact Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="contact_email" 
                                           name="contact_email" 
                                           value="<?php echo htmlspecialchars($settingsArray['contact_email'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="<?php echo htmlspecialchars($settingsArray['phone_number'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="address" 
                                           name="address" 
                                           value="<?php echo htmlspecialchars($settingsArray['address'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Hero Section Settings -->
                        <div class="setting-group">
                            <h6><i class="uil uil-presentation-line me-2"></i>Hero Section</h6>
                            
                            <div class="mb-3">
                                <label for="hero_title" class="form-label">Hero Title</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="hero_title" 
                                       name="hero_title" 
                                       value="<?php echo htmlspecialchars($settingsArray['hero_title'] ?? ''); ?>">
                                <div class="form-text">Main headline displayed on the homepage</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="hero_subtitle" class="form-label">Hero Subtitle</label>
                                <textarea class="form-control" 
                                          id="hero_subtitle" 
                                          name="hero_subtitle" 
                                          rows="3"><?php echo htmlspecialchars($settingsArray['hero_subtitle'] ?? ''); ?></textarea>
                                <div class="form-text">Supporting text below the main headline</div>
                            </div>
                        </div>

                        <!-- Social Media Settings -->
                        <div class="setting-group">
                            <h6><i class="uil uil-share me-2"></i>Social Media</h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="linkedin_url" 
                                           name="linkedin_url" 
                                           value="<?php echo htmlspecialchars($settingsArray['linkedin_url'] ?? ''); ?>"
                                           placeholder="https://linkedin.com/in/yourprofile">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="twitter_url" class="form-label">Twitter URL</label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="twitter_url" 
                                           name="twitter_url" 
                                           value="<?php echo htmlspecialchars($settingsArray['twitter_url'] ?? ''); ?>"
                                           placeholder="https://twitter.com/yourhandle">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        <i class="uil uil-check me-1"></i>Save All Settings
                                    </button>
                                    
                                    <a href="../index.php" target="_blank" class="btn btn-outline-info">
                                        <i class="uil uil-external-link-alt me-1"></i>Preview Website
                                    </a>
                                    
                                    <button type="button" class="btn btn-outline-warning" onclick="resetForm()">
                                        <i class="uil uil-redo me-1"></i>Reset Changes
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- System Info -->
                        <div class="card mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="mb-0">System Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-6"><small class="text-muted">PHP Version:</small></div>
                                    <div class="col-6"><small><?php echo phpversion(); ?></small></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6"><small class="text-muted">Database:</small></div>
                                    <div class="col-6"><small>MySQL</small></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6"><small class="text-muted">Server:</small></div>
                                    <div class="col-6"><small><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></small></div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><small class="text-muted">Last Updated:</small></div>
                                    <div class="col-6"><small><?php echo date('M j, Y'); ?></small></div>
                                </div>
                            </div>
                        </div>

                        <!-- Database Backup -->
                        <div class="card mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="mb-0">Database Management</h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-3">Manage your database and backups</p>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="downloadBackup()">
                                        <i class="uil uil-download-alt me-1"></i>Download Backup
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCache()">
                                        <i class="uil uil-trash me-1"></i>Clear Cache
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="card mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="mb-0">Security</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-6"><small class="text-muted">Login IP:</small></div>
                                    <div class="col-6"><small><?php echo $_SERVER['REMOTE_ADDR']; ?></small></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6"><small class="text-muted">Session:</small></div>
                                    <div class="col-6"><small class="text-success">Active</small></div>
                                </div>
                                <div class="d-grid">
                                    <a href="#" class="btn btn-outline-warning btn-sm" onclick="changePassword()">
                                        <i class="uil uil-key-skeleton me-1"></i>Change Password
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="passwordForm">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updatePassword()">Update Password</button>
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

        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                location.reload();
            }
        }

        function changePassword() {
            const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
            modal.show();
        }

        function updatePassword() {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                alert('Please fill in all password fields.');
                return;
            }

            if (newPassword !== confirmPassword) {
                alert('New password and confirmation do not match.');
                return;
            }

            if (newPassword.length < 6) {
                alert('New password must be at least 6 characters long.');
                return;
            }

            // Here you would typically send an AJAX request to update the password
            alert('Password change functionality would be implemented here.');
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
            modal.hide();
        }

        function downloadBackup() {
            if (confirm('Download a backup of your database?')) {
                // Implementation for database backup
                alert('Database backup functionality would be implemented here.');
            }
        }

        function clearCache() {
            if (confirm('Clear all cached data? This may temporarily slow down your site.')) {
                // Implementation for clearing cache
                alert('Cache clearing functionality would be implemented here.');
            }
        }

        // Auto-save notification
        let saveTimeout;
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    // Show unsaved changes indicator
                    const saveBtn = document.querySelector('button[name="submit"]');
                    if (!saveBtn.classList.contains('btn-warning')) {
                        saveBtn.classList.remove('btn-primary');
                        saveBtn.classList.add('btn-warning');
                        saveBtn.innerHTML = '<i class="uil uil-exclamation-triangle me-1"></i>Save Changes';
                    }
                }, 1000);
            });
        });

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