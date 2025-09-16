@extends('layouts.admin')

@section('page-title')
@if (Auth::user()->type == 'super admin')
    {{ __('Companies') }}
@else
    {{ __('Users') }}
@endif
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
        --shadow-xl: 0 25px 50px rgba(0, 0, 0, 0.15);
        --text-primary: #2d3748;
        --text-secondary: #6b7280;
        --glass-bg: rgba(255, 255, 255, 0.25);
        --glass-border: rgba(255, 255, 255, 0.18);
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --card-shadow-hover: 0 20px 40px rgba(0,0,0,0.15);
    }

    /* Premium Page Header */
    .page-header-premium {
        background: var(--primary);
        border-radius: 20px;
        padding: 30px 40px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header-premium::before {
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

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .header-left {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .header-text {
        flex: 1;
    }

    .header-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 25px;
        backdrop-filter: blur(10px);
    }

    .header-icon i {
        font-size: 32px;
        color: white;
    }

    .page-title-compact {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 5px;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .page-subtitle-compact {
        font-size: 16px;
        opacity: 0.9;
        color: white;
        font-weight: 400;
        margin: 0;
    }

    .header-stats {
        display: flex;
        align-items: center;
        gap: 30px;
        flex-shrink: 0;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 800;
        color: white;
        margin: 0;
        line-height: 1;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
        margin: 5px 0 0 0;
        font-weight: 500;
    }

    .premium-btn {
        background: rgba(255,255,255,0.2);
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 12px;
        padding: 12px 24px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        backdrop-filter: blur(10px);
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
    }

    .premium-btn:hover {
        background: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    /* Grid Layout */
    .row-equal-height {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 0;
    }

    /* Premium Cards */
    .premium-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(255,255,255,0.8);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        /* height: 420px; */
        display: flex;
        flex-direction: column;
        backdrop-filter: blur(10px);
    }

    .premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary);
        opacity: 0;
        transition: var(--transition);
    }

    .premium-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.03) 0%, transparent 50%);
        pointer-events: none;
        transition: var(--transition);
        opacity: 0;
    }

    .premium-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.2);
    }

    .premium-card:hover::before {
        opacity: 1;
    }

    .premium-card:hover::after {
        opacity: 1;
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .premium-card-body {
        padding: 35px 28px 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        z-index: 2;
    }

    .premium-card-body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.3), transparent);
        border-radius: 2px;
    }

    /* Card Content Sections */
    .card-main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Statistics for Users/Employees Display */
    .user-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 16px;
        width: 100%;
    }

    .user-stats .info-item {
        text-align: center;
        padding: 12px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-radius: 12px;
        border: 1px solid rgba(102, 126, 234, 0.1);
        transition: var(--transition);
    }

    .premium-card:hover .user-stats .info-item {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        border-color: rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }

    /* Actions Dropdown */
    .actions-dropdown {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 10;
    }

    .actions-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.9) 100%);
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .actions-btn:hover {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        color: #667eea;
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2);
    }

    .dropdown-menu {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        padding: 12px;
        min-width: 180px;
        backdrop-filter: blur(20px);
        background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
        border: 1px solid rgba(255,255,255,0.2);
        transform:translate(-44px , 44px) !important;
    }

    .dropdown-item {
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
        position: relative;
        overflow: hidden;
    }

    .dropdown-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: var(--transition);
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        transform: translateX(4px);
        color: #667eea;
    }

    .dropdown-item:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(220, 38, 38, 0.08) 100%);
        color: #dc2626;
    }

    .dropdown-item i {
        width: 16px;
        text-align: center;
        transition: var(--transition);
    }

    .dropdown-item:hover i {
        transform: scale(1.2);
    }

    /* Avatar Section */
    .avatar-wrapper {
        width: 90px;
        height: 90px;
        margin: 0 auto 20px;
        position: relative;
        border-radius: 50%;
        padding: 4px;
        background: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        transition: var(--transition);
    }

    .avatar-wrapper::before {
        content: '';
        position: absolute;
        inset: -2px;
        padding: 2px;
        background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #f5576c, #4facfe, #00f2fe);
        border-radius: 50%;
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: xor;
        -webkit-mask-composite: xor;
        animation: rotate 3s linear infinite;
        opacity: 0;
        transition: var(--transition);
    }

    .premium-card:hover .avatar-wrapper::before {
        opacity: 1;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .avatar-wrapper.offline-avatar {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        box-shadow: 0 8px 25px rgba(156, 163, 175, 0.2);
    }

    .avatar-wrapper.offline-avatar::before {
        background: linear-gradient(45deg, #9ca3af, #6b7280, #d1d5db, #9ca3af);
    }

    .premium-card:hover .avatar-wrapper {
        transform: scale(1.05);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.3);
    }

    .user-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        transition: var(--transition);
    }

    /* Online Status Indicator */
    .avatar-wrapper::after {
        content: '';
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 18px;
        height: 18px;
        background: #10b981;
        border: 3px solid white;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .avatar-wrapper.offline-avatar::after {
        background: #ef4444;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    /* User Information */
    .user-name {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        text-align: center;
        line-height: 1.2;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: var(--transition);
    }

    .premium-card:hover .user-name {
        transform: scale(1.02);
    }

    .user-company {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 6px;
        text-align: center;
        font-weight: 500;
        position: relative;
    }

    .user-company::before {
        content: '🏢';
        margin-right: 6px;
        opacity: 0.7;
    }

    .user-email {
        font-size: 13px;
        color: #9ca3af;
        margin-bottom: 24px;
        text-align: center;
        word-break: break-word;
        position: relative;
        padding: 8px 12px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 20px;
        border: 1px solid rgba(102, 126, 234, 0.1);
        transition: var(--transition);
    }

    .user-email::before {
        content: '✉️';
        margin-right: 6px;
        opacity: 0.7;
    }

    .premium-card:hover .user-email {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-color: rgba(102, 126, 234, 0.2);
    }

    /* Role Badges */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0 auto 25px;
        width: fit-content;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: var(--transition);
    }

    .role-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .premium-card:hover .role-badge::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .role-badge i {
        font-size: 14px;
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
    }

    .role-admin {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .role-employee {
        background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .role-manager {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .role-director {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .role-project-manager {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .role-hr {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
    }

    .role-super-admin {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
    }

    .premium-card:hover .role-badge {
        transform: translateY(-2px) scale(1.05);
    }

    .premium-card:hover .plan-info {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-color: rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 15px;
        align-items: center;
    }

    .info-item {
        text-align: left;
    }

    .info-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-label::before {
        content: '●';
        color: #667eea;
        font-size: 8px;
    }

    .info-value {
        font-size: 15px;
        color: #1f2937;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .upgrade-link {
        background: var(--primary);
        color: white;
        padding: 10px 18px;
        border-radius: 25px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .upgrade-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .upgrade-link:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .upgrade-link:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .plan-expires {
        font-size: 12px;
        color: #6b7280;
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-radius: 20px;
        border: 1px solid rgba(102, 126, 234, 0.1);
        font-weight: 500;
    }

    .plan-expires i {
        color: #667eea;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 40px;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 24px;
    }

    .empty-state h3 {
        font-size: 24px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 12px;
    }

    .empty-state p {
        font-size: 16px;
        color: #6b7280;
        max-width: 400px;
        margin: 0 auto;
    }

    /* Loading States */
    .loading-card {
        position: relative;
        pointer-events: none;
    }

    .loading-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(2px);
        border-radius: var(--border-radius);
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .loading-card::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 32px;
        height: 32px;
        margin: -16px 0 0 -16px;
        border: 3px solid #e5e7eb;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 101;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Fade In Animation */
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Ripple Effect */
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
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

    /* Responsive Design */
    @media (max-width: 1200px) {
        .row-equal-height {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
    }

    @media (max-width: 992px) {
        .header-content {
            flex-direction: column;
            gap: 25px;
            text-align: center;
        }

        .header-left {
            justify-content: center;
        }

        .header-stats {
            justify-content: center;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 15px;
        }

        .page-header-premium {
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 16px;
        }

        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        .header-left {
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            margin-right: 0;
            margin-bottom: 10px;
        }

        .header-icon i {
            font-size: 28px;
        }

        .header-text {
            text-align: center;
        }

        .header-stats {
            flex-direction: row;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .stat-item {
            min-width: 100px;
        }

        .page-title-compact {
            font-size: 26px;
            margin-bottom: 8px;
        }

        .page-subtitle-compact {
            font-size: 14px;
            line-height: 1.4;
        }

        .stat-number {
            font-size: 24px;
        }

        .stat-label {
            font-size: 12px;
        }

        .premium-btn {
            padding: 10px 20px;
            font-size: 13px;
            border-radius: 10px;
        }

        .row-equal-height {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .premium-card {
            height: auto;
            min-height: 380px;
        }

        .premium-card-body {
            padding: 25px 20px 20px;
        }
    }

    @media (max-width: 576px) {
        .page-header-premium {
            padding: 18px;
            margin-bottom: 20px;
        }

        .header-left {
            flex-direction: column;
            gap: 12px;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            margin-bottom: 8px;
        }

        .header-icon i {
            font-size: 24px;
        }

        .page-title-compact {
            font-size: 22px;
            margin-bottom: 6px;
        }

        .page-subtitle-compact {
            font-size: 13px;
            padding: 0 10px;
        }

        .header-stats {
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .stat-item {
            min-width: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stat-number {
            font-size: 22px;
        }

        .stat-label {
            font-size: 11px;
        }

        .premium-btn {
            padding: 8px 16px;
            font-size: 12px;
            min-width: 120px;
            justify-content: center;
        }

        .premium-btn i {
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .page-header-premium {
            padding: 15px;
            border-radius: 12px;
        }

        .page-title-compact {
            font-size: 20px;
        }

        .page-subtitle-compact {
            font-size: 12px;
        }

        .header-icon {
            width: 45px;
            height: 45px;
        }

        .header-icon i {
            font-size: 20px;
        }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Enhanced Focus States */
    button:focus,
    a:focus,
    .dropdown-item:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }

    /* Print Styles */
    @media print {
        .actions-dropdown,
        .premium-btn {
            display: none;
        }

        .premium-card {
            box-shadow: none;
            border: 1px solid #e5e7eb;
            break-inside: avoid;
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
                    @if(Auth::user()->type == 'super admin')
                        <i class="fas fa-building"></i>
                    @else
                        <i class="fas fa-users"></i>
                    @endif
                </div>
                <div class="header-text">
                    @if (Auth::user()->type == 'super admin')
                        <h1 class="page-title-compact">{{ __('Manage Companies') }}</h1>
                        <p class="page-subtitle-compact">{{ __('Manage and monitor company accounts and permissions') }}</p>
                    @else
                        <h1 class="page-title-compact">{{ __('Manage Users') }} </h1>
                        <p class="page-subtitle-compact">{{ __('Manage and monitor user accounts and permissions') }}</p>
                    @endif
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    @if (Auth::user()->type == 'super admin')
                        <p class="stat-number">{{ $users->count() }}</p>
                        <p class="stat-label"><b>{{ __('Total Companies') }}</b></p>
                    @else
                        <p class="stat-number">{{ $users->count() }}</p>
                        <p class="stat-label"><b>{{ __('Total Users') }}</b></p>
                    @endif
                </div>
                @can('Create User')
                <div class="stat-item">
                    <a href="#" data-url="{{ route('user.create') }}" data-ajax-popup="true" data-size="xl"
                       data-title="{{ Auth::user()->type == 'super admin' ? __('Create New Company') : __('Create New User') }}"
                       class="premium-btn">
                        <i class="fa fa-plus"></i> {{ __('Create') }}
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    @if($users->count() > 0)
        <div class="row-equal-height">
            @foreach($users as $user)
            <div class="fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s">
                <div class="premium-card">
                    {{-- Actions Dropdown --}}
                    @if (Gate::check('Edit User') || Gate::check('Delete User'))
                    <div class="actions-dropdown">
                        <div class="dropdown">
                            <button class="actions-btn" type="button" id="dropdownMenuButton{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                @can('Edit User')
                                <li>
                                    <a href="#" data-ajax-popup="true"
                                       data-url="{{ route('user.edit',$user->id) }}"
                                       data-title="{{ Auth::user()->type == 'super admin' ? __('Edit Company') : __('Edit User') }}"
                                       class="dropdown-item">
                                        <i class="fas fa-edit"></i>
                                        {{ __('Edit') }}
                                    </a>
                                </li>
                                @endcan
                                @can('Delete User')
                                <li>
                                    <a href="#" class="dropdown-item text-danger delete-user"
                                       data-user-id="{{ $user->id }}"
                                       data-user-name="{{ $user->name }}"
                                       data-user-type="{{ Auth::user()->type == 'super admin' ? 'company' : 'user' }}">
                                        <i class="fas fa-trash"></i>
                                        {{ __('Delete') }}
                                    </a>
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('user.destroy',$user->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </li>
                                @endcan
                                <li>
                                    <a href="#" data-ajax-popup="true"
                                       data-url="{{ route('user.reset',\Crypt::encrypt($user->id)) }}"
                                       data-title="{{ Auth::user()->type == 'super admin' ? __('Reset Company Password') : __('Reset User Password') }}"
                                       class="dropdown-item">
                                        <i class="fas fa-key"></i>
                                        {{ __('Reset Password') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endif

                    <div class="premium-card-body">
                        <div class="card-main-content">
                            {{-- Avatar --}}
                            <div class="avatar-wrapper {{ $user->last_login && \Carbon\Carbon::parse($user->last_login)->diffInHours(now()) < 24 ? '' : 'offline-avatar' }}">
                                <img src="{{ $user->avatar ? asset(Storage::url('uploads/avatar/'.$user->avatar)) : asset(Storage::url('uploads/avatar/avatar.png')) }}"
                                     alt="avatar"
                                     class="user-avatar">
                            </div>

                            {{-- User Info --}}
                            <h5 class="user-name">{{ $user->name }}</h5>
                            <p class="user-company">{{ $user->company_name ?? 'Company Name' }}</p>
                            <p class="user-email">{{ $user->email }}</p>

                            <div class="role-badge role-{{ str_replace(' ', '-', strtolower($user->type)) }}">
                                @if($user->type == 'admin')
                                    <i class="fas fa-user-shield"></i>
                                @elseif($user->type == 'employee')
                                    <i class="fas fa-user"></i>
                                @elseif($user->type == 'manager')
                                    <i class="fas fa-user-tie"></i>
                                @elseif($user->type == 'director')
                                    <i class="fas fa-user-graduate"></i>
                                @elseif($user->type == 'project manager')
                                    <i class="fas fa-tasks"></i>
                                @elseif($user->type == 'hr')
                                    <i class="fas fa-heart"></i>
                                @else
                                    <i class="fas fa-crown"></i>
                                @endif
                                {{ Auth::user()->type == 'super admin' ? 'Company' : strtoupper(str_replace(' ', ' ', $user->type)) }}
                            </div>
                        </div>

                        @if(Auth::user()->type == 'super admin')
                        <div class="plan-info">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">{{ __('Plan') }}</div>
                                    <div class="info-value">{{ $user->currentPlan->name ?? 'No Plan Selected' }}</div>
                                </div>
                                <div class="info-item">
                                    <a href="#" data-ajax-popup="true"
                                       data-url="{{ route('plan.upgrade',$user->id) }}"
                                       data-title="{{ __('Upgrade Company Plan') }}"
                                       class="upgrade-link">
                                        <i class="fas fa-arrow-up"></i>
                                        {{ __('Upgrade') }}
                                    </a>
                                </div>
                            </div>

                            <div class="plan-expires">
                                <i class="fas fa-clock"></i>
                                @if($user->plan_expire_date == null)
                                {{ __('No Limit') }}
                                @else
                                {{ __('Expires: ') }}{{ Auth::user()->dateFormat($user->plan_expire_date) }}
                                @endif
                            </div>

                            <div class="user-stats">
                                <div class="info-item">
                                    <div class="info-label">{{ __('Users') }}</div>
                                    <div class="info-value">{{ Auth::user()->countUsers() }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">{{ __('Employees') }}</div>
                                    <div class="info-value">{{ Auth::user()->countEmployees() }}</div>
                                </div>
                            </div>
                        </div>
                        @else
                        {{-- For non-super admin users, show basic stats --}}
                        
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="premium-card fade-in">
            <div class="premium-card-body">
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>{{ Auth::user()->type == 'super admin' ? __('No Companies Found') : __('No Users Found') }}</h3>
                    <p>{{ Auth::user()->type == 'super admin' ? __('Start by creating your first company account.') : __('Start by creating your first user account.') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced hover effects for cards
    const cards = document.querySelectorAll('.premium-card');

    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Enhanced delete confirmation with proper styling
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-user')) {
            e.preventDefault();
            const deleteBtn = e.target.closest('.delete-user');
            const userId = deleteBtn.dataset.userId;
            const userName = deleteBtn.dataset.userName;
            const userType = deleteBtn.dataset.userType;

            // Show confirmation dialog with SweetAlert2 or native confirm
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: `Are you sure?`,
                    text: `Do you want to delete this ${userType} "${userName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: `Yes, delete ${userType}!`,
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add loading state to the card
                        const card = deleteBtn.closest('.premium-card');
                        if (card) {
                            card.classList.add('loading-card');
                        }

                        // Submit the form
                        document.getElementById(`delete-form-${userId}`).submit();
                    }
                });
            } else {
                // Fallback to native confirm
                const confirmMessage = `Are you sure you want to delete this ${userType} "${userName}"? This action cannot be undone.`;
                if (confirm(confirmMessage)) {
                    // Add loading state to the card
                    const card = deleteBtn.closest('.premium-card');
                    if (card) {
                        card.classList.add('loading-card');
                    }

                    // Submit the form
                    document.getElementById(`delete-form-${userId}`).submit();
                }
            }
        }
    });

    // Loading state for AJAX actions
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-ajax-popup]')) {
            const card = e.target.closest('.premium-card');
            if (card) {
                card.classList.add('loading-card');
                setTimeout(() => {
                    card.classList.remove('loading-card');
                }, 3000); // Remove loading state after 3 seconds
            }
        }
    });

    // Smooth scroll to top when creating new user
    const createButtons = document.querySelectorAll('[data-ajax-popup]');
    createButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.dataset.title && this.dataset.title.includes('Create')) {
                setTimeout(() => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 100);
            }
        });
    });

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.premium-btn');
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
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Enhanced dropdown animations
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');

        dropdown.addEventListener('show.bs.dropdown', function() {
            if (menu) {
                menu.style.transform = 'translateY(-10px)';
                menu.style.opacity = '0';
                setTimeout(() => {
                    menu.style.transform = 'translateY(0)';
                    menu.style.opacity = '1';
                }, 10);
            }
        });
    });

    // Remove loading state when modal is closed
    document.addEventListener('hidden.bs.modal', function() {
        document.querySelectorAll('.loading-card').forEach(card => {
            card.classList.remove('loading-card');
        });
    });

    // Handle window resize for better responsive behavior
    function handleResize() {
        const windowWidth = window.innerWidth;

        // Adjust card heights based on screen size
        const cards = document.querySelectorAll('.premium-card');
        cards.forEach(card => {
            if (windowWidth <= 576) {
                card.style.minHeight = '380px';
            } else if (windowWidth <= 768) {
                card.style.minHeight = '400px';
            } else if (windowWidth <= 992) {
                card.style.minHeight = '420px';
            } else {
                card.style.minHeight = '420px';
            }
        });
    }

    // Initial call and resize listener
    handleResize();
    window.addEventListener('resize', handleResize);

    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all fade-in elements
    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endpush