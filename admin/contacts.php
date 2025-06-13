<?php
require_once '../config/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$contactManager = new ContactManager();

$status = $_GET['status'] ?? null;
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    try {
        $contactManager->updateSubmissionStatus($id, $_POST['status'], $_POST['notes'] ?? null);
        $message = 'Contact status updated successfully!';
    } catch (Exception $e) {
        $error = 'Error updating status: ' . $e->getMessage();
    }
}

// Get contact details for viewing
$contactDetails = null;
if ($action === 'view' && $id) {
    $contactDetails = $contactManager->getSubmissionById($id);
    if (!$contactDetails) {
        $error = 'Contact submission not found!';
        $action = 'list';
    } else {
        // Mark as read if it's new
        if ($contactDetails['status'] === 'new') {
            $contactManager->updateSubmissionStatus($id, 'read');
            $contactDetails['status'] = 'read';
        }
    }
}

// Get all contact submissions
$contacts = $contactManager->getAllSubmissions($status, 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Management - Admin</title>
    
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
        .table th {
            border: none;
            background: #f8f9fa;
            font-weight: 600;
        }
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .contact-message {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .status-filters {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .status-filters .btn {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
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
            <li><a href="contacts.php" class="active"><i class="uil uil-envelope me-2"></i>Contact Messages</a></li>
            <li><a href="settings.php"><i class="uil uil-setting me-2"></i>Settings</a></li>
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
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none me-2" onclick="toggleSidebar()">
                        <i class="uil uil-bars"></i>
                    </button>
                    <h4 class="mb-0">Contact Messages</h4>
                </div>
                <?php if ($action === 'view'): ?>
                    <a href="?" class="btn btn-secondary">
                        <i class="uil uil-arrow-left me-1"></i>Back to List
                    </a>
                <?php endif; ?>
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

            <?php if ($action === 'list'): ?>
                <!-- Contact Messages List -->
                <div class="card">
                    <div class="card-header bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Contact Messages (<?php echo count($contacts); ?>)</h6>
                        </div>
                        
                        <!-- Status Filters -->
                        <div class="status-filters">
                            <a href="?" class="btn <?php echo !$status ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                All Messages
                            </a>
                            <a href="?status=new" class="btn <?php echo $status === 'new' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                                New
                            </a>
                            <a href="?status=read" class="btn <?php echo $status === 'read' ? 'btn-info' : 'btn-outline-info'; ?>">
                                Read
                            </a>
                            <a href="?status=replied" class="btn <?php echo $status === 'replied' ? 'btn-success' : 'btn-outline-success'; ?>">
                                Replied
                            </a>
                            <a href="?status=archived" class="btn <?php echo $status === 'archived' ? 'btn-secondary' : 'btn-outline-secondary'; ?>">
                                Archived
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($contacts)): ?>
                            <div class="text-center py-5">
                                <i class="uil uil-envelope display-4 text-muted"></i>
                                <h5 class="mt-3 text-muted">No contact messages found</h5>
                                <p class="text-muted">Contact messages will appear here when visitors submit the contact form.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name & Email</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contacts as $contact): ?>
                                            <tr class="<?php echo $contact['status'] === 'new' ? 'table-warning' : ''; ?>">
                                                <td>
                                                    <strong><?php echo htmlspecialchars($contact['name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($contact['email']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                                                <td>
                                                    <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        <?php echo htmlspecialchars($contact['message']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small><?php echo timeAgo($contact['created_at']); ?></small>
                                                    <br>
                                                    <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($contact['created_at'])); ?></small>
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
                                                    <span class="badge badge-status <?php echo $statusColors[$contact['status']] ?? 'bg-secondary'; ?>">
                                                        <?php echo ucfirst($contact['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="?action=view&id=<?php echo $contact['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="uil uil-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif ($action === 'view' && $contactDetails): ?>
                <!-- Contact Details View -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="mb-0">Message Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-semibold">Name:</div>
                                    <div class="col-sm-9"><?php echo htmlspecialchars($contactDetails['name']); ?></div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-semibold">Email:</div>
                                    <div class="col-sm-9">
                                        <a href="mailto:<?php echo htmlspecialchars($contactDetails['email']); ?>">
                                            <?php echo htmlspecialchars($contactDetails['email']); ?>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-semibold">Subject:</div>
                                    <div class="col-sm-9"><?php echo htmlspecialchars($contactDetails['subject']); ?></div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-semibold">Date:</div>
                                    <div class="col-sm-9"><?php echo date('F j, Y g:i A', strtotime($contactDetails['created_at'])); ?></div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-semibold">IP Address:</div>
                                    <div class="col-sm-9"><?php echo htmlspecialchars($contactDetails['ip_address']); ?></div>
                                </div>
                                
                                <hr>
                                
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Message:</h6>
                                    <div class="contact-message">
                                        <?php echo nl2br(htmlspecialchars($contactDetails['message'])); ?>
                                    </div>
                                </div>
                                
                                <?php if ($contactDetails['notes']): ?>
                                    <div class="mb-3">
                                        <h6 class="fw-semibold">Notes:</h6>
                                        <div class="contact-message">
                                            <?php echo nl2br(htmlspecialchars($contactDetails['notes'])); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="mb-0">Update Status</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="update_status" value="1">
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="new" <?php echo $contactDetails['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                            <option value="read" <?php echo $contactDetails['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                            <option value="replied" <?php echo $contactDetails['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                            <option value="archived" <?php echo $contactDetails['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control" 
                                                  id="notes" 
                                                  name="notes" 
                                                  rows="4" 
                                                  placeholder="Add notes about this contact..."><?php echo htmlspecialchars($contactDetails['notes'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="uil uil-check me-1"></i>Update Status
                                    </button>
                                </form>
                                
                                <hr>
                                
                                <div class="d-grid gap-2">
                                    <a href="mailto:<?php echo htmlspecialchars($contactDetails['email']); ?>?subject=Re: <?php echo urlencode($contactDetails['subject']); ?>" 
                                       class="btn btn-success">
                                        <i class="uil uil-envelope me-1"></i>Reply via Email
                                    </a>
                                    
                                    <button class="btn btn-outline-secondary" onclick="window.print()">
                                        <i class="uil uil-print me-1"></i>Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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