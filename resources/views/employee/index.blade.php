@extends('layouts.admin')

@section('page-title')
    @if(isset($_GET['type']) && $_GET['type'] == 'probation')
        {{ __('Probation Employees') }}
    @else
        {{ __('Active Employees') }}
    @endif
@endsection

@push('css-page')
<style>
    :root {
        /* Existing color and shadow variables */
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
        
        /* Responsive breakpoints */
        --breakpoint-xs: 320px;
        --breakpoint-sm: 480px;
        --breakpoint-md: 768px;
        --breakpoint-lg: 1025px;
        --breakpoint-xl: 1441px;
        
        /* Responsive header variables */
        --header-padding-mobile: 20px;
        --header-padding-tablet: 28px;
        --header-padding-desktop: 32px;
        --header-icon-size-mobile: 48px;
        --header-icon-size-tablet: 60px;
        --header-icon-size-desktop: 72px;
        --header-title-size-mobile: 1.5rem;
        --header-title-size-tablet: 1.8rem;
        --header-title-size-desktop: 2rem;
        --header-subtitle-size-mobile: 0.875rem;
        --header-subtitle-size-tablet: 0.95rem;
        --header-subtitle-size-desktop: 1rem;
        
        /* Responsive statistics card variables */
        --stat-card-padding-mobile: 16px;
        --stat-card-padding-tablet: 18px;
        --stat-card-padding-desktop: 20px;
        --stat-number-size-mobile: 1.5rem;
        --stat-number-size-tablet: 1.8rem;
        --stat-number-size-desktop: 2rem;
        --stat-icon-size-mobile: 36px;
        --stat-icon-size-tablet: 42px;
        --stat-icon-size-desktop: 48px;
        --stat-label-size-mobile: 0.65rem;
        --stat-label-size-tablet: 0.7rem;
        --stat-label-size-desktop: 0.75rem;
        
        /* Responsive table variables */
        --table-font-size-mobile: 12px;
        --table-font-size-tablet: 14px;
        --table-font-size-desktop: 16px;
        --table-padding-mobile: 8px 6px;
        --table-padding-tablet: 10px 8px;
        --table-padding-desktop: 12px 15px;
        --table-header-padding-mobile: 12px 8px;
        --table-header-padding-tablet: 16px 10px;
        --table-header-padding-desktop: 24px 16px;
        
        /* Responsive action button variables */
        --action-btn-size-mobile: 44px;
        --action-btn-size-tablet: 40px;
        --action-btn-size-desktop: 36px;
        --action-btn-font-size-mobile: 14px;
        --action-btn-font-size-tablet: 13px;
        --action-btn-font-size-desktop: 12px;
        --action-btn-gap-mobile: 8px;
        --action-btn-gap-tablet: 6px;
        --action-btn-gap-desktop: 4px;
        
        /* Responsive badge and tag variables */
        --badge-padding-mobile: 6px 10px;
        --badge-padding-tablet: 6px 12px;
        --badge-padding-desktop: 8px 16px;
        --badge-font-size-mobile: 10px;
        --badge-font-size-tablet: 11px;
        --badge-font-size-desktop: 12px;
        --tag-padding-mobile: 4px 6px;
        --tag-padding-tablet: 4px 8px;
        --tag-padding-desktop: 4px 12px;
        --tag-font-size-mobile: 9px;
        --tag-font-size-tablet: 10px;
        --tag-font-size-desktop: 11px;
        
        /* Responsive spacing variables */
        --spacing-xs: 4px;
        --spacing-sm: 8px;
        --spacing-md: 16px;
        --spacing-lg: 24px;
        --spacing-xl: 32px;
        --spacing-2xl: 48px;
        
        /* Responsive border radius variables */
        --border-radius-sm: 8px;
        --border-radius-md: 12px;
        --border-radius-lg: 16px;
        --border-radius-xl: 20px;
        --border-radius-2xl: 24px;
        
        /* Touch target variables */
        --touch-target-mobile: 44px;
        --touch-target-tablet: 40px;
        --touch-target-desktop: 36px;
        
        /* Container max-width variables */
        --container-max-width-mobile: 100%;
        --container-max-width-tablet: 100%;
        --container-max-width-desktop: 1200px;
        --container-max-width-large: 1400px;
    }

    /* Responsive utility classes */
    .responsive-container {
        width: 100%;
        max-width: var(--container-max-width-desktop);
        margin: 0 auto;
        padding: 0 var(--spacing-md);
    }

    .responsive-grid {
        display: grid;
        gap: var(--spacing-md);
        grid-template-columns: 1fr;
    }

    .responsive-flex {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-sm);
    }

    .touch-target {
        min-width: var(--touch-target-mobile);
        min-height: var(--touch-target-mobile);
    }

    .text-responsive {
        font-size: clamp(0.875rem, 2.5vw, 1rem);
        line-height: 1.5;
    }

    .hide-mobile {
        display: block;
    }

    .show-mobile {
        display: none;
    }

    .hide-tablet {
        display: block;
    }

    .show-tablet {
        display: none;
    }

    /* Mobile-first responsive grid classes */
    @media (min-width: 480px) {
        .responsive-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 768px) {
        .responsive-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .responsive-grid.four-col {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .hide-tablet {
            display: none;
        }
        
        .show-tablet {
            display: block;
        }
    }

    @media (min-width: 1025px) {
        .responsive-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .responsive-container {
            padding: 0 var(--spacing-lg);
        }
    }

    @media (max-width: 767px) {
        .hide-mobile {
            display: none;
        }
        
        .show-mobile {
            display: block;
        }
        
        .touch-target {
            min-width: var(--touch-target-mobile);
            min-height: var(--touch-target-mobile);
        }
    }

    @media (min-width: 768px) and (max-width: 1024px) {
        .touch-target {
            min-width: var(--touch-target-tablet);
            min-height: var(--touch-target-tablet);
        }
    }

    @media (min-width: 1025px) {
        .touch-target {
            min-width: var(--touch-target-desktop);
            min-height: var(--touch-target-desktop);
        }
    }

    /* Compact Header */
    .page-header-compact {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.95) 0%, 
            rgba(59, 130, 246, 0.95) 50%, 
            rgba(96, 165, 250, 0.95) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius-md);
        padding: var(--header-padding-mobile);
        margin-bottom: var(--spacing-lg);
        box-shadow: 
            0 32px 64px rgba(37, 99, 235, 0.3),
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Mobile-first header responsive design */
    @media (min-width: 768px) {
        .page-header-compact {
            padding: var(--header-padding-tablet);
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-xl);
        }
    }

    @media (min-width: 1025px) {
        .page-header-compact {
            padding: var(--header-padding-desktop) 40px;
            border-radius: var(--border-radius-2xl);
        }
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
        display: flex;
        flex-direction: column;
        gap: var(--spacing-md);
    }

    /* Mobile header layout */
    .page-header-compact .header-content .col-md-6 {
        width: 100%;
        max-width: 100%;
        flex: none;
    }

    .page-header-compact .header-content .col-md-6.d-flex {
        display: flex !important;
        align-items: center;
        gap: var(--spacing-md);
    }

    /* Tablet and desktop header layout */
    @media (min-width: 768px) {
        .page-header-compact .header-content {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: var(--spacing-lg);
        }
        
        .page-header-compact .header-content .col-md-6 {
            width: auto;
            flex: 1;
        }
    }

    .page-title-compact {
        font-size: var(--header-title-size-mobile);
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        letter-spacing: -0.025em;
    }

    @media (min-width: 768px) {
        .page-title-compact {
            font-size: var(--header-title-size-tablet);
        }
    }

    @media (min-width: 1025px) {
        .page-title-compact {
            font-size: var(--header-title-size-desktop);
        }
    }

    .page-subtitle-compact {
        color: rgba(255, 255, 255, 0.9);
        margin: 6px 0 0 0;
        font-size: var(--header-subtitle-size-mobile);
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    @media (min-width: 768px) {
        .page-subtitle-compact {
            font-size: var(--header-subtitle-size-tablet);
        }
    }

    @media (min-width: 1025px) {
        .page-subtitle-compact {
            font-size: var(--header-subtitle-size-desktop);
        }
    }

    .header-icon {
        width: var(--header-icon-size-mobile);
        height: var(--header-icon-size-mobile);
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
        backdrop-filter: blur(20px);
        box-shadow: 
            0 8px 32px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .header-icon {
            width: var(--header-icon-size-tablet);
            height: var(--header-icon-size-tablet);
            font-size: 1.6rem;
            border-radius: var(--border-radius-lg);
        }
    }

    @media (min-width: 1025px) {
        .header-icon {
            width: var(--header-icon-size-desktop);
            height: var(--header-icon-size-desktop);
            font-size: 1.8rem;
            border-radius: var(--border-radius-xl);
        }
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
    
    .premium-title {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }
    
    .premium-subtitle {
        color: rgba(255,255,255,0.9);
        font-size: 1.1rem;
        margin-top: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .premium-actions {
        display: flex;
        gap: var(--spacing-sm);
        align-items: stretch;
        flex-direction: column;
        margin-top: 0;
        position: relative;
        z-index: 1;
        width: 100%;
    }

    @media (min-width: 480px) {
        .premium-actions {
            flex-direction: row;
            align-items: center;
            flex-wrap: wrap;
            width: auto;
        }
    }

    @media (min-width: 768px) {
        .premium-actions {
            gap: var(--spacing-md);
            margin-top: 0;
            justify-content: flex-end;
        }
    }

    @media (min-width: 1025px) {
        .premium-actions {
            gap: 1rem;
            margin-top: 1.5rem;
        }
    }
    
    .premium-btn {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        min-height: var(--touch-target-mobile);
        font-size: 0.875rem;
        flex: 1;
        text-align: center;
    }

    @media (min-width: 480px) {
        .premium-btn {
            flex: none;
            padding: 0.75rem 1.25rem;
        }
    }

    @media (min-width: 768px) {
        .premium-btn {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            min-height: var(--touch-target-tablet);
        }
    }

    @media (min-width: 1025px) {
        .premium-btn {
            min-height: var(--touch-target-desktop);
        }
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
    
    .premium-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: #fff;
        border-radius: var(--border-radius-sm);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: relative;
    }

    /* Mobile table optimizations */
    @media (max-width: 767px) {
        .premium-table-container {
            font-size: var(--table-font-size-mobile);
            border-radius: var(--border-radius-sm);
        }
        
        .premium-table-container::after {
            content: '← Scroll to see more →';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.05);
            text-align: center;
            padding: 4px;
            font-size: 10px;
            color: var(--text-muted);
            pointer-events: none;
        }
    }

    @media (min-width: 768px) and (max-width: 1024px) {
        .premium-table-container {
            font-size: var(--table-font-size-tablet);
        }
    }

    @media (min-width: 1025px) {
        .premium-table-container {
            font-size: var(--table-font-size-desktop);
            border-radius: var(--border-radius-sm);
        }
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
        padding: var(--table-padding-mobile);
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
        font-size: inherit;
    }

    @media (min-width: 768px) {
        .premium-table th,
        .premium-table td {
            padding: var(--table-padding-tablet);
        }
    }

    @media (min-width: 1025px) {
        .premium-table th,
        .premium-table td {
            padding: var(--table-padding-desktop);
        }
    }

    .premium-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        border-top: none;
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
        padding: var(--table-header-padding-mobile);
        border: none;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
    }

    @media (min-width: 768px) {
        .premium-table thead th {
            padding: var(--table-header-padding-tablet);
            font-size: 0.8rem;
        }
    }

    @media (min-width: 1025px) {
        .premium-table thead th {
            padding: var(--table-header-padding-desktop);
            font-size: 0.9rem;
        }
    }
    
    .premium-table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background: linear-gradient(90deg, transparent, #667eea, transparent);
    }
    
    .premium-table tbody tr {
        transition: all 0.3s ease;
        border: none;
    }
    
    .premium-table tbody tr:hover {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        transform: scale(1.001);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }
    
    .premium-table tbody td {
        padding: var(--table-padding-mobile);
        border: none;
        vertical-align: middle;
        color: #2d3748;
        font-weight: 500;
    }

    @media (min-width: 768px) {
        .premium-table tbody td {
            padding: var(--table-padding-tablet);
        }
    }

    @media (min-width: 1025px) {
        .premium-table tbody td {
            padding: 20px 16px;
        }
    }
    
    .premium-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #e2e8f0;
    }

    /* Responsive table column management */
    .premium-table th,
    .premium-table td {
        min-width: 80px;
    }

    /* Mobile: Hide less critical columns */
    @media (max-width: 767px) {
        .premium-table th:nth-child(6),
        .premium-table td:nth-child(6),
        .premium-table th:nth-child(7),
        .premium-table td:nth-child(7) {
            display: none;
        }
        
        .premium-table th:nth-child(1),
        .premium-table td:nth-child(1) {
            min-width: 100px;
        }
        
        .premium-table th:nth-child(2),
        .premium-table td:nth-child(2) {
            min-width: 150px;
        }
        
        .premium-table th:nth-child(3),
        .premium-table td:nth-child(3),
        .premium-table th:nth-child(4),
        .premium-table td:nth-child(4),
        .premium-table th:nth-child(5),
        .premium-table td:nth-child(5) {
            min-width: 120px;
        }
    }

    /* Tablet: Hide only shift time column */
    @media (min-width: 768px) and (max-width: 1024px) {
        .premium-table th:nth-child(7),
        .premium-table td:nth-child(7) {
            display: none;
        }
    }

    /* Desktop: Show all columns */
    @media (min-width: 1025px) {
        .premium-table th,
        .premium-table td {
            min-width: auto;
        }
    }

    #DataTables_Table_0_wrapper{
        overflow: auto!important;
    }
    
    .employee-id-badge {
        background: linear-gradient(135deg, #5c85ff 0%, #5c66ff 100%);
        color: white;
        padding: var(--badge-padding-mobile);
        border-radius: 20px;
        font-weight: 700;
        font-size: var(--badge-font-size-mobile);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow);
        white-space: nowrap;
    }

    @media (min-width: 768px) {
        .employee-id-badge {
            padding: var(--badge-padding-tablet);
            font-size: var(--badge-font-size-tablet);
            gap: 6px;
        }
    }

    @media (min-width: 1025px) {
        .employee-id-badge {
            padding: var(--badge-padding-desktop);
            font-size: var(--badge-font-size-desktop);
        }
    }

    .employee-id-badge:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .employee-id-badge::before {
        content: '#';
        opacity: 0.8;
    }
    
    .employee-name {
        font-weight: 700;
        color: #2d3748;
        font-size: 0.9rem;
        line-height: 1.3;
        margin-bottom: 2px;
    }

    @media (min-width: 768px) {
        .employee-name {
            font-size: 1rem;
        }
    }

    @media (min-width: 1025px) {
        .employee-name {
            font-size: 1.05rem;
        }
    }
    
    .employee-email {
        color: #718096;
        font-size: 0.75rem;
        line-height: 1.2;
        word-break: break-word;
    }

    @media (min-width: 768px) {
        .employee-email {
            font-size: 0.8rem;
        }
    }

    @media (min-width: 1025px) {
        .employee-email {
            font-size: 0.9rem;
        }
    }
    
    .department-badge {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .branch-badge {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .designation-badge {
        background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .action-buttons {
        display: flex;
        gap: var(--action-btn-gap-mobile);
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        padding: var(--spacing-xs) 0;
    }

    @media (min-width: 768px) {
        .action-buttons {
            gap: var(--action-btn-gap-tablet);
        }
    }

    @media (min-width: 1025px) {
        .action-buttons {
            gap: var(--action-btn-gap-desktop);
        }
    }
    
    .action-btn {
        width: var(--action-btn-size-mobile);
        height: var(--action-btn-size-mobile);
        min-width: var(--action-btn-size-mobile);
        min-height: var(--action-btn-size-mobile);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: none;
        cursor: pointer;
        font-size: var(--action-btn-font-size-mobile);
        position: relative;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .action-btn {
            width: var(--action-btn-size-tablet);
            height: var(--action-btn-size-tablet);
            min-width: var(--action-btn-size-tablet);
            min-height: var(--action-btn-size-tablet);
            font-size: var(--action-btn-font-size-tablet);
        }
    }

    @media (min-width: 1025px) {
        .action-btn {
            width: var(--action-btn-size-desktop);
            height: var(--action-btn-size-desktop);
            min-width: var(--action-btn-size-desktop);
            min-height: var(--action-btn-size-desktop);
            font-size: var(--action-btn-font-size-desktop);
        }
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        text-decoration: none;
    }

    /* Touch-friendly hover states */
    @media (hover: none) and (pointer: coarse) {
        .action-btn:hover {
            transform: none;
        }
        
        .action-btn:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }
    }

    /* Enhanced touch feedback */
    .action-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.3s ease, height 0.3s ease;
        pointer-events: none;
    }

    .action-btn:active::before {
        width: 100%;
        height: 100%;
    }
    
    .action-btn-edit {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }
    
    .action-btn-delete {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }
    
    .action-btn-deactivate {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }
    
    .stats-compact {
        margin-bottom: var(--spacing-lg);
    }

    .stats-compact .row {
        margin: 0 -8px;
    }

    .stats-compact .col-lg-3,
    .stats-compact .col-md-6 {
        padding: 0 8px;
        margin-bottom: var(--spacing-md);
    }

    /* Mobile: single column */
    @media (max-width: 479px) {
        .stats-compact .col-lg-3,
        .stats-compact .col-md-6 {
            width: 100%;
            max-width: 100%;
            flex: 0 0 100%;
        }
    }

    /* Large mobile: two columns */
    @media (min-width: 480px) and (max-width: 767px) {
        .stats-compact .col-lg-3,
        .stats-compact .col-md-6 {
            width: 50%;
            max-width: 50%;
            flex: 0 0 50%;
        }
    }

    /* Tablet: 2x2 grid */
    @media (min-width: 768px) and (max-width: 1024px) {
        .stats-compact .col-lg-3,
        .stats-compact .col-md-6 {
            width: 50%;
            max-width: 50%;
            flex: 0 0 50%;
        }
    }

    /* Desktop: four columns */
    @media (min-width: 1025px) {
        .stats-compact .col-lg-3 {
            width: 25%;
            max-width: 25%;
            flex: 0 0 25%;
        }
    }

    .stat-card-compact {
        background: white;
        border-radius: var(--border-radius-md);
        padding: var(--stat-card-padding-mobile);
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        min-height: 100px;
    }

    @media (min-width: 768px) {
        .stat-card-compact {
            padding: var(--stat-card-padding-tablet);
            min-height: 120px;
        }
    }

    @media (min-width: 1025px) {
        .stat-card-compact {
            padding: var(--stat-card-padding-desktop);
            border-radius: var(--border-radius-md);
            min-height: 140px;
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
        gap: var(--spacing-sm);
        flex-wrap: wrap;
    }

    @media (max-width: 479px) {
        .stat-content {
            flex-direction: column;
            text-align: center;
            gap: var(--spacing-xs);
        }
    }

    .stat-number-compact {
        font-size: var(--stat-number-size-mobile);
        font-weight: 900;
        color: var(--text-primary);
        margin: 0;
        line-height: 1;
    }

    @media (min-width: 480px) {
        .stat-number-compact {
            font-size: var(--stat-number-size-tablet);
        }
    }

    @media (min-width: 1025px) {
        .stat-number-compact {
            font-size: var(--stat-number-size-desktop);
        }
    }

    .stat-label-compact {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: var(--stat-label-size-mobile);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 4px 0 0 0;
    }

    @media (min-width: 480px) {
        .stat-label-compact {
            font-size: var(--stat-label-size-tablet);
        }
    }

    @media (min-width: 1025px) {
        .stat-label-compact {
            font-size: var(--stat-label-size-desktop);
        }
    }

    .stat-icon-compact {
        width: var(--stat-icon-size-mobile);
        height: var(--stat-icon-size-mobile);
        border-radius: var(--border-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: transform 0.3s ease;
        flex-shrink: 0;
    }

    @media (min-width: 480px) {
        .stat-icon-compact {
            width: var(--stat-icon-size-tablet);
            height: var(--stat-icon-size-tablet);
            font-size: 1.3rem;
        }
    }

    @media (min-width: 1025px) {
        .stat-icon-compact {
            width: var(--stat-icon-size-desktop);
            height: var(--stat-icon-size-desktop);
            border-radius: var(--border-radius-md);
            font-size: 1.5rem;
        }
    }

    .stat-card-compact:hover .stat-icon-compact {
        transform: scale(1.1);
    }
    
    /* Compact Tags */
    .tag-stack {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
        align-items: flex-start;
    }

    @media (max-width: 479px) {
        .tag-stack {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 2px;
        }
    }

    .info-tag-compact {
        padding: var(--tag-padding-mobile);
        border-radius: var(--border-radius-sm);
        font-weight: 600;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
        border: 1px solid transparent;
        font-size: var(--tag-font-size-mobile);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
    }

    @media (min-width: 768px) {
        .info-tag-compact {
            padding: var(--tag-padding-tablet);
            font-size: var(--tag-font-size-tablet);
            max-width: 120px;
        }
    }

    @media (min-width: 1025px) {
        .info-tag-compact {
            padding: var(--tag-padding-desktop);
            border-radius: var(--border-radius-md);
            font-size: var(--tag-font-size-desktop);
            max-width: none;
        }
    }

    .info-tag-compact:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }

    .branch-tag {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border-color: #fbbf24;
    }

    .department-tag {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border-color: #22c55e;
    }

    .designation-tag {
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        color: #6b21a8;
        border-color: #8b5cf6;
    }

    /* Loading State */
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

    /* Enhanced Responsive SweetAlert2 Styling */
    .swal2-popup {
        border-radius: var(--border-radius-lg) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        padding: 1.5rem !important;
        margin: 1rem !important;
        max-width: calc(100vw - 2rem) !important;
        width: auto !important;
    }

    .swal2-title {
        color: var(--text-primary) !important;
        font-weight: 700 !important;
        font-size: 1.25rem !important;
        line-height: 1.3 !important;
        margin-bottom: 1rem !important;
    }

    .swal2-content {
        color: var(--text-secondary) !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        margin-bottom: 1.5rem !important;
    }

    .swal2-actions {
        gap: var(--spacing-sm) !important;
        flex-direction: column !important;
        width: 100% !important;
        margin: 0 !important;
    }

    .swal2-confirm,
    .swal2-cancel {
        border-radius: var(--border-radius-lg) !important;
        font-weight: 600 !important;
        padding: 12px 20px !important;
        font-size: 0.875rem !important;
        min-height: var(--touch-target-mobile) !important;
        width: 100% !important;
        margin: 0 !important;
        border: none !important;
    }

    .swal2-confirm {
        background: linear-gradient(135deg, var(--danger), #f87171) !important;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3) !important;
        order: 2 !important;
    }

    .swal2-cancel {
        background: linear-gradient(135deg, var(--text-secondary), #9ca3af) !important;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3) !important;
        order: 1 !important;
    }

    .swal2-confirm.swal2-styled.swal2-deactivate {
        background: linear-gradient(135deg, var(--warning), #fbbf24) !important;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3) !important;
    }

    /* Tablet responsive modal styles */
    @media (min-width: 768px) {
        .swal2-popup {
            padding: 2rem !important;
            margin: 2rem !important;
            max-width: 500px !important;
            width: auto !important;
        }

        .swal2-title {
            font-size: 1.4rem !important;
        }

        .swal2-content {
            font-size: 1rem !important;
        }

        .swal2-actions {
            flex-direction: row !important;
            justify-content: center !important;
        }

        .swal2-confirm,
        .swal2-cancel {
            width: auto !important;
            min-width: 120px !important;
            min-height: var(--touch-target-tablet) !important;
        }

        .swal2-confirm {
            order: 1 !important;
        }

        .swal2-cancel {
            order: 2 !important;
        }
    }

    /* Desktop responsive modal styles */
    @media (min-width: 1025px) {
        .swal2-popup {
            padding: 2.5rem !important;
            max-width: 600px !important;
        }

        .swal2-title {
            font-size: 1.5rem !important;
        }

        .swal2-confirm,
        .swal2-cancel {
            min-height: var(--touch-target-desktop) !important;
            padding: 12px 24px !important;
            font-size: 1rem !important;
        }
    }

    /* Modal backdrop responsive adjustments */
    .swal2-container {
        padding: 1rem !important;
    }

    @media (min-width: 768px) {
        .swal2-container {
            padding: 2rem !important;
        }
    }

    /* Performance optimizations */
    
    /* Reduce motion for users who prefer it */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        
        .page-header-compact::before {
            animation: none !important;
        }
        
        .premium-table tbody tr:hover {
            transform: none !important;
        }
        
        .action-btn:hover {
            transform: none !important;
        }
    }

    /* GPU acceleration for smooth animations */
    .page-header-compact,
    .stat-card-compact,
    .premium-card,
    .action-btn,
    .premium-btn {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Optimize font rendering */
    .page-title-compact,
    .stat-number-compact,
    .employee-name {
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Efficient scrolling */
    .premium-table-container {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }

    .premium-table-container::-webkit-scrollbar {
        height: 8px;
    }

    .premium-table-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .premium-table-container::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 4px;
    }

    .premium-table-container::-webkit-scrollbar-thumb:hover {
        background: var(--text-muted);
    }

    /* Contain layout shifts */
    .stat-card-compact,
    .premium-card {
        contain: layout style paint;
    }

    /* Optimize repaints */
    .action-btn::before,
    .ripple {
        contain: strict;
    }

    /* Critical CSS for above-the-fold content */
    .page-header-compact,
    .stats-compact {
        contain: layout;
    }

    /* Responsive debugging utilities (only in development) */
    .responsive-debug {
        position: fixed;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-family: monospace;
        z-index: 9999;
        pointer-events: none;
    }

    .responsive-debug::before {
        content: 'XS';
    }

    @media (min-width: 480px) {
        .responsive-debug::before {
            content: 'SM';
        }
    }

    @media (min-width: 768px) {
        .responsive-debug::before {
            content: 'MD';
        }
    }

    @media (min-width: 1025px) {
        .responsive-debug::before {
            content: 'LG';
        }
    }

    @media (min-width: 1441px) {
        .responsive-debug::before {
            content: 'XL';
        }
    }

    /* Validation helpers for touch targets */
    .touch-target-debug {
        outline: 2px dashed red !important;
        outline-offset: 2px !important;
    }

    .touch-target-debug::after {
        content: 'Touch target too small';
        position: absolute;
        background: red;
        color: white;
        padding: 2px 4px;
        font-size: 10px;
        top: -20px;
        left: 0;
        white-space: nowrap;
        z-index: 1000;
    }

    /* Layout validation helpers */
    .layout-debug * {
        outline: 1px solid rgba(255, 0, 0, 0.3) !important;
    }

    .layout-debug .responsive-grid {
        outline: 2px solid blue !important;
    }

    .layout-debug .responsive-flex {
        outline: 2px solid green !important;
    }

    /* Performance monitoring styles */
    .performance-monitor {
        position: fixed;
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-family: monospace;
        z-index: 9999;
        max-width: 200px;
    }

    /* Accessibility validation */
    .a11y-debug [tabindex]:focus {
        outline: 3px solid #ff6b6b !important;
        outline-offset: 2px !important;
    }

    .a11y-debug button:focus,
    .a11y-debug a:focus,
    .a11y-debug input:focus,
    .a11y-debug select:focus {
        outline: 3px solid #4ecdc4 !important;
        outline-offset: 2px !important;
    }

    #employeeTable_filter{
        width: fit-content;
        float: inline-end;
    }
    
    @media (max-width: 768px) {
        .premium-header {
            padding: 1.5rem;
        }
        
        .premium-title {
            font-size: 2rem;
        }
        
        .premium-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .premium-btn {
            justify-content: center;
        }
        
        .premium-table-container {
            overflow-x: auto;
        }
        .premium-table-container .dataTable {
            width: 100% !important;
            table-layout: auto;
        }
    }

    /* Legacy styles - now handled by responsive system above */

    .branch-tag {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .department-tag {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }

    .designation-tag {
        background-color: #e8f5e8;
        color: #388e3c;
    }

    /* Legacy action button styles - removed duplicate definitions */

    .action-btn-deactivate {
        background-color: #ffc107;
        color: #212529;
    }

    .action-btn-deactivate:hover {
        background-color: #e0a800;
        color: #212529;
    }

    .action-btn-edit {
        background-color: #17a2b8;
        color: white;
    }

    .action-btn-edit:hover {
        background-color: #138496;
        color: white;
        text-decoration: none;
    }

    .action-btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .action-btn-delete:hover {
        background-color: #c82333;
        color: white;
    }

    /* Legacy responsive styles - now handled by new responsive system */

    /* Responsive search and pagination controls */
    .employee-controls,
    .employee-pagination {
        margin: 0 -8px;
    }

    .employee-controls > div,
    .employee-pagination > div {
        padding: 0 8px;
    }

    .entries-control {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .entries-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        white-space: nowrap;
    }

    .entries-select {
        width: auto;
        min-width: 80px;
        padding: 6px 12px;
        font-size: 0.875rem;
        border-radius: var(--border-radius-sm);
        border: 1px solid var(--border);
    }

    .search-control {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
    }

    .search-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .search-input {
        width: 100%;
        padding: 8px 12px;
        font-size: 0.875rem;
        border-radius: var(--border-radius-sm);
        border: 1px solid var(--border);
        min-height: var(--touch-target-mobile);
    }

    .table-info {
        font-size: 0.875rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        min-height: var(--touch-target-mobile);
    }

    .pagination-controls {
        display: flex;
        gap: var(--spacing-sm);
        justify-content: flex-start;
    }

    .pagination-btn {
        min-height: var(--touch-target-mobile);
        padding: 8px 16px;
        font-size: 0.875rem;
        border-radius: var(--border-radius-sm);
        flex: 1;
        max-width: 120px;
    }

    /* Mobile-specific adjustments */
    @media (max-width: 767px) {
        .search-control {
            margin-top: var(--spacing-sm);
        }
        
        .search-label {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-xs);
        }
        
        .pagination-controls {
            justify-content: center;
            margin-top: var(--spacing-sm);
        }
        
        .pagination-btn {
            flex: 1;
            max-width: none;
        }
        
        .table-info {
            text-align: center;
            justify-content: center;
        }
    }

    /* Tablet and desktop adjustments */
    @media (min-width: 768px) {
        .search-control {
            align-items: flex-end;
        }
        
        .search-input {
            width: 250px;
            max-width: 100%;
        }
        
        .pagination-controls {
            justify-content: flex-end;
        }
        
        .pagination-btn {
            min-height: var(--touch-target-tablet);
        }
        
        .search-input {
            min-height: var(--touch-target-tablet);
        }
        
        .table-info {
            min-height: var(--touch-target-tablet);
        }
    }

    @media (min-width: 1025px) {
        .pagination-btn {
            min-height: var(--touch-target-desktop);
        }
        
        .search-input {
            min-height: var(--touch-target-desktop);
        }
        
        .table-info {
            min-height: var(--touch-target-desktop);
        }
    }

    /* CSS for ripple effect */
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
</style>
@endpush

@section('content')
    <!-- Premium Header Section -->
    <div class="page-header-compact">
        <div class="header-content">
            <div class="col-md-6 d-flex">
                <div class="header-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="ml-3">
                    <h1 class="page-title-compact">
                        @if(isset($_GET['type']) && $_GET['type'] == 'probation')
                            {{ __('Probation Employees') }}
                        @else
                            {{ __('Active Employees') }} 
                        @endif
                    </h1>
                    <p class="page-subtitle-compact">{{ __('Manage your team with premium tools and insights') }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="premium-actions">
                    @can('Create Employee')
                        @if(isset($_GET['type']) && $_GET['type'] == 'probation')
                            <a href="{{route('employee.index')}}" class="premium-btn">
                                <i class="fas fa-users"></i> 
                                <span class="hide-mobile">{{ __('Active Employees') }}</span>
                                <span class="show-mobile">{{ __('Active') }}</span>
                            </a>
                        @else
                            <a href="{{ route('employee.index', ['type' => 'probation']) }}" class="premium-btn">
                                <i class="fas fa-user-clock"></i> 
                                <span class="hide-mobile">{{ __('Probation Employees') }}</span>
                                <span class="show-mobile">{{ __('Probation') }}</span>
                            </a>
                        @endif
                        <a href="{{ route('employee.create') }}" class="premium-btn premium-btn-primary">
                            <i class="fas fa-plus"></i> 
                            <span class="hide-mobile">{{ __('Add Employee') }}</span>
                            <span class="show-mobile">{{ __('Add') }}</span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row stats-compact">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ count($employees) }}</h3>
                        <p class="stat-label-compact">{{ __('Total Employees') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #c4d3f9 0%, #b4d3f5 100%); color: #3a3ded;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $employees->where('is_active', 1)->count() }}</h3>
                        <p class="stat-label-compact">{{ __('Active') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #059669;">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $employees->where('company_doj', '>=', now()->startOfYear())->count() }}</h3>
                        <p class="stat-label-compact">{{ __('This Year') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $employees->unique('department_id')->count() }}</h3>
                        <p class="stat-label-compact">{{ __('Departments') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706;">
                        <i class="far fa-building"></i>
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
                        <table class="table premium-table" style="width: 100%;" id="employeeTable">
                            <thead>
                                <tr>
                                    <th style="min-width: 120px;">{{ __('Employee ID') }}</th>
                                    <th style="min-width: 200px;">{{ __('Employee Details') }}</th>
                                    <th style="min-width: 150px;">{{ __('Department') }}</th>
                                    <th style="min-width: 120px;">{{ __('Branch') }}</th>
                                    <th style="min-width: 150px;">{{ __('Designation') }}</th>
                                    <th style="min-width: 120px;">{{ __('Joining Date') }}</th>
                                    <th style="min-width: 100px;">{{ __('Shift Time') }}</th>
                                    @if (Gate::check('Edit Employee') || Gate::check('Delete Employee'))
                                        <th class="text-center" style="min-width: 120px;">{{ __('Actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr id="employee-row-{{ $employee->id }}">
                                        <td>
                                            @can('Show Employee')
                                                <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" class="employee-id-badge">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </a>
                                            @else
                                                <span class="employee-id-badge">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td>
                                            <div class="employee-name">{{ $employee->name }}</div>
                                            <div class="employee-email">{{ $employee->email }}</div>
                                        </td>
                                        <td>
                                            <div class="tag-stack">
                                                <span class="info-tag-compact branch-tag">
                                                    {{ !empty(\Auth::user()->getDepartment($employee->department_id)) ? \Auth::user()->getDepartment($employee->department_id)->name : 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tag-stack">
                                                <span class="info-tag-compact department-tag">
                                                    {{ !empty(\Auth::user()->getBranch($employee->branch_id)) ? \Auth::user()->getBranch($employee->branch_id)->name : 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tag-stack">
                                                <span class="info-tag-compact designation-tag">
                                                    {{ !empty(\Auth::user()->getDesignation($employee->designation_id)) ? \Auth::user()->getDesignation($employee->designation_id)->name : 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>
                                                {{ !empty($employee->company_doj) ? date('d M, Y', strtotime($employee->company_doj)) : 'N/A' }}
                                            </strong>
                                        </td>
                                        <td>
                                            <strong>
                                                {{ !empty($employee->shift_start) ? date('h:i A', strtotime($employee->shift_start)) : 'N/A' }}
                                            </strong>
                                        </td>
                                        @if (Gate::check('Edit Employee') || Gate::check('Delete Employee'))
                                            <td>
                                                <div class="action-buttons">
                                                    @if ($employee->is_active == 1)
                                                        @can('Edit Employee')
                                                            <button type="button" 
                                                                    class="action-btn action-btn-deactivate deactivate-employee" 
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Deactivate User') }}"
                                                                    data-employee-id="{{ $employee->id }}"
                                                                    data-employee-name="{{ $employee->name }}">
                                                                <i class="fas fa-user-slash"></i>
                                                            </button>
                                                            
                                                            {{-- Hidden Form for Deactivate --}}
                                                            <form id="deactivate-form-{{ $employee->id }}" 
                                                                action="{{ route('employee.deactivate', $employee->id) }}" 
                                                                method="GET" 
                                                                style="display: none;">
                                                            </form>

                                                            <a href="{{ route('employee.edit', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" 
                                                            class="action-btn action-btn-edit" 
                                                            data-toggle="tooltip"
                                                            data-original-title="{{ __('Edit Employee') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endcan
                                                        @can('Delete Employee')
                                                            <button type="button" 
                                                                    class="action-btn action-btn-delete delete-employee" 
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Delete Employee') }}"
                                                                    data-employee-id="{{ $employee->id }}"
                                                                    data-employee-name="{{ $employee->name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            
                                                            {{-- Hidden Form for Delete --}}
                                                            <form id="delete-form-{{ $employee->id }}" 
                                                                action="{{ route('employee.destroy', $employee->id) }}" 
                                                                method="POST" 
                                                                style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Handle session messages separately to avoid Blade directive conflicts --}}
<script>
// Handle session messages first
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Success!',
                text: {!! json_encode(session('success')) !!},
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
        }
    });
@endif

@if(session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error!',
                text: {!! json_encode(session('error')) !!},
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
        }
    });
@endif
</script>

<script>
$(document).ready(function() {
    console.log('Initializing employee table...');
    
    // Performance optimizations
    let resizeTimeout;
    let isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    // Detect viewport size for responsive behavior
    function getViewportSize() {
        const width = window.innerWidth;
        if (width < 480) return 'xs';
        if (width < 768) return 'sm';
        if (width < 1025) return 'md';
        if (width < 1441) return 'lg';
        return 'xl';
    }
    
    let currentViewport = getViewportSize();
    
    // Debounced resize handler
    function handleResize() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const newViewport = getViewportSize();
            if (newViewport !== currentViewport) {
                currentViewport = newViewport;
                updateEmployeeTable();
                console.log('Viewport changed to:', currentViewport);
            }
        }, 150);
    }
    
    // Add resize listener with debouncing
    window.addEventListener('resize', handleResize, { passive: true });
    
    // Simple search and pagination for employee table
    var currentPage = 1;
    var entriesPerPage = 25;
    var searchTerm = '';
    
    // Cache DOM elements for better performance
    const $tableContainer = $('.premium-table-container');
    const $employeeTable = $('#employeeTable');
    const $tableBody = $employeeTable.find('tbody');
    let $allRows = null; // Will be cached after first use
    
    // Add responsive controls
    function addEmployeeControls() {
        var controlsHtml = `
            <div class="row mb-3 employee-controls">
                <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <div class="entries-control">
                        <label class="entries-label">Show 
                            <select id="employeeEntriesSelect" class="form-select entries-select">
                                <option value="10">10</option>
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </label>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="search-control">
                        <label class="search-label">Search: 
                            <input type="text" id="employeeSearchInput" class="form-control search-input" placeholder="Search employees...">
                        </label>
                    </div>
                </div>
            </div>
        `;
        
        var paginationHtml = `
            <div class="row mt-3 employee-pagination">
                <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <div id="employeeTableInfo" class="table-info">Showing 1 to 25 of 0 entries</div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="pagination-controls">
                        <button id="employeePrevBtn" class="btn btn-sm btn-outline-primary pagination-btn">Previous</button>
                        <button id="employeeNextBtn" class="btn btn-sm btn-outline-primary pagination-btn">Next</button>
                    </div>
                </div>
            </div>
        `;
        
        $('.premium-table-container').before(controlsHtml);
        $('.premium-table-container').after(paginationHtml);
    }
    
    // Optimized filter and paginate employees
    function updateEmployeeTable() {
        // Cache rows if not already cached
        if (!$allRows) {
            $allRows = $tableBody.find('tr');
        }
        
        var filteredRows = $allRows;
        
        // Apply search filter with performance optimization
        if (searchTerm) {
            const searchLower = searchTerm.toLowerCase();
            filteredRows = $allRows.filter(function() {
                // Use textContent for better performance than jQuery .text()
                return this.textContent.toLowerCase().indexOf(searchLower) > -1;
            });
        }
        
        var totalFiltered = filteredRows.length;
        var totalPages = Math.ceil(totalFiltered / entriesPerPage);
        
        // Validate current page
        if (currentPage > totalPages) currentPage = Math.max(1, totalPages);
        if (currentPage < 1) currentPage = 1;
        
        // Batch DOM updates for better performance
        requestAnimationFrame(() => {
            // Hide all rows
            $allRows.hide();
            
            // Show current page rows
            var startIndex = (currentPage - 1) * entriesPerPage;
            var endIndex = Math.min(startIndex + entriesPerPage, totalFiltered);
            
            filteredRows.slice(startIndex, endIndex).show();
        });
        
        // Update info
        var showingStart = totalFiltered > 0 ? startIndex + 1 : 0;
        $('#employeeTableInfo').text(`Showing ${showingStart} to ${endIndex} of ${totalFiltered} entries`);
        
        // Update buttons
        $('#employeePrevBtn').prop('disabled', currentPage <= 1);
        $('#employeeNextBtn').prop('disabled', currentPage >= totalPages);
    }
    
    // Initialize controls and event handlers
    addEmployeeControls();
    
    // Debounced search for better performance
    let searchTimeout;
    $(document).on('input', '#employeeSearchInput', function() {
        const value = $(this).val();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchTerm = value;
            currentPage = 1;
            updateEmployeeTable();
        }, 300); // 300ms debounce
    });
    
    $(document).on('change', '#employeeEntriesSelect', function() {
        entriesPerPage = parseInt($(this).val());
        currentPage = 1;
        updateEmployeeTable();
    });
    
    $(document).on('click', '#employeePrevBtn', function() {
        if (currentPage > 1) {
            currentPage--;
            updateEmployeeTable();
        }
    });
    
    $(document).on('click', '#employeeNextBtn', function() {
        if (!$allRows) $allRows = $tableBody.find('tr');
        
        var filteredRows = searchTerm ? 
            $allRows.filter(function() { return this.textContent.toLowerCase().indexOf(searchTerm.toLowerCase()) > -1; }) : 
            $allRows;
        var totalPages = Math.ceil(filteredRows.length / entriesPerPage);
        
        if (currentPage < totalPages) {
            currentPage++;
            updateEmployeeTable();
        }
    });
    
    // Responsive testing and validation utilities
    window.ResponsiveTest = {
        // Test viewport detection
        testViewportDetection: function() {
            console.log('Current viewport:', currentViewport);
            console.log('Window dimensions:', window.innerWidth + 'x' + window.innerHeight);
            console.log('Touch device:', isTouch);
            return {
                viewport: currentViewport,
                dimensions: { width: window.innerWidth, height: window.innerHeight },
                isTouch: isTouch
            };
        },
        
        // Test responsive breakpoints
        testBreakpoints: function() {
            const breakpoints = {
                xs: window.matchMedia('(max-width: 479px)').matches,
                sm: window.matchMedia('(min-width: 480px) and (max-width: 767px)').matches,
                md: window.matchMedia('(min-width: 768px) and (max-width: 1024px)').matches,
                lg: window.matchMedia('(min-width: 1025px) and (max-width: 1440px)').matches,
                xl: window.matchMedia('(min-width: 1441px)').matches
            };
            console.log('Breakpoint matches:', breakpoints);
            return breakpoints;
        },
        
        // Test touch targets
        testTouchTargets: function() {
            const touchElements = $('.action-btn, .premium-btn, .pagination-btn, .search-input, .entries-select');
            const results = [];
            
            touchElements.each(function() {
                const $el = $(this);
                const rect = this.getBoundingClientRect();
                const minSize = currentViewport === 'xs' || currentViewport === 'sm' ? 44 : 
                               currentViewport === 'md' ? 40 : 36;
                
                const result = {
                    element: this.tagName + (this.className ? '.' + this.className.split(' ').join('.') : ''),
                    width: rect.width,
                    height: rect.height,
                    meetsMinimum: rect.width >= minSize && rect.height >= minSize,
                    expectedMinimum: minSize
                };
                
                results.push(result);
                
                if (!result.meetsMinimum) {
                    console.warn('Touch target too small:', result);
                }
            });
            
            console.log('Touch target test results:', results);
            return results;
        },
        
        // Test responsive layout
        testResponsiveLayout: function() {
            const tests = {
                headerStacking: $('.page-header-compact .header-content').css('flex-direction'),
                statsLayout: $('.stats-compact .row').children().first().css('width'),
                tableScrollable: $('.premium-table-container')[0].scrollWidth > $('.premium-table-container')[0].clientWidth,
                modalResponsive: $('.swal2-popup').length > 0 ? $('.swal2-popup').css('max-width') : 'No modal present'
            };
            
            console.log('Layout test results:', tests);
            return tests;
        },
        
        // Test performance metrics
        testPerformance: function() {
            const start = performance.now();
            updateEmployeeTable();
            const tableUpdateTime = performance.now() - start;
            
            const metrics = {
                tableUpdateTime: tableUpdateTime + 'ms',
                cachedRows: $allRows ? $allRows.length : 0,
                viewport: currentViewport,
                memoryUsage: performance.memory ? {
                    used: Math.round(performance.memory.usedJSHeapSize / 1048576) + 'MB',
                    total: Math.round(performance.memory.totalJSHeapSize / 1048576) + 'MB'
                } : 'Not available'
            };
            
            console.log('Performance metrics:', metrics);
            return metrics;
        },
        
        // Run all tests
        runAllTests: function() {
            console.log('=== Responsive Design Test Suite ===');
            const results = {
                viewport: this.testViewportDetection(),
                breakpoints: this.testBreakpoints(),
                touchTargets: this.testTouchTargets(),
                layout: this.testResponsiveLayout(),
                performance: this.testPerformance()
            };
            
            console.log('=== Test Suite Complete ===');
            return results;
        }
    };
    
    // Auto-run basic tests in development
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        setTimeout(() => {
            console.log('Running responsive design validation...');
            ResponsiveTest.testViewportDetection();
            ResponsiveTest.testTouchTargets();
        }, 1000);
    }
    
    // Initialize table
    updateEmployeeTable();

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Enhanced delete confirmation with SweetAlert2
    $(document).on('click', '.delete-employee', function(e) {
        e.preventDefault();
        
        const employeeId = $(this).data('employee-id');
        const employeeName = $(this).data('employee-name');
        const row = $(this).closest('tr');
        
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                html: `Do you want to delete employee <strong>"${employeeName}"</strong>?<br><small class="text-muted">This action cannot be undone and will permanently remove all employee data.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete employee!',
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
                        title: 'Deleting Employee...',
                        text: 'Please wait while we process your request.',
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
                        const form = document.getElementById(`delete-form-${employeeId}`);
                        if (form) {
                            form.submit();
                        }
                    });
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert2 is not available
            const confirmMessage = `Are you sure you want to delete employee "${employeeName}"? This action cannot be undone and will permanently remove all employee data.`;
            if (confirm(confirmMessage)) {
                // Add loading state to the row
                row.addClass('loading-row');
                
                // Submit the form
                const form = document.getElementById(`delete-form-${employeeId}`);
                if (form) {
                    form.submit();
                }
            }
        }
    });

    // Enhanced deactivate confirmation with SweetAlert2
    $(document).on('click', '.deactivate-employee', function(e) {
        e.preventDefault();
        
        const employeeId = $(this).data('employee-id');
        const employeeName = $(this).data('employee-name');
        const row = $(this).closest('tr');
        
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Deactivate Employee?',
                html: `Do you want to deactivate employee <strong>"${employeeName}"</strong>?<br><small class="text-muted">The employee will be moved to inactive status and won't be able to access the system.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-user-slash mr-2"></i>Yes, deactivate!',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    content: 'swal2-content',
                    confirmButton: 'swal2-confirm swal2-deactivate',
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
                        title: 'Deactivating Employee...',
                        text: 'Please wait while we process your request.',
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
                        const form = document.getElementById(`deactivate-form-${employeeId}`);
                        if (form) {
                            form.submit();
                        }
                    });
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert2 is not available
            const confirmMessage = `Are you sure you want to deactivate employee "${employeeName}"? The employee will be moved to inactive status.`;
            if (confirm(confirmMessage)) {
                // Add loading state to the row
                row.addClass('loading-row');
                
                // Submit the form
                const form = document.getElementById(`deactivate-form-${employeeId}`);
                if (form) {
                    form.submit();
                }
            }
        }
    });
    
    // Add smooth scrolling for better UX
    $('html').css('scroll-behavior', 'smooth');
    
    // Animate table rows on load
    $('.premium-table tbody tr').each(function(index) {
        $(this).css({
            'animation-delay': (index * 0.05) + 's',
            'animation-fill-mode': 'forwards'
        });
    });

    // Enhanced hover effects for action buttons
    $('.action-btn').hover(
        function() {
            $(this).css('transform', 'translateY(-2px) scale(1.05)');
        },
        function() {
            $(this).css('transform', 'translateY(0) scale(1)');
        }
    );

    // Add ripple effect to action buttons
    $(document).on('click', '.action-btn', function(e) {
        const button = $(this);
        const ripple = $('<span class="ripple"></span>');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.css({
            width: size + 'px',
            height: size + 'px',
            left: x + 'px',
            top: y + 'px'
        });
        
        button.append(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });

    // Add responsive debugging in development
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        // Add debug indicator
        $('body').append('<div class="responsive-debug"></div>');
        
        // Add keyboard shortcut for testing (Ctrl+Shift+R)
        $(document).on('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'R') {
                e.preventDefault();
                ResponsiveTest.runAllTests();
            }
        });
        
        // Add console helper
        console.log('%c🎯 Responsive Design Testing Available', 'color: #4ecdc4; font-weight: bold;');
        console.log('%c• Use ResponsiveTest.runAllTests() to run all tests', 'color: #666;');
        console.log('%c• Use Ctrl+Shift+R to run tests quickly', 'color: #666;');
        console.log('%c• Individual tests: testViewportDetection(), testTouchTargets(), etc.', 'color: #666;');
    }

}); // End of $(document).ready

// Global responsive utilities
window.ResponsiveUtils = {
    // Get current breakpoint
    getCurrentBreakpoint: function() {
        const width = window.innerWidth;
        if (width < 480) return 'xs';
        if (width < 768) return 'sm';
        if (width < 1025) return 'md';
        if (width < 1441) return 'lg';
        return 'xl';
    },
    
    // Check if touch device
    isTouchDevice: function() {
        return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    },
    
    // Validate touch target size
    validateTouchTarget: function(element) {
        const rect = element.getBoundingClientRect();
        const minSize = this.isTouchDevice() ? 44 : 36;
        return rect.width >= minSize && rect.height >= minSize;
    },
    
    // Get optimal font size for current viewport
    getOptimalFontSize: function(baseSize) {
        const breakpoint = this.getCurrentBreakpoint();
        const multipliers = { xs: 0.875, sm: 0.9, md: 0.95, lg: 1, xl: 1.1 };
        return baseSize * (multipliers[breakpoint] || 1);
    }
};
</script>
@endsection