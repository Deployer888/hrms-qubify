@extends('backend.layouts.master')

@section('template_title')
    {{ $trustedLogo->name ?? __('Show') . " " . __('Trusted Logo') }}
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Trusted Logo</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('admin.trusted-logos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Image:</strong>
                                    <img src="{{ asset($trustedLogo->image) }}" class="bg-primary p-2" alt="Trusted Logo" height="100px" width="100px" >
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
