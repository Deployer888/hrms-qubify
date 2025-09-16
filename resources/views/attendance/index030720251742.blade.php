@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection
@php
use App\Helpers\Helper;
use Carbon\Carbon;
$requestType = isset($_GET['type']) ? $_GET['type'] : 'daily';
@endphp
<style>
    .not-found td{
        background: gray; /* Light gray */
    }
    .name-lable td{
        background: #1b4f72;
        color: #fff;
        font-size: 16px !important
    }
    .d-flex.radio-check {
        display: inline-flex!important;
        float: inline-end!important;
        padding-top: 10px!important;
    }

    .absent-row td{
        color: white!important;
        background: red!important;
    }

    .weekend-row td {
        background: #00000059!important;
        color:white!important;
        font-weight: bolder!important;
    }

    .holiday-row td {
        background: #21ff0063!important;
        color:black!important;
        font-weight: bolder!important;
    }

    .leave-row td {
        background: gold!important;
        color:black!important;
        font-weight: bolder!important;
    }

    .dateRow th{
        /*background: content-box!important;*/
        background: #525252fc!important;
        color: #fff!important;
        padding: 5px 0!important;
    }

    .employee-name-row th u{
        font-size: larger;
    }
    .time-box {
    color: red !important; /* Change the text color to red */
    background: #fff; /* You can also change the background color if needed */
    border: 1px solid red !important; /* Optional: If you want to change the border color to red */
}
</style>

@section('action-button')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body py-0">
                    <div class="row d-flex justify-content-start mt-2">
                        <div class="col-md-8 col-sm-6">
                            <form method="POST" action="{{ route('attendanceemployee.import') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-5 col-sm-6">
                                        <div class="choose-file form-group">
                                            <label for="clock_in" class="form-control-label">
                                                <div>{{ __('Choose Clock In xlsx file here') }}</div>
                                                <input type="file" class="form-control" name="clock_in_file" id="clock_in"
                                                    data-filename="clock_in_file" accept=".xlsx" required>
                                            </label>
                                            <p class="clock_in_file"></p>
                                        </div>
                                    </div>

                                    <div class="col-md-5 col-sm-6">
                                        <div class="choose-file form-group">
                                            <label for="file" class="form-control-label">
                                                <div>{{ __('Choose Clock Out xlsx file here') }}</div>
                                                <input type="file" class="form-control" name="clock_out_file" id="file"
                                                    data-filename="clock_out_file" accept=".xlsx" required>
                                            </label>
                                            <p class="clock_out_file"></p>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-6">
                                        <button type="submit"  class="btn btn-xs badge-blue radius-10px" >
                                            Import
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <form method="POST" action="{{ route('attendanceemployee.import.rollback') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-7 col-sm-6">
                                        <select name="batch_id" class="form-control select2" required>
                                            <option value="">Select Batch</option>
                                            @foreach (Helper::attendanceBatchList() as $batch_id)
                                                @php
                                                    $batch_date = \Carbon\Carbon::createFromTimestamp($batch_id, 'Asia/Kolkata');
                                                @endphp
                                                <option value="{{ $batch_id }}">
                                                    {{ $batch_date->format('d M Y, h:i:s A') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-5 col-sm-6">
                                        <button type="submit"  class="btn btn-xs badge-blue radius-10px" >
                                            Rollback
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card pb-3">
                <div class="card-body py-0">
                    <form method="GET" action="{{ route('attendanceemployee.index') }}">
                        <div class="row d-flex justify-content-end mt-2">
                            <!-- Form Controls Section -->
                            <div class="col-12 pt-2">
                                <div class="row">
                                    <div class="col-xl-2 col-lg-2 col-6 col-md-2 col-auto">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="daily" value="daily" name="type" class="custom-control-input" onclick="activeDailyBox()" {{ $requestType == 'daily' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="daily">{{ __('Day') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="monthly" value="monthly" name="type" class="custom-control-input" onclick="activeMonthBox()" {{ $requestType == 'monthly' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="monthly">{{ __('Month') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Right Side: Dropdowns and Buttons -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-end col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                        <div class="col-xl-3 col-lg-3 col-6 col-md-3" id="monthAndDate">
                                            @if($requestType == 'daily')
                                            <input type="date" name="date" class="form-control month-btn" value="{{ isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') }}">
                                            @else
                                                <input type="month" name="month" class="month-btn form-control" value="{{ isset($_GET['month']) ? $_GET['month'] : date('Y-m') }}">
                                            @endif
                                        </div>
                                        <!-- Branch, Department, and Employee Dropdowns -->
                                        <div class="col-xl-3 col-lg-3 col-6 col-md-3">
                                            <select name="branch" class="form-control select2">
                                                @foreach ($branch as $branchId => $branchName)
                                                    <option value="{{ $branchId }}" {{ isset($_GET['branch']) && $_GET['branch'] == $branchId ? 'selected' : '' }}>
                                                        {{ $branchName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-xl-3 col-lg-3 col-6 col-md-3">
                                            <select name="department" class="form-control select2">
                                                @foreach ($department as $departmentId => $departmentName)
                                                    <option value="{{ $departmentId }}" {{ isset($_GET['department']) && $_GET['department'] == $departmentId ? 'selected' : '' }}>
                                                        {{ $departmentName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-xl-3 col-lg-3 col-6 col-md-3">
                                            <select name="employee" class="form-control select2">
                                                <option value="" selected>All Employees</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ isset($_GET['employee']) && $_GET['employee'] == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                    <!-- Search and Reset Buttons -->
                                    <div class="col-xl-2 col-lg-2 col-6 col-auto">
                                        <button type="submit" class="apply-btn" title="Search">
                                            <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
                                        </button>
                                        <a href="{{ route('attendanceemployee.index') }}" class="reset-btn" title="Reset">
                                            <span class="btn-inner--icon"><i class="fas fa-sync-alt"></i></span>
                                        </a>
                                    </div>
                                </div>

                                <script>
                                    function activeMonthBox() {
                                        document.getElementById('monthAndDate').innerHTML = `<input type="month" name="month" class="month-btn form-control" value="{{ isset($_GET['month']) ? $_GET['month'] : date('Y-m') }}">`;
                                    }

                                    function activeDailyBox() {
                                        document.getElementById('monthAndDate').innerHTML = `<input type="date" name="date" class="form-control month-btn" value="{{ isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') }}">`;
                                    }
                                </script>
                            </div>
                        </div>
                    </form>

                    <div class="table">
                        <table class="table table-striped mb-0 ">
                            @if($requestType != 'monthly')
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Clock In') }}</th>
                                    <th>{{ __('Clock Out') }}</th>
                                    <th>{{ __('Shift Start') }}</th>
                                    <th>{{ __('Late/Rest') }}</th>
                                    @if (Gate::check('Edit Attendance') || Gate::check('Delete Attendance') && \Auth::user()->type != 'employee')
                                        <th width="3%">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            @endif
                            <tbody id="" class="mb-3">
                                @if(\Auth::user()->type == 'employee')
                                    @if($requestType == 'daily')
                                        @php
                                            $status = $leaveToday ? $leaveToday : ($holidays ? 'HOLIDAY' : ($isWeekend ? 'WEEK-END' : 'Absent'));
                                        @endphp
                                        @if(count($attendanceEmployee)<1 ||  $status == 'Short Leave')
                                        <tr>
                                            <td align="center" colspan="7" class=" @if($leaveToday) btn-warning @endif">{{ $status }}</td>
                                        </tr>
                                        @endif
                                        @foreach ($attendanceEmployee as $attendance)
                                            <tr class="accordion-content">
                                                <td>{{ date('d-m-Y', strtotime($attendance->date)) }}</td>
                                                <td>{{ $attendance->status }}</td>
                                                <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</td>
                                                <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</td>
                                                <td>{{ $attendance->employee->shift_start ?? 'N/A' }}</td>
                                                <td>{{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest == '00:00:00' ? $attendance->late : $attendance->total_rest) }}{{ $attendance->total_rest == '00:00:00' ? ' (Late)' : ' (Rest)' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        {{-- @foreach($dateList as $date)
                                        <tr>
                                            <td align="center" colspan="7" class="{{$date['is_weekend']?'btn-white':'btn-dark'}}">{{ $date['date']}} {{$date['is_weekend']?'(WEEKEND)':''}}</td>
                                        </tr>
                                        @endforeach --}}
                                        @foreach($monthAttendanceEmployee as $key => $attEmp)
                                        @if($attEmp['is_weekend'])
                                            <tr>
                                                <td align="center" colspan="7" class="btn-white">{{ $key }} (WEEKEND)</td>
                                            </tr>
                                        @elseif($attEmp['is_leave'])
                                            <tr>
                                                <td align="center" colspan="7" class="btn-warning">{{ $key }} (LEAVE)</td>
                                            </tr>
                                        @elseif(!$attEmp['is_weekend'] && !$attEmp['is_leave'])
                                            <tr>
                                                <td align="center" colspan="7" data-toggle="collapse" data-target="#collapse-{{ $key }}" role="button"  style="background: linear-gradient(135deg, #3a8ef6, #6259ca); /* Light gray */
                                                color: #fff;">{{ $key }}</td>
                                            </tr>
                                            <tr class="collapse" id="collapse-{{ $key }}">
                                                <td colspan="7">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('Date') }}</th>
                                                                <th>{{ __('Status') }}</th>
                                                                <th>{{ __('Clock In') }}</th>
                                                                <th>{{ __('Clock Out') }}</th>
                                                                <th>{{ __('Shift Start') }}</th>
                                                                <th>{{ __('Late/Rest') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($attEmp['attendance'] as $attendance)
                                                                <tr class="accordion-content">
                                                                    <td>{{ date('d-m-Y', strtotime($attendance['date'])) }}</td>
                                                                    <td>{{ $attendance['status'] }}</td>
                                                                    <td>{{ $attendance['clock_in'] != '00:00:00' ? \Auth::user()->timeFormat($attendance['clock_in']) : '00:00' }}</td>
                                                                    <td>{{ $attendance['clock_out'] != '00:00:00' ? \Auth::user()->timeFormat($attendance['clock_out']) : '00:00' }}</td>
                                                                    <td>{{ $attendance['employee']['shift_start'] ?? 'N/A' }}</td>
                                                                    <td>{{ Helper::convertTimeToMinutesAndSeconds($attendance['total_rest'] == '00:00:00' ? $attendance['late'] : $attendance['total_rest']) }}{{ $attendance['total_rest'] == '00:00:00' ? ' (Late)' : ' (Rest)' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @endif
                                @else
                                    @if($holidays || $isWeekend)
                                        @if($holidays)
                                        <tr>
                                            <td colspan="7">
                                                HOLIDAY
                                            </td>
                                        </tr>
                                        @endif
                                        @if($isWeekend)
                                        <tr>
                                            <td colspan="7">
                                            WEEK-END
                                            </td>
                                        </tr>
                                        @endif
                                    @else
                                        @foreach ($attendanceWithEmployee as $employee)
                                            <?php
                                            $isAbsent = $employee->attendance->isEmpty();
                                            $isLeave = Helper::checkLeave($date?$date:today(), $employee->id);
                                            if($isLeave != 0){
                                                $leaveToday = Helper::checkLeaveWithTypes($date?$date:today(), $employee->id);
                                                if($leaveToday == 'afternoon halfday' || $leaveToday == 'morning halfday'){
                                                    $leaveToday = 'Half-Day Leave';
                                                }elseif($leaveToday == 'on short leave'){
                                                    $leaveToday = 'Short Leave';
                                                }elseif($leaveToday == 'fullday Leave'){
                                                    $leaveToday = 'Leave';
                                                }
                                            }else{
                                                $leaveToday = 0;
                                            }
                                            $totalTime = Helper::calculateTotalTimeDifference($employee->attendance);
                                            $threshold = '08:00'; // Default threshold
                                            if ($leaveToday == 'Half-Day Leave') {
                                                $threshold = '04:00';
                                            } elseif ($leaveToday == 'Short Leave') {
                                                $threshold = '06:00';
                                            }?>
                                            <tr class="name-lable">
                                                <td colspan="7" align="center">
                                                    <div style="display: flex; align-items: center; justify-content: center; width: 100%; position: relative;">
                                                        <span>
                                                            {{ $employee->name }}
                                                            (<strong>
                                                                Total Time:
                                                                <i class="{{ $totalTime < $threshold ? 'text-danger' : '' }}">
                                                                    {{ $totalTime }}
                                                                </i>
                                                            </strong>)

                                                            @if($leaveToday && $leaveToday != 0)
                                                                <span class="bg-warning" style="color: #FFF !important; padding: 5px; border-radius: 10px; background:#ffac04 !important;">
                                                                    {{ $leaveToday }}
                                                                </span>
                                                            @endif
                                                        </span>
                                                        @if(!count($employee->attendance ?? []))
                                                        <a href="#"
                                                           data-url="{{ URL::to('attendanceemployee/create?employee_id=' . $employee->id . '&date=' . (isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'))) }}"
                                                           data-size="lg"
                                                           data-ajax-popup="true"
                                                           data-title="{{ __('Copy Attendance') }}"
                                                           class="edit-icon bg-warning"
                                                           data-toggle="tooltip"
                                                           title="{{ __('Copy') }}"
                                                           style="position: absolute; right: 10px;">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>

                                            </tr>

                                            @forelse ($employee->attendance ?? [] as $key=>$attendance)
                                                <tr class="accordion-content">
                                                    <td>{{ date('d-m-Y', strtotime($attendance->date)) }}</td>
                                                    <td>{{ $attendance->status }}</td>
                                                    <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</td>
                                                    <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</td>
                                                    <td>{{ $employee->shift_start ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($key)
                                                            {{Helper::dynRestTime($employee->attendance[$key-1]->clock_out??'',$employee->attendance[$key]->clock_in)}}
                                                        @else
                                                            {{Helper::dynLateTime($employee->shift_start??'09:00:00',$attendance->clock_in)}}
                                                        @endif
                                                    </td>
                                                    <td class="text-right action-btns">
                                                        @if($attendance->clock_out != '00:00:00')
                                                            <a href="#" data-url="{{ URL::to('copy/attendance/' . $attendance->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Copy Attendance') }}" class="edit-icon" data-toggle="tooltip" title="{{ __('Copy') }}"><i class="far fa-copy"></i></a>
                                                        @endif
                                                        @can('Edit Attendance')
                                                            <a href="#" data-url="{{ URL::to('attendanceemployee/' . $attendance->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Attendance') }}" class="edit-icon" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                                                        @endcan
                                                        @can('Delete Attendance')
                                                            <a href="#" class="delete-icon" data-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') . '|' . __('This action cannot be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-{{ $attendance->id }}').submit();"><i class="fas fa-trash"></i></a>
                                                            <form method="POST" action="{{ route('attendanceemployee.destroy', $attendance->id) }}" id="delete-form-{{ $attendance->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                            <tr class="{{ $leaveToday ? 'leave-row' : 'absent-row' }}">
                                                <td colspan="7" align="center">{{$leaveToday?$leaveToday:'Absent'}}</td>
                                            </tr>
                                            @endforelse

                                        @endforeach
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endsection
        @push('script-page')
        <script>
            $(document).ready(function() {
                $('select[name="branch"]').on('change', function() {
                    var branchId = $(this).val();  // Get the selected branch ID

                    if (branchId) {
                        $.ajax({
                            url: '{{ route("getDepartmentsByBranch") }}',  // Laravel route for fetching departments and employees
                            type: 'GET',
                            data: { branch_id: branchId },  // Send the selected branch_id to the backend
                            success: function(response) {
                                console.log(response);

                                if (response.status === 'success') {
                                    var departmentSelect = $('select[name="department"]');
                                    var employeeSelect = $('select[name="employee"]');

                                    departmentSelect.empty();  // Clear existing department options
                                    employeeSelect.empty();  // Clear existing employee options

                                    departmentSelect.append('<option value="" disabled selected>Select Department</option>');  // Add placeholder option
                                    employeeSelect.append('<option value="" disabled selected>Select Employee</option>');  // Add placeholder option

                                    // Loop through departments and append them to the department select box
                                    $.each(response.data.departments, function(key, department) {
                                        departmentSelect.append('<option value="' + department.id + '">' + department.name + '</option>');
                                    });

                                    // Loop through employees and append them to the employee select box
                                    $.each(response.data.employees, function(key, employee) {
                                        employeeSelect.append('<option value="' + employee.id + '">' + employee.name + '</option>');
                                    });
                                } else {
                                    $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                                    $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                                }
                            },
                            error: function() {
                                // alert('Error fetching departments and employees.');
                                $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                                $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                            }
                        });
                    } else {
                        // If no branch is selected, clear the department and employee dropdowns
                        $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                        $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                    }
                });
            });
            $(document).ready(function() {
                $('select[name="department"]').on('change', function() {
                    var departmentId = $(this).val();  // Get the selected branch ID
                    if (departmentId) {
                        $.ajax({
                            url: '{{ route("getEmployeeByDepartment") }}',  // Laravel route for fetching departments and employees
                            type: 'GET',
                            data: {
                                department_id: departmentId,
                             },  // Send the selected branch_id to the backend
                            success: function(response) {
                                console.log(response);

                                if (response.status === 'success') {
                                    var departmentSelect = $('select[name="department"]');
                                    var employeeSelect = $('select[name="employee"]');

                                    employeeSelect.empty();  // Clear existing employee options

                                    employeeSelect.append('<option value="" disabled selected>Select Employee</option>');  // Add placeholder option

                                    // Loop through employees and append them to the employee select box
                                    $.each(response.data.employees, function(key, employee) {
                                        employeeSelect.append('<option value="' + employee.id + '">' + employee.name + '</option>');
                                    });
                                } else {
                                    // alert('Error: ' + response.message);  // Show error if no departments or employees found
                                    $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                                }
                            },
                            error: function() {
                                $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                                // alert('Error fetching departments and employees.');
                            }
                        });
                    } else {
                        $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                    }
                });
            });
        </script>
        @endpush

