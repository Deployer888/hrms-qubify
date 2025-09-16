@extends('backend.layouts.master')
@section('template_title')
    {{ __('Update') }} Industry List
@endsection

@section('admin-content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Industry List</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('admin.industry-lists.update', $industryList->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PUT') }}
                            @csrf

                            @include('backend.industry-list.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
