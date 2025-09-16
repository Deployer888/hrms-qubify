@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection
@php
use App\Helpers\Helper;
use Carbon\Carbon;

@endphp
<style> 
    .not-found td{
        background-color: gray; /* Light gray */
    }
    .name-lable td{
        background-color: #004041; /* Light gray */
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
        background-color: #525252fc!important;
        color: #fff!important;
        padding: 5px 0!important;
    }

    .employee-name-row th u{
        font-size: larger;
    }
    .time-box {
    color: red !important; /* Change the text color to red */
    background-color: #fff; /* You can also change the background color if needed */
    border: 1px solid red !important; /* Optional: If you want to change the border color to red */
}
</style>

@section('action-button')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card pb-3">
                <div class="card-body py-0">
                    <form method="GET" action="{{ route('attendanceemployee.index') }}">
                        <div class="row d-flex justify-content-end mt-2">
                            <!-- Form Controls Section -->
                            <div class="d-flex justify-content-end pt-2">
                                <!-- Date Input -->

                                @if(\Auth::user()->type == 'employee')
                                <?php
                                    
                                $isAbsent = $attendanceEmployee->isEmpty();
                                $isLeave = Helper::checkLeave($date?$date:today(), $employee);
                                if($isLeave != 0){
                                    $leaveToday = Helper::checkLeaveWithTypes($date?$date:today(), $employee);
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
                                $totalTime = Helper::calculateTotalTimeDifference($attendanceEmployee);
                                $threshold = '08:00'; // Default threshold
                                if ($leaveToday == 'Half-Day Leave') {
                                    $threshold = '04:00';
                                } elseif ($leaveToday == 'Short Leave') {
                                    $threshold = '06:00';
                                }?>
                                <div class="col-xl-3 col-lg-3">
                                    <div class="all-select-box">
                                        <div class="btn-box">
                                            <input type="text" name="date" class="form-control month-btn  {{ $totalTime < $threshold ? 'time-box' : '' }}" value="{{ $totalTime }}" disabled>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-auto">
                                    <div class="all-select-box">
                                        <div class="btn-box">
                                            <input type="date" name="date" class="form-control month-btn " value="{{ isset($_GET['date']) ? $_GET['date'] : now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            
                                @if (\Auth::user()->type != 'employee')
                                    <!-- Employee Dropdown -->
                                    
                            
                                    <!-- Branch Dropdown -->
                                    <div class="col-xl-2 col-lg-3">
                                        <div class="all-select-box">
                                            <div class="btn-box">
                                                <select name="branch" class="form-control select2">
                                                    @foreach ($branch as $branchId => $branchName)
                                                        <option value="{{ $branchId }}" {{ isset($_GET['branch']) && $_GET['branch'] == $branchId ? 'selected' : '' }}>
                                                            {{ $branchName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            
                                    <!-- Department Dropdown -->
                                    <div class="col-xl-2 col-lg-3">
                                        <div class="all-select-box">
                                            <div class="btn-box">
                                                <select name="department" class="form-control select2">
                                                    @foreach ($department as $departmentId => $departmentName)
                                                        <option value="{{ $departmentId }}" {{ isset($_GET['department']) && $_GET['department'] == $departmentId ? 'selected' : '' }}>
                                                            {{ $departmentName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-3">
                                        <div class="all-select-box">
                                            <div class="btn-box">
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
                                    </div>
                                @endif
                            
                                <!-- Search and Reset Buttons -->
                                <div class="">
                                    <button type="submit" class="apply-btn" title="Search">
                                        <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
                                    </button>
                                    <a href="{{ route('attendanceemployee.index') }}" class="reset-btn" title="Reset">
                                        <span class="btn-inner--icon"><i class="fas fa-sync-alt"></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped mb-0 ">
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

                            <tbody id="" class="mb-3">
                            @if(\Auth::user()->type == 'employee')
                            @forelse ($attendanceEmployee as $attendance)
                            <tr class="accordion-content">
                                <td>{{ date('d-m-Y', strtotime($date??today())) }}</td>
                                <td>{{ $attendance->status }}</td>
                                <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</td>
                                <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</td>
                                <td>{{ $attendance->employee->shift_start ?? 'N/A' }}</td>
                                <td>{{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest == '00:00:00' ? $attendance->late : $attendance->total_rest) }}{{ $attendance->total_rest == '00:00:00' ? ' (Late)' : ' (Rest)' }}</td>
                            </tr>
                        @empty
                            @php
                                $status = !$leaveToday ? ($holidays ? 'HOLIDAY' : ($isWeekend ? 'WEEK-END' : 'Absent')):'';
                            @endphp
                            <tr>
                                <td colspan="7">{{ $status }}</td>
                            </tr>
                        @endforelse
                        
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
                                        <td  colspan="7">
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
                                            <td colspan="7" align="center">{{$employee->name}}  (<strong>
                                                Total Time: 
                                                <i class="{{ $totalTime < $threshold ? 'text-danger' : '' }}">
                                                    {{ $totalTime }}
                                                </i>
                                                
                                            </strong>) @if($leaveToday && $leaveToday != 0) 
                                            <span class="bg-warning" style="color: #FFF !important; padding: 5px; border-radius: 10px; background-color:#ffac04 !important;">{{ $leaveToday }}</span>
                                            @endif</td>
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
                                                    <!--@if ($attendance->total_rest == '00:00:00')-->
                                                    <!--    @if ($attendance->late < '00:00:00')-->
                                                    <!--        (Early)-->
                                                    <!--    @else-->
                                                    <!--        {{ Helper::convertTimeToMinutesAndSeconds($attendance->late) }} (Late)-->
                                                    <!--    @endif-->
                                                    <!--@else-->
                                                    <!--    {{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest) }} (Rest)-->
                                                    <!--@endif-->
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
                                        <tr>
                                            <td colspan="7"></td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endif
                            </tbody>
                            </table>
                        </div>
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
     
