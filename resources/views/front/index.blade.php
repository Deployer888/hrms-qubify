<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Reimagine HR Management</title>
    
    <!-- Meta Tags -->
    <meta name="description" content="All-in-one Human Resource Management software built to streamline workflows, boost team engagement, and keep your organization compliant—automatically.">
    <meta name="keywords" content="HRMS, Human Resource Management, HR Software, Employee Management, Payroll, Attendance">
    <meta name="author" content="HRMS Solutions">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('landing/images/favicon.ico') }}">
    
    <!-- External Stylesheets -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" rel="stylesheet">
    
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('landing/css/landing_style.css') }}">
</head>
<body>
    <!-- Navigation -->
    <!-- <nav class="navbar navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="#" aria-label="HRMS Home">
                <img src="{{ asset('landing/images/logo.png') }}" alt="">
            </a>
            
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav ms-auto me-4">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#benefits">Benefits</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#solutions">Solutions</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cal">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}" >Login</a>
                    </li>
                </ul>
                <div class="nav-ctaaa">
                    <a href="#cal" class="btn btn-primary">Start Free Trial</a>
                </div>
            </div>
        </div>
    </nav> -->

    <nav class="navbar navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="#" aria-label="HRMS Home">
                <img src="{{ asset('storage/uploads/logo/logo.png') }}" alt="">
            </a>
            
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav ms-auto me-4">
                    <!-- Features Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="featuresDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Features
                        </a>
                        <ul class="dropdown-menu features-dropdown" aria-labelledby="featuresDropdown">
                            <div class="dropdown-grid">
                                <li>
                                    <a href="#" class="dropdown-item-custom">
                                        <div class="dropdown-icon icon-payroll">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="dropdown-content">
                                            <h6>Payroll Management</h6>
                                            <p>Automate salary calculations, tax deductions, and generate payslips effortlessly</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item-custom">
                                        <div class="dropdown-icon icon-hiring">
                                            <i class="fas fa-user-plus"></i>
                                        </div>
                                        <div class="dropdown-content">
                                            <h6>Hiring & Onboarding</h6>
                                            <p>Streamline recruitment process and welcome new employees seamlessly</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item-custom">
                                        <div class="dropdown-icon icon-tracking">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="dropdown-content">
                                            <h6>Time Tracking & Attendance</h6>
                                            <p>Monitor work hours, track attendance, and manage time-off requests efficiently</p>
                                        </div>
                                    </a>
                                </li>
                            </div>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#benefits">Benefits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cal">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}" >Login</a>
                    </li>
                </ul>
                <div class="nav-ctaaa">
                    <a href="#cal" class="btn btn-primary">Start Free Trial</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Offcanvas Menu -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">HRMS Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <!-- Features with Submenu -->
                <li class="nav-item">
                    <button class="features-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#featuresSubmenu" aria-expanded="false" aria-controls="featuresSubmenu">
                        Features
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </button>
                    <div class="collapse features-collapse" id="featuresSubmenu">
                        <ul class="features-submenu">
                            <li class="submenu-item">
                                <a href="#" class="submenu-link" data-bs-dismiss="offcanvas">
                                    <div class="submenu-icon icon-payroll">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="submenu-content">
                                        <h6>Payroll Management</h6>
                                        <p>Automate salary calculations and payslips</p>
                                    </div>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="#" class="submenu-link" data-bs-dismiss="offcanvas">
                                    <div class="submenu-icon icon-hiring">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="submenu-content">
                                        <h6>Hiring & Onboarding</h6>
                                        <p>Streamline recruitment process</p>
                                    </div>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="#" class="submenu-link" data-bs-dismiss="offcanvas">
                                    <div class="submenu-icon icon-tracking">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="submenu-content">
                                        <h6>Time Tracking & Attendance</h6>
                                        <p>Monitor work hours and attendance</p>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#benefits" data-bs-dismiss="offcanvas">Benefits</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#testimonials" data-bs-dismiss="offcanvas">Reviews</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#cal" data-bs-dismiss="offcanvas">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}" data-bs-dismiss="offcanvas">Login</a>
                </li>
            </ul>
            <div class="offcanvas-ctaaa">
                <a href="#cal" class="btn btn-primary" data-bs-dismiss="offcanvas">Start Free Trial</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main">
        <!-- Hero Section -->
        <section class="hero-section" id="hero">
            <div class="hero-background">
                <div class="hero-shapes" aria-hidden="true">
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                    <div class="shape shape-3"></div>
                </div>
            </div>
            
            <div class="container">
                <div class="row align-items-center min-vh-100">
                    <div class="col-lg-5 hero-content" data-aos="fade-right" data-aos-duration="800">
                        <div class="hero-badge">
                            <i class="fas fa-award" aria-hidden="true"></i>
                            <span>Trusted by 10,000+ Companies</span>
                        </div>
                        
                        <h1 class="hero-title">Reimagine HR with <span class="gradient-text">HRMS</span></h1>
                        
                        <p class="hero-subtitle">All-in-one Human Resource Management software built to streamline workflows, boost team engagement, and keep your organization compliant—automatically.</p>
                        
                        <div class="hero-cta">
                            <a href="#cal" class="btn btn-primary btn-lg me-3" id="primaryCTAA">
                                <span>Start Free Trial</span>
                                <i class="fas fa-rocket ms-2" aria-hidden="true"></i>
                            </a>
                            <a href="#cal" class="btn btn-outline-dark btn-lg" id="secondaryCTAA">
                                <i class="fas fa-calendar me-2" aria-hidden="true"></i>
                                <span>Book a Live Demo</span>
                            </a>
                        </div>
                        
                        <p class="hero-note">
                            <i class="fas fa-cloud" aria-hidden="true"></i>
                            Fully cloud-based, mobile-ready, and customizable for businesses of all sizes.
                        </p>
                    </div>
                    
                    <div class="col-lg-7" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
                        <figure><img src="{{ asset('landing/images/ban-image.png') }}" alt=""></figure>
                    </div>
                </div>
            </div>
        </section>

        <!-- Value Proposition Section -->
        <section class="value-section section-padding">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7" data-aos="fade-right">
                        <div class="section-header">
                            <h2 class="section-title text-start">Take the Stress Out of <span class="gradient-text">HR Operations</span></h2>
                            <p class="section-description text-start">Managing people is complex—but your tools shouldn't be. HRMS simplifies core HR functions by centralizing everything into one intuitive system. That means less time chasing paperwork and more time focusing on your people.</p>
                        </div>
                        
                        <div class="value-points">
                            <div class="value-point" data-aos="fade-up" data-aos-delay="100">
                                <div class="point-icon">
                                    <i class="fas fa-robot" aria-hidden="true"></i>
                                </div>
                                <div class="point-content">
                                    <h4>Automate hiring, onboarding, payroll, and leave tracking</h4>
                                </div>
                            </div>
                            
                            <div class="value-point" data-aos="fade-up" data-aos-delay="200">
                                <div class="point-icon">
                                    <i class="fas fa-chart-line" aria-hidden="true"></i>
                                </div>
                                <div class="point-content">
                                    <h4>Gain real-time visibility into performance and workforce trends</h4>
                                </div>
                            </div>
                            
                            <div class="value-point" data-aos="fade-up" data-aos-delay="300">
                                <div class="point-icon">
                                    <i class="fas fa-heart" aria-hidden="true"></i>
                                </div>
                                <div class="point-content">
                                    <h4>Deliver a better experience for employees and HR teams alike</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-5" data-aos="fade-left">
                        <div class="value-visual">
                            <div class="process-diagram">
                                <div class="process-step">
                                    <div class="step-icon">
                                        <i class="fas fa-file-upload"></i>
                                    </div>
                                    <div class="step-content">
                                        <h5>Collect Data</h5>
                                        <p>Automated data collection from multiple sources</p>
                                    </div>
                                </div>
                                <div class="process-arrow">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <div class="process-step">
                                    <div class="step-icon">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <div class="step-content">
                                        <h5>Process Intelligently</h5>
                                        <p>AI-powered analysis and workflow automation</p>
                                    </div>
                                </div>
                                <div class="process-arrow">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <div class="process-step">
                                    <div class="step-icon">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                    <div class="step-content">
                                        <h5>Deliver Results</h5>
                                        <p>Actionable insights and automated outcomes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features-section section-padding">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h2 class="section-title">Complete HR Management <span class="gradient-text">In One Platform</span></h2>
                    <p class="section-description">Everything you need to manage your workforce effectively, integrated into one powerful system</p>
                </div>
                
                <div class="features-grid">
                    <!-- Employee Management -->
                    <div class="feature-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="feature-content">
                                    <div class="feature-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h3 class="feature-title">Employee Management</h3>
                                    <h4 class="feature-subtitle">Everything about your team in one place</h4>
                                    <p class="feature-description">Manage complete employee records including roles, departments, reporting lines, and access permissions. HRMS helps you onboard faster, stay organized, and reduce manual data entry.</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <figure><img src="{{ asset('landing/images/employee-management.png') }}" alt="" ></figure>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance & Leave -->
                    <div class="feature-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="row align-items-center">
                            <div class="col-lg-6 order-lg-2">
                                <div class="feature-content">
                                    <div class="feature-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <h3 class="feature-title">Attendance & Leave</h3>
                                    <h4 class="feature-subtitle">Track hours, leave, and time off—automatically</h4>
                                    <p class="feature-description">Real-time attendance logging integrates with biometrics or virtual check-ins. Employees can request time off directly, while HR reviews everything from a centralized dashboard.</p>
                                </div>
                            </div>
                            <div class="col-lg-6 order-lg-1 pe-5">
                                <figure><img src="{{ asset('landing/images/management-1.png') }}" alt=""></figure>
                            </div>
                        </div>
                    </div>

                    <!-- Payroll & Compensation -->
                    <div class="feature-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="feature-content">
                                    <div class="feature-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <h3 class="feature-title">Payroll & Compensation</h3>
                                    <h4 class="feature-subtitle">Run payroll without stress</h4>
                                    <p class="feature-description">HRMS handles salary calculations, deductions, and bonus payouts with precision. Automatically generate payslips, process payments, and stay tax-compliant without switching systems.</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <figure><img src="{{ asset('landing/images/payroll.png') }}" alt=""></figure>
                            </div>
                        </div>
                    </div>

                    <!-- Performance & Appraisals -->
                    <div class="feature-item" data-aos="fade-up" data-aos-delay="400">
                        <div class="row align-items-center">
                            <div class="col-lg-6 order-lg-2">
                                <div class="feature-content">
                                    <div class="feature-icon">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <h3 class="feature-title">Performance & Appraisals</h3>
                                    <h4 class="feature-subtitle">Turn feedback into growth</h4>
                                    <p class="feature-description">Set goals, track OKRs, and collect performance reviews in one place. Whether you're running quarterly appraisals or informal check-ins, HRMS gives your team the tools to grow.</p>
                                </div>
                            </div>
                            <div class="col-lg-6 order-lg-1">
                                <figure><img src="{{ asset('landing/images/analys.png') }}" alt=""></figure>
                            </div>
                        </div>
                    </div>

                    <!-- Hiring & Onboarding -->
                    <div class="feature-item" data-aos="fade-up" data-aos-delay="500">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="feature-content">
                                    <div class="feature-icon">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                    <h3 class="feature-title">Hiring & Onboarding</h3>
                                    <h4 class="feature-subtitle">Attract talent, onboard faster</h4>
                                    <p class="feature-description">Post job openings, review applications, and automate interview scheduling. Once hired, employees are automatically guided through onboarding with pre-assigned tasks and documents.</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="feature-visual">
                                    <div class="hiring-pipeline">
                                        <div class="pipeline-stage">
                                            <div class="stage-header">
                                                <div class="stage-title">Applications</div>
                                                <div class="stage-count">24</div>
                                            </div>
                                        </div>
                                        <div class="pipeline-arrow">→</div>
                                        <div class="pipeline-stage">
                                            <div class="stage-header">
                                                <div class="stage-title">Interviews</div>
                                                <div class="stage-count">8</div>
                                            </div>
                                        </div>
                                        <div class="pipeline-arrow">→</div>
                                        <div class="pipeline-stage">
                                            <div class="stage-header">
                                                <div class="stage-title">Offers</div>
                                                <div class="stage-count">3</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset & Document Control -->
                    <div class="feature-item" data-aos="fade-up" data-aos-delay="600">
                        <div class="row align-items-center">
                            <div class="col-lg-6 order-lg-2">
                                <div class="feature-content">
                                    <div class="feature-icon">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <h3 class="feature-title">Asset & Document Control</h3>
                                    <h4 class="feature-subtitle">Keep track of company resources</h4>
                                    <p class="feature-description">From laptops to ID cards, HRMS logs every asset allocated to your team. Upload, organize, and protect employee files in a secure, access-controlled document vault.</p>
                                </div>
                            </div>
                            <div class="col-lg-6 order-lg-1">
                                <div class="feature-visual">
                                    <div class="asset-tracker">
                                        <div class="asset-categories">
                                            <div class="asset-category">
                                                <div class="category-icon">
                                                    <i class="fas fa-laptop"></i>
                                                </div>
                                                <div class="category-info">
                                                    <div class="category-name">Laptops</div>
                                                    <div class="category-count">45 Active</div>
                                                </div>
                                            </div>
                                            <div class="asset-category">
                                                <div class="category-icon">
                                                    <i class="fas fa-mobile-alt"></i>
                                                </div>
                                                <div class="category-info">
                                                    <div class="category-name">Phones</div>
                                                    <div class="category-count">32 Active</div>
                                                </div>
                                            </div>
                                            <div class="asset-category">
                                                <div class="category-icon">
                                                    <i class="fas fa-id-card"></i>
                                                </div>
                                                <div class="category-info">
                                                    <div class="category-name">ID Cards</div>
                                                    <div class="category-count">78 Active</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reporting & Analytics -->
                    <div class="feature-item" data-aos="fade-up" data-aos-delay="700">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="feature-content">
                                    <div class="feature-icon">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <h3 class="feature-title">Reporting & Analytics</h3>
                                    <h4 class="feature-subtitle">Make decisions backed by real-time data</h4>
                                    <p class="feature-description">Customizable reports give you instant visibility into key HR metrics—from headcount and payroll to performance trends. Spot issues early and optimize your strategy continuously.</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <figure><img src="{{ asset('landing/images/growth.png') }}" alt=""></figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section id="benefits" class="benefits-section section-padding">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h2 class="section-title">Why Choose <span class="gradient-text">HRMS</span></h2>
                    <p class="section-description">Built for modern businesses that value security, scalability, and exceptional user experience</p>
                </div>
                
                <div class="benefits-grid">
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="100">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="benefit-title">Enhanced Compliance & Security</h3>
                        <p class="benefit-description mb-0">Every action is logged. Every file is encrypted. HRMS is built to meet global compliance standards—so you never have to worry about audits or data breaches.</p>
                    </div>
                    
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="200">
                        <div class="benefit-icon">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </div>
                        <h3 class="benefit-title">Built to Scale with You</h3>
                        <p class="benefit-description mb-0">Start small, grow big. HRMS is modular and flexible, so you can expand features and customize workflows as your team grows.</p>
                    </div>
                    
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="300">
                        <div class="benefit-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="benefit-title">Save Time & Cut Costs</h3>
                        <p class="benefit-description mb-0">Stop wasting hours on spreadsheets and approvals. Automate core processes and focus your HR team's energy where it matters most.</p>
                    </div>
                    
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="400">
                        <div class="benefit-icon">
                            <i class="fas fa-smile"></i>
                        </div>
                        <h3 class="benefit-title">Better Employee Experience</h3>
                        <p class="benefit-description mb-0">Employees get a clean, self-service portal where they can check payslips, apply for leave, track performance, and manage personal info—no HR tickets required.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Use Cases Section -->
        <section id="use-cases" class="use-cases-section section-padding">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h2 class="section-title">Who's <span class="gradient-text">HRMS</span> For?</h2>
                    <p class="section-description">HRMS is designed for teams that need control, visibility, and automation. Whether you're remote-first or office-based, here's how teams use it:</p>
                </div>
                
                <div class="use-cases-carousel owl-carousel owl-theme" data-aos="fade-up" data-aos-delay="200">
                    <div class="use-case-card">
                        <div class="use-case-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3 class="use-case-title">Startups</h3>
                        <p class="use-case-description">Fast-track hiring and scale your team without chaos</p>
                        <ul class="use-case-features">
                            <li><i class="fas fa-check"></i> Essential HR tools</li>
                            <li><i class="fas fa-check"></i> Affordable pricing</li>
                            <li><i class="fas fa-check"></i> Scale as you grow</li>
                        </ul>
                    </div>
                    
                    <div class="use-case-card">
                        <div class="use-case-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="use-case-title">Enterprises</h3>
                        <p class="use-case-description">Standardize HR processes across multiple locations</p>
                        <ul class="use-case-features">
                            <li><i class="fas fa-check"></i> Advanced analytics</li>
                            <li><i class="fas fa-check"></i> Custom integrations</li>
                            <li><i class="fas fa-check"></i> Enterprise security</li>
                        </ul>

                    </div>
                    
                    <div class="use-case-card">
                        <div class="use-case-icon">
                            <i class="fas fa-laptop-house"></i>
                        </div>
                        <h3 class="use-case-title">Remote Teams</h3>
                        <p class="use-case-description">Manage people, documents, and time zones from one dashboard</p>
                        <ul class="use-case-features">
                            <li><i class="fas fa-check"></i> Cloud-first design</li>
                            <li><i class="fas fa-check"></i> Virtual onboarding</li>
                            <li><i class="fas fa-check"></i> Mobile-friendly</li>
                        </ul>

                    </div>
                    
                    <div class="use-case-card">
                        <div class="use-case-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3 class="use-case-title">Agencies</h3>
                        <p class="use-case-description">Handle contractors, time tracking, and payroll in a unified space</p>
                        <ul class="use-case-features">
                            <li><i class="fas fa-check"></i> Project-based tracking</li>
                            <li><i class="fas fa-check"></i> Flexible billing</li>
                            <li><i class="fas fa-check"></i> Client reporting</li>
                        </ul>

                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="testimonials-section section-padding">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h2 class="section-title">What Customers Are <span class="gradient-text">Saying</span></h2>
                    <p class="section-description">Join thousands of companies that trust HRMS to manage their most valuable asset - their people</p>
                </div>
                
                <div class="testimonials-carousel owl-carousel owl-theme" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <blockquote class="testimonial-text">"We consolidated 4 tools into HRMS. Payroll, performance, onboarding—it's all just there, and it works."</blockquote>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%236366f1'/><text x='50%' y='50%' dy='0.35em' text-anchor='middle' fill='white' font-size='30' font-weight='bold'>MP</text></svg>" alt="Maya P." loading="lazy">
                            </div>
                            <div class="author-info">
                                <h4 class="author-name">Maya P.</h4>
                                <p class="author-title">VP of People</p>
                                <p class="author-company">SaaS Company</p>
                            </div>
                        </div>
                        <div class="testimonial-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <blockquote class="testimonial-text">"Our payroll used to take two full days every month. Now it takes 30 minutes. That's the impact HRMS had."</blockquote>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%2310b981'/><text x='50%' y='50%' dy='0.35em' text-anchor='middle' fill='white' font-size='30' font-weight='bold'>LD</text></svg>" alt="Leo D." loading="lazy">
                            </div>
                            <div class="author-info">
                                <h4 class="author-name">Leo D.</h4>
                                <p class="author-title">HR Manager</p>
                                <p class="author-company">Logistics Firm</p>
                            </div>
                        </div>
                        <div class="testimonial-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <blockquote class="testimonial-text">"It's a relief not having to chase people for leave approvals or document signatures anymore."</blockquote>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23f59e0b'/><text x='50%' y='50%' dy='0.35em' text-anchor='middle' fill='white' font-size='30' font-weight='bold'>AR</text></svg>" alt="Ayesha R." loading="lazy">
                            </div>
                            <div class="author-info">
                                <h4 class="author-name">Ayesha R.</h4>
                                <p class="author-title">Operations Lead</p>
                                <p class="author-company">E-commerce Startup</p>
                            </div>
                        </div>
                        <div class="testimonial-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="final-cta-section section-padding">
            <div class="container">
                <div class="row justify-content-center ">
                    <div class="col-lg-8 text-center">
                        <div class="final-cta-content" data-aos="fade-up">
                            <h2 class="cta-title">Simplify HR. <span class="gradient-text">Empower Your Team.</span></h2>
                            <p class="cta-description">HRMS gives you the tools to work smarter—not harder. Whether you're building a team or managing thousands, you'll have everything you need to stay in control.</p>
                            
                            <div class="cta-buttons">
                                <a href="#cal" class="btn btn-outline-light btn-lg me-3">
                                    <i class="fas fa-rocket me-2"></i>
                                    <span>Start Free Trial</span>
                                </a>
                                <a href="#cal" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-phone me-2"></i>
                                    <span>Talk to an Expert</span>
                                </a>
                            </div>
                            
                            <div class="cta-features" data-aos="fade-up" data-aos-delay="200">
                                <div class="cta-feature">
                                    <i class="fas fa-tools"></i>
                                    <span>No setup fees</span>
                                </div>
                                <div class="cta-feature">
                                    <i class="fas fa-clock"></i>
                                    <span>Go live in under a week</span>
                                </div>
                                <div class="cta-feature">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>SOC2 & GDPR compliant</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Inline Calendar through Calendly section -->
        <section id="cal" class="cal-section">
            <div class="container my-5">
                <h2 class="text-center mb-3">Book a Time That Works for You</h2>
       
                <!-- 1) Placeholder for the inline widget -->
                <div id="calendly-inline-scheduler">
                    <!-- Calendly inline widget begin -->
                    <div class="calendly-inline-widget" data-url="https://calendly.com/qubifytech/hrms-demo-meeting" style="min-width:320px;height:700px;"></div>
                    <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
                    <!-- Calendly inline widget end -->
                </div>
            </div>
        </section>
        
    </main>

    <!-- Footer -->
    <footer class="footer-section" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-brand">
                            <div class="brand-logo">
                               <img src="{{ asset('landing/images/logo.png') }}" alt="">
                            </div>
                            <p class="brand-description">Transform your workforce management with intelligent HR solutions that scale with your business.</p>
                            <div class="social-links">
                                <a href="#" class="social-link" aria-label="Follow us on Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="Connect with us on LinkedIn">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="Like us on Facebook">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="Follow us on Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5 class="footer-title">Product</h5>
                        <ul class="footer-links">
                            <li><a href="#features">Features</a></li>
                            <li><a href="#">Pricing</a></li>
                            <li><a href="#">Integrations</a></li>
                            <li><a href="#">Security</a></li>
                            <li><a href="#">API</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5 class="footer-title">Solutions</h5>
                        <ul class="footer-links">
                            <li><a href="#">For Startups</a></li>
                            <li><a href="#">For Enterprise</a></li>
                            <li><a href="#">For Remote Teams</a></li>
                            <li><a href="#">For Agencies</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5 class="footer-title">Resources</h5>
                        <ul class="footer-links">
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Documentation</a></li>
                            <li><a href="#">Blog</a></li>
                            <li><a href="#">Case Studies</a></li>
                            <li><a href="#">Webinars</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5 class="footer-title">Company</h5>
                        <ul class="footer-links">
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Careers</a></li>
                            <li><a href="#">Press</a></li>
                            <li><a href="#">Contact</a></li>
                            <li><a href="#">Partners</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright">&copy; 2025 HRMS Solutions. All rights reserved.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-legal">
                            <a href="#">Privacy Policy</a>
                            <a href="#">Terms of Service</a>
                            <a href="#">Cookie Policy</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="{{ asset('landing/js/landing_script.js') }}"></script>
</body>
</html>