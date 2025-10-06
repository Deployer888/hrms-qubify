@extends('layouts.admin')
@section('page-title')
    {{ __('Plans') }}
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

    /* Plans Grid */
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 0;
    }

    /* Premium Plan Cards */
    .premium-plan-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(255,255,255,0.8);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        min-height: 480px;
        display: flex;
        flex-direction: column;
        backdrop-filter: blur(10px);
    }

    .premium-plan-card::before {
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

    .premium-plan-card::after {
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

    .premium-plan-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.2);
    }

    .premium-plan-card:hover::before {
        opacity: 1;
    }

    .premium-plan-card:hover::after {
        opacity: 1;
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .premium-plan-card.recommended {
        border-color: var(--warning);
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2), var(--card-shadow);
    }

    .premium-plan-card.recommended::before {
        background: var(--warning);
        opacity: 1;
    }

    .premium-plan-card.active {
        border-color: var(--success);
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2), var(--card-shadow);
    }

    .premium-plan-card.active::before {
        background: var(--success);
        opacity: 1;
    }

    .premium-plan-card.blurred {
        opacity: 0.6;
        filter: blur(1px);
        transform: scale(0.98);
    }

    /* Plan Status Badges */
    .plan-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 10;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: var(--transition);
    }

    .plan-badge.recommended {
        background: linear-gradient(135deg, var(--warning), #fbbf24);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .plan-badge.active {
        background: linear-gradient(135deg, var(--success), #34d399);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    /* Actions Dropdown */
    .plan-actions-dropdown {
        position: absolute;
        top: 20px;
        left: 20px;
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
        transform: translate(-44px, 44px) !important;
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

    .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        transform: translateX(4px);
        color: #667eea;
    }

    .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(220, 38, 38, 0.08) 100%);
        color: #dc2626;
    }

    /* Plan Content */
    .premium-plan-card-body {
        padding: 35px 28px 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        z-index: 2;
    }

    .premium-plan-card-body::before {
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

    /* Plan Icon */
    .plan-icon-wrapper {
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

    .plan-icon-wrapper::before {
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

    .premium-plan-card:hover .plan-icon-wrapper::before {
        opacity: 1;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .premium-plan-card:hover .plan-icon-wrapper {
        transform: scale(1.05);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.3);
    }

    .plan-icon {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        border: 3px solid white;
        transition: var(--transition);
        background: inherit;
    }

    .premium-plan-card.recommended .plan-icon-wrapper {
        background: linear-gradient(135deg, var(--warning), #fbbf24);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.2);
    }

    .premium-plan-card.active .plan-icon-wrapper {
        background: linear-gradient(135deg, var(--success), #34d399);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
    }

    /* Plan Information */
    .plan-name {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 12px;
        text-align: center;
        line-height: 1.2;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: var(--transition);
    }

    .premium-plan-card:hover .plan-name {
        transform: scale(1.02);
    }

    .plan-price {
        text-align: center;
        margin-bottom: 20px;
        padding: 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 20px;
        border: 1px solid rgba(102, 126, 234, 0.1);
        transition: var(--transition);
    }

    .premium-plan-card:hover .plan-price {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-color: rgba(102, 126, 234, 0.2);
    }

    .price-currency {
        font-size: 18px;
        font-weight: 600;
        color: #6b7280;
        vertical-align: top;
    }

    .price-amount {
        font-size: 32px;
        font-weight: 800;
        color: #667eea;
        line-height: 1;
        margin: 0 4px;
    }

    .price-duration {
        font-size: 14px;
        color: #9ca3af;
        font-weight: 500;
        display: block;
        margin-top: 4px;
    }

    /* Plan Features */
    .plan-features {
        margin-bottom: 24px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
        padding: 16px;
        border: 1px solid rgba(102, 126, 234, 0.1);
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
    }

    .feature-item:not(:last-child) {
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        padding-bottom: 12px;
        margin-bottom: 12px;
    }

    .feature-icon {
        width: 18px;
        height: 18px;
        color: #667eea;
        font-size: 14px;
        flex-shrink: 0;
    }

    .feature-text {
        color: #6b7280;
        font-weight: 500;
        font-size: 14px;
    }

    .feature-text strong {
        font-weight: 700;
        color: #1f2937;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Plan Actions */
    .plan-actions {
        margin-top: auto;
    }

    .plan-btn,
    .current-plan,
    .request-link,
    .requested-badge {
        width: 100%;
        padding: 12px 20px;
        border-radius: 25px;
        font-weight: 600;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 12px;
        transition: var(--transition);
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }

    .plan-btn::before,
    .current-plan::before,
    .request-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .plan-btn:hover::before,
    .current-plan:hover::before,
    .request-link:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .plan-btn {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .plan-btn.free {
        background: linear-gradient(135deg, var(--success), #34d399);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .plan-btn:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .current-plan {
        background: linear-gradient(135deg, var(--success), #34d399);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .request-link {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    .request-link:hover {
        background: var(--primary);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .requested-badge {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(156, 163, 175, 0.3);
    }

    .plan-expires {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        color: #6b7280;
        padding: 12px;
        border-radius: 20px;
        text-align: center;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-weight: 500;
        border: 1px solid rgba(102, 126, 234, 0.1);
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

    /* Modal Styles - Enhanced */
    .modal-content {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: var(--shadow-xl);
    }

    .modal-form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }

    .modal-form-label.required::after {
        content: ' *';
        color: var(--danger);
    }

    .field-validation {
        position: relative;
        margin-bottom: 20px;
    }

    .modal-form-input,
    .modal-form-select {
        width: 100%;
        padding: 12px 16px 12px 48px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        transition: var(--transition);
        background: white;
    }

    .modal-form-input:focus,
    .modal-form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .modal-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 14px;
    }

    .validation-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--success);
        font-size: 14px;
        opacity: 0;
        transition: var(--transition);
    }

    .field-validation.valid .validation-icon {
        opacity: 1;
    }

    .field-validation.focused .modal-form-input,
    .field-validation.focused .modal-form-select {
        border-color: var(--primary);
    }

    .modal-button-container {
        text-align: center;
        margin-top: 24px;
    }

    .modal-btn {
        padding: 12px 32px;
        border-radius: 25px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modal-btn-submit {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .modal-btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .modal-btn.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .plans-grid {
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

        .plans-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .premium-plan-card {
            min-height: 420px;
        }

        .premium-plan-card-body {
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title-compact">{{ __('Manage Plans') }}</h1>
                    <p class="page-subtitle-compact">{{ __('Choose the perfect plan for your organization') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $plans->count() }}</p>
                    <p class="stat-label"><b>{{ __('Available Plans') }}</b></p>
                </div>
                @can('Create Plan')
                @if (
                    !empty($admin_payment_setting) &&
                        (($admin_payment_setting['is_stripe_enabled'] == 'on' &&
                            !empty($admin_payment_setting['stripe_key']) &&
                            !empty($admin_payment_setting['stripe_secret'])) ||
                            ($admin_payment_setting['is_paypal_enabled'] == 'on' &&
                                !empty($admin_payment_setting['paypal_client_id']) &&
                                !empty($admin_payment_setting['paypal_secret_key']))))
                <div class="stat-item">
                    <a href="#" data-url="{{ route('plans.create') }}" data-ajax-popup="true" 
                       data-title=""
                       class="premium-btn">
                        <i class="fa fa-plus"></i> {{ __('Create') }}
                    </a>
                </div>
                @endif
                @endcan
            </div>
        </div>
    </div>

    @php
        $activePlanId = \Auth::user()->plan;
        $active = $activePlanId ? true : false;
    @endphp

    @if($plans->count() > 0)
        <div class="plans-grid">
            @foreach ($plans as $plan)
            @php
                $isCompany = \Auth::user()->type == 'company';
                $isRecommended = false;
                $isActive = false;
                
                if(\Auth::user()->plan == $plan->id){
                    $isActive = true;
                }
                else if(!$active && $isCompany && \Auth::user()->employees_count == $plan->max_employees){
                    $isRecommended = true;
                }
            @endphp
            <div class="fade-in premium-plan-card {{ $isRecommended ? 'recommended' : '' }} {{ $isActive ? 'active' : '' }} {{ $isRecommended || $isActive || \Auth::user()->type == 'super admin' ? '' : 'blurred' }}" 
                 style="animation-delay: {{ $loop->index * 0.1 }}s">
                
                {{-- Plan Status Badge --}}
                @if($isRecommended && !$active)
                <div class="plan-badge recommended">
                    <i class="fas fa-star"></i>
                    {{ __('Recommended') }}
                </div>
                @endif
                @if($isActive)
                <div class="plan-badge active">
                    <i class="fas fa-check-circle"></i>
                    {{ __('Active') }}
                </div>
                @endif

                {{-- Actions Dropdown --}}
                @if (Gate::check('Edit Plan') && \Auth::user()->type == 'super admin')
                <div class="plan-actions-dropdown">
                    <div class="dropdown">
                        <button class="actions-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" data-ajax-popup="true" 
                                   data-url="{{ route('plans.edit', $plan->id) }}" 
                                   data-title="{{ __('Edit Plan') }}"
                                   class="dropdown-item">
                                    <i class="fas fa-edit"></i>
                                    {{ __('Edit') }}
                                </a>
                            </li>
                            @if($plan->id != 1)
                            <li>
                                <a href="#" class="dropdown-item text-danger delete-plan" 
                                   data-plan-id="{{ $plan->id }}"
                                   data-plan-name="{{ $plan->name }}">
                                    <i class="fas fa-trash"></i>
                                    {{ __('Delete') }}
                                </a>
                                <form id="delete-form-{{ $plan->id }}" action="{{ route('plans.destroy', $plan->id) }}" method="POST" style="display: none;">
                                    @csrf 
                                    @method('DELETE')
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                @endif

                <div class="premium-plan-card-body">
                    <div class="card-main-content">
                        {{-- Plan Icon --}}
                        <div class="plan-icon-wrapper">
                            <div class="plan-icon">
                                @if($plan->price == 0)
                                    <i class="fas fa-gift"></i>
                                @elseif($isRecommended)
                                    <i class="fas fa-crown"></i>
                                @else
                                    <i class="fas fa-rocket"></i>
                                @endif
                            </div>
                        </div>

                        {{-- Plan Info --}}
                        <h5 class="plan-name">{{ $plan->name }}</h5>
                        <div class="plan-price">
                            <span class="price-currency">{{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') }}</span>
                            <span class="price-amount">{{ $plan->price }}</span>
                            @if($plan->price > 0)
                            <span class="price-duration">/ {{ ucfirst($plan->duration) }}</span>
                            @elseif($plan->duration == '2_weeks')
                            <span class="price-duration">for 2 Weeks</span>
                            @else
                            <span class="price-duration">for a {{ ucfirst($plan->duration) }}</span>
                            @endif
                        </div>

                        {{-- Plan Features --}}
                        <div class="plan-features">
                            <div class="feature-item">
                                <i class="fas fa-users feature-icon"></i>
                                <span class="feature-text">
                                    <strong>{{ $plan->max_users == -1 ? __('Unlimited') : number_format($plan->max_users) }}</strong> {{ __('Users') }}
                                </span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-user-tie feature-icon"></i>
                                <span class="feature-text">
                                    <strong>{{ $plan->max_employees == -1 ? __('Unlimited') : number_format($plan->max_employees) }}</strong> {{ __('Employees') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Plan Actions --}}
                    <div class="plan-actions">
                        @if(\Auth::user()->type != 'super admin')
                            @if($isActive)
                                <div class="current-plan">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __('Current Plan') }}
                                </div>
                                @if(\Auth::user()->type == 'company' && \Auth::user()->plan_expire_date)
                                <div class="plan-expires">
                                    <i class="fas fa-clock"></i>
                                    {{ __('Expires: ') }}{{ \Auth::user()->dateFormat(\Auth::user()->plan_expire_date) }}
                                </div>
                                @endif
                            @else
                                {{-- Purchase/Request Actions --}}
                                @if (
                                    (!empty($admin_payment_setting) &&
                                        ($admin_payment_setting['is_stripe_enabled'] == 'on' ||
                                            $admin_payment_setting['is_paypal_enabled'] == 'on' ||
                                            $admin_payment_setting['is_paystack_enabled'] == 'on' ||
                                            $admin_payment_setting['is_flutterwave_enabled'] == 'on' ||
                                            $admin_payment_setting['is_razorpay_enabled'] == 'on' ||
                                            $admin_payment_setting['is_mercado_enabled'] == 'on' ||
                                            $admin_payment_setting['is_paytm_enabled'] == 'on' ||
                                            $admin_payment_setting['is_mollie_enabled'] == 'on' ||
                                            $admin_payment_setting['is_paypal_enabled'] == 'on' ||
                                            $admin_payment_setting['is_skrill_enabled'] == 'on' ||
                                            $admin_payment_setting['is_coingate_enabled'] == 'on')) ||
                                        (isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on'))
                                    @can('Buy Plan')
                                        @if($plan->price > 0)
                                            <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                               class="plan-btn">
                                                <i class="fas fa-credit-card"></i>
                                                {{ __('Buy Plan') }}
                                            </a>
                                        @else
                                            <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                               class="plan-btn free">
                                                <i class="fas fa-gift"></i>
                                                {{ __('Get Free') }}
                                            </a>
                                        @endif
                                    @endcan
                                @endif

                                {{-- Plan Request --}}
                                @if($plan->id != 1)
                                    @if(\Auth::user()->requested_plan != $plan->id)
                                        <a href="{{ route('plan_request', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                           class="request-link">
                                            <i class="fas fa-paper-plane"></i>
                                            {{ __('Request Plan') }}
                                        </a>
                                    @else
                                        <div class="requested-badge">
                                            <i class="fas fa-clock"></i>
                                            {{ __('Request Pending') }}
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="premium-plan-card fade-in">
            <div class="premium-plan-card-body">
                <div class="empty-state">
                    <i class="fas fa-credit-card"></i>
                    <h3>{{ __('No Plans Found') }}</h3>
                    <p>{{ __('Start by creating your first subscription plan to get started.') }}</p>
                    @can('Create Plan')
                    @if (
                        !empty($admin_payment_setting) &&
                            (($admin_payment_setting['is_stripe_enabled'] == 'on' &&
                                !empty($admin_payment_setting['stripe_key']) &&
                                !empty($admin_payment_setting['stripe_secret'])) ||
                                ($admin_payment_setting['is_paypal_enabled'] == 'on' &&
                                    !empty($admin_payment_setting['paypal_client_id']) &&
                                    !empty($admin_payment_setting['paypal_secret_key']))))
                        <a href="#" data-url="{{ route('plans.create') }}" data-ajax-popup="true" 
                           data-title="{{ __('Create New Plan') }}"
                           class="premium-btn" style="background: var(--primary); border-color: var(--primary); margin-top: 20px;">
                            <i class="fa fa-plus"></i> {{ __('Create Your First Plan') }}
                        </a>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Organizational Info Modal --}}
<div id="organizationalInfoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="organizationalInfoModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Modal Header with Premium Design --}}
            <div class="page-header-premium" style="margin-bottom: 0; border-radius: 24px 24px 0 0;">
                <div class="header-content" style="justify-content: space-between;">
                    <div class="header-left">
                        <div class="header-icon" style="width: 56px; height: 56px; font-size: 1.4rem;">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="header-text">
                            <h1 style="font-size: 1.6rem; margin: 0;">{{ __('Organization Info') }}</h1>
                            <p style="margin: 4px 0 0 0; font-size: 0.85rem;">{{ __('Complete your organization details') }}</p>
                        </div>
                    </div>
                    <div class="header-stats">
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="actions-btn" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div style="background: white; padding: 32px; border-radius: 0 0 24px 24px;">
                <form id="organizationalInfoForm">
                    <div class="row gx-4 gy-4">
                        <div class="col-lg-6">
                            <label class="modal-form-label">{{ __('Name') }}</label>
                            <div class="field-validation position-relative">
                                <input type="text" class="form-control modal-form-input" value="{{ Auth::user()->name }}" readonly disabled>
                                <i class="fas fa-user modal-input-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="modal-form-label">{{ __('Email') }}</label>
                            <div class="field-validation position-relative">
                                <input type="email" class="form-control modal-form-input" value="{{ Auth::user()->email }}" readonly disabled>
                                <i class="fas fa-envelope modal-input-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="modal-form-label required">{{ __('Company Name') }}</label>
                            <div class="field-validation position-relative">
                                <input type="text" class="form-control modal-form-input" id="company_name" required>
                                <i class="fas fa-building modal-input-icon"></i>
                                <i class="fas fa-check-circle validation-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="modal-form-label required">{{ __('Employees Count') }}</label>
                            <div class="field-validation position-relative">
                                <select class="form-control modal-form-select" id="employees_count" required>
                                    <option value="">{{ __('Select employee count') }}</option>
                                    <option value="10">{{ __('Up to 10') }}</option>
                                    <option value="50">{{ __('Up to 50') }}</option>
                                    <option value="100">{{ __('Up to 100') }}</option>
                                    <option value="150">{{ __('Up to 150') }}</option>
                                    <option value="200">{{ __('Up to 200') }}</option>
                                </select>
                                <i class="fas fa-check-circle validation-icon validation-icon-select"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="modal-button-container">
                                <button type="button" id="saveButton" class="modal-btn modal-btn-submit">
                                    <i class="fas fa-save"></i>
                                    {{ __('Save Information') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
document.addEventListener("DOMContentLoaded", function () {
    var authUserType = "{{ \Auth::user()->type }}";
    if(authUserType == 'super admin'){
        document.querySelectorAll('.blurred').forEach(function(el) {
            el.classList.remove('blurred');
        });
    }
    
    let userType = "{{ Auth::user()->type }}";
    let companyName = "{{ Auth::user()->company_name }}";
    let employeesCount = "{{ Auth::user()->employees_count }}";

    // Show organizational info modal for companies without complete info
    if (userType === 'company' && (!companyName || !employeesCount)) {
        $('#organizationalInfoModal').modal('show');
        $('body').addClass('modal-open');
    }

    // Save organizational info
    $('#saveButton').click(function () {
        let company_name = $('#company_name').val();
        let employees_count = $('#employees_count').val();

        if (company_name && employees_count) {
            $(this).addClass('loading');
            $(this).prop('disabled', true);
            
            $.ajax({
                url: '{{ route('update.organization.info') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    company_name: company_name,
                    employees_count: employees_count
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error saving information. Please try again.');
                        $('#saveButton').removeClass('loading').prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Error saving information. Please try again.');
                    $('#saveButton').removeClass('loading').prop('disabled', false);
                }
            });
        } else {
            alert('Please fill in all required fields.');
        }
    });

    // Enhanced plan card interactions for companies
    if (userType === 'company') {
        const planCards = document.querySelectorAll('.premium-plan-card');
        let selectedCard = null;

        planCards.forEach(card => {
            // Add hover effects
            card.addEventListener('mouseenter', function() {
                if (!this.classList.contains('active')) {
                    this.style.transform = 'translateY(-12px) scale(1.02)';
                }
            });

            card.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active') && this !== selectedCard) {
                    this.style.transform = '';
                }
            });

            // Click interactions
            card.addEventListener('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (e.target.closest('.plan-actions') || e.target.closest('.plan-actions-dropdown')) {
                    return;
                }

                selectedCard = this;
                
                // Animate card selection
                planCards.forEach(c => {
                    if (c !== this) {
                        c.classList.add('blurred');
                        c.classList.remove('recommended');
                        c.style.transform = '';
                    } else {
                        c.classList.remove('blurred');
                        if (!c.classList.contains('active')) {
                            c.classList.add('recommended');
                            c.style.transform = 'translateY(-8px) scale(1.05)';
                        }
                    }
                });
            });
        });

        // Reset selection on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.premium-plan-card')) {
                planCards.forEach(c => {
                    if (!c.classList.contains('active')) {
                        c.classList.remove('blurred');
                        c.classList.remove('recommended');
                        c.style.transform = '';
                    }
                });
                selectedCard = null;
            }
        });
    }

    // Enhanced delete confirmation for plans
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-plan')) {
            e.preventDefault();
            const deleteBtn = e.target.closest('.delete-plan');
            const planId = deleteBtn.dataset.planId;
            const planName = deleteBtn.dataset.planName;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete the plan "${planName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete plan!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const card = deleteBtn.closest('.premium-plan-card');
                        if (card) {
                            card.classList.add('loading-card');
                        }
                        document.getElementById(`delete-form-${planId}`).submit();
                    }
                });
            } else {
                const confirmMessage = `Are you sure you want to delete the plan "${planName}"? This action cannot be undone.`;
                if (confirm(confirmMessage)) {
                    const card = deleteBtn.closest('.premium-plan-card');
                    if (card) {
                        card.classList.add('loading-card');
                    }
                    document.getElementById(`delete-form-${planId}`).submit();
                }
            }
        }
    });

    // Loading state for AJAX actions
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-ajax-popup]')) {
            const card = e.target.closest('.premium-plan-card');
            if (card) {
                card.classList.add('loading-card');
                setTimeout(() => {
                    card.classList.remove('loading-card');
                }, 3000);
            }
        }
    });

    // Dropdown animations
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        const button = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        
        if (button && menu) {
            button.addEventListener('show.bs.dropdown', function() {
                menu.style.transform = 'translateY(-10px)';
                menu.style.opacity = '0';
            });
            
            button.addEventListener('shown.bs.dropdown', function() {
                menu.style.transform = 'translateY(0)';
                menu.style.opacity = '1';
            });
            
            button.addEventListener('hide.bs.dropdown', function() {
                menu.style.transform = 'translateY(-10px)';
                menu.style.opacity = '0';
            });
        }
    });

    // Remove loading state when modal is closed
    document.addEventListener('hidden.bs.modal', function() {
        document.querySelectorAll('.loading-card').forEach(card => {
            card.classList.remove('loading-card');
        });
    });

    // Form validation for modal
    const formFields = document.querySelectorAll('#organizationalInfoForm .modal-form-input, #organizationalInfoForm .modal-form-select');
    
    function updateFormValidation() {
        formFields.forEach(field => {
            const validationContainer = field.closest('.field-validation');
            if (field.hasAttribute('required') && field.value.trim() !== '') {
                validationContainer.classList.add('valid');
                validationContainer.classList.remove('invalid');
            } else if (field.hasAttribute('required')) {
                validationContainer.classList.remove('valid');
            }
        });
    }

    formFields.forEach(field => {
        field.addEventListener('input', updateFormValidation);
        field.addEventListener('change', updateFormValidation);
        
        field.addEventListener('focus', function() {
            this.closest('.field-validation').classList.add('focused');
        });
        
        field.addEventListener('blur', function() {
            this.closest('.field-validation').classList.remove('focused');
        });
    });

    updateFormValidation();

    // Enhanced card animations
    const cards = document.querySelectorAll('.premium-plan-card');

    cards.forEach(card => {
        // Enhanced hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.premium-btn, .plan-btn, .request-link');
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
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.4)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple-animation 0.6s linear';
            ripple.style.pointerEvents = 'none';

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

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

// Add CSS for ripple animation
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