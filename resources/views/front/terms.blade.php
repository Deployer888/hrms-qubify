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
        
        <style> 
            main.container {
                max-width: 900px;
                margin: 40px auto;
                background-color: var(--card-bg);
                padding: 40px 30px;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            }

            h1 {
                color: var(--primary-color);
                font-size: 32px;
                margin-bottom: 20px;
            }

            h2 {
                font-size: 22px;
                margin-top: 30px;
                color: var(--secondary-color);
            }

            p, li, address {
                font-size: 16px;
                line-height: 1.7;
                color: var(--text-color);
            }

            ul {
                padding-left: 20px;
            }

            li {
                margin-bottom: 8px;
            }

            a {
                color: var(--accent-color);
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
            }

            .footer {
                margin-top: 50px;
                border-top: 1px solid var(--border-color);
                padding-top: 20px;
                font-size: 14px;
                color: #6b7280;
                text-align: center;
            }

            @media screen and (max-width: 600px) {
                .container {
                    margin: 20px;
                    padding: 25px 20px;
                }

                h1 {
                    font-size: 26px;
                }

                h2 {
                    font-size: 20px;
                }

                p, li {
                    font-size: 15px;
                }
            }
        </style>

    </head>
    <body> 
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
        
        <main id="main" class="my-5">
            <div class="container">
                <h1>Terms & Conditions</h1>

                <p>Welcome to Qubify HRMS, provided by Qubify Technologies Private Limited. By using this application, you agree to the following terms and conditions. If you do not agree, please refrain from using the app.</p>

                <h2>1. Acceptance of Terms</h2>
                <p>By downloading, accessing, or using Qubify HRMS, you agree to be bound by these Terms & Conditions, our Privacy Policy, and any applicable laws and regulations.</p>

                <h2>2. User Responsibilities</h2>
                <ul>
                    <li>Users must provide accurate and complete information during registration and usage.</li>
                    <li>Employees are responsible for proper use of the Clock-In/Clock-Out system.</li>
                    <li>Field employees agree to share location during work hours to ensure accurate tracking and attendance.</li>
                </ul>

                <h2>3. Location Tracking</h2>
                <p>The app captures real-time location every 30 minutes for employees working outside the office premises. The system may auto-clock out users if they leave the designated premises without clocking out manually.</p>

                <h2>4. Leave Management</h2>
                <ul>
                    <li>Employees may submit leave requests through the app.</li>
                    <li>Team Leaders have the authority to approve or reject leave requests.</li>
                    <li>All actions are notified in real-time via app notifications.</li>
                </ul>

                <h2>5. Data Usage & Security</h2>
                <p>We collect only the information necessary to operate the app effectively. Your data is protected under our Privacy Policy and is not sold or misused.</p>

                <h2>6. Account Termination</h2>
                <p>We reserve the right to suspend or terminate access to the app if a user is found violating policies, misusing features, or attempting to tamper with the system.</p>

                <h2>7. Changes to Terms</h2>
                <p>We may update these Terms & Conditions periodically. Continued use of the app constitutes your acceptance of any modifications.</p>

                <h2>8. Contact Us</h2>
                <p>For any concerns regarding these Terms & Conditions, you may reach out to us:</p>
                <address>
                    Qubify Technologies Private Limited<br />
                    Office Space No. 242, 2nd Floor, Tricity Plaza,<br />
                    Peermuchalla, Zirakpur, Tehsil Derabassi,<br />
                    Distt S.A.S. Nagar, Zirakpur, Mohali, Rajpura,<br />
                    Punjab, India, 140603<br />
                    Email: <a href="mailto:contact@qubifytech.com">contact@qubifytech.com</a>
                </address>

                <div class="footer">
                    <p>Effective Date: July 14, 2025</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer-section" id="contact">
            <div class="container">
                <div class="footer-content">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="footer-brand">
                                <div class="brand-logo">
                                <img src="{{ asset('storage/uploads/logo/logo.png') }}" alt="">
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