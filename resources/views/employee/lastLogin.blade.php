@extends('layouts.admin')
@section('page-title')
    {{__('Last Login')}}
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
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        background-attachment: fixed;
        min-height: 100vh;
        position: relative;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3), transparent 50.2%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1), transparent 50.2%),
            radial-gradient(circle at 40% 80%, rgba(120, 119, 198, 0.2), transparent 50.2%);
        pointer-events: none;
        z-index: -1;
    }

    .content-wrapper {
        background: transparent;
        padding: 0;
    }

    /* Compact container */
    .container-fluid {
        padding: 0 20px;
        margin: 0 auto;
    }

    /* Ultra Premium Header */
    .page-header-premium {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.95) 0%, 
            rgba(59, 130, 246, 0.95) 50%, 
            rgba(96, 165, 250, 0.95) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        padding: 32px 40px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .page-header-premium::before {
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

    .page-header-premium::after {
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

    .page-header-premium:hover::after {
        opacity: 1;
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

    .header-text h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        letter-spacing: -0.025em;
    }

    .header-text p {
        color: rgba(255, 255, 255, 0.9);
        margin: 6px 0 0 0;
        font-size: 1rem;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .header-stats {
        display: flex;
        gap: 32px;
        align-items: center;
    }

    .stat-item {
        text-align: center;
        color: white;
        padding: 16px 24px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 100px;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 800;
        margin: 0;
        line-height: 1;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stat-label {
        font-size: 0.75rem;
        opacity: 0.9;
        margin: 6px 0 0 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    /* Ultra Premium Cards */
    .premium-card {
        background: rgba(255, 255, 255, 0.98);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        box-shadow: 
            0 32px 64px rgba(0, 0, 0, 0.1),
            0 8px 32px rgba(0, 0, 0, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        margin-bottom: 32px;
    }

    .premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, 
            var(--primary) 0%, 
            var(--secondary) 50%, 
            var(--accent) 100%);
        box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
    }

    .premium-card::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.03) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .premium-card:hover {
        transform: translateY(-4px) scale(1.002);
        box-shadow: 
            0 48px 80px rgba(37, 99, 235, 0.15),
            0 16px 48px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .premium-card:hover::after {
        opacity: 1;
    }

    .card-header-premium {
        background: linear-gradient(135deg, 
            rgba(248, 250, 252, 0.95) 0%, 
            rgba(241, 245, 249, 0.95) 100%);
        padding: 24px 32px;
        border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 24px 24px 0 0;
        position: relative;
    }

    .card-header-premium::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 5%;
        right: 5%;
        height: 1px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(37, 99, 235, 0.3) 20%, 
            rgba(37, 99, 235, 0.6) 50%, 
            rgba(37, 99, 235, 0.3) 80%, 
            transparent 100%);
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.025em;
    }

    .premium-card-body {
        padding: 32px;
        position: relative;
    }

    table thead tr {
        background: none;
        border-radius: 0px;
    }

    /* Elite Table Styling */
    .table-container {
        position: relative;
        overflow: hidden;
        max-height: 650px;
        border: none;
    }

    .table-responsive {
        border-radius: 20px;
        overflow: hidden;
        position: relative;
    }

    .dataTable {
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .dataTable thead {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.95) 0%, 
            rgba(59, 130, 246, 0.95) 50%, 
            rgba(96, 165, 250, 0.95) 100%);
        position: relative;
    }

    .dataTable thead::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(255, 255, 255, 0.5) 20%, 
            rgba(255, 255, 255, 0.8) 50%, 
            rgba(255, 255, 255, 0.5) 80%, 
            transparent 100%);
    }

    .dataTable thead th {
        background: transparent !important;
        color: #fff !important;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 24px 32px;
        border: none !important;
        position: relative;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .dataTable thead th:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .dataTable thead th:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 30%;
        bottom: 30%;
        width: 1px;
        background: linear-gradient(180deg, 
            transparent 0%, 
            rgba(255, 255, 255, 0.3) 20%, 
            rgba(255, 255, 255, 0.6) 50%, 
            rgba(255, 255, 255, 0.3) 80%, 
            transparent 100%);
    }

    .dataTable tbody tr {
        border: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(255, 255, 255, 0.6);
        position: relative;
    }

    .dataTable tbody tr::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 0;
        background: linear-gradient(90deg, 
            rgba(37, 99, 235, 0.1) 0%, 
            transparent 100%);
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }

    .dataTable tbody tr:hover {
        background: rgba(248, 250, 252, 0.9);
        transform: translateX(8px) scale(1.002);
        box-shadow: 
            0 8px 32px rgba(37, 99, 235, 0.1),
            0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .dataTable tbody tr:hover::before {
        width: 100%;
    }

    .dataTable tbody tr:nth-child(even) {
        background: rgba(37, 99, 235, 0.03);
    }

    .dataTable tbody tr:nth-child(even):hover {
        background: rgba(248, 250, 252, 0.9);
    }

    .dataTable tbody td {
        padding: 20px 32px;
        border: none !important;
        border-bottom: 1px solid rgba(241, 245, 249, 0.6) !important;
        font-size: 0.95rem;
        color: var(--text-primary);
        vertical-align: middle;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
    }

    .dataTable tbody td:first-child {
        font-weight: 700;
        color: var(--primary);
        text-shadow: 0 1px 2px rgba(37, 99, 235, 0.1);
    }
    

    /* Enhanced Role Badge */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 24px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: capitalize;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .role-badge::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: rotate(-45deg) translateX(-100%);
        transition: transform 0.6s ease;
    }

    .role-badge:hover::before {
        transform: rotate(-45deg) translateX(100%);
    }

    .role-employee {
        background: linear-gradient(135deg, 
            rgba(16, 185, 129, 0.15) 0%, 
            rgba(16, 185, 129, 0.1) 100%);
        color: var(--success);
        border-color: rgba(16, 185, 129, 0.3);
        box-shadow: 
            0 4px 16px rgba(16, 185, 129, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .role-employee:hover {
        background: linear-gradient(135deg, 
            rgba(16, 185, 129, 0.25) 0%, 
            rgba(16, 185, 129, 0.15) 100%);
        transform: translateY(-1px);
        box-shadow: 
            0 8px 24px rgba(16, 185, 129, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .role-admin {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.15) 0%, 
            rgba(37, 99, 235, 0.1) 100%);
        color: var(--primary);
        border-color: rgba(37, 99, 235, 0.3);
        box-shadow: 
            0 4px 16px rgba(37, 99, 235, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .role-admin:hover {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.25) 0%, 
            rgba(37, 99, 235, 0.15) 100%);
        transform: translateY(-1px);
        box-shadow: 
            0 8px 24px rgba(37, 99, 235, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .role-manager {
        background: linear-gradient(135deg, 
            rgba(245, 158, 11, 0.15) 0%, 
            rgba(245, 158, 11, 0.1) 100%);
        color: var(--warning);
        border-color: rgba(245, 158, 11, 0.3);
        box-shadow: 
            0 4px 16px rgba(245, 158, 11, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .role-manager:hover {
        background: linear-gradient(135deg, 
            rgba(245, 158, 11, 0.25) 0%, 
            rgba(245, 158, 11, 0.15) 100%);
        transform: translateY(-1px);
        box-shadow: 
            0 8px 24px rgba(245, 158, 11, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    /* Elite Status Indicators */
    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        position: relative;
        transition: all 0.3s ease;
    }

    .status-dot::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border-radius: 50%;
        opacity: 0.3;
        transition: all 0.3s ease;
    }

    .status-online {
        background: var(--success);
        box-shadow: 
            0 0 10px rgba(16, 185, 129, 0.5),
            0 0 20px rgba(16, 185, 129, 0.3);
        animation: pulse 2s infinite;
    }

    .status-online::before {
        background: var(--success);
        animation: pulseRing 2s infinite;
    }

    .status-offline {
        background: var(--text-secondary);
        animation: none;
    }

    .status-offline::before {
        background: var(--text-secondary);
    }

    @keyframes pulseRing {
        0% {
            transform: scale(0.8);
            opacity: 0.8;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.3;
        }
        100% {
            transform: scale(0.8);
            opacity: 0.8;
        }
    }

    /* Premium Quick Actions */
    .quick-actions {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 
            0 16px 40px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.6);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .quick-actions:hover {
        transform: translateY(-2px);
        box-shadow: 
            0 24px 48px rgba(0, 0, 0, 0.12),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .filter-group {
        display: flex;
        gap: 16px;
        align-items: center;
    }

    .filter-select {
        border: 2px solid rgba(229, 231, 235, 0.6);
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.9rem;
        background: rgba(255, 255, 255, 0.95);
        min-width: 140px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
    }

    .filter-select:focus {
        border-color: var(--primary);
        box-shadow: 
            0 0 0 4px rgba(37, 99, 235, 0.1),
            0 4px 16px rgba(37, 99, 235, 0.2);
        outline: none;
        background: rgba(255, 255, 255, 0.95);
    }

    .premium-btn-sm {
        background: linear-gradient(135deg, 
            var(--primary) 0%, 
            var(--secondary) 100%);
        border: none;
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 4px 16px rgba(37, 99, 235, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .premium-btn-sm::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(255, 255, 255, 0.2) 50%, 
            transparent 100%);
        transition: left 0.6s ease;
    }

    .premium-btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 
            0 8px 24px rgba(37, 99, 235, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        color: white;
    }

    .premium-btn-sm:hover::before {
        left: 100%;
    }

    /* Enhanced Animations */
    .fade-in {
        animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Premium Table Scrolling */
    #lastLoginTable thead,
    #lastLoginTable tbody {
        display: block;
    }

    #lastLoginTable thead tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    #lastLoginTable tbody {
        display: block;
        max-height: 400px;
        overflow-y: auto;
        overflow-x: hidden;
        width: 100%;
        scrollbar-width: thin;
        scrollbar-color: rgba(37, 99, 235, 0.3) rgba(0, 0, 0, 0.1);
    }

    #lastLoginTable tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    #lastLoginTable th:nth-child(1),
    #lastLoginTable td:nth-child(1) { width: 15%; }
    #lastLoginTable th:nth-child(2),
    #lastLoginTable td:nth-child(2) { width: 30%; }
    #lastLoginTable th:nth-child(3),
    #lastLoginTable td:nth-child(3) { width: 35%; }
    #lastLoginTable th:nth-child(4),
    #lastLoginTable td:nth-child(4) { width: 20%; }

    /* Ultra Premium Scrollbar */
    #lastLoginTable tbody::-webkit-scrollbar {
        width: 12px;
    }

    #lastLoginTable tbody::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        margin: 4px 0;
    }

    #lastLoginTable tbody::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, 
            rgba(37, 99, 235, 0.6) 0%, 
            rgba(59, 130, 246, 0.6) 50%, 
            rgba(96, 165, 250, 0.6) 100%);
        border-radius: 10px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 2px 8px rgba(37, 99, 235, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    #lastLoginTable tbody::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, 
            rgba(37, 99, 235, 0.8) 0%, 
            rgba(59, 130, 246, 0.8) 50%, 
            rgba(96, 165, 250, 0.8) 100%);
        box-shadow: 
            0 4px 16px rgba(37, 99, 235, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    #lastLoginTable tbody::-webkit-scrollbar-corner {
        background: transparent;
    }

    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(30px) scale(0.95); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1); 
        }
    }

    @keyframes pulse {
        0%, 100% { 
            transform: scale(1); 
            opacity: 1; 
        }
        50% { 
            transform: scale(1.1); 
            opacity: 0.8; 
        }
    }

    /* Ultra Responsive Design */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 16px;
        }

        .page-header-premium {
            padding: 24px 28px;
            border-radius: 20px;
        }

        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        
        .header-stats {
            justify-content: center;
            gap: 20px;
        }

        .stat-item {
            padding: 12px 20px;
            min-width: 80px;
        }

        .quick-actions {
            flex-direction: column;
            gap: 16px;
            padding: 20px 24px;
        }

        .filter-group {
            flex-wrap: wrap;
            justify-content: center;
        }

        .dataTable thead th,
        .dataTable tbody td {
            padding: 16px 20px;
            font-size: 0.85rem;
        }

        .premium-card-body {
            padding: 24px;
        }
    }

    /* Premium Loading States */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        z-index: 1000;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid rgba(37, 99, 235, 0.1);
        border-top: 4px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.2);
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Enhanced Badge Styling */
    .badge {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.1) 0%, 
            rgba(37, 99, 235, 0.05) 100%) !important;
        color: var(--primary) !important;
        padding: 10px 20px !important;
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        border-radius: 16px !important;
        border: 2px solid rgba(37, 99, 235, 0.2) !important;
        box-shadow: 
            0 4px 16px rgba(37, 99, 235, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .badge:hover {
        transform: translateY(-1px);
        box-shadow: 
            0 8px 24px rgba(37, 99, 235, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
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
                    <i class="fas fa-clock"></i>
                </div>
                <div class="header-text">
                    <h1>{{__('Last Login Activity')}}</h1>
                    <p>{{__('Monitor user login patterns and system access')}}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $users->where('type', 'employee')->count() }}</p>
                    <p class="stat-label">{{__('Employees')}}</p>
                </div>
                {{-- <div class="stat-item">
                    <p class="stat-number">{{ $users->where('type', 'admin')->count() }}</p>
                    <p class="stat-label">{{__('Admins')}}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">{{ $users->count() }}</p>
                    <p class="stat-label">{{__('Total Users')}}</p>
                </div> --}}
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="premium-card fade-in">
        <div class="card-header-premium">
            <h3 class="card-title">
                <i class="fas fa-users text-primary"></i>
                {{__('User Login History')}}
            </h3>
            <span class="badge badge-pill" style="background: rgba(37, 99, 235, 0.1); color: var(--primary); padding: 8px 16px; font-size: 0.8rem;">
                {{ $users->count() }} {{__('Records')}}
            </span>
        </div>
        
        <div class="premium-card-body" style="padding: 20px;">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 datatable " id="lastLoginTable">
                        <thead>
                            <tr>
                                <th>
                                    <i class="fas fa-hashtag me-2"></i>
                                    {{__('ID')}}
                                </th>
                                <th>
                                    <i class="fas fa-user me-2"></i>
                                    {{__('Name')}}
                                </th>
                                <th>
                                    <i class="fas fa-clock me-2"></i>
                                    {{__('Last Login')}}
                                </th>
                                <th>
                                    <i class="fas fa-user-tag me-2"></i>
                                    {{__('Role')}}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="font-style">
                            @foreach ($users as $user)
                                <tr data-role="{{ $user->type }}">
                                    <td>
                                        <strong>{{ \Auth::user()->employeeIdFormat($user->id) }}</strong>
                                        {{-- @if($user->type=='employee')
                                        @else
                                            <span style="color: var(--text-secondary);">--</span>
                                        @endif  --}}
                                    </td>
                                    <td>
                                        <div class="status-indicator">
                                            <div class="status-dot {{ $user->last_login && \Carbon\Carbon::parse($user->last_login)->diffInHours(now()) < 24 ? 'status-online' : 'status-offline' }}"></div>
                                            <strong>{{ $user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->last_login)
                                            <div>
                                                <strong>{{ \Carbon\Carbon::parse($user->last_login)->format('M d, Y h:i A') }}</strong>
                                                <br>
                                                <small style="color: var(--text-secondary);">
                                                    {{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}
                                                </small>
                                            </div>
                                        @else
                                            <span style="color: var(--text-secondary); font-style: italic;">{{__('Never logged in')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="role-badge role-{{ $user->type }}">
                                            @if($user->type == 'super admin')
                                                <i class="fas fa-user-shield"></i>
                                            @elseif($user->type == 'employee')
                                                <i class="fas fa-user"></i>
                                            @else
                                                <i class="fas fa-user-tie"></i>
                                            @endif
                                            {{ ucfirst($user->type) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit to ensure all other scripts have loaded
    setTimeout(function() {
        initializeDataTable();
    }, 100);

    function initializeDataTable() {
        // Initialize DataTable if available
        if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            try {
                // Multiple ways to check and destroy existing DataTable
                const table = $('#lastLoginTable');
                
                // Method 1: Check if it's a DataTable and destroy
                if ($.fn.DataTable.isDataTable('#lastLoginTable')) {
                    table.DataTable().clear().destroy();
                }
                
                // Method 2: Remove any existing DataTable classes and data
                table.removeClass('dataTable');
                table.removeAttr('role');
                table.find('thead, tbody, tfoot').removeClass();
                
                // Method 3: Clear any DataTable wrapper
                if (table.parent().hasClass('dataTables_wrapper')) {
                    table.unwrap();
                }
                
                // Now safely initialize DataTable
                table.DataTable({
                    responsive: true,
                    pageLength: 25,
                    destroy: true, // This ensures any existing table is destroyed
                    order: [[2, 'desc']], // Sort by last login date
                    columnDefs: [
                        { orderable: false, targets: [3] } // Disable sorting for role column
                    ],
                    language: {
                        search: "{{__('Search users')}}:",
                        lengthMenu: "{{__('Show')}} _MENU_ {{__('entries')}}",
                        info: "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
                        infoEmpty: "{{__('No entries available')}}",
                        infoFiltered: "({{__('filtered from')}} _MAX_ {{__('total entries')}})",
                        paginate: {
                            first: "{{__('First')}}",
                            last: "{{__('Last')}}",
                            next: "{{__('Next')}}",
                            previous: "{{__('Previous')}}"
                        }
                    }
                });
                
                console.log('DataTable initialized successfully');
                
            } catch (error) {
                console.warn('DataTable initialization failed:', error);
                // Fallback: Use basic table functionality
                initializeBasicTable();
            }
        } else {
            console.log('DataTables not available, using basic table');
            // Fallback: Use basic table functionality
            initializeBasicTable();
        }
    }

    function initializeBasicTable() {
        // Basic table functionality without DataTables
        const table = document.getElementById('lastLoginTable');
        const rows = table.querySelectorAll('tbody tr');
        
        // Add basic search functionality
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = '{{__("Search users...")}}';
        searchInput.className = 'form-control mb-3';
        searchInput.style.maxWidth = '200px';
        
        // Insert search input before table
        table.parentNode.insertBefore(searchInput, table);
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Filter functionality
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#lastLoginTable tbody tr');

    function applyFilters() {
        const selectedRole = roleFilter.value;
        const selectedStatus = statusFilter.value;

        tableRows.forEach(row => {
            let showRow = true;
            const userRole = row.getAttribute('data-role');
            
            // Role filter
            if (selectedRole && userRole !== selectedRole) {
                showRow = false;
            }

            // Status filter (simplified - you can enhance this based on your needs)
            if (selectedStatus && selectedStatus === 'recent') {
                const statusDot = row.querySelector('.status-dot');
                if (!statusDot.classList.contains('status-online')) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    if (roleFilter && statusFilter) {
        roleFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
    }

    // Refresh data function
    window.refreshData = function() {
        const button = event.target.closest('.premium-btn-sm');
        const icon = button.querySelector('i');
        
        icon.classList.add('fa-spin');
        button.disabled = true;
        
        setTimeout(() => {
            location.reload();
        }, 1000);
    };

    // Export data function
    window.exportData = function() {
        // Implement export functionality
        console.log('Exporting data...');
    };

    // Add loading animation to table
    const tableContainer = document.querySelector('.table-container');
    
    function showLoading() {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
        tableContainer.style.position = 'relative';
        tableContainer.appendChild(loadingOverlay);
    }

    function hideLoading() {
        const loadingOverlay = tableContainer.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }

    // Enhanced hover effects
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, #f8fafc, #f1f5f9)';
            this.style.transform = 'translateX(4px)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.background = '';
            this.style.transform = '';
        });
    });
});
</script>
@endsection