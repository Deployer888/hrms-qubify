@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Monthly Attendance') }}
@endsection
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endpush

@php use \Carbon\Carbon; @endphp

@section('action-button')
    
@endsection

@section('content')
    <style>
        .white{
            color: white;
        }
        .yellow{
            color: yellow;
        }
        .red{
            color: #b62323;
        }
        .table td, .table th {
            font-size: 1rem!important;
        }
        thead .col-sticky {
            position: sticky !important;
            top: 0 !important;
            background-color: #f8f9fa; /* Background color to avoid overlap */
            z-index: 5 !important; /* Higher z-index for header */
            white-space: nowrap;
            background:#fff;
        }
        tbody .sticky-col,thead .sticky-col {
            position: sticky;
            left: 0;
            background-color: #f8f9fa; /* Background color to avoid overlap */
            z-index: 999;
            white-space: nowrap;
        }
    </style>

    <div class="row d-flex justify-content-end">
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12">
            <form action="{{ route('report.monthly.attendance') }}" method="get" id="report_monthly_attendance">
                <div class="all-select-box">
                    <div class="btn-box">
                        <label for="month" class="text-type">{{ __('Month') }}</label>
                        <input type="month" name="month"
                            value="{{ request()->get('month', date('Y-m')) }}"
                            class="month-btn form-control">
                    </div>
                </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-12">
            <div class="all-select-box">
                <div class="btn-box">
                    <label for="branch" class="text-type">{{ __('Branch') }}</label>
                    <select name="branch" class="form-control select2">
                        @foreach ($branch as $key => $value)
                            <option value="{{ empty($key) ? 0 : $key }}"
                                {{ request()->get('branch') == $key ? 'selected' : '' }}>
                                {{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-12">
            <div class="all-select-box">
                <div class="btn-box">
                    <label for="department" class="text-type">{{ __('Department') }}</label>
                    <select name="department" class="form-control select2">
                        @foreach ($department as $key => $value)
                            <option value="{{ empty($key) ? 0 : $key }}"
                                {{ request()->get('department') == $key ? 'selected' : '' }}>
                                {{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-auto my-custom">
            <a href="#" class="apply-btn"
                onclick="document.getElementById('report_monthly_attendance').submit(); return false;" data-toggle="tooltip"
                data-original-title="{{ __('Apply') }}">
                <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
            </a>
            <a href="{{ route('report.monthly.attendance') }}" class="reset-btn" data-toggle="tooltip"
                data-original-title="{{ __('Reset') }}">
                <span class="btn-inner--icon"><i class="fas fa-undo"></i></span>
            </a>
            <a href="{{ route('report.attendance', [
                        'month' => request()->get('month', date('Y-m')),
                        'branch' => request()->get('branch', 0),
                        'department' => request()->get('department', 0)
                    ]) }}"
                class="action-btn" data-toggle="tooltip" data-original-title="{{ __('Download') }}">
                <span class="btn-inner--icon"><i class="fas fa-download"></i></span>
            </a>
        </div>
    </div>
    </form>

    <div id="printableArea">
        <div class="row mt-3">
            <div class="col">
                <input type="hidden"
                    value="{{ $data['curMonth'] . ' ' . __('Attendance Report') }}"
                    id="filename">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0">{{ __('Report') }} :</h5>
                    <h5 class="report-text mb-0">{{ __('Attendance Summary') }}</h5>
                </div>
            </div>
            @if ($branch != 'All')
                <div class="col">
                    <div class="card p-4 mb-4">
                        <h5 class="report-text gray-text mb-0">{{ __('Branch') }} :</h5>
                        <h5 class="report-text mb-0">{{ $data['branch'] }}</h5>
                    </div>
                </div>
            @endif
            @if ($department != 'All')
                <div class="col">
                    <div class="card p-4 mb-4">
                        <h5 class="report-text gray-text mb-0">{{ __('Department') }} :</h5>
                        <h5 class="report-text mb-0">{{ $data['department'] }}</h5>
                    </div>
                </div>
            @endif
            <div class="col">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0">{{ __('Month') }} :</h5>
                    <h5 class="report-text mb-0">{{ $data['curMonth'] }}</h5>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6 col-lg-3">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0">{{ __('Total Present') }}</h5>
                    <h5 class="report-text mb-0">{{ $data['totalPresent'] }}</h5>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-lg-3">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0">{{ __('Total Leave') }}</h5>
                    <h5 class="report-text mb-0">{{ $data['totalLeave'] }}</h5>
                </div>
            </div>
            <!--<div class="col-xl-3 col-md-6 col-lg-3">-->
            <!--    <div class="card p-4 mb-4">-->
            <!--        <h5 class="report-text gray-text mb-0">{{-- __('Total Overtime (hrs)') --}}</h5>-->
            <!--        <h5 class="report-text mb-0">{{-- number_format($data['totalOvertime'], 2) --}}</h5>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-xl-3 col-md-6 col-lg-3">-->
            <!--    <div class="card p-4 mb-4">-->
            <!--        <h5 class="report-text gray-text mb-0">{{-- __('Total Early Leave (hrs)') --}}</h5>-->
            <!--        <h5 class="report-text mb-0">{{-- number_format($data['totalEarlyLeave'], 2) --}}</h5>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-xl-3 col-md-6 col-lg-3">-->
            <!--    <div class="card p-4 mb-4">-->
            <!--        <h5 class="report-text gray-text mb-0">{{-- __('Total Late (hrs)') --}}</h5>-->
            <!--        <h5 class="report-text mb-0">{{-- number_format($data['totalLate'], 2) --}}</h5>-->
            <!--    </div>-->
            <!--</div>-->
        </div>

        <div class="row">
            <div class="col">
               @if(count($employeesAttendance) > 0)
                    <div class="card">
                        <div class="table-responsive py-4 attendance-table-responsive">
                            <table class="table table-striped mb-0" id="dataTable-1">
                                <thead>
                                    <tr class="text-center">
                                        <th  class="sticky-col bg-white">{{ __('Name') }}</th>
                                        @php 
                                            $todayDate = \Carbon\Carbon::today()->day; 
                                            $todayMonth = Carbon::today()->month;
                                            $todayYear = Carbon::today()->year;
                                        @endphp
                                        @foreach ($dates as $date)
                                            @if ($date <= $todayDate || (isset($_GET['month']) && $todayYear.'-'.$todayMonth != $_GET['month']))
                                                <th class="col-sticky">{{ $date }}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($employeesAttendance as $attendance)
                                        <tr>
                                            <td class="sticky-col">{{ $attendance['name'] }}</td>
                                            @foreach ($attendance['attendance'] as $status)
                                                @php $backgroundColor = $attendance['backgroundColor']; @endphp
                                                <td>
                                                    <span class="badge {{ 
                                                        $status == 'Absent' ? 'badge-danger' : 
                                                        ($status == 'Holiday' ? 'badge-info' : 
                                                        ($status == 'Week-End' ? 'badge-primary' : 
                                                        (($status == 'Full-Day' || $status == 'Half-Day' || $status == 'Short-Day') ? 'badge-warning text-dark' : ($status == 'N/A' ? '' : (empty($backgroundColor) ? 'badge-success' : '' ))))) 
                                                    }}">{!! $status !!}</span>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <p class="text-center m-5">No attendance data available.</p>
                @endif

            </div>
        </div>
    </div>
@endsection
