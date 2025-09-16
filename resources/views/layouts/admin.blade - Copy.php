@php
$logo = asset(Storage::url('uploads/logo/'));
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
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Form Plugins CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1.0/daterangepicker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ac.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}">
    @if (env('SITE_RTL') == 'on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('css-page')

    <meta name="url" content="{{ url('') . '/' . config('chatify.routes.prefix') }}"
        data-user="{{ Auth::user()->id }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/hideseek/0.8.0/jquery.hideseek.min.js"></script>
    {{-- scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/6.0.1/autosize.min.js"></script>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- styles --}}
    <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
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
    </style>
</head>

<body class="application application-offset">
    <div id="loader">
        <img src="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}" alt="Logo" class="spinner-icon">
        <div class="spinner"></div>
    </div>
    <div class="container-fluid container-application">
        @include('partial.Admin.menu')
        <div class="main-content position-relative">
            @include('partial.Admin.header')
            <div class="page-content">
                <div class="page-title">
                    <div class="row justify-content-between align-items-center">
                        <div
                            class="col-xl-3 col-lg-3 col-md-12 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                            <div class="d-inline-block">
                                <h5 class="h4 d-inline-block font-weight-400 mb-0 ">@yield('page-title')</h5>
                            </div>
                        </div>
                        <div
                            class="col-xl-9 col-lg-9 col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            @yield('action-button')
                        </div>
                    </div>
                </div>
                @yield('content')
            </div>
        </div>
    </div>

    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div>
                    <h4 class="h4 font-weight-400 float-left modal-title" id="exampleModalLabel"></h4>
                    <a href="#" class="more-text widget-text float-right close-icon" data-bs-dismiss="modal"
                        aria-label="Close">{{ __('Close') }}</a>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="musicModal" tabindex="-1" aria-labelledby="musicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-body text-center">
                    <img src="{{ asset('storage/app/public/gurpreet2701.jpg') }}" alt="Image" class="img-fluid mb-3">
                    <audio controls id="audioPlayer" class="d-none">
                        <source src="{{ asset('storage/app/public/bday.mp3') }}" type="audio/mpeg" id="audioSource">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>
    </div>

    <canvas id="birthday" class="d-none"></canvas>
    
    <!-- Bootstrap 5.3.7 and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/site.core.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/progressbar.js/1.1.0/progressbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1.0/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.3/sweetalert2.all.min.js"></script>

    @stack('theme-script')

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

    <!-- Core JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/6.0.1/autosize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hideseek/0.8.0/jquery.hideseek.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/letter-avatar/1.0.2/letter-avatar.min.js"></script>
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/site.core.js') }}"></script>
    <script src="{{ asset('assets/js/site.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/jquery.form.js') }}"></script>
    <script src="{{ mix('/js/app.js') }}"></script>
    
    @include('Chatify::layouts.footerLinks')

    @if (Utility::getValByName('gdpr_cookie') == 'on')
        <script type="text/javascript">
            var defaults = {
                'messageLocales': {
                    'en': "{{ Utility::getValByName('cookie_text') }}"
                },
                'buttonLocales': {
                    'en': 'Ok'
                },
                'cookieNoticePosition': 'bottom',
                'learnMoreLinkEnabled': false,
                'learnMoreLinkHref': '/cookie-banner-information.html',
                'learnMoreLinkText': {
                    'it': 'Saperne di più',
                    'en': 'Learn more',
                    'de': 'Mehr erfahren',
                    'fr': 'En savoir plus'
                },
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

    @if (\Auth::user()->type != 'super admin')
        <script>
            $(document).ready(function() {
                pushNotification('{{ Auth::id() }}');
            });

            function pushNotification(id) {

                // ajax setup form csrf token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Enable pusher logging - don't include this in production
                Pusher.logToConsole = false;

                var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                    forceTLS: true
                });

                // Pusher Notification
                var channel = pusher.subscribe('send_notification');
                channel.bind('notification', function(data) {
                    if (id == data.user_id) {
                        $(".notification-toggle").addClass('beep');
                        $(".notification-dropdown #notification-list").prepend(data.html);
                    }
                });

                // Pusher Message
                var msgChannel = pusher.subscribe('my-channel');
                msgChannel.bind('my-chat', function(data) {
                    // console.log(data);
                    if (id == data.to) {
                        getChat();
                    }
                });
            }

            // Get chat for top ox
            function getChat() {
                $.ajax({
                    url: '{{-- route('message.data') --}}',
                    type: "get",
                    cache: false,
                    success: function(data) {
                        // console.log(data);
                        if (data.length != 0) {
                            $(".message-toggle-msg").addClass('beep');
                            $(".dropdown-list-message-msg").html(data);
                        }
                    }
                })
            }

            // getChat();

            $(document).on("click", ".mark_all_as_read_message", function() {
                $.ajax({
                    url: '{{-- route('message.seen') --}}',
                    type: "get",
                    cache: false,
                    success: function(data) {
                        $('.dropdown-list-message-msg').html('');
                        $(".message-toggle-msg").removeClass('beep');
                    }
                })
            });
        </script>
    @endif

    @stack('script-page')

    <script>
        var date_picker_locale = {
            format: 'YYYY-MM-DD',
            daysOfWeek: [
                "{{ __('Sun') }}",
                "{{ __('Mon') }}",
                "{{ __('Tue') }}",
                "{{ __('Wed') }}",
                "{{ __('Thu') }}",
                "{{ __('Fri') }}",
                "{{ __('Sat') }}"
            ],
            monthNames: [
                "{{ __('January') }}",
                "{{ __('February') }}",
                "{{ __('March') }}",
                "{{ __('April') }}",
                "{{ __('May') }}",
                "{{ __('June') }}",
                "{{ __('July') }}",
                "{{ __('August') }}",
                "{{ __('September') }}",
                "{{ __('October') }}",
                "{{ __('November') }}",
                "{{ __('December') }}"
            ],
        };
        var dataTabelLang = {
            paginate: {
                previous: "{{ __('Previous') }}",
                next: "{{ __('Next') }}"
            },
            lengthMenu: "{{ __('Show') }} _MENU_ {{ __('entries') }}",
            zeroRecords: "{{ __('No data available in table') }}",
            info: "{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}",
            infoEmpty: " ",
            search: "{{ __('Search:') }}"
        }
        
    </script>
    <script>
        var calender_header = {
            today: "{{ __('today') }}",
            month: '{{ __('month') }}',
            week: '{{ __('week') }}',
            day: '{{ __('day') }}',
            list: '{{ __('list') }}'
        };
    </script>
    @if ($message = Session::get('success'))
        <script>
            show_toastr('Success', '{!! $message !!}', 'success');
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            show_toastr('Error', '{!! $message !!}', 'error');
        </script>
    @endif

    <?php
        $fcmToken = \Auth::user()->fcm_token;
    ?>

    <script src="{{ mix('/js/app.js') }}"></script>
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.17.1/firebase-app.js";
        import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.17.1/firebase-messaging.js";

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

        async function requestNotificationPermission() {
            const permission = await Notification.requestPermission();
            if (permission === "granted") {
                console.log("Notification permission granted.");
                const token = await getToken(messaging, { vapidKey: "BOzmoaTdY9ppxw70ELzBrmIz-eY29kzVJsoC8N4fievqThk40vOweP-IQ-Y4Zm8koN-ZjOTy85AfAOzEwgGCoJM" });
            } else {
                console.error("Notification permission denied.");
            }
        }

        onMessage(messaging, (payload) => {
            console.log("Message received in foreground:", payload);
            new Notification(payload.notification.title, {
                body: payload.notification.body,
            });
        });

        requestNotificationPermission();

        function sendTokenToServer(fcmToken) {
            $.ajax({
                url: "{{ route('update.fcm.token') }}",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: { fcm_token: fcmToken },
                success: function (response) {
                    console.log("Token saved successfully:", response.message);
                },
                error: function (xhr) {
                    console.error("Failed to save token:", xhr.responseJSON?.error || xhr.statusText);
                },
            });
        }

        var $fcmToken = '<?php echo $fcmToken; ?>';
        // Example usage: Call the function with the FCM token
        
        (async function () {
            const fcmToken = await getToken(messaging, {
                vapidKey: "BOzmoaTdY9ppxw70ELzBrmIz-eY29kzVJsoC8N4fievqThk40vOweP-IQ-Y4Zm8koN-ZjOTy85AfAOzEwgGCoJM",
            });
            if (!$fcmToken) {
                sendTokenToServer(fcmToken);
            }
        })();

        /*if(!$fcmToken || $fcmToken == NULL){
            const fcmToken = await getToken(messaging, {
                vapidKey: "BOzmoaTdY9ppxw70ELzBrmIz-eY29kzVJsoC8N4fievqThk40vOweP-IQ-Y4Zm8koN-ZjOTy85AfAOzEwgGCoJM",
            });
            // Replace this with the actual token
            sendTokenToServer(fcmToken);
        }*/

    </script>
     <script>
  window.addEventListener('load', function() {
    // Show preloader for 3 seconds
    const preloader = document.getElementById('loader');
    if (preloader) {
        setTimeout(function() {
            preloader.style.display = 'none';
        }, 1000); // 3000 milliseconds = 3 seconds
    }
});

    </script>
</body>

</html>
