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
    <link rel="icon" href="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.ico') }}"
        type="image" sizes="16x16">

    @stack('head')
    
    <!-- Preconnect for faster loading -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.datatables.net">
    <link rel="preconnect" href="https://unpkg.com">
    
    <!-- Core CSS - Load in order of importance -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    
    <!-- DataTables CSS - Bootstrap 5 compatible -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <!-- Form Plugins CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1.0/daterangepicker.css" crossorigin="anonymous">
    
    <!-- Notification & UI CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" crossorigin="anonymous">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ac.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}">
    @if (env('SITE_RTL') == 'on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('css-page')

    <!-- Critical inline styles -->
    <style>
        .page-title{
            display: none!important;
        }
        /* Loader optimization */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .spinner-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
            border-radius: 50%;
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
        
        /* Performance optimizations */
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
        
        /* Prevent layout shifts */
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body class="application application-offset">
    <!-- Optimized Loader -->
    <div id="loader">
        <img src="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}" 
             alt="Logo" class="spinner-icon" loading="eager">
        <div class="spinner"></div>
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
                                <h5 class="h4 d-inline-block font-weight-400 mb-0 ">@yield('page-title')</h5>
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
    
    <!-- Core JavaScript - Load in order -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
    <script>
        // Set global variables early
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
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/site.core.js') }}"></script>
    <script src="{{ asset('assets/libs/progressbar.js/dist/progressbar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>-->
 
    @stack('theme-script')
 
    <script>
        $(document).ready(function() {
            $('[data-dismiss="modal"]').on('click', function() {
                $(this).closest('.modal').modal('hide');
            });
        });
    </script>
 
    <script src="{{ asset('assets/js/site.js') }}"></script>
    <script src="{{ asset('js/jquery.form.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script>
        var toster_pos = "{{ env('SITE_RTL') == 'on' ? 'left' : 'right' }}";
    </script>
 
    <script src="{{ asset('js/letter.avatar.js') }}"></script>
    <script>
        LetterAvatar.transform();
    </script>
 
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        moment.locale('en');
    </script>
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    <!--<script src="{{ asset('js/leave_custom.js') }}"></script>-->
 
    @include('Chatify::layouts.footerLinks')

    <!-- Pusher Notifications -->
    @if (\Auth::user()->type != 'super admin')
        <script>
            $(document).ready(function() {
                if (typeof pushNotification === 'function') {
                    pushNotification('{{ Auth::id() }}');
                }
            });

            function pushNotification(id) {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                });

                try {
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
                } catch(e) {
                    console.warn('Pusher initialization failed:', e);
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
        <script src="{{ asset('assets/js/cookie.notice.js') }}"></script>
    @endif

    <!-- Toast Notifications -->
    @if ($message = Session::get('success'))
        <script>
            $(document).ready(function() {
                if (typeof show_toastr === 'function') {
                    show_toastr('Success', '{!! addslashes($message) !!}', 'success');
                }
            });
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            $(document).ready(function() {
                if (typeof show_toastr === 'function') {
                    show_toastr('Error', '{!! addslashes($message) !!}', 'error');
                }
            });
        </script>
    @endif

    <!-- Firebase & FCM -->
    @php $fcmToken = \Auth::user()->fcm_token; @endphp
    <script type="module">
        try {
            const { initializeApp } = await import("https://www.gstatic.com/firebasejs/9.17.1/firebase-app.js");
            const { getMessaging, getToken, onMessage } = await import("https://www.gstatic.com/firebasejs/9.17.1/firebase-messaging.js");

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

            async function requestNotificationPermission() {
                try {
                    const permission = await Notification.requestPermission();
                    if (permission === "granted") {
                        console.log("Notification permission granted.");
                        await getToken(messaging, { vapidKey });
                    }
                } catch(e) {
                    console.warn("Notification setup failed:", e);
                }
            }

            onMessage(messaging, (payload) => {
                console.log("Message received:", payload);
                if (Notification.permission === "granted") {
                    new Notification(payload.notification.title, {
                        body: payload.notification.body,
                    });
                }
            });

            function sendTokenToServer(fcmToken) {
                $.ajax({
                    url: "{{ route('update.fcm.token') }}",
                    type: "POST",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    data: { fcm_token: fcmToken },
                    success: function (response) {
                        console.log("Token saved:", response.message);
                    },
                    error: function (xhr) {
                        console.warn("Token save failed:", xhr.responseJSON?.error);
                    }
                });
            }

            const existingToken = '{{ $fcmToken }}';
            if (!existingToken) {
                try {
                    const fcmToken = await getToken(messaging, { vapidKey });
                    if (fcmToken) {
                        sendTokenToServer(fcmToken);
                    }
                } catch(e) {
                    console.warn("FCM token generation failed:", e);
                }
            }

            requestNotificationPermission();
        } catch(e) {
            console.warn("Firebase initialization failed:", e);
        }
    </script>

    <!-- Optimized Loader -->
    <script>
        window.addEventListener('load', function() {
            const preloader = document.getElementById('loader');
            if (preloader) {
                // Fade out effect
                preloader.style.opacity = '0';
                preloader.style.transition = 'opacity 0.3s ease';
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 300);
            }
        });
    </script>

    @stack('script-page')
</body>
</html>