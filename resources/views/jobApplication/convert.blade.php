@extends('layouts.admin')
@section('page-title')
    {{ __('Convert To Employee') }}
@endsection
@section('content')
    <div class="row">
        <form action="{{ route('job.on.board.convert', $jobOnBoard->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
    </div>
    <div class="row">
        <div class="col-md-6 ">
            <div class="card card-fluid">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
                </div>
                <div class="card-body ">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name" class="form-control-label">{{ __('Name') }}</label><span
                                class="text-danger pl-1">*</span>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ !empty($jobOnBoard->applications) ? $jobOnBoard->applications->name : '' }}"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone" class="form-control-label">{{ __('Phone') }}</label><span
                                class="text-danger pl-1">*</span>
                            <input type="number" name="phone" id="phone" class="form-control"
                                value="{{ !empty($jobOnBoard->applications) ? $jobOnBoard->applications->phone : '' }}">
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dob" class="form-control-label">{{ __('Date of Birth') }}</label><span
                                    class="text-danger pl-1">*</span>
                                <input type="text" name="dob" id="dob" class="form-control datepicker"
                                    value="{{ !empty($jobOnBoard->applications) ? $jobOnBoard->applications->dob : '' }}">
                            </div>
                        </div>

                        <div class="col-md-6 ">
                            <div class="form-group ">
                                <label for="gender" class="form-control-label">{{ __('Gender') }}</label><span
                                    class="text-danger pl-1">*</span>
                                <div class="d-flex radio-check">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="g_male" value="Male" name="gender"
                                            class="custom-control-input"
                                            {{ !empty($jobOnBoard->applications) && $jobOnBoard->applications->gender == 'Male' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="g_male">{{ __('Male') }}</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="g_female" value="Female" name="gender"
                                            class="custom-control-input"
                                            {{ !empty($jobOnBoard->applications) && $jobOnBoard->applications->gender == 'Female' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="g_female">{{ __('Female') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email" class="form-control-label">{{ __('Email') }}</label><span
                                class="text-danger pl-1">*</span>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password" class="form-control-label">{{ __('Password') }}</label><span
                                class="text-danger pl-1">*</span>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-control-label">{{ __('Address') }}</label><span
                            class="text-danger pl-1">*</span>
                        <textarea name="address" id="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 ">
            <div class="card card-fluid">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                </div>
                <div class="card-body employee-detail-create-body">
                    <div class="row">
                        @csrf
                        <div class="form-group col-md-12">
                            <label for="employee_id" class="form-control-label">{{ __('Employee ID') }}</label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control"
                                value="{{ $employeesId }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="branch_id" class="form-control-label">{{ __('Branch') }}</label>
                            <select name="branch_id" id="branch_id" class="form-control select2" required>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                            <select name="department_id" id="department_id" class="form-control select2" required>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="designation_id" class="form-control-label">{{ __('Designation') }}</label>
                            <select name="designation_id" id="designation_id"
                                class="form-control select2-multiple select2" data-toggle="select2"
                                data-placeholder="{{ __('Select Designation ...') }}">
                                <option value="">Select Designation</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}"
                                        {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
                                        {{ $designation->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 ">
                            <label for="company_doj"
                                class="form-control-label">{{ __('Company Date Of Joining') }}</label>
                            <input type="text" name="company_doj" id="company_doj" class="form-control datepicker"
                                value="{{ $jobOnBoard->joining_date }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 ">
            <div class="card card-fluid">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Document') }}</h6>
                </div>
                <div class="card-body employee-detail-create-body">
                    @foreach ($documents as $key => $document)
                        <div class="row">
                            <div class="form-group col-12">
                                <div class="float-left col-4">
                                    <label for="document"
                                        class="float-left pt-1 form-control-label">{{ $document->name }}
                                        @if ($document->is_required == 1)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                </div>
                                <div class="float-right col-8">
                                    <input type="hidden" name="emp_doc_id[{{ $document->id }}]" id=""
                                        value="{{ $document->id }}">
                                    <div class="choose-file form-group">
                                        <label for="document[{{ $document->id }}]">
                                            <div>{{ __('Choose File') }}</div>
                                            <input class="form-control  @error('document') is-invalid @enderror border-0"
                                                @if ($document->is_required == 1) required @endif
                                                name="document[{{ $document->id }}]" type="file"
                                                id="document[{{ $document->id }}]"
                                                data-filename="{{ $document->id . '_filename' }}">
                                        </label>
                                        <p class="{{ $document->id . '_filename' }}"></p>
                                    </div>

                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="card card-fluid">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
                </div>
                <div class="card-body employee-detail-create-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="account_holder_name"
                                class="form-control-label">{{ __('Account Holder Name') }}</label>
                            <input type="text" name="account_holder_name" id="account_holder_name"
                                class="form-control" value="{{ old('account_holder_name') }}">

                        </div>
                        <div class="form-group col-md-6">
                            <label for="account_number" class="form-control-label">{{ __('Account Number') }}</label>
                            <input type="number" name="account_number" id="account_number" class="form-control"
                                value="{{ old('account_number') }}">

                        </div>
                        <div class="form-group col-md-6">
                            <label for="bank_name" class="form-control-label">{{ __('Bank Name') }}</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control"
                                value="{{ old('bank_name') }}">

                        </div>
                        <div class="form-group col-md-6">
                            <label for="bank_identifier_code"
                                class="form-control-label">{{ __('Bank Identifier Code') }}</label>
                            <input type="text" name="bank_identifier_code" id="bank_identifier_code"
                                class="form-control" value="{{ old('bank_identifier_code') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="branch_location" class="form-control-label">{{ __('Branch Location') }}</label>
                            <input type="text" name="branch_location" id="branch_location" class="form-control"
                                value="{{ old('branch_location') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tax_payer_id" class="form-control-label">{{ __('Tax Payer Id') }}</label>
                            <input type="text" name="tax_payer_id" id="tax_payer_id" class="form-control"
                                value="{{ old('tax_payer_id') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <input type="submit" value="Create" class="btn btn-xs badge-blue float-right radius-10px">
            </form>
        </div>
    </div>
@endsection


@push('script-page')
    <script>
        $(document).ready(function() {
            var d_id = $('#department_id').val();
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });

        function getDesignation(did) {

            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append(
                        '<option value="">Select Designation</option>');
                    $.each(data, function(key, value) {
                        $('#designation_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                }
            });
        }
    </script>
@endpush
