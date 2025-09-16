@extends('layouts.admin')
@section('page-title')
    {{ __('Office Details') }}
@endsection

@push('css-page')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
    .office-dashboard {
        padding: 25px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Premium Page Header */
    .page-header-premium {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 35px 40px;
        margin-bottom: 35px;
        color: white;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header-premium::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .header-left {
        display: flex;
        align-items: center;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 25px;
        backdrop-filter: blur(10px);
    }

    .header-icon i {
        font-size: 36px;
        color: white;
    }

    .header-text h1 {
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 8px;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .header-text p {
        font-size: 16px;
        opacity: 0.9;
        color: white;
        font-weight: 400;
        display: flex;
        align-items: center;
    }

    .header-text p i {
        margin-right: 8px;
    }

    .header-actions {
        display: flex;
        gap: 15px;
        position: relative;
        z-index: 2;
    }

    .back-button {
        background: rgba(255,255,255,0.2);
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 12px;
        padding: 12px 24px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .back-button:hover {
        background: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    /* Metrics Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .dashboard-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .card-content {
        display: flex;
        align-items: center;
    }

    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 24px;
        color: white;
    }

    .users-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .present-icon { background: linear-gradient(135deg, #4f7cff 0%, #3b82f6 100%); }
    .plans-icon { background: linear-gradient(135deg, #26c6da 0%, #00bcd4 100%); }
    .revenue-icon { background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%); }
    .absent-icon { background: linear-gradient(135deg, #ff6b6b 0%, #ef4444 100%); }
    .leave-icon { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }

    .card-details {
        flex: 1;
    }

    .card-title {
        font-size: 14px;
        font-weight: 600;
        color: #8b9dc3;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .card-value {
        font-size: 36px;
        font-weight: 800;
        color: #2d3748;
        margin-bottom: 5px;
        line-height: 1;
    }

    .card-subtitle {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    /* Premium Tab Navigation */
    .office-tab-navigation {
        display: flex;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        margin-bottom: 35px;
        overflow-x: auto;
        border: 1px solid rgba(255,255,255,0.2);
        position: relative;
    }

    .office-tab-item {
        padding: 20px 30px;
        font-weight: 600;
        color: #64748b;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        position: relative;
        background: transparent;
        font-size: 16px;
        border: none;
        outline: none;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: fit-content;
        flex: 1;
    }

    .office-tab-item:hover {
        color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.03), rgba(118, 75, 162, 0.03));
        text-decoration: none;
    }

    .office-tab-item.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
        font-weight: 700;
    }

    /* Tab Content */
    .tab-content {
        display: none;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .tab-content.active {
        display: block !important;
        opacity: 1;
        transform: translateY(0);
    }

    /* Section Styling */
    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title::before {
        content: '';
        width: 4px;
        height: 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    /* Content Cards */
    .content-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: 1px solid rgba(255,255,255,0.2);
        margin-bottom: 30px;
        height: 400px;
        overflow-y: auto;
    }

    /* Map Container */
    .map-container {
        height: 400px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        border: 1px solid rgba(255,255,255,0.2);
        background: white;
        position: relative;
    }

    #map {
        width: 100%;
        height: 100%;
        border-radius: 20px;
    }

    /* Info List */
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #64748b;
        font-size: 14px;
    }

    .info-value {
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
        text-align: right;
        word-break: break-word;
        max-width: 200px;
    }

    /* Chart Container */
    .chart-container {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: 1px solid rgba(255,255,255,0.2);
        margin-bottom: 30px;
        height: 400px;
    }

    .chart-header {
        margin-bottom: 25px;
    }

    .chart-title {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .chart-subtitle {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    .chart-canvas {
        position: relative;
        height: 300px;
        width: 100%;
    }

    /* Employee Table Styling */
    .office-employee-table {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: 1px solid rgba(255,255,255,0.2);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: collapse;
    }

    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 700;
        padding: 18px 20px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        font-weight: 500;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    /* Employee Avatar */
    .employee-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 12px;
        object-fit: cover;
        border: 2px solid #f1f5f9;
    }

    .employee-name {
        font-weight: 600;
        color: #2d3748;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .employee-name:hover {
        color: #667eea;
        text-decoration: none;
    }

    .employee-position {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    /* Status Badges */
    .office-badge-presence {
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }

    .office-badge-inside {
        background: #10b981;
        color: white;
    }

    .office-badge-outside {
        background: #ef4444;
        color: white;
    }

    .office-badge-onleave {
        background: #8b5cf6;
        color: white;
    }

    /* Presence Summary */
    .office-presence-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 25px;
        padding: 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .office-presence-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 14px;
        color: #2d3748;
        flex: 1;
        min-width: 150px;
    }

    .office-presence-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .office-presence-in {
        background: #10b981;
    }

    .office-presence-out {
        background: #ef4444;
    }

    /* Search Controls */
    .office-search-control {
        position: relative;
        margin-bottom: 20px;
    }

    .office-search-control input {
        width: 100%;
        padding: 12px 45px 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .office-search-control input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .office-search-control i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 16px;
    }

    /* Action Buttons */
    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-view {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        color: white;
    }

    /* Form Controls */
    .form-control {
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .office-dashboard {
            padding: 15px;
        }

        .page-header-premium {
            padding: 25px;
        }

        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        .header-text h1 {
            font-size: 24px;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .dashboard-card {
            padding: 20px;
        }

        .card-value {
            font-size: 28px;
        }

        .office-presence-summary {
            flex-direction: column;
            gap: 15px;
        }

        .office-tab-navigation {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .office-tab-item {
            min-width: 120px;
            flex: none;
        }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>
@endpush

@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <div class="office-dashboard">
        <!-- Premium Header Section -->
        <div class="page-header-premium">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="header-text">
                        <h1>{{ $office->name ?? 'Office' }}</h1>
                        <p><i class="fas fa-map-marker-alt"></i>
                           {{ $office->address ?? $office->location }}, {{ $office->city }}, {{ $office->state }} {{ $office->zip_code }}, {{ $office->country }}
                        </p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="javascript:history.back()" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Premium Stats Section -->
        <div class="metrics-grid">
            <div class="dashboard-card">
                <div class="card-content">
                    <div class="card-icon users-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-details">
                        <div class="card-title">Total Employees</div>
                        <div class="card-value">{{ $employeeCount }}</div>
                        <div class="card-subtitle">Active workforce</div>
                    </div>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-content">
                    <div class="card-icon present-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="card-details">
                        <div class="card-title">Present Today</div>
                        <div class="card-value">{{ $todayPresent }}</div>
                        <div class="card-subtitle">Currently in office</div>
                    </div>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-content">
                    <div class="card-icon plans-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="card-details">
                        <div class="card-title">Departments</div>
                        <div class="card-value">{{ count($departmentStats) }}</div>
                        <div class="card-subtitle">Active departments</div>
                    </div>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-content">
                    <div class="card-icon revenue-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="card-details">
                        <div class="card-title">Attendance Rate</div>
                        <div class="card-value">{{ $attendanceRate }}%</div>
                        <div class="card-subtitle">Overall performance</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Tab Navigation -->
        <div class="office-tab-navigation">
            <button class="office-tab-item active" data-tab="overview">Overview</button>
            <button class="office-tab-item" data-tab="employees">Employees</button>
            <button class="office-tab-item" data-tab="attendance">Attendance</button>
            <button class="office-tab-item" data-tab="departments">Departments</button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content active" id="overview">
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="section-title">Location</div>
                    <div class="map-container">
                        <div id="map"></div>
                    </div>

                    <div class="section-title">Weekly Attendance Overview</div>
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Attendance Analytics</h3>
                            <p class="chart-subtitle">Weekly attendance trends and analytics</p>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="section-title">Office Information</div>
                    <div class="content-card">
                        <ul class="info-list">
                            <li>
                                <span class="info-label">Office Name</span>
                                <span class="info-value">{{ $office->name ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="info-label">Address</span>
                                <span class="info-value">{{ $office->address ?? $office->location ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="info-label">City</span>
                                <span class="info-value">{{ $office->city ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="info-label">State</span>
                                <span class="info-value">{{ $office->state ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="info-label">Country</span>
                                <span class="info-value">{{ $office->country ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="info-label">Zip Code</span>
                                <span class="info-value">{{ $office->zip_code ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="info-label">Phone</span>
                                <span class="info-value">{{ $office->phone ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $office->email ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="info-label">Working Hours</span>
                                <span class="info-value">9:00 AM - 6:00 PM</span>
                            </li>
                            <li>
                                <span class="info-label">Established</span>
                                <span class="info-value">{{ $office->created_at ? $office->created_at->format('Y') : 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="section-title">Department Distribution</div>
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Department Overview</h3>
                            <p class="chart-subtitle">Employee distribution across departments</p>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="employees">
            <div class="row mb-3">
                <div class="col-lg-8 col-md-12">
                    <div class="section-title">Employees</div>
                    <div class="office-presence-summary">
                        <div class="office-presence-item">
                            <span class="office-presence-indicator office-presence-in"></span>
                            <span>Inside Office ({{ $insideOffice }})</span>
                        </div>
                        <div class="office-presence-item">
                            <span class="office-presence-indicator office-presence-out"></span>
                            <span>Outside Office ({{ $outsideOffice }})</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="office-search-control">
                        <input type="text" placeholder="Search employees..." id="employee-search">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>

            <div class="office-employee-table">
                <table id="employees-datatable" class="table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAttendanceLogs as $log)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($log['avatar'])
                                        <img src="{{ asset('storage/uploads/avatar/'.$log['avatar']) }}" alt="Avatar" class="employee-avatar">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log['name']) }}&background=667eea&color=fff" alt="Avatar" class="employee-avatar">
                                    @endif
                                    <div>
                                        <a href="{{ route('office.employee', $log['id']) }}" class="employee-name">{{ $log['name'] }}</a>
                                        <div class="employee-position">{{ $log['position'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $log['department'] }}</td>
                            <td>{{ $log['position'] }}</td>
                            <td>
                                @if($log['status'] == 'Present' || $log['status'] == 'Late')
                                    <span class="office-badge-presence office-badge-inside">Inside Office</span>
                                @elseif($log['status'] == 'On Leave')
                                    <span class="office-badge-presence office-badge-onleave">On Leave</span>
                                @else
                                    <span class="office-badge-presence office-badge-outside">Outside Office</span>
                                @endif
                            </td>
                            <td>{{ $log['clock_in'] }}</td>
                            <td><a href="{{ route('office.employee', $log['id']) }}" class="btn action-btn btn-view"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 40px; color: #64748b;">
                                <i class="fas fa-users" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i><br>
                                No employees found in this office
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-content" id="attendance">
            <div class="section-title">Attendance Analytics</div>

            <div class="section-title">Attendance Summary</div>
            <div class="metrics-grid">
                <div class="dashboard-card">
                    <div class="card-content">
                        <div class="card-icon present-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="card-details">
                            <div class="card-title">Average Attendance</div>
                            <div class="card-value">{{ $attendanceRate }}%</div>
                            <div class="card-subtitle">Overall performance</div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-content">
                        <div class="card-icon absent-icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="card-details">
                            <div class="card-title">Absent Today</div>
                            <div class="card-value">{{ $todayAbsent }}</div>
                            <div class="card-subtitle">Today's absence</div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-content">
                        <div class="card-icon leave-icon">
                            <i class="fas fa-calendar-minus"></i>
                        </div>
                        <div class="card-details">
                            <div class="card-title">On Leave Today</div>
                            <div class="card-value">{{ $todayLeave }}</div>
                            <div class="card-subtitle">Approved leaves</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Analytics Chart -->
            <div class="section-title">Weekly Trends</div>
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Attendance Trends</h3>
                    <p class="chart-subtitle">7-day attendance analysis</p>
                </div>
                <div class="chart-canvas">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>

            <div class="section-title">Attendance Records</div>

            <div class="row mb-3">
                <div class="col-lg-4 col-md-6">
                    <div class="office-search-control">
                        <input type="text" placeholder="Search attendance records..." id="attendance-search">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>

            <div class="office-employee-table">
                <table id="attendance-datatable" class="table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Hours Worked</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAttendanceLogs as $log)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($log['avatar'])
                                        <img src="{{ asset('storage/uploads/avatar/'.$log['avatar']) }}" alt="Avatar" class="employee-avatar">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log['name']) }}&background=667eea&color=fff" alt="Avatar" class="employee-avatar">
                                    @endif
                                    <div>
                                        <span class="employee-name">{{ $log['name'] }}</span>
                                        <div class="employee-position">{{ $log['position'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $log['date'] }}</td>
                            <td>{{ $log['clock_in'] }}</td>
                            <td>{{ $log['clock_out'] }}</td>
                            <td>
                                @if($log['status'] == 'Present')
                                    <span class="office-badge-presence office-badge-inside">Present</span>
                                @elseif($log['status'] == 'Late')
                                    <span class="office-badge-presence office-badge-inside">Late</span>
                                @elseif($log['status'] == 'On Leave')
                                    <span class="office-badge-presence office-badge-onleave">On Leave</span>
                                @else
                                    <span class="office-badge-presence office-badge-outside">Absent</span>
                                @endif
                            </td>
                            <td>
                                @if(is_object($log['hours_worked']) && $log['hours_worked']->count() > 0)
                                    @php
                                        $totalMinutes = 0;
                                        foreach($log['hours_worked'] as $attendance) {
                                            if($attendance->clock_in && $attendance->clock_out) {
                                                $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
                                                $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
                                                $totalMinutes += $clockOut->diffInMinutes($clockIn);
                                            }
                                        }
                                        $hours = floor($totalMinutes / 60);
                                        $minutes = $totalMinutes % 60;
                                    @endphp
                                    {{ sprintf('%02d:%02d Hrs', $hours, $minutes) }}
                                @else
                                    00:00 Hrs
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No attendance records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-content" id="departments">
            <div class="section-title">Department Overview</div>
            <div class="row">
                <div class="col-12">
                    <div class="office-employee-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Head</th>
                                    <th>Employees</th>
                                    <th>Present Today</th>
                                    <th>Attendance Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departmentStats as $dept)
                                <tr>
                                    <td><strong>{{ $dept['name'] }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($dept['head_avatar'])
                                                <img src="{{ asset('storage/uploads/avatar/'.$dept['head_avatar']) }}" alt="Avatar" class="employee-avatar">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($dept['head']) }}&background=667eea&color=fff" alt="Avatar" class="employee-avatar">
                                            @endif
                                            <div>{{ $dept['head'] }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $dept['total'] }}</td>
                                    <td>{{ $dept['present'] }}</td>
                                    <td>
                                        @if($dept['percentage'] >= 80)
                                            <span style="color: #10b981; font-weight: 600;">{{ $dept['percentage'] }}%</span>
                                        @elseif($dept['percentage'] >= 60)
                                            <span style="color: #ffa726; font-weight: 600;">{{ $dept['percentage'] }}%</span>
                                        @else
                                            <span style="color: #ef4444; font-weight: 600;">{{ $dept['percentage'] }}%</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No departments found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <script>
        let map;
        let attendanceChart;
        let departmentChart;
        let attendanceTrendChart;

        // Initialize Google Map
        function initMap() {
            @if($office->latitude && $office->longitude)
            const officeLocation = { 
                lat: {{ $office->latitude }}, 
                lng: {{ $office->longitude }} 
            };
            @else
            const officeLocation = { 
                lat: 30.6502, 
                lng: 76.8127 
            };
            @endif

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: officeLocation,
                gestureHandling: 'cooperative',
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true,
                styles: [
                    {
                        featureType: 'all',
                        elementType: 'geometry.fill',
                        stylers: [{ saturation: -20 }]
                    },
                    {
                        featureType: 'water',
                        elementType: 'geometry',
                        stylers: [{ color: '#a2daf2' }]
                    },
                    {
                        featureType: 'road',
                        elementType: 'geometry',
                        stylers: [{ lightness: 20 }]
                    }
                ]
            });

            // Custom marker
            const marker = new google.maps.Marker({
                position: officeLocation,
                map: map,
                title: "{{ $office->name ?? 'Office' }}",
                animation: google.maps.Animation.DROP,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 12,
                    fillColor: '#667eea',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 3
                }
            });

            // Office radius circle
            const radiusCircle = new google.maps.Circle({
                strokeColor: "#667eea",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#667eea",
                fillOpacity: 0.15,
                map: map,
                center: officeLocation,
                radius: {{ $office->radius ?? 400 }},
            });

            // Info window
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 15px; font-family: 'Inter', sans-serif; min-width: 200px;">
                        <h3 style="margin: 0 0 8px 0; color: #2d3748; font-size: 18px; font-weight: 700;">{{ $office->name ?? 'Office' }}</h3>
                        <p style="margin: 0 0 8px 0; color: #64748b; font-size: 14px;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 6px; color: #667eea;"></i>
                            {{ $office->address ?? $office->location }}, {{ $office->city }}
                        </p>
                        <p style="margin: 0; color: #64748b; font-size: 12px;">
                            <i class="fas fa-circle" style="margin-right: 6px; color: #667eea;"></i>
                            Office Location
                        </p>
                    </div>
                `
            });

            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });
        }

        // Initialize Charts
        function initCharts() {
            // Prepare weekly data for charts
            const weeklyData = @json($weeklyStats ?? []);
            const departmentData = @json($departmentStats ?? []);
            
            // Check if we have data before initializing charts
            if (weeklyData.length === 0) {
                console.warn('No weekly attendance data available');
                // Show placeholder message in chart containers
                document.getElementById('attendanceChart').getContext('2d').fillText('No data available', 50, 50);
                document.getElementById('attendanceTrendChart').getContext('2d').fillText('No data available', 50, 50);
                return;
            }
            
            // Attendance Overview Chart (Overview Tab)
            const ctx1 = document.getElementById('attendanceChart').getContext('2d');
            attendanceChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: weeklyData.map(item => `${item.day} ${item.date}`),
                    datasets: [{
                        label: 'Present',
                        data: weeklyData.map(item => item.present),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Absent',
                        data: weeklyData.map(item => item.absent),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'On Leave',
                        data: weeklyData.map(item => item.leave),
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Department Distribution Chart
            const ctx2 = document.getElementById('departmentChart').getContext('2d');
            departmentChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: departmentData.length > 0 ? departmentData.map(dept => dept.name) : ['No Data'],
                    datasets: [{
                        data: departmentData.length > 0 ? departmentData.map(dept => dept.total) : [1],
                        backgroundColor: [
                            '#667eea',
                            '#4BC0C0',
                            '#FF6384',
                            '#FFCE56',
                            '#8b5cf6',
                            '#26c6da'
                        ],
                        borderWidth: 0,
                        hoverBorderWidth: 3,
                        hoverBorderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });

            // Attendance Trend Chart (Attendance Tab)
            const ctx3 = document.getElementById('attendanceTrendChart').getContext('2d');
            attendanceTrendChart = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: weeklyData.map(item => item.day),
                    datasets: [{
                        label: 'Attendance Rate (%)',
                        data: weeklyData.map(item => item.attendance_rate),
                        backgroundColor: weeklyData.map(item => {
                            if (item.attendance_rate >= 80) return '#10b981';
                            if (item.attendance_rate >= 60) return '#ffa726';
                            return '#ef4444';
                        }),
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        $(document).ready(function() {
            console.log('Office Dashboard loaded successfully');

            // Tab Navigation
            $('.office-tab-item').on('click', function(e) {
                e.preventDefault();

                const tabId = $(this).data('tab');
                console.log('Switching to tab:', tabId);

                // Remove active class from all tabs and contents
                $('.office-tab-item').removeClass('active');
                $('.tab-content').removeClass('active');

                // Add active class to clicked tab
                $(this).addClass('active');

                // Show the target tab content
                $(`#${tabId}`).addClass('active');

                // Initialize DataTables for specific tabs
                if (tabId === 'employees') {
                    initializeEmployeeTable();
                } else if (tabId === 'attendance') {
                    initializeAttendanceTable();
                }
            });

            // Initialize DataTables
            function initializeEmployeeTable() {
                if ($.fn.DataTable.isDataTable('#employees-datatable')) {
                    $('#employees-datatable').DataTable().destroy();
                }

                $('#employees-datatable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    columnDefs: [
                        { orderable: false, targets: [5] },
                        { className: "text-center", targets: [3, 4, 5] }
                    ],
                    language: {
                        search: "Search employees:",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });

                console.log('Employee DataTable initialized');
            }

            function initializeAttendanceTable() {
                if ($.fn.DataTable.isDataTable('#attendance-datatable')) {
                    $('#attendance-datatable').DataTable().destroy();
                }

                $('#attendance-datatable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    order: [[1, 'desc']],
                    columnDefs: [
                        { type: 'date', targets: [1] },
                        { className: "text-center", targets: [2, 3, 4, 5] }
                    ],
                    language: {
                        search: "Search attendance:",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });

                console.log('Attendance DataTable initialized');
            }

            // Search functionality
            $('#employee-search').on('keyup', function() {
                if ($.fn.DataTable.isDataTable('#employees-datatable')) {
                    $('#employees-datatable').DataTable().search($(this).val()).draw();
                }
            });

            $('#attendance-search').on('keyup', function() {
                if ($.fn.DataTable.isDataTable('#attendance-datatable')) {
                    $('#attendance-datatable').DataTable().search($(this).val()).draw();
                }
            });

            // Initialize charts
            setTimeout(() => {
                initCharts();
                console.log('Charts initialized');
            }, 500);

            console.log('Dashboard initialization complete');
        });

        // Make initMap globally available for Google Maps callback
        window.initMap = initMap;
    </script>

    <!-- Google Maps API -->
    @if($office->latitude && $office->longitude)
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBUI4YwyEVg-TcI_R-sRdwuCuA22pY9VXg&callback=initMap"></script>
    @else
    <script>
        // If no coordinates available, hide the map container
        document.addEventListener('DOMContentLoaded', function() {
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                mapContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #64748b; font-size: 16px;"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Location coordinates not available</div>';
            }
        });
    </script>
    @endif
@endsection
