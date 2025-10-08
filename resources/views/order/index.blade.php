@extends('layouts.admin')
@section('page-title')
    {{__('Orders')}}
@endsection
@push('css-page')
 <link rel="stylesheet" href="{{ asset('css/superAdmin/order.css') }}">
 <style>
    /* Enhanced Responsive Table Styles */
    .table-responsive-wrapper {
        overflow-x: auto !important;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        width: 100%;
        position: relative;
    }
    
    .table-responsive {
        min-height: 400px;
        overflow: visible !important;
        width: auto !important;
    }
    
    #ordersTable {
        min-width: 1200px !important;
        margin-bottom: 0;
        white-space: nowrap;
        width: auto !important;
        table-layout: fixed;
    }
    
    #ordersTable th,
    #ordersTable td {
        padding: 12px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    /* Column specific widths */
    /* #ordersTable th:nth-child(1), #ordersTable td:nth-child(1) { min-width: 140px; }
    #ordersTable th:nth-child(2), #ordersTable td:nth-child(2) { min-width: 120px; }
    #ordersTable th:nth-child(3), #ordersTable td:nth-child(3) { min-width: 120px; }
    #ordersTable th:nth-child(4), #ordersTable td:nth-child(4) { min-width: 100px; }
    #ordersTable th:nth-child(5), #ordersTable td:nth-child(5) { min-width: 110px; }
    #ordersTable th:nth-child(6), #ordersTable td:nth-child(6) { min-width: 120px; } 
    #ordersTable th:nth-child(7), #ordersTable td:nth-child(7) { min-width: 100px; }
    #ordersTable th:nth-child(8), #ordersTable td:nth-child(8) { min-width: 100px; } 
    #ordersTable th:nth-child(9), #ordersTable td:nth-child(9) { min-width: 150px; }  */
    
    /* Text wrapping for specific columns */
    .order-id {
        font-family: monospace;
        font-size: 0.85rem;
        word-break: break-all;
    }
    
    .user-name {
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .plan-name {
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .special-note {
        font-size: 0.75rem;
        color: #6c757d;
        font-style: italic;
        line-height: 1.3;
        white-space: normal;
        max-width: 140px;
        padding: 4px 6px;
        background: #f8f9fa;
        border-radius: 4px;
        border-left: 3px solid #6c757d;
    }
    
    .invoice-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        background: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.8rem;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .invoice-link:hover {
        background: #0056b3;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .status-badge.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-badge.danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .status-icon {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    
    .status-icon.success {
        background: #28a745;
    }
    
    .status-icon.danger {
        background: #dc3545;
    }
    
    .coupon-code {
        background: #fff3cd;
        color: #856404;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .payment-type {
        background: #e2e3e5;
        color: #495057;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8rem;
        text-transform: capitalize;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 5px;
        }
        
        .premium-card-body {
            padding: 5px;
            margin: 0 -5px;
        }
        
        .table-responsive-wrapper {
            margin: 0 -5px;
            border-radius: 0;
            box-shadow: none;
            border: 1px solid #dee2e6;
            overflow-x: scroll !important;
            overflow-y: visible !important;
            -webkit-overflow-scrolling: touch;
            width: calc(100vw - 10px);
            max-width: none;
        }
        
        .table-responsive {
            overflow: visible !important;
            width: auto !important;
            min-width: 1000px !important;
        }
        
        #ordersTable {
            min-width: 1000px !important;
            width: 1000px !important;
            table-layout: fixed;
        }
        
        #ordersTable th,
        #ordersTable td {
            padding: 6px 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Fixed column widths for mobile to ensure all columns are visible */
        #ordersTable th:nth-child(1), #ordersTable td:nth-child(1) { width: 170px; min-width: 170px; max-width: 170px; }
        /* #ordersTable th:nth-child(2), #ordersTable td:nth-child(2) { width: 100px; min-width: 100px; max-width: 100px; }
        #ordersTable th:nth-child(3), #ordersTable td:nth-child(3) { width: 100px; min-width: 100px; max-width: 100px; }*/
        #ordersTable th:nth-child(4), #ordersTable td:nth-child(4) { width: 118px; min-width: 118px; max-width: 118px; }
      /*  #ordersTable th:nth-child(5), #ordersTable td:nth-child(5) { width: 100px; min-width: 100px; max-width: 100px; }
        #ordersTable th:nth-child(6), #ordersTable td:nth-child(6) { width: 110px; min-width: 110px; max-width: 110px; }
        #ordersTable th:nth-child(7), #ordersTable td:nth-child(7) { width: 80px; min-width: 80px; max-width: 80px; }
        #ordersTable th:nth-child(8), #ordersTable td:nth-child(8) { width: 90px; min-width: 90px; max-width: 90px; }
        #ordersTable th:nth-child(9), #ordersTable td:nth-child(9) { width: 140px; min-width: 140px; max-width: 140px; } */
        
        .header-stats {
            flex-direction: column;
            gap: 10px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .page-header-premium {
            padding: 10px;
        }
        
        .header-text h1 {
            font-size: 1.3rem;
        }
        
        .header-text p {
            font-size: 0.85rem;
        }
        
        /* Mobile specific text adjustments */
        .order-id {
            font-size: 0.7rem;
            word-break: break-all;
        }
        
        .user-name {
            font-size: 0.75rem;
        }
        
        .plan-name {
            font-size: 0.75rem;
        }
        
        .status-badge {
            font-size: 0.7rem;
            padding: 2px 4px;
        }
        
        .invoice-link {
            font-size: 0.7rem;
            padding: 2px 4px;
        }
        
        .special-note {
            font-size: 0.65rem;
            padding: 2px 4px;
        }
        
        .coupon-code {
            font-size: 0.7rem;
            padding: 1px 3px;
        }
        
        .payment-type {
            font-size: 0.7rem;
            padding: 1px 3px;
        }
    }
    
    /* Tablet responsive adjustments */
    @media (min-width: 769px) and (max-width: 1024px) {
        #ordersTable {
            min-width: 1000px;
        }
        
        #ordersTable th,
        #ordersTable td {
            padding: 10px 6px;
            font-size: 0.9rem;
        }
        
        .premium-card-body {
            padding: 15px;
        }
    }
    
    /* Large screen adjustments */
    @media (min-width: 1200px) {
        #ordersTable th,
        #ordersTable td {
            padding: 15px 10px;
        }
        
        .table-responsive-wrapper {
            border-radius: 12px;
        }
    }
    
    /* DataTables responsive overrides */
    .dataTables_wrapper {
        padding: 15px;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin: 10px 0;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 8px;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 200px;
        transition: all 0.3s ease;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        outline: none;
    }
    
    .dataTables_wrapper .dataTables_length select {
        padding: 6px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin: 0 8px;
        background: white;
        transition: all 0.3s ease;
    }
    
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        outline: none;
    }
    
    /* Mobile DataTables adjustments */
    @media (max-width: 768px) {
        .dataTables_wrapper {
            padding: 5px;
            overflow-x: visible !important;
        }
        
        .dataTables_wrapper .dataTables_filter {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            width: 120px;
            padding: 4px 6px;
            font-size: 0.8rem;
        }
        
        .dataTables_wrapper .dataTables_length {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .dataTables_wrapper .dataTables_length select {
            padding: 3px 6px;
            font-size: 0.8rem;
        }
        
        .dataTables_wrapper .dataTables_info {
            font-size: 0.75rem;
            text-align: center;
            margin-top: 10px;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.75rem;
            text-align: center;
            margin-top: 10px;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 3px 6px;
            margin: 0 1px;
            font-size: 0.75rem;
        }
        
        /* Ensure table wrapper doesn't interfere with scrolling */
        .dataTables_scrollHead,
        .dataTables_scrollBody,
        .dataTables_scrollFoot {
            overflow-x: visible !important;
        }
        
        .dataTables_scroll {
            overflow-x: visible !important;
        }
    }
    
    /* Scrollbar styling */
    .table-responsive-wrapper::-webkit-scrollbar {
        height: 10px;
    }
    
    .table-responsive-wrapper::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 6px;
        margin: 0 10px;
    }
    
    .table-responsive-wrapper::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border-radius: 6px;
        border: 2px solid #f8f9fa;
    }
    
    .table-responsive-wrapper::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
    }
    
    /* Scroll indicator for mobile */
    .table-responsive-wrapper::before {
        content: '';
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid #007bff;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 100;
        pointer-events: none;
    }
    
    @media (max-width: 768px) {
        .table-responsive-wrapper.has-scroll::before {
            opacity: 0.8;
            animation: pulse-arrow 2s infinite;
        }
        
        /* Add a mobile scroll indicator at the bottom */
        .table-responsive-wrapper::after {
            content: '← Scroll to see all columns →';
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 123, 255, 0.9);
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 0.75rem;
            z-index: 1000;
            display: block;
            border-radius: 0 0 8px 8px;
        }
        
        .table-responsive-wrapper.scrolled-right::after {
            content: '← Scroll back to see first columns';
        }
    }
    
    @keyframes pulse-arrow {
        0%, 100% { opacity: 0.7; transform: translateY(-50%) translateX(0); }
        50% { opacity: 1; transform: translateY(-50%) translateX(-3px); }
    }
    
    /* Row hover effects */
    #ordersTable tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .row-selected {
        background-color: #e3f2fd !important;
    }
    
    /* Ripple animation */
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes pulse-animation {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
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
                    <p class="stat-number revenue-counter" id="totalRevenue">{{ (\App\Models\Utility::getValByName('site_currency_symbol') ?: '$') . number_format($orders->where('payment_status', 'succeeded')->sum('price'), 2) }}</p>
                    <p class="stat-label">{{ __('Total Revenue') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Card Container - uses styles from custom.css -->
    <div class="premium-card-body">
        @if($orders->count() > 0)
            <div class="premium-table-container">
                <div class="table-responsive-wrapper">
                    <div class="table-responsive">
                        <table class="table premium-table" id="ordersTable">
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
        "responsive": false, // Disable DataTables responsive to use custom solution
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "order": [[5, 'desc']], // Sort by date column (newest first)
        "processing": true,
        "autoWidth": false,
        "destroy": true, // Allow reinitialization
        "scrollX": false, // Disable DataTables scrollX to use custom wrapper
        "scrollCollapse": false,
        
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
            adjustTableResponsiveness();
        },
        
        // Callback after table is drawn/redrawn
        "drawCallback": function(settings) {
            initializeEnhancements();
            adjustTableResponsiveness();
        }
    });

    // Function to initialize all enhancements
    function initializeEnhancements() {
        initializeHoverEffects();
        initializeRippleEffects();
        initializeStatusBadges();
    }

    // Function to adjust table responsiveness
    function adjustTableResponsiveness() {
        // Force proper table dimensions
        const tableWrapper = $('.table-responsive-wrapper');
        const table = $('#ordersTable');
        const isMobile = $(window).width() <= 768;
        
        if (isMobile) {
            // Force table width and ensure scrolling works
            table.css({
                'min-width': '1000px',
                'width': '1000px',
                'table-layout': 'fixed'
            });
            
            tableWrapper.css({
                'overflow-x': 'auto',
                'overflow-y': 'visible',
                '-webkit-overflow-scrolling': 'touch',
                'width': '100%',
                'max-width': 'none'
            });
            
            // Remove any DataTables scroll wrappers that might interfere
            $('.dataTables_scrollHead, .dataTables_scrollBody, .dataTables_scrollFoot').css({
                'overflow-x': 'visible',
                'width': 'auto'
            });
        }
        
        // Add scroll indicators if content overflows
        if (table.width() > tableWrapper.width()) {
            tableWrapper.addClass('has-scroll');
            
            // Add scroll hint for first-time users
            if (!localStorage.getItem('table-scroll-hint-shown') && isMobile) {
                showScrollHint();
                localStorage.setItem('table-scroll-hint-shown', 'true');
            }
        } else {
            tableWrapper.removeClass('has-scroll');
        }
        
        // Add scroll event listener for better UX
        tableWrapper.off('scroll.responsive').on('scroll.responsive', function() {
            const scrollLeft = $(this).scrollLeft();
            const maxScroll = table.width() - tableWrapper.width();
            
            // Add visual feedback for scroll position
            if (scrollLeft > 0) {
                tableWrapper.addClass('scrolled-left');
            } else {
                tableWrapper.removeClass('scrolled-left');
            }
            
            if (scrollLeft >= maxScroll - 10) {
                tableWrapper.addClass('scrolled-right');
            } else {
                tableWrapper.removeClass('scrolled-right');
            }
        });
        
        // Adjust DataTables controls for mobile
        adjustDataTablesControls();
    }

    // Show scroll hint for mobile users
    function showScrollHint() {
        if ($(window).width() <= 768) {
            const hint = $('<div class="scroll-hint"><i class="fas fa-hand-point-right"></i> Swipe to see more columns</div>');
            hint.css({
                'position': 'absolute',
                'top': '15px',
                'right': '15px',
                'background': 'linear-gradient(45deg, #007bff, #0056b3)',
                'color': 'white',
                'padding': '10px 15px',
                'border-radius': '25px',
                'font-size': '0.8rem',
                'z-index': '1000',
                'box-shadow': '0 4px 12px rgba(0,123,255,0.3)',
                'animation': 'slideInBounce 0.5s ease-out, fadeOutSlide 0.5s ease-in 2.5s forwards'
            });
            
            $('.table-responsive-wrapper').css('position', 'relative').append(hint);
            
            setTimeout(function() {
                hint.remove();
            }, 3000);
        }
    }

    // Adjust DataTables controls for better mobile experience
    function adjustDataTablesControls() {
        const isMobile = $(window).width() <= 768;
        
        if (isMobile) {
            // Stack length and filter controls vertically on mobile
            $('.dataTables_length').css({
                'float': 'none',
                'text-align': 'center',
                'margin-bottom': '10px'
            });
            
            $('.dataTables_filter').css({
                'float': 'none',
                'text-align': 'center',
                'margin-bottom': '10px'
            });
            
            $('.dataTables_info').css({
                'float': 'none',
                'text-align': 'center',
                'margin-top': '10px'
            });
            
            $('.dataTables_paginate').css({
                'float': 'none',
                'text-align': 'center',
                'margin-top': '10px'
            });
        }
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

    // Force responsive behavior on mobile
    function forceMobileResponsive() {
        if ($(window).width() <= 768) {
            // Remove any DataTables scroll containers that might interfere
            $('.dataTables_scroll').each(function() {
                $(this).css({
                    'overflow-x': 'visible',
                    'width': 'auto'
                });
            });
            
            // Ensure our wrapper is the only scroll container
            $('.table-responsive-wrapper').css({
                'overflow-x': 'auto',
                'overflow-y': 'visible',
                '-webkit-overflow-scrolling': 'touch'
            });
            
            // Force table dimensions
            $('#ordersTable').css({
                'min-width': '1000px',
                'width': '1000px'
            });
        }
    }

    // Initialize on page load
    setTimeout(function() {
        initializeEnhancements();
        adjustTableResponsiveness();
        forceMobileResponsive();
    }, 100);

    // Handle window resize
    $(window).on('resize', function() {
        clearTimeout(window.resizeTimer);
        window.resizeTimer = setTimeout(function() {
            adjustTableResponsiveness();
            forceMobileResponsive();
        }, 250);
    });

    // Force responsive check after DataTable is fully loaded
    setTimeout(function() {
        forceMobileResponsive();
        adjustTableResponsiveness();
    }, 500);

    // Add CSS animations and scroll indicators
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            @keyframes slideInBounce {
                0% { opacity: 0; transform: translateX(30px) scale(0.8); }
                60% { opacity: 1; transform: translateX(-5px) scale(1.05); }
                100% { opacity: 1; transform: translateX(0) scale(1); }
            }
            
            @keyframes fadeOutSlide {
                0% { opacity: 1; transform: translateX(0); }
                100% { opacity: 0; transform: translateX(30px); }
            }
            
            .has-scroll::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 30px;
                height: 100%;
                background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
                pointer-events: none;
                z-index: 10;
                transition: opacity 0.3s ease;
            }
            
            .scrolled-right::after {
                opacity: 0;
            }
            
            .has-scroll::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 30px;
                height: 100%;
                background: linear-gradient(to right, rgba(255,255,255,0.9), transparent);
                pointer-events: none;
                z-index: 10;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .scrolled-left::before {
                opacity: 1;
            }
            
            /* Enhanced table hover effects */
            .premium-table tbody tr {
                transition: all 0.2s ease;
            }
            
            .premium-table tbody tr:hover {
                background-color: #f8f9fa !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            
            /* Loading state for table */
            .dataTables_processing {
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                background: rgba(255,255,255,0.9) !important;
                border: none !important;
                border-radius: 8px !important;
                padding: 20px 30px !important;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
                font-weight: 500 !important;
                color: #007bff !important;
            }
        `)
        .appendTo('head');

});

</script>
@endpush