@extends('layouts.admin')
@section('page-title')
    {{ __('Create Job') }}
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
    <link href="{{ asset('assets/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet" />
@endpush
@push('script-page')
    <script src="{{ asset('assets/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>

    <script>
        var e = $('[data-toggle="tags"]');
        e.length && e.each(function() {
            $(this).tagsinput({
                tagClass: "badge badge-primary"
            })
        });
    </script>
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
@endpush
@section('content')
    <form action="{{ url('job') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6 ">
                <div class="card card-fluid">
                    <div class="card-body ">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="title" class="form-control-label">{{ __('Job Title') }}</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ old('title') }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                                <select name="branch" id="branch" class="form-control select2" required>
                                    @foreach ($branches as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('branch') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category" class="form-control-label">{{ __('Job Category') }}</label>
                                <select name="category" id="category" class="form-control select2" required>
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="position" class="form-control-label">{{ __('Positions') }}</label>
                                <input type="text" name="position" id="position" class="form-control"
                                    value="{{ old('positions') }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status" class="form-control-label">{{ __('Status') }}</label>
                                <select name="status" id="status" class="form-control select2" required>
                                    @foreach ($status as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="start_date" class="form-control-label">{{ __('Start Date') }}</label>
                                <input type="text" name="start_date" id="start_date" class="form-control datepicker"
                                    value="{{ old('start_date') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date" class="form-control-label">{{ __('End Date') }}</label>
                                <input type="text" name="end_date" id="end_date" class="form-control datepicker"
                                    value="{{ old('end_date') }}">
                            </div>
                            <div class="form-group col-md-12">
                                <input type="text" class="form-control" value="" data-toggle="tags" name="skill"
                                    placeholder="Skill">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-6 ">
                <div class="card card-fluid">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>{{ __('Need to ask ?') }}</h6>
                                    <div class="my-4">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="applicant[]"
                                                value="gender" id="check-gender">
                                            <label class="custom-control-label"
                                                for="check-gender">{{ __('Gender') }}</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="applicant[]"
                                                value="dob" id="check-dob">
                                            <label class="custom-control-label"
                                                for="check-dob">{{ __('Date Of Birth') }}</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="applicant[]"
                                                value="country" id="check-country">
                                            <label class="custom-control-label"
                                                for="check-country">{{ __('Country') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>{{ __('Need to show option ?') }}</h6>
                                    <div class="my-4">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="visibility[]"
                                                value="profile" id="check-profile">
                                            <label class="custom-control-label"
                                                for="check-profile">{{ __('Profile Image') }}</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="visibility[]"
                                                value="resume" id="check-resume">
                                            <label class="custom-control-label"
                                                for="check-resume">{{ __('Resume') }}</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="visibility[]"
                                                value="letter" id="check-letter">
                                            <label class="custom-control-label"
                                                for="check-letter">{{ __('Cover Letter') }}</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="visibility[]"
                                                value="terms" id="check-terms">
                                            <label class="custom-control-label"
                                                for="check-terms">{{ __('Terms And Conditions') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <h6>{{ __('Custom Question') }}</h6>
                                <div class="my-4">
                                    @foreach ($customQuestion as $question)
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="custom_question[]"
                                                value="{{ $question->id }}" id="custom_question_{{ $question->id }}">
                                            <label class="custom-control-label"
                                                for="custom_question_{{ $question->id }}">{{ $question->question }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-fluid">
                    <div class="card-body ">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description" class="form-control-label">{{ __('Job Description') }}</label>
                                <textarea class="form-control summernote-simple" name="description" id="exampleFormControlTextarea1" rows="15"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-fluid">
                    <div class="card-body ">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="requirement" class="form-control-label">{{ __('Job Requirement') }}</label>
                                <textarea class="form-control summernote-simple" name="requirement" id="exampleFormControlTextarea2" rows="8"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-right">
                <div class="form-group">
                    <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                </div>
            </div>
        </div>
    </form>
@endsection
