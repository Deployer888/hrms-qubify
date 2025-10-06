@extends('layouts.admin')
@section('page-title')
    {{ __('Coupons') }}
@endsection
@push('script-page')
    <script>
        $(document).on('click', '.code', function() {
            var type = $(this).val();
            if (type == 'manual') {
                $('#manual').removeClass('d-none');
                $('#manual').addClass('d-block');
                $('#auto').removeClass('d-block');
                $('#auto').addClass('d-none');
            } else {
                $('#auto').removeClass('d-none');
                $('#auto').addClass('d-block');
                $('#manual').removeClass('d-block');
                $('#manual').addClass('d-none');
            }
        });

        $(document).on('click', '#code-generate', function() {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
@endpush

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
        color: white;
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

    /* Premium Table Container */
    .premium-table-container {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(255,255,255,0.8);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .premium-table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary);
        opacity: 1;
    }

    .premium-table-container::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.03) 0%, transparent 50%);
        pointer-events: none;
        opacity: 1;
    }

    /* Table Header */
    .table-header {
        padding: 32px 32px 24px;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        position: relative;
        z-index: 2;
    }

    .table-title {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .table-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
        font-weight: 500;
    }

    /* Premium Table */
    .table-responsive {
        border-radius: 0 0 var(--border-radius) var(--border-radius);
        overflow: hidden;
        position: relative;
        z-index: 2;
    }

    .premium-table {
        width: 100%;
        margin: 0;
        background: transparent;
    }

    .premium-table thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: none;
        padding: 20px 24px;
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        position: relative;
    }

    .premium-table tbody tr {
        border: none;
        transition: var(--transition);
        position: relative;
    }

    .premium-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
        transform: scale(1.005);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.08);
    }

    .premium-table tbody tr:not(:last-child) {
        border-bottom: 1px solid rgba(102, 126, 234, 0.05);
    }

    .premium-table tbody td {
        padding: 24px;
        vertical-align: middle;
        border: none;
        font-size: 14px;
        color: #374151;
        position: relative;
    }

    /* Coupon Code Badge */
    .coupon-code {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
        color: var(--primary);
        border: 1px solid rgba(37, 99, 235, 0.2);
        border-radius: 20px;
        font-weight: 700;
        font-size: 13px;
        font-family: 'Monaco', 'Menlo', monospace;
        letter-spacing: 1px;
        text-transform: uppercase;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .coupon-code::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .coupon-code:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .premium-table tbody tr:hover .coupon-code {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(59, 130, 246, 0.15) 100%);
        border-color: rgba(37, 99, 235, 0.3);
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
    }

    .coupon-code i {
        font-size: 12px;
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
    }

    /* Discount Badge */
    .discount-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.1) 100%);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 16px;
        font-weight: 700;
        font-size: 14px;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .discount-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .premium-table tbody tr:hover .discount-badge::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .premium-table tbody tr:hover .discount-badge {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(52, 211, 153, 0.15) 100%);
        border-color: rgba(16, 185, 129, 0.3);
        transform: translateY(-2px) scale(1.05);
    }

    /* Usage Stats */
    .usage-stats {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 120px;
    }

    .usage-bar {
        width: 100%;
        height: 8px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-radius: 4px;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .usage-fill {
        height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 4px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .usage-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .usage-text {
        font-size: 12px;
        color: var(--text-secondary);
        text-align: center;
        font-weight: 600;
    }

    /* Coupon Details */
    .coupon-name {
        font-weight: 700;
        color: #1f2937;
        font-size: 16px;
        margin-bottom: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .coupon-desc {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0;
        font-weight: 500;
    }

    /* Action Buttons */
    .action-btns {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .action-btn:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .view-btn {
        background: linear-gradient(135deg, var(--info), #7dd3fc);
        color: white;
        box-shadow: 0 4px 15px rgba(147, 197, 253, 0.3);
    }

    .view-btn:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(147, 197, 253, 0.4);
        color: white;
        text-decoration: none;
    }

    .edit-btn {
        background: linear-gradient(135deg, var(--warning), #fbbf24);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .edit-btn:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        color: white;
        text-decoration: none;
    }

    .delete-btn {
        background: linear-gradient(135deg, var(--danger), #dc2626);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .delete-btn:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        color: white;
        text-decoration: none;
    }

    .action-btn i {
        font-size: 12px;
        transition: var(--transition);
    }

    .action-btn:hover i {
        transform: scale(1.1);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 40px;
        color: #6b7280;
        position: relative;
        z-index: 2;
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
    .loading-row {
        opacity: 0.5;
        pointer-events: none;
        position: relative;
    }

    .loading-row::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #e5e7eb;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 10;
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

    /* Modal Enhancements */
    #commonModal {
        padding: 25px !important;
    }

    .modal-content {
        border: none;
        border-radius: 24px;
        box-shadow: var(--shadow-xl);
        overflow: hidden;
    }

    /* Responsive Design */
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

        .table-header {
            padding: 24px 20px 16px;
        }

        .table-title {
            font-size: 20px;
        }

        .table-subtitle {
            font-size: 13px;
        }

        .premium-table {
            font-size: 13px;
        }

        .premium-table thead {
            display: none;
        }

        .premium-table tbody tr {
            display: block;
            margin-bottom: 16px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .premium-table tbody tr:hover {
            transform: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .premium-table tbody td {
            display: block;
            padding: 8px 0;
            border: none;
            text-align: left;
        }

        .premium-table tbody td::before {
            content: attr(data-label) ": ";
            font-weight: 700;
            color: #374151;
            display: inline-block;
            margin-right: 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .premium-table tbody td.action-btns::before {
            display: none;
        }

        .action-btns {
            justify-content: flex-start;
            margin-top: 12px;
        }

        .usage-stats {
            min-width: auto;
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
            margin-right: 0;
            margin-bottom: 8px;
        }

        .header-icon i {
            font-size: 24px;
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

        .table-header {
            padding: 20px 16px 12px;
        }

        .premium-table tbody tr {
            padding: 16px;
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
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="header-text">
                    <h1>{{ __('Manage Coupons') }}</h1>
                    <p>{{ __('Create and manage discount coupons for your customers') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $coupons->count() }}</p>
                    <p class="stat-label">{{ __('Total Coupons') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">{{ $coupons->where('is_active', 1)->count() }}</p>
                    <p class="stat-label">{{ __('Active') }}</p>
                </div>
                @can('create coupon')
                <div class="stat-item">
                    <a href="#" data-url="{{ route('coupons.create') }}" 
                       data-ajax-popup="true"
                       data-title="{{ __('Create New Coupon') }}" 
                       class="premium-btn">
                        <i class="fa fa-plus"></i> {{ __('Create Coupon') }}
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- Premium Table Card --}}
    <div class="premium-table-container fade-in">
        <div class="table-header">
            <div>
                <h2 class="table-title">{{ __('Coupon List') }}</h2>
                <p class="table-subtitle">{{ __('Manage all your discount coupons') }}</p>
            </div>
        </div>
        
        @if($coupons->count() > 0)
            <div class="table-responsive">
                <table class="premium-table table">
                    <thead>
                        <tr>
                            <th>{{ __('Coupon Details') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Discount') }}</th>
                            <th>{{ __('Usage') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $coupon)
                            <tr class="font-style">
                                <td data-label="{{ __('Coupon Details') }}">
                                    <div class="coupon-name">{{ $coupon->name }}</div>
                                    <div class="coupon-desc">{{ __('Discount Coupon') }}</div>
                                </td>
                                <td data-label="{{ __('Code') }}">
                                    <div class="coupon-code">
                                        <i class="fas fa-tag"></i>
                                        {{ $coupon->code }}
                                    </div>
                                </td>
                                <td data-label="{{ __('Discount') }}">
                                    <div class="discount-badge">
                                        {{ $coupon->discount }}
                                        <i class="fas fa-percent"></i>
                                    </div>
                                </td>
                                <td data-label="{{ __('Usage') }}">
                                    <div class="usage-stats">
                                        @php
                                            $usedCount = $coupon->used_coupon();
                                            $totalLimit = $coupon->limit;
                                            $usagePercent = $totalLimit > 0 ? ($usedCount / $totalLimit) * 100 : 0;
                                        @endphp
                                        <div class="usage-bar">
                                            <div class="usage-fill" style="width: {{ $usagePercent }}%"></div>
                                        </div>
                                        <div class="usage-text">
                                            {{ $usedCount }} / {{ $totalLimit }} {{ __('used') }}
                                        </div>
                                    </div>
                                </td>
                                <td data-label="{{ __('Actions') }}" class="action-btns">
                                    <a href="{{ route('coupons.show', $coupon->id) }}" 
                                       class="action-btn view-btn"
                                       data-toggle="tooltip" 
                                       data-original-title="{{ __('View Details') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit coupon')
                                        <a href="#" 
                                           class="action-btn edit-btn"
                                           data-url="{{ route('coupons.edit', $coupon->id) }}" 
                                           data-ajax-popup="true"
                                           data-title="{{ __('Edit Coupon') }}" 
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('delete coupon')
                                        <a href="#" 
                                           class="action-btn delete-btn" 
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('Delete') }}"
                                           data-confirm="{{ __('Are You Sure?') . '|' . __('This action cannot be undone. Do you want to continue?') }}"
                                           data-confirm-yes="document.getElementById('delete-form-{{ $coupon->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form action="{{ route('coupons.destroy', $coupon->id) }}" 
                                              method="POST"
                                              id="delete-form-{{ $coupon->id }}"
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <h3>{{ __('No Coupons Found') }}</h3>
                <p>{{ __('Start by creating your first discount coupon to attract customers.') }}</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Enhanced confirmation dialog
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-confirm]')) {
            e.preventDefault();
            const confirmBtn = e.target.closest('[data-confirm]');
            const confirmData = confirmBtn.dataset.confirm.split('|');
            const title = confirmData[0];
            const message = confirmData[1];
            const confirmYes = confirmBtn.dataset.confirmYes;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                        // Execute the action
                        eval(confirmYes);
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm(title + '\n' + message)) {
                    // Add loading state to row
                    // Execute the action
                    eval(confirmYes);
                }
            }
        }
    });

    // Enhanced usage bar animations
    const usageBars = document.querySelectorAll('.usage-fill');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            }
        });
    });

    usageBars.forEach(bar => {
        observer.observe(bar);
    });

    // Copy coupon code functionality
    document.querySelectorAll('.coupon-code').forEach(codeElement => {
        codeElement.addEventListener('click', function() {
            const code = this.textContent.trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(() => {
                    // Show success message
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    this.style.background = 'linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1))';
                    this.style.color = 'var(--success)';
                    
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.style.background = '';
                        this.style.color = '';
                    }, 2000);
                });
            }
        });
        
        // Add cursor pointer to indicate clickable
        codeElement.style.cursor = 'pointer';
        codeElement.setAttribute('title', 'Click to copy');
    });
});
</script>
@endsection