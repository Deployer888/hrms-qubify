@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Employee Salary List') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <section class="nav-tabs">
                <div class="col-lg-12 our-system">
                    <div class="row">
                        <ul class="nav nav-tabs my-4">
                            <li>
                                <a data-toggle="tab" href="#salary" class="active">{{ __('Salary') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#allowance" class="">{{ __('Allowance') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#commission" class="">{{ __('Commission') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#loan" class="">{{ __('Loan') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#saturation-deduction"
                                    class="">{{ __('Saturation Deduction') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#other-payment" class="">{{ __('Other Payment') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#overtime" class="">{{ __('Overtime') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div id="salary" class="tab-pane in active">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('employee.salary.update', $employee->id) }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="salary_type"
                                                    class="form-control-label">{{ __('Payslip Type*') }}</label>
                                                <select name="salary_type" id="salary_type" class="form-control select2"
                                                    required>
                                                    @foreach ($payslip_type as $key => $type)
                                                        <option value="{{ $key }}"
                                                            {{ $employee->salary_type == $key ? 'selected' : '' }}>
                                                            {{ $type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="salary"
                                                    class="form-control-label">{{ __('Salary') }}</label>
                                                <input type="number" name="salary" id="salary" class="form-control"
                                                    required value="{{ $employee->salary }}">
                                            </div>
                                        </div>
                                    </div>
                                    @can('Create Set Salary')
                                        <div class="row">
                                            <div class="col-12 text-right mt-1">
                                                <input type="submit" value="{{ __('Save Change') }}"
                                                    class="btn-create badge-blue">
                                            </div>
                                        </div>
                                    @endcan
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="allowance" class="tab-pane">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ url('allowance') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="allowance_option"
                                                    class="form-control-label">{{ __('Allowance Options*') }}</label>
                                                <select name="allowance_option" id="allowance_option"
                                                    class="form-control select2" required>
                                                    @foreach ($allowance_options as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="title"
                                                    class="form-control-label">{{ __('Title') }}</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="amount"
                                                    class="form-control-label">{{ __('Amount') }}</label>
                                                <input type="number" name="amount" id="amount" class="form-control"
                                                    required step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    @can('Create Allowance')
                                        <div class="row">
                                            <div class="col-12 text-right mt-1">
                                                <input type="submit" value="{{ __('Save Change') }}"
                                                    class="btn-create badge-blue">
                                            </div>
                                        </div>
                                    @endcan
                                </form>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0" id="allowance-dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Employee Name') }}</th>
                                                <th>{{ __('Allowance Option') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th width="3%">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-style">
                                            @foreach ($allowances as $allowance)
                                                <tr>
                                                    <td>{{ $allowance->employee()->name }}</td>
                                                    <td>{{ $allowance->allowance_option()->name }}</td>
                                                    <td>{{ $allowance->title }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($allowance->amount) }}</td>
                                                    @can('Delete Set Salary')
                                                        <td>
                                                            @can('Edit Allowance')
                                                                <a href="#"
                                                                    data-url="{{ URL::to('allowance/' . $allowance->id . '/edit') }}"
                                                                    data-size="lg" data-ajax-popup="true"
                                                                    data-title="{{ __('Edit Allowance') }}" class="edit-icon"
                                                                    data-bs-toggle="tooltip"
                                                                    title="{{ __('Edit') }}"><i
                                                                        class="fas fa-pencil-alt"></i></a>
                                                            @endcan
                                                            @can('Delete Allowance')
                                                                <a href="#" class="delete-icon" data-bs-toggle="tooltip"
                                                                    title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="document.getElementById('allowance-delete-form-{{ $allowance->id }}').submit();"><i
                                                                        class="fas fa-trash"></i></a>
                                                                <form action="{{ route('allowance.destroy', $allowance->id) }}"
                                                                    method="post"
                                                                    id="allowance-delete-form-{{ $allowance->id }}">
                                                                    @method('DELETE')
                                                                    @csrf
                                                                </form>
                                                            @endcan
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="commission" class="tab-pane">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ url('commission') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="title"
                                                    class="form-control-label">{{ __('Title') }}</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="amount"
                                                    class="form-control-label">{{ __('Amount') }}</label>
                                                <input type="number" name="amount" id="amount" class="form-control"
                                                    required step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    @can('Create Commission')
                                        <div class="row">
                                            <div class="col-12 text-right mt-1">
                                                <input type="submit" value="{{ __('Save Change') }}"
                                                    class="btn-create badge-blue">
                                            </div>
                                        </div>
                                    @endcan
                                </form>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0" id="commission-dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Employee Name') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th width="3%">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-style">
                                            @foreach ($commissions as $commission)
                                                <tr>
                                                    <td>{{ $commission->employee()->name }}</td>
                                                    <td>{{ $commission->title }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($commission->amount) }}</td>

                                                    <td class="text-right">
                                                        @can('Edit Commission')
                                                            <a href="#"
                                                                data-url="{{ URL::to('commission/' . $commission->id . '/edit') }}"
                                                                data-size="lg" data-ajax-popup="true"
                                                                data-title="{{ __('Edit Commission') }}" class="edit-icon"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"><i
                                                                    class="fas fa-pencil-alt"></i></a>
                                                        @endcan
                                                        @can('Delete Commission')
                                                            <a href="#" class="delete-icon" data-bs-toggle="tooltip"
                                                                title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('commission-delete-form-{{ $commission->id }}').submit();"><i
                                                                    class="fas fa-trash"></i></a>
                                                            <form action="{{ route('commission.destroy', $commission->id) }}"
                                                                method="post"
                                                                id="commission-delete-form-{{ $commission->id }}">
                                                                @method('DELETE')
                                                                @csrf
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="loan" class="tab-pane">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ url('loan') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="loan_option"
                                                    class="form-control-label">{{ __('Loan Options*') }}</label>
                                                <select name="loan_option" id="loan_option" class="form-control select2"
                                                    required>
                                                    @foreach ($loan_options as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="title"
                                                    class="form-control-label">{{ __('Title') }}</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="amount"
                                                    class="form-control-label">{{ __('Loan Amount') }}</label>
                                                <input type="number" name="amount" id="amount" class="form-control"
                                                    required step="0.01">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="start_date"
                                                    class="form-control-label">{{ __('Start Date') }}</label>
                                                <input type="text" name="start_date" id="start_date"
                                                    class="form-control datepicker" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="end_date"
                                                    class="form-control-label">{{ __('End Date') }}</label>
                                                <input type="text" name="end_date" id="end_date"
                                                    class="form-control datepicker" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="reason"
                                                    class="form-control-label">{{ __('Reason') }}</label>
                                                <textarea name="reason" id="reason" class="form-control" rows="1" required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    @can('Create Loan')
                                        <div class="row">
                                            <div class="col-12 text-right mt-1">
                                                <input type="submit" value="{{ __('Save Change') }}"
                                                    class="btn-create badge-blue">
                                            </div>
                                        </div>
                                    @endcan
                                </form>

                                <hr>

                                <div class="table-responsive">
                                    <table class="table table-striped mb-0" id="loan-dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('employee') }}</th>
                                                <th>{{ __('Loan Options') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Loan Amount') }}</th>
                                                <th>{{ __('Start Date') }}</th>
                                                <th>{{ __('End Date') }}</th>
                                                <th width="3%">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-style">
                                            @foreach ($loans as $loan)
                                                <tr>
                                                    <td>{{ $loan->employee()->name }}</td>
                                                    <td>{{ $loan->loan_option()->name }}</td>
                                                    <td>{{ $loan->title }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($loan->amount) }}</td>
                                                    <td>{{ \Auth::user()->dateFormat($loan->start_date) }}</td>
                                                    <td>{{ \Auth::user()->dateFormat($loan->end_date) }}</td>

                                                    <td class="text-right">
                                                        @can('Edit Loan')
                                                            <a href="#"
                                                                data-url="{{ URL::to('loan/' . $loan->id . '/edit') }}"
                                                                data-size="lg" data-ajax-popup="true"
                                                                data-title="{{ __('Edit Loan') }}" class="edit-icon"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"><i
                                                                    class="fas fa-pencil-alt"></i></a>
                                                        @endcan
                                                        @can('Delete Loan')
                                                            <a href="#" class="delete-icon" data-bs-toggle="tooltip"
                                                                title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('loan-delete-form-{{ $loan->id }}').submit();"><i
                                                                    class="fas fa-trash"></i></a>
                                                            <form action="{{ route('loan.destroy', $loan->id) }}"
                                                                method="post" id="loan-delete-form-{{ $loan->id }}">
                                                                @method('DELETE')
                                                                @csrf
                                                            </form>
                                                        @endcan
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="saturation-deduction" class="tab-pane">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ url('saturationdeduction') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="deduction_option"
                                                    class="form-control-label">{{ __('Deduction Options*') }}</label>
                                                <select name="deduction_option" id="deduction_option"
                                                    class="form-control select2" required>
                                                    @foreach ($deduction_options as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="title"
                                                    class="form-control-label">{{ __('Title') }}</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="amount"
                                                    class="form-control-label">{{ __('Amount') }}</label>
                                                <input type="number" name="amount" id="amount" class="form-control"
                                                    required step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    @can('Create Saturation Deduction')
                                        <div class="row">
                                            <div class="col-12 text-right mt-1">
                                                <input type="submit" value="{{ __('Save Change') }}"
                                                    class="btn-create badge-blue">
                                            </div>
                                        </div>
                                    @endcan
                                </form>

                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0" id="saturation-deduction-dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Employee Name') }}</th>
                                                <th>{{ __('Deduction Option') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th width="3%">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-style">
                                            @foreach ($saturationdeductions as $saturationdeduction)
                                                <tr>
                                                    <td>{{ $saturationdeduction->employee()->name }}</td>
                                                    <td>{{ $saturationdeduction->deduction_option()->name }}</td>
                                                    <td>{{ $saturationdeduction->title }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($saturationdeduction->amount) }}
                                                    </td>
                                                    <td class="text-right">
                                                        @can('Edit Saturation Deduction')
                                                            <a href="#"
                                                                data-url="{{ URL::to('saturationdeduction/' . $saturationdeduction->id . '/edit') }}"
                                                                data-size="lg" data-ajax-popup="true"
                                                                data-title="{{ __('Edit Saturation Deduction') }}"
                                                                class="edit-icon" data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"><i
                                                                    class="fas fa-pencil-alt"></i></a>
                                                        @endcan
                                                        @can('Delete Saturation Deduction')
                                                            <a href="#" class="delete-icon" data-bs-toggle="tooltip"
                                                                title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('deduction-delete-form-{{ $saturationdeduction->id }}').submit();"><i
                                                                    class="fas fa-trash"></i></a>
                                                            <form
                                                                action="{{ route('saturationdeduction.destroy', $saturationdeduction->id) }}"
                                                                method="post"
                                                                id="deduction-delete-form-{{ $saturationdeduction->id }}">
                                                                @method('DELETE')
                                                                @csrf
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="other-payment" class="tab-pane">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ url('otherpayment') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="title"
                                                    class="form-control-label">{{ __('Title') }}</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="amount"
                                                    class="form-control-label">{{ __('Amount') }}</label>
                                                <input type="number" name="amount" id="amount" class="form-control"
                                                    required step="0.01">
                                            </div>
                                        </div>
                                    </div>

                                    @can('Create Other Payment')
                                        <div class="row">
                                            <div class="col-12 text-right mt-1">
                                                <input type="submit" value="{{ __('Save Change') }}"
                                                    class="btn-create badge-blue">
                                            </div>
                                        </div>
                                    @endcan
                                </form>


                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0" id="other-payment-dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('employee') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th width="3%">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-style">
                                            @foreach ($otherpayments as $otherpayment)
                                                <tr>
                                                    <td>{{ $otherpayment->employee()->name }}</td>
                                                    <td>{{ $otherpayment->title }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($otherpayment->amount) }}</td>

                                                    <td class="text-right">
                                                        @can('Edit Other Payment')
                                                            <a href="#"
                                                                data-url="{{ URL::to('otherpayment/' . $otherpayment->id . '/edit') }}"
                                                                data-size="lg" data-ajax-popup="true"
                                                                data-title="{{ __('Edit Other Payment') }}"
                                                                class="edit-icon" data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"><i
                                                                    class="fas fa-pencil-alt"></i></a>
                                                        @endcan
                                                        @can('Delete Other Payment')
                                                            <a href="#" class="delete-icon" data-bs-toggle="tooltip"
                                                                title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('payment-delete-form-{{ $otherpayment->id }}').submit();"><i
                                                                    class="fas fa-trash"></i></a>
                                                            <form
                                                                action="{{ route('otherpayment.destroy', $otherpayment->id) }}"
                                                                method="post"
                                                                id="payment-delete-form-{{ $otherpayment->id }}">
                                                                @method('DELETE')
                                                                @csrf
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="overtime" class="tab-pane">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ url('overtime') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="title"
                                                    class="form-control-label">{{ __('Overtime Title*') }}</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="number_of_days"
                                                    class="form-control-label">{{ __('Number of days') }}</label>
                                                <input type="number" name="number_of_days" id="number_of_days"
                                                    class="form-control" required step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="hours"
                                                    class="form-control-label">{{ __('Hours') }}</label>
                                                <input type="number" name="hours" id="hours" class="form-control"
                                                    required step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="rate"
                                                    class="form-control-label">{{ __('Rate') }}</label>
                                                <input type="number" name="rate" id="rate" class="form-control"
                                                    required step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    @can('Create Overtime')
                                        <div class="row">
                                            <div class="col-12 text-right mt-1">
                                                <input type="submit" value="{{ __('Save Change') }}"
                                                    class="btn-create badge-blue">
                                            </div>
                                        </div>
                                    @endcan
                                </form>

                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0" id="overtime-dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Employee Name') }}</th>
                                                <th>{{ __('Overtime Title') }}</th>
                                                <th>{{ __('Number of days') }}</th>
                                                <th>{{ __('Hours') }}</th>
                                                <th>{{ __('Rate') }}</th>
                                                <th width="3%">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-style">
                                            @foreach ($overtimes as $overtime)
                                                <tr>
                                                    <td>{{ $overtime->employee()->name }}</td>
                                                    <td>{{ $overtime->title }}</td>
                                                    <td>{{ $overtime->number_of_days }}</td>
                                                    <td>{{ $overtime->hours }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($overtime->rate) }}</td>
                                                    <td class="text-right">
                                                        @can('Edit Overtime')
                                                            <a href="#"
                                                                data-url="{{ URL::to('overtime/' . $overtime->id . '/edit') }}"
                                                                data-size="lg" data-ajax-popup="true"
                                                                data-title="{{ __('Edit Overtime') }}" class="edit-icon"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"><i
                                                                    class="fas fa-pencil-alt"></i></a>
                                                        @endcan
                                                        @can('Delete Overtime')
                                                            <a href="#" class="delete-icon" data-bs-toggle="tooltip"
                                                                title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('overtime-delete-form-{{ $overtime->id }}').submit();"><i
                                                                    class="fas fa-trash"></i></a>
                                                            <form action="{{ route('overtime.destroy', $overtime->id) }}"
                                                                method="post"
                                                                id="overtime-delete-form-{{ $overtime->id }}">
                                                                @method('DELETE')
                                                                @csrf
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('script-page')
    <script type="text/javascript">
        $(document).ready(function() {
            var d_id = $('#department_id').val();
            var designation_id = '{{ $employee->designation_id }}';
            getDesignation(d_id);


            $("#allowance-dataTable").dataTable({
                "columnDefs": [{
                    "sortable": false,
                    "targets": [1]
                }]
            });

            $("#commission-dataTable").dataTable({
                "columnDefs": [{
                    "sortable": false,
                    "targets": [1]
                }]
            });

            $("#loan-dataTable").dataTable({
                "columnDefs": [{
                    "sortable": false,
                    "targets": [1]
                }]
            });

            $("#saturation-deduction-dataTable").dataTable({
                "columnDefs": [{
                    "sortable": false,
                    "targets": [1]
                }]
            });

            $("#other-payment-dataTable").dataTable({
                "columnDefs": [{
                    "sortable": false,
                    "targets": [1]
                }]
            });

            $("#overtime-dataTable").dataTable({
                "columnDefs": [{
                    "sortable": false,
                    "targets": [1]
                }]
            });


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
    </script>
@endpush
