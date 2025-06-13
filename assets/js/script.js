/**
 * Ted Rubin Project Management Site
 * Custom JavaScript
 */

(function() {
    'use strict';

    /**
     * Portfolio Filtering
     */
    function initPortfolioFilter() {
        const filterButtons = document.querySelectorAll('.portfolio-filter .btn');
        const portfolioItems = document.querySelectorAll('.portfolio-item');

        filterButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Get filter value
                const filterValue = this.getAttribute('data-filter');
                
                // Filter items
                portfolioItems.forEach(item => {
                    if (filterValue === '*' || item.classList.contains(filterValue.substring(1))) {
                        item.classList.remove('filtered');
                        item.classList.add('visible');
                        item.style.display = 'block';
                    } else {
                        item.classList.add('filtered');
                        item.classList.remove('visible');
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    /**
     * Portfolio Item Views Tracking
     */
    function initPortfolioTracking() {
        const portfolioLinks = document.querySelectorAll('.portfolio-overlay a, .video-overlay a, .link-overlay a');
        
        portfolioLinks.forEach(link => {
            link.addEventListener('click', function() {
                const portfolioCard = this.closest('.portfolio-item');
                if (portfolioCard) {
                    const itemId = portfolioCard.getAttribute('data-item-id');
                    if (itemId) {
                        // Track portfolio item view
                        trackPortfolioView(itemId);
                    }
                }
            });
        });
    }

    /**
     * Track Portfolio View
     */
    function trackPortfolioView(itemId) {
        // Send AJAX request to track view
        fetch('track-view.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ item_id: itemId })
        })
        .catch(error => {
            // Silently handle tracking errors
            console.debug('Portfolio tracking error:', error);
        });
    }

    // Initialize portfolio functionality
    document.addEventListener('DOMContentLoaded', function() {
        initNavigation();
        initSmoothScrolling();
        initFormHandling();
        initAnimations();
        initContactForm();
        initPortfolioFilter(); // Add portfolio filtering
        initPortfolioTracking(); // Add portfolio tracking
    });

    /**
     * Navigation Functions
     */
    function initNavigation() {
        const navbar = document.querySelector('.navbar');
        const navLinks = document.querySelectorAll('.nav-link');

        // Handle navbar scroll effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.classList.remove('scrolled');
                navbar.style.backgroundColor = '';
                navbar.style.backdropFilter = '';
            }
        });

        // Handle active nav link
        window.addEventListener('scroll', function() {
            let current = '';
            const sections = document.querySelectorAll('section[id]');
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        // Mobile menu close on link click
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    toggle: false
                });
                bsCollapse.hide();
            });
        });
    }

    /**
     * Smooth Scrolling
     */
    function initSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    const navHeight = document.querySelector('.navbar').offsetHeight;
                    const targetPosition = target.offsetTop - navHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    /**
     * Form Handling
     */
    function initFormHandling() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }

    /**
     * Contact Form Specific Handling
     */
    function initContactForm() {
        const contactForm = document.querySelector('#contact form');
        if (!contactForm) return;

        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Simulate form submission (replace with actual AJAX call)
            setTimeout(() => {
                // Reset button
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                // Show success message
                showNotification('Message sent successfully! I\'ll get back to you soon.', 'success');
                
                // Reset form
                this.reset();
            }, 2000);
        });
    }

    /**
     * Form Validation
     */
    function validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                showFieldError(input, 'This field is required');
                isValid = false;
            } else {
                clearFieldError(input);
                
                // Email validation
                if (input.type === 'email' && !isValidEmail(input.value)) {
                    showFieldError(input, 'Please enter a valid email address');
                    isValid = false;
                }
            }
        });
        
        return isValid;
    }

    /**
     * Field Error Handling
     */
    function showFieldError(input, message) {
        clearFieldError(input);
        input.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
    }

    function clearFieldError(input) {
        input.classList.remove('is-invalid');
        const errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    /**
     * Email Validation
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Animations
     */
    function initAnimations() {
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements for animation
        const animateElements = document.querySelectorAll(
            '.service-card, .portfolio-card, .testimonial-card, .section-title, .section-subtitle'
        );
        
        animateElements.forEach(el => {
            observer.observe(el);
        });

        // Counter animation for any number elements
        const counters = document.querySelectorAll('[data-count]');
        counters.forEach(counter => {
            observer.observe(counter);
            counter.addEventListener('animateCounter', function() {
                animateCounter(this);
            });
        });
    }

    /**
     * Counter Animation
     */
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, 16);
    }

    /**
     * Notification System
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 100px; right: 20px; z-index: 1050; min-width: 300px;';
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(notification);
            bsAlert.close();
        }, 5000);
    }

    /**
     * Utility Functions
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Performance optimization for scroll events
    const optimizedScrollHandler = debounce(function() {
        // Any scroll-based animations can go here
    }, 16);

    window.addEventListener('scroll', optimizedScrollHandler);

    /**
     * Page Load Performance
     */
    window.addEventListener('load', function() {
        // Hide loading spinner if you have one
        const loader = document.querySelector('.page-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => loader.remove(), 300);
        }

        // Add loaded class to body
        document.body.classList.add('page-loaded');
    });

    /**
     * Error Handling
     */
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', e.error);
        // You could send this to an error reporting service
    });

})();