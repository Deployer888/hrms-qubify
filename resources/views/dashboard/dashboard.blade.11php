
@extends('layouts.admin')
@section('page-title')
    {{ __('Dashboard') }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}">
@endpush

@section('style')

@endsection

@section('content')
@php
use App\Helpers\Helper;
$totalTimeDifference = '';
if (isset($employeeAttendanceList))
    $totalTimeDifference = Helper::calculateTotalTimeDifference($employeeAttendanceList);
@endphp
<style>
    button.disabled {
        background-color: grey !important;
        border-color: grey !important;
        cursor: not-allowed;
        border-radius: 10px;
    }

    .fc-day, .fc-day-top {
        line-height: 4.65 !important;
    }

    .attandanceList {
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
    }

    /* Time & Button Section */
    .time-section {
        margin-bottom: 10px;
    }

    .time-section div {
        font-size: 14px;
        padding: 5px;
    }

    /* Buttons */
    .btn-sm {
        padding: 5px 10px;
        font-size: 14px;
        font-weight: bold;
    }

    .btn-success {
        background-color: #28a745;
        border: none;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-sm i {
        margin-right: 3px;
    }

    .btn-sm:hover {
        opacity: 0.9;
    }

    .attendance-card {
        height: 578px;
    }
    /* Table Styles */
    .attendance-table {
        margin-top: 10px;
        border: 1px solid #e1e4e8;
    }

    .attendance-table thead {
        background-color: #f7f9fc;
        font-weight: bold;
    }

    .attendance-table tbody tr:hover {
        background-color: #f1f3f5;
    }

    .fc-scroller > .fc-day-grid, .fc-scroller > .fc-time-grid {
        height: 340px!improtant;
    }

    .fc-content-skeleton td{
        height: 0;
    }

    .fc-row.fc-week.fc-widget-content{
        height: 55px !important;
    }

    .fc-scroller.fc-day-grid-container{
        overflow: hidden !important;
    }

    .fc-content-skeleton tbody tr{
        position: relative !important;
        top: -25px !important;
    }

    .card{
        margin-bottom: 1rem!important;
    }

    .attendanceBody{
        overflow: auto;
    }

    .attendanceBody tr td{
        border: none !important;
    }

    .table-responsive tbody tr td{
        height: 0px!important;
        padding: 10px 20px!important;
    }
</style>
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <?php // Helper::clockinAttendance(2, '2025-01-31', '0:00', '0:00');  ?>

    <input type="hidden" id="att_id" value="{{ isset($employeeAttendance) ? $employeeAttendance->employee_id : '' }}" />
    @if (\Auth::user()->type == 'employee')
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Event View') }}</h4>
                    </div>
                    <div class="card-body dash-card-body">
                        <div class="page-title">
                            <div class="row justify-content-between align-items-center full-calender">
                                <div class="col d-flex align-items-center">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="#" class="fullcalendar-btn-prev btn btn-sm btn-neutral">
                                            <i class="fas fa-angle-left"></i>
                                        </a>
                                        <a href="#" class="fullcalendar-btn-next btn btn-sm btn-neutral">
                                            <i class="fas fa-angle-right"></i>
                                        </a>
                                    </div>
                                    <h5 class="fullcalendar-title h4 d-inline-block font-weight-400 mb-0"></h5>
                                </div>
                                <div class="col-lg-6 mt-3 mt-lg-0 text-lg-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="#" class="btn btn-sm btn-neutral"
                                            data-calendar-view="month">{{ __('Month') }}</a>
                                        <a href="#" class="btn btn-sm btn-neutral"
                                            data-calendar-view="basicWeek">{{ __('Week') }}</a>
                                        <a href="#" class="btn btn-sm btn-neutral"
                                            data-calendar-view="basicDay">{{ __('Day') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <!-- Fullcalendar -->
                                <div class="overflow-hidden widget-calendar">
                                    <div class="calendar e-height" data-toggle="event_calendar" id="event_calendar"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card attendance-card">
                    <div class="card-header">
                        <h4>{{ __('Attendance') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-center text-muted">
                                <h4><strong>Total Time:</strong>&nbsp; {{ $totalTimeDifference }}<h4>
                            </div>
                            <div class="text-center text-muted">
                                <h4><strong>Timer:</strong>&nbsp; <span id="timer-display"> 00:00:00</span></h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <form id="clock_in_form">
                                @csrf
                                @php
                                    $disabledIn = 'disabled';
                                    if (empty($employeeAttendance) || $employeeAttendance->clock_out != '00:00:00')
                                        $disabledIn = '';
                                @endphp
                                <!-- if weekdays -> 10am-6pm diasbled and on sat-sun full time Enable -->
                                @if(\Auth::check() && \Auth::user()->employee && (\Auth::user()->employee->id == 4 || \Auth::user()->employee->id == 1 || \Auth::user()->employee->id == 3) && (now()->isWeekend() || now()->isWeekday() && (now()->format('H:i') < '10:00' || now()->format('H:i') > '18:00')) )<!-- && ( (weekday && time ) || (weekend))-->
                                <button type="button" value="0" name="in" id="clock_in"
                                    class="btn btn-success btn-sm {{ $disabledIn }}"
                                    {{ $disabledIn }}>
                                    <i class="fas fa-clock"></i>&nbsp;{{ __('Clock In') }}
                                </button>
                                @endif
                            </form>
                            <form id="clock_out_form" class="text-right">
                                @csrf
                                @method('POST')
                                @php
                                    $disabledOut = 'disabled';
                                    if (!empty($employeeAttendance) && $employeeAttendance->clock_out == '00:00:00')
                                        $disabledOut = '';

                                @endphp
                                @if(\Auth::check() && \Auth::user()->employee && (\Auth::user()->employee->id == 4 || \Auth::user()->employee->id == 1 || \Auth::user()->employee->id == 3) && (now()->isWeekend() || now()->isWeekday() && (now()->format('H:i') < '10:00' || now()->format('H:i') > '18:00')) )   <!--  && ( (weekday && time ) || (weekend))-->
                                <button type="button" value="1" name="out" id="clock_out"
                                    class="btn btn-danger btn-sm {{ $disabledOut }}" {{ $disabledOut }}>
                                    <i class="fas fa-sign-out-alt"></i>&nbsp;{{ __('Clock Out') }}
                                </button>
                                @endif
                            </form>
                        </div>
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered table-hover attendance-table" style="overflow-y: auto!important;">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Clock In') }}</th>
                                        <th>{{ __('Clock Out') }}</th>
                                        <th>{{ __('Time') }}</th>
                                        <th>{{ __('Late/Rest Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="attendanceBody">
                                    <?php
                                        $late = $early = '';
                                        if (count($employeeAttendanceList) > 0) {
                                            $LastData = $employeeAttendanceList[count($employeeAttendanceList) - 1];
                                            $late = \App\Helpers\Helper::FormatTime($LastData->late);
                                        }
                                    ?>

                                    @foreach ($employeeAttendanceList as $key => $employeeAttendance)
                                        <tr >
                                            <td>{{ $employeeAttendance->clock_in }}</td>
                                            <td>
                                                @if($employeeAttendance->clock_out != '00:00:00')
                                                {{ $employeeAttendance->clock_out ?? '' }}
                                                @endif
                                            </td>

                                            @php
                                                $early = strtotime($employeeAttendance->clock_in) < strtotime($employeeAttendance->employee->shift_start) ? 1 : 0;
                                                if (!function_exists('formatTimeDifference')) {
                                                    function formatTimeDifference($checkIn, $checkOut) {
                                                        $checkInTime = strtotime($checkIn);
                                                        $checkOutTime = strtotime($checkOut);
                                                        $diffInSeconds = abs($checkOutTime - $checkInTime);
                                                        if ($diffInSeconds < 60) {
                                                            return $diffInSeconds . ' secs';
                                                        }
                                                        $diffInMinutes = floor($diffInSeconds / 60);
                                                        $remainingSeconds = $diffInSeconds % 60;
                                                        if ($diffInMinutes < 60) {
                                                            return $diffInMinutes . ' mins ' . $remainingSeconds . ' secs';
                                                        }
                                                        $diffInHours = floor($diffInMinutes / 60);
                                                        $remainingMinutes = $diffInMinutes % 60;
                                                        return $diffInHours . ' hrs ' . $remainingMinutes . ' mins ' . $remainingSeconds . ' secs';
                                                    }
                                                }
                                                $checkIn = $employeeAttendance->clock_in;
                                                $checkOut = $employeeAttendance->clock_out;
                                                $formattedDifference = '';
                                                if ($checkOut != '00:00:00') {
                                                    $formattedDifference = formatTimeDifference($checkIn, $checkOut);
                                                }
                                                $totalRest = \App\Helpers\Helper::FormatTime($employeeAttendance->total_rest);
                                            @endphp
                                            <td>{{ $formattedDifference }}</td>
                                            <!--<td>{{-- $employeeAttendance->late --}}</td>-->
                                            <td>
                                                @if($employeeAttendance->total_rest == '00:00:00')
                                                 @if($early == 1) (Arrived Early) @else {{ $late }} (Late) @endif
                                                @else
                                                {{ $totalRest }} (Rest)
                                                @endif
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
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Announcement List') }}</h4>
                    </div>
                    <div class="card-body dash-card-body">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Start Date') }}</th>
                                        <th>{{ __('End Date') }}</th>
                                        <th>{{ __('description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($announcements as $announcement)
                                        <tr>
                                            <td>{{ $announcement->title }}</td>
                                            <td>{{ \Auth::user()->dateFormat($announcement->start_date) }}</td>
                                            <td>{{ \Auth::user()->dateFormat($announcement->end_date) }}</td>
                                            <td>{{ $announcement->description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Meeting List') }}</h4>
                    </div>
                    <div class="card-body dash-card-body">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Meeting title') }}</th>
                                        <th>{{ __('Meeting Date') }}</th>
                                        <th>{{ __('Meeting Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($meetings as $meeting)
                                        <tr>
                                            <td>{{ $meeting->title }}</td>
                                            <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                            <td>{{ \Auth::user()->timeFormat($meeting->time) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Total Staff Card -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- Left Column: Icon and Total Staff Title with Count -->
                            <div class="col-7 d-flex align-items-center">
                                <div class="icon-box bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 60px; height: 55px;">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 d-inline">{{ __('Total Staff') }}</h6>
                                    <p class="h4 mb-0 ms-2">{{ $countUser + $countEmployee }}</p>
                                </div>
                            </div>

                            <!-- Right Column: User and Employee Counts -->
                            <div class="col-5">
                                <div>
                                    <p class="mb-1">{{ __('User') }}: <strong>{{ $countUser }}</strong></p>
                                    <p class="mb-0">{{ __('Employee') }}: <strong>{{ $countEmployee }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Ticket Card -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- Left Column: Icon and Total Ticket Title with Count -->
                            <div class="col-7 d-flex align-items-center">
                                <div class="icon-box bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 60px; height: 55px;">
                                    <i class="fas fa-tag fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 d-inline">{{ __('Total Ticket') }}</h6>
                                    <p class="h4 mb-0 ms-2">{{ $countTicket }}</p>
                                </div>
                            </div>

                            <!-- Right Column: Open and Close Tickets -->
                            <div class="col-5">
                                <div>
                                    <p class="mb-1">{{ __('Open ticket') }}: <strong>{{ $countOpenTicket }}</strong></p>
                                    <p class="mb-0">{{ __('Close ticket') }}: <strong>{{ $countCloseTicket }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Balance Card (Visible for Company User Type Only) -->
            @if (\Auth::user()->type == 'company')
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <!-- Left Column: Icon and Account Balance Title with Amount -->
                                <div class="col-7 d-flex align-items-center">
                                    <div class="icon-box bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 60px; height: 55px;">
                                        <i class="fas fa-money-bill fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 d-inline">{{ __('Account Balance') }}</h6>
                                        <p class="h4 mb-0 ms-2">{{ \Auth::user()->priceFormat($accountBalance) }}</p>
                                    </div>
                                </div>

                                <!-- Right Column: Payee and Payer -->
                                <div class="col-5">
                                    <div>
                                        <p class="mb-1">{{ __('Payee') }}: <strong>{{ $totalPayer }}</strong></p>
                                        <p class="mb-0">{{ __('Payer') }}: <strong>{{ $totalPayer }}</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6">
                <h4 class="h4 font-weight-400">{{ __("Today's Not Clock In") }}</h4>
                <div class="card bg-none min-height-443">
                    <div class="table-responsive">
                        <table class="table align-items-center">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($notClockIns as $notClockIn)
                                    <tr>
                                        <td>{{ $notClockIn->name }}</td>
                                        <td><span class='{{ $notClockIn->class }}'>{{ $notClockIn->status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6">
                <div class="">
                    <h4 class="h4 font-weight-400 float-left">{{ __('Announcement List') }}</h4>
                </div>
                <div class="card bg-none min-height-443">
                    <div class="table-responsive">
                        <table class="table align-items-center">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($announcements as $announcement)
                                    <tr>
                                        <td>{{ $announcement->title }}</td>
                                        <td>{{ \Auth::user()->dateFormat($announcement->start_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($announcement->end_date) }}</td>
                                        <td>{{ $announcement->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4 class="h4 font-weight-400 float-left">{{ __('Event View') }}</h4>
                <div class="card widget-calendar min-height-460">
                    <div class="card-header ">
                        <div class="row">
                            <div class="col-xl-2 col-lg-3 col-md-2 col-sm-2">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="#" class="fullcalendar-btn-prev btn btn-sm btn-neutral">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                    <a href="#" class="fullcalendar-btn-next btn btn-sm btn-neutral">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </div>

                            </div>
                            <div class="col-xl-5 col-lg-4 col-md-5 col-sm-6 text-center">
                                <h5 class="fullcalendar-title h4 d-inline-block font-weight-600 mb-0">{{ __('Calendar') }}
                                </h5>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-4 text-lg-right">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="#" class="btn btn-sm btn-neutral"
                                        data-calendar-view="month">{{ __('Month') }}</a>
                                    <a href="#" class="btn btn-sm btn-neutral"
                                        data-calendar-view="basicWeek">{{ __('Week') }}</a>
                                    <a href="#" class="btn btn-sm btn-neutral"
                                        data-calendar-view="basicDay">{{ __('Day') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="calendar" data-toggle="event_calendar"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="">
                    <h4 class="h4 font-weight-400 float-left">{{ __('Meeting schedule') }}</h4>
                </div>
                <div class="card bg-none min-height-460">
                    <div class="table-responsive">
                        <table class="table align-items-center">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Time') }}</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($meetings as $meeting)
                                    <tr>
                                        <td>{{ $meeting->title }}</td>
                                        <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                        <td>{{ \Auth::user()->timeFormat($meeting->time) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@php
    $isArray = is_array($arrEvents);
    $eventsJson = $isArray ? json_encode($arrEvents) : $arrEvents;
@endphp

@push('theme-script')
    <script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>
@endpush
@push('script-page')
    <script>


        document.getElementById('testMusicButton').addEventListener('click', () => {
            // Open modal
            const modal = new bootstrap.Modal(document.getElementById('musicModal'));
            modal.show();

            // Play music
            const audio = new Audio('/storage/app/public/bday.mp3'); // Replace with your file path
            audio.play();
        });

        // event_calendar (not working now)
        /*var e, t, a = $('[data-toggle="event_calendar"]');
        a.length && (t = {
                header: {
                    right: "",
                    center: "",
                    left: ""
                },
                buttonIcons: {
                    prev: "calendar--prev",
                    next: "calendar--next"
                },
                theme: !1,
                selectable: !0,
                selectHelper: !0,
                editable: !0,
                events: {!! json_encode($arrEvents) !!},
                eventStartEditable: !1,
                locale: '{{ basename(App::getLocale()) }}',
                dayClick: function(e) {
                    var t = moment(e).toISOString();
                    $("#new-event").modal("show"), $(".new-event--title").val(""), $(".new-event--start").val(t), $(
                        ".new-event--end").val(t)
                },
                eventResize: function(event) {
                    var eventObj = {
                        start: event.start.format(),
                        end: event.end.format(),
                    };

                    // $.ajax({
                    //     url: event.resize_url,
                    //     method: 'PUT',
                    //     data: eventObj,
                    //     success: function (response) {
                    //     },
                    //     error: function (data) {
                    //         data = data.responseJSON;
                    //     }
                    // });
                },
                viewRender: function(t) {
                    e.fullCalendar("getDate").month(), $(".fullcalendar-title").html(t.title)
                },
                eventClick: function(e, t) {
                    var title = e.title;
                    var url = e.url;

                    if (typeof url != 'undefined') {
                        $("#commonModal .modal-title").html(title);
                        $("#commonModal .modal-dialog").addClass('modal-md');
                        $("#commonModal").modal('show');
                        $.get(url, {}, function(data) {
                            $('#commonModal .modal-body').html(data);
                        });
                        return false;
                    }
                }
            }, (e = a).fullCalendar(t),
            $("body").on("click", "[data-calendar-view]", function(t) {
                t.preventDefault(), $("[data-calendar-view]").removeClass("active"), $(this).addClass("active");
                var a = $(this).attr("data-calendar-view");
                e.fullCalendar("changeView", a)
            }), $("body").on("click", ".fullcalendar-btn-next", function(t) {
                t.preventDefault(), e.fullCalendar("next")
            }), $("body").on("click", ".fullcalendar-btn-prev", function(t) {
                t.preventDefault(), e.fullCalendar("prev")
            }));*/

        var events = {!! $eventsJson !!};
        //console.log(events);
        var e, t, a = $('[data-toggle="event_calendar"]');
        a.length && (t = {
            header: {right: "", center: "", left: ""},
            buttonIcons: {prev: "calendar--prev", next: "calendar--next"},
            theme: !1,
            selectable: !0,
            selectHelper: !0,
            editable: !0,
            events: events ,
            eventStartEditable: !1,
            locale: '{{basename(App::getLocale())}}',

            eventResize: function (event) {
                var eventObj = {
                    start: event.start.format(),
                    end: event.end.format(),
                };

                $.ajax({
                    url: event.resize_url,
                    method: 'PUT',
                    data: eventObj,
                    success: function (response) {

                    },
                    error: function (data) {
                        data = data.responseJSON;
                    }
                });
            },
            viewRender: function (t) {
                e.fullCalendar("getDate").month(), $(".fullcalendar-title").html(t.title)
            },
            eventClick: function (e, t) {
                var title = e.title;
                var url = e.url;
                var description = e.description;

                if (typeof url != 'undefined') {
                    $("#commonModal .modal-title").html(title);
                    $("#commonModal .modal-dialog").addClass('modal-md');

                    var formattedDescription = '<p>' + description.replace(/\n/g, '<br>') + '</p>';

                    $("#commonModal .modal-body").html(formattedDescription);
                    $("#commonModal").modal('show');
                    $.get(url, {}, function (data) {
                        $('#commonModal .modal-body').append(data);
                    });
                    return false;
                }
            }

        }, (e = a).fullCalendar(t),
            $("body").on("click", "[data-calendar-view]", function (t) {
                t.preventDefault(), $("[data-calendar-view]").removeClass("active"), $(this).addClass("active");
                var a = $(this).attr("data-calendar-view");
                e.fullCalendar("changeView", a)
            }), $("body").on("click", ".fullcalendar-btn-next", function (t) {
            t.preventDefault(), e.fullCalendar("next")
        }), $("body").on("click", ".fullcalendar-btn-prev", function (t) {
            t.preventDefault(), e.fullCalendar("prev")
        }));

    </script>

<script>
    $(document).ready(function() {
    let timerInterval;
    // bdayAnimation();
    // Function to format time in HH:MM:SS format
    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const remainingSeconds = seconds % 60;

        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    // Function to start the timer
    function startTimer(startTime) {
        if(startTime){
            const startTimestamp = Date.parse(startTime);
            localStorage.setItem('startTime', startTimestamp);
        }else{
            startTimestamp = Date.now();
            localStorage.setItem('startTime', startTimestamp);
        }
        localStorage.setItem('isRunning', 'true');
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    }

    // Function to update the timer display
    function updateTimer() {
        const startTimestamp = parseInt(localStorage.getItem('startTime'));
        if (!isNaN(startTimestamp)) {
            const currentTime = Date.now();
            const elapsedTimeInSeconds = Math.floor((currentTime - startTimestamp) / 1000);
            document.getElementById('timer-display').textContent = formatTime(elapsedTimeInSeconds);
        } else {
            document.getElementById('timer-display').textContent = "00:00:00";
        }
    }

    function convertToFormattedTime(timeString) {
        var timeParts = timeString.split(':');
        var hours = parseInt(timeParts[0], 10);
        var minutes = parseInt(timeParts[1], 10);
        var seconds = parseInt(timeParts[2], 10);

        // Convert hours and minutes to seconds
        var totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

        // Create a formatted string based on the time parts
        var formattedTime = '';

        // Add hours if greater than 0
        if (hours > 0) {
            formattedTime += hours + 'hr ';
        }

        // Add minutes if greater than 0 or if hours are present
        if (minutes > 0 || hours > 0) {
            formattedTime += minutes + 'm ';
        }

        // Always add seconds
        formattedTime += seconds + 's';

        // Trim any extra spaces at the end
        return {
            totalSeconds: totalSeconds,
            formattedTime: formattedTime.trim()
        };
    }

    // Function to stop the timer
    function stopTimer() {
        clearInterval(timerInterval);
        localStorage.setItem('isRunning', 'false');
    }

    // Function to initialize the timer on page load
    function initializeTimer() {
        $.ajax({
            url: "{{ url('attendanceemployee/current-timer-state') }}",
            type: "GET",
            success: function(response) {
                if (response.clock_in) {
                    startTimer(response.clock_in);
                    $('#clock_in').attr('disabled', 'disabled');
                    $('#clock_out').removeAttr('disabled');
                }
            }
        });
    }

    // Event listeners for clock in and clock out
     // Add event listeners once the DOM content is fully loaded
        $(document).ready(function() {

            $('#clock_in').click(function() {
                let currentTime = new Date().toLocaleTimeString('en-GB', { hour12: false });
                startTimer();
                $('#clock_in').attr('disabled', 'disabled');
                $('#clock_in').addClass('disabled');
                $.ajax({
                    url: "{{ url('attendanceemployee/attendance') }}",
                    type: "POST",
                    data: {
                        _token: $('input[name="_token"]').val(),
                        time: currentTime
                    },
                    success: function(response) {
                        toastr.success('Clocked in successfully at ' + currentTime);
                        var totalRest = convertToFormattedTime(response.totalRest);
                        var late = convertToFormattedTime(response.late);

                        $('#clock_out').removeAttr('disabled');

                        $('#clock_out').removeClass('disabled');

                        var timeDisplay = '';
                        if (response.totalRest === "00:00:00") {
                            timeDisplay = `${late.formattedTime} (Late)`;
                        } else {
                            timeDisplay = `${totalRest.formattedTime} (Rest)`;
                        }

                        $('.attendanceBody').prepend(`
                            <tr>
                                <td>${currentTime}</td>
                                <td></td>
                                <td></td>
                                <td>${timeDisplay}</td>
                            </tr>
                        `);

                        // Check if it's the employee's birthday
                        if (response.is_birthday != '') {
                            setTimeout(function() {
                                $('canvas#birthday').toggleClass('d-none');
                                bdayAnimation();
                                $('#musicModal').modal('show');
                                $('div.modal-backdrop').removeClass('modal-backdrop');
                                var audioPlayer = document.getElementById('audioPlayer');
                                audioPlayer.load();
                                audioPlayer.play();
                            }, 100); // Show modal after 1 minute (60000 milliseconds)
                        }
                    },
                    error: function(response) {
                        if (response.responseJSON) {
                            alert(response.responseJSON[0]);
                        } else {
                            alert('Error clocking in');
                        }
                        location.reload();
                    }
                });
            });

            $('#clock_out').click(function() {
                let currentTime = new Date().toLocaleTimeString('en-GB', { hour12: false });
                let attendanceId = document.getElementById('att_id').value;
                stopTimer();
                $.ajax({
                    url: "{{ route('attendanceemployee.update', ':id') }}".replace(':id', attendanceId),
                    type: "POST",
                    data: {
                        _token: $('input[name="_token"]').val(),
                        _method: 'PUT',
                        time: currentTime
                    },
                    success: function(response) {
                        //console.log(response);
                        if(response == 'success'){
                            toastr.success('Clocked out successfully at ' + currentTime);
                            $('#clock_out').attr('disabled');
                            $('#clock_in').removeAttr('disabled');
                            $('#clock_out').addClass('disabled');
                            $('#clock_in').removeClass('disabled');
                            location.reload();
                        }
                        else{
                            console.log(response.message);
                            toastr.error('ERROR !!');
                        }
                    },
                    error: function(response) {
                        alert('Error clocking out.');
                        location.reload();
                    }
                });
            });
            // Initialize the timer
            initializeTimer();
        });

        function bdayAnimation(){
    // helper functions
const PI2 = Math.PI * 2
const random = (min, max) => Math.random() * (max - min + 1) + min | 0
const timestamp = _ => new Date().getTime()

// container
class Birthday {
  constructor() {
    this.resize()

    // create a lovely place to store the firework
    this.fireworks = []
    this.counter = 0

  }

  resize() {
    this.width = canvas.width = window.innerWidth
    let center = this.width / 2 | 0
    this.spawnA = center - center / 4 | 0
    this.spawnB = center + center / 4 | 0

    this.height = canvas.height = window.innerHeight
    this.spawnC = this.height * .1
    this.spawnD = this.height * .5

  }

  onClick(evt) {
     let x = evt.clientX || evt.touches && evt.touches[0].pageX
     let y = evt.clientY || evt.touches && evt.touches[0].pageY

     let count = random(3,5)
     for(let i = 0; i < count; i++) this.fireworks.push(new Firework(
        random(this.spawnA, this.spawnB),
        this.height,
        x,
        y,
        random(0, 260),
        random(30, 110)))

     this.counter = -1

  }

  update(delta) {
    ctx.globalCompositeOperation = 'hard-light'
    ctx.fillStyle = `rgba(20,20,20,${ 7 * delta })`
    ctx.fillRect(0, 0, this.width, this.height)

    ctx.globalCompositeOperation = 'lighter'
    for (let firework of this.fireworks) firework.update(delta)

    // if enough time passed... create new new firework
    this.counter += delta * 3 // each second
    if (this.counter >= 1) {
      this.fireworks.push(new Firework(
        random(this.spawnA, this.spawnB),
        this.height,
        random(0, this.width),
        random(this.spawnC, this.spawnD),
        random(0, 360),
        random(30, 110)))
      this.counter = 0
    }

    // remove the dead fireworks
    if (this.fireworks.length > 1000) this.fireworks = this.fireworks.filter(firework => !firework.dead)

  }
}

class Firework {
  constructor(x, y, targetX, targetY, shade, offsprings) {
    this.dead = false
    this.offsprings = offsprings

    this.x = x
    this.y = y
    this.targetX = targetX
    this.targetY = targetY

    this.shade = shade
    this.history = []
  }
  update(delta) {
    if (this.dead) return

    let xDiff = this.targetX - this.x
    let yDiff = this.targetY - this.y
    if (Math.abs(xDiff) > 3 || Math.abs(yDiff) > 3) { // is still moving
      this.x += xDiff * 2 * delta
      this.y += yDiff * 2 * delta

      this.history.push({
        x: this.x,
        y: this.y
      })

      if (this.history.length > 20) this.history.shift()

    } else {
      if (this.offsprings && !this.madeChilds) {

        let babies = this.offsprings / 2
        for (let i = 0; i < babies; i++) {
          let targetX = this.x + this.offsprings * Math.cos(PI2 * i / babies) | 0
          let targetY = this.y + this.offsprings * Math.sin(PI2 * i / babies) | 0

          birthday.fireworks.push(new Firework(this.x, this.y, targetX, targetY, this.shade, 0))

        }

      }
      this.madeChilds = true
      this.history.shift()
    }

    if (this.history.length === 0) this.dead = true
    else if (this.offsprings) {
        for (let i = 0; this.history.length > i; i++) {
          let point = this.history[i]
          ctx.beginPath()
          ctx.fillStyle = 'hsl(' + this.shade + ',100%,' + i + '%)'
          ctx.arc(point.x, point.y, 1, 0, PI2, false)
          ctx.fill()
        }
      } else {
      ctx.beginPath()
      ctx.fillStyle = 'hsl(' + this.shade + ',100%,50%)'
      ctx.arc(this.x, this.y, 1, 0, PI2, false)
      ctx.fill()
    }

  }
}

let canvas = document.getElementById('birthday')
let ctx = canvas.getContext('2d')

let then = timestamp()

let birthday = new Birthday
window.onresize = () => birthday.resize()
document.onclick = evt => birthday.onClick(evt)
document.ontouchstart = evt => birthday.onClick(evt)

  ;(function loop(){
  	requestAnimationFrame(loop)

  	let now = timestamp()
  	let delta = now - then

    then = now
    birthday.update(delta / 1000)


  })()
}
});

</script>

@endpush
