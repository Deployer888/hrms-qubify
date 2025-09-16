@php
$logo = asset('storage/uploads/logo/');
$company_favicon = Utility::getValByName('company_favicon');
@endphp
<!DOCTYPE html>
<html lang="en"{{ env('SITE_RTL') == 'on' ? ' dir=rtl' : '' }}>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="url" content="{{ url('') . '/' . config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">
    
    <title>
        {{ Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'Qubify HRMS') }}
        - @yield('page-title')
    </title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.ico') }}" type="image" sizes="16x16">

    @stack('head')
    
    <!-- Core CSS Framework -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    
    <!-- Custom Application CSS (Load FIRST to establish base styles) -->
    @if(file_exists(public_path('assets/css/site.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v={{ filemtime(public_path('assets/css/site.css')) }}">
    @endif
    
    @if(file_exists(public_path('assets/css/ac.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/ac.css') }}?v={{ filemtime(public_path('assets/css/ac.css')) }}">
    @endif
    
    @if(file_exists(public_path('assets/css/stylesheet.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}?v={{ filemtime(public_path('assets/css/stylesheet.css')) }}">
    @endif
    
    <!-- Plugin CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1/daterangepicker.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

    <!-- RTL Support -->
    @if (env('SITE_RTL') == 'on' && file_exists(public_path('css/bootstrap-rtl.css')))
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}?v={{ filemtime(public_path('css/bootstrap-rtl.css')) }}">
    @endif
    
    <!-- Custom Overrides -->
    @if(file_exists(public_path('css/custom.css')))
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}">
    @endif
    
    <!-- Attendance Timepicker Styles -->
    @if(file_exists(public_path('css/attendance-timepicker.css')))
        <link rel="stylesheet" href="{{ asset('css/attendance-timepicker.css') }}?v={{ filemtime(public_path('css/attendance-timepicker.css')) }}">
    @endif
    
    @stack('css-page')

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif!important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)!important;
        }
        
        .page-title { display: none!important; }
        #loader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.95); z-index: 9999;
            display: flex; align-items: center; justify-content: center; flex-direction: column;
        }
        .spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        /* Sidebar dropdown fixes */
        .nav-link[data-bs-toggle="collapse"] {
            position: relative;
            cursor: pointer;
        }
        
        .nav-link[data-bs-toggle="collapse"] .fas.fa-sort-up {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%) rotate(0deg);
            transition: transform 0.3s ease;
        }
        
        .nav-link[data-bs-toggle="collapse"].collapsed .fas.fa-sort-up {
            transform: translateY(-50%) rotate(-90deg);
        }
        
        .nav-link[data-bs-toggle="collapse"]:not(.collapsed) .fas.fa-sort-up {
            transform: translateY(-50%) rotate(0deg);
        }
        
        /* Smooth collapse animation */
        .collapse {
            transition: height 0.35s ease;
        }
        
        /* Submenu styling */
        .submenu-ul {
            padding-left: 0;
            margin-left: 20px;
        }
        
        .submenu-ul .nav-item {
            border-left: 2px solid rgba(255,255,255,0.1);
            margin-left: 10px;
        }
        
        .submenu-ul .nav-link {
            padding-left: 20px;
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .submenu-ul .nav-item.active .nav-link {
            opacity: 1;
            font-weight: 500;
        }

        .time-input-group {
            position: relative;
        }

        .time-input-group .form-control {
            padding-right: 45px;
            background-color: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .time-input-group .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .time-picker-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            cursor: pointer;
            z-index: 10;
            pointer-events: none;
        }

        .time-input-group .form-control:focus + .time-picker-icon {
            color: #2563eb;
        }

        /* Tempus Dominus customization */
        .bootstrap-datetimepicker-widget {
            z-index: 9999 !important;
        }

        .bootstrap-datetimepicker-widget .list-unstyled {
            margin: 0;
        }

        .bootstrap-datetimepicker-widget .timepicker .timepicker-hour,
        .bootstrap-datetimepicker-widget .timepicker .timepicker-minute {
            font-size: 1.2em;
            font-weight: bold;
        }

        /* Modal Enhancements */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .modal-header {
            border-bottom: none;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            padding: 1.5rem 2rem;
        }

        .modal-header .modal-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0;
        }

        .modal-header .close-icon {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .modal-header .close-icon:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }

        .modal-body {
            padding: 0;
            background: #f8fafc;
        }

        /* Fix for Select2 in modals */
        .select2-container {
            z-index: 9999 !important;
        }

        .select2-dropdown {
            z-index: 9999 !important;
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            height: auto;
            padding: 0.75rem 1rem;
            background: #fff;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            font-weight: 500;
            padding: 0;
            line-height: 1.5;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 10px;
        }

        /* Daterangepicker in modals */
        .daterangepicker {
            z-index: 9999 !important;
        }

        /* Loading states */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Form validation styles */
        .form-control.is-invalid,
        .form-control-modern.is-invalid {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        }

        .form-control.is-valid,
        .form-control-modern.is-valid {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        /* Time picker modal overlay */
        .time-picker-modal {
            backdrop-filter: blur(4px);
        }

        .time-picker-content {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 1rem;
            }
            
            .attendance-form-body {
                padding: 1.5rem;
            }
            
            .modal-footer-modern {
                padding: 1rem 1.5rem;
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .btn-modern {
                width: 100%;
                justify-content: center;
            }
            
            .time-picker-content {
                margin: 1rem;
                min-width: auto;
                width: calc(100% - 2rem);
            }
        }

        /* Fix for Bootstrap 5 modal backdrop */
        .modal-backdrop {
            z-index: 1050;
        }

        .modal {
            z-index: 1055;
        }

        .time-picker-modal {
            z-index: 1060;
        }

        /* Additional form enhancements */
        .form-group-modern {
            position: relative;
        }

        .form-group-modern:hover .form-label-modern {
            color: #2563eb;
        }

        .form-control-modern:focus + .input-icon {
            color: #2563eb;
            transform: translateY(-50%) scale(1.1);
        }

        /* Success state animations */
        @keyframes successPulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .form-control-modern.success-animation {
            animation: successPulse 1s ease-out;
        }

        /* Button loading animation */
        .loading-btn .fa-spinner {
            animation: spin 1s linear infinite;
        }

        /* Time display hover effects */
        .time-display:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .time-display:active {
            transform: translateY(0);
        }

        /* Error state styling */
        .error-message {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .error-message::before {
            content: "⚠";
            font-size: 0.875rem;
        }

        /* Success message styling */
        .success-message {
            color: #10b981;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .success-message::before {
            content: "✓";
            font-size: 0.875rem;
        }
    </style>
</head>

<body class="application application-offset">
        
    <div class="container-fluid container-application">
        @include('partial.Admin.menu')
        <div class="main-content position-relative">
            @include('partial.Admin.header')
            <div class="page-content">
                <div class="page-title">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-xl-3 col-lg-3 col-md-12 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                            <div class="d-inline-block">
                                <h5 class="h4 d-inline-block font-weight-400 mb-0">@yield('page-title')</h5>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-9 col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            @yield('action-button')
                        </div>
                    </div>
                </div>
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Common Modal -->
    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div>
                    <h4 class="h4 font-weight-400 float-left modal-title" id="exampleModalLabel"></h4>
                    <a href="#" class="more-text widget-text float-right close-icon" data-bs-dismiss="modal" aria-label="Close">{{ __('Close') }}</a>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <!-- Core JavaScript -->
    
    
    <!-- Essential Plugins -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1/daterangepicker.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" crossorigin="anonymous"></script>

    <!-- Custom Scripts -->
    @if(file_exists(public_path('js/custom.js')))
        <script src="{{ asset('js/custom.js') }}?v={{ filemtime(public_path('js/custom.js')) }}"></script>
    @endif
    
    @if(file_exists(public_path('assets/js/site.js')))
        <script src="{{ asset('assets/js/site.js') }}?v={{ filemtime(public_path('assets/js/site.js')) }}"></script>
    @endif

    <script>
        // Global variables
        var toster_pos = "{{ env('SITE_RTL') == 'on' ? 'left' : 'right' }}";
        var dataTabelLang = {
            paginate: { previous: "{{ __('Previous') }}", next: "{{ __('Next') }}" },
            lengthMenu: "{{ __('Show') }} _MENU_ {{ __('entries') }}",
            zeroRecords: "{{ __('No data available in table') }}",
            info: "{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}",
            infoEmpty: " ",
            search: "{{ __('Search:') }}"
        };

        // Configure toastr
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-" + toster_pos
            };
        }

        // Hide loader
        $(window).on('load', function() {
            setTimeout(function() {
                $('#loader').fadeOut(500);
            }, 1000);
        });

        // Fallback loader hide
        $(document).ready(function() {
            setTimeout(function() {
                $('#loader').fadeOut(500);
            }, 3000);
        });

        // Fix Bootstrap 4 to Bootstrap 5 compatibility for sidebar dropdowns AND header dropdowns
        $(document).ready(function() {
            // Convert Bootstrap 4 data-toggle to Bootstrap 5 data-bs-toggle for ALL elements
            $('[data-toggle="collapse"]').each(function() {
                $(this).attr('data-bs-toggle', 'collapse');
                $(this).removeAttr('data-toggle');
                console.log('Fixed sidebar dropdown:', $(this).text().trim());
            });
            
            // Fix header dropdowns
            $('[data-toggle="dropdown"]').each(function() {
                $(this).attr('data-bs-toggle', 'dropdown');
                $(this).removeAttr('data-toggle');
                console.log('Fixed header dropdown');
            });
            
            // Fix tooltips
            $('[data-toggle="tooltip"]').each(function() {
                $(this).attr('data-bs-toggle', 'tooltip');
                $(this).removeAttr('data-toggle');
            });
            
            // Initialize Bootstrap 5 dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
            
            // Initialize Bootstrap 5 tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Enhanced sidebar dropdown handling with proper toggle and click-outside-to-close
            $(document).off('click.sidebar-dropdown').on('click.sidebar-dropdown', '.nav-link[data-bs-toggle="collapse"], .nav-link[href^="#navbar-"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $this = $(this);
                var target = $this.attr('href') || $this.attr('data-bs-target');
                var $target = $(target);
                
                console.log('Sidebar dropdown clicked:', $this.text().trim(), 'Target:', target);
                
                if ($target.length) {
                    var isCurrentlyOpen = $target.hasClass('show');
                    
                    // Close ALL dropdowns first
                    $('.collapse.show').each(function() {
                        $(this).removeClass('show').slideUp(200);
                    });
                    $('.nav-link[data-bs-toggle="collapse"], .nav-link[href^="#navbar-"]').each(function() {
                        $(this).removeClass('active').addClass('collapsed');
                        $(this).attr('aria-expanded', 'false');
                    });
                    
                    // If this dropdown was closed, open it
                    if (!isCurrentlyOpen) {
                        $target.addClass('show').slideDown(200);
                        $this.addClass('active').removeClass('collapsed');
                        $this.attr('aria-expanded', 'true');
                        console.log('Opened dropdown:', $this.text().trim());
                    } else {
                        console.log('Closed dropdown:', $this.text().trim());
                    }
                }
            });
            
            // Close dropdowns when clicking outside the sidebar
            $(document).on('click.sidebar-outside', function(e) {
                if (!$(e.target).closest('.sidenav').length) {
                    $('.collapse.show').removeClass('show').slideUp(200);
                    $('.nav-link[data-bs-toggle="collapse"], .nav-link[href^="#navbar-"]').removeClass('active').addClass('collapsed').attr('aria-expanded', 'false');
                }
            });
            
            // Prevent dropdown from closing when clicking inside the dropdown content
            $('.collapse').on('click', function(e) {
                e.stopPropagation();
            });

            // Initialize dropdowns that should be open based on current route
            $('.nav-link.active[data-bs-toggle="collapse"]').each(function() {
                var target = $(this).attr('href') || $(this).attr('data-bs-target');
                var $target = $(target);
                if ($target.length && !$target.hasClass('show')) {
                    $target.addClass('show');
                    $(this).removeClass('collapsed');
                    $(this).attr('aria-expanded', 'true');
                }
            });
        });

        (function() {
            'use strict';
            
            // Global utilities
            window.AttendanceFormUtils = {
                // Format time to HH:MM
                formatTime: function(hour, minute) {
                    return String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                },
                
                // Parse time string to hour and minute
                parseTime: function(timeStr) {
                    if (!timeStr || timeStr === 'HH:MM') return { hour: 0, minute: 0 };
                    const [hour, minute] = timeStr.split(':');
                    return { 
                        hour: parseInt(hour) || 0, 
                        minute: parseInt(minute) || 0 
                    };
                },
                
                // Validate time
                isValidTime: function(timeStr) {
                    if (!timeStr || timeStr === 'HH:MM') return false;
                    const { hour, minute } = this.parseTime(timeStr);
                    return hour >= 0 && hour <= 23 && minute >= 0 && minute <= 59;
                },
                
                // Compare times (returns -1 if time1 < time2, 0 if equal, 1 if time1 > time2)
                compareTimes: function(time1, time2) {
                    const t1 = this.parseTime(time1);
                    const t2 = this.parseTime(time2);
                    const minutes1 = t1.hour * 60 + t1.minute;
                    const minutes2 = t2.hour * 60 + t2.minute;
                    return minutes1 < minutes2 ? -1 : minutes1 > minutes2 ? 1 : 0;
                }
            };
            
            // Enhanced form validation
            window.validateAttendanceForm = function() {
                let isValid = true;
                const errors = {};
                
                // Clear previous errors
                if (window.clearAllErrors) {
                    clearAllErrors();
                }
                
                // Validate employee
                const employee = document.getElementById('employee_id');
                if (employee && !employee.value) {
                    errors.employee = 'Please select an employee';
                    isValid = false;
                }
                
                // Validate date
                const date = document.getElementById('date');
                if (date && !date.value) {
                    errors.date = 'Please select a date';
                    isValid = false;
                }
                
                // Validate clock in
                const clockIn = document.getElementById('clock_in');
                if (clockIn && (!clockIn.value || !AttendanceFormUtils.isValidTime(clockIn.value))) {
                    errors.clock_in = 'Please enter a valid clock in time';
                    isValid = false;
                }
                
                // Validate clock out (if provided)
                const clockOut = document.getElementById('clock_out');
                if (clockOut && clockOut.value && !AttendanceFormUtils.isValidTime(clockOut.value)) {
                    errors.clock_out = 'Please enter a valid clock out time';
                    isValid = false;
                }
                
                // Validate time sequence
                if (clockIn && clockOut && clockIn.value && clockOut.value && 
                    AttendanceFormUtils.isValidTime(clockIn.value) && 
                    AttendanceFormUtils.isValidTime(clockOut.value)) {
                    
                    if (AttendanceFormUtils.compareTimes(clockOut.value, clockIn.value) <= 0) {
                        errors.clock_out = 'Clock out time must be after clock in time';
                        isValid = false;
                    }
                }
                
                // Show errors
                Object.keys(errors).forEach(field => {
                    if (window.showError) {
                        showError(field, errors[field]);
                    }
                });
                
                return isValid;
            };
            
            // Initialize when DOM is ready
            $(document).ready(function() {
                // Prevent multiple initializations
                if (window.attendanceFormInitialized) return;
                window.attendanceFormInitialized = true;
                
                // Enhanced modal handling
                $(document).off('show.bs.modal.attendance').on('show.bs.modal.attendance', '#commonModal', function() {
                    console.log('Modal showing - preparing form components');
                });
                
                $(document).off('shown.bs.modal.attendance').on('shown.bs.modal.attendance', '#commonModal', function() {
                    console.log('Modal shown - initializing form components');
                    
                    // Delay initialization to ensure DOM is ready
                    setTimeout(function() {
                        try {
                            common_bind();
                            
                            // Focus on first visible input
                            const firstInput = $('#commonModal').find('input:visible:first, select:visible:first');
                            if (firstInput.length) {
                                firstInput.focus();
                            }
                            
                            console.log('Form components initialized successfully');
                        } catch (error) {
                            console.error('Error initializing form components:', error);
                        }
                    }, 300);
                });
                
                $(document).off('hidden.bs.modal.attendance').on('hidden.bs.modal.attendance', '#commonModal', function() {
                    console.log('Modal hidden - cleaning up');
                    
                    // Close time picker if open
                    const timePicker = document.getElementById('timePickerModal');
                    if (timePicker) {
                        timePicker.style.display = 'none';
                    }
                    
                    // Reset current time target
                    if (window.currentTimeTarget) {
                        window.currentTimeTarget = null;
                    }
                    
                    // Clear any global form state
                    if (window.attendanceFormState) {
                        window.attendanceFormState = {};
                    }
                });
                
                // Handle form submissions globally
                $(document).off('submit.attendance').on('submit.attendance', '#attendanceForm', function(e) {
                    e.preventDefault();
                    
                    console.log('Form submission attempted');
                    
                    if (window.validateAttendanceForm && !validateAttendanceForm()) {
                        console.log('Form validation failed');
                        return false;
                    }
                    
                    const $form = $(this);
                    const $submitBtn = $form.find('button[type="submit"]');
                    
                    // Prevent double submission
                    if ($submitBtn.hasClass('loading-btn')) {
                        return false;
                    }
                    
                    // Add loading state
                    $submitBtn.addClass('loading-btn');
                    const originalText = $submitBtn.html();
                    $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating...');
                    
                    // Submit form
                    setTimeout(() => {
                        this.submit();
                    }, 500);
                    
                    // Fallback to restore button
                    setTimeout(() => {
                        if ($submitBtn.hasClass('loading-btn')) {
                            $submitBtn.removeClass('loading-btn').html(originalText);
                        }
                    }, 10000);
                });
                
                // Handle time picker outside clicks
                $(document).off('click.timepicker').on('click.timepicker', function(e) {
                    const timePicker = document.getElementById('timePickerModal');
                    if (timePicker && timePicker.style.display === 'flex') {
                        if (e.target === timePicker) {
                            if (window.closeTimePicker) {
                                closeTimePicker();
                            }
                        }
                    }
                });
                
                // Handle keyboard events
                $(document).off('keydown.attendance').on('keydown.attendance', function(e) {
                    // Escape key closes time picker
                    if (e.key === 'Escape') {
                        const timePicker = document.getElementById('timePickerModal');
                        if (timePicker && timePicker.style.display === 'flex') {
                            if (window.closeTimePicker) {
                                closeTimePicker();
                            }
                        }
                    }
                    
                    // Enter key in time inputs confirms selection
                    if (e.key === 'Enter') {
                        const target = e.target;
                        if (target && (target.id === 'hourInput' || target.id === 'minuteInput')) {
                            e.preventDefault();
                            if (window.confirmTime) {
                                confirmTime();
                            }
                        }
                    }
                });
                
                // Auto-format number inputs
                $(document).off('input.timeformat').on('input.timeformat', '#hourInput, #minuteInput', function() {
                    let value = parseInt(this.value) || 0;
                    const isHour = this.id === 'hourInput';
                    const max = isHour ? 23 : 59;
                    
                    if (value < 0) value = 0;
                    if (value > max) value = max;
                    
                    this.value = value;
                });
                
                console.log('Attendance form system initialized');
            });
            
            // Global error handling for AJAX requests
            $(document).ajaxError(function(event, xhr, settings, thrownError) {
                if (settings.url && settings.url.includes('attendanceemployee')) {
                    console.error('Attendance AJAX Error:', thrownError);
                    
                    // Hide loading states
                    $('.loading-btn').removeClass('loading-btn').html('Create Attendance');
                    
                    // Show user-friendly error
                    if (typeof show_toastr !== 'undefined') {
                        show_toastr('Error', 'An error occurred. Please try again.', 'error');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
            
        })();

        // Utility to ensure form functions are available globally
        window.ensureFormFunctions = function() {
            if (typeof window.openTimePicker === 'undefined') {
                console.log('Timepicker functions not found, initializing...');
                
                // Re-initialize form functions if needed
                setTimeout(function() {
                    const script = document.querySelector('script[src*="attendance"]');
                    if (script) {
                        // Script is loaded, functions should be available
                        console.log('Attendance script loaded');
                    }
                }, 100);
            }
        };

        // Call ensure functions on load
        $(document).ready(function() {
            window.ensureFormFunctions();
        });
    </script>

    @stack('theme-script')
    @include('Chatify::layouts.footerLinks')

    <!-- Toast Messages -->
    @if ($message = Session::get('success'))
        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('{!! addslashes($message) !!}', 'Success');
                    }
                }, 1000);
            });
        </script>
    @endif
    
    @if ($message = Session::get('error'))
        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('{!! addslashes($message) !!}', 'Error');
                    }
                }, 1000);
            });
        </script>
    @endif
</body>
</html>