@extends('backend.layouts.master')

@section('template_title')
    {{ __('Update') }} Trusted Logo
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Trusted Logo</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('admin.trusted-logos.update', $trustedLogo->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('backend.trusted-logo.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
