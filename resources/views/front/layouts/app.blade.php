<!DOCTYPE html>
<html class="wide wow-animation" lang="en">

<head>
    <title>QubifyTech</title>
    <meta name=”robots” content=”noindex”>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width height=device-height initial-scale=1.0 maximum-scale=1.0 user-scalable=0, shrink-to-fit=no">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('qubifylogo/favicon-32x32.png') }}" type="image/x-icon">
    <!-- Stylesheets-->
    <link rel="stylesheet" type="text/css"
        href="//fonts.googleapis.com/css?family=Work+Sans:300,700,800%7COswald:300,400,500">
    <link rel="stylesheet" href="{{ asset('front/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
    
    <script src="{{ asset('front/js/html5shiv.min.js') }}"></script>
    @cookieconsentscripts
</head>

@include('front.layouts.body')

<div class="group-data-[sidebar-size=sm]:min-h-sm group-data-[sidebar-size=sm]:relative">
    <!-- topbar -->
    @include('front.layouts.topbar')
    <div class="relative min-h-screen group-data-[sidebar-size=sm]:min-h-sm">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <!-- content -->
            @yield('content')
        </div>
    </div>
    <!-- End Page-content -->
    <!-- footer -->
    @include('front.layouts.footer')
</div>

<!-- end main content -->
@cookieconsentview
</body>

</html>
