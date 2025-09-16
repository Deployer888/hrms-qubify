@extends('layouts.admin')
@push('css-page')
    <link href="{{ asset('css/dash.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="attendance-dashboard">
    <!-- Header Section with Title -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title">Attendance Analytics</h1>
                <div class="refresh-indicator">
                    <span class="refresh-label">Auto-refreshes every 10 minutes</span>
                    <span class="refresh-countdown" id="refresh-countdown">10:00</span>
                </div>
            </div>
            <div class="col-md-2 offset-md-4">
                <label for="office-filter">Office</label>
                <select class="form-select custom-select" id="office-filter" name="office">
                    <option value="all" selected>All Offices</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="filter-container d-none">
        <div class="row g-3">
            {{-- <!--<div class="col-md-3">
                <div class="filter-card">
                    <label for="office-filter">Office</label>
                    <select class="form-select custom-select" id="office-filter" name="office">
                        <option value="all">All Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>--> --}}
            <div class="col-md-3">
                <div class="filter-card">
                    <label for="department-filter">Department</label>
                    <select class="form-select custom-select" id="department-filter" name="department">
                        <option value="all">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-card">
                    <label for="branch-filter">Branch</label>
                    <select class="form-select custom-select" id="branch-filter" name="branch">
                        <option value="all">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
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
            <div class="col-md-3">
                <div class="summary-card present-card">
                    <div class="summary-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="summary-content">
                        <h3 class="summary-title">Present</h3>
                        <p class="summary-value" id="present-count">{{ $dashboardData['presentCount'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card absent-card">
                    <div class="summary-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="summary-content">
                        <h3 class="summary-title">Absent</h3>
                        <p class="summary-value" id="absent-count">{{ $dashboardData['absentCount'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card late-card">
                    <div class="summary-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="summary-content">
                        <h3 class="summary-title">Late</h3>
                        <p class="summary-value" id="late-count">{{ $dashboardData['lateCount'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card leave-card">
                    <div class="summary-icon">
                        <i class="fas fa-calendar-minus"></i>
                    </div>
                    <div class="summary-content">
                        <h3 class="summary-title">On Leave</h3>
                        <p class="summary-value" id="leave-count">{{ $dashboardData['leaveCount'] ?? 0 }}</p>
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
                            <div class="gauge-percentage" id="overall-percentage">{{ $dashboardData['attendancePercentage'] }}%</div>
                            <div class="gauge-details">
                                <span id="present-employees">{{ $dashboardData['presentCount'] }}</span> / 
                                <span id="total-expected">{{ $dashboardData['totalEmployees'] * $dashboardData['workingDays'] }}</span> Present
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
                        <div class="chart-container">
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
            @foreach($offices as $office)
            <div class="office-card" data-office-id="{{ $office->id }}">
                <div class="card-header">
                    <h3>{{ $office->name }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="office-chart-summary">
                                <canvas id="office-bar-{{ $office->id }}"></canvas>
                            </div>
                            <div class="attendance-info text-center mt-2">
                                <div class="attendance-percentage">{{ $office->stats['attendancePercentage'] }}%</div>
                                <div class="attendance-label">Overall Attendance Rate</div>
                            </div>
                            
                            {{-- <!--<div class="office-gauge-container" id="office-gauge-{{ $office->id }}">
                                <figure class="highcharts-figure">
                                <div id="container">
                                    <img src="{{ asset('landing/images/gauge.png') }}" style="width:100%">
                                </div>
                            </figure>
                            </div>
                            <div class="office-gauge-info text-center">
                                
                                <div class="gauge-percentage">{{ $office->stats['attendancePercentage'] }}%</div>
                                <div class="gauge-label">Attendance Rate</div>
                            </div>--> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="office-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Employees:</span>
                                    <span class="stat-value">{{ $office->stats['employeeCount'] }}</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Present:</span>
                                    <span class="stat-value">{{ $office->stats['presentCount'] }}</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Absent:</span>
                                    <span class="stat-value">{{ $office->stats['absentCount'] }}</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Late:</span>
                                    <span class="stat-value">{{ $office->stats['lateCount'] }}</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">On Leave:</span>
                                    <span class="stat-value">{{ $office->stats['leaveCount'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="office-chart-container mt-3">
                        <canvas id="office-trend-{{ $office->id }}"></canvas>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Data Container for JavaScript with dynamic data from controller -->
<div id="dashboard-data" class="d-none" 
    data-overall-percentage="{{ $dashboardData['attendancePercentage'] ?? 0 }}"
    data-present-count="{{ $dashboardData['presentCount'] ?? 0 }}"
    data-absent-count="{{ $dashboardData['absentCount'] ?? 0 }}"
    data-late-count="{{ $dashboardData['lateCount'] ?? 0 }}"
    data-leave-count="{{ $dashboardData['leaveCount'] ?? 0 }}"
    data-total-employees="{{ $dashboardData['totalEmployees'] ?? 0 }}"
    data-working-days="{{ $dashboardData['workingDays'] ?? 0 }}"
    data-weekly-trend="{{ json_encode($dashboardData['weeklyTrend'] ?? []) }}"
    data-office-data="{{ json_encode($dashboardData['officeData'] ?? []) }}">
</div>
@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-gauge@0.3.0/dist/chartjs-gauge.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('js/dash.js') }}"></script>
@endpush