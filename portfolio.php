<?php
// Include geo restrictions check
require_once 'config/geo-restrictions.php';

require_once 'config/database.php';

$portfolioManager = new PortfolioManager();
$settingsManager = new SettingsManager();

// Get filter parameters
$category = $_GET['category'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;

// Get all portfolio items and categories
$allItems = $portfolioManager->getAllItems('active');
$categories = $portfolioManager->getCategories();

// Filter by category if specified
$filteredItems = $category ? array_filter($allItems, function($item) use ($category) {
    return strtolower($item['category']) === strtolower($category);
}) : $allItems;

// Pagination
$totalItems = count($filteredItems);
$totalPages = ceil($totalItems / $perPage);
$offset = ($page - 1) * $perPage;
$portfolioItems = array_slice($filteredItems, $offset, $perPage);

// Get site settings
$siteTitle = $settingsManager->getSetting('site_title', 'Ted Rubin - Project Management');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Portfolio - <?php echo htmlspecialchars($siteTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional project management portfolio showcasing successful transformations from chaos to art" />
    <meta name="keywords" content="Portfolio, Project Management, Success Stories, Case Studies" />
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="uil uil-rocket me-2"></i>Ted Rubin
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="portfolio.php">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-light" style="padding-top: 120px; padding-bottom: 80px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-5 fw-bold mb-4">Project Portfolio</h1>
                    <p class="lead text-muted mb-0">
                        Explore my collection of successful project transformations. Each story represents 
                        chaos turned into a work of art through strategic project management.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="section">
        <div class="container">
            <!-- Portfolio Stats -->
            <div class="row mb-5">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-primary mb-1"><?php echo count($allItems); ?></h3>
                            <small class="text-muted">Total Projects</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-success mb-1"><?php echo count($categories); ?></h3>
                            <small class="text-muted">Categories</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <?php
                            $totalViews = 0;
                            foreach($allItems as $item) {
                                $totalViews += $item['views'];
                            }
                            ?>
                            <h3 class="text-info mb-1"><?php echo number_format($totalViews); ?></h3>
                            <small class="text-muted">Total Views</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <?php
                            $clientCount = count(array_unique(array_filter(array_column($allItems, 'client_name'))));
                            ?>
                            <h3 class="text-warning mb-1"><?php echo $clientCount; ?></h3>
                            <small class="text-muted">Happy Clients</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portfolio Filter -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-10">
                    <div class="portfolio-filter text-center">
                        <a href="portfolio.php" class="btn <?php echo !$category ? 'btn-primary' : 'btn-outline-primary'; ?> me-2 mb-2">
                            All Projects
                        </a>
                        <?php foreach($categories as $cat): ?>
                            <a href="?category=<?php echo urlencode($cat); ?>" 
                               class="btn <?php echo $category === $cat ? 'btn-primary' : 'btn-outline-primary'; ?> me-2 mb-2">
                                <?php echo htmlspecialchars($cat); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Current Filter Display -->
            <?php if ($category): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <span>
                                <i class="uil uil-filter me-2"></i>
                                Showing projects in: <strong><?php echo htmlspecialchars($category); ?></strong>
                                (<?php echo count($filteredItems); ?> projects)
                            </span>
                            <a href="portfolio.php" class="btn btn-sm btn-outline-primary">
                                <i class="uil uil-times me-1"></i>Clear Filter
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Portfolio Grid -->
            <?php if (empty($portfolioItems)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="uil uil-folder-open display-4 text-muted"></i>
                            <h4 class="mt-3 text-muted">No Projects Found</h4>
                            <p class="text-muted">
                                <?php if ($category): ?>
                                    No projects found in the "<?php echo htmlspecialchars($category); ?>" category.
                                <?php else: ?>
                                    No portfolio projects are currently available.
                                <?php endif; ?>
                            </p>
                            <a href="portfolio.php" class="btn btn-primary">View All Projects</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row" id="portfolio-grid">
                    <?php foreach($portfolioItems as $index => $project): 
                        $categoryClass = strtolower(str_replace(' ', '-', $project['category']));
                        $tags = json_decode($project['tags'], true) ?? [];
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4 portfolio-item <?php echo $categoryClass; ?>" 
                             data-item-id="<?php echo $project['id']; ?>"
                             style="animation-delay: <?php echo ($index * 0.1); ?>s;">
                            <div class="portfolio-card card border-0 shadow-sm h-100">
                                <?php if ($project['content_type'] === 'image' && $project['thumbnail_url']): ?>
                                    <div class="portfolio-image">
                                        <img src="<?php echo htmlspecialchars($project['thumbnail_url']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($project['title']); ?>"
                                             style="height: 250px; object-fit: cover;">
                                        <div class="portfolio-overlay">
                                            <a href="<?php echo htmlspecialchars($project['content_url']); ?>" 
                                               class="btn btn-primary btn-sm" 
                                               target="_blank">
                                                <i class="uil uil-eye"></i> View Project
                                            </a>
                                        </div>
                                    </div>
                                <?php elseif ($project['content_type'] === 'video'): ?>
                                    <div class="portfolio-video">
                                        <div class="video-thumbnail" style="height: 250px; background: linear-gradient(45deg, #2f55d4, #4d6ce8); position: relative; display: flex; align-items: center; justify-content: center;">
                                            <i class="uil uil-play-circle" style="font-size: 3rem; color: white; opacity: 0.9;"></i>
                                            <div class="video-overlay">
                                                <a href="<?php echo htmlspecialchars($project['content_url']); ?>" 
                                                   class="btn btn-primary btn-sm" 
                                                   target="_blank">
                                                    <i class="uil uil-play"></i> Watch Video
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($project['content_type'] === 'link'): ?>
                                    <div class="portfolio-link" style="height: 250px; background: linear-gradient(45deg, #22c55e, #16a34a); position: relative; display: flex; align-items: center; justify-content: center;">
                                        <i class="uil uil-external-link-alt" style="font-size: 3rem; color: white; opacity: 0.9;"></i>
                                        <div class="link-overlay">
                                            <a href="<?php echo htmlspecialchars($project['content_url']); ?>" 
                                               class="btn btn-success btn-sm" 
                                               target="_blank">
                                                <i class="uil uil-external-link-alt"></i> View Case Study
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($project['category']); ?></span>
                                        <div class="d-flex align-items-center text-muted">
                                            <small class="me-3">
                                                <i class="uil uil-eye me-1"></i><?php echo $project['views']; ?>
                                            </small>
                                            <?php if ($project['project_date']): ?>
                                                <small>
                                                    <i class="uil uil-calendar-alt me-1"></i>
                                                    <?php echo date('M Y', strtotime($project['project_date'])); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                    <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($project['description']); ?></p>
                                    
                                    <?php if ($project['client_name']): ?>
                                        <div class="client-info mb-2">
                                            <small class="text-muted">
                                                <i class="uil uil-building me-1"></i>
                                                <?php echo htmlspecialchars($project['client_name']); ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($project['metrics']): ?>
                                        <div class="project-metrics mb-3">
                                            <small class="text-success fw-semibold">
                                                <i class="uil uil-chart-growth-alt me-1"></i>
                                                <?php echo htmlspecialchars($project['metrics']); ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($tags)): ?>
                                        <div class="portfolio-tags">
                                            <?php foreach (array_slice($tags, 0, 4) as $tag): ?>
                                                <span class="badge bg-light text-dark me-1">
                                                    <?php echo htmlspecialchars($tag); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="row mt-5">
                        <div class="col-12">
                            <nav aria-label="Portfolio pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">
                                                <i class="uil uil-angle-left"></i> Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">
                                                Next <i class="uil uil-angle-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $perPage, $totalItems); ?> 
                                    of <?php echo $totalItems; ?> projects
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-8 col-12">
                    <div class="section-title">
                        <h4 class="title mb-3">Ready to transform your chaos into art?</h4>
                        <p class="para-desc title-dark text-muted mb-0">
                            Let's discuss how I can help bring order and success to your next project. 
                            Every great transformation starts with a conversation.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12 mt-4 pt-2 mt-sm-0 pt-sm-0 text-md-end">
                    <a href="index.php#contact" class="btn btn-primary">
                        Start Your Project <i class="uil uil-arrow-right align-middle"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer bg-dark text-white">
        <div class="container">
            <div class="row py-5">
                <div class="col-lg-6">
                    <h5 class="text-primary mb-3">
                        <i class="uil uil-rocket me-2"></i>Ted Rubin
                    </h5>
                    <p class="text-light opacity-75 mb-4">
                        Transforming chaos into works of art, one project at a time. 
                        Professional project management services that deliver results.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="uil uil-linkedin"></i></a>
                        <a href="#" class="text-light me-3"><i class="uil uil-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="uil uil-envelope"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light opacity-75">Home</a></li>
                        <li><a href="index.php#services" class="text-light opacity-75">Services</a></li>
                        <li><a href="portfolio.php" class="text-light opacity-75">Portfolio</a></li>
                        <li><a href="index.php#contact" class="text-light opacity-75">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white mb-3">Portfolio Categories</h6>
                    <ul class="list-unstyled">
                        <?php foreach(array_slice($categories, 0, 4) as $cat): ?>
                            <li>
                                <a href="?category=<?php echo urlencode($cat); ?>" class="text-light opacity-75">
                                    <?php echo htmlspecialchars($cat); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="row py-3">
                <div class="col-md-6">
                    <p class="text-light opacity-75 mb-0">
                        © <?php echo date('Y'); ?> Ted Rubin. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-light opacity-75 mb-0">
                        Turning chaos into art since <?php echo date('Y') - 10; ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>