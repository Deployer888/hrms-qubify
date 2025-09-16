    </section>
</div>
<!-- Page Footer-->
<footer class="section footer-linked bg-gray-700">
    <div class="footer-linked-main">
        <div class="container">
            <div class="row row-50">

                <div class="col-lg-3">
                    <img src="{{asset('front/images/qubifylogo.png')}}">
                    <p class="justify-content text-justify" style="font-size: small;"> Transforming your vision into reality, our software development company crafts innovative, scalable solutions tailored to your needs. Experience seamless integration, exceptional quality, and cutting-edge technology for your business success. </p>
                </div>

                <div class="col-lg-5">
                    <h4>Quick Links</h4>
                    <hr class="offset-right-1">
                    <div class="row row-20">
                        <div class="col-6 col-sm-3">
                            <ul class="list list-xs">
                                <li><a href="{{route('/')}}#services">Services</a></li>
                                <!-- <li><a href="#">Pricing</a></li> -->
                                <li><a href="#">Contacts</a></li>
                                <li><a href="{{route('/')}}#testimonials">Testimonials</a></li>
                                <li><a href="#">Partners</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-3">
                            <ul class="list list-xs">
                                <!-- <li><a href="#">Blog</a></li> -->
                                <li><a href="{{route('/')}}#about-us">About Us</a></li>
                                <li><a href="#">Get a Quote</a></li>
                                <li><a href="{{ route('login') }}">Login</a></li>
                                <li><a href="{{ route('register') }}">Registration</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-3">
                            <ul class="list list-xs">
                                <li><a href="#">Careers</a></li>
                                <li><a href="#">Portfolio</a></li>
                                <li><a href="#">Our Story</a></li>
                                <!-- <li><a href="#">Our History</a></li>    -->
                                <li><a href="#">Awards</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-lg-4">
                    <h4>Contact Information</h4>
                    <hr>
                    <ul class="list-sm">
                        <li class="object-inline"><span
                                class="icon icon-md mdi mdi-map-marker text-gray-700"></span><a class="link-default"
                                href="#">Peer Muchalla Rd, Sector 20, Sanauli, <br> Punjab - 160104 INDIA</a></li>
                        <li class="object-inline"><span class="icon icon-md mdi mdi-phone text-gray-700"></span>
                            <ul class="list-0" style="margin-left:6px!important">
                                <a class="link-default" href="tel:#">+91 7087076111</a>
                            </ul>
                        </li>
                        <li class="object-inline"><span class="icon icon-md mdi mdi-email text-gray-700"></span><a
                                class="link-default" href="mailto:#">sales[at]qubifytech.com</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<footer
    class="ltr:md:left-vertical-menu rtl:md:right-vertical-menu group-data-[sidebar-size=md]:ltr:md:left-vertical-menu-md group-data-[sidebar-size=md]:rtl:md:right-vertical-menu-md group-data-[sidebar-size=sm]:ltr:md:left-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:md:right-vertical-menu-sm absolute right-0 bottom-0 px-4 h-14 group-data-[layout=horizontal]:ltr:left-0  group-data-[layout=horizontal]:rtl:right-0 left-0 border-t py-3 flex items-center dark:border-zink-600">
    <div class="group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl w-full">
        <div
            class="grid items-center grid-cols-1 text-center lg:grid-cols-2 text-slate-400 dark:text-zink-200 ltr:lg:text-left rtl:lg:text-right">
            <div>
                {{ date('Y') }} © Qubify Tech. All Rights Reserved.
            </div>
        </div>
    </div>
</footer>
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('front/js/core.min.js') }}"></script>


<script src="{{ asset('dist/js/toastr.min.js') }}"></script>

<script src="{{ asset('front/js/script.js') }}"></script>
<script src="{{ asset('dist/js/jquery.validate.min.js') }}"></script>
<script src="//cdn.jsdelivr.net/gh/freeps2/a7rarpress@main/swiper-bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/gh/freeps2/a7rarpress@main/script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
<script src="{{ asset('front/js/custom.js') }}"></script>
