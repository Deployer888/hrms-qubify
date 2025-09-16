<?php

    $navbarmenu = \Helper::navbarMenu();

    $technologies = isset($navbarmenu['technologies']) ? $navbarmenu['technologies'] : '';
    $services = isset($navbarmenu['services']) ? $navbarmenu['services'] : '';
    $industries = isset($navbarmenu['industries']) ? $navbarmenu['industries'] : '';
    $solutions = isset($navbarmenu['solutions']) ? $navbarmenu['solutions'] : '';

?>

<style>
    .social-navbar a:hover{
        background-color: #262626;
        border-bottom: 0.5px solid rgb(0, 247, 255);
    }
    .nav-item {
        padding: 0.5rem;
    }

    .dropdown-hover:hover>.dropdown-menu {
        display: inline-block;
    }

    .dropdown-hover>.dropdown-toggle:active {
        pointer-events: none;
    }

    /* Style the tab */
    .tab {
        float: left;
        width: 100%;
        height: 300px;
    }

    /* Style the buttons inside the tab */
    .tab button {
        display: block;
        background-color: inherit;
        color: black;
        padding: 5px;
        width: 100%;
        border: none;
        outline: none;
        text-align: left;
        cursor: pointer;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current "tab button" class */
    .tab button.active {
        background-color: #ccc;
        font-weight: bold;
        scale: 1.1;
    }

    /* Style the tab content */
    .tabcontent {
        float: left;
        padding: 0px 12px;
        width: 100%;
        border-left: none;
        height: 300px;
        display: none;
    }

    /* Clear floats after the tab */
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    .dropdown-menu.full-width {
        position: absolute;
        width: calc(100vw - 20px);
        max-width: 1500px;
        left: 50%;
        transform: translateX(-58.4%);
        top: 100%;
        padding: 0px;
        height: 600px;
    }

    .menu, .sub-menu{
        overflow-y: auto;
        height: 500px;
    }

    @media (max-width: 767px) {
        .dropdown-menu.full-width {
            position: static;
            width: auto;
            padding: 10px;
            max-width: none;
            display: none!important;
        }
        
        .rd-navbar-nav li.active a {
            color: #ffffff !important;
        }
        
        .nav-link {
            padding-left: 1.1rem!important;
            font-size: 17px;
        }
    }
    
    @media (min-width: 1200px)
        .container {
            max-width: 1400px;
        }
    }

    .menu {
        text-align: left;
    }

    .tablinks {
        display: block;
        width: 100%;
        text-align: left;
    }
    
    /*header ul li a{*/
    /*    color: #fff!important;*/
    /*}*/
    
    li.nav-item{
        min-height: 57px;
    }
    
    li.nav-item a{
        padding-top: 18px !important;
        font-weight: 100 !important;
    }
    
    li.rd-nav-item a{
        font-weight: 100 !important;
    }
    
    li.active a{
        color: #007fba !important;
        font-weight: 600 !important;
    }
    
    ul.rd-navbar-nav li{
        font-size: 15px !important;
    }
    
    .rd-navbar-static .rd-nav-link .btn a.rd-nav-link{
        color: #fff!important;
    }

</style>
<div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-ebde2e0"
            data-id="ebde2e0" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <section
                    class="elementor-section elementor-inner-section elementor-element elementor-element-adb7d1b elementor-section-boxed elementor-section-height-default elementor-section-height-default"
                    data-id="adb7d1b" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-f93ca2b"
                            data-id="f93ca2b" data-element_type="column">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-12b1394 elementor-align-center elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                                    data-id="12b1394" data-element_type="widget"
                                    data-widget_type="icon-list.default">
                                    <div class="elementor-widget-container">
                                        <ul class="elementor-icon-list-items">
                                            <li class="elementor-icon-list-item">
                                                <span class="elementor-icon-list-icon">
                                                    <i aria-hidden="true"
                                                        class="fas fa-map-marker-alt"></i> </span>
                                                <span class="elementor-icon-list-text">Tricity Plaza Panchkula</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-7f8e87e"
            data-id="7f8e87e" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-9c194bd elementor-align-left elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                    data-id="9c194bd" data-element_type="widget" data-widget_type="icon-list.default">
                    <div class="elementor-widget-container">
                        <ul class="elementor-icon-list-items">
                            <li class="elementor-icon-list-item">
                                <span class="elementor-icon-list-icon">
                                    <i aria-hidden="true" class="far fa-envelope"></i> </span>
                                <span class="elementor-icon-list-text">sales[at]qubifytech.com</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-3bc9235"
            data-id="3bc9235" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-f483489 elementor-align-center elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                    data-id="f483489" data-element_type="widget" data-widget_type="icon-list.default">
                    <div class="elementor-widget-container">
                        <ul class="elementor-icon-list-items">
                            <li class="elementor-icon-list-item">
                                <span class="elementor-icon-list-icon">
                                    <i aria-hidden="true" class="fas fa-phone-volume"></i> </span>
                                <span class="elementor-icon-list-text">India +91 7087076111</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-3bc9235"
            data-id="3bc9235" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-f483489 elementor-align-center elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                    data-id="f483489" data-element_type="widget" data-widget_type="icon-list.default">
                    <nav class="social-navbar">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-instagram"></i></a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
<header class="section page-header" data-type="anchor">
    <!-- RD Navbar-->
    <div class="rd-navbar-wrap" style="height:85px">
    
        <nav class="rd-navbar rd-navbar-minimal" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed"
            data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-static"
            data-lg-device-layout="rd-navbar-fixed" data-xl-layout="rd-navbar-static"
            data-xl-device-layout="rd-navbar-fixed" data-lg-stick-up-offset="46px" data-xl-stick-up-offset="46px"
            data-xxl-stick-up-offset="46px" data-lg-stick-up="true" data-xl-stick-up="true" data-xxl-stick-up="true">
            
            <div class="rd-navbar-main-outer">          
                    
                <div class="rd-navbar-main row">
                    <!-- RD Navbar Panel-->
                    <div class="rd-navbar-panel col-md-2 col-lg-2 col-md-12 col-sm-12">
                        <!-- RD Navbar Brand-->
                        <a class="rd-navbar-brand col-md-11 col-sm-11 ml-lg-0 ml-md-0 ml-sm-0 pl-lg-0 pl-md-0 pl-sm-0" href="{{ route('/') }}">
                            <img src="{{ asset('images/qubifylogo/qubifylogo.png') }}" alt="" style="max-height:100px">
                        </a>
                        <!-- RD Navbar Toggle-->
                        <button class="rd-navbar-toggle col-md-1 col-sm-1" data-rd-navbar-toggle="#rd-navbar-nav-wrap-1"><span></span></button>
                    </div>
                    <div class="rd-navbar-main-element col-md-10">
                        <div class="rd-navbar-nav-wrap" id="rd-navbar-nav-wrap-1">
                            <!-- RD Navbar Nav-->
                            <ul class="rd-navbar-nav ml-auto">
                                <li class="rd-nav-item active"><a class="rd-nav-link" href="{{route('/')}}#home">Home</a></li>

                                <li class="p-0 rd-nav-item nav-item dropdown dropdown-hover position-static" style="height: var(--navbar-height);">
                                    <a data-mdb-dropdown-init class="nav-link p-0 d-flex ml-lg-0 ml-md-3 ml-xs-3" href="{{ route('technology.info') }}" id="navbarDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                                        Technology <i class="fa fa-caret-down d-lg-block pl-lg-1 d-md-none d-xs-none" style="font-style: normal;" aria-hidden="true"></i>
                                    </a>
                                    <!-- Dropdown menu -->
                                    <div class="dropdown-menu mt-0 full-width" aria-labelledby="navbarDropdown">
                                        <div class="pl-5">
                                            <div class="row my-4">
                                                <div class="col-md-3 col-lg-3 mb-3 mb-lg-0 border-right">
                                                    <div class="tab">
                                                        @foreach($technologies as $key => $tech)
                                                            <button class="tablinks"
                                                                onmouseover="openCity(event, '{{ str_replace(' ', '', $tech->name) }}')">{{ $tech->name }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="col-md-9 col-lg-9 mb-3 mb-lg-0">
                                                    <div class="row col-12">
                                                        @foreach($technologies as $key => $tech)
                                                            <div id="{{ str_replace(' ', '', $tech->name) }}" class="tabcontent">
                                                                @foreach(array_chunk($tech->technology->toArray(), 3) as $chunk)
                                                                    <div class="row">
                                                                        @foreach($chunk as $technology)
                                                                            <div class="col-md-4 mb-4 pr-0">
                                                                                <a href="#">
                                                                                    <div class="icon m-0 d-flex">
                                                                                        <img width="32" height="32" src="{{ asset($technology['icon']) }}" alt="{{ $technology['slug'] }}"
                                                                                            data-lazy-src="{{ asset($technology['icon']) }}">
                                                                                        <div class="title pl-2">
                                                                                            <b>{{ $technology['name'] }}</b>
                                                                                            <p><span class="desc" style="font-size:12px">{!! \Str::limit($technology['description'], 50, $end='...') !!}</span></p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="p-0 rd-nav-item nav-item dropdown dropdown-hover position-static">
                                    <a data-mdb-dropdown-init class="nav-link p-0 d-flex ml-lg-0 ml-md-3 ml-xs-3" href="{{ route('industry.info') }}"
                                        id="navbarDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                                        Industries <i class="fa fa-caret-down d-lg-block pl-lg-1 d-md-none d-xs-none" style="font-style: normal;" aria-hidden="true"></i>
                                    </a>
                                    <!-- Dropdown menu -->
                                    <div class="dropdown-menu mt-0 full-width" aria-labelledby="navbarDropdown">
                                        <div class="pl-5">
                                            <div class="row my-4">
                                                <div class="col-md-3 col-lg-3 mb-3 mb-lg-0 menu border-right">
                                                    <div class="tab">
                                                        @foreach($industries as $key => $indus)
                                                            <button class="tablinks"
                                                                onmouseover="openCity(event, '{{ str_replace(' ', '', $indus->name) }}')">{{ $indus->name }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="col-md-9 col-lg-9 mb-3 mb-lg-0 sub-menu">
                                                    <div class="row col-12">
                                                        @foreach($industries as $key => $ind)
                                                            <div id="{{ str_replace(' ', '', $ind->name) }}" class="tabcontent">
                                                                @foreach(array_chunk($ind->industry->toArray(), 3) as $chunk)
                                                                    <div class="row">
                                                                        @foreach($chunk as $industry)
                                                                            <div class="col-md-4 mb-4 pr-0">
                                                                                <a href="#">
                                                                                    <div class="icon m-0 d-flex">
                                                                                        <img width="32" height="32" src="{{ asset($industry['icon']) }}" alt="{{ $industry['slug'] }}"
                                                                                            data-lazy-src="{{ asset($industry['icon']) }}">
                                                                                        <div class="title pl-2">
                                                                                            <b>{{ $industry['name'] }}</b>
                                                                                            <p><span class="desc" style="font-size:12px">{!! \Str::limit($industry['description'], 50, $end='...') !!}</span></p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                
                                <li class="p-0 rd-nav-item nav-item dropdown dropdown-hover position-static">
                                    <a data-mdb-dropdown-init class="nav-link p-0 d-flex ml-lg-0 py-lg-0 py-md-3 py-xs-3 ml-md-3 ml-xs-3" href="{{ route('service.info') }}"
                                        id="navbarDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                                        Services <i class="fa fa-caret-down d-lg-block pl-lg-1 d-md-none d-xs-none" style="font-style: normal;" aria-hidden="true"></i>
                                    </a>
                                    <!-- Dropdown menu -->
                                    <div class="dropdown-menu mt-0 full-width" aria-labelledby="navbarDropdown">
                                        <div class="pl-5">
                                            <div class="row my-4">
                                                <div class="col-md-3 col-lg-3 mb-3 mb-lg-0 menu border-right">
                                                    <div class="tab">
                                                        @foreach($services as $key => $serv)
                                                            <button class="tablinks"
                                                                onmouseover="openCity(event, '{{ str_replace(' ', '', $serv->name) }}')">{{ $serv->name }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="col-md-9 col-lg-9 mb-3 mb-lg-0 sub-menu">
                                                    <div class="row col-12">
                                                        @foreach($services as $key => $ser)
                                                            <div id="{{ str_replace(' ', '', $ser->name) }}" class="tabcontent">
                                                                @foreach(array_chunk($ser->service->toArray(), 3) as $chunk)
                                                                    <div class="row">
                                                                        @foreach($chunk as $service)
                                                                            <div class="col-md-4 mb-4 pr-0">
                                                                                <a href="#">
                                                                                    <div class="icon m-0 d-flex">
                                                                                        <img width="32" height="32" src="{{ asset($service['icon']) }}" alt="{{ $service['slug'] }}"
                                                                                            data-lazy-src="{{ asset($service['icon']) }}">
                                                                                        <div class="title pl-2">
                                                                                            <b>{{ $service['name'] }}</b>
                                                                                            <p><span class="desc" style="font-size:12px">{!! \Str::limit($service['description'], 50, $end='...') !!}</span></p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="rd-nav-item"><a class="rd-nav-link" href="{{route('/')}}#about-us">About Us</a></li>
                                <li class="rd-nav-item"><a class="rd-nav-link" href="{{route('/')}}#our-team">Our Team</a></li>
                                <li class="rd-nav-item"><a class="rd-nav-link" href="{{route('/')}}#testimonials">Testimonials</a></li>
                                <li class="rd-nav-item mr-3"><a class="rd-nav-link" href="{{route('/')}}#blog">Blog</a></li>
                                <a class="btn btn-primary" href="{{route('/')}}#contact-us">Contact Us</a>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>


<script>
    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.classList.add("active");
    }
</script>