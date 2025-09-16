@extends('backend.layouts.master')

@section('template_title')
    {{ __('Create') }} Home Roadmap
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} Home Roadmap</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('admin.home-roadmaps.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('backend.home-roadmap.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
