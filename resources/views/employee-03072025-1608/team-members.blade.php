@extends('layouts.admin')
@section('page-title')
    @if(isset($_GET['type']) && $_GET['type'] == 'probation')
    {{ __('Probation Members') }}
    @else
    {{ __('Team Members') }}
    @endif
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @if(isset($_GET['type']) && $_GET['type'] == 'probation')
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6" id="RegularEMP">
            <div class="all-button-box">
                <a id="RegularBTN" href="{{route('employee.team-members')}}" class="btn btn-xs btn-white btn-icon-only width-auto">
                    {{ __('Active Employees') }}
                </a>
            </div>
        </div>
        @else
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6" id="ProbationEMP">
            <div class="all-button-box">
                <a id="ProbationBTN" class="btn btn-xs btn-white btn-icon-only width-auto" href="{{ route('employee.team-members', ['type' => 'probation' ?? null]) }}">
                    {{ __('Probation Employees') }}
                </a>
            </div>
        </div>
        @endif
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 dataTable">
                            <thead>
                                <tr class="text-center">
                                    <th>{{ __('Employee ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Shift Start Time') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Today Clock In') }}</th>
                                    <th>{{ __('Today Clock Out') }}</th>
                                    <th>{{ __('Today Total Time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr class="text-center">
                                        <td class="Id">
                                            @can('Show Employee')
                                                <a
                                                    href="{{ route('employee.member.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                            @else
                                                <a
                                                    href="#">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                            @endcan
                                        </td>
                                        <td class="font-style">{{ $employee->name }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td class="font-style">
                                            {{ !empty($employee->shift_start) ? date('h:i A', strtotime($employee->shift_start)) : '' }}
                                        </td>
                                        @php
                                            $date = date("Y-m-d");
                                            $attendance = $employee->attendanceEmployees;
                                            $isAbsent = empty($attendance);
                                            $isLeave = \App\Helpers\Helper::checkLeaveWithTypes($date, $employee->id);
                                            $currentTime = date("H:i:s");
                                            $firstHalf = "13:00:00";
                                            $secondHalf = "14:00:00";
                                        @endphp
                                        <td class="font-style">
                                            @if (!empty($employee->attendanceEmployees) && !empty($employee->attendanceEmployees->clock_in && !($isLeave)))
                                                <span class="badge badge-success">Present</span>
                                            @elseif($isAbsent)
                                                <span class="absent-btn">Absent</span>
                                            @elseif($isLeave)
                                               
                                                @if($isLeave == 'morning halfday')
                                                    @if($currentTime <= $firstHalf)
                                                        <span class="badge badge-warning">1st Half Leave</span>
                                                    @else
                                                        @if($employee->attendanceEmployees->clock_in)
                                                        <span class="badge badge-success">Present</span>
                                                        @endif
                                                        <span class="badge badge-warning">1st Half Leave</span>
                                                        <!--{{ date('h:i A', strtotime($employee->attendanceEmployees->clock_in)) }}-->
                                                    @endif
                                                @elseif($isLeave == 'afternoon halfday')
                                                    @if($currentTime >= $secondHalf)
                                                        <span class="badge badge-warning">2nd Half Leave</span>
                                                    @else
                                                        @if($employee->attendanceEmployees->clock_in)
                                                        <span class="badge badge-success">Present</span>
                                                        @endif
                                                        <span class="badge badge-warning">2nd Half Leave</span>
                                                        <!--{{ date('h:i A', strtotime($employee->attendanceEmployees->clock_in)) }}-->
                                                    @endif
                                                @elseif($isLeave == 'fullday Leave')
                                                    <span class="badge badge-warning">Leave</span>
                                                @elseif($isLeave == 'on short leave')
                                                    <span class="badge badge-warning">Short Leave</span>
                                                @elseif($isLeave == 'not on short leave')
                                                    @if($isAbsent)
                                                        <span class="absent-btn">Absent</span>
                                                    @elseif(!empty($employee->attendanceEmployees) && !empty($employee->attendanceEmployees->clock_in && !($isLeave)))
                                                        {{ date('h:i A', strtotime($employee->attendanceEmployees->clock_in)) }} 
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                        <td class="font-style">
                                            @if (!empty($employee->attendanceEmployees) && !empty($employee->attendanceEmployees->clock_in))
                                                {{ date('h:i A', strtotime($employee->attendanceEmployees->clock_in)) }}
                                            @endif
                                        </td>
                                        <td class="font-style">
                                            <?php $clockOut = \App\Helpers\Helper::getLatestAttendance($employee->id); ?>
                                            {{ $clockOut != '' ? $clockOut : '00:00:00' }}
                                        </td>
                                        <td class="font-style">
                                            {{ \App\Helpers\Helper::getTotalAttendanceTime($employee->id); }}
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
@endsection
