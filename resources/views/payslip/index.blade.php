@extends('layouts.admin')
@section('page-title')
    {{ __('Payslip') }}
@endsection

@push('css-page')
<style>
    :root {
        --primary: #2563eb;
        --secondary: #3b82f6;
        --accent: #60a5fa;
        --info: #93c5fd;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        --shadow-md: 0 8px 25px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.1);
        --text-primary: #2d3748;
        --text-secondary: #6b7280;
        --border-radius: 20px;
        --border-radius-sm: 12px;
    }

    body {
        background: linear-gradient(135deg, #eef2f6 0%, #d1d9e6 100%);
        min-height: 100vh;
    }

    .content-wrapper {
        background: transparent;
        padding: 0;
    }

    /* Compact container */
    .container-fluid {
        margin: 0 auto;
        padding: 0 16px;
    }

    /* Premium Header */
    .page-header-premium {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: var(--border-radius);
        padding: 24px 32px;
        margin-bottom: 32px;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }
    .page-header-premium::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.15), transparent 70%);
        animation: rotateBg 20s linear infinite;
    }
    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 2;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        backdrop-filter: blur(10px);
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }

    .header-text h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-text p {
        color: rgba(255, 255, 255, 0.85);
        margin: 8px 0 0 0;
        font-size: 1.1rem;
        font-weight: 500;
    }

    .header-stats {
        display: flex;
        gap: 24px;
        align-items: center;
    }

    .stat-item {
        text-align: center;
        color: white;
        background: rgba(255, 255, 255, 0.1);
        padding: 16px 20px;
        border-radius: 16px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.2);
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.8rem;
        opacity: 0.9;
        margin: 6px 0 0 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Premium Generation Section */
    .generation-section {
        background: #fff;
        border-radius: var(--border-radius);
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .generation-section::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--secondary));
    }

    .generation-section h4 {
        color: var(--text-primary);
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.4rem;
    }

    .filter-group {
        margin-bottom: 20px;
    }

    .filter-group label {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 8px;
        display: block;
    }

    .filter-group select,
    .form-control {
        border: 2px solid #e5e7eb;
        border-radius: var(--border-radius-sm);
        padding: 12px 16px;
        color: var(--text-primary);
        background: #fff;
        font-weight: 500;
        transition: all 0.3s ease;
        width: 100%;
        font-size: 0.95rem;
    }

    .filter-group select:focus,
    .form-control:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        transform: translateY(-2px);
    }

    /* Premium Buttons */
    .btn-generate {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        color: white;
        padding: 16px 32px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 8px;
    }
    .btn-generate::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    .btn-generate:hover::before {
        left: 100%;
    }
    .btn-generate:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        color: white;
        text-decoration: none;
    }

    .bulk-payment-btn {
        background: linear-gradient(135deg, var(--warning), #fbbf24);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: var(--shadow);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bulk-payment-btn:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    /* Premium Table Container */
    .employee-list-card {
        background: #fff;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        position: relative;
        margin-bottom: 24px;
    }

    .employee-list-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--secondary));
    }

    .employee-list-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 24px 32px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .employee-list-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-count-badge {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: 16px;
        box-shadow: var(--shadow);
        backdrop-filter: blur(10px);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .filter-section {
        display: flex;
        align-items: end;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-section .filter-group {
        margin-bottom: 0;
        min-width: 140px;
    }

    .filter-section .filter-group label {
        font-size: 0.9rem;
        margin-bottom: 6px;
    }

    .filter-section .filter-group select {
        padding: 8px 12px;
        font-size: 0.9rem;
    }

    /* Enhanced Table */
    .table-container {
        position: relative;
        overflow: hidden;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
    }

    .table-responsive {
        background: #fff;
    }

    .custom-table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        background: #fff;
    }

    .custom-table thead th {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 20px 16px;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        position: sticky;
        top: 0;
        z-index: 100;
        text-align: center;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
    }

    .custom-table thead th:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 30%;
        bottom: 30%;
        width: 1px;
        background: rgba(255, 255, 255, 0.2);
    }

    .custom-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        background: #fff;
    }

    .custom-table tbody tr:nth-child(even) {
        background: rgba(37, 99, 235, 0.02);
    }

    .custom-table tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .custom-table tbody td {
        padding: 20px 16px;
        vertical-align: middle;
        border: none;
        color: var(--text-primary);
        font-size: 0.9rem;
        font-weight: 500;
        text-align: center;
    }

    /* Enhanced Status Badges */
    .status-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        letter-spacing: 0.5px;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .status-badge.paid {
        background: linear-gradient(135deg, var(--success), #34d399);
        color: white;
    }

    .status-badge.unpaid {
        background: linear-gradient(135deg, var(--danger), #f87171);
        color: white;
    }

    /* Enhanced Action Buttons */
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 5px 12px !important;
        border-radius: 25px!important;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        letter-spacing: 0.5px;
        cursor: pointer;
        text-decoration: none;
        color: white;
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
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.4s ease;
    }

    .action-btn:hover::before {
        left: 100%;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        text-decoration: none;
        color: white;
    }

    .action-btn.btn-view {
        background: linear-gradient(135deg, #6b7280, #4b5563);
    }

    .action-btn.btn-payslip {
        background: linear-gradient(135deg, var(--warning), #fbbf24);
    }

    .action-btn.btn-paid {
        background: linear-gradient(135deg, var(--success), #34d399);
    }

    .action-btn.btn-edit {
        background: linear-gradient(135deg, var(--info), #60a5fa);
    }

    .action-btn.btn-delete {
        background: linear-gradient(135deg, var(--danger), #f87171);
    }

    /* Employee ID Badge */
    .employee-id-badge {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-block;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
    }

    .employee-id-badge:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* Salary Display */
    .salary-display {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.95rem;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        border: 1px solid #e5e7eb;
    }

    /* DataTable Customization */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        margin: 5px !important;
        padding: 0 20px 0 10px;
        transition: all 0.3s ease;
    }

    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px;
        margin: 0 4px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, var(--primary), var(--secondary)) !important;
        border-color: var(--primary) !important;
        color: white !important;
        box-shadow: var(--shadow);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: linear-gradient(135deg, var(--primary), var(--secondary)) !important;
        border-color: var(--primary) !important;
        color: white !important;
        transform: translateY(-2px);
    }

    .dataTables_info {
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Enhanced Scrollbar */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--secondary), var(--primary));
    }

    /* Bootstrap Badge Override */
    .badge {
        padding: 8px 16px !important;
        border-radius: 25px !important;
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: var(--shadow) !important;
        transition: all 0.3s ease !important;
    }

    .badge-success {
        background: linear-gradient(135deg, var(--success), #34d399) !important;
        color: white !important;
    }

    .badge-danger {
        background: linear-gradient(135deg, var(--danger), #f87171) !important;
        color: white !important;
    }

    .badge:hover {
        transform: translateY(-2px) !important;
        box-shadow: var(--shadow-md) !important;
    }

    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    .dataTables_wrapper .dataTables_length{
        height: auto !important;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        
        .header-stats {
            justify-content: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .generation-section {
            padding: 20px;
        }

        .employee-list-header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
            padding: 20px;
        }
        
        .filter-section {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-section .filter-group {
            min-width: auto;
        }

        .table-container {
            max-height: 450px;
            overflow: hidden;
        }
        
        .custom-table {
            font-size: 0.8rem;
        }
        
        .custom-table thead th,
        .custom-table tbody td {
            padding: 12px 8px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 4px;
        }

        .action-btn {
            width: 100%;
            min-width: auto;
        }
    }

    /* Animations */
    .fade-in {
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .slide-in {
        animation: slideIn 0.6s ease;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Custom padding for DataTable */
    table.dataTable tbody td {
        padding: 20px 16px !important;
    }
</style>
@endpush

@section('action-button')
@endsection

@section('content')
<div class="container-fluid">
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="header-text">
                    <h1>
                        💰 {{ __('Payslip Management') }}
                    </h1>
                    <p>{{ __('Comprehensive payroll tracking and management dashboard for enhanced employee compensation') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number" id="total-employees">--</p>
                    <p class="stat-label">{{ __('Total') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number" id="paid-count">--</p>
                    <p class="stat-label">{{ __('Paid') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number" id="unpaid-count">--</p>
                    <p class="stat-label">{{ __('Pending') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Generation Section --}}
    @can('Create Pay Slip')
    <div class="generation-section fade-in" style="animation-delay: 0.2s">
        <h4><i class="fas fa-cogs"></i> {{ __('Generate Monthly Payslips') }}</h4>
        <form action="{{ route('payslip.store') }}" method="POST" id="payslip_form">
            @csrf
            <div class="row align-items-end">
                <div class="col-md-3">
                    <div class="filter-group">
                        <label for="month">{{ __('Select Month') }}</label>
                        <select name="month" class="form-control month select2">
                            @foreach ($month as $key => $value)
                                <option value="{{ $key }}" {{ $key == date('m') ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="filter-group">
                        <label for="year">{{ __('Select Year') }}</label>
                        <select name="year" class="form-control year select2">
                            @foreach ($year as $key => $value)
                                <option value="{{ $key }}" {{ $value == date('Y') ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 30px 0px;">
                    <button type="button" class="btn-generate" onclick="document.getElementById('payslip_form').submit(); return false;">
                        <i class="fas fa-plus-circle"></i> {{ __('Generate Payslips') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endcan

    {{-- Enhanced Employee List Card --}}
    <div class="employee-list-card fade-in" style="animation-delay: 0.4s">
        <div class="employee-list-header">
            <h3 class="employee-list-title">
                <i class="fas fa-users"></i>
                {{ __('Employee Payslip Records') }}
                <span class="employee-count-badge" id="employee-badge">
                    {{ __('Loading...') }}
                </span>
            </h3>
            <div class="filter-section">
                <div class="filter-group">
                    <label>{{ __('Select Month') }}</label>
                    <select class="form-control month_date select2" name="month" tabindex="-1">
                        <option value="--">--</option>
                        @foreach ($month as $k => $mon)
                            <option value="{{ $k }}">{{ $mon }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>{{ __('Select Year') }}</label>
                    <select name="year" class="form-control year_date select2">
                        @foreach ($year as $key => $value)
                            <option value="{{ $key }}" {{ $value == date('Y') ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                @can('Create Pay Slip')
                <div class="filter-group" style="padding: 15px 0px;">
                    <button class="bulk-payment-btn" id="bulk_payment">
                        <i class="fas fa-credit-card"></i> {{ __('Bulk Payment') }}
                    </button>
                </div>
                @endcan
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table custom-table" id="dataTable1" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="display: none;"><i class="fas fa-hashtag"></i> {{ __('Id') }}</th>
                            <th><i class="fas fa-id-badge"></i> {{ __('Employee ID') }}</th>
                            <th><i class="fas fa-user"></i> {{ __('Name') }}</th>
                            <th><i class="fas fa-rupee-sign"></i> {{ __('Salary') }}</th>
                            <th><i class="fas fa-money-bill-wave"></i> {{ __('Net Salary') }}</th>
                            <th><i class="fas fa-info-circle"></i> {{ __('Status') }}</th>
                            <th><i class="fas fa-cogs"></i> {{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#dataTable1').DataTable({
            "scrollY": "400px",
            "scrollCollapse": true,
            "paging": false,
            "aoColumnDefs": [
                {
                    "aTargets": [3, 4],
                    "mData": null,
                    "mRender": function(data, type, full, meta) {
                        var value = data[3];
                        console.log(value);
                        if (value && value !== 'N/A' && value !== null && value !== '') {
                            // var formattedValue = parseFloat(value).toLocaleString('en-IN');
                            return '<span class="salary-display">' + value + '</span>';
                        }
                        return '<span class="salary-display">--</span>';
                    }
                },
                {
                    "aTargets": [5],
                    "mData": null,
                    "mRender": function(data, type, full) {
                        var month = $(".month_date").val();
                        var year = $(".year_date").val();
                        var datePicker = year + '-' + month;
                        var id = data[0];

                        if (data[5] == 'Paid')
                            return '<span class="badge badge-success"><i class="fas fa-check"></i> ' + data[5] + '</span>';
                        else
                            return '<span class="badge badge-danger"><i class="fas fa-times"></i> ' + data[5] + '</span>';
                    }
                },
                {
                    "aTargets": [6],
                    "mData": null,
                    "mRender": function(data, type, full) {
                        var month = $(".month_date").val();
                        var year = $(".year_date").val();
                        var datePicker = year + '-' + month;
                        var id = data[0];
                        var payslip_id = data[6];

                        var buttons = '<div class="action-buttons">';

                        // View Button
                        if (data[6] != 0) {
                            buttons += '<a href="#" data-url="{{ url('payslip/showemployee/') }}/' + payslip_id + 
                                      '" data-ajax-popup="true" class="action-btn btn-view" data-title="{{ __('View Employee Detail') }}"><i class="fas fa-eye"></i> View</a>';
                        }

                        // Payslip Button
                        if (data[6] != 0) {
                            buttons += '<a href="#" data-url="{{ url('payslip/pdf/') }}/' + id + '/' + datePicker + 
                                      '" data-size="md-pdf" data-ajax-popup="true" class="action-btn btn-payslip" data-title="{{ __('Employee Payslip') }}"><i class="fas fa-file-pdf"></i> Payslip</a>';
                        }

                        // Click to Paid Button
                        if (data[5] == "UnPaid" && data[6] != 0) {
                            buttons += '<a href="{{ url('payslip/paysalary/') }}/' + id + '/' + datePicker + 
                                      '" class="action-btn btn-paid"><i class="fas fa-credit-card"></i> Pay Now</a>';
                        }

                        // Edit Button
                        if (data[6] != 0 && data[5] == "UnPaid") {
                            buttons += '<a href="#" data-url="{{ url('payslip/editemployee/') }}/' + payslip_id + 
                                      '" data-ajax-popup="true" class="action-btn btn-edit" data-title="{{ __('Edit Employee salary') }}"><i class="fas fa-edit"></i> Edit</a>';
                        }

                        // Delete Button
                        @if (\Auth::user()->type != 'employee')
                        if (data[6] != 0) {
                            var url = '{{ route('payslip.delete', ':id') }}';
                            url = url.replace(':id', payslip_id);
                            buttons += '<a href="#" data-url="' + url + '" class="payslip_delete action-btn btn-delete"><i class="fas fa-trash"></i> Delete</a>';
                        }
                        @endif

                        buttons += '</div>';
                        return buttons;
                    }
                },
                {
                    "aTargets": [1],
                    "mData": null,
                    "mRender": function(data, type, full) {
                        return '<span class="employee-id-badge">' + data[1] + '</span>';
                    }
                },
                {
                    "aTargets": [3, 4],
                    "mData": null,
                    "mRender": function(data, type, full, meta) {
                        var value = data[meta.col];
                        if (value && value !== 'N/A') {
                            var formattedValue = parseFloat(value).toLocaleString('en-IN');
                            return '<span class="salary-display">₹' + formattedValue + '</span>';
                        }
                        return '<span class="salary-display">N/A</span>';
                    }
                }
            ],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {
                "search": "",
                "searchPlaceholder": "{{ __('Search payslips...') }}",
                "lengthMenu": "{{ __('Show _MENU_ entries') }}",
                "info": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                "infoEmpty": "{{ __('No entries available') }}",
                "infoFiltered": "({{ __('filtered from _MAX_ total entries') }})",
                "paginate": {
                    "first": "{{ __('First') }}",
                    "last": "{{ __('Last') }}", 
                    "next": "{{ __('Next') }}",
                    "previous": "{{ __('Previous') }}"
                }
            },
            "dom": '<"top"fl>rt<"bottom"ip><"clear">',
            "searching": false,
            "info": false,
            "drawCallback": function() {
                // Add staggered animation to rows
                $('#dataTable1 tbody tr').each(function(index) {
                    $(this).css('animation-delay', (index * 0.05) + 's');
                    $(this).addClass('slide-in');
                });
            }
        });

        function updateEmployeeCount(data) {
            var total = data.length;
            var paid = data.filter(function(item) { return item[5] === 'Paid'; }).length;
            var unpaid = total - paid;
            
            $('#employee-badge').text(total + ' {{ __('Employees') }}');
            $('#total-employees').text(total);
            $('#paid-count').text(paid);
            $('#unpaid-count').text(unpaid);
        }

        function updateHeaderStats(data) {
            var total = data.length;
            var paid = data.filter(function(item) { return item[5] === 'Paid'; }).length;
            var unpaid = total - paid;
            
            $('#total-employees').text(total);
            $('#paid-count').text(paid);
            $('#unpaid-count').text(unpaid);
        }

        function callback() {
            var month = $(".month_date").val();
            var year = $(".year_date").val();
            var datePicker = year + '-' + month;

            if (month === '--' || year === '') {
                table.rows().remove().draw();
                updateEmployeeCount([]);
                updateHeaderStats([]);
                return;
            }

            $.ajax({
                url: '{{ route('payslip.search_json') }}',
                type: 'POST',
                data: {
                    "datePicker": datePicker,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    table.rows().remove().draw();
                    table.rows.add(data).draw();
                    table.column(0).visible(false);
                    updateEmployeeCount(data);
                    updateHeaderStats(data);

                    if (!(data.length)) {
                        show_toastr('error', '{{ __('Employee payslip not found! Please generate first.') }}', 'error');
                    }
                },
                error: function(data) {
                    console.error('Error fetching payslip data');
                    show_toastr('error', '{{ __('Error fetching payslip data') }}', 'error');
                }
            });
        }

        $(document).on("change", ".month_date,.year_date", function() {
            callback();
        });

        // Bulk payment functionality
        $(document).on('click', '#bulk_payment', function() {
            var month = $(".month_date").val();
            var year = $(".year_date").val();
            
            if (month === '--' || year === '') {
                show_toastr('Error', '{{ __('Please select month and year first.') }}');
                return;
            }
            
            var datePicker = year + '-' + month;
            var title = '{{ __('Bulk Payment') }}';
            var size = 'md';
            var url = 'payslip/bulk_pay_create/' + datePicker;

            $("#commonModal .modal-title").html(title);
            $("#commonModal .modal-dialog").addClass('modal-' + size);
            
            $.ajax({
                url: url,
                success: function(data) {
                    if (data.length) {
                        $('#commonModal .modal-body').html(data);
                        $("#commonModal").modal('show');
                    } else {
                        show_toastr('Error', '{{ __('Permission denied.') }}');
                        $("#commonModal").modal('hide');
                    }
                },
                error: function(data) {
                    data = data.responseJSON;
                    show_toastr('Error', data.error || '{{ __('Error occurred') }}');
                }
            });
        });

        // Delete functionality
        $(document).on("click", ".payslip_delete", function() {
            var confirmation = confirm("{{ __('Are you sure you want to delete this payslip?') }}");
            var url = $(this).data('url');
            
            if (confirmation) {
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "JSON",
                    success: function(data) {
                        show_toastr('Success', '{{ __('Payslip successfully deleted') }}', 'success');
                        setTimeout(function() {
                            callback(); // Refresh the table
                        }, 800);
                    },
                    error: function(data) {
                        show_toastr('Error', '{{ __('Failed to delete payslip') }}');
                    }
                });
            }
        });

        // Enhanced button interactions with loading state
        $(document).on('click', '.action-btn', function(e) {
            if (!$(this).hasClass('payslip_delete')) {
                var $btn = $(this);
                var originalContent = $btn.html();
                $btn.html('<div class="loading-spinner"></div>');
                
                setTimeout(function() {
                    $btn.html(originalContent);
                }, 1500);
            }
        });

        // Initialize with empty data
        updateEmployeeCount([]);
        updateHeaderStats([]);

        // Enhanced hover effects for action buttons
        $(document).on('mouseenter', '.action-btn', function() {
            $(this).css('transform', 'translateY(-2px) scale(1.05)');
        });

        $(document).on('mouseleave', '.action-btn', function() {
            $(this).css('transform', 'translateY(0) scale(1)');
        });

        // Add ripple effect to buttons
        $(document).on('click', '.btn-generate, .bulk-payment-btn, .action-btn', function(e) {
            var $btn = $(this);
            var ripple = $('<span class="ripple"></span>');
            var btnOffset = $btn.offset();
            var x = e.pageX - btnOffset.left;
            var y = e.pageY - btnOffset.top;
            
            ripple.css({
                left: x + 'px',
                top: y + 'px'
            });
            
            $btn.append(ripple);
            
            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    });
</script>

<style>
/* Ripple effect */
.ripple {
    position: absolute;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>
@endpush