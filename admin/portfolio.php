<?php
require_once '../config/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$portfolioManager = new PortfolioManager();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add') {
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'category' => $_POST['category'],
                'content_type' => $_POST['content_type'],
                'content_url' => $_POST['content_url'],
                'thumbnail_url' => $_POST['thumbnail_url'] ?: null,
                'metrics' => $_POST['metrics'] ?: null,
                'client_name' => $_POST['client_name'] ?: null,
                'project_date' => $_POST['project_date'] ?: null,
                'tags' => json_encode(array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')))),
                'status' => $_POST['status'],
                'sort_order' => (int)($_POST['sort_order'] ?: 0),
                'created_by' => $user['id']
            ];
            
            $portfolioManager->createItem($data);
            $message = 'Portfolio item created successfully!';
            $action = 'list';
            
        } elseif ($action === 'edit' && $id) {
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'category' => $_POST['category'],
                'content_type' => $_POST['content_type'],
                'content_url' => $_POST['content_url'],
                'thumbnail_url' => $_POST['thumbnail_url'] ?: null,
                'metrics' => $_POST['metrics'] ?: null,
                'client_name' => $_POST['client_name'] ?: null,
                'project_date' => $_POST['project_date'] ?: null,
                'tags' => json_encode(array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')))),
                'status' => $_POST['status'],
                'sort_order' => (int)($_POST['sort_order'] ?: 0)
            ];
            
            $portfolioManager->updateItem($id, $data);
            $message = 'Portfolio item updated successfully!';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Handle delete action
if ($action === 'delete' && $id) {
    try {
        $portfolioManager->deleteItem($id);
        $message = 'Portfolio item deleted successfully!';
        $action = 'list';
    } catch (Exception $e) {
        $error = 'Error deleting item: ' . $e->getMessage();
    }
}

// Get data for editing
$editItem = null;
if ($action === 'edit' && $id) {
    $editItem = $portfolioManager->getItemById($id);
    if (!$editItem) {
        $error = 'Portfolio item not found!';
        $action = 'list';
    }
}

// Get all portfolio items for listing
$portfolioItems = $portfolioManager->getAllItems();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portfolio Management - Admin</title>
    
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
        .portfolio-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .content-type-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
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
            <li><a href="portfolio.php" class="active"><i class="uil uil-folder me-2"></i>Portfolio</a></li>
            <li><a href="contacts.php"><i class="uil uil-envelope me-2"></i>Contact Messages</a></li>
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
                    <h4 class="mb-0">Portfolio Management</h4>
                </div>
                <?php if ($action === 'list'): ?>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="uil uil-plus me-1"></i>Add New Project
                    </a>
                <?php else: ?>
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
                <!-- Portfolio Items List -->
                <div class="card">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Portfolio Items (<?php echo count($portfolioItems); ?>)</h6>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" onchange="filterPortfolio(this.value)">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Preview</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Views</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($portfolioItems as $item): ?>
                                        <tr data-status="<?php echo $item['status']; ?>">
                                            <td>
                                                <?php if ($item['thumbnail_url']): ?>
                                                    <img src="<?php echo htmlspecialchars($item['thumbnail_url']); ?>" 
                                                         class="portfolio-thumbnail" alt="Thumbnail">
                                                <?php else: ?>
                                                    <div class="portfolio-thumbnail bg-secondary d-flex align-items-center justify-content-center">
                                                        <i class="uil uil-image text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                                <?php if ($item['client_name']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($item['client_name']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['category']); ?></td>
                                            <td>
                                                <?php
                                                $typeColors = [
                                                    'image' => 'bg-primary',
                                                    'video' => 'bg-danger',
                                                    'link' => 'bg-success'
                                                ];
                                                ?>
                                                <span class="badge content-type-badge <?php echo $typeColors[$item['content_type']] ?? 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($item['content_type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo number_format($item['views']); ?></td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'active' => 'bg-success',
                                                    'inactive' => 'bg-warning',
                                                    'draft' => 'bg-secondary'
                                                ];
                                                ?>
                                                <span class="badge badge-status <?php echo $statusColors[$item['status']] ?? 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($item['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo date('M j, Y', strtotime($item['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?action=edit&id=<?php echo $item['id']; ?>" 
                                                       class="btn btn-outline-primary">
                                                        <i class="uil uil-edit"></i>
                                                    </a>
                                                    <a href="?action=delete&id=<?php echo $item['id']; ?>" 
                                                       class="btn btn-outline-danger"
                                                       onclick="return confirm('Are you sure you want to delete this item?')">
                                                        <i class="uil uil-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="mb-0"><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Portfolio Item</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Project Title *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="title" 
                                               name="title" 
                                               value="<?php echo htmlspecialchars($editItem['title'] ?? ''); ?>" 
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description *</label>
                                        <textarea class="form-control" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="4" 
                                                  required><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="category" class="form-label">Category *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="category" 
                                                   name="category" 
                                                   value="<?php echo htmlspecialchars($editItem['category'] ?? ''); ?>" 
                                                   required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="content_type" class="form-label">Content Type *</label>
                                            <select class="form-select" id="content_type" name="content_type" required>
                                                <option value="">Select Type</option>
                                                <option value="image" <?php echo ($editItem['content_type'] ?? '') === 'image' ? 'selected' : ''; ?>>Image</option>
                                                <option value="video" <?php echo ($editItem['content_type'] ?? '') === 'video' ? 'selected' : ''; ?>>Video</option>
                                                <option value="link" <?php echo ($editItem['content_type'] ?? '') === 'link' ? 'selected' : ''; ?>>External Link</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="content_url" class="form-label">Content URL *</label>
                                        <input type="url" 
                                               class="form-control" 
                                               id="content_url" 
                                               name="content_url" 
                                               value="<?php echo htmlspecialchars($editItem['content_url'] ?? ''); ?>" 
                                               required>
                                        <div class="form-text">
                                            For images: Direct image URL | For videos: YouTube/Vimeo embed URL | For links: External website URL
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="thumbnail_url" class="form-label">Thumbnail URL</label>
                                        <input type="url" 
                                               class="form-control" 
                                               id="thumbnail_url" 
                                               name="thumbnail_url" 
                                               value="<?php echo htmlspecialchars($editItem['thumbnail_url'] ?? ''); ?>">
                                        <div class="form-text">Optional thumbnail image for video and link content types</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status *</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="active" <?php echo ($editItem['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo ($editItem['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            <option value="draft" <?php echo ($editItem['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="sort_order" 
                                               name="sort_order" 
                                               value="<?php echo $editItem['sort_order'] ?? 0; ?>"
                                               min="0">
                                        <div class="form-text">Lower numbers appear first</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="client_name" class="form-label">Client Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="client_name" 
                                               name="client_name" 
                                               value="<?php echo htmlspecialchars($editItem['client_name'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="project_date" class="form-label">Project Date</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="project_date" 
                                               name="project_date" 
                                               value="<?php echo $editItem['project_date'] ?? ''; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="metrics" class="form-label">Key Metrics</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="metrics" 
                                               name="metrics" 
                                               value="<?php echo htmlspecialchars($editItem['metrics'] ?? ''); ?>"
                                               placeholder="e.g., 98% uptime, 30% cost reduction">
                                    </div>

                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="tags" 
                                               name="tags" 
                                               value="<?php echo htmlspecialchars(implode(', ', json_decode($editItem['tags'] ?? '[]', true) ?: [])); ?>"
                                               placeholder="tag1, tag2, tag3">
                                        <div class="form-text">Separate tags with commas</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="?" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="uil uil-check me-1"></i>
                                    <?php echo $action === 'add' ? 'Create' : 'Update'; ?> Project
                                </button>
                            </div>
                        </form>
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

        function filterPortfolio(status) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (status === '' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
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