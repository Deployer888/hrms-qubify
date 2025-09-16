@extends('layouts.admin')

@section('page-title')
    {{ __('Attendance Analytics') }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/emp-dashnew.css') }}">
    <style>
        /* Premium Attendance Analytics Styles */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --info-gradient: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            --leave-gradient: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            --absent-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --card-shadow: 0 15px 35px rgba(0,0,0,0.08);
            --card-shadow-hover: 0 25px 50px rgba(0,0,0,0.15);
            --border-radius: 20px;
            --border-radius-sm: 12px;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            position: relative;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(102, 126, 234, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .container-fluid {
            position: relative;
            z-index: 1;
        }

        /* Premium Cards */
        .premium-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255,255,255,0.8);
            backdrop-filter: blur(15px);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
            margin-bottom: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .premium-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .premium-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }

        /* Enhanced Attendance Stats Cards */
        .attendance-stats-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255,255,255,0.8);
            backdrop-filter: blur(15px);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
            margin-bottom: 0 !important;
            height: 160px;
        }

        .attendance-stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .attendance-stats-card:nth-child(1)::before {
            background: var(--success-gradient);
        }

        .attendance-stats-card:nth-child(2)::before {
            background: var(--info-gradient);
        }

        .attendance-stats-card:nth-child(3)::before {
            background: var(--danger-gradient);
        }

        .attendance-stats-card:nth-child(4)::before {
            background: var(--warning-gradient);
        }

        .attendance-stats-card:nth-child(5)::before {
            background: var(--primary-gradient);
        }

        .attendance-stats-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--card-shadow-hover);
        }

        .attendance-stats-card .card-body {
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            position: relative;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stats-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .attendance-stats-card:hover .stats-icon::before {
            transform: translateX(100%);
        }

        .stats-icon i {
            font-size: 28px;
            color: white;
            z-index: 1;
            position: relative;
        }

        /* Enhanced icon colors for each stat */
        .attendance-stats-card:nth-child(1) .stats-icon {
            background: var(--success-gradient);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .attendance-stats-card:nth-child(2) .stats-icon {
            background: var(--info-gradient);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .attendance-stats-card:nth-child(3) .stats-icon {
            background: var(--danger-gradient);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }

        .attendance-stats-card:nth-child(4) .stats-icon {
            background: var(--warning-gradient);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }

        .attendance-stats-card:nth-child(5) .stats-icon {
            background: var(--primary-gradient);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .attendance-stats-card:hover .stats-icon {
            transform: scale(1.15) rotate(10deg);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .attendance-stats-card h5 {
            font-size: 28px;
            font-weight: 900;
            color: #1f2937;
            margin: 12px 0 8px 0;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .attendance-stats-card small {
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .attendance-stats-card p {
            font-size: 13px;
            color: #6b7280;
            font-weight: 700;
            margin: 0;
            text-align: center;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Enhanced Card Headers */
        .premium-card-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            padding: 25px 30px;
            border-bottom: 1px solid rgba(102, 126, 234, 0.15);
            position: relative;
        }

        .premium-card-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 30px;
            right: 30px;
            height: 2px;
            background: var(--primary-gradient);
            border-radius: 1px;
        }

        .premium-card-header h4 {
            font-size: 20px;
            font-weight: 800;
            color: #1f2937;
            margin: 0;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Enhanced Card Bodies */
        .premium-card-body {
            padding: 30px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .premium-card-body.compact {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Enhanced Buttons */
        .premium-btn {
            padding: 15px 25px;
            border-radius: var(--border-radius-sm);
            border: none;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .premium-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: var(--transition);
        }

        .premium-btn:hover::before {
            left: 100%;
            transition: left 0.6s ease-in-out;
        }

        .btn-success {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-success:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.5);
            color: white;
        }

        .btn-danger {
            background: var(--danger-gradient);
            color: white;
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }

        .btn-danger:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(239, 68, 68, 0.5);
            color: white;
        }

        .btn-success.disabled,
        .btn-danger.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: var(--card-shadow) !important;
        }

        /* Enhanced Tables */
        .premium-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            background: transparent;
            border-radius: var(--border-radius-sm);
            overflow: hidden;
        }

        .premium-table thead {
            background: var(--primary-gradient);
        }

        .premium-table thead th {
            color: white;
            font-weight: 800;
            padding: 20px 25px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            position: relative;
        }

        .premium-table thead th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 25px;
            right: 25px;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }

        .premium-table tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .premium-table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            transform: scale(1.01);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
        }

        .premium-table tbody tr:last-child {
            border-bottom: none;
        }

        .premium-table tbody td {
            padding: 18px 25px;
            vertical-align: middle;
            font-size: 14px !important;
            font-weight: 600;
            border: none;
            color: #1f2937;
        }

        .premium-table tbody tr td:hover {
            cursor: pointer;
        }

        /* Enhanced Chart Container */
        .chart-container {
            position: relative;
            height: 320px;
            width: 100%;
            padding: 20px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
            border-radius: var(--border-radius-sm);
            margin: 10px 0;
        }

        .chart-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #attendanceChart {
            max-height: 280px !important;
            max-width: 280px !important;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.1));
        }

        /* Enhanced Clock Forms */
        .clock-forms {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding: 30px 30px 0 30px;
            justify-content: flex-start;
            align-items: center;
        }

        /* Enhanced Modal */
        .premium-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            backdrop-filter: blur(15px);
        }

        .premium-modal.show {
            opacity: 1;
            visibility: visible;
        }

        .premium-modal-content {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: var(--border-radius);
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
            max-width: 550px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            transform: translateY(50px) scale(0.9);
            transition: var(--transition);
        }

        .premium-modal.show .premium-modal-content {
            transform: translateY(0) scale(1);
        }

        .premium-modal-header {
            background: var(--primary-gradient);
            padding: 30px 35px;
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .premium-modal-header h3 {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .premium-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .premium-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1) rotate(90deg);
        }

        .premium-modal-body {
            padding: 35px;
        }

        /* Enhanced Chart Legend */
        .chart-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .chart-container {
                height: 300px;
                padding: 15px;
            }
        }

        @media (max-width: 768px) {
            .clock-forms {
                flex-direction: column;
                gap: 15px;
                padding: 25px 25px 0 25px;
            }

            .premium-btn {
                justify-content: center;
                width: 100%;
                padding: 18px 25px;
            }

            .attendance-stats-card {
                height: 140px;
            }

            .attendance-stats-card h5 {
                font-size: 24px;
            }

            .chart-container {
                height: 280px;
                padding: 15px;
            }

            .premium-table thead th,
            .premium-table tbody td {
                padding: 15px 20px;
                font-size: 12px !important;
            }

            .premium-card-body {
                padding: 25px;
            }

            .premium-card-body.compact {
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .attendance-stats-card {
                height: 130px;
            }

            .stats-icon {
                width: 50px;
                height: 50px;
            }

            .stats-icon i {
                font-size: 24px;
            }

            .attendance-stats-card h5 {
                font-size: 22px;
            }

            .attendance-stats-card small,
            .attendance-stats-card p {
                font-size: 10px;
            }

            .chart-container {
                height: 260px;
                padding: 15px;
            }

            .premium-card-header h4 {
                font-size: 18px;
            }
        }

        /* Enhanced Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        .attendance-stats-card {
            animation: fadeInUp 0.8s ease-out;
        }

        .attendance-stats-card:nth-child(1) { animation-delay: 0.1s; }
        .attendance-stats-card:nth-child(2) { animation-delay: 0.2s; }
        .attendance-stats-card:nth-child(3) { animation-delay: 0.3s; }
        .attendance-stats-card:nth-child(4) { animation-delay: 0.4s; }
        .attendance-stats-card:nth-child(5) { animation-delay: 0.5s; }

        /* Loading states */
        .chart-loading {
            animation: pulse 2s infinite;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }

        /* Enhanced empty states */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 48px;
            color: rgba(102, 126, 234, 0.3);
            margin-bottom: 15px;
        }

        .empty-state h5 {
            color: #6b7280;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .empty-state p {
            color: #9ca3af;
            font-size: 14px;
            margin: 0;
        }
    </style>
@endpush

@section('content')
    @php
        use App\Helpers\Helper;
        $totalTimeDifference = '';

        if (isset($employeeAttendanceList))
            $totalTimeDifference = Helper::calculateTotalTimeDifference($employeeAttendanceList[0]->attendance);
    @endphp

    @if (session('status'))     
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <input type="hidden" id="att_id" value="{{ isset($employeeAttendance) ? $employeeAttendance->employee_id : '' }}" />
    
    @if (\Auth::user()->type == 'employee')
        <!-- Enhanced Attendance Statistics (Original 5 cards) -->
        <div class="col-12 mb-4">
            <div class="row">
                <div class="col-lg col-md-6 col-sm-6 mb-3">
                    <div class="attendance-stats-card">
                        <div class="card-body">
                            <div class="stats-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5>{{ $attendanceMetrics['presentRate'] }}%</h5>
                            <small>{{ $attendanceMetrics['presentDays'] }} / {{ $attendanceMetrics['totalDays'] }} days</small>
                            <p>{{ __('Present Rate') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md-6 col-sm-6 mb-3">
                    <div class="attendance-stats-card">
                        <div class="card-body">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5>
                                @if($attendanceMetrics['avgCheckIn'])
                                    {{ $attendanceMetrics['avgCheckIn'] }}
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </h5>
                            <p>{{ __('Avg Check-in') }}<br>{{ __('Time') }}</p>
                        </div>
                    </div>
                </div>
            
                <div class="col-lg col-md-6 col-sm-6 mb-3">
                    <div class="attendance-stats-card">
                        <div class="card-body">
                            <div class="stats-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <h5>{{ $attendanceMetrics['absentRate'] }}%</h5>
                            <small>{{ $attendanceMetrics['absentDays'] }} / {{ $attendanceMetrics['totalDays'] }} days</small>
                            <p>{{ __('Absent Rate') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md-6 col-sm-6 mb-3">
                    <div class="attendance-stats-card">
                        <div class="card-body">
                            <div class="stats-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <h5>{{ $attendanceMetrics['lateRate'] }}%</h5>
                            <small>{{ $attendanceMetrics['lateDays'] }} / {{ $attendanceMetrics['presentDays'] }} days</small>
                            <p>{{ __('Late Rate') }}</p>
                        </div>
                    </div>
                </div>
            
                <div class="col-lg col-md-6 col-sm-6 mb-3">
                    <div class="attendance-stats-card">
                        <div class="card-body">
                            <div class="stats-icon">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <h5>{{ $totalTimeDifference }}</h5>
                            <p>{{ __('Total Working') }}<br>{{ __('Time') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Clock In/Out Section -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="premium-card" style="min-height: 500px;">
                    <div class="premium-card-header d-flex justify-content-between align-items-center">
                        <h4>{{ __('Attendance Log') }}</h4>
                        <h4>{{ isset(auth()->user()->employee->office) ? auth()->user()->employee->office->name : ''}}</h4>
                    </div>

                    <div class="premium-card-body">
                        <div class="clock-forms">
                            <form id="clock_in_form">
                                @csrf
                                @php
                                    $employeeId = \Auth::user()->employee->id;
                                    $alwaysAllowed = [];
                                    $disabledIn = 'disabled';
                                    if (empty($employeeAttendance) || $employeeAttendance->clock_out != '00:00:00')
                                        $disabledIn = '';
                                @endphp
                                
                                @if(\Auth::check() && \Auth::user()->employee)
                                    <button type="button" value="0" name="in" id="clock_in"
                                        class="premium-btn btn-success {{ $disabledIn }}"
                                        {{ $disabledIn }}>
                                        <i class="fas fa-clock"></i>{{ __('Clock In') }}
                                    </button>
                                @endif
                            </form>
                            
                            <form id="clock_out_form">
                                @csrf
                                @method('POST')
                                @php
                                    $disabledOut = 'disabled';
                                    if (!empty($employeeAttendance) && $employeeAttendance->clock_out == '00:00:00')
                                        $disabledOut = '';
                                @endphp
                                
                                @if(\Auth::check() && \Auth::user()->employee)
                                    <button type="button" value="1" name="out" id="clock_out"
                                        class="premium-btn btn-danger {{ $disabledOut }}" {{ $disabledOut }}>
                                        <i class="fas fa-sign-out-alt"></i>{{ __('Clock Out') }}
                                    </button>
                                @endif
                            </form>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Clock In') }}</th>
                                        <th>{{ __('Clock Out') }}</th>
                                        <th>{{ __('Time') }}</th>
                                        <th>{{ __('Late/Rest Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="attendanceBody">
                                    @forelse($employeeAttendanceList as $k => $attEmp)
                                        @foreach($attEmp['attendance'] as $key => $attendance)
                                            <tr>
                                                <td>{{ $attendance->clock_in }}</td>
                                                <td>
                                                    @if($attendance->clock_out != '00:00:00')
                                                        {{ $attendance->clock_out ?? '' }}
                                                    @endif
                                                </td>
                                                @php
                                                    if (!function_exists('formatTimeDifference')) {
                                                        function formatTimeDifference($checkIn, $checkOut) {
                                                            $checkInTime = strtotime($checkIn);
                                                            $checkOutTime = strtotime($checkOut);
                                                            $diffInSeconds = abs($checkOutTime - $checkInTime);
                                                            if ($diffInSeconds < 60) {
                                                                return $diffInSeconds . ' secs';
                                                            }
                                                            $diffInMinutes = floor($diffInSeconds / 60);
                                                            if ($diffInMinutes < 60) {
                                                                return $diffInMinutes . ' mins';
                                                            }
                                                            $diffInHours = floor($diffInMinutes / 60);
                                                            $remainingMinutes = $diffInMinutes % 60;
                                                            return $diffInHours . ' hrs ' . $remainingMinutes . ' mins';
                                                        }
                                                    }
                                                    $checkIn = $attendance->clock_in;
                                                    $checkOut = $attendance->clock_out;
                                                    $formattedDifference = '';
                                                    if ($checkOut != '00:00:00') {
                                                        $formattedDifference = formatTimeDifference($checkIn, $checkOut);
                                                    }
                                                @endphp
                                                <td>{{ $formattedDifference }}</td>
                                                <td>
                                                    @if($key)
                                                        {{Helper::dynRestTime($attEmp['attendance'][$key-1]->clock_out??'',$attEmp['attendance'][$key]->clock_in)}}
                                                    @else
                                                        {{Helper::dynLateTime(\Auth::user()->employee->shift_start??'09:00:00',$attendance->clock_in)}}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4" class="empty-state">
                                                <i class="fas fa-clock"></i>
                                                <h5>{{ __('No Attendance Records') }}</h5>
                                                <p>{{ __('Start tracking your attendance by clocking in') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="premium-card" style="min-height: 500px;">
                    <div class="premium-card-header">
                        <h4>{{ __('Monthly Attendance') }}</h4>
                    </div>
                    <div class="premium-card-body compact">
                        <p class="text-center mb-3" style="font-weight: 600; color: #1f2937;">
                            <strong>{{ date('d M Y', strtotime('first day of this month')) }} - {{ date('d M Y') }}</strong>
                        </p>
                        <div class="chart-container">
                            <div class="chart-wrapper">
                                <div id="chart-placeholder" class="chart-loading" style="text-align: center; padding: 30px; color: #6b7280;">
                                    <i class="fas fa-spinner fa-spin mb-3" style="font-size: 32px; color: #667eea;"></i><br>
                                    <h5 style="color: #6b7280; margin-bottom: 5px;">Loading Chart...</h5>
                                    <p style="font-size: 12px; color: #9ca3af;">Preparing your attendance data</p>
                                </div>
                                <canvas id="attendanceChart" style="display: none;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Event View, Announcements, and Meetings Row -->
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="premium-card" style="min-height: 450px;">
                    <div class="premium-card-header">
                        <h4>{{ __('Event View') }}</h4>
                    </div>
                    <div class="premium-card-body compact">
                        <div class="table-responsive" style="flex: 1;">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Title') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($events as $event)
                                        <tr onclick="openModal('{{ $event->id }}')">
                                            <td style="width: 30%; cursor: pointer;">
                                                {{ date('d-m-Y', strtotime($event->start_date)) }}
                                            </td>
                                            <td style="cursor: pointer;">
                                                {{ $event->title }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="empty-state">
                                                <i class="fas fa-calendar-times"></i>
                                                <h5>{{ __('No Events') }}</h5>
                                                <p>{{ __('No events scheduled') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="premium-card" style="min-height: 450px;">
                    <div class="premium-card-header">
                        <h4>{{ __('Announcement List') }}</h4>
                    </div>
                    <div class="premium-card-body compact">
                        <div class="table-responsive" style="flex: 1;">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($announcements as $announcement)
                                        <tr>
                                            <td>{{ $announcement->title }}</td>
                                            <td>{{ \Auth::user()->dateFormat($announcement->start_date) }}</td>
                                            <td>{{ Str::limit($announcement->description, 30) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="empty-state">
                                                <i class="fas fa-bullhorn"></i>
                                                <h5>{{ __('No Announcements') }}</h5>
                                                <p>{{ __('No announcements available') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="premium-card" style="min-height: 450px;">
                    <div class="premium-card-header">
                        <h4>{{ __('Meeting') }}</h4>
                    </div>
                    <div class="premium-card-body compact">
                        <div class="table-responsive" style="flex: 1;">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($meetings as $meeting)
                                        <tr>
                                            <td>{{ $meeting->title }}</td>
                                            <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                            <td>{{ \Auth::user()->timeFormat($meeting->time) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="empty-state">
                                                <i class="fas fa-handshake"></i>
                                                <h5>{{ __('No Meetings') }}</h5>
                                                <p>{{ __('No meetings scheduled') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Premium Event Modal -->
        <div id="eventModal" class="premium-modal">
            <div class="premium-modal-content">
                <div class="premium-modal-header">
                    <h3>{{ __('Event Details') }}</h3>
                    <button onclick="closeModal()" class="premium-modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="premium-modal-body">
                    <div class="mb-4">
                        <h4 id="eventTitle" style="color: #1f2937; font-weight: 800; font-size: 24px; margin-bottom: 20px; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></h4>
                    </div>
                    
                    <div style="display: flex; align-items: center; margin-bottom: 20px; color: #6b7280; padding: 15px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); border-radius: 12px;">
                        <i class="fas fa-calendar-alt" style="margin-right: 15px; color: #667eea; font-size: 20px;"></i>
                        <span id="eventDate" style="font-weight: 600;"></span>
                    </div>

                    <div style="display: flex; align-items: center; margin-bottom: 25px; color: #6b7280; padding: 15px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); border-radius: 12px;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 15px; color: #667eea; font-size: 20px;"></i>
                        <span id="eventLocation" style="font-weight: 600;"></span>
                    </div>

                    <div>
                        <h5 style="color: #1f2937; font-weight: 700; margin-bottom: 15px; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Description') }}</h5>
                        <div style="padding: 20px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%); border-radius: 12px; border-left: 4px solid #667eea;">
                            <p id="eventDescription" style="color: #6b7280; line-height: 1.7; margin: 0; font-weight: 500;"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
    @endif

    <!-- Scripts -->
    <script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    <script>
        var csrfToken = "{{ csrf_token() }}";
        var clockInUrl = "{{ route('attendanceemployee.attendance') }}";
        var clockOutUrl = "{{ route('attendanceemployee.update', ':id') }}";
    </script>
    
    <script src="{{ asset('assets/js/emp-dashnew.js') }}"></script>
    <script src="{{ asset('assets/js/emp-old-js.js') }}"></script>
    
    <!-- Enhanced Chart.js and Main Application Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global variables
        let attendanceChartInstance = null;
        const events = @json($events ?? []);
        const attendanceMetrics = @json($attendanceMetrics ?? null);
        
        // Enhanced chart creation function with 4 categories - ALWAYS shows all 4
        function createAttendanceChart(attendanceData = null) {
            console.log('Creating enhanced attendance chart with 4 categories:', attendanceData);
            
            const placeholder = document.getElementById('chart-placeholder');
            const canvas = document.getElementById('attendanceChart');
            
            if (!canvas) {
                console.error('Canvas not found!');
                return;
            }
            
            // Hide placeholder
            if (placeholder) {
                placeholder.style.display = 'none';
            }
            
            // Destroy existing chart if it exists
            if (attendanceChartInstance) {
                console.log('Destroying existing chart...');
                attendanceChartInstance.destroy();
                attendanceChartInstance = null;
            }
            
            canvas.style.display = 'block';
            const ctx = canvas.getContext('2d');
            
            // Enhanced data processing - 4 separate categories (ALWAYS include all 4)
            let presentDays = 0;
            let absentDays = 0;
            let lateDays = 0;
            let leaveDays = 0;
            
            if (attendanceData) {
                presentDays = parseInt(attendanceData.present) || 0;
                absentDays = parseInt(attendanceData.absent) || 0;
                lateDays = parseInt(attendanceData.late) || 0;
                leaveDays = parseInt(attendanceData.leave) || 0;
            }
            
            console.log('Chart data breakdown:', {
                present: presentDays,
                absent: absentDays, 
                late: lateDays,
                leave: leaveDays
            });
            
            // Always show chart with all 4 categories, even if some are 0
            const totalDays = presentDays + absentDays + lateDays + leaveDays;
            
            // If no data at all, show empty state
            if (totalDays === 0) {
                if (placeholder) {
                    placeholder.style.display = 'block';
                    placeholder.className = 'empty-state';
                    placeholder.innerHTML = `
                        <i class="fas fa-chart-pie"></i>
                        <h5>{{ __('No Attendance Data') }}</h5>
                        <p>{{ __('No attendance records found for this month.') }}</p>
                    `;
                }
                canvas.style.display = 'none';
                return;
            }
            
            // Enhanced gradient colors for all 4 categories
            const gradientPresent = ctx.createLinearGradient(0, 0, 0, 400);
            gradientPresent.addColorStop(0, '#10b981');
            gradientPresent.addColorStop(1, '#059669');
            
            const gradientAbsent = ctx.createLinearGradient(0, 0, 0, 400);
            gradientAbsent.addColorStop(0, '#ef4444');
            gradientAbsent.addColorStop(1, '#dc2626');
            
            const gradientLate = ctx.createLinearGradient(0, 0, 0, 400);
            gradientLate.addColorStop(0, '#f59e0b');
            gradientLate.addColorStop(1, '#d97706');
            
            const gradientLeave = ctx.createLinearGradient(0, 0, 0, 400);
            gradientLeave.addColorStop(0, '#8b5cf6');
            gradientLeave.addColorStop(1, '#7c3aed');
            
            // ALWAYS include all 4 categories in the chart data
            const chartData = {
                labels: ['Present', 'Absent', 'Late', 'Leave'],
                datasets: [{
                    data: [presentDays, absentDays, lateDays, leaveDays],
                    backgroundColor: [gradientPresent, gradientAbsent, gradientLate, gradientLeave],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4,
                    hoverOffset: 8
                }]
            };
            
            console.log('Final chart data being sent to Chart.js:', chartData);
            
            try {
                attendanceChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    color: '#4a5568',
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    // Force all labels to show even if value is 0
                                    filter: function(item, chart) {
                                        return true; // Always show all labels
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: '#667eea',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: ${value} days (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        elements: {
                            arc: {
                                borderWidth: 3,
                                borderColor: '#ffffff'
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1200,
                            easing: 'easeOutCubic'
                        },
                        // Ensure all segments are visible
                        circumference: 360,
                        rotation: 0
                    }
                });
                
                console.log('✅ Enhanced 4-category chart created successfully!');
                console.log('Chart segments:', attendanceChartInstance.data.datasets[0].data);
                
                // Add enhanced data summary note with all 4 categories
                const container = canvas.parentElement;
                const existingNote = container.querySelector('.chart-note');
                if (existingNote) {
                    existingNote.remove();
                }
                
                const note = document.createElement('div');
                note.className = 'chart-note';
                note.style.cssText = `
                    text-align: center; 
                    font-size: 11px; 
                    color: #6b7280; 
                    margin-top: 15px; 
                    font-weight: 600;
                    padding: 10px;
                    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
                    border-radius: 8px;
                    border-left: 3px solid #667eea;
                    line-height: 1.4;
                `;
                note.innerHTML = `
                    <i class="fas fa-info-circle" style="color: #667eea; margin-right: 5px;"></i>
                    <strong>Total:</strong> ${presentDays} present, ${absentDays} absent, ${lateDays} late, ${leaveDays} leave 
                    <span style="color: #9ca3af;">(${totalDays} days)</span>
                `;
                container.appendChild(note);
                
            } catch (error) {
                
            }
        }

        // Validate and sanitize chart data
        function validateChartData(data) {
            if (!data || typeof data !== 'object') {
                console.warn('Invalid chart data provided:', data);
                return null;
            }
            
            // Ensure all values are non-negative integers
            const sanitized = {
                present: Math.max(0, parseInt(data.present) || 0),
                absent: Math.max(0, parseInt(data.absent) || 0),
                late: Math.max(0, parseInt(data.late) || 0),
                leave: Math.max(0, parseInt(data.leave) || 0)
            };
            
            // Log validation warnings
            if (sanitized.present !== (data.present || 0)) {
                console.warn('Present days value sanitized:', data.present, '->', sanitized.present);
            }
            if (sanitized.absent !== (data.absent || 0)) {
                console.warn('Absent days value sanitized:', data.absent, '->', sanitized.absent);
            }
            if (sanitized.late !== (data.late || 0)) {
                console.warn('Late days value sanitized:', data.late, '->', sanitized.late);
            }
            if (sanitized.leave !== (data.leave || 0)) {
                console.warn('Leave days value sanitized:', data.leave, '->', sanitized.leave);
            }
            
            return sanitized;
        }

        // Initialize chart with enhanced error handling for 4 categories
        function initializeChart() {
            if (typeof Chart === 'undefined') {
                console.log('Chart.js not ready yet...');
                return;
            }
            
            console.log('Raw attendance metrics:', attendanceMetrics);
            
            let attendanceData = null;
            
            try {
                if (attendanceMetrics && attendanceMetrics.pieChartData) {
                    // Validate pie chart data structure
                    const pieData = attendanceMetrics.pieChartData;
                    if (!Array.isArray(pieData.data) || pieData.data.length !== 4) {
                        throw new Error('Invalid pie chart data structure');
                    }
                    
                    attendanceData = {
                        present: pieData.data[0] || 0,
                        absent: pieData.data[1] || 0,
                        late: pieData.data[2] || 0,
                        leave: pieData.data[3] || 0
                    };
                    
                    console.log('Using backend pie chart data:', attendanceData);
                } else if (attendanceMetrics) {
                    // Fallback to old calculation method with validation
                    attendanceData = {
                        present: attendanceMetrics.presentDays || 0,
                        absent: attendanceMetrics.absentDays || 0,
                        late: attendanceMetrics.lateDays || 0,
                        leave: attendanceMetrics.leaveDays || 0
                    };
                    
                    console.log('Using fallback attendance data:', attendanceData);
                } else {
                    throw new Error('No attendance metrics available');
                }
                
                // Validate and sanitize the data
                attendanceData = validateChartData(attendanceData);
                if (!attendanceData) {
                    throw new Error('Chart data validation failed');
                }
                
            } catch (error) {
                console.error('Error processing attendance data:', error);
                console.log('Using default sample data');
                
                // Use safe default data
                attendanceData = {
                    present: 0,
                    absent: 0,
                    late: 0,
                    leave: 0
                };
            }
            
            createAttendanceChart(attendanceData);
        }

        // Enhanced modal functions
        function openModal(eventId) {
            const event = events.find(e => e.id == eventId);
            if (!event) return;

            document.getElementById('eventTitle').textContent = event.title;
            document.getElementById('eventDate').textContent = formatDate(event.start_date);
            document.getElementById('eventLocation').textContent = event.location || 'Location not specified';
            document.getElementById('eventDescription').textContent = event.description || 'No description available';
            
            const modal = document.getElementById('eventModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('eventModal');
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function formatDate(dateString) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        // Enhanced event listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready, initializing enhanced dashboard...');
            
            // Try to initialize chart immediately if Chart.js is ready
            if (typeof Chart !== 'undefined') {
                initializeChart();
            } else {
                // Wait for Chart.js to load with enhanced loading feedback
                let attempts = 0;
                const maxAttempts = 15;
                
                function tryInit() {
                    attempts++;
                    const placeholder = document.getElementById('chart-placeholder');
                    
                    if (typeof Chart !== 'undefined') {
                        console.log('Chart.js loaded, initializing enhanced chart...');
                        initializeChart();
                    } else if (attempts < maxAttempts) {
                        if (placeholder && attempts > 5) {
                            placeholder.innerHTML = `
                                <i class="fas fa-spinner fa-spin mb-3" style="font-size: 28px; color: #667eea;"></i><br>
                                <h6 style="color: #6b7280;">Loading Chart Library...</h6>
                                <p style="font-size: 11px; color: #9ca3af;">Attempt ${attempts}/${maxAttempts}</p>
                            `;
                        }
                        setTimeout(tryInit, 400);
                    } else {
                        console.error('Chart.js failed to load after maximum attempts');
                        if (placeholder) {
                            placeholder.className = 'empty-state';
                            placeholder.innerHTML = `
                                <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                                <h5>{{ __('Loading Failed') }}</h5>
                                <p>{{ __('Chart library failed to load') }}</p>
                                <button onclick="location.reload()" style="margin-top: 15px; padding: 8px 16px; background: var(--primary-gradient); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                    {{ __('Reload Page') }}
                                </button>
                            `;
                        }
                    }
                }
                
                setTimeout(tryInit, 200);
            }
        });

        // Enhanced modal event listeners
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('eventModal');
            if (e.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Enhanced button ripple effect
        document.addEventListener('click', function(e) {
            if (e.target.closest('.premium-btn')) {
                const button = e.target.closest('.premium-btn');
                const ripple = document.createElement('span');
                const rect = button.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.6);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;

                button.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }
        });

        // Performance optimization: Lazy load Chart.js if not already loaded
        function loadChartJS() {
            return new Promise((resolve, reject) => {
                if (typeof Chart !== 'undefined') {
                    resolve();
                    return;
                }
                
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        // Optimized chart update function
        function updateChartData(newData) {
            if (!attendanceChartInstance) {
                console.warn('Chart instance not available for update');
                return;
            }
            
            const validatedData = validateChartData(newData);
            if (!validatedData) {
                console.error('Invalid data provided for chart update');
                return;
            }
            
            // Update data without destroying the chart
            attendanceChartInstance.data.datasets[0].data = [
                validatedData.present,
                validatedData.absent,
                validatedData.late,
                validatedData.leave
            ];
            
            // Animate the update
            attendanceChartInstance.update('active');
            
            console.log('Chart data updated successfully');
        }

        // Memory management: Enhanced cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (attendanceChartInstance) {
                attendanceChartInstance.destroy();
                attendanceChartInstance = null;
            }
            
            // Clear any cached data
            if (window.attendanceChart) {
                window.attendanceChart = null;
            }
        });

        // Enhanced debug utility with performance monitoring
        window.attendanceChart = {
            instance: () => attendanceChartInstance,
            recreate: () => initializeChart(),
            update: (newData) => updateChartData(newData),
            destroy: () => {
                if (attendanceChartInstance) {
                    attendanceChartInstance.destroy();
                    attendanceChartInstance = null;
                }
            },
            getType: () => attendanceChartInstance?.config?.type || 'none',
            getPerformanceInfo: () => ({
                isLoaded: typeof Chart !== 'undefined',
                hasInstance: !!attendanceChartInstance,
                dataPoints: attendanceChartInstance?.data?.datasets?.[0]?.data?.length || 0,
                lastUpdate: attendanceChartInstance?.lastUpdate || null
            })
        };
    </script>
@endsection