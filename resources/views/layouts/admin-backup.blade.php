@php
$logo = asset('storage/uploads/logo/');
$company_favicon = Utility::getValByName('company_favicon');

// Helper function for safe file versioning
function safeFileVersion($path) {
    $fullPath = public_path($path);
    return file_exists($fullPath) ? '?v=' . filemtime($fullPath) : '';
}
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
    <link rel="icon" href="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.ico') }}"
        type="image" sizes="16x16">

    @stack('head')
    
    <!-- DNS Prefetch for Performance (but not the problematic speedcf domain) -->
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//cdn.datatables.net">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//unpkg.com">
    
    <!-- Core CSS - NO INTEGRITY CHECKS to avoid mismatches -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/icons.css" crossorigin="anonymous">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" crossorigin="anonymous">
    
    <!-- Form Plugins CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1.0/daterangepicker.css" crossorigin="anonymous">
    
    <!-- UI Enhancement CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" crossorigin="anonymous">
    
    <!-- Custom CSS with Safe File Checking (Load FIRST) -->
    @if(file_exists(public_path('assets/css/site.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}{{ safeFileVersion('assets/css/site.css') }}">
    @endif
    
    @if(file_exists(public_path('assets/css/ac.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/ac.css') }}{{ safeFileVersion('assets/css/ac.css') }}">
    @endif
    
    @if(file_exists(public_path('assets/css/stylesheet.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}{{ safeFileVersion('assets/css/stylesheet.css') }}">
    @endif
    
    <!-- Laravel Mix Compiled CSS (Tailwind + Custom) - Load after custom CSS -->
    @if(file_exists(public_path('css/app.css')))
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @endif
    
    @if (env('SITE_RTL') == 'on' && file_exists(public_path('css/bootstrap-rtl.css')))
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}{{ safeFileVersion('css/bootstrap-rtl.css') }}">
    @endif
    
    @if(file_exists(public_path('css/custom.css')))
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}{{ safeFileVersion('css/custom.css') }}">
    @endif
    
    @stack('css-page')

    <!-- Critical inline styles -->
    <style>
        .page-title{
            display: none!important;
        }
        
        /* Optimized Loader */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: opacity 0.3s ease;
        }
        
        .spinner-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        /* Performance optimizations */
        * {
            box-sizing: border-box;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        canvas {
            position: absolute;
            z-index: 9999;
            top: 0;
            height: 100vh;
        }
        
        #musicModal {
            z-index: 99999!important;
        }
        
        .collapse {
            visibility: visible !important;
        }
        
        .navbar-user {
            width: 100%;
        }
        
        .modal-dialog {
            max-width: 900px !important;
        }
        
        /* Hide error notice initially */
        .error-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px 15px;
            border-radius: 4px;
            margin: 10px;
            font-size: 14px;
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
        }
    </style>
</head>

<body class="application application-offset">
    <!-- Error Notice -->
    <div id="error-notice" class="error-notice">
        <strong>Notice:</strong> Some resources failed to load. Functionality may be limited.
        <button onclick="this.parentElement.style.display='none'" style="float: right; background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    
    <!-- Optimized Loader -->
    <div id="loader">
        <img src="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}" 
             alt="Logo" class="spinner-icon" loading="eager">
        <div class="spinner"></div>
        <p style="margin-top: 15px; color: #666;">Loading...</p>
    </div>
    
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

    <!-- Music Modal -->
    <div class="modal fade" id="musicModal" tabindex="-1" aria-labelledby="musicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{ asset('storage/app/public/gurpreet2701.jpg') }}" alt="Image" class="img-fluid mb-3" loading="lazy">
                    <audio controls id="audioPlayer" class="d-none" preload="none">
                        <source src="{{ asset('storage/app/public/bday.mp3') }}" type="audio/mpeg" id="audioSource">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>
    </div>

    <canvas id="birthday" class="d-none"></canvas>
    
    <!-- JavaScript with Advanced Error Handling -->
    <script>
        // Global variables setup
        var toster_pos = "{{ env('SITE_RTL') == 'on' ? 'left' : 'right' }}";
        var date_picker_locale = {
            format: 'YYYY-MM-DD',
            daysOfWeek: ["{{ __('Sun') }}", "{{ __('Mon') }}", "{{ __('Tue') }}", "{{ __('Wed') }}", "{{ __('Thu') }}", "{{ __('Fri') }}", "{{ __('Sat') }}"],
            monthNames: ["{{ __('January') }}", "{{ __('February') }}", "{{ __('March') }}", "{{ __('April') }}", "{{ __('May') }}", "{{ __('June') }}", "{{ __('July') }}", "{{ __('August') }}", "{{ __('September') }}", "{{ __('October') }}", "{{ __('November') }}", "{{ __('December') }}"]
        };
        var dataTabelLang = {
            paginate: { previous: "{{ __('Previous') }}", next: "{{ __('Next') }}" },
            lengthMenu: "{{ __('Show') }} _MENU_ {{ __('entries') }}",
            zeroRecords: "{{ __('No data available in table') }}",
            info: "{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}",
            infoEmpty: " ",
            search: "{{ __('Search:') }}"
        };
        var calender_header = {
            today: "{{ __('today') }}",
            month: '{{ __('month') }}',
            week: '{{ __('week') }}',
            day: '{{ __('day') }}',
            list: '{{ __('list') }}'
        };

        // Error tracking
        window.loadingErrors = [];
        window.errorCount = 0;

        // Advanced script loader with comprehensive error handling
        function loadScriptSafely(urls, callback, errorCallback) {
            if (typeof urls === 'string') {
                urls = [urls];
            }
            
            let currentIndex = 0;
            
            function tryNext() {
                if (currentIndex >= urls.length) {
                    console.error('❌ All URLs failed for script:', urls);
                    window.loadingErrors.push(urls[0]);
                    window.errorCount++;
                    if (errorCallback) errorCallback();
                    return;
                }
                
                const script = document.createElement('script');
                script.src = urls[currentIndex];
                script.crossOrigin = 'anonymous';
                
                script.onload = function() {
                    console.log('✅ Loaded:', urls[currentIndex]);
                    if (callback) callback();
                };
                
                script.onerror = function() {
                    console.warn('❌ Failed:', urls[currentIndex]);
                    currentIndex++;
                    tryNext();
                };
                
                document.head.appendChild(script);
            }
            
            tryNext();
        }

        // Show error notice if too many failures
        function checkErrors() {
            if (window.errorCount > 2) {
                document.getElementById('error-notice').style.display = 'block';
            }
        }

        // Polyfills for missing functions
        function createPolyfills() {
            // NProgress polyfill
            if (typeof NProgress === 'undefined') {
                window.NProgress = {
                    start: function() { console.log('NProgress.start() - using polyfill'); },
                    done: function() { console.log('NProgress.done() - using polyfill'); },
                    set: function(value) { console.log('NProgress.set(' + value + ') - using polyfill'); }
                };
            }

            // Toastr polyfill
            if (typeof toastr === 'undefined') {
                window.toastr = {
                    success: function(message, title) { 
                        console.log('✅ Success:', title, message);
                        alert('Success: ' + message);
                    },
                    error: function(message, title) { 
                        console.log('❌ Error:', title, message);
                        alert('Error: ' + message);
                    },
                    warning: function(message, title) { 
                        console.log('⚠️ Warning:', title, message);
                        alert('Warning: ' + message);
                    },
                    info: function(message, title) { 
                        console.log('ℹ️ Info:', title, message);
                        alert('Info: ' + message);
                    }
                };
            }

            // show_toastr function
            if (typeof show_toastr === 'undefined') {
                window.show_toastr = function(title, message, type) {
                    if (window.toastr && window.toastr[type]) {
                        window.toastr[type](message, title);
                    } else {
                        console.log('Toast:', type, title, message);
                        alert(title + ': ' + message);
                    }
                };
            }

            // initDateRangePicker polyfill
            if (typeof initDateRangePicker === 'undefined') {
                window.initDateRangePicker = function() {
                    console.log('initDateRangePicker() - using polyfill');
                };
            }
        }
    </script>
    
    <!-- Core Dependencies with Multiple CDN Fallbacks -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
    <script>
        // Verify jQuery and create fallback if needed
        if (typeof jQuery === 'undefined') {
            console.error('❌ jQuery failed to load from CDN');
            document.write('<script src="https://code.jquery.com/jquery-3.7.1.min.js"><\/script>');
        }
    </script>

    <script>
        $(document).ready(function() {
            // Create polyfills first
            createPolyfills();
            
            console.log('🚀 Starting application initialization...');
            
            // Load Bootstrap (essential for UI)
            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.bundle.min.js',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js'
            ], function() {
                console.log('✅ Bootstrap loaded successfully');
            });

            // Load Moment.js
            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js',
                'https://unpkg.com/moment@2.29.4/moment.js'
            ], function() {
                if (typeof moment !== 'undefined') {
                    moment.locale('en');
                    console.log('✅ Moment.js configured');
                }
            });

            // Load DataTables (with dependencies)
            loadScriptSafely([
                'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js'
            ], function() {
                // Load DataTables Bootstrap integration
                loadScriptSafely([
                    'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js'
                ], function() {
                    // Load DataTables Responsive
                    loadScriptSafely([
                        'https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js'
                    ]);
                });
            });

            // Load Form Enhancement Plugins
            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js',
                'https://unpkg.com/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ]);

            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1.0/daterangepicker.min.js',
                'https://unpkg.com/daterangepicker@3.1.0/daterangepicker.js'
            ]);

            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js',
                'https://unpkg.com/jquery-validation@1.19.5/dist/jquery.validate.min.js'
            ]);

            // Load UI Enhancement Libraries
            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js',
                'https://unpkg.com/toastr@2.1.4/toastr.js'
            ], function() {
                console.log('✅ Toastr loaded');
                // Configure toastr
                if (typeof toastr !== 'undefined') {
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true,
                        "positionClass": "toast-top-" + toster_pos
                    };
                }
            });

            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js',
                'https://unpkg.com/nprogress@0.2.0/nprogress.js'
            ], function() {
                console.log('✅ NProgress loaded');
            });

            // Load Additional Plugins
            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/autosize.js/6.0.1/autosize.min.js'
            ]);

            // Load Communication Libraries
            loadScriptSafely([
                'https://js.pusher.com/8.2.0/pusher.min.js',
                'https://js.pusher.com/7.2/pusher.min.js'
            ], function() {
                console.log('✅ Pusher loaded');
            });

            loadScriptSafely([
                'https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.3/sweetalert2.all.min.js',
                'https://unpkg.com/sweetalert2@11/dist/sweetalert2.all.min.js'
            ], function() {
                console.log('✅ SweetAlert2 loaded');
            });

            // Load custom scripts with error handling
            @if(file_exists(public_path('assets/js/site.core.js')))
                $.getScript('{{ asset('assets/js/site.core.js') }}{{ safeFileVersion('assets/js/site.core.js') }}')
                    .done(function() { console.log('✅ Site core loaded'); })
                    .fail(function() { console.warn('⚠️ Site core failed to load'); });
            @endif
            
            @if(file_exists(public_path('assets/js/site.js')))
                $.getScript('{{ asset('assets/js/site.js') }}{{ safeFileVersion('assets/js/site.js') }}')
                    .done(function() { console.log('✅ Site JS loaded'); })
                    .fail(function() { console.warn('⚠️ Site JS failed to load'); });
            @endif
            
            @if(file_exists(public_path('js/custom.js')))
                $.getScript('{{ asset('js/custom.js') }}{{ safeFileVersion('js/custom.js') }}')
                    .done(function() { console.log('✅ Custom JS loaded'); })
                    .fail(function() { console.warn('⚠️ Custom JS failed to load'); });
            @endif
            
            @if(file_exists(public_path('js/jquery.form.js')))
                $.getScript('{{ asset('js/jquery.form.js') }}{{ safeFileVersion('js/jquery.form.js') }}')
                    .done(function() { console.log('✅ jQuery Form loaded'); })
                    .fail(function() { console.warn('⚠️ jQuery Form failed to load'); });
            @endif

            // Check for errors after loading
            setTimeout(checkErrors, 3000);
            
            console.log('🎉 Application initialization complete');
        });
    </script>

    <!-- Optional Scripts -->
    @if(file_exists(public_path('js/app.js')))
        <script src="{{ mix('/js/app.js') }}" defer></script>
    @endif
    
    @if(file_exists(public_path('assets/libs/apexcharts/dist/apexcharts.min.js')))
        <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}" defer></script>
    @endif
    
    @stack('theme-script')
    @include('Chatify::layouts.footerLinks')

    <!-- Enhanced Pusher Notifications -->
    @if (\Auth::user()->type != 'super admin')
        <script>
            $(document).ready(function() {
                function initializePusher() {
                    if (typeof Pusher !== 'undefined') {
                        try {
                            pushNotification('{{ Auth::id() }}');
                        } catch(e) {
                            console.warn('⚠️ Pusher initialization failed:', e);
                        }
                    } else {
                        console.warn('⚠️ Pusher not loaded, retrying...');
                        setTimeout(initializePusher, 2000);
                    }
                }
                
                setTimeout(initializePusher, 1000);
            });

            function pushNotification(id) {
                try {
                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    });

                    if (typeof Pusher === 'undefined') {
                        console.warn('Pusher not available, skipping notifications');
                        return;
                    }

                    Pusher.logToConsole = false;
                    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                        forceTLS: true
                    });

                    var channel = pusher.subscribe('send_notification');
                    channel.bind('notification', function(data) {
                        if (id == data.user_id) {
                            $(".notification-toggle").addClass('beep');
                            $(".notification-dropdown #notification-list").prepend(data.html);
                        }
                    });

                    var msgChannel = pusher.subscribe('my-channel');
                    msgChannel.bind('my-chat', function(data) {
                        if (id == data.to && typeof getChat === 'function') {
                            getChat();
                        }
                    });

                    console.log('✅ Pusher notifications initialized');
                } catch(e) {
                    console.warn('⚠️ Pusher setup failed:', e);
                }
            }

            $(document).on("click", ".mark_all_as_read_message", function() {
                $.ajax({
                    url: '{{-- route("message.seen", [], false) --}}',
                    type: "get",
                    cache: false,
                    success: function(data) {
                        $('.dropdown-list-message-msg').html('');
                        $(".message-toggle-msg").removeClass('beep');
                    },
                    error: function(xhr) {
                        console.warn('Failed to mark messages as read:', xhr);
                    }
                });
            });
        </script>
    @endif

    <!-- GDPR Cookie Notice -->
    @if (Utility::getValByName('gdpr_cookie') == 'on')
        <script>
            var defaults = {
                'messageLocales': { 'en': "{{ Utility::getValByName('cookie_text') }}" },
                'buttonLocales': { 'en': 'Ok' },
                'cookieNoticePosition': 'bottom',
                'learnMoreLinkEnabled': false,
                'expiresIn': 30,
                'buttonBgColor': '#d35400',
                'buttonTextColor': '#fff',
                'noticeBgColor': '#051c4b',
                'noticeTextColor': '#fff',
                'linkColor': '#009fdd'
            };
        </script>
        @if(file_exists(public_path('assets/js/cookie.notice.js')))
            <script src="{{ asset('assets/js/cookie.notice.js') }}" defer></script>
        @endif
    @endif

    <!-- Toast Notifications with Safe Execution -->
    @if ($message = Session::get('success'))
        <script>
            $(document).ready(function() {
                function showSuccessToast() {
                    try {
                        if (typeof show_toastr === 'function') {
                            show_toastr('Success', '{!! addslashes($message) !!}', 'success');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success('{!! addslashes($message) !!}', 'Success');
                        } else {
                            console.log('✅ Success: {!! addslashes($message) !!}');
                        }
                    } catch(e) {
                        console.log('✅ Success: {!! addslashes($message) !!}');
                    }
                }
                
                setTimeout(showSuccessToast, 2000);
            });
        </script>
    @endif
    
    @if ($message = Session::get('error'))
        <script>
            $(document).ready(function() {
                function showErrorToast() {
                    try {
                        if (typeof show_toastr === 'function') {
                            show_toastr('Error', '{!! addslashes($message) !!}', 'error');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.error('{!! addslashes($message) !!}', 'Error');
                        } else {
                            console.log('❌ Error: {!! addslashes($message) !!}');
                        }
                    } catch(e) {
                        console.log('❌ Error: {!! addslashes($message) !!}');
                    }
                }
                
                setTimeout(showErrorToast, 2000);
            });
        </script>
    @endif

    <!-- Firebase with Comprehensive Error Handling -->
    @php $fcmToken = \Auth::user()->fcm_token; @endphp
    <script type="module">
        try {
            const { initializeApp } = await import("https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js");
            const { getMessaging, getToken, onMessage } = await import("https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js");

            const firebaseConfig = {
                apiKey: "AIzaSyA433JT-E9RICNsvrqNn-8ORBL902kL2Qw",
                authDomain: "qubifyhrm.firebaseapp.com",
                projectId: "qubifyhrm",
                storageBucket: "qubifyhrm.firebasestorage.app",
                messagingSenderId: "830057480358",
                appId: "1:830057480358:web:275d7c3da0b7b6b54fb467",
                measurementId: "G-SQBR2GK49N"
            };

            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);
            const vapidKey = "BOzmoaTdY9ppxw70ELzBrmIz-eY29kzVJsoC8N4fievqThk40vOweP-IQ-Y4Zm8koN-ZjOTy85AfAOzEwgGCoJM";

            console.log("✅ Firebase initialized successfully");

        } catch(e) {
            console.warn("⚠️ Firebase unavailable:", e.message);
            // App continues working without Firebase
        }
    </script>

    <!-- Enhanced Loader with Multiple Triggers -->
    <script>
        let loaderRemoved = false;
        
        function hideLoader() {
            if (loaderRemoved) return;
            
            const preloader = document.getElementById('loader');
            if (preloader) {
                preloader.style.opacity = '0';
                setTimeout(function() {
                    if (preloader.parentNode) {
                        preloader.style.display = 'none';
                        preloader.remove();
                    }
                    loaderRemoved = true;
                    console.log('🚀 Application ready!');
                    
                    // Log any errors that occurred
                    if (window.loadingErrors.length > 0) {
                        console.warn('⚠️ Some resources failed to load:', window.loadingErrors);
                    } else {
                        console.log('✅ All resources loaded successfully');
                    }
                }, 300);
            }
        }

        // Multiple loader removal triggers
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(hideLoader, 1000);
            });
        } else {
            setTimeout(hideLoader, 1000);
        }

        window.addEventListener('load', function() {
            setTimeout(hideLoader, 1500);
        });

        // Emergency loader removal
        setTimeout(function() {
            if (!loaderRemoved) {
                console.warn('⚠️ Emergency loader removal');
                hideLoader();
            }
        }, 15000);
    </script>

    @stack('script-page')
</body>
</html>