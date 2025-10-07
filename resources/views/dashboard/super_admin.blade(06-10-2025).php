@extends('layouts.admin')

@section('page-title')
    {{ __('Dashboard') }}
@endsection
@push('css-page')
<style>
    /* ===================================
    PROFESSIONAL REFINED METRICS CARDS
    ================================== */

    /* Refined Metrics Grid - Professional spacing */
    .modern-dashboard .metrics-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
        margin: 0 0 2.5rem 0 !important;
        padding: 0 !important;
        width: 100% !important;
        list-style: none !important;
        flex-direction: unset !important;
        align-items: unset !important;
        justify-content: unset !important;
        grid-auto-rows: minmax(120px, auto);
        transition: all var(--transition-normal);
    }
    .dashboard-row .second-row {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    .chart-container::before {
        display: none;
    }
    .apexcharts-zoom-icon{
        display: none;
    }
    .apexcharts-pan-icon{
        display: none;
    }
    .second-row {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    @media (min-width: 640px) {
        .modern-dashboard .metrics-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 1.5rem !important;
        }
    }

    @media (min-width: 768px) {
        .modern-dashboard .metrics-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 2rem !important;
        }
    }

    @media (min-width: 1024px) {
        .modern-dashboard .metrics-grid {
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 1.75rem !important;
        }
    }

    @media (min-width: 1200px) {
        .modern-dashboard .metrics-grid {
            gap: 2rem !important;
        }
    }

    /* Refined Metric Cards - Professional & Clean */
    .modern-dashboard .metric-card-modern {
        display: flex !important;
        align-items: center;
        gap: 1.25rem !important;
        padding: 1.5rem !important;
        background: var(--bg-primary);
        border-radius: 16px !important;
        box-shadow: 0 2px 12px -2px rgba(0, 0, 0, 0.08), 0 4px 8px -4px rgba(0, 0, 0, 0.06) !important;
        border: 1px solid rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease !important;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        min-height: 120px !important;
        width: 100% !important;
        margin: 0 !important;
        float: none !important;
        box-sizing: border-box !important;
    }

    /* Subtle professional hover effect */
    .metric-card-modern:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 25px -8px rgba(0, 0, 0, 0.15), 0 6px 16px -6px rgba(0, 0, 0, 0.08) !important;
        border-color: rgba(0, 0, 0, 0.08);
    }

    /* Subtle background enhancement */
    .metric-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 60px;
        height: 60px;
        background: radial-gradient(circle, rgba(0, 0, 0, 0.02) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(30%, -30%);
        transition: all 0.4s ease;
        opacity: 0;
    }

    .metric-card-modern:hover::before {
        opacity: 1;
        transform: translate(20%, -20%) scale(1.2);
    }

    /* Professional shine effect */
    .metric-card-modern::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
        transition: left 0.6s ease;
    }

    .metric-card-modern:hover::after {
        left: 100%;
    }

    /* Refined Icon Design - Professional sizing */
    .metric-icon-wrapper {
        flex-shrink: 0;
        position: relative;
        z-index: 2;
    }

    .metric-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px !important;
        height: 56px !important;
        border-radius: 14px !important;
        font-size: 1.4rem !important;
        color: var(--text-white);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.25) !important;
    }

    @media (min-width: 768px) {
        .metric-icon {
            width: 60px !important;
            height: 60px !important;
            font-size: 1.5rem !important;
        }
    }

    @media (min-width: 1200px) {
        .metric-icon {
            width: 64px !important;
            height: 64px !important;
            font-size: 1.6rem !important;
        }
    }

    /* Professional icon shine effect */
    .metric-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent 40%, rgba(255, 255, 255, 0.3) 50%, transparent 60%);
        transform: translateX(-100%) translateY(-100%);
        transition: transform 0.6s ease;
    }

    .metric-card-modern:hover .metric-icon::before {
        transform: translateX(0%) translateY(0%);
    }

    .metric-card-modern:hover .metric-icon {
        transform: scale(1.05);
        box-shadow: 0 6px 16px -6px rgba(0, 0, 0, 0.3) !important;
    }

    /* Professional metric details */
    .metric-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem !important;
        min-width: 0;
        z-index: 2;
        position: relative;
    }

    .metric-label {
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px !important;
        margin: 0;
        line-height: var(--line-height-tight);
        opacity: 0.85;
    }

    @media (min-width: 768px) {
        .metric-label {
            font-size: 0.8rem !important;
        }
    }

    .metric-number {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.1 !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        transition: all 0.3s ease;
        font-variant-numeric: tabular-nums;
    }

    @media (min-width: 768px) {
        .metric-number {
            font-size: 1.5rem !important;
        }
    }

    @media (min-width: 1200px) {
        .metric-number {
            font-size: 1.5rem !important;
        }
    }

    .metric-number.updating {
        transform: scale(1.03);
        color: var(--primary-color);
    }

    /* Professional growth indicators */
    .metric-growth {
        display: inline-flex !important;
        align-items: center;
        gap: 0.375rem !important;
        font-size: 0.8rem !important;
        font-weight: 500 !important;
        margin-top: 0.25rem;
        transition: all 0.3s ease;
        padding: 0.25rem 0.5rem !important;
        border-radius: 8px !important;
        width: fit-content;
    }

    .metric-growth i {
        font-size: 0.7rem !important;
        width: 14px;
        height: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
    }

    .metric-growth:hover {
        transform: translateX(2px);
    }

    /* Professional growth trend colors */
    .metric-growth.positive { 
        color: #059669 !important;
        background: rgba(16, 185, 129, 0.1) !important;
    }
    .metric-growth.negative { 
        color: #dc2626 !important;
        background: rgba(220, 38, 38, 0.1) !important;
    }
    .metric-growth.neutral { 
        color: var(--neutral-color) !important;
        background: rgba(107, 114, 128, 0.1) !important;
    }

    .metric-growth.positive:hover { 
        color: #047857 !important; 
        background: rgba(16, 185, 129, 0.15) !important;
    }
    .metric-growth.negative:hover { 
        color: #b91c1c !important;
        background: rgba(220, 38, 38, 0.15) !important;
    }

    /* Professional Card Theme Colors */
    .present-card .metric-icon {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%) !important;
        box-shadow: 0 4px 12px -4px rgba(37, 99, 235, 0.35) !important;
    }
    .present-card {
        border: 1px solid rgba(37, 99, 235, 0.08) !important;
    }
    .present-card:hover {
        border-color: rgba(37, 99, 235, 0.12) !important;
        box-shadow: 0 8px 25px -8px rgba(37, 99, 235, 0.12), 0 6px 16px -6px rgba(37, 99, 235, 0.08) !important;
    }

    .absent-card .metric-icon {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%) !important;
        box-shadow: 0 4px 12px -4px rgba(16, 185, 129, 0.35) !important;
    }
    .absent-card {
        border: 1px solid rgba(16, 185, 129, 0.08) !important;
    }
    .absent-card:hover {
        border-color: rgba(16, 185, 129, 0.12) !important;
        box-shadow: 0 8px 25px -8px rgba(16, 185, 129, 0.12), 0 6px 16px -6px rgba(16, 185, 129, 0.08) !important;
    }

    .late-card .metric-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%) !important;
        box-shadow: 0 4px 12px -4px rgba(245, 158, 11, 0.35) !important;
    }
    .late-card {
        border: 1px solid rgba(245, 158, 11, 0.08) !important;
    }
    .late-card:hover {
        border-color: rgba(245, 158, 11, 0.12) !important;
        box-shadow: 0 8px 25px -8px rgba(245, 158, 11, 0.12), 0 6px 16px -6px rgba(245, 158, 11, 0.08) !important;
    }

    .leave-card .metric-icon {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%) !important;
        box-shadow: 0 4px 12px -4px rgba(139, 92, 246, 0.35) !important;
    }
    .leave-card {
        border: 1px solid rgba(139, 92, 246, 0.08) !important;
    }
    .leave-card:hover {
        border-color: rgba(139, 92, 246, 0.12) !important;
        box-shadow: 0 8px 25px -8px rgba(139, 92, 246, 0.12), 0 6px 16px -6px rgba(139, 92, 246, 0.08) !important;
    }

    /* Responsive refinements */
    @media (max-width: 767px) {
        .metric-card-modern {
            flex-direction: column !important;
            text-align: center;
            padding: 1.25rem !important;
            gap: 0.875rem !important;
            min-height: 130px !important;
        }

        .metric-icon {
            width: 48px !important;
            height: 48px !important;
            font-size: 1.25rem !important;
        }

        .metric-number {
            font-size: 1.5rem !important;
        }

        .metric-label {
            font-size: 0.7rem !important;
        }

        .metric-growth {
            font-size: 0.75rem !important;
            padding: 0.2rem 0.4rem !important;
            margin: 0 auto;
        }
    }

    @media (max-width: 479px) {
        .modern-dashboard .metrics-grid {
            gap: 1.25rem !important;
        }

        .metric-card-modern {
            padding: 1rem !important;
            min-height: 120px !important;
        }

        .metric-icon {
            width: 44px !important;
            height: 44px !important;
            font-size: 1.1rem !important;
        }

        .metric-number {
            font-size: 1.5rem !important;
        }
    }

    /* Refined animation delays */
    .metrics-grid .metric-card-modern:nth-child(1) { 
        animation-delay: 0.05s; 
    }
    .metrics-grid .metric-card-modern:nth-child(2) { 
        animation-delay: 0.1s; 
    }
    .metrics-grid .metric-card-modern:nth-child(3) { 
        animation-delay: 0.15s; 
    }
    .metrics-grid .metric-card-modern:nth-child(4) { 
        animation-delay: 0.2s; 
    }

    /* Professional focus states */
    .metric-card-modern:focus-within {
        outline: 2px solid rgba(37, 99, 235, 0.4) !important;
        outline-offset: 2px !important;
        transform: translateY(-3px) !important;
    }

    /* Subtle data emphasis animation */
    .metric-number {
        position: relative;
    }

    .metric-number::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: currentColor;
        opacity: 0.3;
        transition: width 0.3s ease;
    }

    .metric-card-modern:hover .metric-number::after {
        width: 100%;
    }

    /* Clean, professional appearance without excessive animations */
    @media (prefers-reduced-motion: reduce) {
        .metric-card-modern,
        .metric-icon,
        .metric-growth {
            transition: none !important;
        }
        
        .metric-card-modern::before,
        .metric-card-modern::after,
        .metric-icon::before,
        .metric-number::after {
            display: none !important;
        }
    }

    /* Ensure consistent text rendering */
    .metric-card-modern * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ===================================
    PLAN REQUESTS TABLE STYLES
    ================================== */

    .plan-requests-table {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 400px;
        overflow-y: auto;
        padding: 0.5rem 0;
    }

    .plan-request-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-primary);
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 12px;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
    }

    .plan-request-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.15);
        border-color: rgba(0, 0, 0, 0.1);
    }

    .plan-request-item.empty-state {
        cursor: default;
        opacity: 0.7;
    }

    .plan-request-item.empty-state:hover {
        transform: none;
        box-shadow: none;
        border-color: rgba(0, 0, 0, 0.06);
    }

    .request-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px -2px rgba(37, 99, 235, 0.3);
    }

    .empty-state .request-icon {
        background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
        box-shadow: 0 2px 8px -2px rgba(107, 114, 128, 0.3);
    }

    .request-details {
        flex: 1;
        min-width: 0;
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
        gap: 1rem;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        min-width: 0;
    }

    .user-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.2;
        word-break: break-word;
    }

    .user-email {
        font-size: 0.75rem;
        color: var(--text-secondary);
        opacity: 0.8;
        word-break: break-word;
    }

    .request-time {
        font-size: 0.75rem;
        color: var(--text-secondary);
        opacity: 0.7;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .request-content {
        margin-top: 0.5rem;
    }

    .plan-info {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .plan-name {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary);
        background: rgba(37, 99, 235, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        border: 1px solid rgba(37, 99, 235, 0.2);
    }

    .plan-price {
        font-size: 0.875rem;
        font-weight: 600;
        color: #059669;
        background: rgba(16, 185, 129, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .plan-duration {
        font-size: 0.75rem;
        font-weight: 500;
        color: #f59e0b;
        background: rgba(245, 158, 11, 0.1);
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        border: 1px solid rgba(245, 158, 11, 0.2);
        text-transform: capitalize;
    }

    .empty-message {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .empty-submessage {
        font-size: 0.75rem;
        color: var(--text-secondary);
        opacity: 0.7;
    }

    .pulse-dot {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
        
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    /* Responsive adjustments for plan requests */
    @media (max-width: 768px) {
        .plan-request-item {
            padding: 0.75rem;
            gap: 0.75rem;
        }
        
        .request-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }
        
        .request-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .request-time {
            white-space: normal;
        }
        
        .plan-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.375rem;
        }
        
        .user-name {
            font-size: 0.8rem;
        }
        
        .user-email {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 480px) {
        .plan-requests-table {
            gap: 0.5rem;
        }
        
        .plan-request-item {
            padding: 0.625rem;
            gap: 0.625rem;
        }
        
        .request-icon {
            width: 32px;
            height: 32px;
            font-size: 0.9rem;
        }
    }

    /* ===================================
    SYSTEM HEALTH STYLES
    ================================== */

    .system-health-widget {
        width: 100% !important;
    }

    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    .status-indicator.online {
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }

    .status-indicator.offline {
        background: #ef4444;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }

    .system-metrics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .system-metric-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: var(--bg-primary);
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .system-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.15);
        border-color: rgba(0, 0, 0, 0.1);
    }

    .metric-icon-container {
        flex-shrink: 0;
    }

    .system-metric-card .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.25);
    }

    .uptime-card .metric-icon {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    }

    .tickets-card .metric-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    }

    .api-card .metric-icon {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
    }

    .activity-card .metric-icon {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
    }

    .metric-content {
        flex: 1;
        min-width: 0;
    }

    .system-metric-card .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .system-metric-card .metric-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.8;
    }

    .recent-tickets-section {
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        padding-top: 1.5rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title::before {
        content: '';
        width: 4px;
        height: 16px;
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        border-radius: 2px;
    }

    .tickets-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 250px;
        overflow-y: auto;
    }

    .ticket-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .ticket-item:hover:not(.empty-state) {
        background: rgba(0, 0, 0, 0.02);
        border-color: rgba(0, 0, 0, 0.08);
    }

    .ticket-item.empty-state {
        opacity: 0.6;
        text-align: center;
        justify-content: center;
    }

    .ticket-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-top: 0.25rem;
        flex-shrink: 0;
    }

    .ticket-status-dot.open {
        background: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }

    .ticket-status-dot.close {
        background: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }

    .ticket-status-dot.pending {
        background: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
    }

    .ticket-content {
        flex: 1;
        min-width: 0;
    }

    .ticket-title {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary);
        line-height: 1.4;
        margin-bottom: 0.375rem;
        word-break: break-word;
    }

    .ticket-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
    }

    .ticket-user {
        font-size: 0.75rem;
        color: var(--text-secondary);
        opacity: 0.8;
        font-weight: 500;
    }

    .ticket-time {
        font-size: 0.75rem;
        color: var(--text-secondary);
        opacity: 0.6;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .third-row {
        grid-template-columns: 1fr !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .system-metrics-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .system-metric-card {
            padding: 1rem;
            gap: 0.75rem;
        }
        
        .system-metric-card .metric-icon {
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
        }
        
        .system-metric-card .metric-value {
            font-size: 1.25rem;
        }
        
        .ticket-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }
        
        .ticket-time {
            white-space: normal;
        }
    }

    @media (max-width: 480px) {
        .system-metrics-grid {
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .system-metric-card {
            padding: 0.75rem;
            gap: 0.625rem;
        }
        
        .system-metric-card .metric-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }
        
        .system-metric-card .metric-value {
            font-size: 1.125rem;
        }
        
        .tickets-list {
            max-height: 200px;
        }
    }
</style>
@endpush


@section('content')
    <div class="modern-dashboard p-2">
        <!-- Professional Welcome Header -->
        <div class="welcome-header">
            <div class="welcome-content">
                <h1 class="welcome-title">{{ __('Welcome back, Super Administrator') }}</h1>
                <p class="welcome-subtitle">{{ __('Monitor your HRMS platform performance and growth metrics') }}</p>
            </div>
            <div class="header-actions">
                <div class="premium-badge">Super Admin</div>
            </div>
        </div>

        <!-- Professional Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card-modern present-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon present-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('TOTAL COMPANIES') }}</div>
                    <div class="metric-number">{{ number_format($user['total_user'] ?? 0) }}</div>
         
                </div>
            </div>

            <div class="metric-card-modern absent-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon absent-icon">
                        <!-- <i class="fas fa-shopping-cart"></i> -->
                         <i class="fa-solid fa-list-ol"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('TOTAL PLANS') }}</div>
                    <div class="metric-number">{{ number_format($user['plans'] ?? 0) }}</div>
            
                </div>
            </div>

            <div class="metric-card-modern late-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon late-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('TOTAL PLAN Request') }}</div>
                    <div class="metric-number">{{ number_format($user['plans_request'] ?? 0) }}</div>
                   
                </div>
            </div>

            <div class="metric-card-modern leave-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon leave-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('MONTHLY REVENUE') }}</div>
                    <div class="metric-number">
                        {{ !empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '₹' }}{{ number_format($user['monthly_revenue'] ?? 0) }}
                    </div>
                </div>
            </div>

            
        </div>

        <!-- Enhanced Main Dashboard Content -->
        <div class="dashboard-content">
            <!-- First Row -->
            <div class="dashboard-row">
                <div class="dashboard-widget" style="grid-column: span 2;">
                    <div class="widget-header">
                        <h2>{{ __('Revenue Analytics') }}</h2>
                        <div class="widget-actions">
                            <span class="small-text">{{ __('Monthly revenue trends and growth patterns') }}</span>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container">
                            <div id="revenue-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Plan Distribution') }}</h2>
                        <div class="widget-actions">
                            <span class="small-text">{{ __('Most Purchased Plans') }}</span>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container">
                            <div id="users-chart"></div>
                        </div>
                        <div class="mini-stats">
                            <div class="mini-stat">
                                <div class="mini-stat-value">
                                    {{ $user['plans'] ?? 0 }}
                                </div>
                                <div class="mini-stat-label">Total Plans</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value">{{ $user['plans_request'] ?? 0 }}</div>
                                <div class="mini-stat-label">Plan Requests</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="dashboard-row second-row" style=" grid-template-columns: repeat(2, 1fr) !important;">
                <!-- System Health Widget -->
                <div class="dashboard-widget system-health-widget">
                    <div class="widget-header">
                        <h2>{{ __('System Health') }}</h2>
                        <div class="widget-actions">
                            <div class="status-indicator {{ ($systemHealth['uptime_stats']['server_status'] ?? 'unknown') === 'online' ? 'online' : 'offline' }}"></div>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- System Metrics Grid -->
                        <div class="system-metrics-grid">
                            <div class="system-metric-card uptime-card">
                                <div class="metric-icon-container">
                                    <div class="metric-icon uptime-icon">
                                        <i class="fas fa-server"></i>
                                    </div>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value">{{ number_format($systemHealth['uptime_stats']['uptime_percentage'] ?? 0, 2) }}%</div>
                                    <div class="metric-label">UPTIME</div>
                                </div>
                            </div>

                            <div class="system-metric-card tickets-card">
                                <div class="metric-icon-container">
                                    <div class="metric-icon ticket-icon">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value">{{ $systemHealth['ticket_stats']['open'] ?? 0 }}/{{ $systemHealth['ticket_stats']['total'] ?? 0 }}</div>
                                    <div class="metric-label">OPEN TICKETS</div>
                                </div>
                            </div>

                            <div class="system-metric-card api-card">
                                <div class="metric-icon-container">
                                    <div class="metric-icon api-icon">
                                        <i class="fas fa-code"></i>
                                    </div>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value">{{ $systemHealth['api_stats']['avg_response_time'] ?? '0ms' }}</div>
                                    <div class="metric-label">API RESPONSE</div>
                                </div>
                            </div>

                            <div class="system-metric-card activity-card">
                                <div class="metric-icon-container">
                                    <div class="metric-icon users-icon">
                                        <i class="fas fa-users-cog"></i>
                                    </div>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value">{{ $systemHealth['db_stats']['user_activity_rate'] ?? 0 }}%</div>
                                    <div class="metric-label">USER ACTIVITY</div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Support Tickets -->
                        <!-- <div class="recent-tickets-section">
                            <h4 class="section-title">Recent Support Tickets</h4>
                            <div class="tickets-list">
                                @forelse($systemHealth['recent_tickets'] ?? [] as $ticket)
                                    <div class="ticket-item">
                                        <div class="ticket-status-dot {{ $ticket['status'] ?? 'pending' }}"></div>
                                        <div class="ticket-content">
                                            <div class="ticket-title">{{ Str::limit($ticket['title'] ?? 'No Title', 40) }}</div>
                                            <div class="ticket-meta">
                                                <span class="ticket-user">{{ $ticket['user_name'] ?? 'Unknown' }}</span>
                                                <span class="ticket-time">{{ $ticket['formatted_time'] ?? 'Unknown' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="ticket-item empty-state">
                                        <div class="ticket-content">
                                            <div class="ticket-title">No recent tickets</div>
                                            <div class="ticket-meta">
                                                <span class="ticket-user">System is running smoothly</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div> -->
                    </div>
                </div>

                  <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Recent Plan Requests') }}</h2>
                        <div class="widget-actions">
                            <button class="btn-icon" data-bs-toggle="tooltip" title="{{ __('View All Plan Requests') }}">
                                <i class="fas fa-external-link-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="plan-requests-table">
                            @forelse($recentPlanRequests ?? [] as $index => $request)
                                <div class="plan-request-item">
                                    <div class="request-icon">
                                        <i class="{{ $request['icon'] ?? 'fas fa-shopping-cart' }}"></i>
                                    </div>
                                    <div class="request-details">
                                        <div class="request-header">
                                            <div class="user-info">
                                                <span class="user-name">{{ $request['user_name'] ?? 'Unknown User' }}</span>
                                                <span class="user-email">{{ $request['user_email'] ?? 'N/A' }}</span>
                                            </div>
                                            <div class="request-time">{{ $request['formatted_time'] ?? 'Unknown' }}</div>
                                        </div>
                                        <div class="request-content">
                                            <div class="plan-info">
                                                <span class="plan-name">{{ $request['plan_name'] ?? 'Unknown Plan' }}</span>
                                                <span class="plan-price">
                                                    {{ !empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '?' }}{{ number_format($request['plan_price'] ?? 0, 2) }}
                                                </span>
                                                @if(!empty($request['duration']) && $request['duration'] !== 'N/A')
                                                    <span class="plan-duration">{{ ucfirst($request['duration']) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($index === 0)
                                        <div class="pulse-dot"></div>
                                    @endif
                                </div>
                            @empty
                                <div class="plan-request-item empty-state">
                                    <div class="request-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <div class="request-details">
                                        <div class="request-content">
                                            <div class="empty-message">{{ __('No recent plan requests') }}</div>
                                            <div class="empty-submessage">{{ __('New plan requests will appear here') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- ApexCharts JavaScript with fallback -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js" 
            onerror="this.onerror=null; this.src='https://unpkg.com/apexcharts@latest/dist/apexcharts.min.js'"></script>
    <script>
        // Ensure ApexCharts is loaded before proceeding
        function waitForApexCharts(callback, maxAttempts = 10) {
            let attempts = 0;
            const checkInterval = setInterval(() => {
                attempts++;
                if (typeof ApexCharts !== 'undefined') {
                    clearInterval(checkInterval);
                    console.log('ApexCharts loaded successfully');
                    callback();
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    console.error('ApexCharts failed to load after', maxAttempts, 'attempts');
                    // Show fallback message
                    document.querySelectorAll('.chart-container').forEach(container => {
                        if (container.children.length === 0) {
                            container.innerHTML = `
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #ef4444; font-size: 14px;">
                                    <div style="text-align: center;">
                                        <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 16px;"></i>
                                        <div>Chart library failed to load</div>
                                        <div style="font-size: 12px; margin-top: 8px;">Please check your internet connection and refresh</div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                }
            }, 200);
        }
    </script>
    <script>
        // Wait for complete page load including CSS and images
        window.addEventListener('load', function() {
            console.log('Page loaded, waiting for ApexCharts...');
            waitForApexCharts(() => {
                // Additional delay to ensure layout calculations are complete
                setTimeout(initializeCharts, 500);
            });
        });

        function initializeCharts() {
            console.log('Initializing charts...');
            
            // Check if ApexCharts is available
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts is not loaded');
                return;
            }

            // Initialize animations first to ensure layout is ready
            initializeAnimations();
            
            // Small delay to ensure layout is settled
            setTimeout(() => {
                // Check if containers exist and have proper dimensions
                if (!isContainerReady('#revenue-chart')) {
                    console.warn('Revenue chart container not ready, retrying...');
                    setTimeout(() => initializeRevenueChart(), 200);
                } else {
                    initializeRevenueChart();
                }
                
                if (!isContainerReady('#users-chart')) {
                    console.warn('Users chart container not ready, retrying...');
                    setTimeout(() => initializeUsersChart(), 200);
                } else {
                    initializeUsersChart();
                }
                
                if (!isContainerReady('#orders-trend')) {
                    console.warn('Orders chart container not ready, retrying...');
                    setTimeout(() => initializeOrdersChart(), 200);
                } else {
                    initializeOrdersChart();
                }
                

            }, 100);
        }

        function isContainerReady(selector) {
            const container = document.querySelector(selector);
            if (!container) {
                console.warn(`Container ${selector} not found`);
                return false;
            }

            const rect = container.getBoundingClientRect();
            const isReady = rect.width > 0 && rect.height > 0;
            
            if (!isReady) {
                console.warn(`Container ${selector} dimensions: ${rect.width}x${rect.height}`);
            } else {
                console.log(`Container ${selector} is ready: ${rect.width}x${rect.height}`);
            }
            
            return isReady;
        }

        function initializeRevenueChart() {
            try {
                console.log('Initializing revenue chart...');
                
                // Prepare chart data with fallbacks
                const chartData = {!! json_encode($chartData['data'] ?? []) !!};
                const chartLabels = {!! json_encode($chartData['label'] ?? []) !!};
                console.log('Chart Data:', chartData);
                console.log('Chart Labels:', chartLabels);
                
                // Use fallback data if empty or all zeros
                const hasValidData = chartData.length > 0 && chartData.some(value => value > 0);
                const revenueData = hasValidData ? chartData : [100, 200, 150, 300, 250, 400, 350, 500, 300, 450, 600, 400, 350, 200];
                const revenueLabels = chartLabels.length > 0 ? chartLabels : ['13-Sep', '14-Sep', '15-Sep', '16-Sep', '17-Sep', '18-Sep', '19-Sep', '20-Sep', '21-Sep', '22-Sep', '23-Sep', '24-Sep', '25-Sep', '26-Sep'];
                
                console.log('Revenue data:', revenueData);
                console.log('Revenue labels:', revenueLabels);
                
                var revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), {
                    chart: {
                        type: 'area',
                        height: 420,
                        width: '100%',
                        toolbar: {
                            show: true
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        },
                        fontFamily: 'inherit',
                        redrawOnParentResize: true,
                        redrawOnWindowResize: true
                    },
                    series: [{
                        name: 'Revenue',
                        data: revenueData
                    }],
                    xaxis: {
                        categories: revenueLabels,
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '12px',
                                fontWeight: 500
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '12px'
                            },
                            formatter: function(value) {
                                return '₹' + value.toFixed(0);
                            }
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3,
                        lineCap: 'round'
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.3,
                            gradientToColors: ['#60a5fa'],
                            inverseColors: false,
                            opacityFrom: 0.8,
                            opacityTo: 0.1,
                            stops: [0, 100]
                        }
                    },
                    colors: ['#2563eb'],
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 3
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '12px',
                        fontWeight: 500,
                        markers: {
                            width: 8,
                            height: 8,
                            radius: 2
                        }
                    },
                    plotOptions: {
                        area: {
                            fillTo: 'origin',
                            gradientToColors: ['#60a5fa']
                        }
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    },
                    zoom: {
                        enabled: true,
                        type: 'x',
                        autoScaleYaxis: true
                    },
                    dataLabels: {
                        enabled: false
                    },
                    markers: {
                        size: 5,
                        colors: ['#2563eb'],
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                            sizeOffset: 3
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    }

                });
                
                revenueChart.render().then(() => {
                    console.log('Revenue chart rendered successfully');
                    
                        //  document.querySelector("#revenue-chart").innerHTML = ""; // Clear fallback if any
                        //  revenueChart.render();
                }).catch((error) => {
                    console.error('Error rendering revenue chart:', error);
                });
                
            } catch (error) {
                console.error('Error initializing revenue chart:', error);
            }
        }

        function initializeUsersChart() {
            try {
                console.log('Initializing plan distribution chart...');
                
                // Get plan distribution data from PHP
                const planDistribution = {!! json_encode($user['plan_distribution'] ?? []) !!};
                console.log('Plan distribution data:', planDistribution);
                
                // Use purchased plans data for the chart
                const chartLabels = planDistribution.purchased_plans?.labels || ['No Data'];
                const chartData = planDistribution.purchased_plans?.data || [1];
                const chartColors = planDistribution.purchased_plans?.colors || ['#e5e7eb'];
                
                console.log('Chart data - Labels:', chartLabels, 'Data:', chartData);
                
                var usersChart = new ApexCharts(document.querySelector("#users-chart"), {
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '100%',
                        fontFamily: 'inherit'
                    },
                    series: chartData,
                    labels: chartLabels,
                    colors: chartColors,
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '14px',
                                        fontWeight: 600,
                                        color: '#6b7280',
                                        offsetY: -10
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '18px',
                                        fontWeight: 700,
                                        color: '#2d3748',
                                        offsetY: 10,
                                        formatter: function(val) {
                                            return val;
                                        }
                                    },
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        label: 'Total Purchases',
                                        fontSize: '14px',
                                        color: '#6b7280',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => {
                                                return a + b;
                                            }, 0);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px',
                        fontWeight: 500,
                        itemMargin: {
                            horizontal: 8,
                            vertical: 5
                        },
                        markers: {
                            width: 8,
                            height: 8,
                            radius: 2
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 0
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + " purchases";
                            }
                        }
                    }
                });
                
                usersChart.render().then(() => {
                    console.log('Plan distribution chart rendered successfully');
                }).catch((error) => {
                    console.error('Error rendering plan distribution chart:', error);
                });
                
            } catch (error) {
                console.error('Error initializing plan distribution chart:', error);
            }
        }



        function initializeOrdersChart() {
            try {
                console.log('Initializing orders chart...');
                
                // Prepare orders data with fallbacks
                const chartData = {!! json_encode($chartData['data'] ?? []) !!};
                const ordersData = chartData.length > 0 ? 
                    chartData.slice(0, 7) : 
                    [12, 19, 15, 27, 22, 35, 28];
                
                // Ensure we have exactly 7 data points
                while (ordersData.length < 7) {
                    ordersData.push(0);
                }
                
                console.log('Orders data:', ordersData);
                
                var ordersChart = new ApexCharts(document.querySelector("#orders-trend"), {
                    chart: {
                        type: 'line',
                        height: '100%',
                        width: '100%',
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit'
                    },
                    series: [{
                        name: 'Orders',
                        data: ordersData
                    }],
                    xaxis: {
                        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '12px'
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '12px'
                            },
                            formatter: function(value) {
                                return value.toFixed(0);
                            }
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 4,
                        lineCap: 'round'
                    },
                    colors: ['#ef4444'],
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(value) {
                                return value + ' orders';
                            }
                        }
                    },
                    markers: {
                        size: 6,
                        colors: ['#ef4444'],
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        hover: {
                            size: 8
                        }
                    }
                });
                
                ordersChart.render().then(() => {
                    console.log('Orders chart rendered successfully');
                }).catch((error) => {
                    console.error('Error rendering orders chart:', error);
                });
                
            } catch (error) {
                console.error('Error initializing orders chart:', error);
            }
        }

        function initializeAnimations() {
            console.log('Initializing animations and layout...');
            
            // Force layout recalculation
            const dashboard = document.querySelector('.modern-dashboard');
            if (dashboard) {
                dashboard.style.display = 'none';
                dashboard.offsetHeight; // Trigger reflow
                dashboard.style.display = 'block';
            }
            
            // Ensure grid layout is applied with debugging
            const metricsGrid = document.querySelector('.metrics-grid');
            if (metricsGrid) {
                metricsGrid.style.display = 'grid';
                console.log('Metrics grid found and set to grid display');
                console.log('Grid computed style:', window.getComputedStyle(metricsGrid).display);
                console.log('Grid template columns:', window.getComputedStyle(metricsGrid).gridTemplateColumns);
            } else {
                console.warn('Metrics grid not found');
            }
            
            const dashboardRows = document.querySelectorAll('.dashboard-row');
            dashboardRows.forEach((row, index) => {
                row.style.display = 'grid';
                console.log(`Dashboard row ${index} set to grid display`);
            });
            
            // Add fallback content for chart containers if charts fail to load
            setTimeout(() => {
                checkChartContainers();
            }, 2000);

            // Animation for cards
            const cards = document.querySelectorAll('.metric-card-modern');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('slideInUp');
            });

            // Chart resize handling
            let resizeTimeout;

            function handleChartResize() {
                // Force layout recalculation on resize
                const dashboard = document.querySelector('.modern-dashboard');
                if (dashboard) {
                    dashboard.style.display = 'none';
                    dashboard.offsetHeight; // Trigger reflow
                    dashboard.style.display = 'block';
                }
                console.log('Window resized - charts will auto-resize');
            }

            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(handleChartResize, 150);
            });

            // Filter functionality
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }
        
        function checkChartContainers() {
            const chartContainers = ['#revenue-chart', '#users-chart', '#orders-trend'];
            
            chartContainers.forEach(selector => {
                const container = document.querySelector(selector);
                if (container && container.children.length === 0) {
                    console.warn(`Chart container ${selector} is empty, adding fallback content`);
                    container.innerHTML = `
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6b7280; font-size: 14px;">
                            <div style="text-align: center;">
                                <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                                <div>Chart loading...</div>
                                <div style="font-size: 12px; margin-top: 8px;">Please refresh if chart doesn't appear</div>
                            </div>
                        </div>
                    `;
                }
            });
        }
        
        // Retry mechanism for failed charts
        function retryChartInitialization() {
            console.log('Retrying chart initialization...');
            setTimeout(() => {
                if (typeof ApexCharts !== 'undefined') {
                    initializeCharts();
                } else {
                    console.error('ApexCharts still not available after retry');
                }
            }, 1000);
        }
        
        // Global error handler for chart issues
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('ApexCharts')) {
                console.error('ApexCharts error detected:', e.message);
                retryChartInitialization();
            }
        });
    </script>
@endsection


    


