@extends('layouts.admin')
@section('page-title')
    {{ __('Edit Employee') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ route('employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-fluid">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
                </div>
                <div class="card-body employee-detail-edit-body">
                    <div class="row">
                        <!-- Name input -->
                        <div class="form-group col-md-6">
                            <label for="name" class="form-control-label">{{ __('Name') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required
                                value="{{ $employee->name }}">
                        </div>
                        <!-- Phone input -->
                        <div class="form-group col-md-6">
                            <label for="phone" class="form-control-label">{{ __('Phone') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                value="{{ $employee->phone }}">
                        </div>
                        <!-- DOB input -->
                        <div class="form-group col-md-6">
                            <label for="dob" class="form-control-label">{{ __('Date of Birth') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <input type="text" name="dob" id="dob" class="form-control datepicker"
                                value="{{ $employee->dob }}">
                                
                        </div>
                        @if (\Auth::user()->type != 'employee')
                        <!-- Personal Email input -->
                        <div class="form-group col-md-6">
                            <label for="personal_email" class="form-control-label">{{ __('Personal Email') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <input type="email" name="personal_email" id="personal_email" class="form-control"
                                value="{{ $employee->user->personal_email }}">
                        </div>
                        @endif
                        <!-- Gender radio buttons -->
                        <div class="form-group col-md-6">
                            <label class="form-control-label">{{ __('Gender') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <div class="d-flex radio-check">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="g_male" value="Male" name="gender"
                                        class="custom-control-input" {{ $employee->gender == 'Male' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="g_male">{{ __('Male') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="g_female" value="Female" name="gender"
                                        class="custom-control-input" {{ $employee->gender == 'Female' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="g_female">{{ __('Female') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="address" class="form-control-label">{{ __('Address') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <textarea name="address" id="address" class="form-control" rows="2">{{ $employee->address }}</textarea>
                        </div>
                    </div>
                    <!-- Address input -->
                    @if (\Auth::user()->type == 'employee')
                        <input type="submit" value="Update" class="btn-create btn-xs badge-blue radius-10px float-right">
                    @endif
                </div>
            </div>
        </div>
        @if (\Auth::user()->type != 'employee')
            <div class="col-md-6">
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                    </div>
                    <div class="card-body employee-detail-edit-body">
                        <div class="row">
                            @csrf
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
                                    @foreach ($branches as $id => $branch)
                                        <option value="{{ $id }}"
                                            {{ $employee->branch_id == $id ? 'selected' : '' }}>{{ $branch }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Department select -->
                            <div class="form-group col-md-6">
                                <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                                <select name="department_id" id="department_id" class="form-control select2" required>
                                    @foreach ($departments as $id => $department)
                                        <option value="{{ $id }}"
                                            {{ $employee->department_id == $id ? 'selected' : '' }}>{{ $department }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Designation select -->
                            <div class="form-group col-md-6">
                                <label for="designation_id" class="form-control-label">{{ __('Designation') }}</label>
                                <select name="designation_id" id="designation_id" class="form-control select2"
                                    data-toggle="select2" data-placeholder="{{ __('Select Designation ...') }}">
                                    @foreach ($designations as $id => $designation)
                                        <option value="{{ $id }}"
                                            {{ $employee->designation_id == $id ? 'selected' : '' }}>{{ $designation }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Company Date of Joining input -->
                            <div class="form-group col-md-6">
                                <label for="company_doj"
                                    class="form-control-label">{{ __('Company Date Of Joining') }}</label>
                                <input type="text" name="company_doj" id="company_doj"
                                    class="form-control datepicker" required value="{{ $employee->company_doj }}">
                            </div>
                            <!-- Shift Start input -->
                            <div class="form-group col-md-6">
                                <label for="shift_start" class="form-control-label">{{ __('Shift Start') }}</label>
                                <input type="time" name="shift_start" id="shift_start"
                                    class="form-control" required value="{{ $employee->shift_start }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company_doj"
                                class="form-control-label mt-2">{{ __('Salary') }}</label>
                                <input type="number" name="salary" id="salary" class="form-control"
                                                required value="{{ $employee->salary }}">
                            </div>
                            
                            @php
                            $teamLeaderDetails = $employee->getTeamLeaderNameAndId();
                            @endphp
                            <div class="form-group col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="is_team_leader" class="form-control-label mt-2">{{ __('Team Leader') }}</label>
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" name="is_team_leader" id="is_team_leader"
                                            class="mx-2" value="1" {{ old('is_team_leader', $employee->is_team_leader) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="team_leader" class="form-control-label">{{ __('Select Team Leader') }}</label>
                                        <select name="team_leader" id="team_leader" class="form-control select2"
                                            required disabled>
                                            <option value="">{{ !empty($teamLeaderDetails) ? $teamLeaderDetails->name : '' }}</option>
                                        </select>
                                        <input type="hidden" name="team_leader" value="{{ !empty($teamLeaderDetails) ? $teamLeaderDetails->id : $employee->team_leader_id }}" id="hidden_team_leader">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="date_of_exit" class="form-control-label">{{ __('Date Of Exit') }}</label>
                                <input type="date" name="date_of_exit" id="date_of_exit" class="form-control" value="{{ !empty($employee->date_of_exit) ? $employee->date_of_exit : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-6">
                <div class="employee-detail-wrap">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info">
                                        <strong>{{ __('Branch') }}</strong>
                                        <span>{{ !empty($employee->branch) ? $employee->branch->name : '' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info font-style">
                                        <strong>{{ __('Department') }}</strong>
                                        <span>{{ !empty($employee->department) ? $employee->department->name : '' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info font-style">
                                        <strong>{{ __('Designation') }}</strong>
                                        <span>{{ !empty($employee->designation) ? $employee->designation->name : '' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info">
                                        <strong>{{ __('Date Of Joining') }}</strong>
                                        <span>{{ \Auth::user()->dateFormat($employee->company_doj) }}</span>
                                    </div>
                                </div>
                                <!-- Shift Start input -->
                                <div class="col-md-6">
                                    <div class="info">
                                        <strong>{{ __('Shift Start') }}</strong>
                                        <span>{{ $employee->shift_start }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
        <div class="row">
            <div class="col-md-6">
            @if (\Auth::user()->type != 'employee')
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Document') }}</h6>
                    </div>
                    <div class="card-body employee-detail-edit-body">
                        @php
                            $employeedoc = $employee->documents()->pluck('document_value', __('document_id'));
                        @endphp

                        @foreach ($documents as $key => $document)
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="float-left col-4">
                                        <label for="document"
                                            class="float-left pt-1 form-control-label">{{ $document->name }} @if ($document->is_required == 1)
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
                                                <input
                                                    class="form-control @if (!empty($employeedoc[$document->id])) float-left @endif @error('document') is-invalid @enderror border-0"
                                                    @if ($document->is_required == 1 && empty($employeedoc[$document->id])) required @endif
                                                    name="document[{{ $document->id }}]" type="file"
                                                    id="document[{{ $document->id }}]"
                                                    data-filename="{{ $document->id . '_filename' }}">
                                            </label>
                                            <p class="{{ $document->id . '_filename' }}"></p>
                                        </div>

                                        @if (!empty($employeedoc[$document->id]))
                                            <br> <span class="text-xs"><a
                                                    href="{{ !empty($employeedoc[$document->id]) ? asset(Storage::url('uploads/document')) . '/' . $employeedoc[$document->id] : '' }}"
                                                    target="_blank">{{ !empty($employeedoc[$document->id]) ? $employeedoc[$document->id] : '' }}</a>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            </div>
            <div class="col-md-6">
                @if (\Auth::user()->type == 'hr' || (\Auth::user()->type == 'employee' && 
                (empty($employee->account_holder_name) || 
                 empty($employee->account_number) || 
                 empty($employee->bank_name) || 
                 empty($employee->bank_identifier_code) || 
                 empty($employee->branch_location) || 
                 empty($employee->tax_payer_id))))
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
                    </div>
                    <div class="card-body employee-detail-edit-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="account_holder_name" class="form-control-label">Account Holder Name</label>
                                <input type="text" name="account_holder_name" id="account_holder_name"
                                    class="form-control" value="{{ old('account_holder_name', $employee->account_holder_name) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="account_number" class="form-control-label">Account Number</label>
                                <input type="number" name="account_number" id="account_number" class="form-control"
                                    value="{{ old('account_number', $employee->account_number) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bank_name" class="form-control-label">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control"
                                    value="{{ old('bank_name', $employee->bank_name) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bank_identifier_code" class="form-control-label">Bank IFSC Code</label>
                                <input type="text" name="bank_identifier_code" id="bank_identifier_code"
                                    class="form-control" value="{{ old('bank_identifier_code', $employee->bank_identifier_code) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="branch_location" class="form-control-label">Branch Location</label>
                                <input type="text" name="branch_location" id="branch_location" class="form-control"
                                    value="{{ old('branch_location', $employee->branch_location) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tax_payer_id" class="form-control-label">PAN Number</label>
                                <input type="text" name="tax_payer_id" id="tax_payer_id" class="form-control"
                                    value="{{ old('tax_payer_id', $employee->tax_payer_id) }}">
                            </div>
                        </div>

                    </div>
                </div>
                @endif
            </div>
        </div>
    @if (\Auth::user()->type == 'hr' || (\Auth::user()->type == 'employee' && 
        (empty($employee->account_holder_name) || 
         empty($employee->account_number) || 
         empty($employee->bank_name) || 
         empty($employee->bank_identifier_code) || 
         empty($employee->branch_location) || 
         empty($employee->tax_payer_id))))
    <div class="row">
        <div class="col-12">
            <input type="submit" value="{{ __('Update') }}"
                class="btn-create btn-xs badge-blue radius-10px float-right">
        </div>
    </div>
    @endif
    </form>
@endsection

@push('script-page')
    <script type="text/javascript">
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
                        var select = '';
                        if (key == '{{ $employee->designation_id }}') {
                            select = 'selected';
                        }

                        $('#designation_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                }
            });
        }

        $(document).ready(function() {
            var d_id = $('#department_id').val();
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });
        
        $(document).ready(function() {
        $('#date_of_exit').datepicker({
            dateFormat: 'yy-mm-dd',  
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,  
            onClose: function(dateText, inst) {
                if (!dateText) {
                    $(this).val('');
                }
            }
        });
    
        // Force focus after initialization to trigger the calendar
        $('#date_of_exit').focus();
    </script>
@endpush
