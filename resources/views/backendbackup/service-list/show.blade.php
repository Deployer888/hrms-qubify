@extends('backend.layouts.master')

@section('template_title')
    {{ $serviceList->name ?? __('Show') . " " . __('Service List') }}
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Service List</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('admin.service-lists.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Technology Id:</strong>
                                    {{ $serviceList->technology_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Name:</strong>
                                    {{ $serviceList->name }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Icon:</strong>
                                    {{ $serviceList->icon }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Description:</strong>
                                    {{ $serviceList->description }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Slug:</strong>
                                    {{ $serviceList->slug }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
