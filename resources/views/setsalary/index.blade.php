@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Employee Salary') }}
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
        margin-bottom: 30px;
        color: white;
        box-shadow: rgba(102, 126, 234, 0.3) 0px 20px 40px;
        position: relative;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(59, 130, 246, 0.95) 50%, rgba(96, 165, 250, 0.95) 100%);
        border-radius: 20px;
        padding: 30px 40px;
        overflow: hidden;
    }
    .page-header-premium::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        pointer-events: none;
        background: conic-gradient(transparent 0deg, rgba(255, 255, 255, 0.1) 60deg, transparent 120deg, rgba(255, 255, 255, 0.05) 180deg, transparent 240deg, rgba(255, 255, 255, 0.1) 300deg, transparent 360deg);
        animation: 25s linear 0s infinite normal none running rotateBg;
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

    /* Premium Statistics Cards */
    .premium-stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 28px;
        box-shadow: var(--shadow);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        height: 100%;
    }

    .premium-stat-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--secondary));
    }

    .premium-stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .premium-stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .stat-card-content {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-icon::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.2), transparent 60%);
        animation: shimmer 3s linear infinite;
    }

    @keyframes shimmer {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .icon-total { background: linear-gradient(135deg, var(--primary), var(--secondary)); }
    .icon-high { background: linear-gradient(135deg, var(--danger), #f87171); }
    .icon-medium { background: linear-gradient(135deg, var(--info), #60a5fa); }
    .icon-low { background: linear-gradient(135deg, var(--success), #34d399); }

    .stat-details h3 {
        font-size: 2.2rem;
        font-weight: 800;
        margin: 0 0 8px 0;
        color: var(--text-primary);
        line-height: 1;
    }

    .stat-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-subtitle {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin: 4px 0 0 0;
        opacity: 0.8;
    }

    /* Premium Table Container */
    .premium-table-container {
        background: #fff;
        border-radius: 20px;
        box-shadow: var(--shadow);
        overflow: hidden;
        position: relative;
        margin-bottom: 24px;
    }

    .premium-table-container::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--secondary));
    }

    .table-header-premium {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        padding: 24px 32px;
        position: relative;
        overflow: hidden;
    }

    .table-header-premium::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.1), transparent 70%);
        animation: rotateBg 20s linear infinite;
    }

    .table-title-premium {
        font-size: 1.4rem;
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 2;
    }

    .employee-count-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: 16px;
        backdrop-filter: blur(10px);
    }

    /* Search and Controls */
    .controls-container {
        padding: 24px 32px;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-bottom: 1px solid #e5e7eb;
    }

    .search-input-container {
        position: relative;
        max-width: 300px;
    }

    .search-input {
        border: 2px solid #e5e7eb;
        border-radius: 50px;
        padding: 12px 20px 12px 48px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fff;
        width: 100%;
    }

    .search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
        transform: translateY(-2px);
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        font-size: 1rem;
    }

    .records-select {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 8px 16px;
        font-size: 0.9rem;
        background: #fff;
        transition: all 0.3s ease;
    }

    .records-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    /* Enhanced Table */
    .premium-table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .premium-table thead th {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: none;
        color: var(--text-primary);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
        position: relative;
    }

    .premium-table thead th:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 30%;
        bottom: 30%;
        width: 1px;
        background: #e5e7eb;
    }

    .premium-table tbody tr {
        border: none;
        transition: all 0.3s ease;
        background: #fff;
    }

    .premium-table tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .premium-table tbody tr:nth-child(even) {
        background: rgba(37, 99, 235, 0.02);
    }

    .premium-table tbody tr:nth-child(even):hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    }

    .premium-table tbody td {
        padding: 20px 24px;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.95rem;
    }

    /* Employee Info Styling */
    .employee-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .employee-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        position: relative;
        overflow: hidden;
    }

    .employee-avatar::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.2), transparent 60%);
        animation: shimmer 3s linear infinite;
    }

    .employee-details h6 {
        margin: 0 0 4px 0;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1rem;
    }

    .employee-details small {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    /* Employee ID Badge */
    .employee-id-badge {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .employee-id-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .employee-id-badge:hover::before {
        left: 100%;
    }

    .employee-id-badge:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        text-decoration: none;
    }

    /* Salary Amount */
    .salary-amount {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1.1rem;
    }

    /* Payroll Type Badge */
    .payroll-type-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: var(--text-primary);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .payroll-type-badge:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    /* Action Button */
    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, var(--success), #34d399);
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
        transition: left 0.5s ease;
    }

    .action-btn:hover::before {
        left: 100%;
    }

    .action-btn:hover {
        transform: scale(1.15);
        color: white;
        text-decoration: none;
        box-shadow: var(--shadow);
    }

    /* DataTable Customization */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 12px;
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

    /* Responsive Design */
    @media (max-width: 1200px) {
        .container-fluid {
            padding: 0 12px;
        }
        
        .page-header-premium {
            padding: 25px 30px;
        }
        
        .header-text h1 {
            font-size: 1.8rem;
        }
        
        .stat-item {
            padding: 14px 18px;
        }
        
        .stat-number {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 992px) {
        .page-header-premium {
            padding: 20px 25px;
        }
        
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
        
        .stat-item {
            min-width: 120px;
        }
        
        .premium-stat-card {
            margin-bottom: 20px;
        }
        
        .stat-card-content {
            gap: 16px;
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            font-size: 1.4rem;
        }
        
        .stat-details h3 {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 8px;
        }
        
        .page-header-premium {
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .header-icon {
            width: 56px;
            height: 56px;
            font-size: 1.6rem;
        }
        
        .header-text h1 {
            font-size: 1.6rem;
        }
        
        .header-text p {
            font-size: 1rem;
        }
        
        .header-stats {
            gap: 12px;
        }
        
        .stat-item {
            padding: 12px 16px;
            min-width: 100px;
        }
        
        .stat-number {
            font-size: 1.4rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
        }

        .premium-stat-card {
            padding: 20px;
            margin-bottom: 16px;
        }
        
        .stat-card-content {
            flex-direction: column;
            text-align: center;
            gap: 12px;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            font-size: 1.2rem;
            margin: 0 auto;
        }
        
        .stat-details h3 {
            font-size: 1.8rem;
        }

        .controls-container {
            padding: 16px;
        }

        .controls-container .row {
            flex-direction: column;
            gap: 16px;
        }
        
        .controls-container .col-md-6 {
            width: 100%;
        }
        
        .d-flex.justify-content-end {
            justify-content: center !important;
        }

        .search-input-container {
            max-width: 100%;
        }
        
        .search-input {
            padding: 10px 16px 10px 40px;
        }

        .premium-table-container {
            margin-bottom: 16px;
        }
        
        .table-header-premium {
            padding: 20px;
        }
        
        .table-title-premium {
            font-size: 1.2rem;
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }
        
        .employee-count-badge {
            margin-left: 0;
        }

        .premium-table {
            font-size: 0.85rem;
        }
        
        .premium-table thead th,
        .premium-table tbody td {
            padding: 12px 8px;
        }
        
        .premium-table thead th {
            font-size: 0.75rem;
        }

        .employee-info {
            gap: 10px;
        }

        .employee-avatar {
            width: 36px;
            height: 36px;
            font-size: 0.8rem;
        }
        
        .employee-details h6 {
            font-size: 0.9rem;
        }
        
        .employee-details small {
            font-size: 0.75rem;
        }
        
        .employee-id-badge {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
        
        .payroll-type-badge {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
        
        .salary-amount {
            font-size: 1rem;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .page-header-premium {
            padding: 16px;
        }
        
        .header-left {
            flex-direction: column;
            gap: 12px;
        }
        
        .header-icon {
            width: 48px;
            height: 48px;
            font-size: 1.4rem;
        }
        
        .header-text h1 {
            font-size: 1.4rem;
        }
        
        .header-text p {
            font-size: 0.9rem;
        }
        
        .stat-item {
            padding: 10px 12px;
            min-width: 90px;
        }
        
        .stat-number {
            font-size: 1.2rem;
        }

        .premium-stat-card {
            padding: 16px;
        }
        
        .stat-details h3 {
            font-size: 1.6rem;
        }
        
        .stat-title {
            font-size: 0.9rem;
        }

        .controls-container {
            padding: 12px;
        }
        
        .records-select {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .table-header-premium {
            padding: 16px;
        }
        
        .table-title-premium {
            font-size: 1.1rem;
        }

        /* Hide less important columns on very small screens */
        .premium-table th:nth-child(3),
        .premium-table td:nth-child(3) {
            display: none;
        }
        
        .premium-table thead th,
        .premium-table tbody td {
            padding: 10px 6px;
        }
        
        .employee-info {
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }
        
        .employee-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
            margin: 0 auto;
        }
        
        .employee-details h6 {
            font-size: 0.85rem;
            margin-bottom: 2px;
        }
        
        .employee-details small {
            font-size: 0.7rem;
        }
        
        .salary-amount {
            font-size: 0.9rem;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 400px) {
        .container-fluid {
            padding: 0 4px;
        }
        
        .page-header-premium {
            padding: 12px;
            margin-bottom: 16px;
        }
        
        .header-text h1 {
            font-size: 1.2rem;
        }
        
        .header-text p {
            font-size: 0.8rem;
        }
        
        .stat-item {
            padding: 8px 10px;
            min-width: 80px;
        }
        
        .stat-number {
            font-size: 1.1rem;
        }
        
        .stat-label {
            font-size: 0.7rem;
        }

        .premium-stat-card {
            padding: 12px;
        }
        
        .stat-details h3 {
            font-size: 1.4rem;
        }

        .table-title-premium {
            font-size: 1rem;
        }
        
        .employee-count-badge {
            padding: 6px 10px;
            font-size: 0.7rem;
        }

        /* Stack table cells vertically on very small screens */
        .premium-table,
        .premium-table thead,
        .premium-table tbody,
        .premium-table th,
        .premium-table td,
        .premium-table tr {
            display: block;
        }
        
        .premium-table thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        
        .premium-table tr {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 12px;
            padding: 12px;
            background: #fff;
        }
        
        .premium-table td {
            border: none !important;
            position: relative;
            padding: 8px 0 !important;
            text-align: left !important;
        }
        
        .premium-table td:before {
            content: attr(data-label) ": ";
            font-weight: 600;
            color: var(--text-secondary);
            display: inline-block;
            width: 100px;
            font-size: 0.8rem;
        }
        
        .premium-table td:last-child {
            text-align: center !important;
            padding-top: 12px !important;
        }
        
        .premium-table td:last-child:before {
            display: none;
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

    /* Loading state */
    .loading-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        backdrop-filter: blur(4px);
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f4f6;
        border-top: 4px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
        .premium-stat-card:hover {
            transform: none;
            box-shadow: var(--shadow);
        }
        
        .premium-table tbody tr:hover {
            background: inherit;
            transform: none;
            box-shadow: none;
        }
        
        .action-btn:hover,
        .employee-id-badge:hover {
            transform: none;
        }
        
        .action-btn:active,
        .employee-id-badge:active {
            transform: scale(0.95);
        }
        
        .stat-item:hover {
            transform: none;
        }
        
        .stat-item:active {
            transform: scale(0.98);
        }
    }

    /* Improve touch targets */
    @media (max-width: 768px) {
        .action-btn,
        .employee-id-badge {
            min-height: 44px;
            min-width: 44px;
        }
        
        .search-input {
            min-height: 44px;
        }
        
        .records-select {
            min-height: 44px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <div class="header-text">
                    <h1>
                        💰 {{ __('Salary Management') }}
                    </h1>
                    <p>{{ __('Comprehensive salary tracking and management dashboard for enhanced payroll planning') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $employees->count() }}</p>
                    <p class="stat-label">{{ __('Total') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">{{ number_format($employees->avg('salary'), 0) }}</p>
                    <p class="stat-label">{{ __('Avg Salary') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">{{ number_format($employees->sum('salary'), 0) }}</p>
                    <p class="stat-label">{{ __('Total Payroll') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row gx-4 gy-4 mb-4">
        <div class="col-lg-3 col-md-6 fade-in" style="animation-delay: 0.1s">
            <div class="premium-stat-card">
                <div class="stat-card-content">
                    <div class="stat-icon icon-total">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <h3>{{ $employees->count() }}</h3>
                        <p class="stat-title">{{ __('Total Employees') }}</p>
                        <p class="stat-subtitle">{{ __('Active workforce') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 fade-in" style="animation-delay: 0.2s">
            <div class="premium-stat-card">
                <div class="stat-card-content">
                    <div class="stat-icon icon-high">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="stat-details">
                        <h3>{{ $employees->where('salary', '>', 50000)->count() }}</h3>
                        <p class="stat-title">{{ __('High Salary') }}</p>
                        <p class="stat-subtitle">{{ __('>50K range') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 fade-in" style="animation-delay: 0.3s">
            <div class="premium-stat-card">
                <div class="stat-card-content">
                    <div class="stat-icon icon-medium">
                        <i class="fas fa-equals"></i>
                    </div>
                    <div class="stat-details">
                        <h3>{{ $employees->whereBetween('salary', [25000, 50000])->count() }}</h3>
                        <p class="stat-title">{{ __('Medium Salary') }}</p>
                        <p class="stat-subtitle">{{ __('25K-50K range') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 fade-in" style="animation-delay: 0.4s">
            <div class="premium-stat-card">
                <div class="stat-card-content">
                    <div class="stat-icon icon-low">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="stat-details">
                        <h3>{{ $employees->where('salary', '<', 25000)->count() }}</h3>
                        <p class="stat-title">{{ __('Entry Level') }}</p>
                        <p class="stat-subtitle">{{ __('<25K range') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Container --}}
    <div class="premium-table-container fade-in" style="animation-delay: 0.5s">
        {{-- Table Header --}}
        <div class="table-header-premium">
            <h5 class="table-title-premium">
                <i class="fas fa-money-check-alt"></i>
                {{ __('Employee Salary Records') }}
                <span class="employee-count-badge">{{ $employees->count() }} {{ __('Employees') }}</span>
            </h5>
        </div>
        
        {{-- Search Container --}}
        <div class="controls-container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <label class="me-3 text-muted fw-semibold">{{ __('Show') }}</label>
                        <select class="records-select" id="recordsPerPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-muted ms-2">{{ __('entries') }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <div class="search-input-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="{{ __('Search employees...') }}" id="searchInput">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Table --}}
        <div class="table-responsive">
            <table class="premium-table table" id="salaryTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i> {{ __('ID') }}</th>
                        <th><i class="fas fa-user me-2"></i> {{ __('Employee') }}</th>
                        <th><i class="fas fa-calendar me-2"></i> {{ __('Payroll Type') }}</th>
                        <th><i class="fas fa-dollar-sign me-2"></i> {{ __('Salary') }}</th>
                        <th><i class="fas fa-calculator me-2"></i> {{ __('Net Salary') }}</th>
                        <th><i class="fas fa-cogs me-2"></i> {{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                    <tr class="slide-in" style="animation-delay: {{ $loop->index * 0.05 }}s">
                        <td data-label="{{ __('ID') }}">
                            <a href="{{ route('setsalary.show', $employee->id) }}" class="employee-id-badge" data-bs-toggle="tooltip" title="{{ __('View Details') }}">
                                {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                            </a>
                        </td>
                        <td data-label="{{ __('Employee') }}">
                            <div class="employee-info">
                                <div class="employee-avatar">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                                <div class="employee-details">
                                    <h6>{{ $employee->name }}</h6>
                                    <small>{{ $employee->email ?? __('N/A') }}</small>
                                </div>
                            </div>
                        </td>
                        <td data-label="{{ __('Payroll Type') }}">
                            <span class="payroll-type-badge">
                                {{ $employee->salary_type() }}
                            </span>
                        </td>
                        <td data-label="{{ __('Salary') }}">
                            <span class="salary-amount">
                                {{ \Auth::user()->priceFormat($employee->salary) }}
                            </span>
                        </td>
                        <td data-label="{{ __('Net Salary') }}">
                            <span class="salary-amount">
                                {{ !empty($employee->get_net_salary()) ? \Auth::user()->priceFormat($employee->get_net_salary()) : __('N/A') }}
                            </span>
                        </td>
                        <td data-label="{{ __('Action') }}">
                            <a href="{{ route('setsalary.show', $employee->id) }}" class="action-btn" data-bs-toggle="tooltip" title="{{ __('View Salary Details') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable with enhanced options
    let table;
    
    function initializeDataTable() {
        if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            try {
                // Check if DataTable is already initialized
                if ($.fn.DataTable.isDataTable('#salaryTable')) {
                    $('#salaryTable').DataTable().clear().destroy();
                }
                
                table = $('#salaryTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "responsive": {
                        "details": {
                            "type": 'column',
                            "target": 'tr'
                        }
                    },
                    "autoWidth": false,
                    "pageLength": 10,
                    "destroy": true,
                    "dom": 'rt<"d-flex justify-content-between align-items-center mt-4 px-3 pb-3"<"dataTables_info">p>',
                    "language": {
                        "search": "",
                        "searchPlaceholder": "{{ __('Search employees...') }}",
                        "lengthMenu": "_MENU_",
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
                    "columnDefs": [
                        { "orderable": false, "targets": [5] }, // Disable sorting for action column
                        { "className": "text-center", "targets": [0, 5] }
                    ],
                    "drawCallback": function() {
                        // Reinitialize tooltips after table redraw
                        initializeTooltips();
                        // Add staggered animation to rows
                        $('#salaryTable tbody tr').each(function(index) {
                            $(this).css('animation-delay', (index * 0.05) + 's');
                            $(this).addClass('slide-in');
                        });
                    }
                });
                
                console.log('DataTable initialized successfully');
                
            } catch (error) {
                console.warn('DataTable initialization failed:', error);
                initializeBasicTable();
            }
        } else {
            console.log('DataTables not available, using basic table');
            initializeBasicTable();
        }
    }

    function initializeBasicTable() {
        // Basic table functionality without DataTables
        const tableBody = document.querySelector('#salaryTable tbody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        
        // Add basic search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    }

    function initializeTooltips() {
        // Initialize tooltips (Bootstrap 5)
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        // Fallback for Bootstrap 4
        else if (typeof $ !== 'undefined' && $.fn.tooltip) {
            $('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]').tooltip();
        }
    }

    // Initialize everything
    setTimeout(function() {
        initializeDataTable();
        initializeTooltips();
    }, 100);

    // Custom search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            if (table) {
                table.search(this.value).draw();
            }
        });
    }
    
    // Custom page length
    const recordsSelect = document.getElementById('recordsPerPage');
    if (recordsSelect) {
        recordsSelect.addEventListener('change', function() {
            if (table) {
                table.page.len(parseInt(this.value)).draw();
            }
        });
    }

    // Enhanced hover effects for stat cards
    const statCards = document.querySelectorAll('.premium-stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Loading state for actions
    document.addEventListener('click', function(e) {
        if (e.target.closest('.action-btn, .employee-id-badge')) {
            const tableContainer = document.querySelector('.premium-table-container');
            if (tableContainer) {
                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'loading-overlay';
                loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
                tableContainer.style.position = 'relative';
                tableContainer.appendChild(loadingOverlay);
                
                setTimeout(() => {
                    if (loadingOverlay.parentNode) {
                        loadingOverlay.remove();
                    }
                }, 1500);
            }
        }
    });

    // Enhanced table row animations (only on non-touch devices)
    const tableRows = document.querySelectorAll('#salaryTable tbody tr');
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    if (!isTouchDevice) {
        tableRows.forEach((row, index) => {
            row.addEventListener('mouseenter', function() {
                if (window.innerWidth > 400) {
                    this.style.transform = 'translateX(4px) scale(1.01)';
                    this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
                }
            });

            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0) scale(1)';
                this.style.boxShadow = '';
            });
        });
    }

    // Handle window resize for responsive behavior
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (table && typeof table.columns !== 'undefined') {
                table.columns.adjust().responsive.recalc();
            }
            
            // Reinitialize tooltips after resize
            setTimeout(initializeTooltips, 100);
        }, 250);
    });

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.action-btn, .employee-id-badge');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple-animation 0.6s linear';
            ripple.style.pointerEvents = 'none';
            
            this.style.position = 'relative';
            this.appendChild(ripple);
            
            setTimeout(() => {
                if (ripple.parentNode) {
                    ripple.remove();
                }
            }, 600);
        });
    });
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush