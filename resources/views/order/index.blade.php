@extends('layouts.admin')
@section('page-title')
    {{__('Orders')}}
@endsection
@push('css-page')
<style>
        /* Premium Orders Page Styling - Based on Plans Reference */

    :root {
            --primary-order-order: #2563eb;
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
        background: var(--primary-order-order);
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

    .header-text {
        flex: 1;
    }

    .header-text h1 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 5px;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .header-text p {
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

    /* Premium Card Body */
    .premium-card-body {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(255,255,255,0.8);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .premium-card-body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-order);
        opacity: 1;
    }

    /* Premium Table Container */
    .premium-table-container {
        padding: 0;
        background: transparent;
    }

    .table-responsive {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: none;
    }

    /* Premium Table Styling */
    .premium-table {
        width: 100%;
        margin-bottom: 0;
        background: transparent;
        border-collapse: separate;
        border-spacing: 0;
    }

    .premium-table thead th {
        background: linear-gradient(135deg, var(--primary-order) 0%, var(--secondary) 100%);
        color: white;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 20px 16px;
        border: none;
        text-align: left;
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
    }

    .premium-table thead th:first-child {
        border-top-left-radius: 16px;
    }

    .premium-table thead th:last-child {
        border-top-right-radius: 16px;
    }

    .premium-table tbody tr {
        background: white;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        transition: var(--transition);
        cursor: pointer;
        position: relative;
    }

    .premium-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    }

    .premium-table tbody tr:last-child {
        border-bottom: none;
    }

    .premium-table tbody tr.row-selected {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-left: 4px solid var(--primary-order);
    }

    .premium-table td {
        padding: 18px 16px;
        border: none;
        color: var(--text-primary);
        font-weight: 500;
        vertical-align: middle;
        font-size: 14px;
    }

    /* Order ID Styling */
    .order-id {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 13px;
        font-weight: 600;
        color: var(--primary-order);
        background: rgba(102, 126, 234, 0.1);
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
    }

    /* User Name Styling */
    .user-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
    }

    /* Plan Name Styling */
    .plan-name {
        font-weight: 600;
        color: var(--primary-order);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Price Display */
    .price-display {
        font-weight: 700;
        font-size: 16px;
        color: var(--success);
        background: rgba(16, 185, 129, 0.1);
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: var(--transition);
    }

    .status-badge.success {
        background: linear-gradient(135deg, var(--success), #34d399);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .status-badge.danger {
        background: linear-gradient(135deg, var(--danger), #f87171);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .status-icon {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        transition: var(--transition);
    }

    .status-icon.success {
        background: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 6px rgba(255, 255, 255, 0.5);
    }

    .status-icon.danger {
        background: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 6px rgba(255, 255, 255, 0.5);
    }

    /* Date Display */
    .date-display {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Coupon Code */
    .coupon-code {
        background: linear-gradient(135deg, var(--warning), #fbbf24);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }

    /* Payment Type */
    .payment-type {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        color: var(--text-primary);
        text-transform: capitalize;
        border: 1px solid rgba(102, 126, 234, 0.1);
    }

    /* Invoice Link */
    .invoice-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--primary-order), var(--secondary));
        color: white;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .invoice-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .invoice-link:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .invoice-link:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .invoice-link i {
        font-size: 12px;
    }

    /* Special Note */
    .special-note {
        background: linear-gradient(135deg, rgba(156, 163, 175, 0.1) 0%, rgba(107, 114, 128, 0.1) 100%);
        color: var(--text-secondary);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-style: italic;
        border: 1px solid rgba(156, 163, 175, 0.2);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 40px;
        color: var(--text-secondary);
        background: white;
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
        color: var(--text-secondary);
        max-width: 400px;
        margin: 0 auto;
    }

    /* DataTables Custom Styling */
    .dataTables_wrapper {
        padding: 24px;
    }

    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        color: var(--text-primary);
    }

    .dataTables_length select {
        background: white;
        border: 2px solid rgba(102, 126, 234, 0.1);
        border-radius: 8px;
        padding: 6px 12px;
        color: var(--text-primary);
        font-weight: 500;
    }

    .dataTables_filter input {
        background: white;
        border: 2px solid rgba(102, 126, 234, 0.1);
        border-radius: 8px;
        padding: 8px 16px;
        color: var(--text-primary);
        font-weight: 500;
        margin-left: 8px;
    }

    .dataTables_filter input:focus {
        outline: none;
        border-color: var(--primary-order);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .dataTables_paginate .paginate_button {
        background: white;
        border: 1px solid rgba(102, 126, 234, 0.1);
        color: var(--text-primary);
        padding: 8px 16px;
        margin: 0 2px;
        border-radius: 8px;
        font-weight: 500;
        transition: var(--transition);
    }

    .dataTables_paginate .paginate_button:hover {
        background: var(--primary-order);
        color: white;
        border-color: var(--primary-order);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--primary-order);
        color: white;
        border-color: var(--primary-order);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .dataTables_paginate .paginate_button.disabled {
        background: #f3f4f6;
        color: #9ca3af;
        border-color: #e5e7eb;
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

    /* Pulse Animation */
    @keyframes pulse-animation {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    /* Ripple Effect */
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .header-stats {
            gap: 20px;
        }
        
        .premium-table td,
        .premium-table th {
            padding: 14px 12px;
            font-size: 13px;
        }
    }

    @media (max-width: 992px) {
        .header-content {
            flex-direction: column;
            gap: 25px;
            text-align: center;
        }

        .header-stats {
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .premium-table-container {
            overflow-x: auto;
        }
        
        .premium-table {
            min-width: 800px;
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

        .header-icon {
            width: 60px;
            height: 60px;
            margin-right: 0;
            margin-bottom: 10px;
        }

        .header-icon i {
            font-size: 28px;
        }

        .header-text h1 {
            font-size: 26px;
            margin-bottom: 8px;
        }

        .header-text p {
            font-size: 14px;
            line-height: 1.4;
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

        .stat-number {
            font-size: 24px;
        }

        .stat-label {
            font-size: 12px;
        }
        
        .dataTables_wrapper {
            padding: 16px;
        }
        
        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 16px;
        }
    }

    @media (max-width: 576px) {
        .page-header-premium {
            padding: 18px;
            margin-bottom: 20px;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            margin-bottom: 8px;
        }

        .header-icon i {
            font-size: 24px;
        }

        .header-text h1 {
            font-size: 22px;
            margin-bottom: 6px;
        }

        .header-text p {
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
        
        .premium-table {
            min-width: 600px;
        }
        
        .premium-table td,
        .premium-table th {
            padding: 12px 8px;
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .page-header-premium {
            padding: 15px;
            border-radius: 12px;
        }

        .header-text h1 {
            font-size: 20px;
        }

        .header-text p {
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
    <!-- Premium Header - uses styles from custom.css -->
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="header-text">
                <h1>{{ __('Plan Order Management') }}</h1>
                <p>{{ __('Monitor and manage all subscription plan orders and payments') }}</p>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number" id="totalOrders">{{ $orders->count() }}</p>
                    <p class="stat-label">{{ __('Total Orders') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number" id="successfulOrders">{{ $orders->where('payment_status', 'succeeded')->count() }}</p>
                    <p class="stat-label">{{ __('Successful') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number revenue-counter" id="totalRevenue">{{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') . number_format($orders->where('payment_status', 'succeeded')->sum('price'), 2) }}</p>
                    <p class="stat-label">{{ __('Total Revenue') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Card Container - uses styles from custom.css -->
    <div class="premium-card-body">
        @if($orders->count() > 0)
            <div class="premium-table-container">
                <div class="table-responsive">
                    <table class="premium-table table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>{{ __('Order ID') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Plan') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Coupon') }}</th>
                                <th>{{ __('Payment') }}</th>
                                <th>{{ __('Invoice') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>
                                    <span class="order-id">{{ $order->order_id }}</span>
                                </td>
                                <td>
                                    <div class="user-name">{{ $order->user_name }}</div>
                                </td>
                                <td>
                                    <span class="plan-name">{{ $order->plan_name }}</span>
                                </td>
                                <td data-order="{{ $order->price }}">
                                    <span class="price-display">
                                        {{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') . number_format($order->price, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->payment_status == 'succeeded')
                                        <span class="status-badge success">
                                            <span class="status-icon success"></span>
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    @else
                                        <span class="status-badge danger">
                                            <span class="status-icon danger"></span>
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    @endif
                                </td>
                                <td data-order="{{ $order->created_at->timestamp }}">
                                    <span class="date-display">{{ $order->created_at->format('M d, Y') }}</span>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">
                                        {{ $order->created_at->format('h:i A') }}
                                    </div>
                                </td>
                                <td>
                                    @if(!empty($order->total_coupon_used) && !empty($order->total_coupon_used->coupon_detail))
                                        <span class="coupon-code">{{ $order->total_coupon_used->coupon_detail->code }}</span>
                                    @else
                                        <span class="text-muted">{{ __('None') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="payment-type">{{ $order->payment_type }}</span>
                                </td>
                                <td>
                                    @if(empty($order->receipt))
                                        <div class="special-note">
                                            {{ __('Manually upgraded by admin') }}
                                        </div>
                                    @elseif($order->receipt == 'free coupon')
                                        <div class="special-note">
                                            {{ __('100% discount coupon used') }}
                                        </div>
                                    @else
                                        <a href="{{ $order->receipt }}" 
                                            title="{{ __('View Invoice') }}" 
                                            target="_blank" 
                                            class="invoice-link">
                                            <i class="fas fa-file-invoice"></i>
                                            {{ __('Invoice') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h3>{{ __('No Orders Found') }}</h3>
                <p>{{ __('There are no plan orders to display at the moment.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
@push('script-page')
<script>
$(document).ready(function() {
    console.log('jQuery version:', $.fn.jquery);
    console.log('DataTables available:', typeof $.fn.DataTable !== 'undefined');
    console.log('DataTable function:', typeof $.fn.dataTable !== 'undefined');

    // Use lowercase 'dataTable' for older versions
    if (typeof $.fn.dataTable === 'undefined' && typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables is not loaded. Please check the CDN links.');
        return;
    }
    // Initialize DataTable with configuration for version 1.10.21
    const table = $('#ordersTable').dataTable({
        "responsive": true,
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "order": [[5, 'desc']], // Sort by date column (newest first)
        "processing": true,
        "autoWidth": false,
        "destroy": true, // Allow reinitialization
        
        // Column definitions for proper sorting
        "columnDefs": [
            {
                "targets": [3], // Price column
                "type": "num",
                "render": function(data, type, row, meta) {
                    if (type === 'display') {
                        return data;
                    }
                    if (type === 'sort' || type === 'type') {
                        // Extract numeric value from the price display
                        var priceText = $(data).text() || data;
                        if (typeof priceText === 'string') {
                            priceText = priceText.replace(/[$,]/g, '');
                        }
                        return parseFloat(priceText) || 0;
                    }
                    return data;
                }
            },
            {
                "targets": [5], // Date column  
                "type": "date",
                "render": function(data, type, row, meta) {
                    if (type === 'display') {
                        return data;
                    }
                    if (type === 'sort' || type === 'type') {
                        // Try to get timestamp from data-order attribute
                        var $cell = $(data);
                        var timestamp = $cell.closest('td').attr('data-order');
                        if (timestamp) {
                            return parseInt(timestamp);
                        }
                        // Fallback to parsing date text
                        var dateText = $cell.text() || data;
                        return new Date(dateText).getTime() || 0;
                    }
                    return data;
                }
            },
            {
                "targets": [8], // Invoice column - disable sorting
                "orderable": false,
                "searchable": false
            }
        ],
        
        // Language customization
        "language": {
            "lengthMenu": "Show _MENU_ orders per page",
            "zeroRecords": "No orders found",
            "info": "Showing _START_ to _END_ of _TOTAL_ orders",
            "infoEmpty": "Showing 0 to 0 of 0 orders",
            "infoFiltered": "(filtered from _MAX_ total orders)",
            "search": "Search:",
            "paginate": {
                "first": "First",
                "last": "Last", 
                "next": "Next",
                "previous": "Previous"
            },
            "processing": "Loading orders..."
        },
        
        // Callback after table is initialized
        "initComplete": function(settings, json) {
            console.log('DataTable initialized successfully');
            initializeEnhancements();
        },
        
        // Callback after table is drawn/redrawn
        "drawCallback": function(settings) {
            initializeEnhancements();
        }
    });

    // Function to initialize all enhancements
    function initializeEnhancements() {
        initializeHoverEffects();
        initializeRippleEffects();
        initializeStatusBadges();
    }

    // Enhanced hover effects for table rows
    function initializeHoverEffects() {
        // Remove existing hover listeners to prevent duplicates
        $('#ordersTable tbody tr').off('mouseenter.custom mouseleave.custom');
        
        $('#ordersTable tbody tr').on('mouseenter.custom', function() {
            $(this).css({
                'transform': 'translateY(-2px)',
                'box-shadow': '0 8px 24px rgba(37, 99, 235, 0.15)',
                'transition': 'all 0.3s ease'
            });
        }).on('mouseleave.custom', function() {
            $(this).css({
                'transform': 'translateY(0)',
                'box-shadow': 'none'
            });
        });
    }

    // Initialize ripple effects
    function initializeRippleEffects() {
        // Remove existing click listeners
        $(document).off('click.ripple', '.invoice-link');
        
        $(document).on('click.ripple', '.invoice-link', function(e) {
            var $this = $(this);
            var pos = $this.offset();
            var relativeX = e.pageX - pos.left;
            var relativeY = e.pageY - pos.top;
            
            // Create ripple element
            var ripple = $('<span class="ripple-effect"></span>');
            ripple.css({
                'position': 'absolute',
                'width': '30px',
                'height': '30px',
                'background': 'rgba(255, 255, 255, 0.6)',
                'border-radius': '50%',
                'left': (relativeX - 15) + 'px',
                'top': (relativeY - 15) + 'px',
                'transform': 'scale(0)',
                'animation': 'ripple-animation 0.6s linear',
                'pointer-events': 'none',
                'z-index': '1000'
            });
            
            if ($this.css('position') === 'static') {
                $this.css('position', 'relative');
            }
            $this.css('overflow', 'hidden').append(ripple);
            
            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    }

    // Initialize status badge animations
    function initializeStatusBadges() {
        $(document).off('mouseenter.status mouseleave.status', '.status-badge');
        
        $(document).on('mouseenter.status', '.status-badge', function() {
            var $icon = $(this).find('.status-icon');
            $icon.css({
                'transform': 'scale(1.2)',
                'transition': 'transform 0.3s ease'
            });
        }).on('mouseleave.status', '.status-badge', function() {
            var $icon = $(this).find('.status-icon');
            $icon.css('transform', 'scale(1)');
        });
    }

    // Row selection for mobile
    $(document).on('click', '#ordersTable tbody tr', function(e) {
        // Don't trigger if clicking on links
        if ($(e.target).closest('a, .invoice-link').length > 0) {
            return;
        }
        
        // Remove selection from other rows
        $(this).siblings().removeClass('row-selected');
        $(this).addClass('row-selected');
        
        // Add pulse animation
        $(this).css('animation', 'pulse-animation 0.3s ease');
        var $row = $(this);
        setTimeout(function() {
            $row.css('animation', '');
        }, 300);
    });

    // Initialize on page load
    setTimeout(function() {
        initializeEnhancements();
    }, 100);


});

</script>
@endpush