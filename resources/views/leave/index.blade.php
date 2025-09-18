@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Leave') }}
@endsection

@push('css-page')
<style>
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary: #3b82f6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
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
        
        /* Responsive Breakpoints */
        --breakpoint-xs: 575.98px;
        --breakpoint-sm: 767.98px;
        --breakpoint-md: 991.98px;
        --breakpoint-lg: 1199.98px;
        --breakpoint-xl: 1200px;
        
        /* Mobile-optimized spacing */
        --mobile-padding: 1rem;
        --mobile-margin: 0.75rem;
        --mobile-gap: 0.5rem;
        
        /* Touch-friendly sizes */
        --touch-target-min: 44px;
        --mobile-font-base: 0.9rem;
        --mobile-font-small: 0.8rem;
    }

    /* Responsive Foundation - Ensure proper mobile rendering */
    * {
        box-sizing: border-box;
    }

    html {
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
    }

    body {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* CSS Grid Fallback System for Older Browsers */
    .stats-compact {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 24px;
    }

    .stats-compact > * {
        flex: 1 1 250px;
        min-width: 250px;
    }

    /* Progressive Enhancement with CSS Grid */
    @supports (display: grid) {
        .stats-compact {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .stats-compact > * {
            flex: none;
            min-width: auto;
        }
    }

    /* Responsive Base Utilities */
    .container-fluid {
        padding-left: var(--mobile-padding);
        padding-right: var(--mobile-padding);
    }

    /* Performance Optimizations */
    .premium-card,
    .stat-card-compact,
    .action-btn {
        will-change: transform;
        transform: translateZ(0); /* Force GPU acceleration */
    }

    /* Touch-friendly base styles */
    @media (hover: none) and (pointer: coarse) {
        .action-btn:hover {
            transform: none; /* Disable hover effects on touch devices */
        }
        
        .stat-card-compact:hover {
            transform: none;
        }
    }

    /* Premium Header Section */
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

    .premium-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 1.5rem;
        position: relative;
        z-index: 1;
        float: inline-end;
    }
    
    .premium-btn {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .premium-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        color: white;
        text-decoration: none;
    }
    
    .premium-btn-primary {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        border: none;
    }
    
    .premium-btn-primary:hover {
        background: linear-gradient(45deg, #ee5a24, #ff6b6b);
        transform: translateY(-2px);
        color: white;
    }

    /* Enhanced Statistics Cards with Responsive Grid */
    .stats-compact {
        margin-bottom: 24px;
    }

    /* Desktop Grid (4 columns) */
    @media (min-width: 992px) {
        .stats-compact {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Tablet Grid (2 columns) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .stats-compact {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
    }

    /* Mobile Grid (1 column) */
    @media (max-width: 767.98px) {
        .stats-compact {
            grid-template-columns: 1fr;
            gap: var(--mobile-gap);
            margin-bottom: var(--mobile-margin);
        }
    }

    .stat-card-compact {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        min-height: 120px;
    }

    /* Tablet Stat Cards */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .stat-card-compact {
            padding: 1.25rem;
            border-radius: 10px;
        }
        
        .stat-number-compact {
            font-size: 1.75rem;
        }
        
        .stat-icon-compact {
            width: 42px;
            height: 42px;
            font-size: 1.3rem;
        }
    }

    /* Mobile Stat Cards */
    @media (max-width: 767.98px) {
        .stat-card-compact {
            padding: var(--mobile-padding);
            border-radius: 8px;
            min-height: 100px;
        }
        
        .stat-number-compact {
            font-size: 1.5rem;
        }
        
        .stat-label-compact {
            font-size: 0.7rem;
        }
        
        .stat-icon-compact {
            width: 36px;
            height: 36px;
            font-size: 1.2rem;
        }
    }

    /* Extra Small Devices */
    @media (max-width: 575.98px) {
        .stat-card-compact {
            padding: 0.75rem;
            min-height: 90px;
        }
        
        .stat-content {
            gap: 0.5rem;
        }
        
        .stat-number-compact {
            font-size: 1.25rem;
        }
        
        .stat-label-compact {
            font-size: 0.65rem;
        }
        
        .stat-icon-compact {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }
    }

    .stat-card-compact::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .stat-card-compact:hover::before {
        opacity: 1;
    }

    .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-number-compact {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin: 0;
        line-height: 1;
    }

    .stat-label-compact {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 4px 0 0 0;
    }

    .stat-icon-compact {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }

    .stat-card-compact:hover .stat-icon-compact {
        transform: scale(1.1);
    }

    /* Premium Card */
    .premium-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }

    /* Enhanced Premium Table Container with Mobile Optimization */
    .premium-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-height: 600px;
        overflow-y: auto;
        position: relative;
        scrollbar-width: thin;
        scrollbar-color: var(--primary) var(--light);
    }

    .premium-table-container .premium-table,
    .premium-table-container .premium-table thead,
    .premium-table-container .premium-table tbody {
        width: 100% !important;
        min-width: 100% !important;
    }

    .premium-table th,
    .premium-table td {
        white-space: nowrap;
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .premium-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        border-top: none;
    }

    /* Tablet Table Optimizations */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .premium-table-container {
            border-radius: 10px;
            max-height: 550px;
        }
        
        .premium-table {
            font-size: 0.85rem;
        }
        
        .premium-table th,
        .premium-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .premium-table thead th {
            padding: 1.25rem 0.75rem;
            font-size: 0.8rem;
        }
        
        .premium-table tbody td {
            padding: 1rem 0.75rem;
            font-size: 0.85rem;
        }
    }

    /* Mobile Table Enhancements */
    @media (max-width: 767.98px) {
        .premium-table-container {
            border-radius: 0;
            box-shadow: none;
            max-height: 70vh;
            margin: 0 calc(-1 * var(--mobile-padding));
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        
        .premium-table {
            min-width: 900px; /* Ensures horizontal scroll */
            font-size: var(--mobile-font-small);
        }
        
        .premium-table th,
        .premium-table td {
            padding: 0.75rem 0.4rem;
            vertical-align: middle;
            text-align: center;
        }
        
        .premium-table thead th {
            padding: 1rem 0.4rem;
            font-size: 0.7rem;
            position: sticky;
            top: 0;
            z-index: 100;
            white-space: nowrap;
        }
        
        .premium-table tbody td {
            padding: 0.875rem 0.4rem;
            font-size: var(--mobile-font-small);
            line-height: 1.3;
        }
        
        /* Mobile scroll helper */
        .premium-table-container::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 30px;
            height: 100%;
            background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
            pointer-events: none;
            z-index: 10;
        }
        
        /* Improved mobile table cell content */
        .premium-table tbody td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        /* Mobile-specific text truncation */
        .premium-table tbody td:nth-child(8) { /* Leave Reason */
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
            padding-left: 0.6rem;
        }
        
        /* Mobile date formatting */
        .premium-table tbody td:nth-child(3),
        .premium-table tbody td:nth-child(4),
        .premium-table tbody td:nth-child(5) {
            font-size: 0.7rem;
            line-height: 1.2;
        }
    }

    /* Extra Small Devices */
    @media (max-width: 575.98px) {
        .premium-table-container {
            max-height: 450px;
        }
        
        .premium-table {
            min-width: 750px;
            font-size: 0.75rem;
        }
        
        .premium-table th,
        .premium-table td {
            padding: 0.5rem 0.375rem;
            min-width: 70px;
        }
        
        .premium-table thead th {
            padding: 0.75rem 0.375rem;
            font-size: 0.7rem;
        }
        
        .premium-table tbody td {
            padding: 0.625rem 0.375rem;
            font-size: 0.75rem;
        }
    }
    
    .premium-table {
        margin-bottom: 0 !important;
        border-collapse: collapse;
        table-layout: auto;
        background-color: #fff;
    }
    
    .premium-table thead {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);
    }
    
    .premium-table thead th {
        font-weight: 700;
        color: #4a5568;
        padding: 1.5rem 1rem;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        position: sticky;
        top: 0;
        z-index: 100;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(59, 130, 246, 0.95) 100%);
        color: white !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
    }
    
    .premium-table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    }
    
    .premium-table tbody tr {
        transition: all 0.3s ease;
        border: none;
    }
    
    .premium-table tbody tr:nth-child(even) {
        background-color: rgba(37, 99, 235, 0.02);
    }
    
    .premium-table tbody tr:hover {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        transform: scale(1.001);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }
    
    .premium-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
        color: #2d3748;
        font-size: 0.9rem;
        font-weight: 500;
        text-align: center;
    }
    
    .premium-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #e2e8f0;
    }

    /* Employee Header Row */
    .employee-header-row {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(59, 130, 246, 0.95) 100%) !important;
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
        color: white !important;
        border-bottom: 2px solid rgba(255,255,255,0.2) !important;
    }

    /* Responsive Employee Header Sections */
    .employee-name-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 96%;
        flex-wrap: wrap;
    }

    .employee-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
        min-width: 200px;
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
        flex-shrink: 0;
    }

    .employee-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .leave-counter {
        background: rgba(255,255,255,0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
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
        min-height: var(--touch-target-min);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .view-more-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: scale(1.05);
    }

    /* Tablet Employee Headers */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .employee-header-row td {
            padding: 1.25rem !important;
            font-size: 1rem !important;
        }
        
        .employee-name-section {
            gap: 1rem;
        }
        
        .employee-avatar {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }
        
        .employee-controls {
            gap: 0.75rem;
        }
        
        .leave-counter,
        .view-more-btn {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
        }
    }

    /* Mobile Employee Headers */
    @media (max-width: 767.98px) {
        .employee-header-row td {
            padding: 1rem var(--mobile-padding) !important;
            font-size: var(--mobile-font-base) !important;
        }
        
        .employee-name-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            width: 100%;
        }
        
        .employee-info {
            width: 100%;
            min-width: auto;
            gap: 0.75rem;
        }
        
        .employee-info > div:last-child {
            flex: 1;
        }
        
        .employee-avatar {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .employee-controls {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 0.75rem;
            width: 100%;
            justify-content: flex-start;
        }
        
        .leave-counter {
            background: rgba(255,255,255,0.25);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: var(--mobile-font-small);
            font-weight: 600;
            white-space: nowrap;
            min-height: var(--touch-target-min);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .view-more-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: var(--mobile-font-small);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: var(--touch-target-min);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }
        
        .view-more-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.02);
        }
    }

    /* Extra Small Devices */
    @media (max-width: 575.98px) {
        .employee-header-row td {
            padding: 0.75rem !important;
            font-size: 0.85rem !important;
        }
        
        .employee-name-section {
            gap: 0.5rem;
        }
        
        .employee-info {
            gap: 0.75rem;
        }
        
        .employee-avatar {
            width: 28px;
            height: 28px;
            font-size: 0.8rem;
        }
        
        .employee-controls {
            flex-direction: column;
            gap: 0.375rem;
            width: 100%;
        }
        
        .leave-counter,
        .view-more-btn {
            width: 100%;
            min-width: auto;
            font-size: 0.75rem;
            padding: 0.5rem;
        }
    }

    /* Touch-specific employee controls */
    @media (hover: none) and (pointer: coarse) {
        .view-more-btn:hover {
            transform: none;
            background: rgba(255,255,255,0.15);
        }
        
        .view-more-btn:active {
            background: rgba(255,255,255,0.3);
            transform: scale(0.98);
        }
    }

    /* Enhanced Tags */
    .employee-id-tag {
        background: linear-gradient(135deg, #5c85ff 0%, #5c66ff 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow);
    }

    .employee-id-tag:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .employee-id-tag::before {
        content: '#';
        opacity: 0.8;
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
        text-decoration: none;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        text-decoration: none;
        color: white;
    }

    .status-badge.pending {
        background: linear-gradient(135deg, var(--warning) 0%, #fab1a0 100%);
        color: white;
    }

    .status-badge.approved {
        background: linear-gradient(135deg, var(--success) 0%, #00cec9 100%);
        color: white;
    }

    .status-badge.rejected {
        background: linear-gradient(135deg, var(--danger) 0%, #fd79a8 100%);
        color: white;
    }

    /* Enhanced Touch-Friendly Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: none;
        cursor: pointer;
        position: relative;
        /* Ensure minimum touch target */
        min-width: var(--touch-target-min);
        min-height: var(--touch-target-min);
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        text-decoration: none;
    }
    
    .action-btn.btn-edit {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }

    .action-btn.btn-edit:hover {
        color: white;
    }
    
    .action-btn.btn-delete {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }

    .action-btn.btn-delete:hover {
        color: white;
    }
    
    .action-btn.btn-action {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }

    .action-btn.btn-action:hover {
        color: white;
    }

    /* Tablet Action Buttons */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .action-buttons {
            gap: 0.375rem;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            font-size: 0.85rem;
            min-width: 40px;
            min-height: 40px;
        }
    }

    /* Mobile Action Buttons - Touch Optimized */
    @media (max-width: 767.98px) {
        .action-buttons {
            flex-direction: row;
            gap: 0.25rem;
            justify-content: center;
            min-height: var(--touch-target-min);
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            font-size: 0.8rem;
            /* Accessibility requirement - minimum 44px touch target */
            min-width: var(--touch-target-min);
            min-height: var(--touch-target-min);
            /* Add padding to increase touch area without changing visual size */
            padding: 4px;
        }
        
        /* Enhanced touch feedback */
        .action-btn:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }
        
        /* Disable hover effects on touch devices */
        .action-btn:hover {
            transform: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    }

    /* Extra Small Devices */
    @media (max-width: 575.98px) {
        .action-buttons {
            gap: 0.125rem;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
            /* Maintain accessibility requirements */
            min-width: var(--touch-target-min);
            min-height: var(--touch-target-min);
            padding: 6px;
        }
    }

    /* Touch-specific enhancements */
    @media (hover: none) and (pointer: coarse) {
        .action-btn {
            /* Increase touch target on touch devices */
            padding: 6px;
        }
        
        .action-btn:hover {
            transform: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .action-btn:active {
            transform: scale(0.95);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    }

    /* Collapsible Rows */
    .collapsible-rows {
        display: none;
    }

    .collapsible-rows.show {
        display: table-row;
    }

    /* Enhanced SweetAlert2 Styling with Mobile Optimization */
    .swal2-popup {
        border-radius: 20px !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        padding: 2rem !important;
        max-width: 90vw !important;
        max-height: 90vh !important;
        overflow-y: auto !important;
    }

    .swal2-title {
        color: var(--text-primary) !important;
        font-weight: 700 !important;
        font-size: 1.5rem !important;
        line-height: 1.3 !important;
    }

    .swal2-content {
        color: var(--text-secondary) !important;
        font-size: 1rem !important;
        line-height: 1.5 !important;
    }

    .swal2-confirm {
        background: linear-gradient(135deg, var(--danger), #f87171) !important;
        border-radius: 25px !important;
        font-weight: 600 !important;
        padding: 12px 24px !important;
        font-size: 1rem !important;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3) !important;
        min-height: var(--touch-target-min) !important;
        min-width: 120px !important;
    }

    .swal2-cancel {
        background: linear-gradient(135deg, var(--text-secondary), #9ca3af) !important;
        border-radius: 25px !important;
        font-weight: 600 !important;
        padding: 12px 24px !important;
        font-size: 1rem !important;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3) !important;
        min-height: var(--touch-target-min) !important;
        min-width: 120px !important;
    }

    /* Mobile-Specific Modal Optimizations */
    @media (max-width: 767.98px) {
        .swal2-popup {
            border-radius: 16px !important;
            padding: 1.5rem !important;
            margin: 1rem !important;
            max-width: calc(100vw - 2rem) !important;
            max-height: calc(100vh - 2rem) !important;
            font-size: var(--mobile-font-base) !important;
        }
        
        .swal2-title {
            font-size: 1.25rem !important;
            margin-bottom: 1rem !important;
        }
        
        .swal2-content {
            font-size: var(--mobile-font-base) !important;
            margin-bottom: 1.5rem !important;
        }
        
        .swal2-actions {
            flex-direction: column !important;
            gap: 0.75rem !important;
            width: 100% !important;
        }
        
        .swal2-confirm,
        .swal2-cancel {
            width: 100% !important;
            padding: 0.875rem 1rem !important;
            font-size: var(--mobile-font-base) !important;
            min-height: var(--touch-target-min) !important;
            border-radius: 12px !important;
        }
        
        .swal2-icon {
            width: 60px !important;
            height: 60px !important;
            margin: 1rem auto !important;
        }
        
        /* Loading spinner optimization */
        .swal2-loading .swal2-confirm {
            padding-left: 2.5rem !important;
        }
        
        .swal2-loading .swal2-confirm::before {
            width: 16px !important;
            height: 16px !important;
            margin-left: -24px !important;
        }
    }

    /* Extra Small Device Modal Adjustments */
    @media (max-width: 575.98px) {
        .swal2-popup {
            padding: 1rem !important;
            margin: 0.5rem !important;
            border-radius: 12px !important;
            max-width: calc(100vw - 1rem) !important;
            max-height: calc(100vh - 1rem) !important;
        }
        
        .swal2-title {
            font-size: 1.1rem !important;
            margin-bottom: 0.75rem !important;
        }
        
        .swal2-content {
            font-size: var(--mobile-font-small) !important;
        }
        
        .swal2-confirm,
        .swal2-cancel {
            padding: 0.75rem !important;
            font-size: var(--mobile-font-small) !important;
            border-radius: 8px !important;
        }
        
        .swal2-icon {
            width: 50px !important;
            height: 50px !important;
        }
    }

    /* AJAX Modal Optimizations */
    @media (max-width: 767.98px) {
        /* Custom modal container for AJAX popups */
        #commonModalCustom .modal-dialog {
            max-width: calc(100vw - 2rem) !important;
            margin: 1rem !important;
        }
        
        #commonModalCustom .modal-content {
            border-radius: 16px !important;
            border: none !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
        }
        
        #commonModalCustom .modal-header {
            padding: 1.25rem 1.5rem 1rem !important;
            border-bottom: 1px solid var(--border) !important;
        }
        
        #commonModalCustom .modal-title {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
        }
        
        #commonModalCustom .modal-body {
            padding: 1.5rem !important;
            font-size: var(--mobile-font-base) !important;
            max-height: calc(100vh - 200px) !important;
            overflow-y: auto !important;
        }
        
        #commonModalCustom .modal-footer {
            padding: 1rem 1.5rem 1.25rem !important;
            border-top: 1px solid var(--border) !important;
            gap: 0.75rem !important;
        }
        
        #commonModalCustom .btn {
            min-height: var(--touch-target-min) !important;
            padding: 0.75rem 1.5rem !important;
            font-size: var(--mobile-font-base) !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
        }
        
        /* Close button optimization */
        #commonModalCustom .btn-close {
            width: var(--touch-target-min) !important;
            height: var(--touch-target-min) !important;
            padding: 0 !important;
            margin: -0.5rem -0.5rem -0.5rem auto !important;
        }
    }

    /* Form Elements in Modals */
    @media (max-width: 767.98px) {
        #commonModalCustom .form-control,
        #commonModalCustom .form-select {
            font-size: var(--mobile-font-base) !important;
            padding: 0.75rem !important;
            border-radius: 8px !important;
            min-height: var(--touch-target-min) !important;
        }
        
        #commonModalCustom .form-label {
            font-size: var(--mobile-font-base) !important;
            font-weight: 600 !important;
            margin-bottom: 0.5rem !important;
        }
        
        #commonModalCustom .form-check-input {
            width: 20px !important;
            height: 20px !important;
            margin-top: 0.125rem !important;
        }
        
        #commonModalCustom .form-check-label {
            font-size: var(--mobile-font-base) !important;
            padding-left: 0.5rem !important;
        }
    }

    /* Landscape Orientation Modal Adjustments */
    @media (max-width: 767.98px) and (orientation: landscape) {
        .swal2-popup {
            max-height: calc(100vh - 1rem) !important;
            margin: 0.5rem !important;
        }
        
        .swal2-content {
            max-height: 200px !important;
            overflow-y: auto !important;
        }
        
        #commonModalCustom .modal-body {
            max-height: calc(100vh - 150px) !important;
        }
    }

    /* Touch-Specific Modal Enhancements */
    @media (hover: none) and (pointer: coarse) {
        .swal2-confirm:active,
        .swal2-cancel:active {
            transform: scale(0.98) !important;
        }
        
        #commonModalCustom .btn:active {
            transform: scale(0.98);
        }
        
        /* Prevent zoom on input focus */
        #commonModalCustom input,
        #commonModalCustom select,
        #commonModalCustom textarea {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }
    }

    /* Cross-Device Testing and Validation Utilities */
    
    /* Debug Mode - Uncomment for testing */
    /*
    .debug-responsive {
        position: fixed;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 10px;
        border-radius: 5px;
        font-size: 12px;
        z-index: 9999;
        font-family: monospace;
    }
    
    .debug-responsive::before {
        content: 'XS: <576px';
    }
    
    @media (min-width: 576px) {
        .debug-responsive::before {
            content: 'SM: 576px+';
        }
    }
    
    @media (min-width: 768px) {
        .debug-responsive::before {
            content: 'MD: 768px+';
        }
    }
    
    @media (min-width: 992px) {
        .debug-responsive::before {
            content: 'LG: 992px+';
        }
    }
    
    @media (min-width: 1200px) {
        .debug-responsive::before {
            content: 'XL: 1200px+';
        }
    }
    */

    /* Performance Monitoring Styles */
    @media (max-width: 767.98px) {
        /* Add visual indicators for performance testing */
        .performance-test {
            outline: 2px dashed rgba(255, 0, 0, 0.3);
        }
        
        .performance-test::after {
            content: 'PERF TEST';
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            font-size: 10px;
            padding: 2px 4px;
            z-index: 1000;
        }
    }

    /* Cross-Browser Compatibility Fixes */
    
    /* Safari iOS specific fixes */
    @supports (-webkit-touch-callout: none) {
        .premium-table-container {
            -webkit-overflow-scrolling: touch;
        }
        
        .action-btn,
        .premium-btn {
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Fix for Safari's aggressive input zooming */
        input, select, textarea {
            font-size: 16px;
        }
    }
    
    /* Chrome Android specific optimizations */
    @media screen and (-webkit-min-device-pixel-ratio: 0) and (min-resolution: .001dpcm) {
        .premium-table-container {
            scroll-behavior: smooth;
        }
        
        .action-btn:active {
            background-color: rgba(0,0,0,0.1);
        }
    }
    
    /* Firefox mobile optimizations */
    @-moz-document url-prefix() {
        .premium-table-container {
            scrollbar-width: thin;
        }
        
        .action-btn {
            -moz-user-select: none;
        }
    }

    /* Device-Specific Optimizations */
    
    /* iPhone X and newer with notch */
    @media only screen 
        and (device-width: 375px) 
        and (device-height: 812px) 
        and (-webkit-device-pixel-ratio: 3) {
        .page-header-compact {
            padding-top: calc(1.5rem + env(safe-area-inset-top, 0px));
        }
        
        .container-fluid {
            padding-left: max(var(--mobile-padding), env(safe-area-inset-left, 0px));
            padding-right: max(var(--mobile-padding), env(safe-area-inset-right, 0px));
        }
    }
    
    /* iPad specific optimizations */
    @media only screen 
        and (min-device-width: 768px) 
        and (max-device-width: 1024px) 
        and (-webkit-min-device-pixel-ratio: 1) {
        .premium-table-container {
            max-height: 70vh;
        }
        
        .stats-compact {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Validation and Testing Helpers */
    
    /* Accessibility testing outline */
    .a11y-test * {
        outline: 1px solid rgba(255, 0, 255, 0.5) !important;
    }
    
    .a11y-test *:focus {
        outline: 3px solid rgba(255, 0, 0, 0.8) !important;
    }
    
    /* Touch target validation */
    .touch-test .action-btn,
    .touch-test .premium-btn,
    .touch-test .view-more-btn {
        outline: 2px solid rgba(0, 255, 0, 0.5);
        position: relative;
    }
    
    .touch-test .action-btn::after,
    .touch-test .premium-btn::after,
    .touch-test .view-more-btn::after {
        content: attr(data-touch-size, '44x44');
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 255, 0, 0.8);
        color: white;
        font-size: 10px;
        padding: 2px 4px;
        border-radius: 2px;
        white-space: nowrap;
    }

    /* Final Cross-Device Validation */
    
    /* Ensure consistent rendering across devices */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }
    
    /* Prevent horizontal overflow on any device */
    html, body {
        overflow-x: hidden;
        max-width: 100vw;
    }
    
    /* Ensure proper box-sizing everywhere */
    *, *::before, *::after {
        box-sizing: border-box;
    }
    
    /* Final touch optimizations */
    @media (hover: none) and (pointer: coarse) {
        /* Remove hover states that don't work on touch */
        .premium-table tbody tr:hover {
            background: inherit;
            transform: none;
            box-shadow: none;
        }
        
        /* Optimize for touch performance */
        .action-btn,
        .premium-btn,
        .view-more-btn {
            cursor: default;
        }
    }
    
    /* Print styles for testing */
    @media print {
        .page-header-compact,
        .premium-actions,
        .action-buttons {
            display: none !important;
        }
        
        .premium-table {
            font-size: 10px;
        }
        
        .premium-table-container {
            overflow: visible;
            max-height: none;
        }
    }

    /* Final performance optimizations */
    @media (max-width: 767.98px) {
        /* Reduce complexity for better performance */
        .page-header-compact::before {
            display: none; /* Remove complex animations on mobile */
        }
        
        /* Optimize repaints */
        .premium-table tbody tr {
            contain: layout style paint;
        }
        
        /* Optimize memory usage */
        .stat-card-compact:not(:hover) {
            will-change: auto;
        }
        
        .action-btn:not(:hover):not(:focus) {
            will-change: auto;
        }
    }

    /* Enhanced Scrollbar */
    .premium-table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .premium-table-container::-webkit-scrollbar-track {
        background: var(--light);
        border-radius: 4px;
    }

    .premium-table-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .premium-table-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
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

    .action-btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .action-btn.loading i {
        animation: spin 1s linear infinite;
    }

    /* Enhanced Header Responsive Design */
    
    /* Tablet Layout (768px - 991px) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .page-header-compact {
            padding: 2rem 1.5rem;
            border-radius: 20px;
        }
        
        .header-icon {
            width: 64px;
            height: 64px;
            font-size: 1.6rem;
        }
        
        .page-title-compact {
            font-size: 1.8rem;
        }
        
        .page-subtitle-compact {
            font-size: 0.95rem;
        }
        
        .premium-actions {
            margin-top: 1rem;
        }
        
        .premium-btn {
            padding: 0.65rem 1.25rem;
            font-size: 0.9rem;
        }
    }

    /* Mobile Layout (≤767px) */
    @media (max-width: 767.98px) {
        .page-header-compact {
            padding: 1.5rem var(--mobile-padding);
            border-radius: 16px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .header-content {
            flex-direction: column !important;
            gap: 1.5rem;
            align-items: center !important;
        }
        
        .header-content > div {
            width: 100% !important;
            max-width: none !important;
        }
        
        .header-content .d-flex {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center;
        }
        
        .header-icon {
            width: 56px;
            height: 56px;
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }
        
        .page-title-compact {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }
        
        .page-subtitle-compact {
            font-size: 0.85rem;
            line-height: 1.4;
            margin: 0;
        }
        
        .premium-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
            margin-top: 0;
            width: 100%;
        }
        
        .premium-btn {
            justify-content: center;
            padding: 0.75rem 1rem;
            font-size: var(--mobile-font-base);
            min-height: var(--touch-target-min);
            width: 100%;
        }
        
        /* Mobile-specific table improvements */
        .premium-table-container {
            margin: 0 calc(-1 * var(--mobile-padding));
            border-radius: 0;
            box-shadow: none;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        
        /* Mobile table scroll indicators */
        .premium-table-container::before {
            content: '← Scroll →';
            position: sticky;
            left: 0;
            top: 0;
            background: rgba(37, 99, 235, 0.9);
            color: white;
            padding: 0.5rem;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 101;
            display: block;
        }
        
        /* Improved mobile table styling */
        .premium-table {
            min-width: 900px;
            font-size: 0.8rem;
        }
        
        /* Better mobile column widths */
        .premium-table th:nth-child(1), /* ID */
        .premium-table td:nth-child(1) {
            min-width: 60px;
            width: 60px;
        }
        
        .premium-table th:nth-child(2), /* Leave Type */
        .premium-table td:nth-child(2) {
            min-width: 100px;
            width: 100px;
        }
        
        .premium-table th:nth-child(3), /* Applied On */
        .premium-table td:nth-child(3),
        .premium-table th:nth-child(4), /* Start Date */
        .premium-table td:nth-child(4),
        .premium-table th:nth-child(5), /* End Date */
        .premium-table td:nth-child(5) {
            min-width: 90px;
            width: 90px;
        }
        
        .premium-table th:nth-child(6), /* Total Days */
        .premium-table td:nth-child(6) {
            min-width: 70px;
            width: 70px;
        }
        
        .premium-table th:nth-child(7), /* Half/Full Day */
        .premium-table td:nth-child(7) {
            min-width: 80px;
            width: 80px;
        }
        
        .premium-table th:nth-child(8), /* Leave Reason */
        .premium-table td:nth-child(8) {
            min-width: 120px;
            width: 120px;
        }
        
        .premium-table th:nth-child(9), /* Status */
        .premium-table td:nth-child(9) {
            min-width: 80px;
            width: 80px;
        }
        
        .premium-table th:nth-child(10), /* Actions */
        .premium-table td:nth-child(10) {
            min-width: 100px;
            width: 100px;
        }
    }

    /* Extra Small Devices (≤575px) */
    @media (max-width: 575.98px) {
        .page-header-compact {
            padding: 1rem var(--mobile-padding);
            margin-bottom: 1rem;
        }
        
        .header-icon {
            width: 48px;
            height: 48px;
            font-size: 1.2rem;
        }
        
        .page-title-compact {
            font-size: 1.25rem;
        }
        
        .page-subtitle-compact {
            font-size: 0.8rem;
        }
        
        .premium-btn {
            padding: 0.65rem 0.75rem;
            font-size: var(--mobile-font-small);
        }
    }
        
        /* Additional mobile table styles handled above in table section */
    }

    /* Large Devices (Small Desktops) - 992px to 1199px */
    @media (min-width: 992px) and (max-width: 1199.98px) {
        .page-header-compact {
            padding: 2.5rem 2rem;
        }
        
        .premium-card {
            border-radius: 18px;
        }
        
        .premium-table-container {
            max-height: 580px;
        }
    }

    /* Extra Large Devices (Large Desktops) - 1200px+ */
    @media (min-width: 1200px) {
        .page-header-compact {
            padding: 3rem 2.5rem;
        }
        
        .stats-compact {
            margin-bottom: 2rem;
        }
        
        .premium-table-container {
            max-height: 650px;
        }
        
        .stat-card-compact {
            padding: 1.5rem;
        }
    }

    /* Small Devices (Large Phones) - 576px to 767px */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .page-header-compact {
            padding: 1.25rem var(--mobile-padding);
            border-radius: 14px;
        }
        
        .header-content {
            flex-direction: column !important;
            gap: 1.25rem;
        }
        
        .header-icon {
            width: 52px;
            height: 52px;
            font-size: 1.3rem;
        }
        
        .page-title-compact {
            font-size: 1.4rem;
        }
        
        .stats-compact {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        
        .premium-table {
            min-width: 750px;
            font-size: 0.8rem;
        }
        
        .premium-table th,
        .premium-table td {
            padding: 0.625rem 0.4rem;
        }
    }

    /* Enhanced Tags Responsive Design */
    @media (max-width: 767.98px) {
        .employee-id-tag {
            font-size: 0.7rem;
            padding: 4px 8px;
        }
        
        .leave-type-tag {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        .status-badge {
            padding: 0.4rem 0.8rem;
            font-size: 0.7rem;
        }
    }

    @media (max-width: 575.98px) {
        .employee-id-tag {
            font-size: 0.65rem;
            padding: 2px 6px;
        }
        
        .leave-type-tag {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
        
        .status-badge {
            padding: 0.3rem 0.6rem;
            font-size: 0.65rem;
        }
    }

    /* Landscape Orientation Optimizations */
    @media (max-width: 767.98px) and (orientation: landscape) {
        .page-header-compact {
            padding: 1rem var(--mobile-padding);
        }
        
        .header-content {
            flex-direction: row !important;
            gap: 1rem;
        }
        
        .stats-compact {
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
        }
        
        .stat-card-compact {
            padding: 0.75rem;
            min-height: 80px;
        }
        
        .premium-table-container {
            max-height: 300px;
        }
    }

    /* Typography and Spacing Optimizations for Mobile */
    
    /* Base Typography Improvements */
    @media (max-width: 767.98px) {
        body {
            font-size: var(--mobile-font-base);
            line-height: 1.5;
        }
        
        /* Container and Layout Spacing */
        .container-fluid {
            padding-left: var(--mobile-padding);
            padding-right: var(--mobile-padding);
        }
        
        .row {
            margin-left: calc(-0.5 * var(--mobile-gap));
            margin-right: calc(-0.5 * var(--mobile-gap));
        }
        
        .row > * {
            padding-left: calc(0.5 * var(--mobile-gap));
            padding-right: calc(0.5 * var(--mobile-gap));
        }
        
        /* Premium Card Mobile Spacing */
        .premium-card {
            margin: 0 calc(-1 * var(--mobile-padding));
            border-radius: 0;
            box-shadow: none;
            border-top: 1px solid var(--border);
        }
        
        /* Improved Text Readability */
        .premium-table tbody td {
            line-height: 1.4;
            word-break: break-word;
        }
        
        /* Mobile-optimized margins and padding */
        .mb-3 {
            margin-bottom: var(--mobile-margin) !important;
        }
        
        /* Enhanced spacing for better touch interaction */
        .btn, .premium-btn {
            padding: 0.75rem 1rem;
            font-size: var(--mobile-font-base);
            line-height: 1.3;
        }
    }

    /* Extra Small Device Typography */
    @media (max-width: 575.98px) {
        body {
            font-size: var(--mobile-font-small);
        }
        
        /* Tighter spacing for very small screens */
        .container-fluid {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        
        /* Compact text elements */
        small, .small {
            font-size: 0.7rem;
        }
        
        /* Improved line heights for readability */
        .premium-table tbody td {
            line-height: 1.3;
        }
        
        /* Reduced margins for space efficiency */
        .mb-3 {
            margin-bottom: 0.5rem !important;
        }
        
        .mt-3 {
            margin-top: 0.5rem !important;
        }
    }

    /* Large Phone Typography Optimization */
    @media (min-width: 576px) and (max-width: 767.98px) {
        body {
            font-size: 0.9rem;
        }
        
        .premium-table {
            font-size: 0.85rem;
        }
        
        .premium-table tbody td {
            line-height: 1.4;
        }
    }

    /* Tablet Typography Refinements */
    @media (min-width: 768px) and (max-width: 991.98px) {
        body {
            font-size: 0.95rem;
        }
        
        .premium-table {
            font-size: 0.9rem;
        }
        
        /* Balanced spacing for tablets */
        .container-fluid {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
    }

    /* Text Overflow and Truncation Improvements */
    @media (max-width: 767.98px) {
        /* Better text handling in table cells */
        .premium-table td {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Specific column width optimizations */
        .premium-table td:nth-child(8) { /* Leave Reason column */
            max-width: 100px;
        }
        
        .premium-table td:nth-child(2) { /* Leave Type column */
            max-width: 90px;
        }
        
        /* Ensure important content is always visible */
        .premium-table td:nth-child(1), /* ID column */
        .premium-table td:nth-child(9), /* Status column */
        .premium-table td:nth-child(10) { /* Actions column */
            max-width: none;
            white-space: nowrap;
        }
    }

    /* Accessibility and Readability Enhancements */
    @media (max-width: 767.98px) {
        /* Improved focus indicators for mobile */
        .action-btn:focus,
        .premium-btn:focus,
        .view-more-btn:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
        
        /* Better contrast for small text */
        .stat-label-compact,
        .premium-table thead th {
            font-weight: 700;
        }
        
        /* Improved spacing for better readability */
        .employee-info > div {
            line-height: 1.3;
        }
        
        .employee-info strong {
            font-size: 1.05em;
        }
        
        .employee-info small {
            font-size: 0.8em;
            opacity: 0.9;
        }
    }

    /* High DPI / Retina Display Optimizations */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .header-icon,
        .stat-icon-compact,
        .action-btn {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Sharper text rendering on high DPI displays */
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    }

    /* Enhanced Table Cell Responsiveness and Content Display */
    
    /* Mobile Table Cell Optimizations */
    @media (max-width: 767.98px) {
        /* Employee ID Tag Mobile Styling */
        .employee-id-tag {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 12px;
            letter-spacing: 0.3px;
        }
        
        .employee-id-tag::before {
            content: '#';
            opacity: 0.7;
        }
        
        /* Leave Type Tag Mobile Optimization */
        .leave-type-tag {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            max-width: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }
        
        /* Status Badge Mobile Enhancements */
        .status-badge {
            padding: 0.4rem 0.8rem;
            font-size: 0.7rem;
            border-radius: 15px;
            gap: 0.25rem;
            min-height: 28px;
        }
        
        .status-badge i {
            font-size: 0.65rem;
        }
        
        /* Table Cell Content Optimization */
        .premium-table tbody td {
            position: relative;
        }
        
        /* Date formatting for mobile */
        .premium-table tbody td:nth-child(3), /* Applied On */
        .premium-table tbody td:nth-child(4), /* Start Date */
        .premium-table tbody td:nth-child(5) { /* End Date */
            font-size: 0.75rem;
            line-height: 1.2;
        }
        
        /* Total Days emphasis */
        .premium-table tbody td:nth-child(6) strong {
            font-size: 0.9rem;
            color: var(--primary);
        }
        
        /* Leave type and day segment styling */
        .premium-table tbody td:nth-child(7) {
            font-size: 0.75rem;
            line-height: 1.3;
        }
        
        .premium-table tbody td:nth-child(7) small {
            font-size: 0.65rem;
            opacity: 0.8;
        }
        
        /* Leave reason truncation with tooltip */
        .premium-table tbody td:nth-child(8) {
            max-width: 100px;
            font-size: 0.75rem;
            line-height: 1.3;
            cursor: help;
        }
    }

    /* Extra Small Device Table Optimizations */
    @media (max-width: 575.98px) {
        .employee-id-tag {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 8px;
        }
        
        .leave-type-tag {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
            border-radius: 8px;
            max-width: 70px;
        }
        
        .status-badge {
            padding: 0.3rem 0.6rem;
            font-size: 0.65rem;
            border-radius: 12px;
            min-height: 24px;
        }
        
        .status-badge i {
            font-size: 0.6rem;
        }
        
        /* Compact date display */
        .premium-table tbody td:nth-child(3),
        .premium-table tbody td:nth-child(4),
        .premium-table tbody td:nth-child(5) {
            font-size: 0.7rem;
        }
        
        /* Smaller total days */
        .premium-table tbody td:nth-child(6) strong {
            font-size: 0.8rem;
        }
        
        /* More compact leave reason */
        .premium-table tbody td:nth-child(8) {
            max-width: 80px;
            font-size: 0.7rem;
        }
    }

    /* Tablet Table Cell Enhancements */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .employee-id-tag {
            font-size: 0.75rem;
            padding: 6px 12px;
        }
        
        .leave-type-tag {
            font-size: 0.75rem;
            padding: 0.3rem 0.75rem;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }
        
        .premium-table tbody td:nth-child(8) {
            max-width: 140px;
        }
    }

    /* Large Phone Table Optimizations */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .employee-id-tag {
            font-size: 0.7rem;
            padding: 4px 10px;
        }
        
        .leave-type-tag {
            font-size: 0.7rem;
            padding: 0.25rem 0.6rem;
            max-width: 90px;
        }
        
        .status-badge {
            padding: 0.4rem 0.9rem;
            font-size: 0.7rem;
        }
        
        .premium-table tbody td:nth-child(8) {
            max-width: 120px;
        }
    }

    /* Enhanced Content Readability */
    @media (max-width: 767.98px) {
        /* Better visual hierarchy in table cells */
        .premium-table tbody td strong {
            font-weight: 700;
        }
        
        .premium-table tbody td small {
            display: block;
            margin-top: 2px;
            opacity: 0.8;
        }
        
        /* Improved spacing for multi-line content */
        .premium-table tbody td br + small {
            margin-top: 4px;
        }
        
        /* Better alignment for centered content */
        .premium-table tbody td {
            text-align: center;
            vertical-align: middle;
        }
        
        /* Left align text-heavy columns */
        .premium-table tbody td:nth-child(8) { /* Leave Reason */
            text-align: left;
            padding-left: 0.75rem;
        }
    }

    /* Touch-friendly table interactions */
    @media (hover: none) and (pointer: coarse) {
        .premium-table tbody tr:hover {
            background: rgba(37, 99, 235, 0.02);
            transform: none;
            box-shadow: none;
        }
        
        .premium-table tbody tr:active {
            background: rgba(37, 99, 235, 0.05);
        }
        
        /* Enhanced touch feedback for clickable elements */
        .status-badge:active {
            transform: scale(0.98);
        }
        
        .employee-id-tag:active {
            transform: scale(0.98);
        }
    }

    /* Orientation Change Handling and Landscape Optimizations */
    
    /* Mobile Landscape Optimizations */
    @media (max-width: 767.98px) and (orientation: landscape) {
        /* Compact header for landscape */
        .page-header-compact {
            padding: 1rem var(--mobile-padding);
            margin-bottom: 1rem;
        }
        
        .header-content {
            flex-direction: row !important;
            gap: 1rem;
            align-items: center !important;
        }
        
        .header-content > div {
            width: auto !important;
        }
        
        .header-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }
        
        .page-title-compact {
            font-size: 1.25rem;
            margin-bottom: 0;
        }
        
        .page-subtitle-compact {
            display: none; /* Hide subtitle in landscape to save space */
        }
        
        .premium-actions {
            flex-direction: row;
            margin-top: 0;
            gap: 0.5rem;
        }
        
        .premium-btn {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            white-space: nowrap;
        }
        
        /* Optimize statistics for landscape */
        .stats-compact {
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-card-compact {
            padding: 0.75rem;
            min-height: 80px;
        }
        
        .stat-number-compact {
            font-size: 1.25rem;
        }
        
        .stat-label-compact {
            font-size: 0.65rem;
        }
        
        .stat-icon-compact {
            width: 28px;
            height: 28px;
            font-size: 1rem;
        }
        
        /* Table optimizations for landscape */
        .premium-table-container {
            max-height: 300px; /* Reduced height for landscape */
            border-radius: 8px;
            margin: 0;
        }
        
        .premium-table {
            min-width: 700px; /* Slightly less minimum width */
            font-size: 0.8rem;
        }
        
        .premium-table th,
        .premium-table td {
            padding: 0.5rem 0.375rem;
        }
        
        /* Employee headers in landscape */
        .employee-header-row td {
            padding: 0.75rem !important;
            font-size: 0.85rem !important;
        }
        
        .employee-name-section {
            flex-direction: row !important;
            gap: 1rem;
            align-items: center;
        }
        
        .employee-controls {
            flex-direction: row;
            gap: 0.5rem;
        }
        
        .leave-counter,
        .view-more-btn {
            flex: none;
            min-width: auto;
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
    }

    /* Tablet Landscape Optimizations */
    @media (min-width: 768px) and (max-width: 991.98px) and (orientation: landscape) {
        .page-header-compact {
            padding: 1.5rem 2rem;
        }
        
        .stats-compact {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .premium-table-container {
            max-height: 400px;
        }
        
        .premium-table {
            font-size: 0.85rem;
        }
    }

    /* Portrait Optimizations for Tablets */
    @media (min-width: 768px) and (max-width: 991.98px) and (orientation: portrait) {
        .stats-compact {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .premium-table-container {
            max-height: 600px;
        }
    }

    /* Smooth Orientation Transitions */
    @media (max-width: 991.98px) {
        .page-header-compact,
        .stats-compact,
        .stat-card-compact,
        .premium-table-container,
        .employee-name-section,
        .employee-controls {
            transition: all 0.3s ease-in-out;
        }
        
        /* Prevent layout shifts during orientation change */
        .header-content,
        .premium-actions {
            transition: flex-direction 0.3s ease-in-out, gap 0.3s ease-in-out;
        }
    }

    /* Sticky Header Adjustments for Orientation Changes */
    @media (max-width: 767.98px) {
        .premium-table thead th {
            position: sticky;
            top: 0;
            z-index: 100;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(59, 130, 246, 0.95) 100%);
        }
        
        .employee-header-row {
            position: sticky;
            top: 60px; /* Adjust based on header height */
            z-index: 99;
        }
    }

    @media (max-width: 767.98px) and (orientation: landscape) {
        .premium-table thead th {
            top: 0;
            padding: 0.75rem 0.375rem;
            font-size: 0.7rem;
        }
        
        .employee-header-row {
            top: 45px; /* Adjusted for landscape header height */
        }
    }

    /* Viewport Height Optimizations */
    @media (max-height: 500px) and (orientation: landscape) {
        .page-header-compact {
            padding: 0.75rem var(--mobile-padding);
            margin-bottom: 0.75rem;
        }
        
        .stats-compact {
            margin-bottom: 0.75rem;
        }
        
        .stat-card-compact {
            min-height: 70px;
            padding: 0.5rem;
        }
        
        .premium-table-container {
            max-height: 250px;
        }
    }

    /* Performance Optimizations for Mobile Devices */
    
    /* GPU Acceleration and Hardware Optimization */
    @media (max-width: 991.98px) {
        /* Force GPU acceleration for smooth animations */
        .page-header-compact,
        .stat-card-compact,
        .premium-table-container,
        .action-btn,
        .premium-btn,
        .status-badge,
        .employee-id-tag,
        .leave-type-tag {
            transform: translateZ(0);
            backface-visibility: hidden;
            perspective: 1000px;
        }
        
        /* Optimize will-change for frequently animated elements */
        .action-btn,
        .premium-btn,
        .stat-card-compact,
        .view-more-btn {
            will-change: transform, box-shadow;
        }
        
        /* Optimize scroll performance */
        .premium-table-container {
            will-change: scroll-position;
            contain: layout style paint;
        }
        
        /* Reduce paint complexity */
        .premium-table tbody tr {
            will-change: background-color;
            contain: layout style;
        }
    }

    /* 60fps Animation Optimizations */
    @media (max-width: 767.98px) {
        /* Use transform instead of changing layout properties */
        .action-btn:active {
            transform: translateZ(0) scale(0.95);
            transition: transform 0.1s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .premium-btn:active {
            transform: translateZ(0) scale(0.98);
            transition: transform 0.1s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Optimize hover effects for performance */
        .stat-card-compact:hover {
            transform: translateZ(0) translateY(-2px);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Smooth scroll optimization */
        .premium-table-container {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* Memory and CPU Optimizations */
    @media (max-width: 767.98px) {
        /* Reduce complexity of gradients on mobile */
        .page-header-compact::before {
            animation-duration: 30s; /* Slower animation to reduce CPU usage */
        }
        
        /* Optimize box-shadows for mobile performance */
        .premium-card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* Lighter shadow */
        }
        
        .stat-card-compact {
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); /* Lighter shadow */
        }
        
        .action-btn {
            box-shadow: 0 2px 6px rgba(0,0,0,0.08); /* Lighter shadow */
        }
        
        /* Reduce animation complexity */
        .loading-spinner {
            animation-duration: 1.2s; /* Slightly slower for better performance */
        }
    }

    /* Network and Loading Optimizations */
    @media (max-width: 767.98px) {
        /* Optimize font loading */
        body {
            font-display: swap; /* Improve font loading performance */
        }
        
        /* Reduce repaints during scrolling */
        .premium-table thead th {
            contain: layout style paint;
        }
        
        .employee-header-row {
            contain: layout style paint;
        }
        
        /* Optimize image rendering if any */
        img {
            image-rendering: optimizeSpeed;
            image-rendering: -webkit-optimize-contrast;
        }
    }

    /* Touch Performance Optimizations */
    @media (hover: none) and (pointer: coarse) {
        /* Disable expensive hover effects on touch devices */
        .stat-card-compact:hover,
        .premium-card:hover {
            transform: none;
            box-shadow: inherit;
        }
        
        /* Optimize touch event handling */
        .action-btn,
        .premium-btn,
        .view-more-btn {
            touch-action: manipulation; /* Prevent double-tap zoom */
        }
        
        /* Reduce animation complexity on touch */
        * {
            animation-duration: 0.2s !important;
            transition-duration: 0.2s !important;
        }
    }

    /* Critical Rendering Path Optimizations */
    @media (max-width: 767.98px) {
        /* Prioritize above-the-fold content */
        .page-header-compact,
        .stats-compact {
            contain: layout style;
        }
        
        /* Defer non-critical animations */
        .premium-table tbody tr:nth-child(n+10) {
            animation-delay: 0.1s;
        }
        
        /* Optimize layout calculations */
        .row,
        .col-12,
        .col-md-6,
        .col-lg-3 {
            contain: layout;
        }
    }

    /* Reduce Motion for Performance and Accessibility */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
        
        .page-header-compact::before {
            animation: none;
        }
        
        .loading-spinner {
            animation: none;
            border: 3px solid var(--primary);
        }
    }

    /* Battery and Power Optimizations */
    @media (max-width: 767.98px) {
        /* Reduce CPU-intensive effects on mobile */
        .header-icon::before,
        .page-header-compact::after {
            display: none; /* Remove complex pseudo-element animations */
        }
        
        /* Simplify gradients for better performance */
        .premium-btn-primary {
            background: var(--danger); /* Solid color instead of gradient */
        }
        
        .status-badge.pending {
            background: var(--warning);
        }
        
        .status-badge.approved {
            background: var(--success);
        }
        
        .status-badge.rejected {
            background: var(--danger);
        }
    }

    /* Accessibility Enhancements for Responsive Design */
    
    /* Touch Target Accessibility */
    @media (max-width: 767.98px) {
        /* Ensure all interactive elements meet WCAG 2.1 AA requirements */
        .action-btn,
        .premium-btn,
        .view-more-btn,
        .status-badge[href],
        .employee-id-tag[href] {
            min-width: var(--touch-target-min);
            min-height: var(--touch-target-min);
            position: relative;
        }
        
        /* Add invisible padding to increase touch area without changing visual appearance */
        .action-btn::before,
        .view-more-btn::before {
            content: '';
            position: absolute;
            top: -6px;
            left: -6px;
            right: -6px;
            bottom: -6px;
            z-index: -1;
        }
        
        /* Ensure adequate spacing between touch targets */
        .action-buttons {
            gap: 8px; /* Minimum 8px spacing between touch targets */
        }
        
        .employee-controls {
            gap: 8px;
        }
    }

    /* Focus Management and Keyboard Navigation */
    @media (max-width: 767.98px) {
        /* Enhanced focus indicators for mobile */
        .action-btn:focus,
        .premium-btn:focus,
        .view-more-btn:focus {
            outline: 3px solid var(--primary);
            outline-offset: 2px;
            box-shadow: 0 0 0 2px white, 0 0 0 5px var(--primary);
        }
        
        /* High contrast focus for better visibility */
        .status-badge:focus,
        .employee-id-tag:focus {
            outline: 2px solid var(--text-primary);
            outline-offset: 2px;
        }
        
        /* Skip to content functionality */
        .skip-to-content {
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--primary);
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
        }
        
        .skip-to-content:focus {
            top: 6px;
        }
        
        /* Logical tab order */
        .premium-table {
            tab-index: 0;
        }
        
        .premium-table:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    }

    /* Screen Reader Enhancements */
    @media (max-width: 767.98px) {
        /* Screen reader only content */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Improved table accessibility */
        .premium-table th {
            scope: col;
        }
        
        /* Better labeling for interactive elements */
        .action-btn[data-original-title] {
            aria-label: attr(data-original-title);
        }
        
        /* Status announcements */
        .status-badge::before {
            content: "Status: ";
            position: absolute;
            left: -10000px;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }
    }

    /* Color Contrast and High Contrast Mode */
    @media (max-width: 767.98px) {
        /* Ensure sufficient color contrast */
        .premium-table tbody td {
            color: var(--text-primary);
        }
        
        .stat-label-compact {
            color: var(--text-primary);
            font-weight: 700;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .action-btn,
            .premium-btn,
            .status-badge {
                border: 2px solid currentColor;
            }
            
            .premium-table th,
            .premium-table td {
                border: 1px solid currentColor;
            }
            
            .stat-card-compact {
                border: 2px solid var(--text-primary);
            }
        }
        
        /* Dark mode considerations */
        @media (prefers-color-scheme: dark) {
            .premium-table-container {
                background: var(--dark);
                color: white;
            }
            
            .stat-card-compact {
                background: var(--dark);
                color: white;
                border-color: rgba(255, 255, 255, 0.2);
            }
        }
    }

    /* Zoom and Magnification Support */
    @media (max-width: 767.98px) {
        /* Support up to 200% zoom without horizontal scrolling */
        @media (min-resolution: 2dppx) {
            .container-fluid {
                max-width: 100%;
                overflow-x: hidden;
            }
            
            .premium-table-container {
                font-size: 1rem; /* Larger base font for high DPI */
            }
            
            .action-btn {
                min-width: 48px; /* Larger touch targets for high DPI */
                min-height: 48px;
            }
        }
        
        /* Text scaling support */
        .premium-table,
        .stat-card-compact,
        .page-header-compact {
            font-size: max(var(--mobile-font-base), 16px); /* Respect user font size preferences */
        }
    }

    /* Motion and Animation Accessibility */
    @media (max-width: 767.98px) and (prefers-reduced-motion: reduce) {
        /* Remove all animations for users who prefer reduced motion */
        .action-btn,
        .premium-btn,
        .stat-card-compact,
        .premium-table tbody tr {
            transition: none !important;
            animation: none !important;
        }
        
        /* Maintain functionality without animation */
        .action-btn:hover,
        .action-btn:focus {
            background-color: var(--primary-dark);
        }
        
        .stat-card-compact:hover {
            border-color: var(--primary);
        }
    }

    /* Language and Internationalization Support */
    @media (max-width: 767.98px) {
        /* RTL language support */
        [dir="rtl"] .premium-table {
            text-align: right;
        }
        
        [dir="rtl"] .action-buttons {
            flex-direction: row-reverse;
        }
        
        [dir="rtl"] .employee-info {
            flex-direction: row-reverse;
        }
        
        /* Long text handling for different languages */
        .premium-table td {
            word-wrap: break-word;
            hyphens: auto;
        }
        
        /* Flexible layout for varying text lengths */
        .premium-btn,
        .status-badge {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
    }

    /* Error States and User Feedback */
    @media (max-width: 767.98px) {
        /* Accessible error states */
        .action-btn[aria-disabled="true"] {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        /* Loading states */
        .action-btn.loading {
            position: relative;
        }
        
        .action-btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        /* Success/error feedback */
        .action-btn.success {
            background-color: var(--success);
        }
        
        .action-btn.error {
            background-color: var(--danger);
        }
    }

    /* CSS Grid Fallback Support for Older Browsers */
    
    /* Internet Explorer 11 and Older Browser Support */
    
    /* Flexbox fallback for statistics cards */
    .stats-compact {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 24px;
    }
    
    .stats-compact > * {
        -webkit-box-flex: 1;
        -ms-flex: 1 1 250px;
        flex: 1 1 250px;
        min-width: 250px;
        max-width: calc(25% - 0.75rem); /* 4 columns on desktop */
    }
    
    /* IE11 specific fixes */
    @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
        .stats-compact {
            display: -ms-flexbox;
            -ms-flex-wrap: wrap;
        }
        
        .stats-compact > * {
            -ms-flex: 1 1 250px;
            width: calc(25% - 1rem);
        }
        
        .stat-card-compact {
            height: auto;
            min-height: 120px;
        }
        
        /* IE11 table fixes */
        .premium-table-container {
            overflow-x: auto;
            overflow-y: auto;
        }
        
        .premium-table {
            width: 100%;
            table-layout: fixed;
        }
        
        /* IE11 flexbox fixes */
        .action-buttons {
            display: -ms-flexbox;
            -ms-flex-pack: center;
            -ms-flex-align: center;
        }
        
        .employee-name-section {
            display: -ms-flexbox;
            -ms-flex-pack: justify;
            -ms-flex-align: center;
        }
    }
    
    /* Progressive Enhancement with CSS Grid */
    @supports (display: grid) {
        .stats-compact {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .stats-compact > * {
            flex: none;
            min-width: auto;
            max-width: none;
            width: auto;
        }
        
        /* Grid-specific responsive breakpoints */
        @media (min-width: 992px) {
            .stats-compact {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        @media (min-width: 768px) and (max-width: 991.98px) {
            .stats-compact {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 767.98px) {
            .stats-compact {
                grid-template-columns: 1fr;
            }
        }
    }
    
    /* Flexbox fallback for mobile layouts */
    @media (max-width: 767.98px) {
        .stats-compact {
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
        }
        
        .stats-compact > * {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 auto;
            flex: 0 0 auto;
            width: 100%;
            min-width: auto;
            max-width: none;
        }
    }
    
    @media (min-width: 768px) and (max-width: 991.98px) {
        .stats-compact > * {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 calc(50% - 0.5rem);
            flex: 0 0 calc(50% - 0.5rem);
            max-width: calc(50% - 0.5rem);
        }
    }
    
    /* Legacy browser header layout */
    @supports not (display: grid) {
        @media (max-width: 767.98px) {
            .header-content {
                display: block !important;
            }
            
            .header-content > div {
                width: 100% !important;
                margin-bottom: 1rem;
            }
            
            .header-content .d-flex {
                display: block !important;
                text-align: center;
            }
            
            .premium-actions {
                display: block;
                text-align: center;
            }
            
            .premium-btn {
                display: inline-block;
                margin: 0.25rem;
            }
        }
    }
    
    /* Older WebKit browser support */
    @supports not (gap: 1rem) {
        .stats-compact {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
        
        .stats-compact > * {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .action-buttons {
            margin-left: -0.25rem;
            margin-right: -0.25rem;
        }
        
        .action-buttons > * {
            margin-left: 0.25rem;
            margin-right: 0.25rem;
        }
        
        .employee-controls {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
        
        .employee-controls > * {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }
    }
    
    /* CSS Custom Properties fallback */
    @supports not (color: var(--primary)) {
        :root {
            /* Fallback colors for browsers without CSS custom properties */
        }
        
        .action-btn.btn-edit {
            background: #4299e1;
        }
        
        .action-btn.btn-delete {
            background: #f56565;
        }
        
        .action-btn.btn-action {
            background: #ed8936;
        }
        
        .status-badge.pending {
            background: #f59e0b;
        }
        
        .status-badge.approved {
            background: #10b981;
        }
        
        .status-badge.rejected {
            background: #ef4444;
        }
        
        .premium-btn-primary {
            background: #ff6b6b;
        }
        
        .page-header-compact {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
        }
    }
    
    /* Transform fallbacks for older browsers */
    @supports not (transform: translateZ(0)) {
        .action-btn:hover {
            margin-top: -2px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card-compact:hover {
            margin-top: -2px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .premium-card:hover {
            margin-top: -5px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
        }
    }
    
    /* Backdrop-filter fallback */
    @supports not (backdrop-filter: blur(20px)) {
        .page-header-compact {
            background: rgba(37, 99, 235, 0.95);
        }
        
        .premium-btn {
            background: rgba(255,255,255,0.3);
        }
        
        .header-icon {
            background: rgba(255, 255, 255, 0.2);
        }
    }
    
    /* Flexbox gap fallback for older browsers */
    @supports not (gap: 1rem) {
        @media (max-width: 767.98px) {
            .header-content {
                margin-bottom: -1.5rem;
            }
            
            .header-content > * {
                margin-bottom: 1.5rem;
            }
            
            .premium-actions {
                margin-bottom: -0.75rem;
            }
            
            .premium-actions > * {
                margin-bottom: 0.75rem;
            }
        }
    }
</style>
@endpush

@section('content')
    <!-- Premium Header Section -->
    <div class="page-header-compact">
        <div class="header-content d-flex justify-content-between align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <div class="header-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="ml-3">
                    <h1 class="page-title-compact">{{ __('Leave Management') }}</h1>
                    <p class="page-subtitle-compact">{{ __('Comprehensive leave tracking and management dashboard for enhanced workforce planning') }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="premium-actions">
                    @can('Create Leave')
                        @if($selfLeaves == 'true' || \Auth::user()->type == 'hr')
                            <a href="#" data-url="{{ route('leave.create') }}" class="premium-btn premium-btn-primary btn-create-leave"
                            data-ajax-popup="true" data-title="{{ __('Create New Leave') }}">
                                <i class="fas fa-plus"></i> {{ __('Create Leave') }}
                            </a>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row stats-compact">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $leaves->count() ?? 0 }}</h3>
                        <p class="stat-label-compact">{{ __('Total Leaves') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #c4d3f9 0%, #b4d3f5 100%); color: #3a3ded;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">
                            @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                {{ $leaves->where('status', 'Pending')->count() }}
                            @else
                                {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Pending')->count(); }) }}
                            @endif
                        </h3>
                        <p class="stat-label-compact">{{ __('Pending') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">
                            @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                {{ $leaves->where('status', 'Approve')->count() }}
                            @else
                                {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Approve')->count(); }) }}
                            @endif
                        </h3>
                        <p class="stat-label-compact">{{ __('Approved') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #059669;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">
                            @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                {{ $leaves->where('status', 'Reject')->count() }}
                            @else
                                {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Reject')->count(); }) }}
                            @endif
                        </h3>
                        <p class="stat-label-compact">{{ __('Rejected') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%); color: #dc2626;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="premium-card">
                <div class="table-responsive">
                    <div class="premium-table-container">
                        <table class="table premium-table" style="width: 100%;" id="leaveTable">
                            <thead>
                                <tr>
                                    <th style="min-width: 80px;"><i class="fas fa-hashtag"></i> {{ __('ID') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-tag"></i> {{ __('Leave Type') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-calendar-plus"></i> {{ __('Applied On') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-play-circle"></i> {{ __('Start Date') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-stop-circle"></i> {{ __('End Date') }}</th>
                                    <th style="min-width: 100px;"><i class="fas fa-clock"></i> {{ __('Total Days') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-adjust"></i> {{ __('Half/Full Day') }}</th>
                                    <th style="min-width: 150px;"><i class="fas fa-comment"></i> {{ __('Leave Reason') }}</th>
                                    <th style="min-width: 100px;"><i class="fas fa-info-circle"></i> {{ __('Status') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-cogs"></i> {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                    @foreach ($leaves as $key => $leave)
                                        <tr id="leave-row-{{ $leave->id }}">
                                            <td><span class="employee-id-tag">{{ ++$i }}</span></td>
                                            <td><span class="leave-type-tag">{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}</span></td>
                                            <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                            <td>{{ \Auth::user()->dateFormat($leave->start_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->formatted_start_time }}</small> @endif</td>
                                            <td>{{ \Auth::user()->dateFormat($leave->end_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->formatted_end_time }}</small> @endif</td>
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
                                                            <button type="button" 
                                                                    class="action-btn btn-delete delete-leave" 
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Delete Leave') }}"
                                                                    data-leave-id="{{ $leave->id }}"
                                                                    data-leave-type="{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : 'Leave' }}"
                                                                    data-employee-name="{{ \Auth::user()->name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            
                                                            {{-- Hidden Form for Delete --}}
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
                                    
                                        <tr class="employee-header-row">
                                            <td colspan="10" style="background: #5799f8;">
                                                <div class="employee-name-section">
                                                    <div class="employee-info">
                                                        <div class="employee-avatar">
                                                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $employee->name }}</strong>
                                                            <br>
                                                            <small>{{ $employee->email ?? 'No email' }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="employee-controls">
                                                        <span class="leave-counter">
                                                            <i class="fas fa-calendar-check"></i> {{ count($employeeLeaves) }} Leaves
                                                        </span>
                                                        @if(count($employeeLeaves) > 5)
                                                            <button class="view-more-btn" onclick="toggleEmployeeLeaves('{{ $employee->id }}')">
                                                                <span id="toggle-text-{{ $employee->id }}">View All</span>
                                                                <i class="fas fa-chevron-down" id="toggle-icon-{{ $employee->id }}"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    
                                        @foreach ($employeeLeaves as $index => $leave)
                                            <tr class="{{ $index >= 5 ? 'collapsible-rows employee-' . $employee->id : '' }}" id="leave-row-{{ $leave->id }}">
                                                <td><span class="employee-id-tag">{{ ++$i }}</span></td>
                                                <td><span class="leave-type-tag">{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}</span></td>
                                                <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                                <td>{{ \Auth::user()->dateFormat($leave->start_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->formatted_start_time }}</small> @endif</td>
                                                <td>{{ \Auth::user()->dateFormat($leave->end_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->formatted_end_time }}</small> @endif</td>
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
                                                                <button type="button" 
                                                                        class="action-btn btn-delete delete-leave" 
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Delete Leave') }}"
                                                                        data-leave-id="{{ $leave->id }}"
                                                                        data-leave-type="{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : 'Leave' }}"
                                                                        data-employee-name="{{ $employee->name ?? \Auth::user()->name }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                                
                                                                {{-- Hidden Form for Delete --}}
                                                                <form method="POST" 
                                                                    action="{{ route('leave.destroy', $leave->id) }}" 
                                                                    id="delete-form-{{ $leave->id }}" 
                                                                    style="display: none;">
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
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Handle duration change (Full Day / Half Day / Short Leave)
    function handleDurationChange() {
        const selectedValue = document.getElementById('is_halfday').value;
        console.log('Duration changed to:', selectedValue);
        setupFormByDurationType(selectedValue);
    }

    // Setup form based on duration type - FIXED VERSION
    function setupFormByDurationType(durationType) {
        const daySegmentContainer = document.getElementById('day_segment_container');
        const timingContainer = document.getElementById('timing-container');
        const endDateContainer = document.getElementById('end_date_container');
        
        console.log('Setting up form for duration:', durationType);
        console.log('Timing container found:', !!timingContainer);
        
        // First, hide all conditional containers
        hideElement(daySegmentContainer);
        hideElement(timingContainer);
        showElement(endDateContainer);
        
        switch(durationType) {
            case 'full':
                // Full day: Show end date only
                showElement(endDateContainer);
                break;
                
            case 'half':
                // Half day: Hide end date, show day segment
                hideElement(endDateContainer);
                showElement(daySegmentContainer);
                updateEndDateToStartDate();
                break;
                
            case 'short':
                // Short leave: Hide end date, show day segment AND timing
                console.log('Setting up short leave - showing timing container');
                hideElement(endDateContainer);
                showElement(daySegmentContainer);
                showElement(timingContainer);
                updateEndDateToStartDate();
                setDefaultStartTime();
                break;
        }
    }

    // Helper function to show element reliably
    function showElement(element) {
        if (element) {
            // Remove all possible hidden classes and set display
            element.classList.remove('hidden', 'd-none');
            element.style.display = 'block';
            element.style.visibility = 'visible';
            element.style.opacity = '1';
            
            // Also ensure child inputs are enabled
            const inputs = element.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.disabled = false;
                input.style.display = 'block';
            });
            
            console.log('Element shown:', element.id);
        }
    }

    // Helper function to hide element reliably
    function hideElement(element) {
        if (element) {
            element.classList.add('hidden');
            element.style.display = 'none';
            console.log('Element hidden:', element.id);
        }
    }

    // Update end date to start date for half/short leaves
    function updateEndDateToStartDate() {
        const startDate = document.getElementById('start_date').value;
        if (startDate) {
            document.getElementById('end_date').value = startDate;
        }
    }

    // Set default start time based on day segment
    function setDefaultStartTime() {
        const daySegment = document.getElementById('day_segment').value;
        const startTimeInput = document.getElementById('start_time');
        
        // Only set default if no existing value
        if (!startTimeInput.value) {
            // Set default times in 24-hour format (HTML5 time input format)
            if (daySegment === 'morning') {
                startTimeInput.value = '09:00';
            } else {
                startTimeInput.value = '14:00';
            }
        }
        
        // Calculate end time and add validation
        calculateEndTime();
        addValidationSuccess(startTimeInput);
    }

    // Issue #4 Fix: Calculate end time (start time + 2 hours) with proper format handling
    function calculateEndTime() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (startTimeInput.value) {
            try {
                // Parse the time input (HTML5 time input provides 24-hour format)
                const [hours, minutes] = startTimeInput.value.split(':').map(Number);
                
                // Create date object for calculation
                const startTime = new Date();
                startTime.setHours(hours, minutes, 0, 0);
                
                // Add 2 hours
                const endTime = new Date(startTime.getTime() + (2 * 60 * 60 * 1000));
                
                // Format back to 24-hour format for the input field
                const endHours = endTime.getHours().toString().padStart(2, '0');
                const endMinutes = endTime.getMinutes().toString().padStart(2, '0');
                
                endTimeInput.value = endHours + ':' + endMinutes;
                
                // Add validation success indicator
                addValidationSuccess(startTimeInput);
                addValidationSuccess(endTimeInput);
                
            } catch (error) {
                console.error('Error calculating end time:', error);
                endTimeInput.value = '';
            }
        } else {
            endTimeInput.value = '';
        }
    }

    // Add validation success indicator
    function addValidationSuccess(element) {
        if (element.value) {
            element.classList.add('input-valid');
            const wrapper = element.closest('.input-wrapper');
            if (wrapper) {
                wrapper.classList.add('valid');
            }
        }
    }








    $(document).ready(function() {
        console.log('Initializing leave table...');

        // Initialize tooltips with mobile-friendly settings
        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover focus',
            placement: 'top',
            container: 'body'
        });
        
        // Mobile-specific optimizations
        if (window.innerWidth <= 768) {
            // Disable tooltips on mobile for better touch experience
            $('[data-toggle="tooltip"]').tooltip('disable');
            
            // Add touch-friendly feedback
            $('.action-btn, .premium-btn, .view-more-btn').on('touchstart', function() {
                $(this).addClass('touch-active');
            }).on('touchend touchcancel', function() {
                $(this).removeClass('touch-active');
            });
        }
        
        // Enhanced delete confirmation with SweetAlert2
        $('.delete-leave').on('click', function(e) {
            e.preventDefault();
            
            const leaveId = $(this).data('leave-id');
            const leaveType = $(this).data('leave-type');
            const employeeName = $(this).data('employee-name');
            const row = $(this).closest('tr');
            
            // Check if SweetAlert2 is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Leave Request?',
                    html: `
                        <div style="text-align: left; margin: 1rem 0;">
                            <p><strong>Leave Type:</strong> ${leaveType}</p>
                            <p><strong>Employee:</strong> ${employeeName}</p>
                            <br>
                            <p style="color: var(--danger); font-weight: 600;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                This action cannot be undone and will permanently remove this leave request.
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, Delete Leave',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        content: 'swal2-content',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add loading state to the row
                        row.addClass('loading-row');
                        
                        // Show loading toast
                        Swal.fire({
                            title: 'Deleting Leave...',
                            html: `
                                <div style="text-align: center; margin: 1rem 0;">
                                    <div class="loading-spinner" style="margin: 0 auto 1rem;"></div>
                                    <p>Please wait while we delete the leave request for <strong>${employeeName}</strong>.</p>
                                </div>
                            `,
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'swal2-popup'
                            }
                        }).then(() => {
                            // Submit the form
                            const form = document.getElementById(`delete-form-${leaveId}`);
                            if (form) {
                                form.submit();
                            }
                        });
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert2 is not available
                const confirmMessage = `Are you sure you want to delete this leave request for "${employeeName}"? This action cannot be undone.`;
                if (confirm(confirmMessage)) {
                    // Add loading state to the row
                    row.addClass('loading-row');
                    
                    // Submit the form
                    const form = document.getElementById(`delete-form-${leaveId}`);
                    if (form) {
                        form.submit();
                    }
                }
            }
        });

        // Function to toggle employee leaves with mobile optimization
        window.toggleEmployeeLeaves = function(employeeId) {
            const collapsibleRows = document.querySelectorAll('.employee-' + employeeId);
            const toggleText = document.getElementById('toggle-text-' + employeeId);
            const toggleIcon = document.getElementById('toggle-icon-' + employeeId);
            const button = toggleText.closest('.view-more-btn');
            
            // Add loading state
            if (button) {
                button.style.opacity = '0.7';
                button.style.pointerEvents = 'none';
            }
            
            // Use requestAnimationFrame for smooth animation
            requestAnimationFrame(() => {
                collapsibleRows.forEach((row, index) => {
                    setTimeout(() => {
                        if (row.classList.contains('show')) {
                            row.classList.remove('show');
                            row.style.display = 'none';
                        } else {
                            row.classList.add('show');
                            row.style.display = 'table-row';
                        }
                    }, index * 50); // Stagger animation for better UX
                });
                
                // Update button text and icon
                setTimeout(() => {
                    if (collapsibleRows[0] && collapsibleRows[0].classList.contains('show')) {
                        toggleText.textContent = 'Show Less';
                        toggleIcon.classList.remove('fa-chevron-down');
                        toggleIcon.classList.add('fa-chevron-up');
                    } else {
                        toggleText.textContent = 'View All';
                        toggleIcon.classList.remove('fa-chevron-up');
                        toggleIcon.classList.add('fa-chevron-down');
                    }
                    
                    // Remove loading state
                    if (button) {
                        button.style.opacity = '1';
                        button.style.pointerEvents = 'auto';
                    }
                }, collapsibleRows.length * 50 + 100);
            });
        };

        // Enhanced hover effects for action buttons
        $('.action-btn').hover(
            function() {
                $(this).css('transform', 'translateY(-2px) scale(1.05)');
            },
            function() {
                $(this).css('transform', 'translateY(0) scale(1)');
            }
        );

        // Success message handling
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Great!',
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    confirmButton: 'swal2-confirm'
                },
                buttonsStyling: false
            });
        @endif

        // Error message handling
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    confirmButton: 'swal2-confirm'
                },
                buttonsStyling: false
            });
        @endif

        // Employee selection change handler for leave creation
        $(document).on('change', '#employee_id', function() {
            var employeeId = $(this).val();
            if (employeeId) {
                $.ajax({
                    url: "{{ url('/leave/get-paid-leave-balance') }}" + "/" + employeeId,
                    type: 'GET',
                    success: function(response) {
                        if (response.leavetypes) {
                            $('#leave_type_id').html("<option value='' disabled selected> Select Leave Type </option>");
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
                            if (typeof halfDayLeave === 'function') {
                                halfDayLeave();
                            }
                        }
                    },
                    error: function() {
                        console.log('Error fetching paid leave balance');
                    }
                });
            }
        });

        // Remove model function
        window.removeModel = function() {
            const modal = document.getElementById('commonModalCustom');
            if (modal) {
                modal.remove();
            }
        };
        
        // Mobile-specific enhancements
        if (window.innerWidth <= 768) {
            // Add scroll position indicator for mobile table
            const tableContainer = document.querySelector('.premium-table-container');
            if (tableContainer) {
                let scrollTimeout;
                tableContainer.addEventListener('scroll', function() {
                    // Show scroll position indicator
                    this.classList.add('scrolling');
                    
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        this.classList.remove('scrolling');
                    }, 1000);
                });
            }
            
            // Optimize touch scrolling performance
            document.addEventListener('touchstart', function() {}, { passive: true });
            document.addEventListener('touchmove', function() {}, { passive: true });
        }
        
        // Handle orientation changes
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                // Recalculate table dimensions after orientation change
                const tableContainer = document.querySelector('.premium-table-container');
                if (tableContainer) {
                    tableContainer.style.maxHeight = window.innerHeight < 600 ? '50vh' : '70vh';
                }
                
                // Refresh tooltips if they exist
                if (typeof $().tooltip === 'function') {
                    $('[data-toggle="tooltip"]').tooltip('dispose').tooltip();
                }
            }, 100);
        });
    });

    // Add CSS for mobile enhancements and loading states
    const style = document.createElement('style');
    style.textContent = `
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
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
        
        .loading-row {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .loading-row::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Mobile touch feedback */
        .touch-active {
            transform: scale(0.95) !important;
            opacity: 0.8 !important;
            transition: all 0.1s ease !important;
        }
        
        /* Mobile-specific improvements */
        @media (max-width: 767.98px) {
            .premium-table-container {
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
            }
            
            .premium-table-container.scrolling::before {
                opacity: 1;
                transition: opacity 0.3s ease;
            }
            
            .action-btn, .premium-btn, .view-more-btn {
                -webkit-tap-highlight-color: transparent;
                user-select: none;
                -webkit-user-select: none;
            }
            
            /* Better mobile table scrolling */
            .premium-table-container::-webkit-scrollbar {
                height: 6px;
            }
            
            .premium-table-container::-webkit-scrollbar-thumb {
                background: rgba(37, 99, 235, 0.5);
                border-radius: 3px;
            }
            
            .premium-table-container::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.1);
            }
            
            /* Mobile table row animations */
            .premium-table tbody tr {
                transition: background-color 0.2s ease;
            }
            
            .premium-table tbody tr:active {
                background-color: rgba(37, 99, 235, 0.05) !important;
            }
            
            /* Improved mobile modal positioning */
            .swal2-container {
                padding: 1rem !important;
            }
            
            .swal2-popup {
                margin: 0 !important;
                max-width: calc(100vw - 2rem) !important;
                max-height: calc(100vh - 2rem) !important;
            }
            
            /* Mobile scroll indicator */
            .premium-table-container::before {
                opacity: 0;
                transition: opacity 0.3s ease;
            }
        }
        
        /* Landscape mobile optimizations */
        @media (max-width: 767.98px) and (orientation: landscape) {
            .page-header-compact {
                padding: 1rem !important;
                margin-bottom: 1rem !important;
            }
            
            .stats-compact {
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 0.5rem !important;
                margin-bottom: 1rem !important;
            }
            
            .stat-card-compact {
                padding: 0.75rem !important;
                min-height: 80px !important;
            }
            
            .premium-table-container {
                max-height: 50vh !important;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Add viewport meta tag if not present for proper mobile rendering
    if (!document.querySelector('meta[name="viewport"]')) {
        const viewport = document.createElement('meta');
        viewport.name = 'viewport';
        viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
        document.head.appendChild(viewport);
    }
</script>

@endsection
