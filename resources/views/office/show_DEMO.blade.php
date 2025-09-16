@extends('layouts.admin')
@section('page-title')
    {{__('Office Details')}}
@endsection

@push('css-page')
<style>
    .office-dashboard {
        margin-bottom: 30px;
    }
    
    .detail-header {
        background: linear-gradient(135deg, #3a8ef6, #6259ca);
        padding: 30px;
        border-radius: 12px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .detail-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }
    
    .detail-header p {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 0;
        position: relative;
        z-index: 2;
    }
    
    .detail-header .header-bg {
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 100%;
        background: url('path/to/office-bg.svg') no-repeat;
        background-position: right;
        background-size: contain;
        opacity: 0.1;
        z-index: 1;
    }
    
    .detail-header .header-actions {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 2;
    }
    
    .stat-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #3a8ef6;
    }
    
    .stat-icon {
        font-size: 2rem;
        margin-bottom: 15px;
        color: #6259ca;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .section-title {
        position: relative;
        margin-bottom: 20px;
        padding-bottom: 10px;
        font-size: 1.4rem;
        font-weight: 600;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(135deg, #3a8ef6, #6259ca);
    }
    
    .map-container {
        height: 457px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    
    .info-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .info-list li {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
    }
    
    .info-list li:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #6c757d;
        font-weight: 500;
    }
    
    .info-value {
        font-weight: 600;
        color: #343a40;
    }
    
    .employee-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .employee-table .table {
        margin-bottom: 0;
    }
    
    .employee-table .table th {
        background: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #fff;
    }
    
    .employee-table .table td {
        vertical-align: middle;.two
    }
    
    .employee-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        margin-right: 10px;
    }
    
    .employee-name {
        font-weight: 600;
        color: #343a40;
        transition: all 0.3s ease;
    }
    
    .employee-name:hover {
        color: #3a8ef6;
        text-decoration: none;
    }
    
    .employee-position {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .badge-presence {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.8rem;
    }
    
    .badge-inside {
        background-color: #28a745;
        color: white;
    }
    
    .badge-outside {
        background-color: #dc3545;
        color: white;
    }
    
    .badge-onleave {
        background-color: #ffc107;
        color: #212529;
    }
    
    .tab-navigation {
        display: flex;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
        overflow-x: auto;
    }
    
    .tab-navigation .tab-item {
        padding: 12px 20px;
        font-weight: 500;
        color: #6c757d;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        white-space: nowrap;
    }
    
    .tab-navigation .tab-item.active {
        color: #3a8ef6;
        border-bottom-color: #3a8ef6;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .attendance-chart {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .chart-container {
        height: 300px;
    }
    
    .action-btn {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        margin-right: 5px;
    }
    
    .btn-view {
        background-color: #17a2b8;
        color: white;
    }
    
    .btn-edit {
        background-color: #ffc107;
        color: #212529;
    }
    
    .btn-delete {
        background-color: #dc3545;
        color: white;
    }
    
    .presence-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    
    .presence-in {
        background-color: #28a745;
    }
    
    .presence-out {
        background-color: #dc3545;
    }
    
    .back-button {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 8px 15px;
        display: inline-flex;
        align-items: center;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    
    .back-button i {
        margin-right: 5px;
    }
    
    .back-button:hover {
        background: #f8f9fa;
        color: #343a40;
        text-decoration: none;
    }
    
    .employee-search {
        position: relative;
        margin-bottom: 20px;
    }
    
    .employee-search input {
        width: 100%;
        padding: 10px 15px;
        padding-right: 40px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 0.9rem;
    }
    
    .employee-search i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    
    .department-filter {
        margin-bottom: 20px;
    }
    
    .department-filter select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 0.9rem;
        background-color: white;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination li {
        margin: 0 5px;
    }
    
    .pagination li a {
        display: block;
        padding: 8px 12px;
        border-radius: 5px;
        background: #f8f9fa;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .pagination li.active a {
        background: #3a8ef6;
        color: white;
    }
    
    .pagination li a:hover {
        background: #e9ecef;
        text-decoration: none;
    }
    
    .presence-summary {
        display: flex;
        margin-bottom: 20px;
    }
    
    .presence-summary .presence-item {
        display: flex;
        align-items: center;
        margin-right: 20px;
    }
    
    canvas{
         position:relative!important;
    }
    
    .action-btn{
        padding: 3px 12px !important;
    }
</style> 
@endpush

@section('content')
<div class="office-dashboard">
    <!-- Header Section --> 
    <div class="detail-header"> 
        <div class="header-bg"></div> 
        <h1 class="text-light">Mumbai Office</h1>
        <p><i class="fas fa-map-marker-alt"></i> 226 Nariman Point, Mumbai, Maharashtra 400021, India</p>
        <div class="header-actions">
            <a href="javascript:history.back()" class="back-button">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @can('Edit Office')
                <!--<a href="#" class="btn btn-light btn-sm mt-md-3" data-url="{{-- route('office.edit', 1) --}}" data-ajax-popup="true" data-title="{{-- __('Edit Office') --}}">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>-->
            @endcan
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="row">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">245</div>
                <div class="stat-label">Total Employees</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-value">205</div>
                <div class="stat-label">Present Today</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-value">12</div>
                <div class="stat-label">Departments</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-value">84%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <div class="tab-item active" data-tab="overview">Overview</div>
        <div class="tab-item" data-tab="employees">Employees</div>
        <div class="tab-item" data-tab="attendance">Attendance</div>
        <div class="tab-item" data-tab="departments">Departments</div>
    </div>
    
    <!-- Tab Content -->
    <div class="tab-content active" id="overview">
        <div class="row">
            <div class="col-md-7">
                <div class="section-title">Location</div>
                <div class="map-container">
                    <div id="office-map" style="width: 100%; height: 100%;"></div>
                </div>
                
                <div class="section-title">Attendance Overview</div>
                <div class="attendance-chart">
                    <div class="chart-container">
                        <canvas id="attendance-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="section-title">Office Information</div>
                <div class="info-card">
                    <ul class="info-list">
                        <li>
                            <span class="info-label">Office Name</span>
                            <span class="info-value">Mumbai Headquarters</span>
                        </li>
                        <li>
                            <span class="info-label">Address</span>
                            <span class="info-value">226 Nariman Point, Mumbai</span>
                        </li>
                        <li>
                            <span class="info-label">City</span>
                            <span class="info-value">Mumbai</span>
                        </li>
                        <li>
                            <span class="info-label">State</span>
                            <span class="info-value">Maharashtra</span>
                        </li>
                        <li>
                            <span class="info-label">Country</span>
                            <span class="info-value">India</span>
                        </li>
                        <li>
                            <span class="info-label">Zip Code</span>
                            <span class="info-value">400021</span>
                        </li>
                        <li>
                            <span class="info-label">Phone</span>
                            <span class="info-value">+91 22 6654 8000</span>
                        </li>
                        <li>
                            <span class="info-label">Email</span>
                            <span class="info-value">mumbai@company.co.in</span>
                        </li>
                    </ul>
                </div>
                
                <div class="section-title">Department Distribution</div>
                <div class="attendance-chart">
                    <div class="chart-container">
                        <canvas id="department-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tab-content" id="employees">
        <div class="row">
            <div class="col-md-9">
                <div class="section-title">Employees</div>
                <div class="presence-summary">
                    <div class="presence-item">
                        <span class="presence-indicator presence-in"></span>
                        <span>Inside Office (185)</span>
                    </div>
                    <div class="presence-item">
                        <span class="presence-indicator presence-out"></span>
                        <span>Outside Office (42)</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="employee-search">
                    <input type="text" placeholder="Search employees..." id="employee-search">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="department-filter">
                    <select id="department-filter">
                        <option value="">All Departments</option>
                        <option value="engineering">Engineering</option>
                        <option value="marketing">Marketing</option>
                        <option value="sales">Sales</option>
                        <option value="hr">Human Resources</option>
                        <option value="finance">Finance</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="employee-table">
            <table class="table">
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
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <a href="{{ route('office.two.index', 1) }}" class="employee-name">Rajesh Sharma</a>
                                    <div class="employee-position">Senior Developer</div>
                                </div>
                            </div>
                        </td>
                        <td>Engineering</td>
                        <td>Team Lead</td>
                        <td><span class="badge badge-presence badge-inside">Inside Office</span></td>
                        <td>8:45 AM</td>
                        <td>
                            <a href="{{ route('office.two.index', 1) }}" class="btn action-btn btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/women/2.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <a href="{{ route('office.two.index', 2) }}" class="employee-name">Priya Patel</a>
                                    <div class="employee-position">UX Designer</div>
                                </div>
                            </div>
                        </td>
                        <td>Engineering</td>
                        <td>Senior Designer</td>
                        <td><span class="badge badge-presence badge-inside">Inside Office</span></td>
                        <td>9:00 AM</td>
                        <td>
                            <a href="{{ route('office.index', 2) }}" class="btn action-btn btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/men/3.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <a href="{{ route('office.two.index', 3) }}" class="employee-name">Vikram Mehta</a>
                                    <div class="employee-position">Marketing Manager</div>
                                </div>
                            </div>
                        </td>
                        <td>Marketing</td>
                        <td>Manager</td>
                        <td><span class="badge badge-presence badge-outside">Outside Office</span></td>
                        <td>--</td>
                        <td>
                            <a href="{{ route('office.index', 3) }}" class="btn action-btn btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/women/4.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <a href="{{ route('office.two.index', 4) }}" class="employee-name">Neha Sharma</a>
                                    <div class="employee-position">HR Specialist</div>
                                </div>
                            </div>
                        </td>
                        <td>Human Resources</td>
                        <td>Specialist</td>
                        <td><span class="badge badge-presence badge-inside">Inside Office</span></td>
                        <td>8:30 AM</td>
                        <td>
                            <a href="{{ route('office.index', 4) }}" class="btn action-btn btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/men/5.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <a href="{{ route('office.two.index', 5) }}" class="employee-name">Arjun Singh</a>
                                    <div class="employee-position">Sales Executive</div>
                                </div>
                            </div>
                        </td>
                        <td>Sales</td>
                        <td>Executive</td>
                        <td><span class="badge badge-presence badge-onleave">On Leave</span></td>
                        <td>--</td>
                        <td>
                            <a href="{{ route('office.index', 5) }}" class="btn action-btn btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/women/6.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <a href="{{ route('office.two.index', 6) }}" class="employee-name">Ananya Gupta</a>
                                    <div class="employee-position">Financial Analyst</div>
                                </div>
                            </div>
                        </td>
                        <td>Finance</td>
                        <td>Analyst</td>
                        <td><span class="badge badge-presence badge-inside">Inside Office</span></td>
                        <td>8:55 AM</td>
                        <td>
                            <a href="{{ route('office.index', 6) }}" class="btn action-btn btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            <ul class="pagination">
                <li><a href="#"><i class="fas fa-chevron-left"></i></a></li>
                <li class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
            </ul>
        </div>
    </div>
    
    <div class="tab-content" id="attendance">
        <div class="section-title">Attendance Analytics</div>
        <div class="row">
            <div class="col-md-6">
                <div class="attendance-chart">
                    <h4>Daily Attendance Rate</h4>
                    <div class="chart-container">
                        <canvas id="daily-attendance-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="attendance-chart">
                    <h4>Monthly Attendance Trend</h4>
                    <div class="chart-container">
                        <canvas id="monthly-attendance-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section-title">Attendance Summary</div>
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-value">84%</div>
                    <div class="stat-label">Average Attendance</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">9:03 AM</div>
                    <div class="stat-label">Avg Check-in Time</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-value">3.2%</div>
                    <div class="stat-label">Absent Rate</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-minus"></i>
                    </div>
                    <div class="stat-value">15</div>
                    <div class="stat-label">On Leave Today</div>
                </div>
            </div>
        </div>
        <!-- Add attendance list section -->
        <div class="section-title">Attendance Records</div>
        <div class="employee-table">
            <table class="table">
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
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <span class="employee-name">Rajesh Sharma</span>
                                    <div class="employee-position">Senior Developer</div>
                                </div>
                            </div>
                        </td>
                        <td>Apr 21, 2025</td>
                        <td>8:45 AM</td>
                        <td>5:30 PM</td>
                        <td><span class="badge badge-presence badge-inside">Present</span></td>
                        <td>8h 45m</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/women/2.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <span class="employee-name">Priya Patel</span>
                                    <div class="employee-position">UX Designer</div>
                                </div>
                            </div>
                        </td>
                        <td>Apr 21, 2025</td>
                        <td>9:00 AM</td>
                        <td>6:15 PM</td>
                        <td><span class="badge badge-presence badge-inside">Present</span></td>
                        <td>9h 15m</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/men/3.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <span class="employee-name">Vikram Mehta</span>
                                    <div class="employee-position">Marketing Manager</div>
                                </div>
                            </div>
                        </td>
                        <td>Apr 21, 2025</td>
                        <td>--</td>
                        <td>--</td>
                        <td><span class="badge badge-presence badge-outside">Absent</span></td>
                        <td>--</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/women/4.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <span class="employee-name">Neha Sharma</span>
                                    <div class="employee-position">HR Specialist</div>
                                </div>
                            </div>
                        </td>
                        <td>Apr 21, 2025</td>
                        <td>8:30 AM</td>
                        <td>5:45 PM</td>
                        <td><span class="badge badge-presence badge-inside">Present</span></td>
                        <td>9h 15m</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://randomuser.me/api/portraits/men/5.jpg" alt="Avatar" class="employee-avatar">
                                <div>
                                    <span class="employee-name">Arjun Singh</span>
                                    <div class="employee-position">Sales Executive</div>
                                </div>
                            </div>
                        </td>
                        <td>Apr 21, 2025</td>
                        <td>--</td>
                        <td>--</td>
                        <td><span class="badge badge-presence badge-onleave">On Leave</span></td>
                        <td>--</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            <ul class="pagination">
                <li><a href="#"><i class="fas fa-chevron-left"></i></a></li>
                <li class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
            </ul>
        </div>
    </div>
    
    <div class="tab-content" id="departments">
        <div class="section-title">Department Overview</div>
        <div class="row">
            <div class="col-md-12">
                <div class="employee-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Head</th>
                                <th>Employees</th>
                                <th>Present Today</th>
                                <th>Attendance Rate</th>
                                <!--<th>Actions</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Engineering</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://randomuser.me/api/portraits/men/71.jpg" alt="Avatar" twocl.ass="employee-avatar" width="50px">
                                        <div>Vikram Mehta</div>
                                    </div>
                                </td>
                                <td>85</td>
                                <td>78</td>
                                <td>91.8%</td>
                                <!--<td>
                                    <a href="#" class="btn action-btn btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>-->
                            </tr>
                            
                            <tr>
                                <td>Marketing</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Avatar" twocl.ass="employee-avatar" width="50px">
                                        <div>Rahul Sharma</div>
                                    </div>
                                </td>
                                <td>32</td>
                                <td>28</td>
                                <td>87.5%</td>
                                <!--<td>
                                    <a href="#" class="btn action-btn btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>-->
                            </tr>
                            
                            <tr>
                                <td>Sales</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://randomuser.me/api/portraits/women/50.jpg" alt="Avatar" twocl.ass="employee-avatar" width="50px">
                                        <div>Priya Patel</div>
                                    </div>
                                </td>
                                <td>48</td>
                                <td>40</td>
                                <td>83.3%</td>
                                <!--<td>
                                    <a href="#" class="btn action-btn btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>-->
                            </tr>
                            
                            <tr>
                                <td>Human Resources</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://randomuser.me/api/portraits/women/66.jpg" alt="Avatar" twocl.ass="employee-avatar" width="50px">
                                        <div>Anjali Gupta</div>
                                    </div>
                                </td>
                                <td>18</td>
                                <td>17</td>
                                <td>94.4%</td>
                                <!--<td>
                                    <a href="#" class="btn action-btn btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>-->
                            </tr>
                            
                            <tr>
                                <td>Finance</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://randomuser.me/api/portraits/men/79.jpg" alt="Avatar" twocl.ass="employee-avatar" width="50px">
                                        <div>Arjun Kapoor</div>
                                    </div>
                                </td>
                                <td>25</td>
                                <td>23</td>
                                <td>92.0%</td>
                                <!--<td>
                                    <a href="#" class="btn action-btn btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>-->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBUI4YwyEVg-TcI_R-sRdwuCuA22pY9VXg&callback=initMap" async defer></script>
<script>
    // Initialize Google Map
    function initMap() {
        const officeLocation = { lat: 18.9217, lng: 72.8332 }; // Mumbai Nariman Point coordinates
        const map = new google.maps.Map(document.getElementById("office-map"), {
            zoom: 15,
            center: officeLocation,
        });
        
        const marker = new google.maps.Marker({
            position: officeLocation,
            map: map,
            title: "Mumbai Headquarters",
            animation: google.maps.Animation.DROP
        });
        
        const cityCircle = new google.maps.Circle({
            strokeColor: "#3a8ef6",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#3a8ef6",
            fillOpacity: 0.1,
            map: map,
            center: officeLocation,
            radius: 200,
        });
    }
    
    $(document).ready(function() {
        // Tab Navigation
        $('.tab-item').on('click', function() {
            const tabId = $(this).data('tab');
            
            $('.tab-item').removeClass('active');
            $(this).addClass('active');
            
            $('.tab-content').removeClass('active').hide();
            $(`#${tabId}`).addClass('active').show();
        });
        
        // Employee Search
        $('#employee-search').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('.employee-table tbody tr').each(function() {
                const rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.includes(value));
            });
        });
        
        // Department Filter
        $('#department-filter').on('change', function() {
            const value = $(this).val().toLowerCase();
            
            $('.employee-table tbody tr').each(function() {
                const department = $(this).find('td:nth-child(2)').text().toLowerCase();
                if (value === '') {
                    $(this).show();
                } else {
                    $(this).toggle(department === value);
                }
            });
        });
        
        // Initialize Charts
        // Attendance Chart
        const attendanceCtx = document.getElementById('attendance-chart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                datasets: [{
                    label: 'Present',
                    data: [235, 230, 240, 225, 220, 60, 40],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#28a745',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Absent',
                    data: [10, 15, 5, 20, 25, 20, 15],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#dc3545',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'On Leave',
                    data: [5, 5, 5, 5, 5, 0, 0],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#ffc107',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Department Chart
        const deptCtx = document.getElementById('department-chart').getContext('2d');
        const deptChart = new Chart(deptCtx, {
            type: 'doughnut',
            data: {
                labels: ['Engineering', 'Marketing', 'Sales', 'HR', 'Finance'],
                datasets: [{
                    data: [85, 32, 48, 18, 25],
                    backgroundColor: [
                        '#3a8ef6',
                        '#6259ca',
                        '#1bc5bd',
                        '#f64e60',
                        '#ffbe0b'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
        
        // Daily Attendance Chart
        const dailyAttendanceCtx = document.getElementById('daily-attendance-chart').getContext('2d');
        const dailyAttendanceChart = new Chart(dailyAttendanceCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Attendance Rate',
                    data: [95, 94, 97, 92, 90, 75, 70],
                    backgroundColor: '#3a8ef6',
                    borderWidth: 0,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
        
        // Monthly Attendance Chart
        const monthlyCtx = document.getElementById('monthly-attendance-chart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Attendance Rate',
                    data: [88, 85, 90, 92, 91, 93, 92, 90, 87, 89, 92, 84],
                    borderColor: '#6259ca',
                    backgroundColor: 'rgba(98, 89, 202, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#6259ca',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 80,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush