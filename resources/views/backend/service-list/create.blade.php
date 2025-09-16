@extends('backend.layouts.master')

@section('template_title')
    {{ __('Create') }} Service List
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} Service List</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('admin.service-lists.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('backend.service-list.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
