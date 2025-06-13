<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Ted Rubin - Project Management Consultant | Transform Business Chaos into Success</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional project management consultant who transforms chaotic business processes into streamlined success. Specializing in team coordination, workflow optimization, and business transformation. Get results." />
    <meta name="keywords" content="project management consultant, business process improvement, workflow optimization, team coordination, business transformation, operational efficiency" />
    <meta name="author" content="Ted Rubin" />
    
    <!-- Open Graph for Social Media -->
    <meta property="og:title" content="Ted Rubin - Project Management Consultant | Transform Business Chaos">
    <meta property="og:description" content="Professional project management consultant who turns operational chaos into organized success. Proven results in business transformation.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/images/ted-rubin-og-image.jpg">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Ted Rubin - Project Management Consultant">
    <meta name="twitter:description" content="Transform your business chaos into organized success with professional project management consulting.">
    
    <!-- Schema.org markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        "name": "Ted Rubin Project Management Consulting",
        "description": "Professional project management consultant specializing in business transformation and process optimization",
        "url": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>",
        "telephone": "+1-555-123-4567",
        "email": "ted@example.com",
        "founder": {
            "@type": "Person",
            "name": "Ted Rubin",
            "jobTitle": "Project Management Consultant"
        },
        "serviceType": [
            "Project Management Consulting",
            "Business Process Improvement", 
            "Team Coordination",
            "Workflow Optimization",
            "Business Transformation",
            "Operational Efficiency Consulting"
        ],
        "areaServed": "United States",
        "priceRange": "$$"
    }
    </script>
    
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
            <a class="navbar-brand fw-bold text-primary" href="#home">
                <i class="uil uil-rocket me-2"></i>Ted Rubin
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#portfolio">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-7">
                    <div class="hero-content">
                        <h1 class="display-4 fw-bold mb-4">
                            Transform Your Business <span class="text-primary">Chaos</span><br>
                            into Organized Success
                        </h1>
                        <p class="lead text-muted mb-4">
                            Expert project management consultant specializing in business process improvement, 
                            team coordination, and workflow optimization. I turn operational chaos into 
                            streamlined success for growing businesses.
                        </p>
                        <div class="hero-buttons">
                            <a href="#contact" class="btn btn-primary btn-lg me-3">Get Free Consultation</a>
                            <a href="#services" class="btn btn-outline-primary btn-lg">View Services</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="hero-image">
                        <i class="uil uil-chart-growth display-1 text-primary opacity-75"></i>
                        <div class="floating-cards">
                            <div class="floating-card card-1">
                                <i class="uil uil-check-circle text-success"></i>
                                <span>Tasks Completed</span>
                            </div>
                            <div class="floating-card card-2">
                                <i class="uil uil-users-alt text-info"></i>
                                <span>Team Sync</span>
                            </div>
                            <div class="floating-card card-3">
                                <i class="uil uil-clock text-warning"></i>
                                <span>On Schedule</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section bg-light">
        <div class="container">
                            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="section-title">Professional Project Management Services</h2>
                    <p class="section-subtitle text-muted">
                        Comprehensive business consulting solutions that transform chaotic operations into 
                        streamlined, efficient processes. Specializing in organizational improvement and 
                        workflow optimization for growing businesses.
                    </p>
                </div>
            </div>
            
            <div class="row mt-5">
                <?php
                $services = [
                    [
                        'icon' => 'uil-rocket',
                        'title' => 'Strategic Project Planning & Management',
                        'description' => 'Transform business vision into actionable roadmaps with clear milestones, deliverables, and measurable outcomes for sustainable growth.',
                        'color' => 'primary'
                    ],
                    [
                        'icon' => 'uil-users-alt',
                        'title' => 'Team Coordination & Leadership',
                        'description' => 'Expert team coordination services that align cross-functional teams, improve communication, and drive collaborative success.',
                        'color' => 'success'
                    ],
                    [
                        'icon' => 'uil-chart-line',
                        'title' => 'Business Process Optimization',
                        'description' => 'Comprehensive workflow analysis and process improvement to eliminate bottlenecks and maximize operational efficiency.',
                        'color' => 'warning'
                    ],
                    [
                        'icon' => 'uil-clock-three',
                        'title' => 'Timeline & Resource Management',
                        'description' => 'Professional project scheduling and resource allocation to ensure on-time delivery while optimizing budget utilization.',
                        'color' => 'info'
                    ],
                    [
                        'icon' => 'uil-shield-check',
                        'title' => 'Risk Management & Crisis Resolution',
                        'description' => 'Proactive risk identification and mitigation strategies to protect project success and ensure business continuity.',
                        'color' => 'danger'
                    ],
                    [
                        'icon' => 'uil-presentation-line',
                        'title' => 'Stakeholder Communication Management',
                        'description' => 'Strategic stakeholder engagement and communication planning to maintain transparency and drive project alignment.',
                        'color' => 'secondary'
                    ]
                ];
                
                foreach($services as $service): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon text-<?php echo $service['color']; ?> mb-3">
                                    <i class="<?php echo $service['icon']; ?>"></i>
                                </div>
                                <h5 class="card-title"><?php echo $service['title']; ?></h5>
                                <p class="card-text text-muted"><?php echo $service['description']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="section">
        <div class="container">
                            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="section-title">Project Management Success Stories</h2>
                    <p class="section-subtitle text-muted">
                        Real business transformation results from my project management consulting. 
                        See how I help companies turn operational chaos into streamlined success.
                    </p>
                </div>
            </div>
            
            <!-- Portfolio Filter -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="portfolio-filter text-center">
                        <button class="btn btn-outline-primary active me-2 mb-2" data-filter="*">All Projects</button>
                        <?php
                        require_once 'config/database.php';
                        $portfolioManager = new PortfolioManager();
                        $categories = $portfolioManager->getCategories();
                        foreach($categories as $category): ?>
                            <button class="btn btn-outline-primary me-2 mb-2" data-filter=".<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5" id="portfolio-grid">
                <?php
                $projects = $portfolioManager->getAllItems('active', 6); // Limit to 6 for homepage
                
                foreach($projects as $project): 
                    $categoryClass = strtolower(str_replace(' ', '-', $project['category']));
                    $tags = json_decode($project['tags'], true) ?? [];
                ?>
                    <div class="col-lg-4 col-md-6 mb-4 portfolio-item <?php echo $categoryClass; ?>">
                        <div class="portfolio-card card border-0 shadow-sm h-100">
                            <?php if ($project['content_type'] === 'image' && $project['thumbnail_url']): ?>
                                <div class="portfolio-image">
                                    <img src="<?php echo htmlspecialchars($project['thumbnail_url']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($project['title']); ?>"
                                         style="height: 200px; object-fit: cover;">
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
                                    <div class="video-thumbnail" style="height: 200px; background: linear-gradient(45deg, #2f55d4, #4d6ce8); position: relative; display: flex; align-items: center; justify-content: center;">
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
                                <div class="portfolio-link" style="height: 200px; background: linear-gradient(45deg, #22c55e, #16a34a); position: relative; display: flex; align-items: center; justify-content: center;">
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
                                    <small class="text-muted">
                                        <i class="uil uil-eye me-1"></i><?php echo $project['views']; ?>
                                    </small>
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
                                        <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
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
            
            <div class="row">
                <div class="col-12 text-center mt-4">
                    <a href="portfolio.php" class="btn btn-primary">
                        View All Projects <i class="uil uil-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="section bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="section-title">What Clients Say</h2>
                    <p class="section-subtitle text-muted">
                        Don't just take my word for it - hear from satisfied clients.
                    </p>
                </div>
            </div>
            
            <div class="row mt-5">
                <?php
                $testimonials = [
                    [
                        'name' => 'Sarah Johnson',
                        'position' => 'CEO, TechStart Inc.',
                        'text' => 'Ted truly does turn chaos into art. Our project went from complete disarray to a smooth, efficient operation in just 3 months.',
                        'rating' => 5
                    ],
                    [
                        'name' => 'Michael Chen',
                        'position' => 'Operations Director',
                        'text' => 'Working with Ted was like watching a master conductor lead an orchestra. Every team member knew their part perfectly.',
                        'rating' => 5
                    ],
                    [
                        'name' => 'Lisa Rodriguez',
                        'position' => 'Product Manager',
                        'text' => 'Ted\'s approach to project management is both analytical and creative. He sees solutions where others see problems.',
                        'rating' => 5
                    ]
                ];
                
                foreach($testimonials as $testimonial): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card card border-0 shadow-sm h-100">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="stars mb-3">
                                    <?php for($i = 1; $i <= $testimonial['rating']; $i++): ?>
                                        <i class="uil uil-star text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="card-text text-muted flex-grow-1">"<?php echo $testimonial['text']; ?>"</p>
                                <div class="client-info mt-auto">
                                    <h6 class="mb-0"><?php echo $testimonial['name']; ?></h6>
                                    <small class="text-muted"><?php echo $testimonial['position']; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="section-title">Ready for Business Transformation?</h2>
                    <p class="section-subtitle text-muted">
                        Get a free consultation to discuss how professional project management can 
                        transform your operational challenges into organized success.
                    </p>
                </div>
            </div>
            
            <div class="row mt-5 justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-card card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <form action="contact-process.php" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Project Type</label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Select Project Type</option>
                                        <option value="new-project">New Project Launch</option>
                                        <option value="process-improvement">Process Improvement</option>
                                        <option value="team-coordination">Team Coordination</option>
                                        <option value="consultation">General Consultation</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="message" class="form-label">Tell me about your chaos</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" 
                                              placeholder="Describe your project challenges and goals..." required></textarea>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="uil uil-envelope me-2"></i>Start the Transformation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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
                    <h6 class="text-white mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light opacity-75">Project Planning</a></li>
                        <li><a href="#" class="text-light opacity-75">Team Coordination</a></li>
                        <li><a href="#" class="text-light opacity-75">Process Optimization</a></li>
                        <li><a href="#" class="text-light opacity-75">Risk Management</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white mb-3">Contact Info</h6>
                    <ul class="list-unstyled">
                        <li class="text-light opacity-75 mb-2">
                            <i class="uil uil-envelope me-2"></i>ted@example.com
                        </li>
                        <li class="text-light opacity-75 mb-2">
                            <i class="uil uil-phone me-2"></i>+1 (555) 123-4567
                        </li>
                        <li class="text-light opacity-75">
                            <i class="uil uil-map-marker me-2"></i>Your City, State
                        </li>
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