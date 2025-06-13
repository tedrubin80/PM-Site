<?php
require_once '../config/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$portfolioManager = new PortfolioManager();
$contactManager = new ContactManager();

// Get dashboard statistics
$stats = [
    'total_projects' => count($portfolioManager->getAllItems()),
    'active_projects' => count($portfolioManager->getAllItems('active')),
    'new_contacts' => count($contactManager->getAllSubmissions('new')),
    'total_views' => 0
];

// Calculate total views
$db = Database::getInstance();
$viewsResult = $db->fetchOne("SELECT SUM(views) as total_views FROM portfolio_items");
$stats['total_views'] = $viewsResult['total_views'] ?? 0;

// Get recent activities
$recentProjects = $portfolioManager->getAllItems('active', 5);
$recentContacts = $contactManager->getAllSubmissions(null, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Ted Rubin Project Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #2f55d4, #4d6ce8);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
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
        
        .sidebar-nav li {
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
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .content-area {
            padding: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: none;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .recent-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: none;
        }
        
        .table th {
            border: none;
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
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
            <li>
                <a href="index.php" class="active">
                    <i class="uil uil-dashboard me-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="portfolio.php">
                    <i class="uil uil-folder me-2"></i>
                    Portfolio
                </a>
            </li>
            <li>
                <a href="contacts.php">
                    <i class="uil uil-envelope me-2"></i>
                    Contact Messages
                    <?php if ($stats['new_contacts'] > 0): ?>
                        <span class="badge bg-danger ms-2"><?php echo $stats['new_contacts']; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="settings.php">
                    <i class="uil uil-setting me-2"></i>
                    Settings
                </a>
            </li>
            <li>
                <a href="geo-restrictions.php">
                    <i class="uil uil-globe me-2"></i>
                    Geo Restrictions
                </a>
            </li>
            <li>
                <a href="../index.php" target="_blank">
                    <i class="uil uil-external-link-alt me-2"></i>
                    View Website
                </a>
            </li>
        </ul>
        
        <div class="mt-auto p-3" style="position: absolute; bottom: 0; width: 100%;">
            <div class="text-center">
                <small class="text-white-50">
                    Logged in as<br>
                    <strong class="text-white"><?php echo htmlspecialchars($user['full_name']); ?></strong>
                </small>
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
                <h4 class="mb-0">Dashboard</h4>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    <i class="uil uil-clock me-1"></i>
                    Last login: <?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?>
                </span>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary">
                                <i class="uil uil-folder"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0"><?php echo $stats['total_projects']; ?></h3>
                                <small class="text-muted">Total Projects</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success">
                                <i class="uil uil-check-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0"><?php echo $stats['active_projects']; ?></h3>
                                <small class="text-muted">Active Projects</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning">
                                <i class="uil uil-envelope"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0"><?php echo $stats['new_contacts']; ?></h3>
                                <small class="text-muted">New Messages</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info">
                                <i class="uil uil-eye"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0"><?php echo number_format($stats['total_views']); ?></h3>
                                <small class="text-muted">Total Views</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <!-- Recent Projects -->
                <div class="col-lg-6 mb-4">
                    <div class="recent-card card">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Recent Portfolio Items</h6>
                            <a href="portfolio.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentProjects)): ?>
                                <p class="text-muted text-center py-3">No portfolio items yet.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Views</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentProjects as $project): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo timeAgo($project['created_at']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($project['category']); ?></td>
                                                    <td><?php echo $project['views']; ?></td>
                                                    <td>
                                                        <span class="badge-status <?php echo $project['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                            <?php echo ucfirst($project['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Contact Messages -->
                <div class="col-lg-6 mb-4">
                    <div class="recent-card card">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Recent Contact Messages</h6>
                            <a href="contacts.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentContacts)): ?>
                                <p class="text-muted text-center py-3">No contact messages yet.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Subject</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentContacts as $contact): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($contact['name']); ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($contact['email']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                                                    <td>
                                                        <small><?php echo timeAgo($contact['created_at']); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusColors = [
                                                            'new' => 'bg-warning',
                                                            'read' => 'bg-info',
                                                            'replied' => 'bg-success',
                                                            'archived' => 'bg-secondary'
                                                        ];
                                                        ?>
                                                        <span class="badge-status <?php echo $statusColors[$contact['status']] ?? 'bg-secondary'; ?>">
                                                            <?php echo ucfirst($contact['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="recent-card card">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="portfolio.php?action=add" class="btn btn-primary w-100">
                                        <i class="uil uil-plus me-2"></i>
                                        Add New Project
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="contacts.php?status=new" class="btn btn-warning w-100">
                                        <i class="uil uil-envelope me-2"></i>
                                        View New Messages
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="../index.php" target="_blank" class="btn btn-info w-100">
                                        <i class="uil uil-external-link-alt me-2"></i>
                                        Preview Website
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="settings.php" class="btn btn-secondary w-100">
                                        <i class="uil uil-setting me-2"></i>
                                        Site Settings
                                    </a>
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