@extends('layouts.admin')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@push('css-page')
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
@endpush

@push('script-page')
    <script>
        // Wait for complete page load including CSS and images
        window.addEventListener('load', function() {
            // Additional delay to ensure layout calculations are complete
            setTimeout(initializeCharts, 200);
        });

        function initializeCharts() {
            // Check if containers exist and have proper dimensions
            if (!isContainerReady('#revenue-chart')) {
                console.warn('Revenue chart container not ready');
                return;
            }
            if (!isContainerReady('#users-chart')) {
                console.warn('Users chart container not ready');
                return;
            }
            if (!isContainerReady('#orders-trend')) {
                console.warn('Orders chart container not ready');
                return;
            }

            try {
                initializeRevenueChart();
                initializeUsersChart();
                initializeOrdersChart();
                initializeAnimations();
            } catch (error) {
                console.error('Error initializing charts:', error);
            }
        }

        function isContainerReady(selector) {
            const container = document.querySelector(selector);
            if (!container) return false;

            const rect = container.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0;
        }

        function initializeRevenueChart() {
            try {
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
                        data: {!! json_encode($chartData['data']) !!}
                    }],
                    xaxis: {
                        categories: {!! json_encode($chartData['label']) !!},
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
                                return '$' + value.toFixed(0);
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
                                return '$' + value.toLocaleString();
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
                                return '$' + value.toLocaleString();
                            }
                        }
                    }

                });
                revenueChart.render();
            } catch (error) {
                console.error('Error initializing revenue chart:', error);
            }
        }

        function initializeUsersChart() {
            try {
                // Fixed Users Distribution Chart with Correct Colors
                var usersChart = new ApexCharts(document.querySelector("#users-chart"), {
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '100%',
                        fontFamily: 'inherit'
                    },
                    series: [{{ $user['total_paid_user'] }},
                        {{ $user->total_user - $user['total_paid_user'] }}
                    ],
                    labels: ['Paid Users', 'Free Users'],
                    colors: ['#2563eb', '#b9b9b8'], // Fixed: Blue for paid, Light gray for free
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
                                        label: 'Total Users',
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
                                return val + " users";
                            }
                        }
                    }
                });
                usersChart.render();
            } catch (error) {
                console.error('Error initializing users chart:', error);
            }
        }

        function initializeOrdersChart() {
            try {
                // Orders Trend Chart
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
                        data: [
                            {{ implode(',', array_slice(array_merge($chartData['data'], [0, 0, 0, 0, 0, 0, 0]), 0, 7)) }}
                        ]
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
                ordersChart.render();
            } catch (error) {
                console.error('Error initializing orders chart:', error);
            }
        }

        function initializeAnimations() {
            // Animation for cards
            const cards = document.querySelectorAll('.metric-card-modern');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('slideInUp');
            });

            // Chart resize handling
            let resizeTimeout;

            function handleChartResize() {
                // Note: Chart variables are scoped to their functions, 
                // so we'll need to handle resize differently
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
    </script>
@endpush

@section('content')
    <div class="modern-dashboard">
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
                    <div class="metric-label">{{ __('TOTAL USERS') }}</div>
                    <div class="metric-number">{{ number_format($user->total_user) }}</div>
                    <div class="metric-growth {{ $dashboardMetrics['users_growth']['trend'] }}">
                        <i class="fas fa-arrow-{{ $dashboardMetrics['users_growth']['trend'] == 'positive' ? 'up' : ($dashboardMetrics['users_growth']['trend'] == 'negative' ? 'down' : 'right') }}"></i>
                        <span>{{ $dashboardMetrics['users_growth']['display'] }}</span>
                    </div>
                </div>
            </div>

            <div class="metric-card-modern absent-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon absent-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('TOTAL ORDERS') }}</div>
                    <div class="metric-number">{{ number_format($user->total_orders) }}</div>
                    <div class="metric-growth {{ $dashboardMetrics['orders_growth']['trend'] }}">
                        <i class="fas fa-arrow-{{ $dashboardMetrics['orders_growth']['trend'] == 'positive' ? 'up' : ($dashboardMetrics['orders_growth']['trend'] == 'negative' ? 'down' : 'right') }}"></i>
                        <span>{{ $dashboardMetrics['orders_growth']['display'] }}</span>
                    </div>
                </div>
            </div>

            <div class="metric-card-modern late-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon late-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('ACTIVE PLANS') }}</div>
                    <div class="metric-number">{{ number_format($user['total_plan']) }}</div>
                    <div class="metric-growth {{ $dashboardMetrics['plans_growth']['trend'] }}">
                        <i class="fas fa-arrow-{{ $dashboardMetrics['plans_growth']['trend'] == 'positive' ? 'up' : ($dashboardMetrics['plans_growth']['trend'] == 'negative' ? 'down' : 'right') }}"></i>
                        <span>{{ $dashboardMetrics['plans_growth']['display'] }}</span>
                    </div>
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
                        {{ !empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$' }}{{ number_format($user['total_orders_price']) }}
                    </div>
                    <div class="metric-growth {{ $dashboardMetrics['revenue_growth']['trend'] }}">
                        <i class="fas fa-arrow-{{ $dashboardMetrics['revenue_growth']['trend'] == 'positive' ? 'up' : ($dashboardMetrics['revenue_growth']['trend'] == 'negative' ? 'down' : 'right') }}"></i>
                        <span>{{ $dashboardMetrics['revenue_growth']['display'] }}</span>
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
                        <h2>{{ __('User Distribution') }}</h2>
                        <div class="widget-actions">
                            <span class="small-text">{{ __('Paid vs Free user breakdown') }}</span>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container">
                            <div id="users-chart"></div>
                        </div>
                        <div class="mini-stats">
                            <div class="mini-stat">
                                <div class="mini-stat-value">
                                    {{ number_format(($user['total_paid_user'] / max($user->total_user, 1)) * 100, 1) }}%
                                </div>
                                <div class="mini-stat-label">Conversion Rate</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value">{{ number_format($dashboardMetrics['user_retention'], 1) }}%</div>
                                <div class="mini-stat-label">User Retention</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="dashboard-row">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Orders Trend') }}</h2>
                        <div class="widget-actions">
                            <span class="small-text">{{ __('Weekly order patterns and trends') }}</span>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container">
                            <div id="orders-trend"></div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Recent Platform Activity') }}</h2>
                        <div class="widget-actions">
                            <button class="btn-icon" data-bs-toggle="tooltip" title="{{ __('More Info') }}">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="activity-list">
                            @forelse($recentActivities as $index => $activity)
                                <div class="activity-item">
                                    <div class="activity-icon {{ $activity['class'] }}">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text">{{ $activity['text'] }}</div>
                                        <div class="activity-time">{{ $activity['time'] }}</div>
                                    </div>
                                    @if($index === 0)
                                        <div class="pulse-dot"></div>
                                    @endif
                                </div>
                            @empty
                                <div class="activity-item">
                                    <div class="activity-icon users-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text">No recent activity</div>
                                        <div class="activity-time">Check back later</div>
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
@endsection
