@extends('backend.layouts.master')

@section('template_title')
    {{ $homeSlider->name ?? __('Show') . " " . __('Home Slider') }}
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Home Slider</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('admin.home-sliders.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Heading:</strong>
                                    {{ $homeSlider->heading }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Sub Heading:</strong>
                                    {{ $homeSlider->sub_heading }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Image:</strong>
                                    <img src="{{ asset($homeSlider->image) }}" alt="Slider Image" class="mt-2" style="max-width: 200px;">
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
