@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Bulk Attendance') }}
@endsection
@push('script-page')
    <script>
        $(document).ready(function() {
            // Date picker initialization
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });

            // Handle 'present_all' checkbox toggle
            $('#present_all').change(function() {
                const isChecked = this.checked;
                $('.present').prop('checked', isChecked);
                $('.present_check_in').toggleClass('d-none', !isChecked);
            });

            // Handle individual 'present' checkbox toggle
            $('.present').change(function() {
                const presentCheckInDiv = $(this).closest('tr').find('.present_check_in');
                presentCheckInDiv.toggleClass('d-none', !this.checked);
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body py-0">
                    <!-- Filter form -->
                    <form action="{{ route('attendanceemployee.bulkattendance') }}" method="get" id="bulkattendance_filter">
                        @csrf
                        <div class="row d-flex justify-content-end py-0">
                            <!-- Date input -->
                            <div class="col-xl-2 col-lg-2 col-md-6">
                                <div class="all-select-box">
                                    <div class="btn-box">
                                        <label for="date" class="text-type">{{ __('Date') }}</label>
                                        <input type="text" name="date" id="date"
                                            value="{{ request('date', date('Y-m-d')) }}"
                                            class="month-btn form-control datepicker">
                                    </div>
                                </div>
                            </div>
                            <!-- Branch input -->
                            <div class="col-xl-2 col-lg-3 col-md-6">
                                <div class="all-select-box">
                                    <div class="btn-box">
                                        <label for="branch" class="text-type">{{ __('Branch') }}</label>
                                        <select name="branch" id="branch" class="form-control select2" required>
                                            @if(isset($branches) && count($branches) > 0)
                                            @foreach ($branches as $id => $name)
                                                <option value="{{ $id }}"
                                                    {{ request('branch') == $id ? 'selected' : '' }}>{{ $name }}
                                                </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Department input -->
                            <div class="col-xl-2 col-lg-3 col-md-6">
                                <div class="all-select-box">
                                    <div class="btn-box">
                                        <label for="department" class="text-type">{{ __('Department') }}</label>
                                        <select name="department" id="department" class="form-control select2" required>
                                            @if(isset($departments) && count($departments) > 0)
                                            @foreach ($departments as $id => $name)
                                                <option value="{{ $id }}"
                                                    {{ request('department') == $id ? 'selected' : '' }}>
                                                    {{ $name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Apply and Reset buttons -->
                            <div class="col-auto my-auto">
                                <button type="submit" class="apply-btn" data-toggle="tooltip" title="{{ __('Apply') }}">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('timesheet.index') }}" class="reset-btn" data-toggle="tooltip"
                                    title="{{ __('Reset') }}">
                                    <i class="fas fa-trash-restore-alt"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Attendance form -->
                    <form action="{{ route('attendanceemployee.bulkattendance') }}" method="post">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-striped mb-0 dataTable">
                                <thead>
                                    <tr>
                                        <th width="10%">{{ __('Employee Id') }}</th>
                                        <th>{{ __('Employee') }}</th>
                                        <th>{{ __('Branch') }}</th>
                                        <th>{{ __('Department') }}</th>
                                        <th>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="present_all"
                                                    name="present_all">
                                                <label class="custom-control-label"
                                                    for="present_all">{{ __('Attendance') }}</label>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $employee)
                                        @php
                                            $attendance = $employee->present_status(
                                                $employee->id,
                                                request('date', date('Y-m-d')),
                                            );
                                        @endphp
                                        <tr>
                                            <!-- Employee ID -->
                                            <td>
                                                <input type="hidden" name="employee_id[]" value="{{ $employee->id }}">
                                                <a
                                                    href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                            </td>
                                            <!-- Employee name -->
                                            <td>{{ $employee->name }}</td>
                                            <!-- Branch name -->
                                            <td>{{ $employee->branch ? $employee->branch->name : '' }}</td>
                                            <!-- Department name -->
                                            <td>{{ $employee->department ? $employee->department->name : '' }}</td>
                                            <!-- Attendance -->
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input present"
                                                                name="present-{{ $employee->id }}"
                                                                id="present{{ $employee->id }}"
                                                                {{ $attendance && $attendance->status == 'Present' ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="present{{ $employee->id }}"></label>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-md-8 present_check_in {{ $attendance ? '' : 'd-none' }}">
                                                        <div class="row">
                                                            <!-- Check-in time -->
                                                            <label class="col-md-2 control-label"
                                                                for="in-{{ $employee->id }}">{{ __('In') }}</label>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control timepicker"
                                                                    name="in-{{ $employee->id }}"
                                                                    value="{{ $attendance && $attendance->clock_in != '00:00:00' ? $attendance->clock_in : \Utility::getValByName('company_start_time') }}">
                                                            </div>
                                                            <!-- Check-out time -->
                                                            <label class="col-md-2 control-label"
                                                                for="out-{{ $employee->id }}">{{ __('Out') }}</label>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control timepicker"
                                                                    name="out-{{ $employee->id }}"
                                                                    value="{{ $attendance && $attendance->clock_out != '00:00:00' ? $attendance->clock_out : \Utility::getValByName('company_end_time') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Submit button -->
                        <div class="attendance-btn text-right pt-4">
                            <input type="hidden" name="date" value="{{ request('date', date('Y-m-d')) }}">
                            <input type="hidden" name="branch" value="{{ request('branch') }}">
                            <input type="hidden" name="department" value="{{ request('department') }}">
                            <button type="submit" class="btn-create badge-blue">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
