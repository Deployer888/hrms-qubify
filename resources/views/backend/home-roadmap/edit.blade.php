@extends('backend.layouts.master')

@section('template_title')
    {{ __('Update') }} Home Roadmap
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Home Roadmap</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('admin.home-roadmaps.update', $homeRoadmap->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('backend.home-roadmap.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
