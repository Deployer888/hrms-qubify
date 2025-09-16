@extends('layouts.admin')

@push('css-page')
    <link href="{{ asset('css/dash.css') }}" rel="stylesheet">
    <link href="{{ asset('css/extra-dash.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet">
@endpush

@section('content')
    <div class="modern-dashboard">
        <!-- Professional Welcome Header -->
        <div class="welcome-header">
            <div class="welcome-content">
                <h1 class="welcome-title">{{ __('Welcome back, Administrator') }}</h1>
                <p class="welcome-subtitle">{{ __('Monitor your HRMS platform performance and growth metrics') }}</p>
            </div>
            <div class="header-actions">
                <button class="super-admin-btn">
                    <i class="fas fa-crown"></i>
                    {{ __('SUPER ADMIN') }}
                </button>
            </div>
        </div>

        <!-- Professional Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card-modern present-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon present-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('PRESENT') }}</div>
                    <div class="metric-number">15</div>
                </div>
            </div>

            <div class="metric-card-modern absent-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon absent-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('ABSENT') }}</div>
                    <div class="metric-number">0</div>
                </div>
            </div>

            <div class="metric-card-modern late-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon late-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('LATE') }}</div>
                    <div class="metric-number">0</div>
                </div>
            </div>

            <div class="metric-card-modern leave-card">
                <div class="metric-icon-wrapper">
                    <div class="metric-icon leave-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="metric-details">
                    <div class="metric-label">{{ __('ON LEAVE') }}</div>
                    <div class="metric-number">0</div>
                </div>
            </div>
        </div>

        <!-- Enhanced Main Dashboard Content -->
        <div class="dashboard-content fade-in" style="animation-delay: 0.3s">
            <!-- First Row -->
            <div class="dashboard-row">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Attendance Rate') }}</h2>
                        <div class="widget-actions">
                            <button class="btn-icon" data-bs-toggle="tooltip" title="{{ __('More Info') }}">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="gauge-wrapper">
                            <div class="gauge-container" id="attendance-gauge">
                                <!-- Gauge chart will be inserted here -->
                            </div>
                            <div class="gauge-value">83.01%</div>
                            <div class="gauge-label">{{ __('Overall Attendance') }}</div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Employee Work Location') }}</h2>
                        <div class="widget-actions">
                            <span class="small-text">{{ __('Drill down to see shift details') }}</span>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container" id="location-breakdown-chart"></div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Leave Type Distribution') }}</h2>
                        <div class="widget-actions">
                            <button class="btn-icon" data-bs-toggle="tooltip" title="{{ __('Export Data') }}">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container" id="leave-distribution-chart"></div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="dashboard-row">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Absenteeism Rate') }}</h2>
                        <div class="widget-actions">
                            <button class="btn-icon" data-bs-toggle="tooltip" title="{{ __('More Info') }}">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="gauge-wrapper">
                            <div class="gauge-container" id="absenteeism-gauge">
                                <!-- Gauge chart will be inserted here -->
                            </div>
                            <div class="gauge-value">16.99%</div>
                            <div class="gauge-label">{{ __('Overall Absenteeism') }}</div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Sick Leave vs. Casual Leave') }}</h2>
                    </div>
                    <div class="widget-body">
                        <div class="leave-comparison">
                            <div class="leave-card">
                                <div class="leave-type">{{ __('Sick Leave') }}</div>
                                <div class="leave-value" id="sick-leave">78</div>
                                <div class="leave-percentage">51%</div>
                            </div>
                            <div class="leave-divider"></div>
                            <div class="leave-card">
                                <div class="leave-type">{{ __('Casual Leave') }}</div>
                                <div class="leave-value" id="casual-leave">76</div>
                                <div class="leave-percentage">49%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Row -->
            <div class="dashboard-row">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Absences by Month') }}</h2>
                        <div class="widget-actions">
                            <span class="small-text">{{ __('Last 6 Months') }}</span>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container" id="absences-month-chart"></div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Employees by Experience') }}</h2>
                        <div class="widget-actions">
                            <span class="small-text">{{ __('Drill down to see employees\' designation details') }}</span>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container" id="experience-chart"></div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>{{ __('Attendance by Department') }}</h2>
                        <div class="widget-actions">
                            <button class="btn-icon" data-bs-toggle="tooltip" title="{{ __('Export Data') }}">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container" id="dept-attendance-chart"></div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Office-Specific Attendance Section -->
            <div class="office-section fade-in" style="animation-delay: 0.4s">
                <h2 class="section-title">
                    <i class="fas fa-building"></i>
                    {{ __('Office-Specific Attendance') }}
                </h2>

                <div class="office-cards" id="office-cards-container">
                    <!-- Office cards will be dynamically generated here -->
                    @if (isset($offices) && count($offices) > 0)
                        @foreach ($offices as $index => $office)
                            <div class="office-card slide-in" data-office-id="{{ $office->id }}"
                                style="animation-delay: {{ $index * 0.1 + 0.5 }}s">
                                <div class="office-header">
                                    <div class="office-info">
                                        <h3>{{ $office->name }}</h3>
                                        <span class="office-employees">{{ $office->totalEmployees }}
                                            {{ __('Employees') }}</span>
                                    </div>
                                    <div class="office-actions">
                                        <button class="btn-toggle-office" data-bs-toggle="tooltip"
                                            title="{{ __('Toggle Office Details') }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="office-metrics">
                                    <div class="office-metric">
                                        <div class="metric-label">{{ __('Present') }}</div>
                                        <div class="metric-number">{{ $office->present }}</div>
                                        <div class="metric-percent">
                                            {{ number_format(($office->present / $office->totalEmployees) * 100, 1) }}%
                                        </div>
                                    </div>
                                    <div class="office-metric">
                                        <div class="metric-label">{{ __('Absent') }}</div>
                                        <div class="metric-number">{{ $office->absent }}</div>
                                        <div class="metric-percent">
                                            {{ number_format(($office->absent / $office->totalEmployees) * 100, 1) }}%
                                        </div>
                                    </div>
                                    <div class="office-metric">
                                        <div class="metric-label">{{ __('On Leave') }}</div>
                                        <div class="metric-number">{{ $office->onLeave }}</div>
                                        <div class="metric-percent">
                                            {{ number_format(($office->onLeave / $office->totalEmployees) * 100, 1) }}%
                                        </div>
                                    </div>
                                    <div class="office-metric">
                                        <div class="metric-label">{{ __('WFH') }}</div>
                                        <div class="metric-number">{{ $office->wfh }}</div>
                                        <div class="metric-percent">
                                            {{ number_format(($office->wfh / $office->totalEmployees) * 100, 1) }}%</div>
                                    </div>
                                </div>

                                <div class="office-details">
                                    <div class="office-charts">
                                        <div class="office-chart-card">
                                            <h4>{{ __('Attendance Rate') }}</h4>
                                            <div class="attendance-gauge" id="office-{{ $office->id }}-gauge"
                                                data-value="{{ $office->attendanceRate }}"></div>
                                        </div>
                                        <div class="office-chart-card">
                                            <h4>{{ __('Attendance by Department') }}</h4>
                                            <div class="dept-chart" id="office-{{ $office->id }}-dept-chart"></div>
                                        </div>
                                    </div>

                                    <table class="office-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Department') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Present') }}</th>
                                                <th>{{ __('Absent') }}</th>
                                                <th>{{ __('On Leave') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($office->departments as $dept)
                                                <tr>
                                                    <td>{{ $dept->name }}</td>
                                                    <td>{{ $dept->total }}</td>
                                                    <td class="text-success">{{ $dept->present }}
                                                        ({{ number_format(($dept->present / $dept->total) * 100, 1) }}%)
                                                    </td>
                                                    <td class="text-danger">{{ $dept->absent }}
                                                        ({{ number_format(($dept->absent / $dept->total) * 100, 1) }}%)
                                                    </td>
                                                    <td class="text-info">{{ $dept->onLeave }}
                                                        ({{ number_format(($dept->onLeave / $dept->total) * 100, 1) }}%)
                                                    </td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-success"
                                                                style="width:{{ ($dept->present / $dept->total) * 100 }}%">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Employee Geo-location Modal -->
    <div class="modal fade" id="geoLocationModal" tabindex="-1" aria-labelledby="geoLocationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="geoLocationModalLabel">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ __('Employee Locations') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="employee-map"></div>
                    <div class="remote-employees">
                        <h6><i class="fas fa-laptop-house"></i> {{ __('Employees Working Remotely') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Department') }}</th>
                                        <th>{{ __('Location') }}</th>
                                        <th>{{ __('Distance') }}</th>
                                        <th>{{ __('Last Check-in') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="remote-employees-table">
                                    <!-- Remote employees will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <!-- Required JS libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-gauge"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('js/dash.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced office card toggle functionality
            document.querySelectorAll('.btn-toggle-office').forEach(button => {
                button.addEventListener('click', function() {
                    const officeCard = this.closest('.office-card');
                    const officeDetails = officeCard.querySelector('.office-details');
                    const icon = this.querySelector('i');

                    if (officeDetails.style.display === 'block') {
                        officeDetails.style.display = 'none';
                        icon.style.transform = 'rotate(0deg)';
                        officeCard.style.transform = '';
                    } else {
                        officeDetails.style.display = 'block';
                        icon.style.transform = 'rotate(180deg)';
                        officeCard.style.transform = 'scale(1.02)';
                    }
                });
            });

            // Enhanced refresh interval functionality
            document.querySelectorAll('.refresh-option').forEach(option => {
                option.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const badge = document.getElementById('refresh-interval');

                    if (value === '0') {
                        badge.textContent = 'Off';
                    } else {
                        badge.textContent = value + 'm';
                    }

                    // Add visual feedback
                    badge.style.animation = 'pulse 0.5s ease';
                    setTimeout(() => {
                        badge.style.animation = '';
                    }, 500);
                });
            });

            // Enhanced refresh button with loading state
            document.getElementById('refresh-now').addEventListener('click', function() {
                const button = this;
                const icon = button.querySelector('i');
                const originalText = button.querySelector('span').textContent;

                // Add loading state
                button.disabled = true;
                icon.style.animation = 'spin 1s linear infinite';
                button.querySelector('span').textContent = '{{ __('Refreshing...') }}';

                // Simulate refresh (replace with actual refresh logic)
                setTimeout(() => {
                    button.disabled = false;
                    icon.style.animation = '';
                    button.querySelector('span').textContent = originalText;

                    // Add success feedback
                    button.style.background = 'rgba(16, 185, 129, 0.2)';
                    button.style.borderColor = 'rgba(16, 185, 129, 0.5)';
                    setTimeout(() => {
                        button.style.background = '';
                        button.style.borderColor = '';
                    }, 1000);
                }, 2000);
            });

            // Enhanced metric card animations
            function animateMetricCards() {
                const metricCards = document.querySelectorAll('.metric-card');
                metricCards.forEach((card, index) => {
                    card.style.animationDelay = (index * 0.1) + 's';
                    card.classList.add('fade-in');
                });
            }

            // Enhanced hover effects for interactive elements
            document.querySelectorAll('.dashboard-widget, .office-card, .metric-card').forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });

                element.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                });
            });

            // Enhanced filter change animations
            document.querySelectorAll('.form-control').forEach(control => {
                control.addEventListener('change', function() {
                    this.style.borderColor = 'var(--success)';
                    this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';

                    setTimeout(() => {
                        this.style.borderColor = '';
                        this.style.boxShadow = '';
                    }, 1000);
                });
            });

            // Initialize tooltips
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Initialize animations
            animateMetricCards();

            // Add ripple effect to buttons
            document.querySelectorAll('.btn-icon, .btn-toggle-office, #refresh-now').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255, 255, 255, 0.5)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple-animation 0.6s linear';
                    ripple.style.pointerEvents = 'none';

                    this.style.position = 'relative';
                    this.appendChild(ripple);

                    setTimeout(() => {
                        if (ripple.parentNode) {
                            ripple.remove();
                        }
                    }, 600);
                });
            });
        });

        // Add CSS for ripple effect and spin animation
        const style = document.createElement('style');
        style.textContent = `
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
        document.head.appendChild(style);
    </script>
@endpush
