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
    
    <!-- RTL Support -->
    @if (env('SITE_RTL') == 'on' && file_exists(public_path('css/bootstrap-rtl.css')))
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}?v={{ filemtime(public_path('css/bootstrap-rtl.css')) }}">
    @endif
    
    <!-- Custom Overrides -->
    @if(file_exists(public_path('css/custom.css')))
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}">
    @endif
    
    @stack('css-page')

    <style>
        .page-title { display: none!important; }
        #loader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.95); z-index: 9999;
            display: flex; align-items: center; justify-content: center; flex-direction: column;
        }
        .spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>

<body class="application application-offset">
    <div id="loader">
        <img src="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}" alt="Logo" style="width: 60px; height: 60px; margin-bottom: 20px;">
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

    <!-- Core JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    
    <!-- Essential Plugins -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" crossorigin="anonymous"></script>
    
    <!-- Custom Scripts -->
    @if(file_exists(public_path('js/custom.js')))
        <script src="{{ asset('js/custom.js') }}?v={{ filemtime(public_path('js/custom.js')) }}"></script>
    @endif
    
    @if(file_exists(public_path('assets/js/site.js')))
        <script src="{{ asset('assets/js/site.js') }}?v={{ filemtime(public_path('assets/js/site.js')) }}"></script>
    @endif

    <!-- Laravel Mix Assets -->
    @if(file_exists(public_path('js/app.js')))
        <script src="{{ mix('js/app.js') }}" defer></script>
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