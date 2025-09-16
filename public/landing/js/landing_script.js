/**
 * Complete HRMS Website - JavaScript
 * Professional HR Management System Landing Page
 */

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeNavigation();
    initializeScrollEffects();
    initializeAnimations();
    initializeCarousels();
    initializeFormHandlers();
    initializeCounterAnimations();
    initializeAccessibility();
    
    console.log('🚀 HRMS Website loaded successfully!');
});

/**
 * Navigation Functions
 */
function initializeNavigation() {
    const navbar = document.getElementById('mainNavbar');
    const navLinks = document.querySelectorAll('.nav-link');
    const mobileToggle = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    // Navbar scroll effect
    function handleNavbarScroll() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
    
    // Smooth scroll for navigation links
    function handleSmoothScroll(e) {
        const targetId = this.getAttribute('href');
        
        if (targetId.startsWith('#')) {
            e.preventDefault();
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const navbarHeight = navbar.offsetHeight;
                const targetPosition = targetElement.offsetTop - navbarHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                if (navbarCollapse.classList.contains('show')) {
                    mobileToggle.click();
                }
                
                // Update active link
                updateActiveNavLink(targetId);
            }
        }
    }
    
    // Update active navigation link
    function updateActiveNavLink(activeId) {
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === activeId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
    
    // Handle mobile menu
    function handleMobileMenu() {
        const isOpen = navbarCollapse.classList.contains('show');
        document.body.style.overflow = isOpen ? 'hidden' : 'auto';
    }
    
    // Event listeners
    window.addEventListener('scroll', throttle(handleNavbarScroll, 16));
    navLinks.forEach(link => link.addEventListener('click', handleSmoothScroll));
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            setTimeout(handleMobileMenu, 300);
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!navbar.contains(e.target) && navbarCollapse.classList.contains('show')) {
            mobileToggle.click();
        }
    });
}

/**
 * Scroll Effects
 */
function initializeScrollEffects() {
    initializeScrollToTop();
    initializeSectionHighlight();
    initializeScrollIndicator();
}

/**
 * Scroll to Top Button
 */
function initializeScrollToTop() {
    const scrollButton = document.getElementById('scrollToTop');
    
    if (!scrollButton) return;
    
    function toggleScrollButton() {
        if (window.scrollY > 500) {
            scrollButton.classList.add('visible');
        } else {
            scrollButton.classList.remove('visible');
        }
    }
    
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
    
    window.addEventListener('scroll', throttle(toggleScrollButton, 16));
    scrollButton.addEventListener('click', scrollToTop);
}

/**
 * Section Highlighting
 */
function initializeSectionHighlight() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const sectionId = `#${entry.target.id}`;
                updateActiveNavLink(sectionId);
            }
        });
    }, {
        threshold: 0.3,
        rootMargin: '-20% 0px -70% 0px'
    });
    
    sections.forEach(section => sectionObserver.observe(section));
    
    function updateActiveNavLink(activeId) {
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === activeId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
}

/**
 * Scroll Indicator
 */
function initializeScrollIndicator() {
    const indicator = document.createElement('div');
    indicator.className = 'scroll-indicator';
    indicator.innerHTML = '<div class="scroll-progress"></div>';
    indicator.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: rgba(37, 99, 235, 0.1);
        z-index: 9999;
    `;
    
    const progressBar = indicator.querySelector('.scroll-progress');
    progressBar.style.cssText = `
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        width: 0%;
        transition: width 0.1s ease;
    `;
    
    document.body.appendChild(indicator);
    
    function updateScrollIndicator() {
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrolled = window.scrollY;
        const progress = (scrolled / scrollHeight) * 100;
        
        progressBar.style.width = `${Math.min(progress, 100)}%`;
    }
    
    window.addEventListener('scroll', throttle(updateScrollIndicator, 16));
}

/**
 * Animations
 */
function initializeAnimations() {
    // Initialize AOS (Animate On Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 100,
            disable: 'mobile'
        });
    }
    
    // Initialize custom animations
    initializeHeroAnimations();
    initializeFeatureAnimations();
}

/**
 * Hero Animations
 */
function initializeHeroAnimations() {
    const heroTitle = document.querySelector('.hero-section .hero-title');
    const heroStats = document.querySelectorAll('.hero-visual .stat-number');
    
    // Typewriter effect for hero title (optional)
    if (heroTitle && !heroTitle.hasAttribute('data-animated')) {
        setTimeout(() => {
            heroTitle.style.opacity = '1';
            heroTitle.style.transform = 'translateY(0)';
        }, 500);
    }
    
    // Animate hero stats
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateStats(heroStats);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        observer.observe(heroSection);
    }
}

/**
 * Feature Animations
 */
function initializeFeatureAnimations() {
    const featureItems = document.querySelectorAll('.feature-item');
    
    featureItems.forEach((item, index) => {
        item.addEventListener('mouseenter', () => {
            const visual = item.querySelector('.feature-visual');
            if (visual) {
                visual.style.transform = 'scale(1.05)';
                visual.style.transition = 'transform 0.3s ease';
            }
        });
        
        item.addEventListener('mouseleave', () => {
            const visual = item.querySelector('.feature-visual');
            if (visual) {
                visual.style.transform = 'scale(1)';
            }
        });
    });
    
    // Animate progress bars
    const progressBars = document.querySelectorAll('.metric-fill');
    const progressObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
                progressObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    progressBars.forEach(bar => progressObserver.observe(bar));
}

/**
 * Counter Animations
 */
function initializeCounterAnimations() {
    const counters = document.querySelectorAll('.stat-number, .payroll-amount, .score-number');
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.hasAttribute('data-animated')) {
                entry.target.setAttribute('data-animated', 'true');
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => counterObserver.observe(counter));
}

function animateCounter(element) {
    const text = element.textContent.trim();
    const isPercentage = text.includes('%');
    const isDollar = text.includes('$');
    const isDecimal = text.includes('.');
    
    let targetValue;
    let prefix = '';
    let suffix = '';
    
    if (isDollar) {
        prefix = '$';
        targetValue = parseFloat(text.replace(/[$,]/g, ''));
    } else if (isPercentage) {
        suffix = '%';
        targetValue = parseFloat(text.replace('%', ''));
    } else if (isDecimal) {
        targetValue = parseFloat(text);
    } else {
        targetValue = parseInt(text.replace(/[^0-9]/g, ''));
    }
    
    if (isNaN(targetValue)) return;
    
    const duration = 2000;
    const startTime = performance.now();
    
    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        let currentValue;
        if (isDecimal) {
            currentValue = (easeOutCubic(progress) * targetValue).toFixed(1);
        } else if (isDollar && targetValue > 1000) {
            currentValue = formatNumber(Math.floor(easeOutCubic(progress) * targetValue));
        } else {
            currentValue = Math.floor(easeOutCubic(progress) * targetValue);
        }
        
        element.textContent = prefix + currentValue + suffix;
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = text;
        }
    }
    
    requestAnimationFrame(updateCounter);
}

function animateStats(stats) {
    stats.forEach((stat, index) => {
        setTimeout(() => {
            animateCounter(stat);
        }, index * 200);
    });
}

/**
 * Carousel Initialization
 */
function initializeCarousels() {
    // Use Cases Carousel
    $('.use-cases-carousel').owlCarousel({
        loop: true,
        margin: 30,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });
    
    // Testimonials Carousel
    $('.testimonials-carousel').owlCarousel({
        loop: true,
        margin: 30,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            1200: {
                items: 3
            }
        }
    });
    
    // Pause carousels when user prefers reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        $('.owl-carousel').trigger('stop.owl.autoplay');
    }
}

/**
 * Form Handlers
 */
function initializeFormHandlers() {
    // CTA Button handlers
    const ctaButtons = document.querySelectorAll('[id$="CTA"], .btn-primary, .btn-outline-primary');
    
    /* ctaButtons.forEach(button => {
        button.addEventListener('click', handleCTAClick);
    }); */
    
    // Newsletter signup
    const newsletterForms = document.querySelectorAll('.newsletter-form');
    newsletterForms.forEach(form => {
        form.addEventListener('submit', handleNewsletterSignup);
    });
}

function handleCTAClick(e) {
    e.preventDefault();
    
    const buttonText = e.target.textContent.trim();
    const buttonType = e.target.classList.contains('btn-primary') ? 'primary' : 'secondary';
    
    // Analytics tracking
    trackEvent('CTA Click', {
        button_text: buttonText,
        button_type: buttonType,
        page_section: getCurrentSection(e.target)
    });
    
    // Button animation
    animateButton(e.target);
    
    // Show modal or redirect (demo purposes)
    showCTAModal(buttonText);
}

function handleNewsletterSignup(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const email = formData.get('email');
    
    if (validateEmail(email)) {
        showSuccessMessage('Thank you for subscribing!');
        e.target.reset();
        trackEvent('Newsletter Signup', { email: email });
    } else {
        showErrorMessage('Please enter a valid email address.');
    }
}

/**
 * Accessibility Functions
 */
function initializeAccessibility() {
    // Keyboard navigation
    initializeKeyboardNavigation();
    
    // Focus management
    initializeFocusManagement();
    
    // Screen reader improvements
    initializeScreenReaderSupport();
    
    // Reduced motion support
    initializeReducedMotion();
}

function initializeKeyboardNavigation() {
    // Skip to main content link
    const skipLink = document.querySelector('.skip-link');
    if (skipLink) {
        skipLink.addEventListener('click', (e) => {
            e.preventDefault();
            const main = document.getElementById('main');
            if (main) {
                main.focus();
                main.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
    
    // Escape key handlers
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Close any open modals or menus
            const openMenu = document.querySelector('.navbar-collapse.show');
            
            if (openMenu) {
                document.querySelector('.navbar-toggler').click();
            }
        }
    });
    
    // Arrow key navigation for carousels
    document.querySelectorAll('.owl-carousel').forEach(carousel => {
        carousel.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                $(carousel).trigger('prev.owl.carousel');
            } else if (e.key === 'ArrowRight') {
                $(carousel).trigger('next.owl.carousel');
            }
        });
    });
}

function initializeFocusManagement() {
    // Ensure interactive elements are focusable
    const interactiveElements = document.querySelectorAll('button, a, input, select, textarea');
    
    interactiveElements.forEach(element => {
        if (!element.hasAttribute('tabindex') && element.getAttribute('tabindex') !== '0') {
            // Only add tabindex if not already set
            if (element.tagName === 'A' && !element.getAttribute('href')) {
                element.setAttribute('tabindex', '0');
                element.setAttribute('role', 'button');
            }
        }
    });
}

function initializeScreenReaderSupport() {
    // Add ARIA labels to interactive elements without labels
    const unlabeledElements = document.querySelectorAll('button:not([aria-label]):not([aria-labelledby]), a:not([aria-label]):not([aria-labelledby])');
    
    unlabeledElements.forEach(element => {
        const text = element.textContent.trim() || element.getAttribute('title') || element.getAttribute('alt');
        if (text) {
            element.setAttribute('aria-label', text);
        }
    });
    
    // Add proper roles to carousel elements
    document.querySelectorAll('.owl-carousel').forEach((carousel, index) => {
        carousel.setAttribute('role', 'region');
        carousel.setAttribute('aria-label', `Carousel ${index + 1}`);
    });
    
    // Live region for announcements
    if (!document.getElementById('live-region')) {
        const liveRegion = document.createElement('div');
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        liveRegion.id = 'live-region';
        document.body.appendChild(liveRegion);
    }
}

function initializeReducedMotion() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (prefersReducedMotion) {
        // Disable animations
        document.body.classList.add('reduced-motion');
        
        // Disable AOS animations
        if (typeof AOS !== 'undefined') {
            AOS.init({ disable: true });
        }
        
        // Stop carousel autoplay
        $('.owl-carousel').trigger('stop.owl.autoplay');
    }
}

/**
 * Analytics Functions
 */
function trackEvent(eventName, properties = {}) {
    // Console log for demo purposes
    console.log(`📊 Analytics Event: ${eventName}`, properties);
    
    // In production, you would send this to your analytics service
    // Example: gtag('event', eventName, properties);
    // Example: analytics.track(eventName, properties);
}

/**
 * Utility Functions
 */

// Throttle function for performance
function throttle(func, delay) {
    let timeoutId;
    let lastExecTime = 0;
    
    return function (...args) {
        const currentTime = Date.now();
        
        if (currentTime - lastExecTime > delay) {
            func.apply(this, args);
            lastExecTime = currentTime;
        } else {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
                lastExecTime = Date.now();
            }, delay - (currentTime - lastExecTime));
        }
    };
}

// Debounce function
function debounce(func, delay) {
    let timeoutId;
    
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// Easing functions
function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
}

function easeInOutCubic(t) {
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
}

// Number formatting
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Email validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Get current section
function getCurrentSection(element) {
    const section = element.closest('section');
    return section ? section.id || section.className : 'unknown';
}

// Button animation
function animateButton(button) {
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 150);
}

// Modal functions
function showCTAModal(buttonText) {
    const message = buttonText.includes('Trial') 
        ? 'Ready to start your free trial? In a real application, this would redirect to the signup page.'
        : buttonText.includes('Demo')
        ? 'Want to see a demo? In a real application, this would open a booking calendar.'
        : 'Thank you for your interest! In a real application, this would show more information.';
    
    // Create a simple modal instead of alert
    showNotification(message, 'info');
}

// Message functions
function showSuccessMessage(message) {
    showNotification(message, 'success');
}

function showErrorMessage(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
            <button class="notification-close" aria-label="Close notification">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 400px;
    `;
    
    const content = notification.querySelector('.notification-content');
    content.style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.75rem;
    `;
    
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        opacity: 0.8;
        margin-left: auto;
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Close button functionality
    closeBtn.addEventListener('click', () => {
        hideNotification(notification);
    });
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideNotification(notification);
    }, 5000);
    
    // Announce to screen readers
    const liveRegion = document.getElementById('live-region');
    if (liveRegion) {
        liveRegion.textContent = message;
        setTimeout(() => {
            liveRegion.textContent = '';
        }, 1000);
    }
}

function hideNotification(notification) {
    notification.style.transform = 'translateX(100%)';
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

/**
 * Performance Monitoring
 */
function initializePerformanceMonitoring() {
    // Monitor page load performance
    window.addEventListener('load', () => {
        setTimeout(() => {
            if (performance.getEntriesByType) {
                const perfData = performance.getEntriesByType('navigation')[0];
                
                if (perfData) {
                    trackEvent('Performance', {
                        load_time: Math.round(perfData.loadEventEnd - perfData.loadEventStart),
                        dom_content_loaded: Math.round(perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart),
                        total_page_load: Math.round(perfData.loadEventEnd - perfData.fetchStart)
                    });
                }
            }
        }, 0);
    });
    
    // Monitor scroll depth
    trackScrollDepth();
    
    // Monitor time on page
    trackTimeOnPage();
}

function trackScrollDepth() {
    let maxScroll = 0;
    const milestones = [25, 50, 75, 90, 100];
    const trackedMilestones = new Set();
    
    function updateScrollDepth() {
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrolled = window.scrollY;
        const scrollPercent = Math.round((scrolled / scrollHeight) * 100);
        
        if (scrollPercent > maxScroll) {
            maxScroll = scrollPercent;
            
            milestones.forEach(milestone => {
                if (scrollPercent >= milestone && !trackedMilestones.has(milestone)) {
                    trackedMilestones.add(milestone);
                    trackEvent('Scroll Depth', { percent: milestone });
                }
            });
        }
    }
    
    window.addEventListener('scroll', throttle(updateScrollDepth, 1000));
}

function trackTimeOnPage() {
    const startTime = Date.now();
    
    function sendTimeTracking() {
        const timeSpent = Math.round((Date.now() - startTime) / 1000);
        trackEvent('Time on Page', { seconds: timeSpent });
    }
    
    // Track time intervals
    const intervals = [30, 60, 120, 300]; // 30s, 1m, 2m, 5m
    intervals.forEach(interval => {
        setTimeout(sendTimeTracking, interval * 1000);
    });
    
    // Track on page unload
    window.addEventListener('beforeunload', sendTimeTracking);
}

/**
 * Feature-specific Functions
 */
function initializeFeatureInteractions() {
    // Add hover effects to feature cards
    const featureCards = document.querySelectorAll('.feature-item, .benefit-card, .use-case-card');
    
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
            card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
            card.style.boxShadow = '';
        });
    });
    
    // Add click tracking to feature cards
    featureCards.forEach(card => {
        card.addEventListener('click', () => {
            const title = card.querySelector('h3')?.textContent || 'Unknown';
            trackEvent('Feature Card Click', { feature: title });
        });
    });
}

/**
 * Error Handling
 */
window.addEventListener('error', (e) => {
    console.error('JavaScript Error:', e.error);
    trackEvent('JavaScript Error', {
        message: e.message,
        filename: e.filename,
        lineno: e.lineno
    });
});

window.addEventListener('unhandledrejection', (e) => {
    console.error('Unhandled Promise Rejection:', e.reason);
    trackEvent('Promise Rejection', {
        reason: e.reason
    });
});

/**
 * Initialize all performance monitoring
 */
initializePerformanceMonitoring();
initializeFeatureInteractions();

/**
 * Lazy Loading for Images
 */
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
}

// Initialize lazy loading
initializeLazyLoading();

/**
 * Service Worker Registration (for PWA capabilities)
 */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

/**
 * Dark Mode Support (Optional)
 */
function initializeDarkMode() {
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    if (prefersDarkScheme.matches) {
        document.body.classList.add('dark-mode');
    }
    
    prefersDarkScheme.addEventListener('change', (e) => {
        if (e.matches) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
    });
}

// Initialize dark mode support
initializeDarkMode();

console.log('✨ All HRMS functionality loaded and ready!');

// Export functions for testing (if in Node.js environment)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        throttle,
        debounce,
        validateEmail,
        formatNumber,
        easeOutCubic,
        easeInOutCubic,
        trackEvent
    };
}