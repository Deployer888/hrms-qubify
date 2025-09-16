@extends('layouts.admin')

{{-- @section('page-title')
    {{ __('Manage Leave') }}
@endsection --}}

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('Create Leave')
            @if($selfLeaves == 'true' || \Auth::user()->type == 'hr')
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('leave.create') }}" class="btn btn-primary-custom btn-create-leave"
                    data-ajax-popup="true" data-title="{{ __('Create New Leave') }}">
                    <i class="fas fa-plus"></i> {{ __('Create Leave') }}
                </a>
            </div>
            @endif
        @endcan
    </div>
@endsection

@if($data??0)
    <div class="modal fade show" id="commonModalCustom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-modal="true" style="display: block; padding-left: 7px;backdrop-filter: blur(10px)">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Leave Action</h4>
                    <button type="button" class="close custom-close" data-bs-dismiss="modal" aria-label="Close" onclick="removeModel()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!!$data!!}
                </div>
            </div>
        </div>
    </div>
@endif

@section('content')
<style>
    /* Enhanced Color Scheme */
    :root {
        --primary-purple: #5f5cff;
        --primary-gradient: linear-gradient(135deg, #5f5cff 0%, #5f5cff 100%);
        --secondary-purple: #5f5cff;
        --light-purple: #d6deff;
        --purple-text: #5f5cff;
        --success-green: #00b894;
        --warning-orange: #fdcb6e;
        --danger-red: #e17055;
        --info-blue: #74b9ff;
        --light-bg: #f8f9fc;
        --white: #ffffff;
        --text-dark: #2d3436;
        --text-light: #636e72;
        --border-light: #e2e8f0;
        --shadow-light: 0 4px 6px rgba(108, 92, 231, 0.08);
        --shadow-medium: 0 10px 25px rgba(108, 92, 231, 0.15);
        --shadow-hover: 0 15px 35px rgba(108, 92, 231, 0.2);
        --border-radius: 16px;
        --border-radius-sm: 8px;
        }

    /* Enhanced Page Layout */
    .leave-management-container {
        background: linear-gradient(135deg, #f8f9fc 0%, #e8ecff 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    /* Premium Header Section */
    .leave-header {
        background: var(--primary-gradient);
        color: white;
        padding: 2.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-medium);
        position: relative;
        overflow: hidden;
    }

    .leave-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .leave-header h2 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 2;
        color: #fff;
    }

    .leave-header p {
        margin: 0.75rem 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
        position: relative;
        z-index: 2;
    }

    /* Enhanced Statistics Cards */
    .stats-container {
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 2rem 1.5rem;
        box-shadow: var(--shadow-light);
        border: 1px solid var(--border-light);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--primary-gradient);
        border-radius: var(--border-radius) var(--border-radius) 0 0;
    }
    
    .table-container {
        max-height: 600px !important;
    }
    
    .table-responsive {
      max-height: 95% !important;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-hover);
    }

    .stat-card .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-card .stat-label {
        color: var(--text-light);
        font-size: 0.95rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    #ColspanTD {
        background: linear-gradient(135deg, #e9edff 0%, rgb(247, 247, 247) 100%);
    }

    .stat-card .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-left: auto;
    }

    .stat-card.total-leaves .stat-icon { 
        background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%); 
    }
    .stat-card.pending-leaves .stat-icon { 
        background: linear-gradient(135deg, var(--warning-orange) 0%, #fab1a0 100%); 
    }
    .stat-card.approved-leaves .stat-icon { 
        background: linear-gradient(135deg, var(--success-green) 0%, #00cec9 100%); 
    }
    .stat-card.rejected-leaves .stat-icon { 
        background: linear-gradient(135deg, var(--danger-red) 0%, #fd79a8 100%); 
    }

    /* Premium Employee List Card */
    .employee-list-card {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-medium);
        border: 1px solid var(--border-light);
        overflow: hidden;
        position: relative;
    }

    .employee-list-header {
        background: linear-gradient(135deg, var(--light-purple) 0%, rgba(108, 92, 231, 0.1) 100%);
        padding: 2rem;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .employee-list-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .employee-count-badge {
        background: var(--primary-gradient);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 0.75rem;
        box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
    }

    /* Enhanced Table Controls */
    .table-controls {
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, #fafbff 0%, #f0f2ff 100%);
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .show-entries {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-light);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .show-entries select {
        border: 2px solid var(--border-light);
        border-radius: var(--border-radius-sm);
        padding: 0.5rem 1rem;
        color: var(--text-dark);
        background: var(--white);
        font-weight: 500;
        transition: border-color 0.3s ease;
    }

    .show-entries select:focus {
        border-color: var(--primary-purple);
        outline: none;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        border: 2px solid var(--border-light);
        border-radius: 25px;
        padding: 0.75rem 3rem 0.75rem 1.5rem;
        width: 300px;
        color: var(--text-dark);
        background: var(--white);
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        border-color: var(--primary-purple);
        outline: none;
        box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
    }

    .search-box i {
        position: absolute;
        right: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
        font-size: 1.1rem;
    }

    /* Premium Table Styling */
    .table-container {
        position: relative;
        height: 600px; /* Fixed height for scrolling */
        overflow: hidden;
    }

    .table-responsive {
        height: 100%;
        overflow-y: auto;
        overflow-x: auto;
        background: var(--white);
        border-radius: 0 0 var(--border-radius) var(--border-radius);
    }

    .custom-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .custom-table thead th {
        background: var(--primary-gradient);
        color: white;
        padding: 1.25rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.75px;
        border: none;
        position: sticky;
        top: 0;
        z-index: 100;
        text-align: center;
        box-shadow: 0 2px 8px rgba(108, 92, 231, 0.2);
    }

    .custom-table tbody tr {
        border-bottom: 1px solid var(--border-light);
        transition: all 0.3s ease;
    }

    .custom-table tbody tr:nth-child(even) {
        background-color: rgba(108, 92, 231, 0.02);
    }

    .custom-table tbody tr:hover {
        background: linear-gradient(135deg, var(--light-purple) 0%, rgba(108, 92, 231, 0.05) 100%);
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(108, 92, 231, 0.1);
    }

    .custom-table tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border: none;
        color: var(--text-dark);
        font-size: 0.9rem;
        font-weight: 500;
        text-align: center;
    }

    /* Employee Header Row */
    .employee-header-row {
        background: var(--primary-gradient) !important;
        color: white !important;
        position: sticky;
        top: 60px; /* Below main header */
        z-index: 99;
    }

    .employee-header-row td {
        padding: 1.5rem !important;
        font-weight: 700 !important;
        font-size: 1.1rem !important;
        text-align: left !important;
        color: white !important;
        border-bottom: 2px solid rgba(255,255,255,0.2) !important;
    }

    .employee-name-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 96%;
    }

    .employee-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .employee-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .employee-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .leave-counter {
        background: rgba(255,255,255,0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .view-more-btn {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .view-more-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: scale(1.05);
    }

    /* Collapsible Rows */
    .collapsible-rows {
        display: none;
    }

    .collapsible-rows.show {
        display: table-row;
    }

    /* Enhanced Status Badges */
    .status-badge {
        padding: 0.6rem 1.2rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    .status-badge.pending {
        background: linear-gradient(135deg, var(--warning-orange) 0%, #fab1a0 100%);
        color: white;
    }

    .status-badge.approved {
        background: linear-gradient(135deg, var(--success-green) 0%, #00cec9 100%);
        color: white;
    }

    .status-badge.rejected {
        background: linear-gradient(135deg, var(--danger-red) 0%, #fd79a8 100%);
        color: white;
    }

    /* Enhanced Tags */
    .employee-id-tag {
        background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-block;
        box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
    }

    .leave-type-tag {
        background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    /* Enhanced Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: var(--border-radius-sm);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .action-btn:hover::before {
        left: 100%;
    }

    .action-btn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .action-btn.btn-edit {
        background: linear-gradient(135deg, var(--info-blue) 0%, #0984e3 100%);
        color: white;
    }

    .action-btn.btn-delete {
        background: linear-gradient(135deg, var(--danger-red) 0%, #fd79a8 100%);
        color: white;
    }

    .action-btn.btn-action {
        background: linear-gradient(135deg, var(--success-green) 0%, #00cec9 100%);
        color: white;
    }

    /* Enhanced Create Button */
    .btn-primary-custom {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: var(--shadow-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary-custom:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-hover);
        color: white;
    }

    /* Enhanced Modal */
    .custom-modal {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        overflow: hidden;
    }

    .custom-modal-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        border-bottom: none;
        padding: 2rem;
    }

    .custom-modal-header .modal-title {
        font-weight: 700;
        margin: 0;
        font-size: 1.2rem;
    }

    .custom-close {
        color: white;
        opacity: 0.8;
        font-size: 1.5rem;
        font-weight: 300;
        border: none;
        background: none;
        transition: all 0.3s ease;
    }

    .custom-close:hover {
        opacity: 1;
        color: white;
        transform: scale(1.1);
    }

    /* Enhanced Scrollbar */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: var(--light-bg);
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--primary-gradient);
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--secondary-purple) 0%, var(--primary-purple) 100%);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .leave-management-container {
            padding: 1rem;
        }
        
        .leave-header {
            padding: 2rem 1.5rem;
        }
        
        .table-controls {
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem;
        }
        
        .search-box input {
            width: 100%;
        }
        
        .table-container {
            height: 500px;
        }
        
        .custom-table {
            font-size: 0.8rem;
        }
        
        .custom-table thead th,
        .custom-table tbody td {
            padding: 1rem 0.5rem;
        }

        .employee-header-row td {
            padding: 1rem !important;
            font-size: 1rem !important;
        }

        .employee-name-section {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
    }

    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="leave-management-container">
    <!-- Enhanced Header Section -->
    <div class="leave-header">
        <h2>
            <i class="fas fa-calendar-alt"></i>
            Leave Management
        </h2>
        <p>Comprehensive leave tracking and management dashboard for enhanced workforce planning</p>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="stats-container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card total-leaves">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-number">{{ $leaves->count() ?? 0 }}</div>
                            <div class="stat-label">Total Leaves</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card pending-leaves">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-number">
                                @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                    {{ $leaves->where('status', 'Pending')->count() }}
                                @else
                                    {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Pending')->count(); }) }}
                                @endif
                            </div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card approved-leaves">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-number">
                                @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                    {{ $leaves->where('status', 'Approve')->count() }}
                                @else
                                    {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Approve')->count(); }) }}
                                @endif
                            </div>
                            <div class="stat-label">Approved</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card rejected-leaves">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-number">
                                @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                    {{ $leaves->where('status', 'Reject')->count() }}
                                @else
                                    {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Reject')->count(); }) }}
                                @endif
                            </div>
                            <div class="stat-label">Rejected</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Employee List Card -->
    <div class="employee-list-card">
        <div class="employee-list-header">
            <h3 class="employee-list-title">
                <i class="fas fa-users"></i>
                Employee Leave Records
                <span class="employee-count-badge">
                    @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                        {{ $leaves->count() }} Records
                    @else
                        {{ $leaves->count() }} Employees
                    @endif
                </span>
            </h3>
        </div>

        <div class="table-controls">
            <div class="show-entries">
                <span>Show</span>
                <select id="entries-per-page">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
            <div class="search-box">
                <input type="text" placeholder="Search employees, leave types, dates..." id="search-input">
                <i class="fas fa-search"></i>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table custom-table" id="datatable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-tag"></i> Leave Type</th>
                            <th><i class="fas fa-calendar-plus"></i> Applied On</th>
                            <th><i class="fas fa-play-circle"></i> Start Date</th>
                            <th><i class="fas fa-stop-circle"></i> End Date</th>
                            <th><i class="fas fa-clock"></i> Total Days</th>
                            <th><i class="fas fa-adjust"></i> Half/Full Day</th>
                            <th><i class="fas fa-comment"></i> Leave Reason</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0; ?>
                        @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                            @foreach ($leaves as $key => $leave)
                                <tr>
                                    <td><span class="employee-id-tag"># {{ ++$i }}</span></td>
                                    <td><span class="leave-type-tag">{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}</span></td>
                                    <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                    <td>{{ \Auth::user()->dateFormat($leave->start_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->start_time }}</small> @endif</td>
                                    <td>{{ \Auth::user()->dateFormat($leave->end_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->end_time }}</small> @endif</td>
                                    <td><strong>{{ $leave->total_leave_days }}</strong></td>
                                    <td>{{ ucwords($leave->leavetype) }} @if($leave->day_segment) <br><small>({{ ucwords($leave->day_segment) }})</small> @endif</td>
                                    <td>{{ \Illuminate\Support\Str::limit($leave->leave_reason, 25) }}</td>
                                    <td>
                                        @if ($leave->status == 'Pending')
                                            <span class="status-badge pending">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @elseif($leave->status == 'Approve')
                                            <span class="status-badge approved">
                                                <i class="fas fa-check"></i> Approved
                                            </span>
                                        @else
                                            <a href="#" data-url="{{ URL::to('leave/' . $leave->id . '/reason') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('See Reason') }}">
                                                <span class="status-badge rejected">
                                                    <i class="fas fa-times"></i> Rejected
                                                </span>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if (\Auth::user()->type == 'employee' && (\Auth::user()->employee->is_team_leader == 0 ||  \Auth::user()->employee->id == $leave->employee_id))
                                                @if ($leave->status == 'Pending')
                                                    @can('Edit Leave')
                                                        <button class="action-btn btn-edit" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endcan
                                                @endif
                                            @else
                                                <button class="action-btn btn-action" data-url="{{ URL::to('leave/' . $leave->id . '/action') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Leave Action') }}">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                                @can('Edit Leave')
                                                    @if ($leave->status == 'Pending')
                                                        <button class="action-btn btn-edit" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                @endcan
                                            @endif
                                            @if ($leave->status == 'Pending')
                                                @can('Delete Leave')
                                                    <button class="action-btn btn-delete" data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-{{ $leave->id }}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('leave.destroy', $leave->id) }}" id="delete-form-{{ $leave->id }}" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @foreach ($leaves as $employee)
                                @php
                                    $employeeLeaves = $employee->employeeLeaves;
                                @endphp
                                
                                @if(!count($employeeLeaves)) @continue @endif
                            
                                <tr class="employee-header-row" style="top: 30px;">
                                    <td id="ColspanTD" colspan="10">
                                        <div class="employee-name-section">
                                            <div class="employee-info text-dark">
                                                <div class="employee-avatar">
                                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $employee->name }}</strong>
                                                    <br>
                                                    <small>{{ $employee->email ?? 'No email' }}</small>
                                                </div>
                                            </div>
                                            <div class="employee-controls text-dark">
                                                <span class="leave-counter">
                                                    <i class="fas fa-calendar-check"></i> {{ count($employeeLeaves) }} Leaves
                                                </span>
                                                @if(count($employeeLeaves) > 5)
                                                    <button class="view-more-btn text-dark" onclick="toggleEmployeeLeaves('{{ $employee->id }}')">
                                                        <span id="toggle-text-{{ $employee->id }}">View All</span>
                                                        <i class="fas fa-chevron-down" id="toggle-icon-{{ $employee->id }}"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            
                                @foreach ($employeeLeaves as $index => $leave)
                                    <tr class="{{ $index >= 5 ? 'collapsible-rows employee-' . $employee->id : '' }}">
                                        <td><span class="employee-id-tag"># {{ ++$i }}</span></td>
                                        <td><span class="leave-type-tag">{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}</span></td>
                                        <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($leave->start_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->start_time }}</small> @endif</td>
                                        <td>{{ \Auth::user()->dateFormat($leave->end_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->end_time }}</small> @endif</td>
                                        <td><strong>{{ $leave->total_leave_days }}</strong></td>
                                        <td>{{ ucwords($leave->leavetype) }} @if($leave->day_segment) <br><small>({{ ucwords($leave->day_segment) }})</small> @endif</td>
                                        <td>{{ \Illuminate\Support\Str::limit($leave->leave_reason, 25) }}</td>
                                        <td>
                                            @if ($leave->status == 'Pending')
                                                <span class="status-badge pending">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @elseif($leave->status == 'Approve')
                                                <span class="status-badge approved">
                                                    <i class="fas fa-check"></i> Approved
                                                </span>
                                            @else
                                                <a href="#" data-url="{{ URL::to('leave/' . $leave->id . '/reason') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('See Reason') }}">
                                                    <span class="status-badge rejected">
                                                        <i class="fas fa-times"></i> Rejected
                                                    </span>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                @if (\Auth::user()->type == 'employee' && (\Auth::user()->employee->is_team_leader == 0 || \Auth::user()->employee->id == $leave->employee_id))
                                                    @if ($leave->status == 'Pending')
                                                        @can('Edit Leave')
                                                            <button class="action-btn btn-edit" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endcan
                                                    @endif
                                                @else
                                                    <button class="action-btn btn-action" data-url="{{ URL::to('leave/' . $leave->id . '/action') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Leave Action') }}">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                    @can('Edit Leave')
                                                        @if ($leave->status == 'Pending')
                                                            <button class="action-btn btn-edit" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                @endif
                                                @if ($leave->status == 'Pending')
                                                    @can('Delete Leave')
                                                        <button class="action-btn btn-delete" data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-{{ $leave->id }}').submit();">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <form method="POST" action="{{ route('leave.destroy', $leave->id) }}" id="delete-form-{{ $leave->id }}" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
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
        function removeModel() {
            document.getElementById('commonModalCustom').remove();
        }

        function toggleEmployeeLeaves(employeeId) {
            const collapsibleRows = document.querySelectorAll('.employee-' + employeeId);
            const toggleText = document.getElementById('toggle-text-' + employeeId);
            const toggleIcon = document.getElementById('toggle-icon-' + employeeId);
            
            collapsibleRows.forEach(row => {
                if (row.classList.contains('show')) {
                    row.classList.remove('show');
                    toggleText.textContent = 'View All';
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                } else {
                    row.classList.add('show');
                    toggleText.textContent = 'Show Less';
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                }
            });
        }
        
        $(document).on('change', '#employee_id', function() {
            var employeeId = $(this).val();
            if (employeeId) {
                $.ajax({
                    url: "{{ url('/leave/get-paid-leave-balance') }}" + "/" + employeeId,
                    type: 'GET',
                    success: function(response) {
                        if (response.leavetypes) {
                            $('#leave_type_id').html('');
                            $.each(response.leavetypes, function(index, leave) {
                                if (leave.title == "Paternity Leaves" && response.employee.gender == 'Female') return true;
                                if (leave.title == "Maternity Leaves" && response.employee.gender == 'Male') return true;
                                var optionText = leave.title;
                                if (leave.title === "Paid Leave") {
                                    optionText += ' (' + leave.days + ')';
                                } else {
                                    optionText += ' (' + leave.days + ')';
                                }
        
                                var isBirthdayLeave = (leave.title === "Birthday Leave" || leave.id === 8);
                                var isSameMonthAsDOB = false;
        
                                if (isBirthdayLeave) {
                                    var dob = new Date(response.employee.dob);
                                    var dobMonth = dob.getMonth() + 1;
                                    var currentMonth = new Date().getMonth() + 1;
                                    isSameMonthAsDOB = (dobMonth === currentMonth);
                                }
    
                                var option = $('<option>', {
                                    value: leave.id,
                                    text: optionText,
                                    'data-title': leave.title,
                                    disabled: leave.days === 0 || (isBirthdayLeave && !isSameMonthAsDOB)
                                });
                                $('#leave_type_id').append(option);
                            });
                            halfDayLeave();
                        }
                    },
                    error: function() {
                        console.log('Error fetching paid leave balance');
                    }
                });
            }
        });

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable().destroy();
            }
            
            var table = $('#datatable').DataTable({
                "columnDefs": [
                    { "targets": [3], "type": "date" }
                ],
                "order": [[3, 'desc']],
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search leaves...",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "first": "First",
                        "last": "Last", 
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "dom": '<"top"fl>rt<"bottom"ip><"clear">',
                "searching": false, // Disable DataTables search since we have custom
                "rowCallback": function(row, data, index) {
                    // Custom row numbering handled in PHP
                }
            });
        
            table.order([3, 'desc']).draw();
            
            // Custom search functionality
            $('#search-input').on('keyup', function() {
                var searchTerm = this.value.toLowerCase();
                
                $('#datatable tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    if (rowText.indexOf(searchTerm) === -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });
            
            // Custom entries per page
            $('#entries-per-page').on('change', function() {
                table.page.len(this.value).draw();
            });

            // Enhanced button interactions
            $(document).on('click', '.action-btn', function() {
                var $btn = $(this);
                var originalContent = $btn.html();
                $btn.html('<div class="loading-spinner"></div>');
                
                setTimeout(function() {
                    $btn.html(originalContent);
                }, 1000);
            });
        });
    </script>
@endpush