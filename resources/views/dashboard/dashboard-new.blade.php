@extends('layouts.admin')
@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('content')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        /* CSS Custom Properties for Responsive Design */
        :root {
            /* Responsive Breakpoints */
            --breakpoint-xs: 0px;
            --breakpoint-sm: 576px;
            --breakpoint-md: 768px;
            --breakpoint-lg: 1024px;
            --breakpoint-xl: 1400px;
            
            /* Responsive Spacing Scale */
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
            --spacing-2xl: 48px;
            
            /* Responsive Font Sizes */
            --font-xs: 0.75rem;
            --font-sm: 0.875rem;
            --font-base: 1rem;
            --font-lg: 1.125rem;
            --font-xl: 1.25rem;
            --font-2xl: 1.5rem;
            --font-3xl: 1.875rem;
            --font-4xl: 2.25rem;
            
            /* Container Widths */
            --container-sm: 540px;
            --container-md: 720px;
            --container-lg: 960px;
            --container-xl: 1140px;
            --container-2xl: 1320px;
            
            /* Grid Gaps */
            --grid-gap-xs: 12px;
            --grid-gap-sm: 16px;
            --grid-gap-md: 20px;
            --grid-gap-lg: 24px;
            --grid-gap-xl: 30px;
            
            /* Border Radius Scale */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-2xl: 24px;
        }

        /* Responsive Container Utilities */
        .container-fluid {
            width: 100%;
            padding-left: var(--spacing-md);
            padding-right: var(--spacing-md);
            margin-left: auto;
            margin-right: auto;
        }

        @media (min-width: 576px) {
            .container-fluid {
                padding-left: var(--spacing-lg);
                padding-right: var(--spacing-lg);
            }
        }

        @media (min-width: 768px) {
            .container-fluid {
                padding-left: var(--spacing-xl);
                padding-right: var(--spacing-xl);
            }
        }

        /* Responsive Utility Classes */
        .d-block { display: block; }
        .d-flex { display: flex; }
        .d-grid { display: grid; }
        .d-none { display: none; }

        @media (max-width: 575.98px) {
            .d-xs-none { display: none !important; }
            .d-xs-block { display: block !important; }
            .d-xs-flex { display: flex !important; }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .d-sm-none { display: none !important; }
            .d-sm-block { display: block !important; }
            .d-sm-flex { display: flex !important; }
        }

        @media (min-width: 768px) and (max-width: 1023.98px) {
            .d-md-none { display: none !important; }
            .d-md-block { display: block !important; }
            .d-md-flex { display: flex !important; }
        }

        /* Responsive Spacing Utilities */
        .p-responsive { padding: var(--spacing-md); }
        .m-responsive { margin: var(--spacing-md); }
        .gap-responsive { gap: var(--grid-gap-md); }

        @media (max-width: 575.98px) {
            .p-responsive { padding: var(--spacing-sm); }
            .m-responsive { margin: var(--spacing-sm); }
            .gap-responsive { gap: var(--grid-gap-xs); }
        }

        @media (min-width: 768px) {
            .p-responsive { padding: var(--spacing-lg); }
            .m-responsive { margin: var(--spacing-lg); }
            .gap-responsive { gap: var(--grid-gap-lg); }
        }

        .modern-dashboard {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: var(--spacing-md);
        }

        @media (max-width: 575.98px) {
            .modern-dashboard {
                padding: var(--spacing-sm);
            }
        }

        .page-header-compact {
            background: linear-gradient(135deg, 
                rgba(37, 99, 235, 0.95) 0%, 
                rgba(59, 130, 246, 0.95) 50%, 
                rgba(96, 165, 250, 0.95) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg) var(--spacing-md);
            margin-bottom: var(--spacing-xl);
            box-shadow: 
                0 32px 64px rgba(37, 99, 235, 0.3),
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (min-width: 576px) {
            .page-header-compact {
                padding: var(--spacing-xl) var(--spacing-lg);
                border-radius: var(--radius-xl);
            }
        }

        @media (min-width: 768px) {
            .page-header-compact {
                padding: var(--spacing-xl) var(--spacing-2xl);
                border-radius: var(--radius-2xl);
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
            align-items: center;
            text-align: center;
            gap: var(--spacing-md);
        }

        @media (min-width: 768px) {
            .page-header-compact .header-content {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                text-align: left;
            }
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            flex-direction: column;
            text-align: center;
        }

        @media (min-width: 576px) {
            .header-left {
                flex-direction: row;
                text-align: left;
            }
        }

        .page-title-compact {
            font-size: var(--font-xl);
            font-weight: 800;
            color: #fff;
            margin: 0;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: -0.025em;
        }

        @media (min-width: 576px) {
            .page-title-compact {
                font-size: var(--font-2xl);
            }
        }

        @media (min-width: 768px) {
            .page-title-compact {
                font-size: var(--font-3xl);
            }
        }

        @media (min-width: 1024px) {
            .page-title-compact {
                font-size: var(--font-4xl);
            }
        }

        .page-subtitle-compact {
            color: rgba(255, 255, 255, 0.9);
            margin: var(--spacing-xs) 0 0 0;
            font-size: var(--font-sm);
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        @media (min-width: 576px) {
            .page-subtitle-compact {
                font-size: var(--font-base);
            }
        }

        .header-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-lg);
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

        @media (min-width: 576px) {
            .header-icon {
                width: 56px;
                height: 56px;
                font-size: var(--font-xl);
                border-radius: var(--radius-lg);
            }
        }

        @media (min-width: 768px) {
            .header-icon {
                width: 64px;
                height: 64px;
                font-size: var(--font-2xl);
            }
        }

        @media (min-width: 1024px) {
            .header-icon {
                width: 72px;
                height: 72px;
                font-size: 1.8rem;
                border-radius: var(--radius-xl);
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

        /* Premium Welcome Header */
        .welcome-header {
            background: #2563eb;
            border-radius: 20px;
            padding: 35px 40px;
            margin-bottom: 35px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-header::before {
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

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .welcome-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 0;
            color: white;
            font-weight: 400;
        }

        .office-select {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: var(--radius-md);
            padding: var(--spacing-sm) var(--spacing-md);
            color: white;
            font-size: var(--font-sm);
            font-weight: 500;
            min-width: 140px;
            width: 100%;
            max-width: 200px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        @media (min-width: 576px) {
            .office-select {
                width: auto;
                min-width: 160px;
                font-size: var(--font-base);
                padding: var(--spacing-md) var(--spacing-lg);
            }
        }

        @media (min-width: 768px) {
            .office-select {
                min-width: 180px;
            }
        }

        .office-select:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
        }

        .office-select option {
            /* background: #2d3748; */
            color: #000;
            padding: 10px;
        }

        /* HRMS Metrics Grid - Enhanced Responsive */
        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: var(--grid-gap-md);
            margin-bottom: var(--spacing-xl);
        }

        @media (min-width: 576px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: var(--grid-gap-lg);
            }
        }

        @media (min-width: 768px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .metrics-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: var(--grid-gap-xl);
            }
        }

        .metric-card-modern {
            background: white;
            border-radius: var(--radius-md);
            padding: var(--spacing-md) var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid;
            position: relative;
            display: flex;
            align-items: center;
            min-height: 70px;
            transition: all 0.3s ease;
        }

        @media (min-width: 576px) {
            .metric-card-modern {
                min-height: 80px;
                padding: var(--spacing-lg) var(--spacing-xl);
            }
        }

        @media (min-width: 768px) {
            .metric-card-modern {
                min-height: 90px;
            }
        }

        @media (min-width: 1024px) {
            .metric-card-modern {
                min-height: 100px;
            }
        }

        .metric-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .metric-card-modern.present-card {
            border-left-color: #4f7cff;
        }

        .metric-card-modern.absent-card {
            border-left-color: #ff6b6b;
        }

        .metric-card-modern.late-card {
            border-left-color: #ffa726;
        }

        .metric-card-modern.leave-card {
            border-left-color: #26c6da;
        }

        .metric-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-base);
            color: white;
            margin-right: var(--spacing-sm);
            flex-shrink: 0;
        }

        @media (min-width: 576px) {
            .metric-icon {
                width: 40px;
                height: 40px;
                font-size: var(--font-lg);
                margin-right: var(--spacing-md);
            }
        }

        @media (min-width: 768px) {
            .metric-icon {
                width: 45px;
                height: 45px;
                font-size: var(--font-xl);
                border-radius: var(--radius-md);
            }
        }

        @media (min-width: 1024px) {
            .metric-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
                margin-right: var(--spacing-lg);
            }
        }

        .present-icon { 
            background: #4f7cff;
        }

        .absent-icon { 
            background: #ff6b6b;
        }

        .late-icon { 
            background: #ffa726;
        }

        .leave-icon { 
            background: #26c6da;
        }

        .metric-details {
            flex: 1;
        }

        .metric-label {
            font-size: var(--font-xs);
            font-weight: 600;
            color: #8b9dc3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: var(--spacing-xs);
        }

        @media (min-width: 576px) {
            .metric-label {
                font-size: 10px;
            }
        }

        @media (min-width: 768px) {
            .metric-label {
                font-size: 11px;
            }
        }

        .metric-number {
            font-size: var(--font-2xl);
            font-weight: 700;
            color: #2d3748;
            margin-bottom: var(--spacing-xs);
            line-height: 1;
        }

        @media (min-width: 576px) {
            .metric-number {
                font-size: 1.75rem;
            }
        }

        @media (min-width: 768px) {
            .metric-number {
                font-size: 2rem;
            }
        }

        @media (min-width: 1024px) {
            .metric-number {
                font-size: 2.25rem;
            }
        }

        .metric-growth {
            font-size: var(--font-xs);
            font-weight: 500;
            color: #10b981;
        }

        @media (min-width: 576px) {
            .metric-growth {
                font-size: var(--font-sm);
            }
        }

        /* Leave Breakdown Styles - Enhanced Responsive */
        .leave-breakdown {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-xs);
            margin-top: var(--spacing-sm);
        }

        @media (min-width: 576px) {
            .leave-breakdown {
                flex-direction: row;
                flex-wrap: wrap;
                gap: var(--spacing-sm);
            }
        }

        @media (min-width: 768px) {
            .leave-breakdown {
                flex-direction: row-reverse;
                gap: var(--spacing-xs);
            }
        }

        .leave-type-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: var(--font-xs);
            font-weight: 600;
            gap: var(--spacing-xs);
            padding: var(--spacing-xs) 0;
        }

        @media (min-width: 576px) {
            .leave-type-item {
                justify-content: flex-start;
                padding: 0;
                flex: 1;
                min-width: 0;
            }
        }

        @media (min-width: 768px) {
            .leave-type-item {
                flex: none;
                font-size: 10px;
            }
        }

        .leave-type-label {
            color: #8b9dc3;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            font-size: inherit;
        }

        .leave-type-count {
            color: #26c6da;
            font-weight: 700;
            background: rgba(38, 198, 218, 0.1);
            padding: 2px var(--spacing-xs);
            border-radius: var(--radius-sm);
            min-width: 18px;
            text-align: center;
            font-size: var(--font-xs);
            flex-shrink: 0;
        }

        @media (min-width: 768px) {
            .leave-type-count {
                min-width: 16px;
                padding: 1px var(--spacing-xs);
                font-size: 9px;
            }
        }

        /* Enhanced Dashboard Content - Responsive */
        .dashboard-content {
            display: grid;
            gap: var(--spacing-lg);
        }

        @media (min-width: 768px) {
            .dashboard-content {
                gap: var(--spacing-xl);
            }
        }

        @media (min-width: 1024px) {
            .dashboard-content {
                gap: 35px;
            }
        }

        .dashboard-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: var(--grid-gap-lg);
        }

        @media (min-width: 768px) {
            .dashboard-row {
                grid-template-columns: 1fr;
                gap: var(--grid-gap-xl);
            }
        }

        @media (min-width: 1024px) {
            .dashboard-row {
                grid-template-columns: 1fr 1fr;
                gap: var(--spacing-xl);
            }
        }

        @media (min-width: 1200px) {
            .dashboard-row {
                grid-template-columns: 1fr 1fr 1fr;
                gap: 30px;
            }
        }

        .dashboard-widget {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            overflow: hidden;
            transition: all 0.3s ease;
            min-height: 300px;
        }

        @media (min-width: 576px) {
            .dashboard-widget {
                min-height: 350px;
            }
        }

        @media (min-width: 768px) {
            .dashboard-widget {
                min-height: 400px;
                border-radius: var(--radius-xl);
            }
        }

        @media (min-width: 1024px) {
            .dashboard-widget {
                min-height: 450px;
            }
        }

        .dashboard-widget:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .widget-header {
            padding: var(--spacing-lg) var(--spacing-lg);
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            flex-wrap: wrap;
            gap: var(--spacing-sm);
        }

        @media (min-width: 576px) {
            .widget-header {
                padding: var(--spacing-xl) var(--spacing-xl);
                flex-wrap: nowrap;
            }
        }

        @media (min-width: 768px) {
            .widget-header {
                padding: 25px 30px;
            }
        }

        .widget-header h2 {
            font-size: var(--font-lg);
            font-weight: 700;
            color: #2d3748;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
        }

        @media (min-width: 576px) {
            .widget-header h2 {
                font-size: var(--font-xl);
            }
        }

        @media (min-width: 768px) {
            .widget-header h2 {
                font-size: 20px;
                white-space: normal;
                overflow: visible;
                text-overflow: unset;
            }
        }

        .small-text {
            font-size: 13px;
            color: #8b9dc3;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .widget-body {
            display: flex;
            flex-direction: column;
            padding: var(--spacing-md);
            flex: 1;
        }

        @media (min-width: 576px) {
            .widget-body {
                padding: var(--spacing-lg);
            }
        }

        @media (min-width: 768px) {
            .widget-body {
                padding: var(--spacing-xl);
            }
        }

        /* Specific height for Employee Attendance Status */
        .employee-attendance-widget .widget-body {
            padding: var(--spacing-md);
        }

        @media (min-width: 576px) {
            .employee-attendance-widget .widget-body {
                padding: var(--spacing-lg);
            }
        }

        .employee-attendance-widget .table-container {
            max-height: 200px;
            flex: 1;
        }

        @media (min-width: 576px) {
            .employee-attendance-widget .table-container {
                max-height: 250px;
            }
        }

        @media (min-width: 768px) {
            .employee-attendance-widget .table-container {
                max-height: 300px;
            }
        }

        @media (min-width: 1024px) {
            .employee-attendance-widget .table-container {
                max-height: 350px;
            }
        }

        /* Enhanced Table Styling - Mobile Optimized */
        .table-container {
            max-height: 250px;
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-lg);
            background: white;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            position: relative;
            /* Enhanced touch scrolling momentum */
            overscroll-behavior: contain;
            scroll-snap-type: y proximity;
        }

        @media (min-width: 576px) {
            .table-container {
                max-height: 300px;
            }
        }

        @media (min-width: 768px) {
            .table-container {
                max-height: 350px;
                overflow-x: visible;
            }
        }

        /* Enhanced scrollbar for touch devices */
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        @media (max-width: 767.98px) {
            .table-container::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: var(--radius-sm);
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
            border-color: #cbd5e0;
        }

        .table-container::-webkit-scrollbar-thumb:active {
            background: #718096;
            border-color: #a0aec0;
        }

        /* Touch-friendly scroll indicators with improved visibility */
        .table-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 20px;
            height: 100%;
            background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
        }

        .table-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(to top, rgba(255,255,255,0.9), transparent);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
        }

        @media (max-width: 767.98px) {
            .table-container.scrollable-horizontal::before {
                opacity: 1;
            }
            
            .table-container.scrollable-vertical::after {
                opacity: 1;
            }
        }

        /* Scroll momentum enhancement for mobile */
        @media (max-width: 767.98px) {
            .table-container {
                scroll-snap-type: none;
                -webkit-overflow-scrolling: touch;
                /* Enhanced momentum scrolling */
                scroll-behavior: auto;
                /* Improve scroll performance */
                will-change: scroll-position;
                transform: translateZ(0);
            }
            
            /* Visual feedback during scroll */
            .table-container.is-scrolling {
                border-color: #3b82f6;
                transition: border-color 0.2s ease;
            }
        }

        /* Scroll momentum visual indicators */
        .table-container::before {
            transition: opacity 0.3s ease, background 0.2s ease;
        }

        .table-container::after {
            transition: opacity 0.3s ease, background 0.2s ease;
        }

        /* Enhanced scroll indicators during active scrolling */
        .table-container.is-scrolling.scrollable-horizontal::before {
            background: linear-gradient(to left, rgba(59, 130, 246, 0.1), transparent);
        }

        .table-container.is-scrolling.scrollable-vertical::after {
            background: linear-gradient(to top, rgba(59, 130, 246, 0.1), transparent);
        }

        .table {
            margin-bottom: 0;
            width: 100%;
            min-width: 300px;
            border-collapse: collapse;
        }

        @media (min-width: 768px) {
            .table {
                min-width: 100%;
            }
        }

        .table thead th {
            background: #2563eb;
            color: white;
            border: none;
            font-weight: 700;
            padding: var(--spacing-md) var(--spacing-xs);
            font-size: var(--font-xs);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
            /* Enhanced sticky behavior */
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
        }

        @media (min-width: 576px) {
            .table thead th {
                padding: var(--spacing-md) var(--spacing-sm);
                font-size: 10px;
            }
        }

        @media (min-width: 768px) {
            .table thead th {
                padding: var(--spacing-lg) var(--spacing-lg);
                font-size: var(--font-sm);
            }
        }

        @media (min-width: 1024px) {
            .table thead th {
                padding: 18px 20px;
                font-size: 14px;
            }
        }

        /* Enhanced sticky header behavior for orientation changes */
        @media screen and (orientation: landscape) and (max-width: 1024px) {
            .table thead th {
                padding: var(--spacing-sm) var(--spacing-xs);
                font-size: var(--font-xs);
            }
        }

        .table tbody td {
            padding: var(--spacing-sm) var(--spacing-xs);
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: var(--font-xs);
            font-weight: 500;
            white-space: nowrap;
            line-height: 1.4;
            color: #4a5568;
            /* Enhanced touch target for mobile */
            min-height: 44px;
        }

        @media (min-width: 576px) {
            .table tbody td {
                padding: var(--spacing-sm) var(--spacing-sm);
                font-size: var(--font-sm);
                min-height: 48px;
            }
        }

        @media (min-width: 768px) {
            .table tbody td {
                padding: var(--spacing-md) var(--spacing-lg);
                font-size: var(--font-base);
                white-space: normal;
                min-height: 52px;
            }
        }

        @media (min-width: 1024px) {
            .table tbody td {
                padding: 15px 20px;
                font-size: 15px;
                min-height: 56px;
            }
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        @media (min-width: 768px) {
            .table tbody tr:hover {
                transform: scale(1.01);
            }
        }

        /* Enhanced touch-friendly row highlighting and feedback */
        @media (max-width: 767.98px) {
            .table tbody tr:active,
            .table tbody tr.touch-active {
                background: #f1f5f9;
                transform: scale(0.98);
            }
            
            .table tbody tr.touch-feedback {
                background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
                transform: scale(1.01);
                transition: all 0.2s ease;
            }
        }

        /* Scrolling state indicators */
        .table-container.is-scrolling {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .table-container.is-scrolling::-webkit-scrollbar-thumb {
            background: #3b82f6;
        }

        /* Enhanced scroll indicators visibility during scrolling */
        .table-container.is-scrolling::before,
        .table-container.is-scrolling::after {
            opacity: 1 !important;
        }

        /* Improved touch target sizing for mobile */
        @media (max-width: 767.98px) {
            .table tbody tr {
                min-height: 44px;
            }
            
            .table tbody td {
                position: relative;
            }
            
            /* Ensure touch targets meet accessibility guidelines */
            .table tbody td .status-badge {
                margin: 2px 0;
            }
        }

        /* Landscape orientation optimizations */
        @media screen and (orientation: landscape) and (max-height: 600px) {
            .table-container {
                max-height: 200px;
            }
            
            .employee-attendance-widget .table-container {
                max-height: 180px;
            }
            
            .table thead th {
                padding: var(--spacing-xs) var(--spacing-xs);
                font-size: 9px;
            }
            
            .table tbody td {
                padding: var(--spacing-xs) var(--spacing-xs);
                font-size: 10px;
                min-height: 36px;
            }
        }

        /* HRMS Status Badges - Mobile Optimized */
        .status-badge {
            padding: 4px var(--spacing-xs);
            font-size: 9px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            text-transform: uppercase;
            letter-spacing: 0.2px;
            display: inline-block;
            min-width: 40px;
            text-align: center;
            white-space: nowrap;
            /* Enhanced touch target */
            min-height: 24px;
            line-height: 1.2;
            transition: all 0.2s ease;
        }

        @media (min-width: 576px) {
            .status-badge {
                padding: var(--spacing-xs) var(--spacing-sm);
                font-size: var(--font-xs);
                min-width: 50px;
                min-height: 28px;
                letter-spacing: 0.3px;
                border-radius: var(--radius-md);
            }
        }

        @media (min-width: 768px) {
            .status-badge {
                padding: var(--spacing-xs) var(--spacing-md);
                font-size: 10px;
                min-width: 60px;
                min-height: 32px;
                letter-spacing: 0.4px;
                border-radius: var(--radius-lg);
            }
        }

        @media (min-width: 1024px) {
            .status-badge {
                padding: 6px 12px;
                font-size: 11px;
                min-width: 70px;
                min-height: 36px;
                letter-spacing: 0.5px;
                border-radius: 15px;
            }
        }

        /* Enhanced mobile touch feedback */
        @media (max-width: 767.98px) {
            .status-badge:active {
                transform: scale(0.95);
            }
        }

        .status-badge.present {
            background: #10b981;
            color: white;
        }

        .status-badge.absent {
            background: #ef4444;
            color: white;
        }

        .status-badge.late {
            background: #f59e0b;
            color: white;
        }

        .status-badge.leave {
            background: #8b5cf6;
            color: white;
        }

        /* Enhanced Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 14px 28px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Enhanced Gauge Styling */
        .gauge-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            height: 100%;
            justify-content: center;
        }

        .speedometer-gauge {
            width: 100%;
            max-width: 340px;
            height: auto;
            margin-bottom: 20px;
        }

        .gauge-background {
            stroke: #e2e8f0;
            stroke-width: 20;
        }

        .gauge-progress {
            stroke: #4f7cff;
            stroke-width: 20;
            transition: stroke-dasharray 1s ease-in-out;
        }

        .gauge-progress.excellent {
            stroke: #10b981;
        }

        .gauge-progress.good {
            stroke: #4f7cff;
        }

        .gauge-progress.warning {
            stroke: #f59e0b;
        }

        .gauge-progress.poor {
            stroke: #ef4444;
        }

        .gauge-needle {
            stroke: #2d3748;
            stroke-width: 4;
            transition: transform 1s ease-in-out;
        }

        .gauge-center {
            fill: #2d3748;
        }

        .gauge-markers line {
            stroke: #94a3b8;
            stroke-width: 2;
        }

        .gauge-text {
            font-size: 14px;
            font-weight: 600;
            fill: #64748b;
            text-anchor: middle;
            font-family: 'Inter', sans-serif;
        }

        .gauge-percentage-text {
            font-size: 32px;
            font-weight: 800;
            fill: #1e293b;
            text-anchor: middle;
            font-family: 'Inter', sans-serif;
        }

        .gauge-percentage-text.excellent {
            fill: #10b981;
        }

        .gauge-percentage-text.good {
            fill: #4f7cff;
        }

        .gauge-percentage-text.warning {
            fill: #f59e0b;
        }

        .gauge-percentage-text.poor {
            fill: #ef4444;
        }

        .gauge-label-text {
            font-size: 14px;
            font-weight: 600;
            fill: #64748b;
            text-anchor: middle;
            font-family: 'Inter', sans-serif;
        }

        .gauge-details {
            font-size: 16px;
            font-weight: 600;
            color: #475569;
            text-align: center;
            margin-top: 10px;
        }

        .gauge-details span {
            font-weight: 700;
            color: #1e293b;
        }

        /* Charts and Analytics */
        .chart-container {
            position: relative;
            height: 350px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chart-container canvas {
            max-width: 100%;
            max-height: 100%;
            border-radius: 12px;
        }

        /* Office-wise Analytics */
        .office-analytics-section {
            margin-top: 40px;
        }

        .section-header {
            margin-bottom: 30px;
        }

        .section-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .office-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }

        .office-grid-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .office-grid-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .office-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .office-card-header h3 {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
        }

        .office-attendance-rate {
            text-align: right;
        }

        .rate-value {
            font-size: 24px;
            font-weight: 800;
            color: #4f7cff;
            display: block;
        }

        .rate-label {
            font-size: 12px;
            color: #8b9dc3;
            text-transform: uppercase;
            font-weight: 600;
        }

        .office-metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .office-metric-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 12px;
            background: #f8fafc;
        }

        .office-metric-item .metric-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-right: 12px;
        }

        .office-metric-item.present .metric-icon {
            background: #4f7cff;
        }

        .office-metric-item.absent .metric-icon {
            background: #ff6b6b;
        }

        .office-metric-item.late .metric-icon {
            background: #ffa726;
        }

        .office-metric-item.leave .metric-icon {
            background: #26c6da;
        }

        .metric-info {
            display: flex;
            flex-direction: column;
        }

        .metric-value {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
        }

        .metric-label {
            font-size: 11px;
            color: #8b9dc3;
            text-transform: uppercase;
            font-weight: 600;
        }

        .office-card-footer {
            text-align: center;
        }

        .office-view-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .office-view-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }

        /* Enhanced Responsive Design */
        @media (max-width: 1400px) {
            .dashboard-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 1024px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-row {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .office-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .modern-dashboard {
                padding: 15px;
            }
            
            .welcome-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
                padding: 25px;
            }
            
            .welcome-title {
                font-size: 24px;
            }
            
            .metrics-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .metric-card-modern {
                min-height: 70px; /* Even more compact on mobile */
                padding: 14px 16px;
            }
            
            .metric-number {
                font-size: 24px;
            }

            .metric-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
                margin-right: 10px;
            }
            
            .leave-breakdown {
                gap: 6px;
            }
            
            .leave-type-item {
                font-size: 9px;
            }

            .office-metrics-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .leave-breakdown {
                flex-direction: row-reverse;
                gap: 2px;
                margin-top: 2px;
            }
            
            .leave-type-item {
                justify-content: space-between;
                width: 100%;
            }
        }

        /* Loading States */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer-loading 1.5s infinite;
        }

        @keyframes shimmer-loading {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes pulse-glow {
            0% {
                filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.3));
            }
            100% {
                filter: drop-shadow(0 0 16px rgba(16, 185, 129, 0.6));
            }
        }

        /* Enhanced Focus States */
        .office-select:focus,
        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }

        /* Custom Scrollbar for Webkit browsers */
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
    
    <div class="modern-dashboard">
        <div class="page-header-compact">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="header-text">
                        <h1 class="page-title-compact">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}</h1>
                        <p class="page-subtitle-compact">{{ __('Monitor your HRMS platform performance and growth metrics') }}</p>
                    </div>
                </div>
                <div class="header-right">
                    <select class="office-select" id="office-filter" name="office">
                        <option value="all" selected>All Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- HRMS Metrics Cards - Dynamic Data -->
        <div class="metrics-grid">
            <div class="metric-card-modern present-card">
                <div class="metric-icon present-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="metric-details">
                    <div class="metric-label">PRESENT</div>
                    <div class="metric-number" id="present-count">{{ $officeData['present'] ?? 0 }}</div>
                    <div class="metric-growth">{{ $officeData['present_percent'] ?? 0 }}%</div>
                </div>
            </div>

            <div class="metric-card-modern absent-card">
                <div class="metric-icon absent-icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="metric-details">
                    <div class="metric-label">ABSENT</div>
                    <div class="metric-number" id="absent-count">{{ $officeData['absent'] ?? 0 }}</div>
                    <div class="metric-growth">{{ $officeData['absent_percent'] ?? 0 }}%</div>
                </div>
            </div>

            <div class="metric-card-modern late-card">
                <div class="metric-icon late-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="metric-details">
                    <div class="metric-label">LATE</div>
                    <div class="metric-number" id="late-count">{{ $officeData['late'] ?? 0 }}</div>
                    <div class="metric-growth">{{ $officeData['late_percent'] ?? 0 }}%</div>
                </div>
            </div>

            <div class="metric-card-modern leave-card">
                <div class="metric-icon leave-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="metric-details">
                    <div class="metric-label">ON LEAVE</div>
                    <div class="metric-number" id="leave-count">{{ $officeData['on_leave'] ?? 0 }}</div>
                    <div class="metric-growth">{{ $officeData['leave_percent'] ?? 0 }}%</div>
                    
                    <!-- Leave Type Breakdown -->
                    <div class="leave-breakdown mt-2">
                        <div class="leave-type-item">
                            <span class="leave-type-label">Full:</span>
                            <span class="leave-type-count" id="full-leave-count">{{ $officeData['full_leave'] ?? 0 }}</span>
                        </div>
                        <div class="leave-type-item">
                            <span class="leave-type-label">Half:</span>
                            <span class="leave-type-count" id="half-leave-count">{{ $officeData['half_leave'] ?? 0 }}</span>
                        </div>
                        <div class="leave-type-item">
                            <span class="leave-type-label">Short:</span>
                            <span class="leave-type-count" id="short-leave-count">{{ $officeData['short_leave'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Main Dashboard Content -->
        <div class="dashboard-content">
            <!-- First Row -->
            <div class="dashboard-row">
                <div class="dashboard-widget employee-attendance-widget">
                    <div class="widget-header">
                        <h2>Employee Attendance Status</h2>
                    </div>
                    <div class="widget-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="employee-table-body">
                                    @foreach($notClockIns as $employee)
                                        <tr>
                                            <td>{{ $employee->name }}</td>
                                            <td>
                                                @php
                                                    $statusClass = 'present';
                                                    $statusText = 'Present';
                                                    
                                                    if (isset($employee['status'])) {
                                                        switch($employee['status']) {
                                                            case 'Absent':
                                                                $statusClass = 'absent';
                                                                $statusText = 'Absent';
                                                                break;
                                                            case 'Half-Day Leave':
                                                            case 'Full Day Leave':
                                                            case 'Short Leave':
                                                                $statusClass = 'leave';
                                                                $statusText = $employee['status'];
                                                                break;
                                                            case 'Present':
                                                            default:
                                                                $statusClass = 'present';
                                                                $statusText = 'Present';
                                                                break;
                                                        }
                                                    }
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a class="btn btn-primary" href="{{ route('attendanceemployee.index') }}">
                            View All Attendance
                        </a>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>Overall Attendance</h2>
                    </div>
                    <div class="widget-body">
                        <div class="gauge-wrapper">
                            <div class="speedometer-gauge" id="attendance-speedometer">
                                <svg viewBox="0 0 320 230" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Background arc (full semicircle) -->
                                    <path class="gauge-background" d="M 50 170 A 110 110 0 0 1 270 170" stroke="#f1f5f9"
                                        stroke-width="25" fill="none" stroke-linecap="round" />

                                    <!-- Progress arc (dynamic based on percentage) -->
                                    <path class="gauge-progress" id="gauge-progress-arc"
                                        d="M 50 170 A 110 110 0 0 1 270 170" stroke="#3b82f6" stroke-width="25"
                                        fill="none" stroke-linecap="round" stroke-dasharray="0 346"
                                        stroke-dashoffset="0" />

                                    <!-- Needle -->
                                    <line class="gauge-needle" id="gauge-needle" x1="160" y1="170"
                                        x2="160" y2="80" stroke="#374151" stroke-width="3"
                                        stroke-linecap="round" transform="rotate(-90 160 170)" />

                                    <!-- Center circle -->
                                    <circle class="gauge-center" cx="160" cy="170" r="8" fill="#374151" />

                                    <!-- Scale markers and labels -->
                                    <g class="gauge-markers">
                                        <!-- 0% marker -->
                                        <line stroke="#9ca3af" stroke-width="2" x2="40" x1="60"
                                            y2="170" y1="170"></line>
                                        <text class="gauge-text" x="20" y="175">0%</text>

                                        <!-- 25% marker -->
                                        <line stroke="#9ca3af" stroke-width="2" y1="92" x2="85"
                                            y2="104" x1="68"></line>
                                        <text class="gauge-text" y="90" x="50">25%</text>

                                        <!-- 50% marker -->
                                        <line x1="160" y2="70" stroke="#9ca3af" stroke-width="2"
                                            x2="160" y1="50"></line>
                                        <text class="gauge-text" y="40" x="160">50%</text>

                                        <!-- 75% marker -->
                                        <line stroke="#9ca3af" stroke-width="2" y2="100" x2="230"
                                            y1="87" x1="248"></line>
                                        <text class="gauge-text" y="80" x="265">75%</text>

                                        <!-- 100% marker -->
                                        <line stroke="#9ca3af" stroke-width="2" y2="170" y1="170"
                                            x1="260" x2="280"></line>
                                        <text class="gauge-text" x="300" y="175">100%</text>
                                    </g>

                                    <!-- Percentage display inside gauge -->
                                    <text x="160" y="205" class="gauge-percentage-text" id="gauge-percentage-display">
                                        {{ $officeData['attendance_rate'] ?? 0 }}%
                                    </text>

                                    <!-- Label below percentage -->
                                    <text x="160" y="225" class="gauge-label-text">
                                        Attendance Rate
                                    </text>
                                </svg>
                            </div>
                            <div class="gauge-details">
                                <span id="present-employees">{{ $officeData['present'] ?? 0 }}</span> /
                                <span id="total-expected">{{ $officeData['total'] ?? 0 }}</span> Present
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>Attendance Breakdown</h2>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container" id="attendance-breakdown-chart-container">
                            <canvas id="attendance-breakdown-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="office-grid">
                <div class="office-grid-card">
                    <div class="widget-header">
                        <h2>Weekly Attendance Trend</h2>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container">
                            <canvas id="weekly-trend-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="office-grid-card">
                    <div class="widget-header">
                        <h2>Employee Status Comparison</h2>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container">
                            <canvas id="employee-status-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Office-wise Analytics Grid Section -->
        <div class="office-analytics-section">
            <div class="section-header">
                <h2>Office-wise Analytics</h2>
            </div>

            <div class="office-grid" id="office-cards">
                @foreach($officesData as $officeId => $office)
                    <div class="office-grid-card" data-office-id="{{ $officeId }}">
                        <div class="office-card-header">
                            <h3>{{ $office['name'] }}</h3>
                            <div class="office-attendance-rate">
                                <span class="rate-value">{{ $office['attendance_rate'] }}%</span>
                                <span class="rate-label">Attendance</span>
                            </div>
                        </div>
                        <hr style="border-top: 1px solid #1e1e1f;">
                        <div class="office-metrics-grid">
                            <div class="office-metric-item present">
                                <div class="metric-icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="metric-info">
                                    <span class="metric-value">{{ $office['present'] }}</span>
                                    <span class="metric-label">Present</span>
                                </div>
                            </div>

                            <div class="office-metric-item absent">
                                <div class="metric-icon">
                                    <i class="fas fa-user-times"></i>
                                </div>
                                <div class="metric-info">
                                    <span class="metric-value">{{ $office['absent'] }}</span>
                                    <span class="metric-label">Absent</span>
                                </div>
                            </div>

                            <div class="office-metric-item late">
                                <div class="metric-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="metric-info">
                                    <span class="metric-value">{{ $office['late'] }}</span>
                                    <span class="metric-label">Late</span>
                                </div>
                            </div>

                            <div class="office-metric-item leave">
                                <div class="metric-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="metric-info">
                                    <span class="metric-value">{{ $office['on_leave'] }}</span>
                                    <span class="metric-label">On Leave</span>
                                </div>
                            </div>
                        </div>

                        <div class="office-card-footer">
                            <a href="{{ route('office.one.index', $office['id']) }}" class="office-view-btn">
                                View Details
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Data Container for JavaScript -->
    <div id="dashboard-data" style="display: none;" 
         data-overall-percentage="{{ $officeData['attendance_rate'] ?? 0 }}"
         data-present-count="{{ $officeData['present'] ?? 0 }}" 
         data-absent-count="{{ $officeData['absent'] ?? 0 }}"
         data-late-count="{{ $officeData['late'] ?? 0 }}" 
         data-leave-count="{{ $officeData['on_leave'] ?? 0 }}"
         data-full-leave-count="{{ $officeData['full_leave'] ?? 0 }}"
         data-half-leave-count="{{ $officeData['half_leave'] ?? 0 }}"
         data-short-leave-count="{{ $officeData['short_leave'] ?? 0 }}"
         data-total-employees="{{ $officeData['total'] ?? 0 }}"
         data-offices='@json($officesData)'
         data-weekly-trend='@json($weeklyTrendData)'
         data-employees='@json($notClockIns)'>
    </div>

    <script>
        // Get dynamic data from controller
        const dashboardDataElement = document.getElementById('dashboard-data');
        const dynamicData = {
            offices: JSON.parse(dashboardDataElement.dataset.offices || '{}'),
            weeklyTrend: JSON.parse(dashboardDataElement.dataset.weeklyTrend || '[]'),
            employees: JSON.parse(dashboardDataElement.dataset.employees || '[]'),
            overallPercentage: parseInt(dashboardDataElement.dataset.overallPercentage || '0'),
            presentCount: parseInt(dashboardDataElement.dataset.presentCount || '0'),
            absentCount: parseInt(dashboardDataElement.dataset.absentCount || '0'),
            lateCount: parseInt(dashboardDataElement.dataset.lateCount || '0'),
            leaveCount: parseInt(dashboardDataElement.dataset.leaveCount || '0'),
            totalEmployees: parseInt(dashboardDataElement.dataset.totalEmployees || '0')
        };

        // Fallback data if no weekly trend data is available
        if (dynamicData.weeklyTrend.length === 0) {
            dynamicData.weeklyTrend = [
                { day: 'Mon', present: 0, absent: 0, late: 0, leave: 0 },
                { day: 'Tue', present: 0, absent: 0, late: 0, leave: 0 },
                { day: 'Wed', present: 0, absent: 0, late: 0, leave: 0 },
                { day: 'Thu', present: 0, absent: 0, late: 0, leave: 0 },
                { day: 'Fri', present: 0, absent: 0, late: 0, leave: 0 }
            ];
        }

        // Chart instances
        let dashboardCharts = {
            weeklyTrend: null,
            attendanceBreakdown: null,
            employeeStatus: null
        };

        // Initialize Speedometer Gauge Animation
        function initSpeedometerGauge(attendanceRate = 78) {
            const progressArc = document.getElementById('gauge-progress-arc');
            const needle = document.getElementById('gauge-needle');
            const percentageDisplay = document.getElementById('gauge-percentage-display');

            if (progressArc && needle && percentageDisplay) {
                // Reset initial states
                progressArc.style.strokeDasharray = '0 346';
                needle.style.transform = 'rotate(-90deg)';
                needle.style.transformOrigin = '160px 170px';

                // Calculate the circumference of the semicircle
                const radius = 110;
                const circumference = Math.PI * radius;
                const progressLength = (attendanceRate / 100) * circumference;
                const remainingLength = circumference - progressLength;

                // Determine color class
                let colorClass = 'good';
                if (attendanceRate >= 90) colorClass = 'excellent';
                else if (attendanceRate >= 75) colorClass = 'good';
                else if (attendanceRate >= 60) colorClass = 'warning';
                else colorClass = 'poor';

                // Apply color classes
                progressArc.className = `gauge-progress ${colorClass}`;
                percentageDisplay.className = `gauge-percentage-text ${colorClass}`;

                // Calculate needle rotation
                const needleRotation = -90 + (attendanceRate / 100) * 180;

                // Animate with smooth easing
                const animationDuration = 2500;
                const startTime = performance.now();

                function animateStep(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / animationDuration, 1);
                    const easedProgress = 1 - Math.pow(1 - progress, 3);

                    const currentProgress = progressLength * easedProgress;
                    const currentNeedleRotation = -90 + ((needleRotation - (-90)) * easedProgress);
                    const currentRemaining = circumference - currentProgress;

                    progressArc.style.strokeDasharray = `${currentProgress} ${currentRemaining}`;
                    needle.style.transform = `rotate(${currentNeedleRotation}deg)`;

                    if (progress < 1) {
                        requestAnimationFrame(animateStep);
                    } else if (attendanceRate >= 90) {
                        setTimeout(() => {
                            progressArc.style.animation = 'pulse-glow 2s ease-in-out infinite alternate';
                        }, 500);
                    }
                }

                // Update percentage display
                percentageDisplay.textContent = attendanceRate + '%';
                requestAnimationFrame(animateStep);
            }
        }

        // Initialize Weekly Trend Chart
        function initWeeklyTrendChart() {
            const canvas = document.getElementById('weekly-trend-chart');
            if (!canvas) return;

            if (dashboardCharts.weeklyTrend) {
                dashboardCharts.weeklyTrend.destroy();
            }

            const ctx = canvas.getContext('2d');
            const labels = dynamicData.weeklyTrend.map(item => item.day);
            const presentData = dynamicData.weeklyTrend.map(item => item.present);
            const absentData = dynamicData.weeklyTrend.map(item => item.absent);
            const lateData = dynamicData.weeklyTrend.map(item => item.late);
            const leaveData = dynamicData.weeklyTrend.map(item => item.leave);

            dashboardCharts.weeklyTrend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Present',
                            data: presentData,
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)'
                        },
                        {
                            label: 'Absent',
                            data: absentData,
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)'
                        },
                        {
                            label: 'Late',
                            data: lateData,
                            backgroundColor: 'rgba(255, 206, 86, 0.1)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(255, 206, 86, 1)'
                        },
                        {
                            label: 'On Leave',
                            data: leaveData,
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
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
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y} employees`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        y: {
                            display: true,
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize Attendance Breakdown Chart
        function initAttendanceBreakdownChart() {
            const canvas = document.getElementById('attendance-breakdown-chart');
            if (!canvas) return;

            if (dashboardCharts.attendanceBreakdown) {
                dashboardCharts.attendanceBreakdown.destroy();
            }

            const ctx = canvas.getContext('2d');

            dashboardCharts.attendanceBreakdown = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Late', 'On Leave'],
                    datasets: [{
                        data: [dynamicData.presentCount, dynamicData.absentCount, dynamicData.lateCount, dynamicData.leaveCount],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 2,
                        hoverBorderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
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
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                    const value = context.dataset.data[context.dataIndex];
                                    const percentage = Math.round((value / total) * 100);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize Employee Status Chart
        function initEmployeeStatusChart() {
            const canvas = document.getElementById('employee-status-chart');
            if (!canvas) return;

            if (dashboardCharts.employeeStatus) {
                dashboardCharts.employeeStatus.destroy();
            }

            const ctx = canvas.getContext('2d');
            const onTime = dynamicData.presentCount - dynamicData.lateCount; // Present minus late

            dashboardCharts.employeeStatus = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['On Time', 'Late', 'Absent', 'On Leave'],
                    datasets: [{
                        label: 'Number of Employees',
                        data: [onTime, dynamicData.lateCount, dynamicData.absentCount, dynamicData.leaveCount],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.parsed.x} employees`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update metrics for office filter with AJAX call
        function updateMetricsForOffice(officeId) {
            // Show loading state
            showLoadingState();
            
            // Make AJAX call to get filtered data
            fetch('/dash-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    office: officeId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardMetrics(data.data);
                } else {
                    console.error('Error fetching filtered data:', data.error);
                    // Fallback to static data
                    updateMetricsFromStaticData(officeId);
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                // Fallback to static data
                updateMetricsFromStaticData(officeId);
            })
            .finally(() => {
                hideLoadingState();
            });
        }

        // Update dashboard metrics with new data
        function updateDashboardMetrics(data) {
            // Update main metric cards
            document.getElementById('present-count').textContent = data.present;
            document.getElementById('absent-count').textContent = data.absent;
            document.getElementById('late-count').textContent = data.late;
            document.getElementById('leave-count').textContent = data.on_leave;

            // Update leave breakdown
            document.getElementById('full-leave-count').textContent = data.full_leave;
            document.getElementById('half-leave-count').textContent = data.half_leave;
            document.getElementById('short-leave-count').textContent = data.short_leave;

            // Update percentages
            const presentPercent = document.querySelector('.present-card .metric-growth');
            const absentPercent = document.querySelector('.absent-card .metric-growth');
            const latePercent = document.querySelector('.late-card .metric-growth');
            const leavePercent = document.querySelector('.leave-card .metric-growth');

            if (presentPercent) presentPercent.textContent = data.present_percent + '%';
            if (absentPercent) absentPercent.textContent = data.absent_percent + '%';
            if (latePercent) latePercent.textContent = data.late_percent + '%';
            if (leavePercent) leavePercent.textContent = data.leave_percent + '%';

            // Update charts if they exist
            const attendanceRate = data.total > 0 ? Math.round((data.present / data.total) * 100) : 0;
            initSpeedometerGauge(attendanceRate);
            updateAttendanceBreakdownChart(data);
            updateEmployeeStatusChart(data);
        }

        // Fallback to static data if AJAX fails
        function updateMetricsFromStaticData(officeId) {
            if (officeId === 'all') {
                // Show totals for all offices
                const totals = Object.values(dynamicData.offices).reduce((acc, office) => {
                    acc.present += office.present;
                    acc.absent += office.absent;
                    acc.late += office.late;
                    acc.leave += office.on_leave;
                    acc.full_leave += office.full_leave || 0;
                    acc.half_leave += office.half_leave || 0;
                    acc.short_leave += office.short_leave || 0;
                    acc.total += office.total;
                    return acc;
                }, { present: 0, absent: 0, late: 0, leave: 0, full_leave: 0, half_leave: 0, short_leave: 0, total: 0 });

                updateDashboardMetrics(totals);
            } else {
                // Show data for specific office
                const office = dynamicData.offices[officeId];
                if (office) {
                    updateDashboardMetrics({
                        present: office.present,
                        absent: office.absent,
                        late: office.late,
                        on_leave: office.on_leave,
                        full_leave: office.full_leave || 0,
                        half_leave: office.half_leave || 0,
                        short_leave: office.short_leave || 0,
                        total: office.total
                    });
                }
            }
        }

        // Show loading state
        function showLoadingState() {
            const metricCards = document.querySelectorAll('.metric-card-modern');
            metricCards.forEach(card => {
                card.style.opacity = '0.6';
                card.style.pointerEvents = 'none';
            });
        }

        // Hide loading state
        function hideLoadingState() {
            const metricCards = document.querySelectorAll('.metric-card-modern');
            metricCards.forEach(card => {
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            });
        }

        // Update charts with new data
        function updateAttendanceBreakdownChart(data) {
            if (dashboardCharts.attendanceBreakdown) {
                dashboardCharts.attendanceBreakdown.data.datasets[0].data = [
                    data.present, data.absent, data.late, data.leave
                ];
                dashboardCharts.attendanceBreakdown.update();
            }
        }

        function updateEmployeeStatusChart(data) {
            if (dashboardCharts.employeeStatus) {
                const onTime = data.present - data.late;
                dashboardCharts.employeeStatus.data.datasets[0].data = [
                    onTime, data.late, data.absent, data.leave
                ];
                dashboardCharts.employeeStatus.update();
            }
        }

        // Office filter functionality
        document.getElementById('office-filter').addEventListener('change', function() {
            const selectedOffice = this.value;
            updateMetricsForOffice(selectedOffice);

            // Visual feedback
            this.style.transform = 'scale(1.05)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all charts with dynamic data
            initSpeedometerGauge(dynamicData.overallPercentage);
            initWeeklyTrendChart();
            initAttendanceBreakdownChart();
            initEmployeeStatusChart();

            // Add staggered animation to cards
            const cards = document.querySelectorAll('.metric-card-modern, .dashboard-widget, .office-grid-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });

            console.log('HRMS Dashboard initialized successfully!');
        });

        // Add smooth hover effects
        document.querySelectorAll('.metric-card-modern, .dashboard-widget, .office-grid-card').forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            element.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Prevent chart flickering on window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (dashboardCharts.weeklyTrend) dashboardCharts.weeklyTrend.resize();
                if (dashboardCharts.attendanceBreakdown) dashboardCharts.attendanceBreakdown.resize();
                if (dashboardCharts.employeeStatus) dashboardCharts.employeeStatus.resize();
            }, 100);
        });

        // Enhanced Table Touch and Scroll Experience
        function initializeTableTouchExperience() {
            const tableContainers = document.querySelectorAll('.table-container');
            
            tableContainers.forEach(container => {
                let isScrolling = false;
                let scrollTimeout;
                
                // Add scroll indicators based on content overflow
                function updateScrollIndicators() {
                    const hasHorizontalScroll = container.scrollWidth > container.clientWidth;
                    const hasVerticalScroll = container.scrollHeight > container.clientHeight;
                    
                    if (hasHorizontalScroll) {
                        container.classList.add('scrollable-horizontal');
                    } else {
                        container.classList.remove('scrollable-horizontal');
                    }
                    
                    if (hasVerticalScroll) {
                        container.classList.add('scrollable-vertical');
                    } else {
                        container.classList.remove('scrollable-vertical');
                    }
                }
                
                // Enhanced momentum scrolling for touch devices
                let touchStartY = 0;
                let touchStartX = 0;
                let velocityY = 0;
                let velocityX = 0;
                let lastTouchY = 0;
                let lastTouchX = 0;
                let lastTouchTime = 0;
                
                container.addEventListener('touchstart', function(e) {
                    touchStartY = e.touches[0].clientY;
                    touchStartX = e.touches[0].clientX;
                    lastTouchY = touchStartY;
                    lastTouchX = touchStartX;
                    lastTouchTime = Date.now();
                    velocityY = 0;
                    velocityX = 0;
                    
                    // Stop any ongoing momentum
                    container.style.scrollBehavior = 'auto';
                }, { passive: true });
                
                container.addEventListener('touchmove', function(e) {
                    const currentTime = Date.now();
                    const currentY = e.touches[0].clientY;
                    const currentX = e.touches[0].clientX;
                    const deltaTime = currentTime - lastTouchTime;
                    
                    if (deltaTime > 0) {
                        velocityY = (currentY - lastTouchY) / deltaTime;
                        velocityX = (currentX - lastTouchX) / deltaTime;
                    }
                    
                    lastTouchY = currentY;
                    lastTouchX = currentX;
                    lastTouchTime = currentTime;
                }, { passive: true });
                
                container.addEventListener('touchend', function(e) {
                    // Apply momentum scrolling
                    const momentumY = velocityY * 100;
                    const momentumX = velocityX * 100;
                    
                    if (Math.abs(momentumY) > 10 || Math.abs(momentumX) > 10) {
                        container.style.scrollBehavior = 'smooth';
                        
                        // Apply momentum with easing
                        const targetScrollTop = Math.max(0, Math.min(
                            container.scrollHeight - container.clientHeight,
                            container.scrollTop - momentumY
                        ));
                        
                        const targetScrollLeft = Math.max(0, Math.min(
                            container.scrollWidth - container.clientWidth,
                            container.scrollLeft - momentumX
                        ));
                        
                        container.scrollTo({
                            top: targetScrollTop,
                            left: targetScrollLeft,
                            behavior: 'smooth'
                        });
                    }
                }, { passive: true });
                
                // Scroll event handling
                container.addEventListener('scroll', function() {
                    isScrolling = true;
                    
                    // Clear existing timeout
                    clearTimeout(scrollTimeout);
                    
                    // Add scrolling class for visual feedback
                    container.classList.add('is-scrolling');
                    
                    // Remove scrolling class after scroll ends
                    scrollTimeout = setTimeout(() => {
                        isScrolling = false;
                        container.classList.remove('is-scrolling');
                    }, 150);
                    
                    updateScrollIndicators();
                }, { passive: true });
                
                // Initialize scroll indicators
                updateScrollIndicators();
                
                // Update on window resize
                window.addEventListener('resize', updateScrollIndicators);
                
                // Enhanced table row touch feedback
                const tableRows = container.querySelectorAll('tbody tr');
                tableRows.forEach(row => {
                    let touchStartTime = 0;
                    
                    row.addEventListener('touchstart', function() {
                        touchStartTime = Date.now();
                        this.classList.add('touch-active');
                    }, { passive: true });
                    
                    row.addEventListener('touchend', function() {
                        const touchDuration = Date.now() - touchStartTime;
                        
                        // Only apply touch feedback for quick taps
                        if (touchDuration < 200) {
                            this.classList.add('touch-feedback');
                            setTimeout(() => {
                                this.classList.remove('touch-feedback');
                            }, 200);
                        }
                        
                        this.classList.remove('touch-active');
                    }, { passive: true });
                    
                    row.addEventListener('touchcancel', function() {
                        this.classList.remove('touch-active');
                    }, { passive: true });
                });
            });
        }
        
        // Orientation change handling for better table layout
        function handleOrientationChange() {
            const tableContainers = document.querySelectorAll('.table-container');
            
            tableContainers.forEach(container => {
                // Force reflow to handle orientation changes
                container.style.display = 'none';
                container.offsetHeight; // Trigger reflow
                container.style.display = '';
                
                // Update scroll indicators after orientation change
                setTimeout(() => {
                    const hasHorizontalScroll = container.scrollWidth > container.clientWidth;
                    const hasVerticalScroll = container.scrollHeight > container.clientHeight;
                    
                    container.classList.toggle('scrollable-horizontal', hasHorizontalScroll);
                    container.classList.toggle('scrollable-vertical', hasVerticalScroll);
                }, 100);
            });
        }
        
        // Initialize table touch experience when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeTableTouchExperience();
        });
        
        // Handle orientation changes
        window.addEventListener('orientationchange', function() {
            setTimeout(handleOrientationChange, 100);
        });
        
        // Handle window resize for responsive table behavior
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleOrientationChange, 100);
        });
    </script>
@endsection
