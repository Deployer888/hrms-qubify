@extends('layouts.admin')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@push('css-page')
    <link href="{{ asset('css/dash.css') }}" rel="stylesheet">
    <style>
        .page-title{
            display: none;
        }
        .office-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            position: relative;
            background: white;
        }
        
        .office-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .office-card .card-header {
            background: linear-gradient(135deg, #3a8ef6, #6259ca);
            color: white;
            padding: 20px;
            position: relative;
            overflow: hidden;
            border-bottom: none;
        }
        
        .office-card .card-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.3rem;
            position: relative;
            z-index: 2;
            color: #fff!important;
        }
        
        .office-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .office-card-link:hover {
            text-decoration: none;
            color: inherit;
        }
        
        .office-stats {
            padding: 15px 0;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        .stat-value {
            font-weight: 600;
            color: #343a40;
        }
        
        .attendance-percentage {
            font-size: 2rem;
            font-weight: 700;
            color: #3a8ef6;
        }
        
        .attendance-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .section-header {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 15px;
        }
        
        .section-header h2 {
            position: relative;
            margin-bottom: 15px;
            padding-bottom: 15px;
        }
        
        .section-header h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, #3a8ef6, #6259ca);
        }
        
        .summary-card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        canvas {
          transform: translateZ(0);
          backface-visibility: hidden;
          perspective: 1000px;
        }
        
        /* Prevent charts from changing size on hover */
        .dashboard-card, .office-card {
          contain: layout style;
        }
        
        /* Chart container sizing fixes */
        .chart-container {
          position: relative;
          height: 300px;
          width: 100%;
          min-height: 300px;
        }
        
        /* Office chart containers need fixed heights */
        .office-chart-container {
          position: relative;
          height: 180px;
          width: 100%;
          min-height: 180px;
        }
        
        .office-chart-summary {
          position: relative;
          height: 120px;
          width: 100%;
          min-height: 120px;
        }

        .card-header{
            color: #fff!important;
        }
    </style>
@endpush
@section('content')
<div class="attendance-dashboard">
    <!-- Header Section with Title -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title">Performance Insights Dashboard</h1>
                <div class="refresh-indicator">
                    <span class="refresh-label">Auto-refreshes every 10 minutes</span>
                    <span class="refresh-countdown" id="refresh-countdown">10:00</span>
                </div>
            </div>
            <div class="col-md-2">
                <label for="office-filter">Office</label>
                <select class="form-select custom-select" id="office-filter" name="office">
                    <option value="all" selected>All Offices</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }} Office</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="department-filter">Department</label>
                <select class="form-select custom-select" id="department-filter" name="department">
                    <option value="all">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="branch-filter">Branch</label>
                <select class="form-select custom-select" id="branch-filter" name="branch">
                    <option value="all">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="filter-container d-none">
        <div class="row g-3">
            <div class="col-md-3 d-none">
                <div class="filter-card">
                    <label for="date-range">Date Range</label>
                    <div class="input-group">
                        <input type="text" class="form-control custom-date" id="date-range" name="date_range" readonly>
                        <button class="btn btn-primary date-btn">
                            <i class="fas fa-calendar"></i>
                        </button>
                    </div>
                    <input type="hidden" id="start-date" name="start_date" value="{{ $startDate ?? '' }}">
                    <input type="hidden" id="end-date" name="end_date" value="{{ $endDate ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards Section -->
    <div class="summary-section">
        <div class="row">
            <div class="col-md-3 col-6 mb-md-0 mb-3">
                <div class="summary-card present-card">
                    <div class="summary-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="summary-content">
                        <h3 class="summary-title text-light font-weight-800">Present</h3>
                        <p class="summary-value" id="present-count">{{ $officeData['present'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-md-0 mb-3">
                <div class="summary-card absent-card">
                    <div class="summary-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="summary-content">
                        <h3 class="summary-title text-light font-weight-800">Absent</h3>
                        <p class="summary-value" id="absent-count">{{ $officeData['absent'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-card late-card">
                    <div class="summary-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="summary-content">
                        <h3 class="summary-title text-light font-weight-800">Late</h3>
                        <p class="summary-value" id="late-count">{{ $officeData['late'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-card leave-card">
                    <div class="summary-icon">
                        <i class="fas fa-calendar-minus"></i>
                    </div>
                    <div class="summary-content">
                        <h3 class="summary-title text-light font-weight-800">On Leave</h3>
                        <p class="summary-value" id="leave-count">{{ $officeData['on_leave'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="main-dashboard-content">
        <div class="row">
            <!-- Overall Attendance Gauge -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Overall Attendance</h3>
                    </div>
                    <div class="card-body">
                        
                        <div class="gauge-container">
                            <div class="gauge-wrapper">
                                <img src="{{ asset('landing/images/gauge.png') }}" class="gauge-image" width="100%">
                                <div class="needle-container">
                                    <div class="needle" id="attendance-needle"></div>
                                    <div class="needle-center"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="gauge-info">
                            <div class="gauge-percentage" id="overall-percentage">{{ $officeData['attendance_rate'] }}%</div>
                            <div class="gauge-details">
                                <span id="present-employees">{{ $officeData['present'] }}</span> / 
                                <span id="total-expected">{{ $officeData['total'] }}</span> Present
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Weekly Trend Chart -->
            <div class="col-md-8">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Weekly Attendance Trend</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="weekly-trend-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Attendance Breakdown Chart -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Attendance Breakdown</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="attendance-breakdown-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Employee Status Chart -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Employee Status Comparison</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="employee-status-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Office-wise Analytics Section -->
    <div class="office-analytics-section mt-4">
        <div class="section-header">
            <h2>Office-wise Analytics</h2>
        </div>
        
        <div class="office-cards" id="office-cards">
            <!-- Headquarters -->
            <a href="{{ route('office.one.index', 1) }}" class="office-card-link">
                <div class="office-card" data-office-id="1">
                    <div class="card-header">
                        <h3>Mumbai Office</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="office-chart-summary" style="position: relative; height: 120px;">
                                    <canvas id="office-bar-1"></canvas>
                                </div>
                                <div class="attendance-info text-center mt-2">
                                    <div class="attendance-percentage">90%</div>
                                    <div class="attendance-label">Overall Attendance Rate</div>
                                    <div class="gauge-details">
                                        <span>220</span> / <span>245</span> Present
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="office-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Employees:</span>
                                        <span class="stat-value">245</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Present:</span>
                                        <span class="stat-value">220</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Absent:</span>
                                        <span class="stat-value">10</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Late:</span>
                                        <span class="stat-value">15</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">On Leave:</span>
                                        <span class="stat-value">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="office-chart-container" style="position: relative; height: 180px;">
                            <canvas id="office-trend-1"></canvas>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- San Francisco Office -->
            <a href="{{ route('office.one.index', 2) }}" class="office-card-link">
                <div class="office-card" data-office-id="2">
                    <div class="card-header">
                        <h3>Delhi Office</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="office-chart-summary">
                                    <canvas id="office-bar-2"></canvas>
                                </div>
                                <div class="attendance-info text-center mt-2">
                                    <div class="attendance-percentage">88%</div>
                                    <div class="attendance-label">Overall Attendance Rate</div>
                                    <div class="gauge-details">
                                        <span>105</span> / <span>120</span> Present
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="office-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Employees:</span>
                                        <span class="stat-value">120</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Present:</span>
                                        <span class="stat-value">105</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Absent:</span>
                                        <span class="stat-value">5</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Late:</span>
                                        <span class="stat-value">10</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">On Leave:</span>
                                        <span class="stat-value">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="office-chart-container mt-3">
                            <canvas id="office-trend-2"></canvas>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- London Office -->
            <a href="{{ route('office.one.index', 3) }}" class="office-card-link">
                <div class="office-card" data-office-id="3">
                    <div class="card-header">
                        <h3>Bangalore Office</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="office-chart-summary">
                                    <canvas id="office-bar-3"></canvas>
                                </div>
                                <div class="attendance-info text-center mt-2">
                                    <div class="attendance-percentage">82%</div>
                                    <div class="attendance-label">Overall Attendance Rate</div>
                                    <div class="gauge-details">
                                        <span>70</span> / <span>85</span> Present
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="office-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Employees:</span>
                                        <span class="stat-value">85</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Present:</span>
                                        <span class="stat-value">70</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Absent:</span>
                                        <span class="stat-value">10</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Late:</span>
                                        <span class="stat-value">5</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">On Leave:</span>
                                        <span class="stat-value">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="office-chart-container mt-3">
                            <canvas id="office-trend-3"></canvas>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- Singapore Office -->
            <a href="{{ route('office.one.index', 4) }}" class="office-card-link">
                <div class="office-card" data-office-id="4">
                    <div class="card-header">
                        <h3>Chennai Office</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="office-chart-summary">
                                    <canvas id="office-bar-4"></canvas>
                                </div>
                                <div class="attendance-info text-center mt-2">
                                    <div class="attendance-percentage">77%</div>
                                    <div class="attendance-label">Overall Attendance Rate</div>
                                    <div class="gauge-details">
                                        <span>50</span> / <span>65</span> Present
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="office-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Employees:</span>
                                        <span class="stat-value">65</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Present:</span>
                                        <span class="stat-value">50</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Absent:</span>
                                        <span class="stat-value">5</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Late:</span>
                                        <span class="stat-value">10</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">On Leave:</span>
                                        <span class="stat-value">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="office-chart-container mt-3">
                            <canvas id="office-trend-4"></canvas>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- Sydney Office -->
            <a href="{{ route('office.one.index', 5) }}" class="office-card-link">
                <div class="office-card" data-office-id="5">
                    <div class="card-header">
                        <h3>Hyderabad Office</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="office-chart-summary">
                                    <canvas id="office-bar-5"></canvas>
                                </div>
                                <div class="attendance-info text-center mt-2">
                                    <div class="attendance-percentage">78%</div>
                                    <div class="attendance-label">Overall Attendance Rate</div>
                                    <div class="gauge-details">
                                        <span>35</span> / <span>45</span> Present
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="office-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Employees:</span>
                                        <span class="stat-value">45</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Present:</span>
                                        <span class="stat-value">35</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Absent:</span>
                                        <span class="stat-value">5</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Late:</span>
                                        <span class="stat-value">5</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">On Leave:</span>
                                        <span class="stat-value">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="office-chart-container mt-3">
                            <canvas id="office-trend-5"></canvas>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- Berlin Office -->
            <a href="{{ route('office.one.index', 6) }}" class="office-card-link">
                <div class="office-card" data-office-id="6">
                    <div class="card-header">
                        <h3>Pune Office</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="office-chart-summary">
                                    <canvas id="office-bar-6"></canvas>
                                </div>
                                <div class="attendance-info text-center mt-2">
                                    <div class="attendance-percentage">78%</div>
                                    <div class="attendance-label">Overall Attendance Rate</div>
                                    <div class="gauge-details">
                                        <span>70</span> / <span>90</span> Present
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="office-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Employees:</span>
                                        <span class="stat-value">90</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Present:</span>
                                        <span class="stat-value">70</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Absent:</span>
                                        <span class="stat-value">10</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Late:</span>
                                        <span class="stat-value">10</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">On Leave:</span>
                                        <span class="stat-value">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="office-chart-container mt-3">
                            <canvas id="office-trend-6"></canvas>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Data Container for JavaScript with dynamic data from controller -->
<div id="dashboard-data" class="d-none" 
    data-overall-percentage="{{ $officeData['attendance_rate'] }}"
    data-present-count="{{ $officeData['present'] }}"
    data-absent-count="{{ $officeData['absent'] }}"
    data-late-count="{{ $officeData['late'] }}"
    data-leave-count="{{ $officeData['on_leave'] }}"
    data-total-employees="{{ $officeData['total'] }}"
    data-working-days="22"
    data-weekly-trend='[{"day":"Mon","present":550,"absent":50,"late":50},{"day":"Tue","present":530,"absent":70,"late":50},{"day":"Wed","present":500,"absent":100,"late":50},{"day":"Thu","present":520,"absent":80,"late":50},{"day":"Fri","present":480,"absent":120,"late":50}]'
    data-office-data='[{"id":1,"stats":{"employeeCount":245,"presentCount":220,"absentCount":10,"lateCount":15,"leaveCount":0,"attendancePercentage":90},"weeklyData":[{"day":"Mon","present":221,"absent":12,"late":12},{"day":"Tue","present":216,"absent":17,"late":12},{"day":"Wed","present":208,"absent":25,"late":12},{"day":"Thu","present":213,"absent":20,"late":12},{"day":"Fri","present":196,"absent":37,"late":12}]},{"id":2,"stats":{"employeeCount":120,"presentCount":105,"absentCount":5,"lateCount":10,"leaveCount":0,"attendancePercentage":88},"weeklyData":[{"day":"Mon","present":108,"absent":6,"late":6},{"day":"Tue","present":106,"absent":8,"late":6},{"day":"Wed","present":102,"absent":12,"late":6},{"day":"Thu","present":104,"absent":10,"late":6},{"day":"Fri","present":96,"absent":18,"late":6}]},{"id":3,"stats":{"employeeCount":85,"presentCount":70,"absentCount":10,"lateCount":5,"leaveCount":0,"attendancePercentage":82},"weeklyData":[{"day":"Mon","present":77,"absent":4,"late":4},{"day":"Tue","present":75,"absent":6,"late":4},{"day":"Wed","present":72,"absent":9,"late":4},{"day":"Thu","present":74,"absent":7,"late":4},{"day":"Fri","present":68,"absent":13,"late":4}]},{"id":4,"stats":{"employeeCount":65,"presentCount":50,"absentCount":5,"lateCount":10,"leaveCount":0,"attendancePercentage":77},"weeklyData":[{"day":"Mon","present":59,"absent":3,"late":3},{"day":"Tue","present":57,"absent":5,"late":3},{"day":"Wed","present":55,"absent":7,"late":3},{"day":"Thu","present":57,"absent":5,"late":3},{"day":"Fri","present":52,"absent":10,"late":3}]},{"id":5,"stats":{"employeeCount":45,"presentCount":35,"absentCount":5,"lateCount":5,"leaveCount":0,"attendancePercentage":78},"weeklyData":[{"day":"Mon","present":41,"absent":2,"late":2},{"day":"Tue","present":40,"absent":3,"late":2},{"day":"Wed","present":38,"absent":5,"late":2},{"day":"Thu","present":39,"absent":4,"late":2},{"day":"Fri","present":36,"absent":7,"late":2}]},{"id":6,"stats":{"employeeCount":90,"presentCount":70,"absentCount":10,"lateCount":10,"leaveCount":0,"attendancePercentage":78},"weeklyData":[{"day":"Mon","present":81,"absent":5,"late":5},{"day":"Tue","present":79,"absent":6,"late":5},{"day":"Wed","present":77,"absent":9,"late":5},{"day":"Thu","present":78,"absent":7,"late":5},{"day":"Fri","present":72,"absent":14,"late":5}]}]'>
</div>
@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-gauge@0.3.0/dist/chartjs-gauge.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('js/dash.js') }}"></script>
<script>
    $(document).ready(function() {
        // Add enhanced functionality to the office filter
        const officeFilter = $('#office-filter');
        
        // Handle change event
        officeFilter.on('change', function() {
            const selectedOffice = $(this).val();
            
            // Visual feedback for selection
            $(this).addClass('selected');
            
            // Filter content based on selection
            if (selectedOffice === 'all') {
                // Show all offices
                $('.office-card').show();
                // Update summary counts to show totals across all offices
                updateSummaryTotals();
            } else {
                // Show only the selected office
                $('.office-card').hide();
                $(`.office-card[data-office-id="${selectedOffice}"]`).show();
                // Update summary counts to show only the selected office
                updateOfficeSummary(selectedOffice);
            }
            
            // Smooth scroll to the office section
            if (selectedOffice !== 'all') {
                $('html, body').animate({
                    scrollTop: $(`.office-card[data-office-id="${selectedOffice}"]`).offset().top - 100
                }, 500);
            }
            
            // Add a visual highlight to the selected office card
            $('.office-card').removeClass('highlighted');
            if (selectedOffice !== 'all') {
                $(`.office-card[data-office-id="${selectedOffice}"]`).addClass('highlighted');
            }
        });
        
        // Add icons to dropdown options
        officeFilter.find('option').each(function() {
            const icon = $(this).data('icon');
            if (icon) {
                $(this).addClass('option-with-icon');
            }
        });
        
        // Update summary counts when filtering
        function updateSummaryTotals() {
            // This would normally fetch data from the server, but for demo use totals
            $('#present-count').text('450');
            $('#absent-count').text('75');
            $('#late-count').text('45');
            $('#leave-count').text('80');
            
            // Update gauge details
            $('#overall-percentage').text('85%');
            $('#present-employees').text('450');
            $('#total-expected').text('530');
            
            // Update gauge needle
            updateGaugeNeedle(85);
            
            // Update attendance breakdown chart
            updateAttendanceBreakdownChart({
                present: 450,
                absent: 75,
                late: 45,
                leave: 80
            });
            
            // Update employee status chart
            updateEmployeeStatusChart({
                onTime: 450 - 45,  // Present minus late
                late: 45,
                absent: 75,
                leave: 80
            });
        }
        
        // Update summary to show only the selected office
        function updateOfficeSummary(officeId) {
            // Get the specific office data from our data structure
            const officeDataString = $('#dashboard-data').attr('data-office-data');
            const officeData = JSON.parse(officeDataString);
            const office = officeData.find(o => o.id.toString() === officeId);
            
            if (office) {
                $('#present-count').text(office.stats.presentCount);
                $('#absent-count').text(office.stats.absentCount);
                $('#late-count').text(office.stats.lateCount);
                $('#leave-count').text(office.stats.leaveCount || 0);
                
                // Update gauge details
                $('#overall-percentage').text(office.stats.attendancePercentage + '%');
                $('#present-employees').text(office.stats.presentCount);
                $('#total-expected').text(office.stats.employeeCount);
                
                // Update gauge needle
                updateGaugeNeedle(office.stats.attendancePercentage);
                
                // Update attendance breakdown chart
                updateAttendanceBreakdownChart({
                    present: office.stats.presentCount,
                    absent: office.stats.absentCount,
                    late: office.stats.lateCount,
                    leave: office.stats.leaveCount || 0
                });
                
                // Update employee status chart
                updateEmployeeStatusChart({
                    onTime: office.stats.presentCount - office.stats.lateCount,  // Present minus late
                    late: office.stats.lateCount,
                    absent: office.stats.absentCount,
                    leave: office.stats.leaveCount || 0
                });
            }
        }
        
        // Add some visual enhancements and animations
        $('.custom-select').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            $(this).parent().removeClass('focused');
        });
        
        // Ensure highlighting works when cards are filtered
        $('.office-card').hover(
            function() { $(this).addClass('hover'); },
            function() { $(this).removeClass('hover'); }
        );
        
        // Function to create office bar charts
        function createOfficeBarChart(officeId, data) {
            const ctx = document.getElementById('office-bar-' + officeId).getContext('2d');
            
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Present', 'Absent', 'Late'],
                    datasets: [{
                        label: 'Employee Count',
                        data: [
                            data.stats.presentCount,
                            data.stats.absentCount,
                            data.stats.lateCount
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(255, 206, 86, 0.6)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
        
        // Initialize the needle position for the gauge
        function updateGaugeNeedle(percentage) {
            const rotation = -90 + (percentage * 1.8);
            $('#attendance-needle').css('transform', `rotate(${rotation}deg)`);
        }
        
        // Initialize charts for each office
        const officeDataString = $('#dashboard-data').attr('data-office-data');
        if (officeDataString) {
            try {
                const officeData = JSON.parse(officeDataString);
                
                // Create charts for each office
                officeData.forEach(office => {
                    // Check if canvas elements exist
                    if (document.getElementById('office-bar-' + office.id)) {
                        createOfficeBarChart(office.id, office);
                    }
                    
                    if (document.getElementById('office-trend-' + office.id)) {
                        createOfficeTrendChart(office.id, office.weeklyData);
                    }
                });
            } catch (error) {
                console.error('Error parsing office data:', error);
            }
        }
    
        // Global chart objects to properly manage chart instances
        const dashboardCharts = {
            weeklyTrend: null,
            attendanceBreakdown: null,
            employeeStatus: null
        };
    
        // Weekly Attendance Trend Chart Code
        // Get weekly trend data from the data attribute
        const weeklyTrendString = $('#dashboard-data').attr('data-weekly-trend');
        let weeklyTrendData;
        
        try {
            // Try to parse the data from JSON
            weeklyTrendData = JSON.parse(weeklyTrendString);
        } catch (error) {
            // If there's an error, use default random data
            weeklyTrendData = [
                { day: 'Mon', present: 550, absent: 50, late: 50, leave: 65 },
                { day: 'Tue', present: 530, absent: 70, late: 50, leave: 65 },
                { day: 'Wed', present: 500, absent: 100, late: 50, leave: 60 },
                { day: 'Thu', present: 520, absent: 80, late: 50, leave: 65 },
                { day: 'Fri', present: 450, absent: 75, late: 45, leave: 80 }
            ];
        }
        
        // Labels for the chart (days of the week)
        const labels = weeklyTrendData.map(item => item.day);
        
        // Data for each status
        const presentData = weeklyTrendData.map(item => item.present);
        const absentData = weeklyTrendData.map(item => item.absent);
        const lateData = weeklyTrendData.map(item => item.late);
        const leaveData = weeklyTrendData.map(item => item.leave || 0);
        
        // Get the canvas element
        const weeklyTrendCanvas = document.getElementById('weekly-trend-chart');
        
        // Create the chart
        if (weeklyTrendCanvas) {
            dashboardCharts.weeklyTrend = new Chart(weeklyTrendCanvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Present',
                            data: presentData,
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 4,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)'
                        },
                        {
                            label: 'Absent',
                            data: absentData,
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 4,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)'
                        },
                        {
                            label: 'Late',
                            data: lateData,
                            backgroundColor: 'rgba(255, 206, 86, 0.1)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 4,
                            pointBackgroundColor: 'rgba(255, 206, 86, 1)'
                        },
                        {
                            label: 'On Leave',
                            data: leaveData,
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 4,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const label = data.datasets[tooltipItem.datasetIndex].label || '';
                                const value = tooltipItem.yLabel;
                                return `${label}: ${value} employees`;
                            }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: false
                            },
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            display: true,
                            ticks: {
                                beginAtZero: true,
                                stepSize: 100
                            },
                            gridLines: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    }
                }
            });
        }
        
        // Initialize the gauge needle
        const overallPercentage = parseInt($('#dashboard-data').attr('data-overall-percentage'));
        updateGaugeNeedle(overallPercentage);
        
        // Initialize Attendance Breakdown Chart
        const attendanceBreakdownCanvas = document.getElementById('attendance-breakdown-chart');
        
        if (attendanceBreakdownCanvas) {
            dashboardCharts.attendanceBreakdown = new Chart(attendanceBreakdownCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Late', 'On Leave'],
                    datasets: [{
                        data: [450, 75, 45, 80],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const dataset = data.datasets[tooltipItem.datasetIndex];
                                const total = dataset.data.reduce((sum, value) => sum + value, 0);
                                const value = dataset.data[tooltipItem.index];
                                const percentage = Math.round((value / total) * 100);
                                return `${data.labels[tooltipItem.index]}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            });
        }
        
        // Update Attendance Breakdown Chart
        function updateAttendanceBreakdownChart(data) {
            if (!dashboardCharts.attendanceBreakdown) {
                return;
            }
            
            dashboardCharts.attendanceBreakdown.data.datasets[0].data = [
                data.present,
                data.absent,
                data.late,
                data.leave
            ];
            
            dashboardCharts.attendanceBreakdown.update();
        }
        
        // Initialize Employee Status Chart
        const employeeStatusCanvas = document.getElementById('employee-status-chart');
        
        if (employeeStatusCanvas) {
            dashboardCharts.employeeStatus = new Chart(employeeStatusCanvas, {
                type: 'horizontalBar',
                data: {
                    labels: ['On Time', 'Late', 'Absent', 'On Leave'],
                    datasets: [{
                        label: 'Number of Employees',
                        data: [405, 45, 75, 80],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true
                            },
                            gridLines: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const value = tooltipItem.xLabel;
                                return `${data.labels[tooltipItem.index]}: ${value} employees`;
                            }
                        }
                    }
                }
            });
        }
        
        // Update Employee Status Chart
        function updateEmployeeStatusChart(data) {
            if (!dashboardCharts.employeeStatus) {
                return;
            }
            
            dashboardCharts.employeeStatus.data.datasets[0].data = [
                data.onTime,
                data.late,
                data.absent,
                data.leave
            ];
            
            dashboardCharts.employeeStatus.update();
        }
        
        // Auto refresh countdown
        function startCountdown() {
            let duration = 10 * 60; // 10 minutes in seconds
            const countdownElement = $('#refresh-countdown');
            
            function updateCountdown() {
                const minutes = Math.floor(duration / 60);
                const seconds = duration % 60;
                
                // Format with leading zeros
                const formattedMinutes = minutes.toString().padStart(2, '0');
                const formattedSeconds = seconds.toString().padStart(2, '0');
                
                countdownElement.text(`${formattedMinutes}:${formattedSeconds}`);
                
                if (duration <= 0) {
                    // Reload the dashboard data
                    refreshDashboardData();
                    duration = 10 * 60; // Reset countdown
                } else {
                    duration--;
                    setTimeout(updateCountdown, 1000);
                }
            }
            
            updateCountdown();
        }
        
        // Simulated data refresh function
        function refreshDashboardData() {
            console.log('Refreshing dashboard data...');
            // This would typically make an AJAX call to fetch new data
            // For demo purposes, we'll just show a visual indicator
            
            $('.dashboard-header').addClass('refreshing');
            setTimeout(function() {
                $('.dashboard-header').removeClass('refreshing');
            }, 1000);
        }
        
        // Start the countdown
        startCountdown();
        
        // Add a small animation to show the dashboard is loaded
        $('.dashboard-card, .summary-card, .office-card').each(function(index) {
            $(this).css({
                'opacity': 0,
                'transform': 'translateY(20px)'
            });
            
            setTimeout(() => {
                $(this).css({
                    'opacity': 1,
                    'transform': 'translateY(0)',
                    'transition': 'all 0.4s ease'
                });
            }, 100 * index);
        });
    });
    
    /**
     * Chart Position and Sizing Fix for Attendance Dashboard
     * This code addresses the fluctuating width/position and office graph issues
     */
    
    // Add these CSS fixes to your stylesheet or inside a <style> tag
    const chartCssFixStyles = `
    /* Chart container sizing fixes */
    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
      min-height: 300px;
    }
    
    /* Office chart containers need fixed heights */
    .office-chart-container {
      position: relative;
      height: 180px;
      width: 100%;
      min-height: 180px;
    }
    
    .office-chart-summary {
      position: relative;
      height: 120px;
      width: 100%;
      min-height: 120px;
    }
    
    /* Fix chart rendering glitches by preventing sub-pixel rendering */
    canvas {
      transform: translateZ(0);
      backface-visibility: hidden;
      perspective: 1000px;
    }
    
    /* Prevent charts from changing size on hover */
    .dashboard-card, .office-card {
      contain: layout style;
    }
    `;
    
    
    
    
    
    // Create and append the style element
    function applyChartFixStyles() {
      const styleElement = document.createElement('style');
      styleElement.textContent = chartCssFixStyles;
      document.head.appendChild(styleElement);
    }
    
    // Fix for office charts
    function fixOfficeCharts() {
      // Store reference to all office charts to prevent recreation
      const officeChartRefs = {};
      
      // Add to the existing chart initialization for offices
      function createOfficeBarChart(officeId, data) {
        const chartCanvas = document.getElementById('office-bar-' + officeId);
        if (!chartCanvas || !chartCanvas.getContext) return null;
        
        // Destroy previous chart if it exists
        if (officeChartRefs['bar-' + officeId]) {
          officeChartRefs['bar-' + officeId].destroy();
        }
        
        // Clear canvas context
        const ctx = chartCanvas.getContext('2d');
        ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
        
        // Prepare chart data with validation
        const presentCount = parseInt(data.stats.presentCount) || 0;
        const absentCount = parseInt(data.stats.absentCount) || 0;
        const lateCount = parseInt(data.stats.lateCount) || 0;
        
        // Create chart with fixed responsive options
        officeChartRefs['bar-' + officeId] = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
              label: 'Employee Count',
              data: [presentCount, absentCount, lateCount],
              backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(255, 206, 86, 0.6)'
              ],
              borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            responsiveAnimationDuration: 0, // Prevent animation on resize
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  precision: 0
                }
              }]
            },
            legend: {
              display: false
            },
            tooltips: {
              enabled: true,
              mode: 'index',
              intersect: false
            },
            hover: {
              animationDuration: 0 // Prevent animation on hover
            },
            animation: {
              duration: 500 // Shorter animation duration
            }
          }
        });
        
        return officeChartRefs['bar-' + officeId];
    }
    
    function createOfficeTrendChart(officeId, weeklyData) {
        const chartCanvas = document.getElementById('office-trend-' + officeId);
        if (!chartCanvas || !chartCanvas.getContext) return null;
        
        // Destroy previous chart if it exists
        if (officeChartRefs['trend-' + officeId]) {
          officeChartRefs['trend-' + officeId].destroy();
        }
        
        // Clear canvas context
        const ctx = chartCanvas.getContext('2d');
        ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
        
        // Make sure we have valid data
        if (!weeklyData || !Array.isArray(weeklyData) || weeklyData.length === 0) {
          ctx.font = '12px Arial';
          ctx.textAlign = 'center';
          ctx.fillStyle = '#718096';
          ctx.fillText('No trend data available', chartCanvas.width / 2, chartCanvas.height / 2);
          return null;
        }
        
        // Prepare chart data with validation
        const days = weeklyData.map(item => item.day || '');
        const presentData = weeklyData.map(item => parseInt(item.present) || 0);
        const absentData = weeklyData.map(item => parseInt(item.absent) || 0);
        const lateData = weeklyData.map(item => parseInt(item.late) || 0);
        
        // Create chart with fixed responsive options
        officeChartRefs['trend-' + officeId] = new Chart(ctx, {
          type: 'line',
          data: {
            labels: days,
            datasets: [
              {
                label: 'Present',
                data: presentData,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderWidth: 2,
                pointRadius: 3,
                tension: 0.3
              },
              {
                label: 'Absent',
                data: absentData,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderWidth: 2,
                pointRadius: 3,
                tension: 0.3
              },
              {
                label: 'Late',
                data: lateData,
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 0.1)',
                borderWidth: 2,
                pointRadius: 3,
                tension: 0.3
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            responsiveAnimationDuration: 0, // Prevent animation on resize
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  precision: 0,
                  maxTicksLimit: 5 // Limit Y-axis ticks
                }
              }],
              xAxes: [{
                ticks: {
                  maxRotation: 0, // Prevent label rotation
                  autoSkipPadding: 10
                }
              }]
            },
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                boxWidth: 12,
                padding: 10,
                fontSize: 10
              }
            },
            tooltips: {
              enabled: true,
              mode: 'index',
              intersect: false
            },
            hover: {
              animationDuration: 0 // Prevent animation on hover
            },
            animation: {
              duration: 500 // Shorter animation duration
            }
          }
        });
        
        return officeChartRefs['trend-' + officeId];
    }
    
    // Initialize office charts on page load
    function initAllOfficeCharts() {
        try {
          const officeDataString = $('#dashboard-data').attr('data-office-data');
          const officeData = JSON.parse(officeDataString);
          
          if (Array.isArray(officeData)) {
            officeData.forEach(office => {
              if (office && office.id) {
                if (document.getElementById('office-bar-' + office.id)) {
                  createOfficeBarChart(office.id, office);
                }
                
                if (document.getElementById('office-trend-' + office.id)) {
                  createOfficeTrendChart(office.id, office.weeklyData);
                }
              }
            });
          }
        } catch (e) {
          console.error('Error initializing office charts:', e);
        }
    }
    
    
    // Replace existing functions with our fixed versions
    window.createOfficeBarChart = createOfficeBarChart;
    window.createOfficeTrendChart = createOfficeTrendChart;
    
    // Wait for DOM to be ready, then initialize charts
    $(document).ready(function() {
        initAllOfficeCharts();
    });
    
    // Make overall charts more stable
    function stabilizeMainCharts() {
      // Global chart objects to properly manage chart instances
      window.dashboardCharts = window.dashboardCharts || {
        weeklyTrend: null,
        attendanceBreakdown: null,
        employeeStatus: null
      };
      
      // Apply fixed options to all charts to prevent resize glitches
      const stableChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 0, // Prevent animation on resize
        hover: {
          animationDuration: 0 // Prevent animation on hover
        },
        onResize: function(chart, size) {
          // Force redraw on resize
          setTimeout(function() {
            chart.update();
          }, 0);
        }
      };
      
      // Set chart defaults
      Chart.defaults.global.hover.animationDuration = 0;
      Chart.defaults.global.responsiveAnimationDuration = 0;
      
      // Apply stable options to existing charts
      function stabilizeExistingCharts() {
        const charts = [
          dashboardCharts.weeklyTrend,
          dashboardCharts.attendanceBreakdown,
          dashboardCharts.employeeStatus
        ];
        
        charts.forEach(chart => {
          if (chart) {
            // Merge in stable options
            chart.options = Object.assign({}, chart.options, stableChartOptions);
            chart.update();
          }
        });
      }
      
      // Apply fixes once document is ready
      $(document).ready(function() {
        stabilizeExistingCharts();
      });
    }
    
    // Ensure charts don't redraw unnecessarily when hovering
    function preventRedrawOnHover() {
      // Override Chart.js hover handler to prevent excessive redraws
      const originalHandleHover = Chart.Controller.prototype.handleHover;
      if (originalHandleHover) {
        Chart.Controller.prototype.handleHover = function(e) {
          // Skip if already hovering at the same position
          if (this._lastHoverPosition && 
              this._lastHoverPosition.x === e.x && 
              this._lastHoverPosition.y === e.y) {
            return;
          }
          
          // Store last hover position
          this._lastHoverPosition = { x: e.x, y: e.y };
          
          // Call original handler
          return originalHandleHover.call(this, e);
        };
      }
    }
    
    // Initialize all fixes
    function initChartFixes() {
      // Add CSS fixes
      applyChartFixStyles();
      
      // Fix office charts
      fixOfficeCharts();
      
      // Stabilize main charts
      stabilizeMainCharts();
      
      // Prevent redraw on hover
      preventRedrawOnHover();
    }
    
    // Apply fixes
    $(document).ready(function() {
      initChartFixes();
    });
    
    // Add this function to your existing JavaScript
    function fixOfficeBarChartLabels() {
      // Function to create office bar charts with proper labels
      function createOfficeBarChartWithLabels(officeId, data) {
        const ctx = document.getElementById('office-bar-' + officeId).getContext('2d');
        
        // Destroy existing chart if it exists in our global reference
        if (window.officeBarsCharts && window.officeBarsCharts[officeId]) {
          window.officeBarsCharts[officeId].destroy();
        }
        
        // Create chart with proper label configuration
        const chart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
              label: 'Employee Count', // This is the key label we need to display
              data: [
                data.stats.presentCount,
                data.stats.absentCount,
                data.stats.lateCount
              ],
              backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(255, 206, 86, 0.6)'
              ],
              borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true
                }
              }]
            },
            // Important change: Make sure the legend is displayed
            legend: {
              display: true,
              position: 'top',
              labels: {
                boxWidth: 12,
                padding: 5,
                fontSize: 10
              }
            },
            // Fix rendering issues
            hover: {
              animationDuration: 0
            },
            responsiveAnimationDuration: 0,
            animation: {
              duration: 500
            }
          }
        });
        
        // Store reference to chart
        if (!window.officeBarsCharts) window.officeBarsCharts = {};
        window.officeBarsCharts[officeId] = chart;
        
        return chart;
      }
      
      // Replace the existing createOfficeBarChart function
      window.createOfficeBarChart = createOfficeBarChartWithLabels;
      
      // Reinitialize all office bar charts
      try {
        const officeDataString = $('#dashboard-data').attr('data-office-data');
        if (officeDataString) {
          const officeData = JSON.parse(officeDataString);
          
          if (Array.isArray(officeData)) {
            officeData.forEach(office => {
              if (office && office.id && document.getElementById('office-bar-' + office.id)) {
                createOfficeBarChartWithLabels(office.id, office);
              }
            });
          }
        }
      } catch (error) {
        console.error('Error initializing office bar charts:', error);
      }
    }
    
    // Execute the fix when document is ready
    $(document).ready(function() {
      fixOfficeBarChartLabels();
      
      // Also fix when office filter changes
      $('#office-filter').on('change', function() {
        // Wait for DOM updates
        setTimeout(fixOfficeBarChartLabels, 100);
      });
    });
    
    /**
     * Mobile-friendly adjustments for the dashboard charts
     * Add this to your existing JavaScript
     */
    
    // Function to ensure charts are displayed properly on mobile devices
    function makeMobileFriendly() {
      // Get window width
      const windowWidth = window.innerWidth;
      
      // Check if we're on a mobile device
      const isMobile = windowWidth < 768;
      
      // Apply mobile-specific chart options
      if (isMobile) {
        // Set chart defaults for mobile
        Chart.defaults.global.defaultFontSize = 10;
        Chart.defaults.global.legend.labels.boxWidth = 10;
        Chart.defaults.global.legend.labels.padding = 5;
        
        // Make sure the office bar charts display their labels
        $('.office-chart-summary').each(function() {
          const canvas = $(this).find('canvas');
          if (canvas.length) {
            const chartId = canvas.attr('id');
            const officeId = chartId.replace('office-bar-', '');
            
            // Ensure bar chart has correct options for mobile
            if (window.officeBarsCharts && window.officeBarsCharts[officeId]) {
              const chart = window.officeBarsCharts[officeId];
              
              // Make sure label is visible
              if (chart.options && chart.options.legend) {
                chart.options.legend.display = true;
                chart.options.legend.position = 'top';
              }
              
              // Update chart
              chart.update();
            }
          }
        });
        
        // Adjust main dashboard charts for mobile
        if (window.dashboardCharts) {
          // Adjust weekly trend chart
          if (window.dashboardCharts.weeklyTrend) {
            const weeklyChart = window.dashboardCharts.weeklyTrend;
            
            // Simplify legend display on mobile
            weeklyChart.options.legend.position = 'top';
            weeklyChart.options.legend.labels.boxWidth = 8;
            weeklyChart.options.legend.labels.padding = 6;
            
            // Limit Y-axis ticks for cleaner display
            if (weeklyChart.options.scales && weeklyChart.options.scales.yAxes) {
              weeklyChart.options.scales.yAxes[0].ticks.maxTicksLimit = 5;
            }
            
            weeklyChart.update();
          }
          
          // Adjust attendance breakdown chart
          if (window.dashboardCharts.attendanceBreakdown) {
            const breakdownChart = window.dashboardCharts.attendanceBreakdown;
            
            // Simplify legend display on mobile
            breakdownChart.options.legend.position = 'bottom';
            breakdownChart.options.legend.labels.boxWidth = 8;
            breakdownChart.options.legend.labels.padding = 6;
            
            breakdownChart.update();
          }
          
          // Adjust employee status chart
          if (window.dashboardCharts.employeeStatus) {
            const statusChart = window.dashboardCharts.employeeStatus;
            
            // Adjust scale for better mobile display
            if (statusChart.options.scales && statusChart.options.scales.xAxes) {
              statusChart.options.scales.xAxes[0].ticks.maxTicksLimit = 5;
            }
            
            statusChart.update();
          }
        }
      }
      
      // Ensure all office charts have Employee Count label displayed
      $('.office-chart-summary canvas').each(function() {
        const canvasId = $(this).attr('id');
        if (canvasId && canvasId.startsWith('office-bar-')) {
          const officeId = canvasId.replace('office-bar-', '');
          
          // Get office data
          try {
            const officeDataString = $('#dashboard-data').attr('data-office-data');
            const officeData = JSON.parse(officeDataString);
            const office = officeData.find(o => o.id.toString() === officeId);
            
            if (office) {
              // Create chart with label displayed
              const ctx = document.getElementById(canvasId).getContext('2d');
              
              // Clear existing chart if any
              if (window.officeBarsCharts && window.officeBarsCharts[officeId]) {
                window.officeBarsCharts[officeId].destroy();
              }
              
              // Create new chart with visible legend
              window.officeBarsCharts = window.officeBarsCharts || {};
              window.officeBarsCharts[officeId] = new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: ['Present', 'Absent', 'Late'],
                  datasets: [{
                    label: 'Employee Count',
                    data: [
                      office.stats.presentCount,
                      office.stats.absentCount,
                      office.stats.lateCount
                    ],
                    backgroundColor: [
                      'rgba(75, 192, 192, 0.6)',
                      'rgba(255, 99, 132, 0.6)',
                      'rgba(255, 206, 86, 0.6)'
                    ],
                    borderColor: [
                      'rgba(75, 192, 192, 1)',
                      'rgba(255, 99, 132, 1)',
                      'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  scales: {
                    yAxes: [{
                      ticks: {
                        beginAtZero: true
                      }
                    }]
                  },
                  legend: {
                    display: true,
                    position: 'top',
                    labels: {
                      boxWidth: 12,
                      padding: 5,
                      fontSize: 10
                    }
                  }
                }
              });
            }
          } catch (error) {
            console.error('Error updating office bar chart:', error);
          }
        }
      });
    }
    
    // Apply mobile fixes on document ready and window resize
    $(document).ready(function() {
      // Apply immediately
      makeMobileFriendly();
      
      // Apply on resize with debounce
      let resizeTimer;
      $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
          makeMobileFriendly();
        }, 250);
      });
    });
    
</script>

<style>
/* Additional styles for the enhanced dashboard */
.dashboard-header.refreshing {
    position: relative;
}

.dashboard-header.refreshing:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(to right, transparent, #3a8ef6, transparent);
    animation: loading-bar 1s ease-in-out;
}

@keyframes loading-bar {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.daterangepicker{
    position: unset !important;
    display: none!important;
}

/* Gauge needle styles */
.needle {
    position: absolute;
    width: 4px;
    height: calc(100% - 10px); /* Dynamic height based on container */
    background-color: #e74c3c;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%) rotate(-90deg); /* Default position (0%) */
    transform-origin: bottom center;
    transition: transform 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    z-index: 10;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}

.needle:before {
    content: '';
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 12px;
    height: 12px;
    background-color: #e74c3c;
    border-radius: 50%;
}

.needle-center {
    position: absolute;
    width: 20px;
    height: 20px;
    background-color: #484848;
    border-radius: 50%;
    bottom: 0px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 11;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}

/* Animation for cards */
.dashboard-card, .summary-card, .office-card {
    transition: all 0.4s ease !important;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Media queries for better responsiveness */
@media (min-width: 1200px) {
    .gauge-container {
        max-width: 450px;
    }
    
    .needle {
        height: calc(100% - 10px);
    }
}

@media (max-width: 991px) {
    .gauge-container {
        max-width: 350px;
    }
    
    .needle {
        height: calc(100% - 8px);
    }
}

@media (max-width: 768px) {
    .gauge-container {
        max-width: 300px;
    }
    
    .needle {
        width: 3px;
        height: calc(100% - 6px);
    }
    
    .needle:before {
        width: 10px;
        height: 10px;
        top: -4px;
    }
    
    .needle-center {
        width: 16px;
        height: 16px;
    }
    
    .percentage {
        font-size: 1.75rem;
    }
    
    .label {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .gauge-container {
        max-width: 250px;
        padding: 10px 5px;
    }
    
    .needle {
        width: 2px;
        height: calc(100% - 5px);
    }
    
    .needle:before {
        width: 8px;
        height: 8px;
        top: -3px;
    }
    
    .needle-center {
        width: 14px;
        height: 14px;
    }
    
    .percentage {
        font-size: 1.5rem;
    }
    
    .label {
        font-size: 0.8rem;
    }
    
    .number {
        font-size: 10px;
    }
}

/* Mobile Responsive Fixes for Dashboard */

/* Additional mobile styles to add to your existing CSS */
@media (max-width: 767px) {
    /* Ensure office cards are properly sized on mobile */
    .office-card {
        margin-bottom: 30px;
    }
    
    /* Make chart containers responsive on small screens */
    .chart-container, 
    .office-chart-container, 
    .office-chart-summary {
        min-height: auto !important;
        height: auto !important;
        aspect-ratio: 28/9;
    }
    
    /* Fix office statistics display on mobile */
    .office-stats {
        margin-top: 20px;
    }
    
    /* Fix canvas overflow on mobile */
    canvas {
        max-width: 100%;
    }
    
    /* Improve text readability on small screens */
    .attendance-percentage {
        font-size: 1.5rem;
    }
    
    .gauge-details {
        font-size: 0.85rem;
    }
    
    /* Fix layout issues in office card body */
    .office-card .card-body .row {
        display: block;
    }
    
    .office-card .card-body .col-md-6 {
        width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
    }
    
    /* Add space between stacked columns */
    .office-card .card-body .col-md-6:first-child {
        margin-bottom: 15px;
    }
    
    /* Fix gauge container on mobile */
    .gauge-container {
        max-width: 100%;
    }
    
    /* Ensure the mobile scrolling works properly */
    html, body {
        overflow-x: hidden;
    }
    
    /* Fix office chart width on small screens */
    #office-bar-1, #office-bar-2, #office-bar-3, 
    #office-bar-4, #office-bar-5, #office-bar-6,
    #office-trend-1, #office-trend-2, #office-trend-3,
    #office-trend-4, #office-trend-5, #office-trend-6 {
        width: 100% !important;
        height: auto !important;
    }
}

/* Ensure bar chart labels display properly on all screen sizes */
.office-chart-summary canvas {
    max-height: 120px;
}

/* Fix legend display on all charts */
.chartjs-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 10px;
}

.chartjs-legend li {
    display: inline-block;
    margin: 0 5px;
}
</style>
@endpush