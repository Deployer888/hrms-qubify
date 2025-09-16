@extends('front.layouts.app')
@section('content')

<style>
    .container-fluid.group-data-[content=boxed]:max-w-boxed.mx-auto {
        margin: 0px !important;
        padding: 0px !important;
    }
</style>


<div class="swiper-container swiper-slider swiper-slider-minimal" id="home" data-loop="true" data-slide-effect="fade"
    data-autoplay="3500" data-simulate-touch="true">
    <div class="swiper-wrapper">
        @foreach($sliders as $slider)
            <div class="swiper-slide" data-slide-bg="{{ asset($slider->image) }}">
                <div class="swiper-slide-caption">
                    <div class="container">
                        <div class="swiper-slide-text">
                            <div class="text-large text-white">{{ $slider->heading }}</div>
                            <h5 class="text-white fw-bold">{{ $slider->sub_heading }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="swiper-pagination-outer container">
        <div class="swiper-pagination swiper-pagination-modern swiper-pagination-marked" data-index-bullet="true"></div>
    </div>
</div>
<!-- Our Services-->
<section class="section section-lg text-center" id="services" data-type="anchor">
    <div class="container">
        <h3 class="wow-outer"><span class="wow slideInUp">Our Services</span></h3>
        <p class="wow-outer"><span class="text-width-1 wow slideInDown">We provide a variety of marketing and promotion
                services to enable you and your business with innovative tools and strategies and attract more
                customers.</span></p>
        <div class="row row-50 row-xxl-70 offset-top-2">
            @foreach($servicelist as $service)
                <div class="col-sm-6 col-md-4 col-lg-3 wow-outer">
                    <!-- Box Light-->
                    <article class="box-light wow slideInLeft">
                        <div class="box-light-icon"><img src="{{ asset($service->icon) }}"
                                alt="{{ $service->name }}" height="50px" width="50px"></div>
                        <h4 class="box-light-title">{{ $service->name }}</h4>
                        <p>{!! $service->description !!}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Slider Portfolio Section  -->

<section class="section section-lg bg-gray-100" id="Swipe-Content" data-type="anchor">
    <div class="container">
        <div class="row justify-content-center">
            <h3 class="wow-outer text-center"><span class="wow slideInUp">Our Work</span></h3>
            <div class="slide-container swiper" id="Work-content">
                <div class="swiper-button-prev swiper-navBtn"
                    style="position: relative; color:black; left: -50px!important;"></div>
                <div class="slide-content">
                    <div class="card-wrapper swiper-wrapper" id="swiper-wrapper">

                        <div class="card swiper-slide" id="swipe-slider">
                            <div class="image-content">
                                <span class="overlay"></span>

                                <div class="card-image">
                                    <a class="thumbnail-thin wow slideInLeft"
                                        href="{{ asset('front/images/support-system.png') }}"
                                        data-lightgallery="item">
                                        <div class="thumbnail-thin-inner"><img class="thumbnail-thin-image"
                                                src="{{ asset('front/images/support-system.png') }}"
                                                alt="" width="640" height="640" />
                                        </div>

                                    </a>
                                </div>
                            </div>

                            <div class="card-content">
                                <button class="button">View More</button>
                            </div>
                        </div>
                        <div class="card swiper-slide" id="swipe-slider">
                            <div class="image-content">
                                <span class="overlay"></span>

                                <div class="card-image">
                                    <a class="thumbnail-thin wow slideInLeft"
                                        href="{{ asset('front/images/crm-portal.png') }}"
                                        data-lightgallery="item">
                                        <div class="thumbnail-thin-inner"><img class="thumbnail-thin-image"
                                                src="{{ asset('front/images/crm-portal.png') }}"
                                                alt="" width="640" height="640" />
                                        </div>

                                    </a>
                                </div>
                            </div>

                            <div class="card-content">
                                <button class="button">View More</button>
                            </div>
                        </div>
                        <div class="card swiper-slide" id="swipe-slider">
                            <div class="image-content">
                                <span class="overlay"></span>

                                <div class="card-image">
                                    <a class="thumbnail-thin wow slideInLeft"
                                        href="{{ asset('front/images/Fitsigma.png') }}"
                                        data-lightgallery="item">
                                        <div class="thumbnail-thin-inner"><img class="thumbnail-thin-image"
                                                src="{{ asset('front/images/Fitsigma.png') }}"
                                                alt="" width="640" height="640" />
                                        </div>

                                    </a>
                                </div>
                            </div>

                            <div class="card-content">
                                <button class="button">View More</button>
                            </div>
                        </div>
                        <div class="card swiper-slide" id="swipe-slider">
                            <div class="image-content">
                                <span class="overlay"></span>

                                <div class="card-image">
                                    <a class="thumbnail-thin wow slideInLeft"
                                        href="{{ asset('front/images/work-management-system.png') }}"
                                        data-lightgallery="item">
                                        <div class="thumbnail-thin-inner"><img class="thumbnail-thin-image"
                                                src="{{ asset('front/images/work-management-system.png') }}"
                                                alt="" width="640" height="640" />
                                        </div>

                                    </a>
                                </div>
                            </div>

                            <div class="card-content">
                                <button class="button">View More</button>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="swiper-button-next swiper-navBtn"
                    style="position: relative; color:black; top:-198px; left:1130px;"></div>
                <div class="swipe-pagination justify-content-center"></div>
            </div>
        </div>
    </div>
</section>

<!-- slider  end  -->

<!-- A Few Words About Us-->
<section class="section section-lg bg-gray-100" id="about-us" data-type="anchor">
    <div class="container">
        <div class="row row-50 justify-content-center justify-content-lg-between">
            <div class="col-md-10 col-lg-6 col-xl-5">
                <h3 class="wow-outer"><span class="wow slideInDown">A Few Words About Us</span></h3>
                <p class="wow-outer"><span class="wow slideInDown" data-wow-delay=".05s">We are a team of talented
                        marketers who love creating smart ideas for clients that appreciate uniqueness. We use our
                        creative potential to provide the best ideas.</span></p>
                <p class="wow-outer"><span class="wow slideInDown" data-wow-delay=".1s">We have a wide range of
                        experience, expertise and tools to create and implement your campaigns, from carefully curating
                        awesome content to optimising it with our great SEO powers as well as outdoor marketing
                        skills.</span></p>

            </div>
            <div class="col-md-10 col-lg-6 wow-outer"><img class="img-responsive wow slideInRight"
                    src="{{ asset('front/images/large-features-2-570x368') }}.jpg" alt=""
                    width="570" height="368" />
            </div>
        </div>
    </div>
</section>
<!-- CTA Thin-->
<h3 class="text-center my-5">Trusted By</h3>
<section class="section section-xs bg-primary-gradient text-center">
    <div class="container">
        <div class="row">
            <div class="logos">
                <div class="logo-slider" id="new-class-name">
                    @if(count($logos) > 0)
                        @foreach($logos as $logo)
                            <img src="{{ asset($logo->image) }}" alt="{{ $logo->name }}" height='70px' width='70px'>
                        @endforeach
                    @endif
                </div>
                <div class="logo-slider">
                    @if(count($logos) > 0)
                        @foreach($logos as $logo)
                            <img src="{{ asset($logo->image) }}" alt="{{ $logo->name }}" height='70px' width='70px'>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- position: relative;
    left: 610px; -->
<!-- Roadmap Process  -->
<!--<h3 class="text-center my-5">Our Approach. Simple Yet Disruptive.</h3>-->
<!--<section class="roadmapsection">-->
<!--    <div class="timeline">-->
<!--        <div class="container col-md-10 col-12 col-lg-10 col-xl-10">-->
<!--            <ol class="col-md-10 col-12 col-lg-10">-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Requirement Gathering</time> At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium-->
<!--                        At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Kicking Things off</time> Proin quam velit, efficitur vel neque vitae, rhoncus commodo mi. Suspendisse finibus-->
<!--                        mauris et bibendum molestie. Aenean ex augue, varius et pulvinar in, pretium non nisi.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Technical Architecture</time> Proin iaculis, nibh eget efficitur varius, libero tellus porta dolor, at pulvinar-->
<!--                        tortor ex eget ligula. Integer eu dapibus arcu, sit amet sollicitudin eros.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Database Design</time> In mattis elit vitae odio posuere, nec maximus massa varius. Suspendisse varius-->
<!--                        volutpat mattis. Vestibulum id magna est.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Design & Analysis</time> In mattis elit vitae odio posuere, nec maximus massa varius. Suspendisse varius-->
<!--                        volutpat mattis. Vestibulum id magna est.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Code Development</time> In mattis elit vitae odio posuere, nec maximus massa varius. Suspendisse varius-->
<!--                        volutpat mattis. Vestibulum id magna est.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Merging to QA Server</time> In mattis elit vitae odio posuere, nec maximus massa varius. Suspendisse varius-->
<!--                        volutpat mattis. Vestibulum id magna est.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Quality Testing</time> Aenean condimentum odio a bibendum rhoncus. Ut mauris felis, volutpat eget porta-->
<!--                        faucibus, euismod quis ante.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Fixing Bugs</time> Vestibulum porttitor lorem sed pharetra dignissim. Nulla maximus, dui a tristique-->
<!--                        iaculis, quam dolor convallis enim, non dignissim ligula ipsum a turpis.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Deployment to the Staging Server</time> In mattis elit vitae odio posuere, nec maximus massa varius. Suspendisse varius-->
<!--                        volutpat mattis. Vestibulum id magna est.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Client Feedback</time> In mattis elit vitae odio posuere, nec maximus massa varius. Suspendisse varius-->
<!--                        volutpat mattis. Vestibulum id magna est.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Deployment to Production Server</time> In mattis elit vitae odio posuere, nec maximus massa varius. Suspendisse varius-->
<!--                        volutpat mattis. Vestibulum id magna est.-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div>-->
<!--                        <time>Sign Out</time> In mattis elit vitae odio posuere, nec maximus massa varius. Suspendisse varius-->
<!--                        volutpat mattis. Vestibulum id magna est.-->
<!--                    </div>-->
<!--                </li><li></li>-->
                <!--  -->
<!--            </ol>-->

<!--            <div class="arrows">-->
<!--                <button class="arrow arrow__prev disabled" disabled>-->
<!--                    <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/arrow_prev.svg" alt="prev timeline arrow">-->
<!--                </button>-->
<!--                <button class="arrow arrow__next">-->
<!--                    <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/arrow_next.svg" alt="next timeline arrow">-->
<!--                </button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

<!-- End Roadmap  -->

<!-- Who We Are-->
<section class="section section-lg section-last text-center" id="our-team" data-type="anchor">
    <div class="container">
        <h3 class="wow-outer text-center"><span class="wow slideInDown">Who We Are</span></h3>
        <div class="row row-50">
            <div class="col-sm-6 col-lg-4 wow-outer">
                <!-- Profile Minimal-->
                <article class="profile-minimal wow slideInLeft"><img class="profile-minimal-image"
                        src="{{ asset('front/images/portrait-freelancer-looking-camera-sitting-desk-with-charts-smart-businessman-sitting-his-workplace-course-late-night-hours-doing-his-job.jpg') }}"
                        alt="" width="370" height="368" />
                    <div class="profile-minimal-caption">
                        <h4 class="profile-minimal-title">Jatin </h4>
                        <p class="profile-minimal-position">CEO, Founder</p>
                    </div>
                </article>
            </div>
            <div class="col-sm-6 col-lg-4 wow-outer">
                <!-- Profile Minimal-->
                <article class="profile-minimal wow slideInLeft" data-wow-delay=".05s"><img
                        class="profile-minimal-image"
                        src="{{ asset('front/images/business-person-looking-finance-graphs.jpg') }}"
                        alt="" width="370" height="368" />
                    <div class="profile-minimal-caption">
                        <h4 class="profile-minimal-title">Rohit Singh</h4>
                        <p class="profile-minimal-position">PR Manager</p>
                    </div>
                </article>
            </div>
            <div class="col-sm-6 col-lg-4 wow-outer">
                <!-- Profile Minimal-->
                <article class="profile-minimal wow slideInLeft" data-wow-delay=".1s"><img class="profile-minimal-image"
                        src="{{ asset('front/images/pexels-cottonbro-5483158.jpg') }}" alt=""
                        width="370" height="368" />
                    <div class="profile-minimal-caption">
                        <h4 class="profile-minimal-title">Shivam</h4>
                        <p class="profile-minimal-position">Marketing Expert</p>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
<!-- Testimonials-->
<section class="section section-lg bg-gray-100" id="testimonials" data-type="anchor">
    <div class="container">
        <h3 class="text-center">Testimonials </h3>
        <div class="row row-50 justify-content-center">
            <div class="col-md-10 col-lg-5">
                <div class="owl-carousel" data-items="1" data-dots="true" data-nav="false" data-loop="true"
                    data-margin="30" data-stage-padding="0" data-mouse-drag="false">
                    <div class="wow-outer">
                        <blockquote class="quote-modern wow slideInLeft">
                            <svg class="quote-modern-mark" x="0px" y="0px" width="35px" height="25px"
                                viewbox="0 0 35 25">
                                <path
                                    d="M27.461,10.206h7.5v15h-15v-15L25,0.127h7.5L27.461,10.206z M7.539,10.206h7.5v15h-15v-15L4.961,0.127h7.5                L7.539,10.206z">
                                </path>
                            </svg>
                            <div class="quote-modern-text">
                                <p>Qubify is, hands down, one of the best companies that we have worked with! The
                                    company has either met or exceeded all of the goals that we initially set for all of
                                    the projects that they implemented for us. I am sure that our company will partner
                                    with them again in the future.</p>
                            </div>
                            <div class="quote-modern-meta">
                                <div class="quote-modern-avatar"><img
                                        src="{{ asset('front/images/testimonials-person-3-96x96') }}.jpg"
                                        alt="" width="96" height="96" />
                                </div>
                                <div class="quote-modern-info">
                                    <cite class="quote-modern-cite">Albert Webb</cite>
                                    <p class="quote-modern-caption">CEO at Majestic</p>
                                </div>
                            </div>
                        </blockquote>
                    </div>
                    <div class="wow-outer">
                        <blockquote class="quote-modern wow slideInLeft">
                            <svg class="quote-modern-mark" x="0px" y="0px" width="35px" height="25px"
                                viewbox="0 0 35 25">
                                <path
                                    d="M27.461,10.206h7.5v15h-15v-15L25,0.127h7.5L27.461,10.206z M7.539,10.206h7.5v15h-15v-15L4.961,0.127h7.5                L7.539,10.206z">
                                </path>
                            </svg>
                            <div class="quote-modern-text">
                                <p>Qubify team provides us with a full service digital marketing service that
                                    encompasses social media, demand generation, digital advertising, search engine
                                    optimization, email and marketing automation that has increased our visibility and
                                    inbound lead generation.</p>
                            </div>
                            <div class="quote-modern-meta">
                                <div class="quote-modern-avatar"><img
                                        src="{{ asset('front/images/testimonials-person-1-96x96') }}.jpg"
                                        alt="" width="96" height="96" />
                                </div>
                                <div class="quote-modern-info">
                                    <cite class="quote-modern-cite">Kelly McMillan</cite>
                                    <p class="quote-modern-caption">Private Entrepreneur</p>
                                </div>
                            </div>
                        </blockquote>
                    </div>
                    <div class="wow-outer">
                        <blockquote class="quote-modern wow slideInLeft">
                            <svg class="quote-modern-mark" x="0px" y="0px" width="35px" height="25px"
                                viewbox="0 0 35 25">
                                <path
                                    d="M27.461,10.206h7.5v15h-15v-15L25,0.127h7.5L27.461,10.206z M7.539,10.206h7.5v15h-15v-15L4.961,0.127h7.5                L7.539,10.206z">
                                </path>
                            </svg>
                            <div class="quote-modern-text">
                                <p>We rely on Qubify for its digital marketing expertise, particularly in the areas of
                                    SEO and social media marketing. Their team is knowledgeable, responsive and
                                    committed to supporting our initiatives, making them invaluable partners in our
                                    effort to promote our company.</p>
                            </div>
                            <div class="quote-modern-meta">
                                <div class="quote-modern-avatar"><img
                                        src="{{ asset('front/images/testimonials-person-2-96x96') }}.jpg"
                                        alt="" width="96" height="96" />
                                </div>
                                <div class="quote-modern-info">
                                    <cite class="quote-modern-cite">Harold Barnett</cite>
                                    <p class="quote-modern-caption">Regional Manager</p>
                                </div>
                            </div>
                        </blockquote>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-10 col-lg-6 wow-outer">
                    <div class="thumbnail-video-1 bg-gray-700 wow slideInLeft">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe width="570" height="320" src="//www.youtube.com/embed/QZzbm-FrkGk"
                                allowfullscreen=""></iframe>
                        </div>
                        <div class="thumbnail-video__overlay video-overlay"
                            style="background-image: url({{ asset('front/images/video-preview-1-570x320') }}.jpg)">
                            <div class="button-video"></div>
                            <h5>Lawrence Alvarado</h5>
                        </div>
                    </div>
                </div> -->
        </div>
    </div>
</section>
<!-- Pricing-->
{{-- <section class="section section-lg text-center" id="pricing" data-type="anchor">
        <div class="container">
            <h3>Pricing</h3>
            <p><span class="text-width-1">In this section, you can learn more about available pricing plans and included
                    services. Even if you own a small business and don’t need big marketing campaign, we have what to
                    offer.</span></p>
            <div class="pricing-group-modern wow-outer">
                <!-- Pricing Modern-->
                <article class="pricing-modern wow fadeInLeft">
                    <ul class="pricing-modern-rating">
                        <li class="mdi mdi-star-outline"></li>
                    </ul>
                    <h5 class="pricing-modern-title"><a href="#">Small Business</a></h5>
                    <table class="pricing-modern-table">
                        <tr>
                            <td>2</td>
                            <td>Campaign Hours</td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Month</td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td>Additional Services</td>
                        </tr>
                    </table>
                    <p class="pricing-modern-price"><span class="pricing-modern-price-currency">$</span>789.00</p><a
                        class="button button-primary button-winona" href="#">Order now</a>
                </article>
                <!-- Pricing Modern-->
                <article class="pricing-modern wow fadeInLeft" data-wow-delay=".05s">
                    <ul class="pricing-modern-rating">
                        <li class="mdi mdi-star-outline"></li>
                        <li class="mdi mdi-star-outline"></li>
                    </ul>
                    <h5 class="pricing-modern-title"><a href="#">Medium Business</a></h5>
                    <table class="pricing-modern-table">
                        <tr>
                            <td>5</td>
                            <td>Campaign Hours</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Months</td>
                        </tr>
                        <tr>
                            <td>20</td>
                            <td>Additional Services</td>
                        </tr>
                    </table>
                    <p class="pricing-modern-price"><span class="pricing-modern-price-currency">$</span>1299.00</p><a
                        class="button button-primary button-winona" href="#">Order now</a>
                </article>
                <!-- Pricing Modern-->
                <article class="pricing-modern wow fadeInLeft" data-wow-delay=".1s">
                    <ul class="pricing-modern-rating">
                        <li class="mdi mdi-star-outline"></li>
                        <li class="mdi mdi-star-outline"></li>
                        <li class="mdi mdi-star-outline"></li>
                    </ul>
                    <h5 class="pricing-modern-title"><a href="#">Big Business</a></h5>
                    <table class="pricing-modern-table">
                        <tr>
                            <td>10</td>
                            <td>Campaign Hours</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Months</td>
                        </tr>
                        <tr>
                            <td>40</td>
                            <td>Additional Services</td>
                        </tr>
                    </table>
                    <p class="pricing-modern-price"><span class="pricing-modern-price-currency">$</span>2369.00</p><a
                        class="button button-primary button-winona" href="#">Order now</a>
                </article>
                <!-- Pricing Modern-->
                <article class="pricing-modern wow fadeInLeft" data-wow-delay=".15s">
                    <ul class="pricing-modern-rating">
                        <li class="mdi mdi-star-outline"></li>
                        <li class="mdi mdi-star-outline"></li>
                        <li class="mdi mdi-star-outline"></li>
                        <li class="mdi mdi-star-outline"></li>
                    </ul>
                    <h5 class="pricing-modern-title"><a href="#">Corporation</a></h5>
                    <table class="pricing-modern-table">
                        <tr>
                            <td>20+</td>
                            <td>Campaign Hours</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Months</td>
                        </tr>
                        <tr>
                            <td>60+</td>
                            <td>Additional Services</td>
                        </tr>
                    </table>
                    <p class="pricing-modern-price"><span class="pricing-modern-price-currency">$</span>6790.00</p><a
                        class="button button-primary button-winona" href="#">Order now</a>
                </article>
            </div>
        </div>
    </section> --}}
<!-- Wide CTA-->
<section class="section section-md bg-primary-gradient text-center">
    <div class="container">
        <div class="box-cta-1 text-center">
            <h3 class="wow-outer"><span class="wow slideInRight">We Offer Quality <span
                        class="font-weight-bold">Branding and Promotion</span></span></h3>
            <!-- <div class="wow-outer button-outer"><a
                        class="button button-lg button-primary button-winona wow slideInLeft" href="#">Free
                        consultation</a></div> -->
        </div>
    </div>
</section>
<!-- Latest Blog Posts-->
<section class="section section-lg text-center" id="blog" data-type="anchor">
    <div class="container">
        <h3 class="wow-outer"><span class="wow slideInDown">Latest Blog Posts</span></h3>
        <div class="row row-50">
            <div class="col-md-6 wow-outer">
                <!-- Post Modern-->
                <article class="post-modern wow slideInLeft"><a class="post-modern-media" href="#"><img
                            src="{{ asset('front/images/grid-blog-1-571x353') }}.jpg" alt=""
                            width="571" height="353" /></a>
                    <h4 class="post-modern-title"><a href="#">10 Digital Marketing Mistakes to Avoid</a></h4>
                    <ul class="post-modern-meta">
                        <li>by Theresa Barnes</li>
                        <li>
                            <time datetime="2019">Apr 21, 2019 at 12:05 pm</time>
                        </li>
                        <li><a class="button-winona" href="#">News</a></li>
                    </ul>
                    <p>Though managing your digital marketing campaign may seem easy, you can encounter some
                        complexities, which usually lead to mistakes and a bad promotion effect.</p>
                </article>
            </div>
            <div class="col-md-6 wow-outer">
                <!-- Post Modern-->
                <article class="post-modern wow slideInLeft"><a class="post-modern-media" href="#"><img
                            src="{{ asset('front/images/grid-blog-2-571x353') }}.jpg" alt=""
                            width="571" height="353" /></a>
                    <h4 class="post-modern-title"><a href="#">Where Marketers Need to Succeed This Season</a>
                    </h4>
                    <ul class="post-modern-meta">
                        <li>by Theresa Barnes </li>
                        <li>
                            <time datetime="2019">Apr 21, 2019 at 12:05 pm</time>
                        </li>
                        <li><a class="button-winona" href="#">News</a></li>
                    </ul>
                    <p>Being a successful marketer today might appear to require a never-ending list of skills. Where do
                        you need to excel -- content creation, social media, web analytics, or all of the above?</p>
                </article>
            </div>
        </div>
        <div class="wow-outer button-outer"><a class="button button-primary-outline button-winona wow slideInUp"
                href="#">View all Blog posts</a></div>
    </div>
</section>
<!-- Contact Us-->
<section class="section bg-gray-100 mb-5" id="contact-us" data-type="anchor">
    <div class="range justify-content-xl-between">
        <div class="cell-xl-6 align-self-center container">
            <div class="row">
                <div class="col-lg-9 cell-inner">
                    <div class="section-lg">
                        <h3 class="wow-outer"><span class="wow slideInDown">Contact Us</span></h3>
                        <!-- RD Mailform-->
                        <form class="rd-form " id="contact_form" data-form-output="form-output-global"
                            data-form-type="contact" method="POST"
                            action="{{ route('contact.submit') }}">
                            @csrf
                            <div class="row row-10">
                                <div class="col-md-6 wow-outer">
                                    <div class="form-wrap wow fadeInUp">
                                        <label class="form-label-outside" for="contact-first-name">First Name</label>
                                        <input class="form-input" id="contact-first-name" type="text" name="firstname"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6 wow-outer">
                                    <div class="form-wrap wow fadeInUp" data-wow-delay=".1s">
                                        <label class="form-label-outside" for="contact-last-name">Last Name</label>
                                        <input class="form-input" id="contact-last-name" type="text" name="lastname"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6 wow-outer">
                                    <div class="form-wrap wow fadeInUp" data-wow-delay=".2s">
                                        <label class="form-label-outside" for="contact-email">E-mail</label>
                                        <input class="form-input" id="contact-email" type="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6 wow-outer">
                                    <div class="form-wrap wow fadeInUp" data-wow-delay=".3s">
                                        <label class="form-label-outside" for="contact-phone">Phone</label>
                                        <input class="form-input" id="contact-phone" type="text" name="phone" required>
                                    </div>
                                </div>
                                <div class="col-12 wow-outer">
                                    <div class="form-wrap wow fadeInUp" data-wow-delay=".4s">
                                        <label class="form-label-outside" for="contact-message">Your Message</label>
                                        <textarea class="form-input" id="contact-message" name="message"
                                            required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="group group-middle wow-outer">
                                <button class="button button-primary button-winona wow fadeInRight" type="submit">Send
                                    Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="cell-xl-5 height-fill wow fadeIn">
            <!--Please, add the data attribute data-key="YOUR_API_KEY" in order to insert your own API key for the Google map.-->
            <!--Please note that YOUR_API_KEY should replaced with your key.-->
            <!--Example: <div class="google-map-container" data-key="YOUR_API_KEY">-->
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3432.063638800677!2d76.85579497402254!3d30.66033643305377!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390f9492aacfffe5%3A0x4395fd5c0a539130!2sTricity%20Plaza!5e0!3m2!1sen!2sin!4v1713509573432!5m2!1sen!2sin"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>


</div>
@endsection