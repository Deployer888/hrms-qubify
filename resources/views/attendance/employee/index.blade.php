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
    
    .btn-danger
    {
        background: red !important;
        border-color: red !important;
    }
    .not-found td{
        background: gray; /* Light gray */
    }
    .name-lable td{
        background: linear-gradient(135deg, #3a8ef6, #6259ca); /* Light gray */
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
        <div class="card pb-3">
            <div class="card-body py-0">
                <form method="GET" action="{{ route('attendanceemployee.index') }}">
                    <div class="row d-flex justify-content-end mt-2">
                        <!-- Form Controls Section -->
                        <div class="col-12 pt-2">
                            <div class="row">
                                <!-- Left Side: Date Input, Radio Buttons, and Month/Date Input -->
                                <div class="d-flex flex-wrap align-items-center col-xl-2 col-lg-2 col-md-2 col-sm-6">
                                    <!-- Date Input -->
                                    @if(\Auth::user()->type == 'employee')
                                        <?php
                                        $isAbsent = $attendanceEmployee->isEmpty();
                                        $isLeave = Helper::checkLeave($date ? $date : today(), $employee);
                                        if ($isLeave != 0) {
                                            $leaveToday = Helper::checkLeaveWithTypes($date ? $date : today(), $employee);
                                            if ($leaveToday == 'afternoon halfday' || $leaveToday == 'morning halfday') {
                                                $leaveToday = 'Half-Day Leave';
                                            } elseif ($leaveToday == 'on short leave') {
                                                $leaveToday = 'Short Leave';
                                            } elseif ($leaveToday == 'fullday Leave') {
                                                $leaveToday = 'Leave';
                                            }
                                        } else {
                                            $leaveToday = 0;
                                        }
                                        $totalTime = Helper::calculateTotalTimeDifference($attendanceEmployee);
                                        $threshold = '08:00'; // Default threshold
                                        if ($leaveToday == 'Half-Day Leave') {
                                            $threshold = '04:00';
                                        } elseif ($leaveToday == 'Short Leave') {
                                            $threshold = '06:00';
                                        }
                                        ?>
                                        <div class="col-12"  id="totalHours">
                                            <input type="text" name="date" class="form-control month-btn {{ $totalTime < $threshold ? 'time-box' : '' }}" value="{{ $totalTime }}" disabled>
                                        </div>
                                    @endif
                            
                                    <!-- Radio Buttons and Month/Date Input -->
                                    
                                </div>
                            
                                <!-- Right Side: Dropdowns and Buttons -->
                                <div class="d-flex flex-wrap align-items-center justify-content-end col-xl-10 col-lg-10 col-md-9 col-sm-12">
                                    <div class="col-6">
                                        <div class="d-flex">
                                            <!-- Radio Buttons -->
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
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
                            
                                            <!-- Month/Date Input -->
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-12" id="monthAndDate">
                                                @if($requestType == 'daily')
                                                    <input type="date" name="date" class="form-control month-btn" value="{{ isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') }}">
                                                @else
                                                    <input type="month" name="month" class="month-btn form-control" value="{{ isset($_GET['month']) ? $_GET['month'] : date('Y-m') }}">
                                                @endif
                                            </div>
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
                    <table class="table table-striped mb-0 " id="attendanceTable">
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
                                    @if(count($attendanceEmployee)>0)
                                        @if($empLeave)
                                            <tr>
                                                <td align="center" colspan="7" class="btn-warning" >({{strtoupper($empLeave['leavetype'])}} LEAVE) {{$empLeave['start_time']}}-{{$empLeave['end_time']}}</td>
                                            </tr>
                                        @endif
                                        @foreach ($attendanceEmployee as $key=>$attendance)
                                            <tr class="accordion-content">
                                                <td>{{ date('d-m-Y', strtotime($attendance->date)) }}</td>
                                                <td>{{ $attendance->status }}</td>
                                                <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</td>
                                                <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</td>
                                                <td>{{ $attendance->employee->shift_start ?? 'N/A' }}</td>
                                                
                                                <td>
                                                    @if($key)
                                                        {{Helper::dynRestTime($attendanceEmployee[$key-1]->clock_out??'',$attendanceEmployee[$key]->clock_in)}}
                                                    @else
                                                        {{Helper::dynLateTime(Auth::user()->employee->shift_start??'09:00:00',$attendance->clock_in)}}
                                                    @endif
                                                    <!--{{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest == '00:00:00' ? $attendance->late : $attendance->total_rest) }}{{ $attendance->total_rest == '00:00:00' ? ' (Late)' : ' (Rest)' }}-->
                                                    </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @if($isWeekend)
                                        <tr>
                                            <td align="center" colspan="7" class="btn-white"> (WEEKEND)</td>
                                        </tr>
                                        @elseif($isLeave)
                                        <tr>
                                            <td align="center" colspan="7" class="btn-white"> (WEEKEND)</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td align="center" colspan="7" class="btn-danger"> (ABSENT)</td>
                                        </tr>
                                        @endif
                                    @endif
                                @else
                                    @foreach($monthAttendanceEmployee as $key => $attEmp)
                                    @php
                                        $doj = "2025-02-10";
                                        $date = "2025-02-28";
                                    
                                        // Create DateTime objects
                                        $dateTime1 = new DateTime(Auth::user()->employee->company_doj);
                                        $dateTime2 = new DateTime($key);
                                        if ($dateTime1 > $dateTime2)
                                        {
                                            continue;
                                        }
                                    @endphp
                                    @if($attEmp['is_weekend'] && !$attEmp['attendance'])
                                        <tr>
                                            <td align="center" colspan="7" class="btn-white">{{ $key }} (WEEKEND)</td>
                                        </tr>
                                    @elseif($attEmp['leave_detail'] && $attEmp['leave_detail']['leavetype'] == 'full')
                                        <tr>
                                            <td align="center" colspan="7" class="btn-warning" >{{ $key }} ({{strtoupper($attEmp['leave_detail']['leavetype'])}} LEAVE) {{$attEmp['leave_detail']['start_time']}}-{{$attEmp['leave_detail']['end_time']}}</td>
                                        </tr>
                                    @elseif($attEmp['attendance'])
                                    
                                        <tr>
                                            @if($attEmp['is_weekend'])
                                            <tr>
                                                <td align="center" colspan="7" data-toggle="collapse" data-target="#collapse-{{ $key }}" role="button" style="background: linear-gradient(135deg, #3a8ef6, #6259ca); /* Light gray */
                                            color: #fff;" >{{ $key }} (WEEKEND)</td>
                                            </tr>
                                            @elseif($attEmp['leave_detail'] && $attEmp['leave_detail']['leavetype'] != 'full')
                                            <td align="center" colspan="7" class="btn-warning"  data-toggle="collapse" data-target="#collapse-{{ $key }}" role="button" >{{ $key }} ({{ strtoupper($attEmp['leave_detail']['leavetype']) }}
                                                LEAVE) <span class=" {{ $attEmp['hours'] < $attEmp['min_hours'] ? 'text-danger' : '' }}">({{$attEmp['hours']}})</span>  {{$attEmp['leave_detail']['start_time']}}{{$attEmp['leave_detail']['start_time']?'-':''}}{{$attEmp['leave_detail']['end_time']}}</td>

                                            @else

                                            <td align="center" colspan="7" data-toggle="collapse" data-target="#collapse-{{ $key }}" role="button"  style="background: linear-gradient(135deg, #3a8ef6, #6259ca); /* Light gray */
                                            color: #fff;">{{ $key }} <span class=" {{ $attEmp['hours'] < $attEmp['min_hours'] ? 'text-warning' : '' }}">({{$attEmp['hours']}})</span></td>
                                            @endif
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
                                                        @foreach($attEmp['attendance'] as $key=>$attendance)
                                                            <tr class="accordion-content">
                                                                <td>{{ date('d-m-Y', strtotime($attendance['date'])) }}</td>
                                                                <td>{{ $attendance['status'] }}</td>
                                                                <td>{{ $attendance['clock_in'] != '00:00:00' ? \Auth::user()->timeFormat($attendance['clock_in']) : '00:00' }}</td>
                                                                <td>{{ $attendance['clock_out'] != '00:00:00' ? \Auth::user()->timeFormat($attendance['clock_out']) : '00:00' }}</td>
                                                                <td>{{ $attendance['employee']['shift_start'] ?? 'N/A' }}</td>
                                                                
                                                                <td>
                                                                    @if($key)
                                                                        {{Helper::dynRestTime($attEmp['attendance'][$key-1]['clock_out']??'',$attEmp['attendance'][$key]['clock_in'])}}
                                                                    @else
                                                                        {{Helper::dynLateTime(Auth::user()->employee->shift_start??'09:00:00',$attendance['clock_in'])}}
                                                                    @endif
                                                                    <!--{{ Helper::convertTimeToMinutesAndSeconds($attendance['total_rest'] == '00:00:00' ? $attendance['late'] : $attendance['total_rest']) }}{{ $attendance['total_rest'] == '00:00:00' ? ' (Late)' : ' (Rest)' }}-->
                                                                </td>
                                                            </tr>   
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @else
                                    <tr>
                                        <td align="center" colspan="7" class="btn-danger" >{{ $key }} (ABSENT) </td>
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
                                            <td colspan="7" align="center">{{$employee->name}}(<strong>
                                                Total Time:
                                                <i class="{{ $totalTime < $threshold ? 'text-danger' : '' }}">
                                                    {{ $totalTime }}
                                                </i>

                                            </strong>) @if($leaveToday && $leaveToday != 0)
                                            <span class="bg-warning" style="color: #FFF !important; padding: 5px; border-radius: 10px; background:#ffac04 !important;">{{ $leaveToday }}</span>
                                            @endif</td>
                                        </tr>

                                        @forelse ($employee->attendance ?? [] as $attendance)
                                            <tr class="accordion-content">
                                                <td>{{ date('d-m-Y', strtotime($attendance->date)) }}</td>
                                                <td>{{ $attendance->status }}</td>
                                                <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</td>
                                                <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</td>
                                                <td>{{ $employee->shift_start ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($attendance->total_rest == '00:00:00')
                                                        @if ($attendance->late < '00:00:00')
                                                            (Early)
                                                        @else
                                                            {{ Helper::convertTimeToMinutesAndSeconds($attendance->late) }} (Late)
                                                        @endif
                                                    @else
                                                        {{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest) }} (Rest)
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

            // $('div[id="monthAndDate"]').on('change', function() {
            //     const preloader = document.getElementById('loader');
            //     preloader.style.display = 'block';

            //     var date = $(this).find('input').val(); 
            //     var type = $(this).find('input').attr('type'); 
            //     var employeeId = "{{ Auth::user()->employee->id }}"; 

            //     $.ajax({
            //         url: "{{ route('json.emp.attendance') }}", 
            //         type: 'POST',
            //         data: { 
            //             date: date,
            //             type: type,
            //             employee_id: employeeId,
            //             _token: "{{ csrf_token() }}" // CSRF token for Laravel POST requests
            //         },
            //         dataType: 'json', 
            //         success: function(response) {   
            //             console.log(response);
                        
            //             let tbody = $("#attendanceTable tbody"); // Target table body
            //             tbody.empty(); // Clear existing table rows before appending
            //             if(response.data.type == 'month')
            //             {
            //                 if (response.data.attendance && Object.keys(response.data.attendance).length > 0) {
            //                     Object.entries(response.data.attendance).forEach(([key, attEmp]) => {
            //                         let row = "";
                                    
            //                         if (attEmp.is_weekend && attEmp.attendance.length<1) {
            //                             row += `<tr><td align="center" colspan="7" class="btn-white">${key} (WEEKEND)</td></tr>`;
            //                         } else if (attEmp.leave_detail && attEmp.leave_detail.leavetype === "full") {
            //                             row += `<tr><td align="center" colspan="7" class="btn-warning">${key} (${attEmp.leave_detail.leavetype.toUpperCase()} LEAVE) ${attEmp.leave_detail.start_time}-${attEmp.leave_detail.end_time}</td></tr>`;
            //                         } else if (attEmp.attendance && attEmp.attendance.length>0) {
            //                             row += `<tr>`;
            //                             if (attEmp.is_weekend) {
            //                                 row += `<td align="center" colspan="7" data-toggle="collapse" data-target="#collapse-${key}" role="button" style="background: linear-gradient(135deg, #3a8ef6, #6259ca); color: #fff;">${key} (WEEKEND)</td>`;
            //                             } else if (attEmp.leave_detail && attEmp.leave_detail.leavetype !== "full") {
            //                                 row += `<td align="center" colspan="7" class="btn-warning" data-toggle="collapse" data-target="#collapse-${key}" role="button">${key} (${attEmp.leave_detail.leavetype.toUpperCase()} LEAVE) <span class="${attEmp.hours < attEmp.min_hours ? 'text-danger' : ''}">(${attEmp.hours})</span> ${attEmp.leave_detail.start_time ? attEmp.leave_detail.start_time + '-' : ''}${attEmp.leave_detail.end_time}</td>`;
            //                             } else {
            //                                 row += `<td align="center" colspan="7" data-toggle="collapse" data-target="#collapse-${key}" role="button" style="background: linear-gradient(135deg, #3a8ef6, #6259ca); color: #fff;">${key} <span class="${attEmp.hours < attEmp.min_hours ? 'text-warning' : ''}">(${attEmp.hours})</span></td>`;
            //                             }
            //                             row += `</tr>`;
    
            //                             // Attendance details collapsible row
            //                             row += `<tr class="collapse" id="collapse-${key}"><td colspan="7">
            //                                         <table class="table">
            //                                             <thead>
            //                                                 <tr>
            //                                                     <th>Date</th>
            //                                                     <th>Status</th>
            //                                                     <th>Clock In</th>
            //                                                     <th>Clock Out</th>
            //                                                     <th>Shift Start</th>
            //                                                     <th>Late/Rest</th>
            //                                                 </tr>
            //                                             </thead>
            //                                             <tbody>`;
    
            //                             attEmp.attendance.forEach(att => {
            //                                 row += `<tr>
            //                                             <td>${att.date}</td>
            //                                             <td>${att.status}</td>
            //                                             <td>${att.clock_in !== "00:00:00" ? att.clock_in : "00:00"}</td>
            //                                             <td>${att.clock_out !== "00:00:00" ? att.clock_out : "00:00"}</td>
            //                                             <td>${att.employee.shift_start ? att.employee.shift_start : "N/A"}</td>
            //                                             <td>${att.total_rest === "00:00:00" ? att.late + " (Late)" : att.total_rest + " (Rest)"}</td>
            //                                         </tr>`;
            //                             });
    
            //                             row += `        </tbody>
            //                                         </table>
            //                                     </td></tr>`;
            //                         } else {
            //                             row += `<tr><td align="center" colspan="7" class="btn-danger">${key} (ABSENT)</td></tr>`;
            //                         }
    
            //                         tbody.append(row);
            //                     });
            //                 } else {
            //                     tbody.append('<tr><td align="center" colspan="7">No attendance data available.</td></tr>');
            //                 }
            //             }
            //             if(response.data.type == 'date')
            //             {
            //                 // start 
            //                 if (response.data.attendances.length > 0) {
            //                     // If leave exists, show leave row
            //                     if (response.data.is_leave) {
            //                         tbody.append(`
            //                             <tr>
            //                                 <td align="center" colspan="7" class="btn-warning">
            //                                     (${response.data.emp_leave.leavetype.toUpperCase()} LEAVE) 
            //                                     ${response.data.emp_leave.start_time} - ${response.data.emp_leave.end_time}
            //                                 </td>
            //                             </tr>
            //                         `);
            //                     }

            //                     // Append attendance records
            //                     response.data.attendances.forEach(function (attendance) {
            //                         var restOrLate = attendance.total_rest === "00:00:00" ? "Late" : "Rest";
            //                         var restOrLateTime = attendance.total_rest === "00:00:00" ? attendance.late : attendance.total_rest;

            //                         tbody.append(`
            //                             <tr class="accordion-content">
            //                                 <td>${formatDate(attendance.date)}</td>
            //                                 <td>${attendance.status}</td>
            //                                 <td>${formatTime(attendance.clock_in)}</td>
            //                                 <td>${formatTime(attendance.clock_out)}</td>
            //                                 <td>${attendance.employee?.shift_start ?? 'N/A'}</td>
            //                                 <td>${convertTimeToMinutesAndSeconds(restOrLateTime)} (${restOrLate})</td>
            //                             </tr>
            //                         `);
            //                     });

            //                 } else {
            //                     // Handle weekend or absence
            //                     if (response.data.is_weekend) {
            //                         tbody.append(`
            //                             <tr>
            //                                 <td align="center" colspan="7" class="btn-white">(WEEKEND)</td>
            //                             </tr>
            //                         `);
            //                     } else if (response.data.is_leave) {
            //                         tbody.append(`
            //                             <tr>
            //                                 <td align="center" colspan="7" class="btn-warning">(LEAVE)</td>
            //                             </tr>
            //                         `);
            //                     } else {
            //                         tbody.append(`
            //                             <tr>
            //                                 <td align="center" colspan="7" class="btn-danger">(ABSENT)</td>
            //                             </tr>
            //                         `);
            //                     }
            //                 }
            //                 let totalTime = response.data.hours; // Example: "07:11 Hrs"
            //                 let threshold = response.data.min_hours; // Example: "06:00"

            //                 let $timeInput = $('#totalHours input[name="date"]');
                            
            //                 // Update the value
            //                 $timeInput.val(totalTime);

            //                 // Add or remove class based on condition
            //                 if (totalTime < threshold) {
            //                     $timeInput.addClass('time-box');
            //                 } else {
            //                     $timeInput.removeClass('time-box');
            //                 }
            //                 //end 
            //             }
            //             preloader.style.display = 'none';
            //         },
            //         error: function(xhr, status, error) {
            //             console.error('Error:', error);
            //             alert('Something went wrong! Please try again.');
            //         }
            //     });
            // });
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
        // Format date from YYYY-MM-DD to DD-MM-YYYY
        function formatDate(dateString) {
            var date = new Date(dateString);
            return date.toLocaleDateString('en-GB'); // Formats as DD-MM-YYYY
        }

        // Format time in 12-hour format with AM/PM
        function formatTime(timeString) {
            if (timeString === "00:00:00") return "00:00 AM"; // Handle midnight case

            var [hours, minutes] = timeString.split(":");
            hours = parseInt(hours, 10);
            var ampm = hours >= 12 ? "PM" : "AM";
            hours = hours % 12 || 12; // Convert 0 to 12 for 12 AM case
            return `${hours}:${minutes} ${ampm}`;
        }


        // Convert time to minutes & seconds
        function convertTimeToMinutesAndSeconds(timeString) {
            var parts = timeString.split(":");
            return parts[0] + "h " + parts[1] + "m " + parts[2] + "s";
        }
    </script>
    @endpush

