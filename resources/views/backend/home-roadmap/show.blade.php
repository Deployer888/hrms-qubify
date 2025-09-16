@extends('backend.layouts.master')

@section('template_title')
    {{ $homeRoadmap->name ?? __('Show') . " " . __('Home Roadmap') }}
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Home Roadmap</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('admin.home-roadmaps.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Title:</strong>
                                    {{ $homeRoadmap->title }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Description:</strong>
                                    {{ $homeRoadmap->description }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Icon:</strong>
                                    <img src="{{ asset($homeRoadmap->icon) }}" class="bg bg-primary p-2" alt="Icon Preview" class="mt-2" style="max-width: 100px;">
                                    
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
