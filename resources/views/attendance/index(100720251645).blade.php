@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection
@php
use App\Helpers\Helper;
use Carbon\Carbon;
$requestType = isset($_GET['type']) ? $_GET['type'] : 'daily';
$profile=asset(Storage::url('uploads/avatar/'));
@endphp

@push('css-page')
<style>
    /* Enhanced Color Scheme - Matching Leave Management */
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary: #3b82f6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info-blue: #74b9ff;
        --info: #60a5fa;
        --dark: #1f2937;
        --light: #f8fafc;
        --border: #e5e7eb;  
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Compact Header */
    .page-header-compact {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.95) 0%, 
            rgba(59, 130, 246, 0.95) 50%, 
            rgba(96, 165, 250, 0.95) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        padding: 32px 40px;
        margin-bottom: 32px;
        box-shadow: 
            0 32px 64px rgba(37, 99, 235, 0.3),
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .page-header-compact::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: 
            conic-gradient(from 0deg at 50% 50%, 
                transparent 0deg, 
                rgba(255, 255, 255, 0.1) 60deg, 
                transparent 120deg, 
                rgba(255, 255, 255, 0.05) 180deg, 
                transparent 240deg, 
                rgba(255, 255, 255, 0.1) 300deg, 
                transparent 360deg);
        animation: rotateBg 25s linear infinite;
        pointer-events: none;
    }

    .page-header-compact::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, 
            transparent 30%, 
            rgba(255, 255, 255, 0.05) 50%, 
            transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .page-header-compact:hover::after {
        opacity: 1;
    }

    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .page-header-compact .header-content {
        position: relative;
        z-index: 2;
    }

    .page-title-compact {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        letter-spacing: -0.025em;
    }

    .page-subtitle-compact {
        color: rgba(255, 255, 255, 0.9);
        margin: 6px 0 0 0;
        font-size: 1rem;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .header-icon {
        width: 72px;
        height: 72px;
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        backdrop-filter: blur(20px);
        box-shadow: 
            0 8px 32px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .header-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(-45deg);
        transition: transform 0.6s ease;
    }

    .header-icon:hover::before {
        transform: rotate(-45deg) translate(100%, 100%);
    }

    /* Enhanced Page Layout */
    .attendance-management-container {
        background: linear-gradient(135deg, #f8f9fc 0%, #e8ecff 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    /* Premium Header Section */
    .attendance-header {
        background: var(--primary);
        color: white;
        padding: 2.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-medium);
        position: relative;
        overflow: hidden;
    }

    .attendance-header::before {
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

    .attendance-header h2 {
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

    .attendance-header p {
        margin: 0.75rem 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
        position: relative;
        z-index: 2;
    }

    /* Enhanced Import/Rollback Section */
    .import-rollback-section {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-light);
        border: 1px solid var(--border-light);
    }

    .import-rollback-section h4 {
        color: var(--text-dark);
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
        background: var(--primary);
        border-radius: var(--border-radius) var(--border-radius) 0 0;
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

    .stat-card.total-attendance .stat-icon { 
        background: var(--primary);
    }
    .stat-card.present-employees .stat-icon { 
        background: linear-gradient(135deg, var(--success) 0%, #00cec9 100%); 
    }
    .stat-card.absent-employees .stat-icon { 
        background: linear-gradient(135deg, var(--danger) 0%, #fd79a8 100%); 
    }
    .stat-card.on-leave .stat-icon { 
        background: linear-gradient(135deg, var(--warning) 0%, #fab1a0 100%); 
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
        background: linear-gradient(135deg, var(--light) 0%, rgba(108, 92, 231, 0.1) 100%);
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
        background: var(--primary);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 0.75rem;
        box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
    }

    /* Enhanced Filter Section */
    .filter-section {
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, #fafbff 0%, #f0f2ff 100%);
    }

    .filter-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        /* align-items: center; */
        gap: 0.5rem;
    }

    .filter-group label {
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .filter-group select, .filter-group input {
        border: 2px solid var(--border-light);
        border-radius: var(--border-radius-sm);
        padding: 0.5rem 1rem;
        color: var(--text-dark);
        background: var(--white);
        font-weight: 500;
        transition: border-color 0.3s ease;
        min-width: 120px;
    }

    .filter-group select:focus, .filter-group input:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
    }

    .apply-btn, .reset-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: var(--border-radius-sm);
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .reset-btn {
        background: linear-gradient(135deg, var(--text-light) 0%, #636e72 100%);
        margin-left: 0.5rem;
    }

    .apply-btn:hover, .reset-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-light);
        color: white;
        text-decoration: none;
    }

    /* Enhanced Table Controls */
    .table-controls {
        padding: 1.5rem 2rem;
        background: var(--white);
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
        border-color: var(--primary);
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
        border-color: var(--primary);
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
        height: 600px;
        overflow: hidden;
        background: #f1f2ff;
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
        background: var(--white);
    }

    .custom-table thead th {
        background: var(--primary);
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
        background: var(--white);
    }

    .custom-table tbody tr:nth-child(even) {
        background-color: rgba(108, 92, 231, 0.02);
    }

    .custom-table tbody tr:hover {
        background: linear-gradient(135deg, var(--light) 0%, rgba(108, 92, 231, 0.05) 100%);
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
        background: inherit;
    }

    /* Employee Header Row */
    .employee-header-row {
        background: var(--primary) !important;
        color: white !important;
        position: sticky;
        top: 60px;
        z-index: 99;
    }

    .employee-header-row td {
        padding: 1.5rem !important;
        font-weight: 700 !important;
        font-size: 1.1rem !important;
        text-align: left !important;
        color: #000 !important;
        border-bottom: 2px solid rgba(255,255,255,0.2) !important;
        background: #e3e6fe !important;
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
        background: var(--primary) !important;
        color: white;
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

    .attendance-counter {
        background: rgba(255,255,255,0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .view-more-btn {
        background: #e8ebfe;
        border: 1px solid #828282;
        color: #000;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .view-more-btn:hover {
        background: #000;
        transform: scale(1.05);
        color: white;
        text-decoration: none;
    }

    .add-attendance-btn {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 50%;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .add-attendance-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: scale(1.1);
        color: white;
        text-decoration: none;
    }
    
    .table-container {
      max-height: 600px !important;
    }
    
    .table-responsive {
      min-height: 99% !important;
    }
    

    /* Collapsible Rows */
    .collapsible-rows {
        display: none;
    }

    .collapsible-rows.show {
        display: table-row;
    }

    /* Enhanced Status Styling */
    .status-present {
        color: var(--success) !important;
        font-weight: 700;
    }

    .status-absent {
        color: var(--danger) !important;
        font-weight: 700;
    }

    .status-leave {
        color: var(--warning) !important;
        font-weight: 700;
    }

    .time-badge {
        background: linear-gradient(135deg, var(--info-blue) 0%, #0984e3 100%);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    .late-time {
        background: linear-gradient(135deg, var(--danger) 0%, #fd79a8 100%);
    }

    .rest-time {
        background: linear-gradient(135deg, var(--success) 0%, #00cec9 100%);
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
        text-decoration: none;
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
        text-decoration: none;
    }

    .action-btn.btn-edit {
        background: linear-gradient(135deg, var(--info-blue) 0%, #0984e3 100%);
        color: white;
    }

    .action-btn.btn-delete {
        background: linear-gradient(135deg, var(--danger) 0%, #fd79a8 100%);
        color: white;
    }

    .action-btn.btn-copy {
        background: linear-gradient(135deg, var(--warning) 0%, #fab1a0 100%);
        color: white;
    }

    /* Form Styling */
    .form-control, .form-control-file {
        border: 2px solid var(--border-light);
        border-radius: var(--border-radius-sm);
        padding: 0.75rem 1rem;
        color: var(--text-dark);
        background: var(--white);
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-control-file:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
    }

    .btn-xs {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: var(--border-radius-sm);
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-xs:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-light);
        color: white;
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
        background: var(--primardy);
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
    }

    .employee-avatar img{
        border-radius: 50% !important;
        height: inherit !important;
        width: 100%;
        max-width: 150px;
        aspect-ratio: 1 / 1;    /* Maintains square aspect ratio */
        object-fit: cover;
        object-position: center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .attendance-management-container {
            padding: 1rem;
        }
        
        .attendance-header {
            padding: 2rem 1.5rem;
        }
        
        .filter-section {
            padding: 1.5rem;
        }
        
        .filter-row {
            flex-direction: column;
            align-items: stretch;
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

    /* Special rows styling */
    .not-found td{
        background: var(--text-light);
        color: white;
    }

    .absent-row td{
        color: white !important;
        background: var(--danger) !important;
    }

    .weekend-row td {
        background: #00000059 !important;
        color: white !important;
        font-weight: bolder !important;
    }

    .holiday-row td {
        background: #21ff0063 !important;
        color: black !important;
        font-weight: bolder !important;
    }

    .leave-row td {
        background: var(--warning) !important;
        color: white !important;
        font-weight: bolder !important;
    }



    /* Premium Filter Styling */
    .premium-filter-container {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 0;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-medium);
        border: 1px solid var(--border-light);
        overflow: hidden;
        position: relative;
    }

    .premium-filter-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary);
    }

    .filter-header {
        background: linear-gradient(135deg, var(--light) 0%, rgba(108, 92, 231, 0.08) 100%);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .filter-header h4 {
        color: var(--text-dark);
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .premium-filter-form {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    /* Type Selection Row */
    .type-selection-row {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-light);
        margin-bottom: 1rem;
    }

    .type-selection-label {
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.95rem;
        min-width: 120px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .radio-group {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .radio-option {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius-sm);
        transition: var(--transition);
        background: var(--light-bg);
        border: 2px solid var(--border-light);
        min-width: 140px;
        justify-content: center;
    }

    .radio-option:hover {
        background: linear-gradient(135deg, var(--light) 0%, rgba(108, 92, 231, 0.1) 100%);
        border-color: var(--secondary);
        transform: translateY(-2px);
        box-shadow: var(--shadow-light);
    }

    .radio-option.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .radio-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .radio-checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        background: var(--white);
    }

    .radio-option.active .radio-checkmark {
        border-color: white;
        background: white;
    }

    .radio-checkmark::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
        opacity: 0;
        transition: var(--transition);
    }

    .radio-option.active .radio-checkmark::after {
        opacity: 1;
    }

    .radio-label {
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition);
    }

    /* Filter Row */
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .filter-group label {
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-group label i {
        color: var(--primary);
        font-size: 0.85rem;
    }

    /* Enhanced Form Controls */
    .filter-section .form-control {
        border: 2px solid var(--border-light);
        border-radius: var(--border-radius-sm);
        padding: 0.875rem 1.25rem;
        color: var(--text-dark);
        background: var(--white);
        font-weight: 500;
        font-size: 0.9rem;
        transition: var(--transition);
        box-shadow: 0 2px 4px rgba(108, 92, 231, 0.04);
    }

    .filter-section .form-control:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1), 0 4px 12px rgba(108, 92, 231, 0.15);
        transform: translateY(-2px);
    }

    .filter-section .form-control:hover:not(:focus) {
        border-color: var(--secondary);
        box-shadow: 0 4px 8px rgba(108, 92, 231, 0.08);
    }

    /* Select Enhancement */
    .filter-section select.form-control {
            background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        padding-right: 3rem;
        cursor: pointer;
    }

    /* Date Input Enhancement */
    .filter-section input[type="date"]::-webkit-calendar-picker-indicator,
    .filter-section input[type="month"]::-webkit-calendar-picker-indicator {
        background: var(--primary);
        border-radius: 4px;
        padding: 4px;
        cursor: pointer;
        transition: var(--transition);
    }

    .filter-section input[type="date"]::-webkit-calendar-picker-indicator:hover,
    .filter-section input[type="month"]::-webkit-calendar-picker-indicator:hover {
        background: var(--secondary);
        transform: scale(1.1);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        align-items: center;
        margin-top: 1rem;
    }

    .btn-premium {
        padding: 0.875rem 2rem;
        border: none;
        border-radius: var(--border-radius-sm);
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        min-width: 140px;
    }

    .btn-premium::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-premium:hover::before {
        left: 100%;
    }

    h3 i {
        color: var(--primary);
    }

    .btn-apply {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
    }

    .btn-apply:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(108, 92, 231, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-reset {
        background: #4e4763;
        color: white;
        box-shadow: 0 4px 12px rgba(99, 110, 114, 0.3);
    }

    .btn-reset:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(99, 110, 114, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .filter-row {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.25rem;
        }
    }

    @media (max-width: 768px) {
        .filter-content {
            padding: 1.5rem;
        }

        .filter-header {
            padding: 1.25rem 1.5rem;
        }

        .type-selection-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .type-selection-label {
            min-width: auto;
        }

        .radio-group {
            width: 100%;
            justify-content: center;
        }

        .radio-option {
            flex: 1;
            min-width: 120px;
        }

        .filter-row {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
        }

        .btn-premium {
            width: 100%;
            min-width: auto;
        }
    }

    @media (max-width: 480px) {
        .filter-header h4 {
            font-size: 1rem;
        }

        .radio-group {
            flex-direction: column;
            gap: 0.75rem;
        }

        .radio-option {
            width: 100%;
        }

        .filter-section .form-control {
            padding: 0.75rem 1rem;
        }

        .btn-premium {
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .filter-group:nth-child(1) { animation-delay: 0.1s; }
    .filter-group:nth-child(2) { animation-delay: 0.2s; }
    .filter-group:nth-child(3) { animation-delay: 0.3s; }
    .filter-group:nth-child(4) { animation-delay: 0.4s; }
    .filter-group:nth-child(5) { animation-delay: 0.5s; }
</style>
@endpush

@section('content')

    <div class="page-header-compact">
        <div class="header-content d-flex justify-content-between align-items-center">
            <div class="col-md-6 d-flex">
                <div class="header-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="ml-3">
                    <h1 class="page-title-compact">
                        {{ __('Attendance Management') }}
                    </h1>
                    <p class="page-subtitle-compact">{{ __('Comprehensive attendance tracking and management dashboard for enhanced workforce monitoring') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{--
        <!-- Enhanced Import/Rollback Section -->
        <div class="import-rollback-section">
            <h4><i class="fas fa-upload"></i> Import & Rollback Management</h4>
            <div class="row">
                <div class="col-md-8">
                    <form method="POST" action="{{ route('attendanceemployee.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        <i class="fas fa-sign-in-alt"></i> {{ __('Choose Clock In xlsx file') }}
                                    </label>
                                    <input type="file" class="form-control" name="clock_in_file" accept=".xlsx" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        <i class="fas fa-sign-out-alt"></i> {{ __('Choose Clock Out xlsx file') }}
                                    </label>
                                    <input type="file" class="form-control" name="clock_out_file" accept=".xlsx" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn-xs" style="margin-top: 2rem;">
                                    <i class="fas fa-upload"></i> Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="POST" action="{{ route('attendanceemployee.import.rollback') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        <i class="fas fa-undo"></i> Select Batch for Rollback
                                    </label>
                                    <select name="batch_id" class="form-control" required>
                                        <option value="">Select Batch</option>
                                        @foreach (Helper::attendanceBatchList() as $batch_id)
                                            @php
                                                $batch_date = \Carbon\Carbon::createFromTimestamp($batch_id, 'Asia/Kolkata');
                                            @endphp
                                            <option value="{{ $batch_id }}">
                                                {{ $batch_date->format('d M Y, h:i:s A') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <button type="submit" class="btn-xs" style="margin-top: 2rem;">
                                    <i class="fas fa-undo"></i> Rollback
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    --}}

    <!-- Enhanced Statistics Cards -->
    <div class="stats-container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card total-attendance">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-number">{{ count($attendanceWithEmployee ?? []) }}</div>
                            <div class="stat-label">Total Employees</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card present-employees">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-number">
                                {{ collect($attendanceWithEmployee ?? [])->filter(function($employee) { return !$employee->attendance->isEmpty(); })->count() }}
                            </div>
                            <div class="stat-label">Present</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            /* $leaveCount = collect($attendanceWithEmployee ?? [])->filter(function($employee) { 
                                return Helper::checkLeave($date ?? today(), $employee->id) != 0; 
                            })->count(); */
                $filterDate = isset($_GET['date']) ? $_GET['date'] : (isset($date) ? $date : today());
                $leaveCount = collect($attendanceWithEmployee ?? [])->filter(function($employee) use ($filterDate) { 
                                        return Helper::checkLeave($filterDate, $employee->id) != 0; 
                                    })->count();
            ?>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card absent-employees">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-number">
                                {{ collect($attendanceWithEmployee ?? [])->filter(function($employee) { return $employee->attendance->isEmpty(); })->count() - $leaveCount }}
                            </div>
                            <div class="stat-label">Absent</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card on-leave">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-number">
                                {{ $leaveCount ?? 0 }}
                            </div>
                            <div class="stat-label">On Leave</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-times"></i>
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
                <i class="fas fa-clock"></i>
                Employee Attendance Records
                <span class="employee-count-badge">
                    {{ count($attendanceWithEmployee ?? []) }} Employees
                </span>
            </h3>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="filter-section">
            <div class="filter-content">
                <form method="GET" action="{{ route('attendanceemployee.index') }}" class="premium-filter-form">
                    
                    <!-- Type Selection Row -->
                    {{-- <!-- <div class="type-selection-row">
                        <div class="type-selection-label">
                            <i class="fas fa-calendar-alt"></i>
                            View Type
                        </div>
                        <div class="radio-group">
                            <label class="radio-option {{ $requestType == 'daily' ? 'active' : '' }}" for="daily">
                                <input type="radio" id="daily" value="daily" name="type" {{ $requestType == 'daily' ? 'checked' : '' }} onchange="activeDailyBox(); updateRadioState(this)">
                                <span class="radio-checkmark"></span>
                                <span class="radio-label">Daily View</span>
                            </label>
                            <label class="radio-option {{ $requestType == 'monthly' ? 'active' : '' }}" for="monthly">
                                <input type="radio" id="monthly" value="monthly" name="type" {{ $requestType == 'monthly' ? 'checked' : '' }} onchange="activeMonthBox(); updateRadioState(this)">
                                <span class="radio-checkmark"></span>
                                <span class="radio-label">Monthly View</span>
                            </label>
                        </div>
                    </div> --> --}}

                    <!-- Filter Row -->
                    <div class="filter-row d-flex">
                        <div class="filter-group">
                            <label for="monthAndDate">
                                <i class="fas fa-calendar"></i>
                                Date Range
                            </label>
                            <div id="monthAndDate">
                                @if($requestType == 'daily')
                                    <input type="date" name="date" class="form-control" value="{{ $currentDate ?? date('Y-m-d') }}">
                                @else
                                    <input type="month" name="month" class="form-control" value="{{ $currentMonth ?? date('Y-m') }}">
                                @endif
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="branch">
                                <i class="fas fa-building"></i>
                                Branch
                            </label>
                            <select name="branch" id="branch" class="form-control">
                                @foreach ($branch ?? [] as $branchId => $branchName)
                                    <option value="{{ $branchId }}" {{ isset($_GET['branch']) && $_GET['branch'] == $branchId ? 'selected' : '' }}>
                                        {{ $branchName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="department">
                                <i class="fas fa-users-cog"></i>
                                Department
                            </label>
                            <select name="department" id="department" class="form-control">
                                @foreach ($department ?? [] as $departmentId => $departmentName)
                                    <option value="{{ $departmentId }}" {{ isset($_GET['department']) && $_GET['department'] == $departmentId ? 'selected' : '' }}>
                                        {{ $departmentName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="employee">
                                <i class="fas fa-user"></i>
                                Employee
                            </label>
                            <select name="employee" id="employee" class="form-control">
                                <option value="">All Employees</option>
                                @foreach ($employees ?? [] as $employee)
                                    <option value="{{ $employee->id }}" {{ isset($_GET['employee']) && $_GET['employee'] == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <div class="action-buttons">
                                <button type="submit" class="btn-premium btn-apply">
                                    <i class="fas fa-search"></i>
                                    Apply Filters
                                </button>
                                <a href="{{ route('attendanceemployee.index') }}" class="btn-premium btn-reset">
                                    <i class="fas fa-sync-alt"></i>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- <div class="table-controls">
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
                <input type="text" placeholder="Search employees, attendance records..." id="search-input">
                <i class="fas fa-search"></i>
            </div>
        </div> --}}

        <div class="table-container">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                            <th><i class="fas fa-sign-in-alt"></i> Clock In</th>
                            <th><i class="fas fa-sign-out-alt"></i> Clock Out</th>
                            <th><i class="fas fa-play-circle"></i> Shift Start</th>
                            <th><i class="fas fa-clock"></i> Late/Rest</th>
                            @if (Gate::check('Edit Attendance') || Gate::check('Delete Attendance') && \Auth::user()->type != 'employee')
                                <th><i class="fas fa-cogs"></i> Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if(\Auth::user()->type == 'employee')
                            @if($requestType == 'daily')
                                @php
                                    $status = $leaveToday ? $leaveToday : ($holidays ? 'HOLIDAY' : ($isWeekend ? 'WEEK-END' : 'Absent'));
                                @endphp
                                @if(count($attendanceEmployee)<1 ||  $status == 'Short Leave')
                                <tr class="{{ $leaveToday ? 'leave-row' : ($holidays ? 'holiday-row' : ($isWeekend ? 'weekend-row' : 'absent-row')) }}">
                                    <td align="center" colspan="7">{{ $status }}</td>
                                </tr>
                                @endif
                                @foreach ($attendanceEmployee as $attendance)
                                    <tr>
                                        <td>{{ date('d-m-Y', strtotime($attendance->date)) }}</td>
                                        <td><span class="status-present">{{ $attendance->status }}</span></td>
                                        <td><span class="time-badge">{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</span></td>
                                        <td><span class="time-badge">{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</span></td>
                                        <td>{{ $attendance->employee->shift_start ?? 'N/A' }}</td>
                                        <td>
                                            <span class="time-badge {{ $attendance->total_rest == '00:00:00' ? 'late-time' : 'rest-time' }}">
                                                {{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest == '00:00:00' ? $attendance->late : $attendance->total_rest) }}{{ $attendance->total_rest == '00:00:00' ? ' (Late)' : ' (Rest)' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($monthAttendanceEmployee as $key => $attEmp)
                                @if($attEmp['is_weekend'])
                                    <tr class="weekend-row">
                                        <td align="center" colspan="7">{{ $key }} (WEEKEND)</td>
                                    </tr>
                                @elseif($attEmp['is_leave'])
                                    <tr class="leave-row">
                                        <td align="center" colspan="7">{{ $key }} (LEAVE)</td>
                                    </tr>
                                @elseif(!$attEmp['is_weekend'] && !$attEmp['is_leave'])
                                    <tr class="employee-header-row">
                                        <td colspan="7" data-toggle="collapse" data-target="#collapse-{{ $key }}">
                                            <div class="employee-name-section">
                                                <div class="employee-info">
                                                    <div class="employee-avatar">{{ strtoupper(substr($key, 0, 2)) }}</div>
                                                    <strong>{{ $key }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="collapse-{{ $key }}">
                                        <td colspan="7" style="padding: 0;">
                                            <table class="table" style="margin: 0; background: rgba(108, 92, 231, 0.02);">
                                                <thead>
                                                    <tr style="background: rgba(108, 92, 231, 0.1);">
                                                        <th>{{ __('Date') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                        <th>{{ __('Clock In') }}</th>
                                                        <th>{{ __('Clock Out') }}</th>
                                                        <th>{{ __('Shift Start') }}</th>
                                                        <th>{{ __('Late/Rest') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($attEmp['attendance'] as $attendance)
                                                        <tr>
                                                            <td>{{ date('d-m-Y', strtotime($attendance['date'])) }}</td>
                                                            <td><span class="status-present">{{ $attendance['status'] }}</span></td>
                                                            <td><span class="time-badge">{{ $attendance['clock_in'] != '00:00:00' ? \Auth::user()->timeFormat($attendance['clock_in']) : '00:00' }}</span></td>
                                                            <td><span class="time-badge">{{ $attendance['clock_out'] != '00:00:00' ? \Auth::user()->timeFormat($attendance['clock_out']) : '00:00' }}</span></td>
                                                            <td>{{ $attendance['employee']['shift_start'] ?? 'N/A' }}</td>
                                                            <td>
                                                                <span class="time-badge {{ $attendance['total_rest'] == '00:00:00' ? 'late-time' : 'rest-time' }}">
                                                                    {{ Helper::convertTimeToMinutesAndSeconds($attendance['total_rest'] == '00:00:00' ? $attendance['late'] : $attendance['total_rest']) }}{{ $attendance['total_rest'] == '00:00:00' ? ' (Late)' : ' (Rest)' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @endif
                        @else
                            @if($holidays || $isWeekend)
                                @if($holidays)
                                <tr class="holiday-row">
                                    <td colspan="7" align="center">HOLIDAY</td>
                                </tr>
                                @endif
                                @if($isWeekend)
                                <tr class="weekend-row">
                                    <td colspan="7" align="center">WEEK-END</td>
                                </tr>
                                @endif
                            @else
                                @foreach ($attendanceWithEmployee as $employee)
                                    @php
                                    $isAbsent = $employee->attendance->isEmpty();
                                    $isLeave = Helper::checkLeave($date?$date:today(), $employee->id);
                                    if($isLeave != 0){
                                        $leaveToday = Helper::checkLeaveWithTypes($date?$date:today(), $employee->id);
                                        if($leaveToday == 'afternoon halfday' || $leaveToday == 'morning halfday'){
                                            $leaveToday = 'Half-Day Leave';
                                        }elseif($leaveToday == 'on short leave'){
                                            $leaveToday = 'Short Leave';
                                        }elseif($leaveToday == 'fullday Leave'){
                                            $leaveToday = 'Leave';
                                        }
                                    }else{
                                        $leaveToday = 0;
                                    }
                                    $totalTime = Helper::calculateTotalTimeDifference($employee->attendance);
                                    $threshold = '08:00';
                                    if ($leaveToday == 'Half-Day Leave') {
                                        $threshold = '04:00';
                                    } elseif ($leaveToday == 'Short Leave') {
                                        $threshold = '06:00';
                                    }
                                    $attendanceCount = count($employee->attendance ?? []);
                                    @endphp
                                    
                                    <tr class="employee-header-row" style="top: 40px;">
                                        <td colspan="7" style="top: 0;">
                                            <div class="employee-name-section">
                                                <div class="employee-info">
                                                    <div class="employee-avatar">
                                                        <img src="{{(!empty($employee->user->avatar)? $profile.'/'.$employee->user->avatar : $profile.'/avatar.png')}}">
                                                    </div>
                                                    <div>
                                                        <strong>{{ $employee->name }}</strong>
                                                        <br>
                                                        <small style="opacity: 0.8;">
                                                            Total Time:
                                                            <span style="{{ $totalTime < $threshold ? 'color: #f00; font-weight: bold;' : 'color: #4caf50; font-weight: bold;' }}">
                                                                {{ $totalTime }}
                                                            </span>
                                                            @if($leaveToday && $leaveToday != 0)
                                                                | <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 10px;">{{ $leaveToday }}</span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="employee-controls">
                                                    <span class="attendance-counter">
                                                        <i class="fas fa-clock"></i> {{ $attendanceCount }} Records
                                                    </span>
                                                    @if($attendanceCount > 5)
                                                        <button class="view-more-btn" onclick="toggleEmployeeAttendance('{{ $employee->id }}')">
                                                            <span id="toggle-text-{{ $employee->id }}">View All</span>
                                                            <i class="fas fa-chevron-down" id="toggle-icon-{{ $employee->id }}"></i>
                                                        </button>
                                                    @endif
                                                    @if(!$attendanceCount)
                                                        <a href="#" 
                                                           data-url="{{ URL::to('attendanceemployee/create?employee_id=' . $employee->id . '&date=' . (isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'))) }}"
                                                           data-size="lg"
                                                           data-ajax-popup="true"
                                                           data-title="{{ __('Add Attendance') }}"
                                                           class="add-attendance-btn">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    @forelse ($employee->attendance ?? [] as $key=>$attendance)
                                        <tr class="{{ $key >= 5 ? 'collapsible-rows employee-' . $employee->id : '' }}">
                                            <td>{{ date('d-m-Y', strtotime($attendance->date)) }}</td>
                                            <td><span class="status-present">{{ $attendance->status }}</span></td>
                                            <td><span class="time-badge">{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</span></td>
                                            <td><span class="time-badge">{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</span></td>
                                            <td>{{ $employee->shift_start ?? 'N/A' }}</td>
                                            <td>
                                                @if($key)
                                                    <span class="time-badge rest-time">{{Helper::dynRestTime($employee->attendance[$key-1]->clock_out??'',$employee->attendance[$key]->clock_in)}}</span>
                                                @else
                                                    <span class="time-badge late-time">{{Helper::dynLateTime($employee->shift_start??'09:00:00',$attendance->clock_in)}}</span>
                                                @endif
                                            </td>
                                            @if (Gate::check('Edit Attendance') || Gate::check('Delete Attendance') && \Auth::user()->type != 'employee')
                                            <td>
                                                <div class="action-buttons">
                                                    @if($attendance->clock_out != '00:00:00')
                                                        <a href="#" data-url="{{ URL::to('copy/attendance/' . $attendance->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Copy Attendance') }}" class="action-btn btn-copy">
                                                            <i class="far fa-copy"></i>
                                                        </a>
                                                    @endif
                                                    @can('Edit Attendance')
                                                        <a href="#" data-url="{{ URL::to('attendanceemployee/' . $attendance->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Attendance') }}" class="action-btn btn-edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Attendance')
                                                        <button class="action-btn btn-delete" data-confirm="{{ __('Are You Sure?') . '|' . __('This action cannot be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-{{ $attendance->id }}').submit();">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <form method="POST" action="{{ route('attendanceemployee.destroy', $attendance->id) }}" id="delete-form-{{ $attendance->id }}" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                    <tr class="{{ $leaveToday ? 'leave-row' : 'absent-row' }}">
                                        <td colspan="7" align="center">{{$leaveToday?$leaveToday:'Absent'}}</td>
                                    </tr>
                                    @endforelse
                                @endforeach
                            @endif
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script>
        const typeParam = new URLSearchParams(window.location.search).get('type') || 'daily';
        if (typeParam === 'monthly') {
            activeMonthBox();
        } else {
            activeDailyBox();
        }
        // Radio button state management
        function updateRadioState(selectedRadio) {
            document.querySelectorAll('.radio-option').forEach(option => {
                option.classList.remove('active');
            });
            selectedRadio.closest('.radio-option').classList.add('active');
        }

        // Date/Month toggle functions  
        function activeDailyBox() {
            const urlParams = new URLSearchParams(window.location.search);
            const currentDate = urlParams.get('date') || '{{ date('Y-m-d') }}';
            document.getElementById('monthAndDate').innerHTML = 
                `<input type="date" name="date" class="form-control" value="${currentDate}">`;
        }

        function activeMonthBox() {
            const urlParams = new URLSearchParams(window.location.search);
            const currentMonth = urlParams.get('month') || '{{ date('Y-m') }}';
            document.getElementById('monthAndDate').innerHTML = 
                `<input type="month" name="month" class="form-control" value="${currentMonth}">`;
        }

        // Form enhancement
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize radio button state
            const checkedRadio = document.querySelector('input[name="type"]:checked');
            if (checkedRadio) {
                updateRadioState(checkedRadio);
            }

            // Form submission loading state
            document.querySelector('.premium-filter-form').addEventListener('submit', function(e) {
                const submitBtn = document.querySelector('.btn-apply');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
            });

            // Enhanced focus effects
            document.querySelectorAll('.form-control').forEach(control => {
                control.addEventListener('focus', function() {
                    this.closest('.filter-group').style.transform = 'translateY(-2px)';
                });
                
                control.addEventListener('blur', function() {
                    this.closest('.filter-group').style.transform = 'translateY(0)';
                });
            });
        });

        function toggleEmployeeAttendance(employeeId) {
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
    </script>
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        // Branch change functionality
        $('select[name="branch"]').on('change', function() {
            var branchId = $(this).val();
            if (branchId) {
                $.ajax({
                    url: '{{ route("getDepartmentsByBranch") }}',
                    type: 'GET',
                    data: { branch_id: branchId },
                    success: function(response) {
                        if (response.status === 'success') {
                            var departmentSelect = $('select[name="department"]');
                            var employeeSelect = $('select[name="employee"]');

                            departmentSelect.empty();
                            employeeSelect.empty();

                            departmentSelect.append('<option value="" disabled selected>Select Department</option>');
                            employeeSelect.append('<option value="" disabled selected>Select Employee</option>');

                            $.each(response.data.departments, function(key, department) {
                                departmentSelect.append('<option value="' + department.id + '">' + department.name + '</option>');
                            });

                            $.each(response.data.employees, function(key, employee) {
                                employeeSelect.append('<option value="' + employee.id + '">' + employee.name + '</option>');
                            });
                        } else {
                            $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                            $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                        }
                    },
                    error: function() {
                        $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                        $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                    }
                });
            } else {
                $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
            }
        });

        // Department change functionality
        $('select[name="department"]').on('change', function() {
            var departmentId = $(this).val();
            if (departmentId) {
                $.ajax({
                    url: '{{ route("getEmployeeByDepartment") }}',
                    type: 'GET',
                    data: { department_id: departmentId },
                    success: function(response) {
                        if (response.status === 'success') {
                            var employeeSelect = $('select[name="employee"]');
                            employeeSelect.empty();
                            employeeSelect.append('<option value="" disabled selected>Select Employee</option>');

                            $.each(response.data.employees, function(key, employee) {
                                employeeSelect.append('<option value="' + employee.id + '">' + employee.name + '</option>');
                            });
                        } else {
                            $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                        }
                    },
                    error: function() {
                        $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                    }
                });
            } else {
                $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
            }
        });

        // Custom search functionality
        $('#search-input').on('keyup', function() {
            var searchTerm = this.value.toLowerCase();
            
            $('#datatable tbody tr').each(function() {
                if (!$(this).hasClass('employee-header-row')) {
                    var rowText = $(this).text().toLowerCase();
                    if (rowText.indexOf(searchTerm) === -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }
            });
        });

        // Custom entries per page (for visual feedback only)
        $('#entries-per-page').on('change', function() {
            var entriesCount = parseInt(this.value);
            var visibleRows = 0;
            
            $('#datatable tbody tr').each(function() {
                if (!$(this).hasClass('employee-header-row') && !$(this).hasClass('collapsible-rows')) {
                    if (visibleRows < entriesCount) {
                        $(this).show();
                        visibleRows++;
                    } else {
                        $(this).hide();
                    }
                }
            });
        });

        // Enhanced button interactions
        $(document).on('click', '.action-btn', function() {
            var $btn = $(this);
            var originalContent = $btn.html();
            $btn.html('<div style="width: 12px; height: 12px; border: 2px solid rgba(255,255,255,.3); border-radius: 50%; border-top-color: #fff; animation: spin 1s ease-in-out infinite;"></div>');
            
            setTimeout(function() {
                $btn.html(originalContent);
            }, 1000);
        });
    });
</script>
@endpush