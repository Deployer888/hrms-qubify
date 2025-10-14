@extends('layouts.admin')
@section('page-title')
    {{ __('Aadhaar Verification') }}
@endsection
@php
use App\Helpers\Helper;
use Carbon\Carbon;
$requestType = isset($_GET['type']) ? $_GET['type'] : 'daily';
@endphp

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
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 20px;
        padding: 20px 28px;
        margin-bottom: 16px;
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
        gap: 16px;
    }

    .header-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        backdrop-filter: blur(10px);
    }

    .header-text h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
    }

    .header-text p {
        color: rgba(255, 255, 255, 0.85);
        margin: 4px 0 0 0;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .header-stats {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .stat-item {
        text-align: center;
        color: white;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        opacity: 0.9;
        margin: 4px 0 0 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Premium Cards - Compact */
    .premium-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
            border: none;
        margin-bottom: 14px;
        height: fit-content;
    }
    .premium-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--secondary));
    }
    .premium-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .card-header-premium {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-subtitle {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin: 4px 0 0 0;
    }

    .premium-card-body {
        padding: 20px;
    }

    /* Main Form Section - Enhanced */
    .main-form-section {
        background: linear-gradient(135deg, #f8fafc, #ffffff);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 16px;
        box-shadow: var(--shadow);
        min-height: 500px;
        display: flex;
        flex-direction: column;
    }

    /* Progress Steps - Compact */
    .verification-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 32px;
        position: relative;
        padding: 0 20px;
    }

    .verification-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 40px;
        right: 40px;
        height: 2px;
        background: #e5e7eb;
        z-index: 1;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        background: white;
        padding: 0 12px;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .step.active .step-icon {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        animation: pulse 2s infinite;
    }

    .step.completed .step-icon {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
    }

    .step-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-align: center;
    }

    .step.active .step-label,
    .step.completed .step-label {
        color: var(--primary);
    }

    /* Form Content Area */
    .form-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .form-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .form-header h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .form-header p {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.6;
    }

    /* Form Styling - Enhanced */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-control {
        border: 3px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px 20px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        background: #fff;
        box-shadow: var(--shadow);
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
        transform: translateY(-2px);
    }

    .form-control:hover {
        border-color: var(--accent);
    }

    .input-group {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        z-index: 5;
        font-size: 1.1rem;
    }

    .input-group .form-control {
        padding-left: 56px;
    }

    /* Enhanced Select Styling */
    .select2-container--default .select2-selection--single {
        border: 3px solid #e5e7eb;
        border-radius: 12px;
        height: 56px;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        box-shadow: var(--shadow);
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    /* Premium Buttons - Enhanced */
    .premium-btn {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        color: white;
        padding: 18px 36px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        min-width: 200px;
        justify-content: center;
    }
    .premium-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    .premium-btn:hover::before {
        left: 100%;
    }
    .premium-btn:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        color: white;
    }
    .premium-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .premium-btn-outline {
        background: transparent;
        border: 3px solid var(--primary);
        color: var(--primary);
        padding: 15px 32px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        min-width: 180px;
        justify-content: center;
    }
    .premium-btn-outline:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-3px);
        box-shadow: var(--shadow);
    }

    /* Sidebar Optimizations */
    .sidebar-section {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .sidebar-card {
        margin-bottom: 0;
        flex: 1;
    }

    .sidebar-card .premium-card-body {
        padding: 16px;
    }

    /* Face Auth Card Enhancement */
    .face-auth-card {
        background: linear-gradient(135deg, #f0f7ff, #e0f1ff);
        border: 2px solid rgba(37, 99, 235, 0.1);
        text-align: center;
        padding: 24px;
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .face-auth-card:hover {
        transform: translateY(-2px);
        border-color: rgba(37, 99, 235, 0.3);
    }

    .face-auth-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 16px;
        box-shadow: var(--shadow);
    }

    /* Info Cards - Compact */
    .info-card {
        background: linear-gradient(135deg, #fff, #f8fafc);
        border-radius: 12px;
        padding: 16px;
        border-left: 4px solid var(--primary);
        transition: all 0.3s ease;
        margin-bottom: 12px;
    }

    .info-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow);
    }

    .info-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .info-card-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 4px 0;
    }

    .info-card-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.4;
        margin: 0;
    }

    /* Activity Items - Compact */
    .activity-item {
        padding: 12px;
        border-left: 3px solid var(--success);
        margin-bottom: 10px;
        background: rgba(16, 185, 129, 0.05);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        transform: translateX(2px);
        background: rgba(16, 185, 129, 0.08);
    }

    .activity-item.pending {
        border-left-color: var(--warning);
        background: rgba(245, 158, 11, 0.05);
    }

    .activity-item.pending:hover {
        background: rgba(245, 158, 11, 0.08);
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    /* Enhanced Instructions */
    .instruction-list {
        background: rgba(37, 99, 235, 0.02);
        border-radius: 12px;
        padding: 16px;
        margin-top: 12px;
    }

    .instruction-item {
        display: flex;
        align-items: start;
        margin-bottom: 12px;
        padding: 8px;
        background: rgba(37, 99, 235, 0.03);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .instruction-item:hover {
        background: rgba(37, 99, 235, 0.06);
        transform: translateX(2px);
    }

    .instruction-number {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        margin-right: 10px;
        flex-shrink: 0;
    }

    .instruction-content h6 {
        margin: 0 0 4px 0;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .instruction-content small {
        color: var(--text-secondary);
        font-size: 0.75rem;
        line-height: 1.4;
    }

    /* Error Messages */
    .text-danger {
        color: var(--danger) !important;
        font-size: 0.875rem;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Responsive Design */
    
    /* Mobile Devices (320px - 767px) */
    @media (max-width: 767px) {
        /* Container optimizations */
        .container-fluid {
            padding: 0 12px;
        }
        
        /* Enhanced Premium Header Mobile Styles */
        .page-header-premium {
            padding: 16px 20px;
            margin-bottom: 12px;
            border-radius: 16px;
        }
        
        .header-content {
            flex-direction: column;
            gap: 12px;
            text-align: center;
            align-items: center;
        }
        
        .header-left {
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }
        
        .header-icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
            border-radius: 12px;
        }
        
        .header-text h1 {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.3;
        }
        
        .header-text p {
            font-size: 0.875rem;
            margin: 2px 0 0 0;
        }
        
        .header-stats {
            gap: 16px;
            margin-top: 8px;
        }
        
        .stat-item {
            min-width: 60px;
        }
        
        .stat-number {
            font-size: 1.25rem;
        }
        
        .stat-label {
            font-size: 0.7rem;
        }
        
        /* Enhanced Main Form Section Mobile Styles */
        .main-form-section {
            padding: 18px 16px;
            margin-bottom: 12px;
            border-radius: 12px;
            min-height: auto;
        }
        
        .form-header {
            margin-bottom: 24px;
        }
        
        .form-header h2 {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }
        
        .form-header p {
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        /* Touch-Friendly Form Controls */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-size: 0.9rem;
            margin-bottom: 8px;
            gap: 8px;
        }
        
        .form-control {
            padding: 14px 18px;
            font-size: 16px; /* Prevents zoom on iOS */
            min-height: 48px;
            border-width: 2px;
            border-radius: 10px;
        }
        
        .form-control:focus {
            transform: none; /* Remove transform on mobile for better performance */
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .input-group .form-control {
            padding-left: 48px;
        }
        
        .input-icon {
            left: 16px;
            font-size: 1rem;
        }
        
        /* Select2 Mobile Optimization */
        .select2-container--default .select2-selection--single {
            height: 48px;
            padding: 10px 16px;
            border-width: 2px;
            border-radius: 10px;
            font-size: 16px;
        }
        
        /* Enhanced Verification Steps Mobile Layout */
        .verification-steps {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            padding: 0 0 0 24px;
            margin-bottom: 24px;
        }
        
        .verification-steps::before {
            display: block;
            width: 2px;
            height: calc(100% - 60px);
            left: 20px;
            top: 40px;
            right: auto;
            background: linear-gradient(180deg, #e5e7eb 0%, rgba(229, 231, 235, 0.3) 100%);
        }
        
        .step {
            flex-direction: row;
            align-items: center;
            padding: 0;
            background: transparent;
            width: 100%;
            text-align: left;
        }
        
        .step-icon {
            width: 36px;
            height: 36px;
            margin-bottom: 0;
            margin-right: 12px;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .step-label {
            font-size: 0.875rem;
            text-align: left;
            margin: 0;
            flex: 1;
        }

        /* Enhanced Button Mobile Styles */
        .premium-btn,
        .premium-btn-outline {
            width: 100%;
            margin: 8px 0;
            padding: 14px 28px;
            font-size: 1rem;
            min-height: 48px;
            border-radius: 12px;
            gap: 10px;
        }
        
        .premium-btn {
            padding: 14px 28px;
        }
        
        .premium-btn-outline {
            padding: 12px 26px;
            border-width: 2px;
        }
        
        /* Touch-friendly interactive elements */
        .premium-card:hover {
            transform: none; /* Disable hover transforms on mobile */
        }
        
        .info-card:hover {
            transform: none;
        }
        
        .face-auth-card:hover {
            transform: none;
        }
        
        /* Mobile Quick Guide and Instructions */
        .instruction-list {
            padding: 12px;
            margin-top: 10px;
        }
        
        .instruction-item {
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        
        .instruction-item:hover {
            transform: none;
        }
        
        .instruction-number {
            width: 22px;
            height: 22px;
            font-size: 0.7rem;
            margin-right: 8px;
        }
        
        .instruction-content h6 {
            font-size: 0.8rem;
            margin-bottom: 3px;
        }
        
        .instruction-content small {
            font-size: 0.7rem;
            line-height: 1.3;
        }
        
        /* Mobile Error Message Enhancements */
        .text-danger {
            font-size: 0.8rem;
            margin-top: 6px;
            gap: 5px;
            padding: 6px 8px;
            background: rgba(239, 68, 68, 0.05);
            border-radius: 6px;
            border-left: 3px solid var(--danger);
        }
        
        .text-danger i {
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        /* Enhanced Sidebar Mobile Layout */
        .sidebar-section {
            margin-top: 16px;
            gap: 12px;
        }
        
        .sidebar-card {
            margin-bottom: 12px;
        }
        
        .sidebar-card .premium-card-body {
            padding: 14px;
        }
        
        .face-auth-card {
            padding: 18px;
            border-radius: 12px;
        }
        
        .face-auth-icon {
            width: 56px;
            height: 56px;
            font-size: 1.375rem;
            margin-bottom: 12px;
        }
        
        .card-title {
            font-size: 1rem;
            margin-bottom: 8px;
        }
        
        .premium-card-body {
            padding: 16px;
        }
        
        /* Face Auth Card Mobile Enhancements */
        .face-auth-card h4 {
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        
        .face-auth-card p {
            font-size: 0.875rem;
            margin-bottom: 16px;
            line-height: 1.4;
        }
        
        .face-auth-card .premium-btn-outline {
            padding: 12px 24px;
            font-size: 0.95rem;
            min-width: auto;
            width: 100%;
        }
    }
    
    /* Small Mobile Devices (320px - 479px) */
    @media (max-width: 479px) {
        .container-fluid {
            padding: 0 8px;
        }
        
        .page-header-premium {
            padding: 14px 16px;
            border-radius: 12px;
        }
        
        .header-icon {
            width: 44px;
            height: 44px;
            font-size: 1.1rem;
        }
        
        .header-text h1 {
            font-size: 1.375rem;
        }
        
        .header-text p {
            font-size: 0.8rem;
        }
        
        .header-stats {
            gap: 12px;
        }
        
        .stat-number {
            font-size: 1.125rem;
        }
        
        .stat-label {
            font-size: 0.65rem;
        }
        
        /* Small Mobile Verification Steps */
        .verification-steps {
            padding: 0 0 0 20px;
            margin-bottom: 20px;
        }
        
        .verification-steps::before {
            left: 18px;
        }
        
        .step-icon {
            width: 32px;
            height: 32px;
            margin-right: 10px;
            font-size: 0.8rem;
        }
        
        .step-label {
            font-size: 0.8rem;
        }
        
        /* Small Mobile Form Optimizations */
        .main-form-section {
            padding: 16px 12px;
        }
        
        .form-header h2 {
            font-size: 1.375rem;
        }
        
        .form-header p {
            font-size: 0.875rem;
        }
        
        .form-control {
            padding: 12px 16px;
            min-height: 44px;
        }
        
        .input-group .form-control {
            padding-left: 44px;
        }
        
        .input-icon {
            left: 14px;
            font-size: 0.9rem;
        }
        
        .select2-container--default .select2-selection--single {
            height: 44px;
            padding: 8px 14px;
        }
        
        /* Small Mobile Sidebar Optimizations */
        .sidebar-section {
            margin-top: 12px;
            gap: 10px;
        }
        
        .sidebar-card .premium-card-body {
            padding: 12px;
        }
        
        .face-auth-card {
            padding: 16px;
        }
        
        .face-auth-icon {
            width: 52px;
            height: 52px;
            font-size: 1.25rem;
            margin-bottom: 10px;
        }
        
        .card-title {
            font-size: 0.95rem;
        }
        
        /* Small Mobile Face Auth Enhancements */
        .face-auth-card h4 {
            font-size: 1rem;
        }
        
        .face-auth-card p {
            font-size: 0.8rem;
            margin-bottom: 14px;
        }
        
        .face-auth-card .premium-btn-outline {
            padding: 10px 20px;
            font-size: 0.9rem;
        }
        
        /* Security Features Mobile Layout */
        .premium-card-body[style*="display: flex"] {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            justify-content: flex-start !important;
        }
        
        .info-card {
            margin-bottom: 8px;
            padding: 12px;
        }
        
        .info-card-icon {
            width: 32px;
            height: 32px;
            margin-bottom: 8px;
            font-size: 1rem;
        }
        
        .info-card-title {
            font-size: 0.85rem;
            margin-bottom: 3px;
        }
        
        .info-card-desc {
            font-size: 0.75rem;
            line-height: 1.3;
        }
        
        /* Small Mobile Security Features */
        .info-card {
            padding: 10px;
        }
        
        .info-card-icon {
            width: 28px;
            height: 28px;
            font-size: 0.9rem;
        }
        
        .info-card-title {
            font-size: 0.8rem;
        }
        
        .info-card-desc {
            font-size: 0.7rem;
        }
        
        /* Small Mobile Button Enhancements */
        .premium-btn,
        .premium-btn-outline {
            padding: 12px 24px;
            font-size: 0.95rem;
            min-height: 44px;
            border-radius: 10px;
        }
        
        .premium-btn-outline {
            padding: 10px 22px;
        }
        
        /* Small Mobile Instructions */
        .instruction-list {
            padding: 10px;
        }
        
        .instruction-item {
            padding: 6px;
            margin-bottom: 8px;
        }
        
        .instruction-number {
            width: 20px;
            height: 20px;
            font-size: 0.65rem;
            margin-right: 6px;
        }
        
        .instruction-content h6 {
            font-size: 0.75rem;
        }
        
        .instruction-content small {
            font-size: 0.65rem;
        }
        
        /* Small Mobile Error Messages */
        .text-danger {
            font-size: 0.75rem;
            margin-top: 5px;
            padding: 5px 6px;
        }
        
        .text-danger i {
            font-size: 0.7rem;
        }
    }
    
    /* Tablet Devices (768px - 1023px) */
    @media (min-width: 768px) and (max-width: 1023px) {
        .page-header-premium {
            padding: 18px 24px;
            border-radius: 18px;
        }
        
        .header-content {
            gap: 14px;
        }
        
        .header-left {
            gap: 14px;
        }
        
        .header-icon {
            width: 52px;
            height: 52px;
            font-size: 1.375rem;
            border-radius: 14px;
        }
        
        .header-text h1 {
            font-size: 1.625rem;
        }
        
        .header-text p {
            font-size: 0.9rem;
        }
        
        .header-stats {
            gap: 18px;
        }
        
        /* Tablet Verification Steps Optimization */
        .verification-steps {
            padding: 0 16px;
            margin-bottom: 28px;
        }
        
        .verification-steps::before {
            left: 36px;
            right: 36px;
        }
        
        .step {
            padding: 0 10px;
        }
        
        .step-icon {
            width: 38px;
            height: 38px;
            font-size: 0.95rem;
        }
        
        .step-label {
            font-size: 0.8rem;
        }
        
        /* Tablet Form Section Optimization */
        .main-form-section {
            padding: 24px 20px;
            border-radius: 14px;
        }
        
        .form-header h2 {
            font-size: 1.7rem;
        }
        
        .form-header p {
            font-size: 1.05rem;
        }
        
        .form-control {
            padding: 15px 19px;
            font-size: 1.05rem;
            min-height: 52px;
        }
        
        .input-group .form-control {
            padding-left: 52px;
        }
        
        .input-icon {
            left: 17px;
            font-size: 1.05rem;
        }
        
        .select2-container--default .select2-selection--single {
            height: 52px;
            padding: 11px 19px;
        }
        
        /* Tablet Sidebar Optimization */
        .sidebar-section {
            gap: 16px;
        }
        
        .sidebar-card .premium-card-body {
            padding: 15px;
        }
        
        .face-auth-card {
            padding: 20px;
            border-radius: 14px;
        }
        
        .face-auth-icon {
            width: 60px;
            height: 60px;
            font-size: 1.4rem;
            margin-bottom: 14px;
        }
        
        .card-title {
            font-size: 1.05rem;
        }
        
        /* Tablet Face Auth Enhancements */
        .face-auth-card h4 {
            font-size: 1.15rem;
            margin-bottom: 10px;
        }
        
        .face-auth-card p {
            font-size: 0.9rem;
            margin-bottom: 18px;
        }
        
        .face-auth-card .premium-btn-outline {
            padding: 13px 28px;
            font-size: 1rem;
        }
        
        /* Tablet Security Features Layout */
        .premium-card-body[style*="display: flex"] {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            gap: 20px !important;
            justify-content: space-between !important;
        }
        
        .info-card {
            flex: 1 1 calc(50% - 10px);
            margin-bottom: 10px;
            padding: 14px;
        }
        
        .info-card:nth-child(3) {
            flex: 1 1 100%;
            max-width: 100%;
        }
        
        .info-card-icon {
            width: 34px;
            height: 34px;
            font-size: 1.05rem;
        }
        
        .info-card-title {
            font-size: 0.875rem;
        }
        
        .info-card-desc {
            font-size: 0.775rem;
        }
        
        /* Additional Tablet Optimizations */
        .container-fluid {
            padding: 0 20px;
        }
        
        .premium-card {
            border-radius: 14px;
        }
        
        .card-header-premium {
            padding: 15px 18px;
        }
        
        .instruction-list {
            padding: 15px;
        }
        
        .instruction-item {
            padding: 9px;
            margin-bottom: 11px;
        }
        
        .instruction-number {
            width: 26px;
            height: 26px;
            font-size: 0.8rem;
        }
        
        .instruction-content h6 {
            font-size: 0.9rem;
        }
        
        .instruction-content small {
            font-size: 0.8rem;
        }
        
        /* Tablet Button Enhancements */
        .premium-btn,
        .premium-btn-outline {
            padding: 16px 32px;
            font-size: 1.05rem;
            min-height: 52px;
            border-radius: 14px;
            min-width: 180px;
        }
        
        .premium-btn-outline {
            padding: 14px 30px;
        }
        
        /* Re-enable hover effects for tablet */
        .premium-card:hover {
            transform: translateY(-2px);
        }
        
        .info-card:hover {
            transform: translateX(4px);
        }
        
        .face-auth-card:hover {
            transform: translateY(-2px);
        }
        
        /* Tablet Error Message Optimization */
        .text-danger {
            font-size: 0.85rem;
            margin-top: 7px;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 8px;
        }
        
        .text-danger i {
            font-size: 0.8rem;
        }
    }
    
    /* Large Desktop (1200px - 1399px) */
    @media (min-width: 1200px) and (max-width: 1399px) {
        .container-fluid {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        .page-header-premium {
            padding: 22px 32px;
        }
        
        .main-form-section {
            padding: 32px 28px;
        }
        
        .form-header h2 {
            font-size: 1.9rem;
        }
        
        .premium-btn,
        .premium-btn-outline {
            min-width: 220px;
        }
    }
    
    /* Extra Large Desktop (1400px+) */
    @media (min-width: 1400px) {
        .container-fluid {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .page-header-premium {
            padding: 24px 36px;
        }
        
        .header-icon {
            width: 60px;
            height: 60px;
            font-size: 1.6rem;
        }
        
        .header-text h1 {
            font-size: 1.875rem;
        }
        
        .main-form-section {
            padding: 36px 32px;
            max-width: none;
        }
        
        .form-header h2 {
            font-size: 2rem;
        }
        
        .form-header p {
            font-size: 1.15rem;
        }
        
        .premium-btn,
        .premium-btn-outline {
            min-width: 240px;
            padding: 20px 40px;
            font-size: 1.15rem;
        }
        
        .sidebar-section {
            gap: 18px;
        }
        
        .face-auth-icon {
            width: 68px;
            height: 68px;
            font-size: 1.6rem;
        }
        
        /* Prevent excessive stretching on ultra-wide screens */
        .row-equal-height {
            max-width: 1600px;
            margin: 0 auto;
        }
    }
    
    /* Performance and Compatibility Optimizations */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
    
    /* High DPI Display Optimizations */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .header-icon,
        .step-icon,
        .face-auth-icon,
        .info-card-icon,
        .instruction-number {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    }
    
    /* Print Styles */
    @media print {
        .page-header-premium,
        .sidebar-section,
        .premium-btn,
        .premium-btn-outline {
            display: none !important;
        }
        
        .main-form-section {
            box-shadow: none;
            border: 1px solid #ccc;
        }
    }

    /* Animation */
    .fade-in {
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Equal height columns */
    .row-equal-height {
        display: flex;
        flex-wrap: wrap;
    }

    .row-equal-height > [class*="col-"] {
        display: flex;
        flex-direction: column;
    }
</style>
@endpush

@section('action-button')
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Premium Header --}}
<div class="page-header-premium">
    <div class="header-content">
        <div class="header-left">
            <div class="header-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="header-text">
                <h1>{{ __('Aadhaar Verification') }}</h1>
                <p>{{ __('Secure Identity Authentication System') }}</p>
            </div>
        </div>
        {{-- <!-- <div class="header-stats">
            <div class="stat-item">
                <p class="stat-number">{{ $statistics['verified'] ?? 150 }}</p>
                <p class="stat-label">{{ __('Verified') }}</p>
            </div>
            <div class="stat-item">
                <p class="stat-number">{{ $statistics['pending'] ?? 12 }}</p>
                <p class="stat-label">{{ __('Pending') }}</p>
            </div>
            <div class="stat-item">
                <p class="stat-number">{{ $statistics['success_rate'] ?? 98 }}%</p>
                <p class="stat-label">{{ __('Success Rate') }}</p>
            </div>
        </div> --> --}}
    </div>
</div>

<div class="container-fluid" id="mainDiv">
    <div class="row row-equal-height">
        <!-- Main Form Section -->
        <div class="col-lg-8">
            {{-- Verification Steps --}}
            <div class="premium-card fade-in">
                <div class="premium-card-body" style="padding: 16px 20px;">
                    <div class="verification-steps">
                        <div class="step active">
                            <div class="step-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="step-label">{{ __('Select Employee') }}</span>
                        </div>
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <span class="step-label">{{ __('Enter Aadhaar') }}</span>
                        </div>
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <span class="step-label">{{ __('OTP Verification') }}</span>
                        </div>
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <span class="step-label">{{ __('Verified') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Form Section --}}
            <div class="main-form-section fade-in">
                <div class="form-header">
                    <h2>{{ __('Identity Verification') }}</h2>
                    <p>{{ __('Please select an employee and enter their Aadhaar number to begin the secure verification process.') }}</p>
                </div>

                <div class="form-content">
                    <form action="{{ route('aadhaar.send.otp') }}" method="POST" id="verificationForm">
                        @csrf
                        <div id="aadhaarForm">
                            {{-- Employee Selection --}}
                            <div class="form-group">
                                <label for="employeeId" class="form-label">
                                    <i class="fas fa-user text-primary"></i>
                                    {{ __('Select Employee') }}
                                </label>
                                <select id="employeeId" name="employee_id" class="form-control select2" required>
                                    <option value="">{{ __('Choose an employee...') }}</option>
                                    @foreach($emp_list as $key => $emp)
                                        <option value="{{ $key}}">{{ $emp }}</option>
                                    @endforeach
                                </select>
                                <p id="employeeIdError" class="text-danger d-none">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span></span>
                                </p>
                            </div>

                            {{-- Aadhaar Number Input --}}
                            <div class="form-group">
                                <label for="aadharNumber" class="form-label">
                                    <i class="fas fa-id-card text-primary"></i>
                                    {{ __('Aadhaar Number') }}
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-hashtag input-icon"></i>
                                    <input type="text" 
                                           id="aadharNumber" 
                                           name="aadhar_number" 
                                           class="form-control" 
                                           placeholder="{{ __('Enter 12-digit Aadhaar Number') }}" 
                                           maxlength="12"
                                           required>
                                </div>
                                <p id="aadharNumberError" class="text-danger d-none">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span></span>
                                </p>
                            </div>

                            {{-- Submit Button --}}
                            <div class="text-center mt-4">
                                <button id="sendOtpButton" type="submit" class="premium-btn" disabled>
                                    <i class="fas fa-search"></i>
                                    <span id="buttonText">{{ __('Verify Details') }}</span>
                                    <span id="sendOtpSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="col-lg-4 sidebar-section">
            {{-- Face Authentication --}}
            <div class="premium-card sidebar-card fade-in">
                <div class="premium-card-body face-auth-card">
                    <div class="face-auth-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h4 class="card-title mb-2">{{ __('Face Authentication') }}</h4>
                    <p class="text-muted mb-3">{{ __('Biometric face verification method using advanced facial recognition technology') }}</p>
                    <a href="{{ route('aadhaar.face.authenticate') }}" target="_blank" class="premium-btn-outline w-100" id="face_authenticate">
                        <i class="fas fa-camera"></i>
                        {{ __('Start Face Auth') }}
                    </a>
                </div>
            </div>

            {{-- Quick Instructions --}}
            <div class="premium-card sidebar-card fade-in">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-list-check text-info"></i>
                        {{ __('Quick Guide') }}
                    </h3>
                </div>
                <div class="premium-card-body">
                    <div class="instruction-list">
                        <div class="instruction-item">
                            <div class="instruction-number">1</div>
                            <div class="instruction-content">
                                <h6>{{ __('Select Employee') }}</h6>
                                <small>{{ __('Choose the employee from the dropdown menu') }}</small>
                            </div>
                        </div>
                        <div class="instruction-item">
                            <div class="instruction-number">2</div>
                            <div class="instruction-content">
                                <h6>{{ __('Enter Aadhaar') }}</h6>
                                <small>{{ __('Input the 12-digit Aadhaar number') }}</small>
                            </div>
                        </div>
                        <div class="instruction-item">
                            <div class="instruction-number">3</div>
                            <div class="instruction-content">
                                <h6>{{ __('Verify Details') }}</h6>
                                <small>{{ __('System will send OTP for verification') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
                    {{-- Security Info --}}
                    <div class="premium-card sidebar-card fade-in mt-5">
                        <div class="card-header-premium">
                            <h3 class="card-title">
                                <i class="fas fa-shield-alt text-success"></i>
                                {{ __('Security Features') }}
                            </h3>
                        </div>
                        <div class="premium-card-body" style="display: flex; justify-content: center; gap: 110px;">
                            <div class="info-card">
                                <div class="info-card-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <h4 class="info-card-title">{{ __('Encrypted Data') }}</h4>
                                <p class="info-card-desc">{{ __('All verification data is encrypted using AES-256 standards') }}</p>
                            </div>
        
                            <div class="info-card">
                                <div class="info-card-icon" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <h4 class="info-card-title">{{ __('UIDAI Compliance') }}</h4>
                                <p class="info-card-desc">{{ __('Fully compliant with UIDAI guidelines and regulations') }}</p>
                            </div>
        
                            <div class="info-card">
                                <div class="info-card-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h4 class="info-card-title">{{ __('Real-time Verification') }}</h4>
                                <p class="info-card-desc">{{ __('Instant verification with live UIDAI database connection') }}</p>
                            </div>
                        </div>
                    </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('verificationForm');
        const aadharNumberInput = document.getElementById('aadharNumber');
        const aadharNumberError = document.getElementById('aadharNumberError');
        const employeeIdSelect = document.getElementById('employeeId');
        const employeeIdError = document.getElementById('employeeIdError');
        const sendOtpButton = document.getElementById('sendOtpButton');
        const sendOtpSpinner = document.getElementById('sendOtpSpinner');
        const buttonText = document.getElementById('buttonText');
        const steps = document.querySelectorAll('.step');
        
        // Initialize Select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                placeholder: "{{ __('Choose an employee...') }}",
                allowClear: true,
                theme: 'default'
            });
        }

        // Update verification steps
        function updateSteps(activeStep) {
            steps.forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index < activeStep) {
                    step.classList.add('completed');
                } else if (index === activeStep) {
                    step.classList.add('active');
                }
            });
        }
        
        // Add input validation for Aadhaar number
        aadharNumberInput.addEventListener('input', function() {
            const value = this.value.replace(/\D/g, '');
            this.value = value;
            
            if (value.length !== 0 && value.length !== 12) {
                showError(aadharNumberError, '{{ __("Aadhaar number must be 12 digits") }}');
                this.style.borderColor = 'var(--danger)';
                updateSteps(0);
            } else if (value.length === 12) {
                hideError(aadharNumberError);
                this.style.borderColor = 'var(--success)';
                updateSteps(1);
            } else {
                hideError(aadharNumberError);
                this.style.borderColor = '#e5e7eb';
                updateSteps(0);
            }
            updateButtonState();
        });

        // Add validation for employee selection
        employeeIdSelect.addEventListener('change', function() {
            if (this.value === '') {
                showError(employeeIdError, '{{ __("Please select an employee") }}');
                updateSteps(0);
            } else {
                hideError(employeeIdError);
                // Check if Aadhaar is also valid
                if (aadharNumberInput.value.length === 12) {
                    updateSteps(1);
                } else {
                    updateSteps(0);
                }
            }
            updateButtonState();
        });

        function showError(errorElement, message) {
            errorElement.querySelector('span').textContent = message;
            errorElement.classList.remove('d-none');
        }

        function hideError(errorElement) {
            errorElement.classList.add('d-none');
        }

        // Update button state
        function updateButtonState() {
            const isEmployeeSelected = employeeIdSelect.value !== '';
            const isAadharValid = aadharNumberInput.value.length === 12;
            
            if (isEmployeeSelected && isAadharValid) {
                sendOtpButton.disabled = false;
                sendOtpButton.style.opacity = '1';
            } else {
                sendOtpButton.disabled = true;
                sendOtpButton.style.opacity = '0.6';
            }
        }

        // Handle form submission
        form.addEventListener('submit', function(event) {
            if (employeeIdSelect.value === '') {
                event.preventDefault();
                showError(employeeIdError, '{{ __("Please select an employee") }}');
                employeeIdSelect.focus();
                return false;
            }

            if (aadharNumberInput.value.length !== 12) {
                event.preventDefault();
                showError(aadharNumberError, '{{ __("Please enter a valid 12-digit Aadhaar number") }}');
                aadharNumberInput.style.borderColor = 'var(--danger)';
                aadharNumberInput.focus();
                return false;
            }
            
            // Show loading state
            sendOtpButton.disabled = true;
            sendOtpSpinner.classList.remove('d-none');
            buttonText.textContent = '{{ __("Processing...") }}';
            updateSteps(2);
            
            return true;
        });

        // Number only input
        aadharNumberInput.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });

        // Add focus effects (responsive aware)
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                // Only apply transform on desktop devices
                if (window.innerWidth > 767) {
                    this.style.transform = 'translateY(-2px)';
                }
            });
            
            input.addEventListener('blur', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Responsive Manager for orientation and resize handling
        const ResponsiveManager = {
            breakpoints: {
                mobile: 767,
                tablet: 1023,
                desktop: 1200
            },
            
            getCurrentBreakpoint() {
                const width = window.innerWidth;
                if (width <= this.breakpoints.mobile) return 'mobile';
                if (width <= this.breakpoints.tablet) return 'tablet';
                return 'desktop';
            },
            
            handleResize() {
                // Reinitialize Select2 on resize to fix positioning
                if (typeof $.fn.select2 !== 'undefined') {
                    $('.select2').select2('close');
                    setTimeout(() => {
                        $('.select2').select2({
                            placeholder: "{{ __('Choose an employee...') }}",
                            allowClear: true,
                            theme: 'default'
                        });
                    }, 100);
                }
                
                // Update focus effects based on screen size
                document.querySelectorAll('.form-control').forEach(input => {
                    if (window.innerWidth <= 767) {
                        input.style.transform = 'translateY(0)';
                    }
                });
            },
            
            handleOrientationChange() {
                // Preserve form state during orientation change
                const formData = {
                    employeeId: employeeIdSelect.value,
                    aadharNumber: aadharNumberInput.value
                };
                
                setTimeout(() => {
                    // Restore form state
                    if (formData.employeeId) {
                        employeeIdSelect.value = formData.employeeId;
                        if (typeof $.fn.select2 !== 'undefined') {
                            $(employeeIdSelect).trigger('change');
                        }
                    }
                    if (formData.aadharNumber) {
                        aadharNumberInput.value = formData.aadharNumber;
                    }
                    
                    // Revalidate form
                    updateButtonState();
                    
                    // Recalculate layouts
                    this.handleResize();
                }, 200);
            }
        };

        // Add resize event listener
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                ResponsiveManager.handleResize();
            }, 150);
        });

        // Add orientation change event listener
        window.addEventListener('orientationchange', function() {
            ResponsiveManager.handleOrientationChange();
        });

        // Initialize
        updateButtonState();
        updateSteps(0);
        ResponsiveManager.handleResize(); // Initial setup
    });
</script>
@endsection