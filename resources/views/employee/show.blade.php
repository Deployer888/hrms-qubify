@extends('layouts.admin')

@section('page-title')
{{ __('Employee Details') }}
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

        /* Responsive Header Variables */
        --header-padding-mobile: 20px 24px;
        --header-padding-tablet: 28px 32px;
        --header-padding-desktop: 32px 40px;
        
        --header-icon-size-mobile: 48px;
        --header-icon-size-tablet: 60px;
        --header-icon-size-desktop: 72px;
        
        --title-size-mobile: 1.5rem;
        --title-size-tablet: 1.8rem;
        --title-size-desktop: 2rem;
        
        /* Responsive Card Variables */
        --card-padding-mobile: 16px;
        --card-padding-tablet: 20px;
        --card-padding-desktop: 24px;
        
        --card-header-padding-mobile: 16px 20px;
        --card-header-padding-tablet: 18px 22px;
        --card-header-padding-desktop: 20px 24px;
        
        /* Responsive Table Variables */
        --table-font-mobile: 12px;
        --table-font-tablet: 14px;
        --table-font-desktop: 16px;
        
        --table-padding-mobile: 0.75rem 0.5rem;
        --table-padding-tablet: 1rem 0.75rem;
        --table-padding-desktop: 1.25rem 1rem;
        
        --table-header-padding-mobile: 1rem 0.5rem;
        --table-header-padding-tablet: 1.25rem 0.75rem;
        --table-header-padding-desktop: 1.5rem 1rem;
        
        /* Responsive Touch Target Variables */
        --touch-target-mobile: 44px;
        --touch-target-tablet: 40px;
        --touch-target-desktop: 36px;
        
        /* Responsive Spacing Variables */
        --section-spacing-mobile: 20px;
        --section-spacing-tablet: 28px;
        --section-spacing-desktop: 32px;
        
        --row-spacing-mobile: 16px;
        --row-spacing-tablet: 24px;
        --row-spacing-desktop: 32px;
        
        /* Responsive Modal Variables */
        --modal-padding-mobile: 16px;
        --modal-padding-tablet: 20px;
        --modal-padding-desktop: 24px;
        
        /* Responsive Document Grid Variables */
        --doc-grid-cols-mobile: 2;
        --doc-grid-cols-tablet: 3;
        --doc-grid-cols-desktop: 4;
        
        --doc-preview-size-mobile: 40px;
        --doc-preview-size-tablet: 44px;
        --doc-preview-size-desktop: 48px;
    }

    body {
        background: var(--light);
        min-height: 100vh;
    }

    .content-wrapper {
        background: transparent;
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
        padding: var(--header-padding-desktop);
        margin-bottom: var(--section-spacing-desktop);
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
        align-items: center;
    }

    .page-title-compact {
        font-size: var(--title-size-desktop);
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        letter-spacing: -0.025em;
        display: inline-flex;
        align-items: center;
    }

    .page-subtitle-compact {
        color: rgba(255, 255, 255, 0.9);
        margin: 6px 0 0 0;
        font-size: 1rem;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .header-icon {
        width: var(--header-icon-size-desktop);
        height: var(--header-icon-size-desktop);
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

    .premium-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        margin-bottom: 24px;
    }
    
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }

    .premium-card-header {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);
        padding: var(--card-header-padding-desktop);
        border-bottom: 1px solid rgba(37, 99, 235, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .premium-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .premium-card-title i {
        font-size: 1rem;
        padding: 8px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: var(--shadow);
    }

    .premium-card-body {
        padding: var(--card-padding-desktop);
    }

    /* Info Styling */
    .info-item {
        margin-bottom: 20px;
    }

    .info-label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.4;
    }

    /* Special Elements */
    .salary-container, .pin-container {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(59, 130, 246, 0.08) 100%);
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid rgba(37, 99, 235, 0.15);
        transition: all 0.3s ease;
        width: fit-content;
    }

    .salary-container:hover, .pin-container:hover {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.12) 0%, rgba(59, 130, 246, 0.12) 100%);
        border-color: rgba(37, 99, 235, 0.25);
    }

    .btn-eye {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        color: white;
        cursor: pointer;
        padding: 6px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
        min-width: var(--touch-target-desktop);
        min-height: var(--touch-target-desktop);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-eye:hover {
        background: linear-gradient(135deg, var(--secondary), var(--primary));
        transform: scale(1.05);
        box-shadow: var(--shadow-md);
    }

    .probation-badge {
        background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
        color: #c2410c;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow);
        border: 1px solid rgba(196, 65, 12, 0.2);
    }

    /* Premium Table Styling */
    .leave-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        margin-bottom: 0px!important;
        background-color: #fff;
    }

    .leave-table thead {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);
    }

    .leave-table thead th {
        font-weight: 700;
        color: #4a5568;
        padding: var(--table-header-padding-desktop);
        border: none;
        font-size: var(--table-font-desktop);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        text-align: center;
    }

    .leave-table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background: linear-gradient(90deg, transparent, #667eea, transparent);
    }

    .leave-table tbody tr {
        transition: all 0.3s ease;
        border: none;
    }

    .leave-table tbody tr:hover {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        transform: scale(1.001);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }

    .leave-table tbody td {
        padding: var(--table-padding-desktop);
        border: none;
        vertical-align: middle;
        color: #2d3748;
        font-weight: 500;
        text-align: center;
        font-size: var(--table-font-desktop);
    }

    .leave-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #e2e8f0;
    }

    /* Document Preview */
    .preview-container {
        display: inline-block;
        align-items: center;
        gap: 10px;
        padding: 12px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
        border-radius: 12px;
        border: 1px solid rgba(37, 99, 235, 0.1);
        transition: all 0.3s ease;
    }

    .preview-container:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(59, 130, 246, 0.08) 100%);
        border-color: rgba(37, 99, 235, 0.2);
    }

    .preview-container img {
        width: var(--doc-preview-size-desktop);
        height: var(--doc-preview-size-desktop);
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(37, 99, 235, 0.2);
        box-shadow: var(--shadow);
    }

    /* Enhanced SweetAlert2 Styling */
    .swal2-popup {
        border-radius: 20px !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        padding: var(--modal-padding-desktop) !important;
    }

    .swal2-title {
        color: var(--text-primary) !important;
        font-weight: 700 !important;
        font-size: 1.5rem !important;
    }

    .swal2-content {
        color: var(--text-secondary) !important;
        font-size: 1rem !important;
    }

    .swal2-confirm {
        background: linear-gradient(135deg, var(--danger), #f87171) !important;
        border-radius: 25px !important;
        font-weight: 600 !important;
        padding: 12px 24px !important;
        font-size: 1rem !important;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3) !important;
    }

    .swal2-cancel {
        background: linear-gradient(135deg, var(--text-secondary), #9ca3af) !important;
        border-radius: 25px !important;
        font-weight: 600 !important;
        padding: 12px 24px !important;
        font-size: 1rem !important;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3) !important;
    }

    /* Premium Modal Styling */
    .premium-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .premium-modal .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        border: none;
        padding: var(--modal-padding-desktop);
    }

    .premium-modal .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .premium-modal .modal-body {
        padding: var(--modal-padding-desktop);
        font-size: 1rem;
        color: var(--text-primary);
        line-height: 1.6;
    }

    /* Card Heights - Uniform Height System */
    .fixed-height-card,
    .medium-height-card,
    .short-height-card {
        min-height: 450px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* Ensure equal height columns */
    .row {
        display: flex;
        flex-wrap: wrap;
    }

    .row > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }

    .premium-card {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .premium-card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .premium-card-body .row {
        flex: 1;
        display: flex;
        flex-wrap: wrap;
        align-content: flex-start;
    }

    .premium-card-body .col-md-6 {
        display: flex;
        flex-direction: column;
    }

    .info-item {
        margin-bottom: 20px;
        flex: 0 0 auto;
    }

    /* Responsive Design */
    /* Tablet Styles */
    @media (min-width: 768px) and (max-width: 1024px) {
        .page-header-compact {
            padding: var(--header-padding-tablet);
            margin-bottom: var(--section-spacing-tablet);
        }
        
        .page-title-compact {
            font-size: var(--title-size-tablet);
        }
        
        .header-icon {
            width: var(--header-icon-size-tablet);
            height: var(--header-icon-size-tablet);
            font-size: 1.6rem;
        }
        
        .premium-actions {
            margin-top: 1.25rem;
        }
        
        /* Card Responsive Styles */
        .premium-card-header {
            padding: var(--card-header-padding-tablet);
        }
        
        .premium-card-body {
            padding: var(--card-padding-tablet);
        }
        
        .fixed-height-card,
        .medium-height-card,
        .short-height-card {
            min-height: 400px;
        }
        
        /* Table Responsive Styles */
        .leave-table thead th {
            padding: var(--table-header-padding-tablet);
            font-size: var(--table-font-tablet);
        }
        
        .leave-table tbody td {
            padding: var(--table-padding-tablet);
            font-size: var(--table-font-tablet);
        }
        
        /* Touch Target Optimization */
        .btn-eye {
            min-width: var(--touch-target-tablet);
            min-height: var(--touch-target-tablet);
            padding: 8px;
        }
        
        .premium-btn {
            min-height: var(--touch-target-tablet);
            padding: 0.75rem 1.5rem;
        }
        
        /* Document Grid Responsive */
        .preview-container img {
            width: var(--doc-preview-size-tablet);
            height: var(--doc-preview-size-tablet);
        }
        
        .preview-container {
            padding: 10px;
        }
        
        /* 3-column layout on tablet */
        .row.text-center .col-md-4.col-lg-3 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
        
        /* Modal Responsive Styles */
        .premium-modal .modal-header {
            padding: var(--modal-padding-tablet);
        }
        
        .premium-modal .modal-body {
            padding: var(--modal-padding-tablet);
        }
        
        .swal2-popup {
            padding: var(--modal-padding-tablet) !important;
        }
        
        .swal2-confirm, .swal2-cancel {
            min-height: var(--touch-target-tablet);
            padding: 10px 20px !important;
        }
    }

    /* Mobile Styles */
    @media (max-width: 767px) {
        .page-header-compact {
            padding: var(--header-padding-mobile);
            margin-bottom: var(--section-spacing-mobile);
        }
        
        .page-title-compact {
            font-size: var(--title-size-mobile);
        }
        
        .header-icon {
            width: var(--header-icon-size-mobile);
            height: var(--header-icon-size-mobile);
            font-size: 1.4rem;
        }
        
        /* Header Content Stacking */
        .header-content {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        
        .header-content .col-md-6:first-child {
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .header-content .col-md-6:last-child {
            width: 100%;
        }
        
        .premium-actions {
            flex-direction: column;
            align-items: stretch;
            margin-top: 1rem;
            width: 100%;
            float: none !important;
        }
        
        .premium-btn {
            justify-content: center;
            width: 100%;
        }
        
        /* Card Responsive Styles */
        .premium-card {
            margin-bottom: 16px;
        }
        
        .premium-card-header {
            padding: var(--card-header-padding-mobile);
        }
        
        .premium-card-body {
            padding: var(--card-padding-mobile);
        }
        
        .fixed-height-card,
        .medium-height-card,
        .short-height-card {
            min-height: auto; /* Remove fixed heights on mobile */
        }
        
        /* Single column layout for mobile */
        .row.align-stretch .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        /* Mobile alignment and spacing fixes */
        .row + .row {
            margin-top: var(--row-spacing-mobile);
        }

        .row.align-stretch {
            margin-bottom: var(--row-spacing-mobile);
        }

        .row > [class*='col-'] {
            margin-bottom: 16px;
        }

        .row > [class*='col-']:last-child {
            margin-bottom: 0;
        }
        
        /* Table Responsive Styles */
        .leave-table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(37, 99, 235, 0.3) transparent;
        }
        
        .leave-table-container::-webkit-scrollbar {
            height: 6px;
        }
        
        .leave-table-container::-webkit-scrollbar-track {
            background: rgba(37, 99, 235, 0.1);
            border-radius: 3px;
        }
        
        .leave-table-container::-webkit-scrollbar-thumb {
            background: rgba(37, 99, 235, 0.3);
            border-radius: 3px;
        }
        
        .leave-table-container::-webkit-scrollbar-thumb:hover {
            background: rgba(37, 99, 235, 0.5);
        }
        
        .leave-table {
            min-width: 500px; /* Ensure table doesn't get too compressed */
        }
        
        .leave-table thead th {
            padding: var(--table-header-padding-mobile);
            font-size: var(--table-font-mobile);
            white-space: nowrap;
        }
        
        .leave-table tbody td {
            padding: var(--table-padding-mobile);
            font-size: var(--table-font-mobile);
            white-space: nowrap;
        }
        
        /* Touch Target Optimization */
        .btn-eye {
            min-width: var(--touch-target-mobile);
            min-height: var(--touch-target-mobile);
            padding: 10px;
            border-radius: 10px;
        }
        
        .premium-btn {
            min-height: var(--touch-target-mobile);
            padding: 1rem 1.5rem;
            font-size: 1rem;
        }
        
        .salary-container, .pin-container {
            padding: 12px 16px;
            gap: 12px;
        }
        
        /* Document Grid Responsive */
        .preview-container img {
            width: var(--doc-preview-size-mobile);
            height: var(--doc-preview-size-mobile);
        }
        
        .preview-container {
            padding: 8px;
        }
        
        /* Force 2-column layout on mobile */
        .row.text-center .col-md-4.col-lg-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        /* Document remove button touch optimization */
        .preview-container .btn-danger {
            min-width: var(--touch-target-mobile);
            min-height: var(--touch-target-mobile);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Modal Responsive Styles */
        .premium-modal .modal-dialog {
            margin: 1rem;
            max-width: calc(100% - 2rem);
        }
        
        .premium-modal .modal-content {
            border-radius: 16px;
        }
        
        .premium-modal .modal-header {
            padding: var(--modal-padding-mobile);
            border-radius: 16px 16px 0 0;
        }
        
        .premium-modal .modal-body {
            padding: var(--modal-padding-mobile);
            font-size: 0.9rem;
        }
        
        .premium-modal .modal-title {
            font-size: 1.1rem;
        }
        
        .premium-modal .close {
            min-width: var(--touch-target-mobile);
            min-height: var(--touch-target-mobile);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        /* SweetAlert2 Mobile Styles */
        .swal2-popup {
            padding: var(--modal-padding-mobile) !important;
            margin: 1rem !important;
            width: calc(100% - 2rem) !important;
            max-width: 400px !important;
            border-radius: 16px !important;
        }
        
        .swal2-title {
            font-size: 1.2rem !important;
        }
        
        .swal2-content {
            font-size: 0.9rem !important;
        }
        
        .swal2-confirm, .swal2-cancel {
            min-height: var(--touch-target-mobile);
            padding: 12px 20px !important;
            font-size: 0.9rem !important;
            border-radius: 20px !important;
        }
        
        .swal2-actions {
            gap: 10px;
        }
    }

    /* Extra Small Mobile Styles */
    @media (max-width: 479px) {
        .page-header-compact {
            border-radius: 16px;
        }
        
        .page-title-compact {
            font-size: 1.3rem;
        }
        
        .header-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            border-radius: 12px;
        }
        
        .premium-card-header {
            padding: var(--card-header-padding-mobile);
        }
        
        .premium-card-body {
            padding: var(--card-padding-mobile);
        }
        
        .premium-card {
            border-radius: 16px;
        }
        
        .leave-table {
            min-width: 450px; /* Slightly smaller minimum width for extra small screens */
        }
        
        .leave-table thead th {
            padding: 0.5rem 0.25rem;
            font-size: 10px;
            letter-spacing: 0.25px;
        }
        
        .leave-table tbody td {
            padding: 0.5rem 0.25rem;
            font-size: 11px;
        }
        
        .fixed-height-card,
        .medium-height-card,
        .short-height-card {
            min-height: auto;
        }

        /* Enhanced mobile spacing */
        .row + .row {
            margin-top: var(--row-spacing-mobile);
        }

        .row.align-stretch {
            margin-bottom: var(--row-spacing-mobile);
        }

        .premium-card {
            margin-bottom: 12px;
        }
        
        /* Touch Target Optimization */
        .btn-eye {
            min-width: var(--touch-target-mobile);
            min-height: var(--touch-target-mobile);
            padding: 12px;
            border-radius: 12px;
        }
        
        .premium-btn {
            min-height: var(--touch-target-mobile);
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
        }
        
        .salary-container, .pin-container {
            padding: 14px 18px;
            gap: 14px;
            border-radius: 14px;
        }
        
        /* Document Grid Responsive */
        .preview-container img {
            width: var(--doc-preview-size-mobile);
            height: var(--doc-preview-size-mobile);
        }
        
        .preview-container {
            padding: 6px;
            border-radius: 10px;
        }
        
        /* Keep 2-column layout but with smaller spacing */
        .row.text-center .col-md-4.col-lg-3 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 8px;
        }
        
        /* Document remove button optimization */
        .preview-container .btn-danger {
            min-width: var(--touch-target-mobile);
            min-height: var(--touch-target-mobile);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Modal Responsive Styles */
        .premium-modal .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .premium-modal .modal-content {
            border-radius: 12px;
        }
        
        .premium-modal .modal-header {
            padding: var(--modal-padding-mobile);
            border-radius: 12px 12px 0 0;
        }
        
        .premium-modal .modal-body {
            padding: var(--modal-padding-mobile);
            font-size: 0.85rem;
        }
        
        .premium-modal .modal-title {
            font-size: 1rem;
        }
        
        /* SweetAlert2 Extra Small Mobile Styles */
        .swal2-popup {
            padding: var(--modal-padding-mobile) !important;
            margin: 0.5rem !important;
            width: calc(100% - 1rem) !important;
            max-width: 350px !important;
            border-radius: 12px !important;
        }
        
        .swal2-title {
            font-size: 1.1rem !important;
        }
        
        .swal2-content {
            font-size: 0.85rem !important;
        }
        
        .swal2-confirm, .swal2-cancel {
            min-height: var(--touch-target-mobile);
            padding: 14px 18px !important;
            font-size: 0.85rem !important;
            border-radius: 18px !important;
        }
    }

    /* Responsive Spacing and Layout Utilities */
    
    /* Base responsive spacing utilities */
    .responsive-section {
        margin-bottom: var(--section-spacing-desktop);
    }
    
    .responsive-row-spacing {
        margin-top: var(--row-spacing-desktop);
    }
    
    .responsive-card-spacing {
        margin-bottom: 32px;
    }
    
    /* Responsive container utilities */
    .responsive-container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    /* Responsive text utilities */
    .responsive-text-center {
        text-align: center;
    }
    
    /* Tablet spacing utilities */
    @media (min-width: 768px) and (max-width: 1024px) {
        .responsive-section {
            margin-bottom: var(--section-spacing-tablet);
        }
        
        .responsive-row-spacing {
            margin-top: var(--row-spacing-tablet);
        }
        
        .responsive-card-spacing {
            margin-bottom: 28px;
        }
        
        .responsive-container {
            padding-left: 20px;
            padding-right: 20px;
        }
    }
    
    /* Mobile spacing utilities */
    @media (max-width: 767px) {
        .responsive-section {
            margin-bottom: var(--section-spacing-mobile);
        }
        
        .responsive-row-spacing {
            margin-top: var(--row-spacing-mobile);
        }
        
        .responsive-card-spacing {
            margin-bottom: 20px;
        }
        
        .responsive-container {
            padding-left: 16px;
            padding-right: 16px;
        }
        
        /* Mobile-specific layout utilities */
        .mobile-stack {
            flex-direction: column !important;
        }
        
        .mobile-full-width {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
        
        .mobile-center {
            text-align: center !important;
        }
        
        .mobile-no-margin {
            margin: 0 !important;
        }
        
        .mobile-small-margin {
            margin: 8px !important;
        }
    }
    
    /* Extra small mobile spacing utilities */
    @media (max-width: 479px) {
        .responsive-section {
            margin-bottom: var(--section-spacing-mobile);
        }
        
        .responsive-row-spacing {
            margin-top: var(--row-spacing-mobile);
        }
        
        .responsive-card-spacing {
            margin-bottom: 16px;
        }
        
        .responsive-container {
            padding-left: 12px;
            padding-right: 12px;
        }
    }

    /* Special handling for table containers */
    .leave-table-container {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Content section spacing */
    .content-wrapper {
        padding-top: 0;
    }

    /* Ensure proper spacing from header */
    .page-header-compact + .row {
        margin-top: 0;
    }

    /* Additional alignment fixes */
    .row.align-stretch {
        align-items: stretch;
    }

    .col-md-6 .premium-card,
    .col-md-12 .premium-card {
        height: 100%;
        margin-bottom: 0;
    }

    /* Ensure consistent vertical spacing using responsive variables */
    .row + .row {
        margin-top: var(--row-spacing-desktop);
    }

    /* Add spacing between card sections */
    .premium-card {
        margin-bottom: var(--section-spacing-desktop);
    }

    /* Enhanced spacing for better visual separation */
    .row.align-stretch {
        margin-bottom: var(--section-spacing-desktop);
    }

    .row.align-stretch:last-child {
        margin-bottom: 0;
    }

    /* Utility classes for consistent spacing */
    .spacing-section {
        margin-bottom: var(--section-spacing-desktop);
    }

    /* Responsive spacing adjustments */
    @media (min-width: 768px) and (max-width: 1024px) {
        .row + .row {
            margin-top: var(--row-spacing-tablet);
        }
        
        .premium-card {
            margin-bottom: var(--section-spacing-tablet);
        }
        
        .row.align-stretch {
            margin-bottom: var(--section-spacing-tablet);
        }
        
        .spacing-section {
            margin-bottom: var(--section-spacing-tablet);
        }
    }

    @media (max-width: 767px) {
        .row + .row {
            margin-top: var(--row-spacing-mobile);
        }
        
        .premium-card {
            margin-bottom: var(--section-spacing-mobile);
        }
        
        .row.align-stretch {
            margin-bottom: var(--section-spacing-mobile);
        }
        
        .spacing-section {
            margin-bottom: var(--section-spacing-mobile);
        }
    }

    /* Desktop specific overrides */
    @media (min-width: 1025px) {
        .premium-card {
            margin-bottom: 0; /* Remove bottom margin on desktop for card grids */
        }
    }

    .spacing-section:last-child {
        margin-bottom: 0;
    }

    /* Performance Optimizations for Mobile */
    
    /* Optimize animations for mobile devices */
    @media (max-width: 767px) {
        /* Reduce animation complexity on mobile */
        .premium-card:hover {
            transform: translateY(-2px); /* Reduced from -5px */
        }
        
        .btn-eye:hover {
            transform: scale(1.02); /* Reduced from 1.05 */
        }
        
        .premium-btn:hover {
            transform: translateY(-1px); /* Reduced from -2px */
        }
        
        /* Optimize background animations for mobile */
        .page-header-compact::before {
            animation-duration: 30s; /* Slower animation for better performance */
        }
        
        /* Disable complex hover effects on touch devices */
        .leave-table tbody tr:hover {
            transform: none; /* Remove scale transform on mobile */
        }
        
        /* Optimize scrolling performance */
        .leave-table-container {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Reduce box-shadow complexity on mobile */
        .premium-card {
            box-shadow: 0 10px 20px rgba(0,0,0,0.08); /* Simplified shadow */
        }
        
        .premium-card:hover {
            box-shadow: 0 15px 30px rgba(0,0,0,0.12); /* Simplified hover shadow */
        }
    }
    
    /* Extra performance optimizations for very small screens */
    @media (max-width: 479px) {
        /* Further reduce animations */
        .premium-card:hover {
            transform: none; /* Remove hover transform entirely */
        }
        
        .btn-eye:hover {
            transform: none; /* Remove hover transform */
        }
        
        .premium-btn:hover {
            transform: none; /* Remove hover transform */
        }
        
        /* Simplify shadows further */
        .premium-card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.06);
        }
        
        .premium-card:hover {
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
        }
        
        /* Disable background animation on very small screens */
        .page-header-compact::before {
            animation: none;
        }
    }
    
    /* CSS-only responsive solutions */
    
    /* Use CSS-only sticky positioning for table headers */
    .leave-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    /* CSS-only smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Optimize font rendering for mobile */
    @media (max-width: 767px) {
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeSpeed; /* Prioritize speed over quality on mobile */
        }
    }
    
    /* Optimize image rendering */
    .preview-container img {
        image-rendering: -webkit-optimize-contrast; /* Optimize for mobile */
        image-rendering: crisp-edges;
    }
    
    /* Reduce repaints and reflows */
    .premium-card,
    .premium-btn,
    .btn-eye {
        will-change: transform; /* Hint to browser for optimization */
    }
    
    /* Remove will-change on mobile to save memory */
    @media (max-width: 767px) {
        .premium-card,
        .premium-btn,
        .btn-eye {
            will-change: auto;
        }
    }
    
    /* Optimize backdrop-filter for mobile */
    @media (max-width: 767px) {
        .page-header-compact {
            backdrop-filter: blur(10px); /* Reduced from 20px */
        }
        
        .header-icon {
            backdrop-filter: blur(10px); /* Reduced from 20px */
        }
        
        .premium-btn {
            backdrop-filter: blur(5px); /* Reduced from 10px */
        }
    }
    
    /* Memory optimization for mobile */
    @media (max-width: 479px) {
        .page-header-compact {
            backdrop-filter: none; /* Remove backdrop-filter entirely on very small screens */
            background: linear-gradient(135deg, 
                rgba(37, 99, 235, 0.98) 0%, 
                rgba(59, 130, 246, 0.98) 50%, 
                rgba(96, 165, 250, 0.98) 100%); /* Increase opacity to compensate */
        }
        
        .header-icon {
            backdrop-filter: none;
            background: rgba(255, 255, 255, 0.2);
        }
        
        .premium-btn {
            backdrop-filter: none;
            background: rgba(255,255,255,0.25);
        }
    }
    
    /* Critical CSS Performance Optimizations */
    
    /* Optimize CSS containment for better performance */
    .premium-card {
        contain: layout style paint;
    }
    
    .leave-table-container {
        contain: layout style;
    }
    
    .preview-container {
        contain: layout style paint;
    }
    
    /* Optimize reflow and repaint */
    @media (max-width: 767px) {
        /* Use transform instead of changing layout properties */
        .info-item {
            transform: translateZ(0); /* Force hardware acceleration */
        }
        
        /* Optimize table rendering */
        .leave-table {
            table-layout: fixed; /* Faster table rendering */
        }
        
        /* Optimize image loading */
        .preview-container img {
            loading: lazy; /* Native lazy loading */
            decoding: async; /* Async image decoding */
        }
    }
    
    /* Reduce CSS complexity on mobile */
    @media (max-width: 479px) {
        /* Simplify gradients */
        .premium-card-header {
            background: #f8f9ff; /* Solid color instead of gradient */
        }
        
        .leave-table thead {
            background: #f8f9ff; /* Solid color instead of gradient */
        }
        
        /* Simplify button gradients */
        .premium-btn-primary {
            background: #ff6b6b; /* Solid color instead of gradient */
        }
        
        .btn-eye {
            background: var(--primary); /* Solid color instead of gradient */
        }
    }
    
    /* Optimize media query efficiency */
    @media (max-width: 767px) and (orientation: portrait) {
        /* Portrait-specific optimizations */
        .row.align-stretch {
            flex-direction: column;
        }
    }
    
    @media (max-width: 767px) and (orientation: landscape) {
        /* Landscape-specific optimizations */
        .page-header-compact {
            padding: var(--header-padding-mobile);
        }
    }

    /* Card container spacing */
    .card-container {
        padding: 0 15px;
    }

    .preview-container:hover .btn-danger {
        opacity: 1;
    }

    .preview-container .btn-danger {
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    /* Responsive Testing and Validation Utilities */
    
    /* Breakpoint indicator for testing (only visible in development) */
    .responsive-breakpoint-indicator {
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
        display: none; /* Hidden by default */
    }
    
    /* Show breakpoint indicator when testing */
    body.responsive-testing .responsive-breakpoint-indicator {
        display: block;
    }
    
    /* Breakpoint-specific indicators */
    @media (max-width: 479px) {
        .responsive-breakpoint-indicator::after {
            content: "XS Mobile (≤479px)";
        }
    }
    
    @media (min-width: 480px) and (max-width: 767px) {
        .responsive-breakpoint-indicator::after {
            content: "Mobile (480px-767px)";
        }
    }
    
    @media (min-width: 768px) and (max-width: 1024px) {
        .responsive-breakpoint-indicator::after {
            content: "Tablet (768px-1024px)";
        }
    }
    
    @media (min-width: 1025px) and (max-width: 1440px) {
        .responsive-breakpoint-indicator::after {
            content: "Desktop (1025px-1440px)";
        }
    }
    
    @media (min-width: 1441px) {
        .responsive-breakpoint-indicator::after {
            content: "Large Desktop (≥1441px)";
        }
    }
    
    /* Layout validation helpers */
    .responsive-layout-debug {
        outline: 2px solid red !important;
        background: rgba(255, 0, 0, 0.1) !important;
    }
    
    .responsive-layout-debug::before {
        content: "DEBUG";
        position: absolute;
        top: -20px;
        left: 0;
        background: red;
        color: white;
        padding: 2px 6px;
        font-size: 10px;
        font-family: monospace;
    }
    
    /* Touch target validation */
    .touch-target-debug {
        position: relative;
    }
    
    .touch-target-debug::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 44px;
        height: 44px;
        border: 2px solid lime;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    body.touch-testing .touch-target-debug::after {
        opacity: 0.7;
    }
    
    /* Responsive grid overlay for testing */
    .responsive-grid-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 9998;
        display: none;
    }
    
    body.grid-testing .responsive-grid-overlay {
        display: block;
        background-image: 
            linear-gradient(to right, rgba(255, 0, 0, 0.1) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 0, 0, 0.1) 1px, transparent 1px);
        background-size: 20px 20px;
    }
    
    /* Accessibility testing helpers */
    .accessibility-debug {
        outline: 3px solid blue !important;
    }
    
    .accessibility-debug[tabindex]:focus {
        outline: 3px solid orange !important;
        outline-offset: 2px;
    }
    
    /* Performance testing indicators */
    .performance-warning {
        position: relative;
    }
    
    .performance-warning::before {
        content: "⚠️ PERF";
        position: absolute;
        top: -15px;
        right: -15px;
        background: orange;
        color: white;
        padding: 2px 4px;
        font-size: 8px;
        border-radius: 2px;
        font-family: monospace;
        display: none;
    }
    
    body.performance-testing .performance-warning::before {
        display: block;
    }
    
    /* Responsive validation classes */
    .validate-mobile-only {
        display: none;
    }
    
    @media (max-width: 767px) {
        .validate-mobile-only {
            display: block;
        }
        
        .validate-desktop-only {
            display: none !important;
        }
    }
    
    .validate-tablet-only {
        display: none;
    }
    
    @media (min-width: 768px) and (max-width: 1024px) {
        .validate-tablet-only {
            display: block;
        }
    }
    
    .validate-desktop-only {
        display: none;
    }
    
    @media (min-width: 1025px) {
        .validate-desktop-only {
            display: block;
        }
    }
    
    /* Overflow detection for testing */
    .overflow-debug {
        position: relative;
    }
    
    .overflow-debug::after {
        content: "OVERFLOW";
        position: absolute;
        top: 0;
        right: 0;
        background: red;
        color: white;
        padding: 2px 6px;
        font-size: 10px;
        font-family: monospace;
        display: none;
    }
    
    body.overflow-testing .overflow-debug::-webkit-scrollbar {
        background: red;
    }
    
    /* Responsive image testing */
    .responsive-image-debug img {
        outline: 2px solid green;
    }
    
    .responsive-image-debug img::after {
        content: attr(width) "x" attr(height);
        position: absolute;
        bottom: 0;
        left: 0;
        background: green;
        color: white;
        padding: 2px 4px;
        font-size: 10px;
        font-family: monospace;
    }
    
    /* Testing utilities for JavaScript functionality */
    .js-testing .salary-container,
    .js-testing .pin-container {
        border: 2px dashed blue;
    }
    
    .js-testing .premium-btn {
        border: 2px dashed green;
    }
    
    .js-testing .btn-eye {
        border: 2px dashed purple;
    }
    
    /* Media query testing helpers */
    @media print {
        .responsive-breakpoint-indicator,
        .responsive-layout-debug,
        .touch-target-debug::after,
        .responsive-grid-overlay {
            display: none !important;
        }
    }
    
    /* High contrast mode testing */
    @media (prefers-contrast: high) {
        .premium-card {
            border: 2px solid currentColor;
        }
        
        .btn-eye {
            border: 2px solid currentColor;
        }
    }
    
    /* Reduced motion testing */
    @media (prefers-reduced-motion: reduce) {
        .premium-card,
        .premium-btn,
        .btn-eye,
        .page-header-compact::before {
            animation: none !important;
            transition: none !important;
        }
    }
    
    /* Dark mode testing preparation */
    @media (prefers-color-scheme: dark) {
        .responsive-breakpoint-indicator {
            background: rgba(255, 255, 255, 0.9);
            color: black;
        }
    }
</style>
@endpush

@section('content')
    @if($employee)
    @if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
        <!-- Premium Header Section -->
        <div class="page-header-compact">
            <div class="header-content d-flex justify-content-between align-items-center">
                <div class="col-md-6 d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="ml-3">
                        <h1 class="page-title-compact d-flex align-items-center">
                            {{ $employee->name }}
                        </h1>
                        <p class="page-subtitle-compact">{{ __('Employee Profile & Details') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="premium-actions">
                        @can('Edit Employee')
                            <a href="{{route('employee.edit',\Illuminate\Support\Facades\Crypt::encrypt($employee->id))}}" class="premium-btn premium-btn-primary">
                                <i class="fa fa-edit"></i> {{ __('Edit') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endif

    <div class="row align-stretch">
        <div class="col-md-6">
            <div class="premium-card fixed-height-card">
                <div class="premium-card-header">
                    <h6 class="premium-card-title">
                        <i class="fas fa-user-circle text-primary"></i>
                        {{__('Personal Details')}}
                    </h6>
                </div>
                <div class="premium-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Employee ID')}}</div>
                                <p class="info-value">{{$employeesId ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Name')}}</div>
                                <p class="info-value">{{$employee->name ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Official Email')}}</div>
                                <p class="info-value">{{$employee->email ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Personal Email')}}</div>
                                <p class="info-value">{{$employee->user->personal_email ?? 'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Date of Birth')}}</div>
                                <p class="info-value">{{ $employee && $employee->dob ? \Auth::user()->dateFormat($employee->dob) : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Phone')}}</div>
                                <p class="info-value">{{$employee->phone ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Address')}}</div>
                                <p class="info-value">{{$employee->address ?? ''}}</p>
                            </div>
                        </div>
                        @if($employee)
                        @if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Salary')}}</div>
                                <div class="salary-container">
                                    <span id="hidden-salary" class="salary-text">*****</span>
                                    <span id="actual-salary" class="salary-text" style="display: none;">₹ {{$employee->salary}}</span>
                                    <button type="button" id="toggle-salary" class="btn-eye" onclick="toggleSalary()">
                                        <i id="eye-icon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="premium-card fixed-height-card">
                <div class="premium-card-header">
                    <h6 class="premium-card-title">
                        <i class="fas fa-building text-success"></i>
                        {{__('Company Details')}}
                    </h6>
                    @if($employee->is_probation == 1)
                        <span class="probation-badge">On-Probation</span>
                    @endif
                </div>
                <div class="premium-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Branch')}}</div>
                                <p class="info-value">{{!empty($employee->branch)?$employee->branch->name:'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Department')}}</div>
                                <p class="info-value">{{!empty($employee->department)?$employee->department->name:'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Designation')}}</div>
                                <p class="info-value">{{!empty($employee->designation)?$employee->designation->name:'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Date Of Joining')}}</div>
                                <p class="info-value">{{ !empty($employee->company_doj) ? \Auth::user()->dateFormat($employee->company_doj) : 'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Shift Start')}}</div>
                                <p class="info-value">{{!empty($employee->shift_start)?$employee->shift_start:'N/A'}}</p>
                            </div>
                        </div>
                        @if($employee->is_team_leader == 0)
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Team Leader')}}</div>
                                <p class="info-value">{{ !empty($teamLeaderDetails) ? $teamLeaderDetails->name : 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                        @if($employee->user_id == auth::user()->id || \Auth::user()->type == 'company')
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Clock In Pin')}}</div>
                                <div class="pin-container">
                                    <span id="hidden-pin" class="pin-text">******</span>
                                    <span id="actual-pin" class="pin-text" style="display: none;">{{$employee->clock_in_pin ?? 'Not Set'}}</span>
                                    <button type="button" id="toggle-pin" class="btn-eye" onclick="togglePin()">
                                        <i id="pin-eye-icon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($employee->is_active == 0)
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Date Of Exit')}}</div>
                                <p class="info-value">{{\Auth::user()->dateFormat($employee->date_of_exit)}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__(!empty($employee->termination) ? 'Termination Reason' : 'Resignation Reason')}}</div>
                                <p class="info-value">
                                    @php
                                        $description = !empty($employee->termination)
                                            ? $employee->termination->description??''
                                            : $employee->resignation->description??'';
                                    @endphp

                                    {{ Str::limit($description, 80) }}

                                    @if (strlen($description) > 80)
                                        <a href="javascript:void(0)" class="read-more-btn text-info text-underline" data-description="{{ $description }}"><b>Read More</b></a>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <?php use Carbon\Carbon; ?>
        @if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
            <div class="col-md-6 mt-4">
                <div class="premium-card medium-height-card">
                    <div class="premium-card-header">
                        <h6 class="premium-card-title">
                            <i class="fas fa-calendar-check text-warning"></i>
                            {{__('Annual Leave Details')}}
                        </h6>
                    </div>
                    <div class="premium-card-body">
                        @php
                            $formattedDate = Carbon::today()->format('Y-m-d');
                            $employeedoc = $employee->documents()->pluck('document_value','document_id');
                            $companyDoj = Carbon::parse($employee->company_doj);
                            $currentYear = Carbon::now();
                            $totalLeaves = 0;
                        @endphp
                        <div class="leave-table-container">
                            <table class="leave-table">
                            <thead>
                                <tr>
                                    <th>{{__('Leave Types')}}</th>
                                    <th>{{__('Total Leaves')}}</th>
                                    <th>{{__('Leaves Available')}}</th>
                                    <th>{{__('Leaves Availed')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $key=>$leave)
                                    @if($employee->gender == "Male" && $leave->title == "Maternity Leaves") @continue; @endif
                                    @if($employee->gender == "Female" && $leave->title == "Paternity Leaves") @continue; @endif
                                    @if($employee->is_probation == 0 || $leave->title == 'Sick Leave')
                                    <tr>
                                        <td><strong>{{$leave->title }}</strong></td>
                                        <td>
                                            @php $totalLeaves = $leave->days; @endphp
                                            @if($employee->is_probation == 1)
                                                {{ $totalLeaves - 2 }}
                                            @else
                                                {{ $totalLeaves }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($leave->title == 'Paid Leave')
                                                @php 
                                                // For paid leave, use real-time balance calculation
                                                if ($employee->is_probation == 1) {
                                                    $availableBalance = 0;
                                                    $leavesAvailed = 0;
                                                } else {
                                                    $breakdown = $employee->getBalanceBreakdown();
                                                    $availableBalance = $breakdown['available_balance']; // Use available_balance to account for pending
                                                    $leavesAvailed = $breakdown['total_availed']; // Use total_availed to include pending leaves
                                                }
                                                @endphp
                                                {{ $availableBalance }}
                                            @else
                                                @php
                                                $leavesAvailed = \App\Helpers\Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leave->id);
                                                @endphp
                                                @if($employee->is_probation == 1)
                                                    {{ max(0, $leave->days - $leavesAvailed - 2) }}
                                                @else
                                                    {{ max(0, $leave->days - $leavesAvailed) }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($leave->title == 'Paid Leave')
                                                {{ $leavesAvailed }}
                                            @else
                                                @php
                                                $leavesAvailed = \App\Helpers\Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leave->id);
                                                @endphp
                                                {{ $leavesAvailed }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-md-6 mt-4">
                <div class="premium-card medium-height-card">
                    <div class="premium-card-header">
                        <h6 class="premium-card-title">
                            <i class="fas fa-university text-info"></i>
                            {{__('Bank Account Details')}}
                        </h6>
                    </div>
                    <div class="premium-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Account Holder Name')}}</div>
                                    <p class="info-value">{{$employee->account_holder_name}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Account Number')}}</div>
                                    <p class="info-value">{{$employee->account_number}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Bank Name')}}</div>
                                    <p class="info-value">{{$employee->bank_name}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Bank IFSC Code')}}</div>
                                    <p class="info-value">{{$employee->bank_identifier_code}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Branch Location')}}</div>
                                    <p class="info-value">{{$employee->branch_location}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('PAN Number')}}</div>
                                    <p class="info-value">{{$employee->tax_payer_id}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <div class="row align-stretch">
        <div class="col-md-12">
            <div class="premium-card short-height-card">
                <div class="premium-card-header">
                    <h6 class="premium-card-title">
                        <i class="fas fa-file-alt text-danger"></i>
                        {{__('Document Details')}}
                    </h6>
                </div>
                <div class="premium-card-body">
                    <div class="row text-center">
                        @php
                           $employeedoc = $employee->documents()->pluck('document_value','document_id');
                        @endphp
                        @foreach($documents as $key=>$document)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="info-item">
                                    <div class="info-label">{{$document->name }}</div>
                                    @if(!empty($employeedoc[$document->id]))
                                        @php
                                            $filename = $employeedoc[$document->id];
                                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                            $supportedExtensions = ['jpeg', 'png', 'jpg', 'svg', 'pdf', 'doc'];
                                        
                                            $fileUrl = asset('document/'.$filename);
                                        @endphp
                                        
                                        <div class="preview-container position-relative" style="width: fit-content;">
                                            <!-- Remove Button -->
                                            <div class="position-absolute" style="top: -8px; right: -8px; z-index: 10;">
                                                <form action="{{ route('employee.document.remove', ['employee' => $employee->id ?? request()->route('employee'), 'document' => $document->id]) }}" 
                                                    method="POST" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to remove this document?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm rounded-circle p-1" 
                                                            style="width: 24px; height: 24px; line-height: 0.5;"
                                                            title="Remove document">
                                                        <i class="fas fa-times" style="font-size: 10px;"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <a href="{{ $fileUrl }}" target="_blank">
                                                @if(in_array($extension, $supportedExtensions))
                                                    @switch($extension)
                                                        @case('jpeg')
                                                        @case('jpg')
                                                        @case('png')
                                                        @case('svg')
                                                            <img src="{{ $fileUrl }}" 
                                                                alt="Image Preview" 
                                                                class="w-16 h-16 object-cover rounded  hover:shadow-lg transition-shadow cursor-pointer"
                                                                title="Click to view full size: {{ $filename }}">
                                                            @break
                                                        
                                                        @case('pdf')
                                                            <div class="w-16 h-16  rounded overflow-hidden cursor-pointer bg-red-50 d-flex align-items-center justify-content-center" 
                                                                onclick="window.open('{{ $fileUrl }}', '_blank')" 
                                                                title="Click to view PDF">
                                                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                                            </div>
                                                            @break
                                                        
                                                        @case('doc')
                                                        @case('docx')
                                                            <div class="w-16 h-16  rounded bg-blue-50 d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-file-word text-blue-500 text-xl"></i>
                                                            </div>
                                                            @break
                                                        
                                                        @default
                                                            <div class="w-16 h-16  rounded bg-gray-50 d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-file text-gray-500 text-xl"></i>
                                                            </div>
                                                    @endswitch
                                                @else
                                                    <div class="w-16 h-16 border rounded bg-gray-50 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-file text-gray-500 text-xl"></i>
                                                    </div>
                                                @endif
                                                <!-- <span class="ml-2 text-primary">{{-- Str::limit($document->name, 15) --}}</span> -->
                                                <span class="ml-2 text-primary">{{ $document->name }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <p class="info-value text-muted">Not uploaded</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Modal for Description -->
    <div class="modal fade premium-modal" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">
                        <i class="fas fa-info-circle mr-2"></i>
                        Description
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="modal-description"></p>
                </div>
            </div>
        </div>
    </div>
    
<script>
    function togglePin() {
        const hiddenPin = document.getElementById('hidden-pin');
        const actualPin = document.getElementById('actual-pin');
        const eyeIcon = document.getElementById('pin-eye-icon');
        
        if (hiddenPin.style.display === "none") {
            hiddenPin.style.display = "inline";
            actualPin.style.display = "none";
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            hiddenPin.style.display = "none";
            actualPin.style.display = "inline";
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }

    function toggleSalary() {
        const hiddenSalary = document.getElementById('hidden-salary');
        const actualSalary = document.getElementById('actual-salary');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (hiddenSalary.style.display === "none") {
            hiddenSalary.style.display = "inline";
            actualSalary.style.display = "none";
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            hiddenSalary.style.display = "none";
            actualSalary.style.display = "inline";
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }

    // Read more functionality
    $(document).ready(function() {
        $('.read-more-btn').on('click', function() {
            const description = $(this).data('description');
            $('#modal-description').text(description);
            $('#descriptionModal').modal('show');
        });
        
        // Responsive Testing and Validation Utilities
        
        // Add breakpoint indicator for testing
        if (window.location.hash === '#responsive-test') {
            $('body').addClass('responsive-testing');
            $('<div class="responsive-breakpoint-indicator"></div>').appendTo('body');
        }
        
        // Touch target testing
        if (window.location.hash === '#touch-test') {
            $('body').addClass('touch-testing');
            $('.btn-eye, .premium-btn, .preview-container .btn-danger').addClass('touch-target-debug');
        }
        
        // Grid overlay testing
        if (window.location.hash === '#grid-test') {
            $('body').addClass('grid-testing');
            $('<div class="responsive-grid-overlay"></div>').appendTo('body');
        }
        
        // JavaScript functionality testing
        if (window.location.hash === '#js-test') {
            $('body').addClass('js-testing');
            console.log('JavaScript testing mode enabled');
            
            // Test salary toggle
            $('#toggle-salary').on('click', function() {
                console.log('Salary toggle clicked');
            });
            
            // Test pin toggle
            $('#toggle-pin').on('click', function() {
                console.log('Pin toggle clicked');
            });
        }
        
        // Performance testing
        if (window.location.hash === '#perf-test') {
            $('body').addClass('performance-testing');
            $('.premium-card, .leave-table, .page-header-compact').addClass('performance-warning');
            
            // Log performance metrics
            console.log('Performance testing mode enabled');
            console.log('Viewport size:', window.innerWidth + 'x' + window.innerHeight);
            console.log('Device pixel ratio:', window.devicePixelRatio);
        }
        
        // Responsive validation helper functions
        window.ResponsiveTest = {
            getCurrentBreakpoint: function() {
                const width = window.innerWidth;
                if (width <= 479) return 'xs-mobile';
                if (width <= 767) return 'mobile';
                if (width <= 1024) return 'tablet';
                if (width <= 1440) return 'desktop';
                return 'large-desktop';
            },
            
            validateTouchTargets: function() {
                const touchElements = $('.btn-eye, .premium-btn, .preview-container .btn-danger');
                const minSize = 44;
                let violations = [];
                
                touchElements.each(function() {
                    const $el = $(this);
                    const width = $el.outerWidth();
                    const height = $el.outerHeight();
                    
                    if (width < minSize || height < minSize) {
                        violations.push({
                            element: this,
                            width: width,
                            height: height,
                            required: minSize
                        });
                    }
                });
                
                console.log('Touch target validation:', violations.length === 0 ? 'PASSED' : 'FAILED');
                if (violations.length > 0) {
                    console.log('Violations:', violations);
                }
                
                return violations;
            },
            
            validateResponsiveLayout: function() {
                const breakpoint = this.getCurrentBreakpoint();
                const issues = [];
                
                // Check for horizontal overflow
                if (document.body.scrollWidth > window.innerWidth) {
                    issues.push('Horizontal overflow detected');
                }
                
                // Check card layout on mobile
                if (breakpoint === 'mobile' || breakpoint === 'xs-mobile') {
                    const cards = $('.col-md-6');
                    cards.each(function() {
                        const $card = $(this);
                        if ($card.css('flex') !== '0 0 100%' && $card.css('max-width') !== '100%') {
                            issues.push('Card not full width on mobile: ' + this.className);
                        }
                    });
                }
                
                console.log('Layout validation:', issues.length === 0 ? 'PASSED' : 'FAILED');
                if (issues.length > 0) {
                    console.log('Issues:', issues);
                }
                
                return issues;
            },
            
            testAllFunctionality: function() {
                console.log('Testing all responsive functionality...');
                console.log('Current breakpoint:', this.getCurrentBreakpoint());
                
                // Test touch targets
                this.validateTouchTargets();
                
                // Test layout
                this.validateResponsiveLayout();
                
                // Test JavaScript functionality
                if (typeof toggleSalary === 'function') {
                    console.log('Salary toggle function: AVAILABLE');
                } else {
                    console.log('Salary toggle function: MISSING');
                }
                
                if (typeof togglePin === 'function') {
                    console.log('Pin toggle function: AVAILABLE');
                } else {
                    console.log('Pin toggle function: MISSING');
                }
                
                console.log('Responsive testing complete');
            }
        };
        
        // Auto-run tests if in test mode
        if (window.location.hash.includes('test')) {
            setTimeout(function() {
                if (window.ResponsiveTest) {
                    window.ResponsiveTest.testAllFunctionality();
                }
            }, 1000);
        }
        
        // Window resize handler for responsive testing
        $(window).on('resize', function() {
            if ($('body').hasClass('responsive-testing')) {
                console.log('Viewport resized to:', window.innerWidth + 'x' + window.innerHeight);
                console.log('New breakpoint:', window.ResponsiveTest.getCurrentBreakpoint());
            }
        });
    });
</script>
@endsection