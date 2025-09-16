@extends('layouts.admin')
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Employee') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></div>
                    <div class="breadcrumb-item">{{ __('Employee') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="section-body">
                    <div class="row">
                        <div class="col-md-6 ">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Personal Detail') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}</label><span
                                            class="text-danger pl-1">*</span>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="dob">{{ __('Date of Birth') }}</label>
                                                <input type="text" name="dob" id="dob"
                                                    class="form-control datepicker">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="gender">{{ __('Gender') }}</label><span
                                                    class="text-danger pl-1">*</span><br>
                                                <input type="radio" name="gender" value="Male" checked>
                                                {{ __('Male') }}
                                                &nbsp&nbsp&nbsp
                                                <input type="radio" name="gender" value="Female"> {{ __('Female') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone') }}</label><span
                                            class="text-danger pl-1">*</span>
                                        <input type="number" name="phone" id="phone" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="address">{{ __('Address') }}</label>
                                        <textarea name="address" id="address" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}</label><span
                                            class="text-danger pl-1">*</span>
                                        <input type="email" name="email" id="email" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">{{ __('Password') }}</label><span
                                            class="text-danger pl-1">*</span>
                                        <input type="text" name="password" id="password" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Company Detail') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="employee_id">{{ __('Employee ID') }}</label>
                                        <input type="text" name="employee_id" id="employee_id" class="form-control"
                                            disabled
                                            value="{{ \Illuminate\Support\Facades\Auth::user()->employeeIdFormat(1) }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="branch_id">{{ __('Branch') }}</label>
                                        <select name="branch_id" id="branch_id" class="form-control select2" required>
                                            <!-- Options will be populated here -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="department_id">{{ __('Department') }}</label>
                                        <select name="department_id" id="department_id" class="form-control select2"
                                            required>
                                            <!-- Options will be populated here -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="designation_id">{{ __('Designation') }}</label>
                                        <select name="designation_id" id="designation_id" class="select2 form-control"
                                            data-placeholder="{{ __('Select Designation ...') }}">
                                            <option value="">Select Designation</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="company_doj">{{ __('Company Date Of Joining') }}</label>
                                        <input type="text" name="company_doj" id="company_doj"
                                            class="form-control datepicker" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 ">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Document') }}</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($documents as $key => $document)
                                        <div class="row">
                                            <div class="form-group col-10">
                                                <div class="float-left">
                                                    <label for="document[{{ $document->id }}]"
                                                        class="float-left pt-1">{{ $document->name }}
                                                        @if ($document->is_required == 1)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                </div>
                                                <div class="float-right">
                                                    <input type="file" name="document[{{ $document->id }}]"
                                                        id="document[{{ $document->id }}]"
                                                        class="form-control float-right border-0"
                                                        @if ($document->is_required == 1) required @endif accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Bank Account Detail') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="account_holder_name">{{ __('Account Holder Name') }}</label>
                                        <input type="text" name="account_holder_name" id="account_holder_name"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="account_number">{{ __('Account Number') }}</label>
                                        <input type="text" name="account_number" id="account_number"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="bank_name">{{ __('Bank Name') }}</label>
                                        <input type="text" name="bank_name" id="bank_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="bank_identifier_code">{{ __('Bank Identifier Code') }}</label>
                                        <input type="text" name="bank_identifier_code" id="bank_identifier_code"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="branch_location">{{ __('Branch Location') }}</label>
                                        <input type="text" name="branch_location" id="branch_location"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="tax_payer_id">{{ __('Tax Payer Id') }}</label>
                                        <input type="text" name="tax_payer_id" id="tax_payer_id"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="submit" value="save" class="btn btn-primary btn-lg float-right">
            </form>
        </section>
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
                    $('#designation_id').append('<option value="">Select any Designation</option>');
                    $.each(data, function(key, value) {
                        $('#designation_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                }
            });
        }
    </script>
@endpush
