@extends('backend.layouts.master')

@section('template_title')
    {{ __('Update') }} Home Slider
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Home Slider</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('admin.home-sliders.update', $homeSlider->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('backend.home-slider.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
