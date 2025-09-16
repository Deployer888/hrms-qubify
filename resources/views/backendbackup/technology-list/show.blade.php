@extends('backend.layouts.master')

@section('template_title')
    {{ $technologyList->name ?? __('Show') . " " . __('Technology List') }}
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Technology List</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('admin.technology-lists.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Technology Id:</strong>
                                    {{ $technologyList->technology_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Name:</strong>
                                    {{ $technologyList->name }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Icon:</strong>
                                    {{ $technologyList->icon }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Description:</strong>
                                    {{ $technologyList->description }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Slug:</strong>
                                    {{ $technologyList->slug }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
