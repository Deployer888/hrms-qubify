@extends('layouts.admin')
@section('page-title')
    {{__('Orders')}}
@endsection

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
                    <table class="premium-table" id="ordersTable">
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