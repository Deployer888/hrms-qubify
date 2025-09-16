@extends('layouts.admin')
@section('page-title')
    {{ __('Create Employee') }}
@endsection
@section('content')
    <div class="row">
        <form action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Personal Detail Card -->
                <div class="col-md-6">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Name input -->
                                <div class="form-group col-md-6">
                                    <label for="name" class="form-control-label">{{ __('Name') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required
                                        value="{{ old('name') }}">
                                </div>
                                <!-- Phone input -->
                                <div class="form-group col-md-6">
                                    <label for="phone" class="form-control-label">{{ __('Phone') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        value="{{ old('phone') }}">
                                </div>
                                <!-- DOB input -->
                                <div class="form-group col-md-6">
                                    <label for="dob" class="form-control-label">{{ __('Date of Birth') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="text" name="dob" id="dob" class="form-control maxDatepicker"
                                        value="{{ old('dob') }}">
                                </div>
                                <!-- Email input -->
                                <div class="form-group col-md-6">
                                    <label for="email" class="form-control-label">{{ __('Email') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" required
                                        value="{{ old('email') }}">
                                </div>
                                <!-- Personal Email input -->
                                <div class="form-group col-md-6">
                                    <label for="personal_email" class="form-control-label">{{ __('Personal Email') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="email" name="personal_email" id="personal_email" class="form-control" required
                                        value="{{ old('personal_email') }}">
                                </div>
                                <!-- Password input -->
                                <div class="form-group col-md-6">
                                    <label for="password" class="form-control-label">{{ __('Password') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <!-- Gender radio buttons -->
                                <div class="form-group col-md-6">
                                    <label class="form-control-label">{{ __('Gender') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <div class="d-flex radio-check">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="g_male" value="Male" name="gender"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="g_male">{{ __('Male') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="g_female" value="Female" name="gender"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="g_female">{{ __('Female') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Address input -->
                                <div class="form-group col-md-6">
                                    <label for="address" class="form-control-label">{{ __('Address') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <textarea name="address" id="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Company Detail Card -->
                <div class="col-md-6">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            <div class="row">
                                <!-- Employee ID input -->
                                <div class="form-group col-md-6">
                                    <label for="employee_id" class="form-control-label">{{ __('Employee ID') }}</label>
                                    <input type="text" name="employee_id" id="employee_id" class="form-control"
                                        value="{{ $employeesId }}" disabled>
                                </div>
                                <!-- Branch select -->
                                <div class="form-group col-md-6">
                                    <label for="branch_id" class="form-control-label">{{ __('Branch') }}</label>
                                    <select name="branch_id" id="branch_id" class="form-control select2" required>
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $id => $branch)
                                            <option value="{{ $id }}"
                                                {{ old('branch_id') == $id ? 'selected' : '' }}>{{ $branch }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Department select -->
                                <div class="form-group col-md-6">
                                    <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                                    <select name="department_id" id="department_id" class="form-control select2"
                                        required>
                                        <option value="">Select Development</option>
                                        @foreach ($departments as $id => $department)
                                            <option value="{{ $id }}"
                                                {{ old('department_id') == $id ? 'selected' : '' }}>{{ $department }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Designation select -->
                                <div class="form-group col-md-6">
                                    <label for="designation_id"
                                        class="form-control-label">{{ __('Designation') }}</label>
                                    <select name="designation_id" id="designation_id"
                                        class="form-control select2-multiple select2" data-toggle="select2"
                                        data-placeholder="{{ __('Select Designation ...') }}">
                                        <option value="">Select Designation</option>
                                        <!-- Designation options will be filled dynamically -->
                                    </select>
                                </div>
                                <!-- Company Date of Joining input -->
                                <div class="form-group col-md-6">
                                    <label for="company_doj"
                                        class="form-control-label">{{ __('Company Date Of Joining') }}</label>
                                    <input type="text" name="company_doj" id="company_doj"
                                        class="form-control datepicker" required value="{{ old('company_doj') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="company_doj"
                                        class="form-control-label">{{ __('Shift Start') }}</label>
                                    <input type="time" name="shift_start" id="shift_start"
                                        class="form-control" required value="{{ old('shift_start') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="company_doj"
                                    class="form-control-label mt-2">{{ __('Salary') }}</label>
                                    <input type="number" name="salary" id="salary" class="form-control"
                                                required value="">
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="is_team_leader" class="form-control-label mt-2">{{ __('Team Leader') }}</label>
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" name="is_team_leader" id="is_team_leader" class="mx-2" value="{{ old('is_team_leader') }}">
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-6">
                                            <label for="team_leader" class="form-control-label">{{ __('Select Team Leader') }}</label>
                                            <select name="team_leader" id="team_leader" class="form-control select2" required disabled>
                                                <option value="">{{ __('Select Team Leader') }}</option>
                                            </select>
                                            <input type="hidden" name="team_leader" id="hidden_team_leader">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Document Card -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Document') }}</h6>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            @foreach ($documents as $document)
                                <div class="row">
                                    <div class="form-group col-12">
                                        <div class="float-left col-4">
                                            <label for="document_{{ $document->id }}"
                                                class="float-left pt-1 form-control-label">
                                                {{ $document->name }}
                                                @if ($document->is_required == 1)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="float-right col-8">
                                            <input type="hidden" name="emp_doc_id[{{ $document->id }}]"
                                                value="{{ $document->id }}">
                                            <div class="choose-file form-group">
                                                <label for="document_{{ $document->id }}">
                                                    <div>{{ __('Choose File') }}</div>
                                                    <input type="file" name="document[{{ $document->id }}]"
                                                        id="document_{{ $document->id }}"
                                                        class="form-control @error('document') is-invalid @enderror border-0"
                                                        @if ($document->is_required == 1) required @endif
                                                        data-filename="{{ $document->id }}_filename">
                                                </label>
                                                <p class="{{ $document->id }}_filename"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Bank Account Detail Card -->
                <div class="col-md-6">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            <div class="row">
                                <!-- Account Holder Name input -->
                                <div class="form-group col-md-6">
                                    <label for="account_holder_name"
                                        class="form-control-label">{{ __('Account Holder Name') }}</label>
                                    <input type="text" name="account_holder_name" id="account_holder_name"
                                        class="form-control" value="{{ old('account_holder_name') }}">
                                </div>
                                <!-- Account Number input -->
                                <div class="form-group col-md-6">
                                    <label for="account_number"
                                        class="form-control-label">{{ __('Account Number') }}</label>
                                    <input type="number" name="account_number" id="account_number" class="form-control"
                                        value="{{ old('account_number') }}">
                                </div>
                                <!-- Bank Name input -->
                                <div class="form-group col-md-6">
                                    <label for="bank_name" class="form-control-label">{{ __('Bank Name') }}</label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control"
                                        value="{{ old('bank_name') }}">
                                </div>
                                <!-- Bank Identifier Code input -->
                                <div class="form-group col-md-6">
                                    <label for="bank_identifier_code"
                                        class="form-control-label">{{ __('Bank Identifier Code') }}</label>
                                    <input type="text" name="bank_identifier_code" id="bank_identifier_code"
                                        class="form-control" value="{{ old('bank_identifier_code') }}">
                                </div>
                                <!-- Branch Location input -->
                                <div class="form-group col-md-6">
                                    <label for="branch_location"
                                        class="form-control-label">{{ __('Branch Location') }}</label>
                                    <input type="text" name="branch_location" id="branch_location"
                                        class="form-control" value="{{ old('branch_location') }}">
                                </div>
                                <!-- Tax Payer ID input -->
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
            <!-- Submit button -->
            <div class="row">
                <div class="col-12">
                    <button type="submit"
                        class="btn btn-xs badge-blue float-right radius-10px">{{ __('Create') }}</button>
                </div>
            </div>
        </form>
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

        function getDesignation(department_id) {
            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": department_id,
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
