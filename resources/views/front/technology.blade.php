@extends('front.layouts.app')
@section('content')
<?php
$url = $_SERVER['REQUEST_URI'];
$heading = "";
if (strpos($url, '/technologies') !== false) {
    $heading = "Technologies";
    $infoList = \Helper::technologyList();
} elseif (strpos($url, '/services') !== false) {
    $heading = "Services";
    $infoList = \Helper::serviceList();
} elseif (strpos($url, '/industries') !== false) {
    $heading = "Industries";
    $infoList = \Helper::industryList();
} else {
    echo "Go back to home.";
}
?>
    <div class="clearfix"></div>
    <section class="services-area main-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="section-title">
                        <h2>Our Exceptional {{$heading}} </h2>
                        <p>We craft the best web and mobile app solutions that reflect your business intentions
                            perfectly to offer robust business growth
                        </p>
                    </div>
                </div>
            </div>
            <div class="row ">
                @foreach($infoList as $info)
                <div class="col-lg-4 col-md-6">
                    <div class="single-services"
                        style="cursor: pointer;" data-cf-modified-8ecfc8b0ab2ddf28f610497e->
                        <div class="icon">
                            <i  style="background-color:#e76d2726" atr-color-hover="#e76d2759"
                                atr-color="#e76d2726">
                                <img width="32" height="32"
                                    data-src="{{asset($info->icon)}}?tr=w-50,h-50"
                                    loading="lazy" class="img-fluid"
                                    src="{{asset($info->icon)}}"
                                    alt="{{ $info->slug }}">
                            </i>
                        </div>
                        <h3 class="h-main-one" atp-color="#e76d27">
                        {{ $info->name }}</h3>
                        <p>{!! $info->description !!}</p>
                        
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    
@endsection