@extends('layouts.admin')
@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('content')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/company/dashboard-new.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    
    <div class="modern-dashboard">
        <div class="page-header-compact">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="header-text">
                        <h1 class="page-title-compact">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}</h1>
                        <p class="page-subtitle-compact">{{ __('Monitor your HRMS platform performance and growth metrics') }}</p>
                    </div>
                </div>
                <div class="header-right">
                    <select class="office-select" id="office-filter" name="office">
                        <option value="all" selected>All Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- HRMS Metrics Cards - Dynamic Data -->
        <div class="metrics-grid">
            <div class="metric-card-modern present-card">
                <div class="metric-icon present-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="metric-details">
                    <div class="metric-label">PRESENT</div>
                    <div class="metric-number" id="present-count">{{ $officeData['present'] ?? 0 }}</div>
                    <div class="metric-growth">{{ $officeData['present_percent'] ?? 0 }}%</div>
                </div>
            </div>

            <div class="metric-card-modern absent-card">
                <div class="metric-icon absent-icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="metric-details">
                    <div class="metric-label">ABSENT</div>
                    <div class="metric-number" id="absent-count">{{ $officeData['absent'] ?? 0 }}</div>
                    <div class="metric-growth">{{ $officeData['absent_percent'] ?? 0 }}%</div>
                </div>
            </div>

            <div class="metric-card-modern late-card">
                <div class="metric-icon late-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="metric-details">
                    <div class="metric-label">LATE</div>
                    <div class="metric-number" id="late-count">{{ $officeData['late'] ?? 0 }}</div>
                    <div class="metric-growth">{{ $officeData['late_percent'] ?? 0 }}%</div>
                </div>
            </div>

            <div class="metric-card-modern leave-card">
                <div class="metric-icon leave-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="metric-details">
                    <div class="metric-label">ON LEAVE</div>
                    <div class="metric-number" id="leave-count">{{ $officeData['on_leave'] ?? 0 }}</div>
                    <div class="metric-growth">{{ $officeData['leave_percent'] ?? 0 }}%</div>
                    
                    <!-- Leave Type Breakdown -->
                    <div class="leave-breakdown mt-2">
                        <div class="leave-type-item">
                            <span class="leave-type-label">Full:</span>
                            <span class="leave-type-count" id="full-leave-count">{{ $officeData['full_leave'] ?? 0 }}</span>
                        </div>
                        <div class="leave-type-item">
                            <span class="leave-type-label">Half:</span>
                            <span class="leave-type-count" id="half-leave-count">{{ $officeData['half_leave'] ?? 0 }}</span>
                        </div>
                        <div class="leave-type-item">
                            <span class="leave-type-label">Short:</span>
                            <span class="leave-type-count" id="short-leave-count">{{ $officeData['short_leave'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Main Dashboard Content -->
        <div class="dashboard-content">
            <!-- First Row -->
            <div class="dashboard-row">
                <div class="dashboard-widget employee-attendance-widget">
                    <div class="widget-header">
                        <h2>Employee Attendance Status</h2>
                    </div>
                    <div class="widget-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="employee-table-body">
                                    @foreach($notClockIns as $employee)
                                        <tr>
                                            <td>{{ $employee->name }}</td>
                                            <td>
                                                @php
                                                    $statusClass = 'present';
                                                    $statusText = 'Present';
                                                    
                                                    if (isset($employee['status'])) {
                                                        switch($employee['status']) {
                                                            case 'Absent':
                                                                $statusClass = 'absent';
                                                                $statusText = 'Absent';
                                                                break;
                                                            case 'Half-Day Leave':
                                                            case 'Full Day Leave':
                                                            case 'Short Leave':
                                                                $statusClass = 'leave';
                                                                $statusText = $employee['status'];
                                                                break;
                                                            case 'Present':
                                                            default:
                                                                $statusClass = 'present';
                                                                $statusText = 'Present';
                                                                break;
                                                        }
                                                    }
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a class="btn btn-primary" href="{{ route('attendanceemployee.index') }}">
                            View All Attendance
                        </a>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>Overall Attendance</h2>
                    </div>
                    <div class="widget-body">
                        <div class="gauge-wrapper">
                            <div class="speedometer-gauge" id="attendance-speedometer">
                                <svg viewBox="0 0 320 230" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Background arc (full semicircle) -->
                                    <path class="gauge-background" d="M 50 170 A 110 110 0 0 1 270 170" stroke="#f1f5f9"
                                        stroke-width="25" fill="none" stroke-linecap="round" />

                                    <!-- Progress arc (dynamic based on percentage) -->
                                    <path class="gauge-progress" id="gauge-progress-arc"
                                        d="M 50 170 A 110 110 0 0 1 270 170" stroke="#3b82f6" stroke-width="25"
                                        fill="none" stroke-linecap="round" stroke-dasharray="0 346"
                                        stroke-dashoffset="0" />

                                    <!-- Needle -->
                                    <line class="gauge-needle" id="gauge-needle" x1="160" y1="170"
                                        x2="160" y2="80" stroke="#374151" stroke-width="3"
                                        stroke-linecap="round" transform="rotate(-90 160 170)" />

                                    <!-- Center circle -->
                                    <circle class="gauge-center" cx="160" cy="170" r="8" fill="#374151" />

                                    <!-- Scale markers and labels -->
                                    <g class="gauge-markers">
                                        <!-- 0% marker -->
                                        <line stroke="#9ca3af" stroke-width="2" x2="40" x1="60"
                                            y2="170" y1="170"></line>
                                        <text class="gauge-text" x="20" y="175">0%</text>

                                        <!-- 25% marker -->
                                        <line stroke="#9ca3af" stroke-width="2" y1="92" x2="85"
                                            y2="104" x1="68"></line>
                                        <text class="gauge-text" y="90" x="50">25%</text>

                                        <!-- 50% marker -->
                                        <line x1="160" y2="70" stroke="#9ca3af" stroke-width="2"
                                            x2="160" y1="50"></line>
                                        <text class="gauge-text" y="40" x="160">50%</text>

                                        <!-- 75% marker -->
                                        <line stroke="#9ca3af" stroke-width="2" y2="100" x2="230"
                                            y1="87" x1="248"></line>
                                        <text class="gauge-text" y="80" x="265">75%</text>

                                        <!-- 100% marker -->
                                        <line stroke="#9ca3af" stroke-width="2" y2="170" y1="170"
                                            x1="260" x2="280"></line>
                                        <text class="gauge-text" x="300" y="175">100%</text>
                                    </g>

                                    <!-- Percentage display inside gauge -->
                                    <text x="160" y="205" class="gauge-percentage-text" id="gauge-percentage-display">
                                        {{ $officeData['attendance_rate'] ?? 0 }}%
                                    </text>

                                    <!-- Label below percentage -->
                                    <text x="160" y="225" class="gauge-label-text">
                                        Attendance Rate
                                    </text>
                                </svg>
                            </div>
                            <div class="gauge-details">
                                <span id="present-employees">{{ $officeData['present'] ?? 0 }}</span> /
                                <span id="total-expected">{{ $officeData['total'] ?? 0 }}</span> Present
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h2>Attendance Breakdown</h2>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container" id="attendance-breakdown-chart-container">
                            <canvas id="attendance-breakdown-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="office-grid">
                <div class="office-grid-card">
                    <div class="widget-header">
                        <h2>Weekly Attendance Trend</h2>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container">
                            <canvas id="weekly-trend-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="office-grid-card">
                    <div class="widget-header">
                        <h2>Employee Status Comparison</h2>
                    </div>
                    <div class="widget-body">
                        <div class="chart-container">
                            <canvas id="employee-status-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Office-wise Analytics Grid Section -->
        <div class="office-analytics-section">
            <div class="section-header">
                <h2>Office-wise Analytics</h2>
            </div>

            <div class="office-grid" id="office-cards">
                @foreach($officesData as $officeId => $office)
                    <div class="office-grid-card" data-office-id="{{ $officeId }}">
                        <div class="office-card-header">
                            <h3>{{ $office['name'] }}</h3>
                            <div class="office-attendance-rate">
                                <span class="rate-value">{{ $office['attendance_rate'] }}%</span>
                                <span class="rate-label">Attendance</span>
                            </div>
                        </div>
                        <hr style="border-top: 1px solid #1e1e1f;">
                        <div class="office-metrics-grid">
                            <div class="office-metric-item present">
                                <div class="metric-icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="metric-info">
                                    <span class="metric-value">{{ $office['present'] }}</span>
                                    <span class="metric-label">Present</span>
                                </div>
                            </div>

                            <div class="office-metric-item absent">
                                <div class="metric-icon">
                                    <i class="fas fa-user-times"></i>
                                </div>
                                <div class="metric-info">
                                    <span class="metric-value">{{ $office['absent'] }}</span>
                                    <span class="metric-label">Absent</span>
                                </div>
                            </div>

                            <div class="office-metric-item late">
                                <div class="metric-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="metric-info">
                                    <span class="metric-value">{{ $office['late'] }}</span>
                                    <span class="metric-label">Late</span>
                                </div>
                            </div>

                            <div class="office-metric-item leave">
                                <div class="metric-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="metric-info">
                                    <span class="metric-value">{{ $office['on_leave'] }}</span>
                                    <span class="metric-label">On Leave</span>
                                </div>
                            </div>
                        </div>

                        <div class="office-card-footer">
                            <a href="{{ route('office.one.index', $office['id']) }}" class="office-view-btn">
                                View Details
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Data Container for JavaScript -->
    <div id="dashboard-data" style="display: none;" 
         data-overall-percentage="{{ $officeData['attendance_rate'] ?? 0 }}"
         data-present-count="{{ $officeData['present'] ?? 0 }}" 
         data-absent-count="{{ $officeData['absent'] ?? 0 }}"
         data-late-count="{{ $officeData['late'] ?? 0 }}" 
         data-leave-count="{{ $officeData['on_leave'] ?? 0 }}"
         data-full-leave-count="{{ $officeData['full_leave'] ?? 0 }}"
         data-half-leave-count="{{ $officeData['half_leave'] ?? 0 }}"
         data-short-leave-count="{{ $officeData['short_leave'] ?? 0 }}"
         data-total-employees="{{ $officeData['total'] ?? 0 }}"
         data-offices='@json($officesData)'
         data-weekly-trend='@json($weeklyTrendData)'
         data-employees='@json($notClockIns)'>
    </div>

    <script>
        // Get dynamic data from controller
        const dashboardDataElement = document.getElementById('dashboard-data');
        const dynamicData = {
            offices: JSON.parse(dashboardDataElement.dataset.offices || '{}'),
            weeklyTrend: JSON.parse(dashboardDataElement.dataset.weeklyTrend || '[]'),
            employees: JSON.parse(dashboardDataElement.dataset.employees || '[]'),
            overallPercentage: parseInt(dashboardDataElement.dataset.overallPercentage || '0'),
            presentCount: parseInt(dashboardDataElement.dataset.presentCount || '0'),
            absentCount: parseInt(dashboardDataElement.dataset.absentCount || '0'),
            lateCount: parseInt(dashboardDataElement.dataset.lateCount || '0'),
            leaveCount: parseInt(dashboardDataElement.dataset.leaveCount || '0'),
            totalEmployees: parseInt(dashboardDataElement.dataset.totalEmployees || '0')
        };

        // Fallback data if no weekly trend data is available
        if (dynamicData.weeklyTrend.length === 0) {
            dynamicData.weeklyTrend = [
                { day: 'Mon', present: 0, absent: 0, late: 0, leave: 0 },
                { day: 'Tue', present: 0, absent: 0, late: 0, leave: 0 },
                { day: 'Wed', present: 0, absent: 0, late: 0, leave: 0 },
                { day: 'Thu', present: 0, absent: 0, late: 0, leave: 0 },
                { day: 'Fri', present: 0, absent: 0, late: 0, leave: 0 }
            ];
        }

        // Chart instances
        let dashboardCharts = {
            weeklyTrend: null,
            attendanceBreakdown: null,
            employeeStatus: null
        };

        // Initialize Speedometer Gauge Animation
        function initSpeedometerGauge(attendanceRate = 78) {
            const progressArc = document.getElementById('gauge-progress-arc');
            const needle = document.getElementById('gauge-needle');
            const percentageDisplay = document.getElementById('gauge-percentage-display');

            if (progressArc && needle && percentageDisplay) {
                // Reset initial states
                progressArc.style.strokeDasharray = '0 346';
                needle.style.transform = 'rotate(-90deg)';
                needle.style.transformOrigin = '160px 170px';

                // Calculate the circumference of the semicircle
                const radius = 110;
                const circumference = Math.PI * radius;
                const progressLength = (attendanceRate / 100) * circumference;
                const remainingLength = circumference - progressLength;

                // Determine color class
                let colorClass = 'good';
                if (attendanceRate >= 90) colorClass = 'excellent';
                else if (attendanceRate >= 75) colorClass = 'good';
                else if (attendanceRate >= 60) colorClass = 'warning';
                else colorClass = 'poor';

                // Apply color classes
                progressArc.className = `gauge-progress ${colorClass}`;
                percentageDisplay.className = `gauge-percentage-text ${colorClass}`;

                // Calculate needle rotation
                const needleRotation = -90 + (attendanceRate / 100) * 180;

                // Animate with smooth easing
                const animationDuration = 2500;
                const startTime = performance.now();

                function animateStep(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / animationDuration, 1);
                    const easedProgress = 1 - Math.pow(1 - progress, 3);

                    const currentProgress = progressLength * easedProgress;
                    const currentNeedleRotation = -90 + ((needleRotation - (-90)) * easedProgress);
                    const currentRemaining = circumference - currentProgress;

                    progressArc.style.strokeDasharray = `${currentProgress} ${currentRemaining}`;
                    needle.style.transform = `rotate(${currentNeedleRotation}deg)`;

                    if (progress < 1) {
                        requestAnimationFrame(animateStep);
                    } else if (attendanceRate >= 90) {
                        setTimeout(() => {
                            progressArc.style.animation = 'pulse-glow 2s ease-in-out infinite alternate';
                        }, 500);
                    }
                }

                // Update percentage display
                percentageDisplay.textContent = attendanceRate + '%';
                requestAnimationFrame(animateStep);
            }
        }

        // Initialize Weekly Trend Chart
        function initWeeklyTrendChart() {
            const canvas = document.getElementById('weekly-trend-chart');
            if (!canvas) return;

            if (dashboardCharts.weeklyTrend) {
                dashboardCharts.weeklyTrend.destroy();
            }

            const ctx = canvas.getContext('2d');
            const labels = dynamicData.weeklyTrend.map(item => item.day);
            const presentData = dynamicData.weeklyTrend.map(item => item.present);
            const absentData = dynamicData.weeklyTrend.map(item => item.absent);
            const lateData = dynamicData.weeklyTrend.map(item => item.late);
            const leaveData = dynamicData.weeklyTrend.map(item => item.leave);

            dashboardCharts.weeklyTrend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Present',
                            data: presentData,
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)'
                        },
                        {
                            label: 'Absent',
                            data: absentData,
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)'
                        },
                        {
                            label: 'Late',
                            data: lateData,
                            backgroundColor: 'rgba(255, 206, 86, 0.1)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(255, 206, 86, 1)'
                        },
                        {
                            label: 'On Leave',
                            data: leaveData,
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y} employees`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        y: {
                            display: true,
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize Attendance Breakdown Chart
        function initAttendanceBreakdownChart() {
            const canvas = document.getElementById('attendance-breakdown-chart');
            if (!canvas) return;

            if (dashboardCharts.attendanceBreakdown) {
                dashboardCharts.attendanceBreakdown.destroy();
            }

            const ctx = canvas.getContext('2d');

            dashboardCharts.attendanceBreakdown = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Late', 'On Leave'],
                    datasets: [{
                        data: [dynamicData.presentCount, dynamicData.absentCount, dynamicData.lateCount, dynamicData.leaveCount],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 2,
                        hoverBorderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                    const value = context.dataset.data[context.dataIndex];
                                    const percentage = Math.round((value / total) * 100);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize Employee Status Chart
        function initEmployeeStatusChart() {
            const canvas = document.getElementById('employee-status-chart');
            if (!canvas) return;

            if (dashboardCharts.employeeStatus) {
                dashboardCharts.employeeStatus.destroy();
            }

            const ctx = canvas.getContext('2d');
            const onTime = dynamicData.presentCount - dynamicData.lateCount; // Present minus late

            dashboardCharts.employeeStatus = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['On Time', 'Late', 'Absent', 'On Leave'],
                    datasets: [{
                        label: 'Number of Employees',
                        data: [onTime, dynamicData.lateCount, dynamicData.absentCount, dynamicData.leaveCount],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.parsed.x} employees`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update metrics for office filter with AJAX call
        function updateMetricsForOffice(officeId) {
            // Show loading state
            showLoadingState();
            
            // Make AJAX call to get filtered data
            fetch('/dash-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    office: officeId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardMetrics(data.data);
                } else {
                    console.error('Error fetching filtered data:', data.error);
                    // Fallback to static data
                    updateMetricsFromStaticData(officeId);
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                // Fallback to static data
                updateMetricsFromStaticData(officeId);
            })
            .finally(() => {
                hideLoadingState();
            });
        }

        // Update dashboard metrics with new data
        function updateDashboardMetrics(data) {
            // Update main metric cards
            document.getElementById('present-count').textContent = data.present;
            document.getElementById('absent-count').textContent = data.absent;
            document.getElementById('late-count').textContent = data.late;
            document.getElementById('leave-count').textContent = data.on_leave;

            // Update leave breakdown
            document.getElementById('full-leave-count').textContent = data.full_leave;
            document.getElementById('half-leave-count').textContent = data.half_leave;
            document.getElementById('short-leave-count').textContent = data.short_leave;

            // Update percentages
            const presentPercent = document.querySelector('.present-card .metric-growth');
            const absentPercent = document.querySelector('.absent-card .metric-growth');
            const latePercent = document.querySelector('.late-card .metric-growth');
            const leavePercent = document.querySelector('.leave-card .metric-growth');

            if (presentPercent) presentPercent.textContent = data.present_percent + '%';
            if (absentPercent) absentPercent.textContent = data.absent_percent + '%';
            if (latePercent) latePercent.textContent = data.late_percent + '%';
            if (leavePercent) leavePercent.textContent = data.leave_percent + '%';

            // Update charts if they exist
            const attendanceRate = data.total > 0 ? Math.round((data.present / data.total) * 100) : 0;
            initSpeedometerGauge(attendanceRate);
            updateAttendanceBreakdownChart(data);
            updateEmployeeStatusChart(data);
        }

        // Fallback to static data if AJAX fails
        function updateMetricsFromStaticData(officeId) {
            if (officeId === 'all') {
                // Show totals for all offices
                const totals = Object.values(dynamicData.offices).reduce((acc, office) => {
                    acc.present += office.present;
                    acc.absent += office.absent;
                    acc.late += office.late;
                    acc.leave += office.on_leave;
                    acc.full_leave += office.full_leave || 0;
                    acc.half_leave += office.half_leave || 0;
                    acc.short_leave += office.short_leave || 0;
                    acc.total += office.total;
                    return acc;
                }, { present: 0, absent: 0, late: 0, leave: 0, full_leave: 0, half_leave: 0, short_leave: 0, total: 0 });

                updateDashboardMetrics(totals);
            } else {
                // Show data for specific office
                const office = dynamicData.offices[officeId];
                if (office) {
                    updateDashboardMetrics({
                        present: office.present,
                        absent: office.absent,
                        late: office.late,
                        on_leave: office.on_leave,
                        full_leave: office.full_leave || 0,
                        half_leave: office.half_leave || 0,
                        short_leave: office.short_leave || 0,
                        total: office.total
                    });
                }
            }
        }

        // Show loading state
        function showLoadingState() {
            const metricCards = document.querySelectorAll('.metric-card-modern');
            metricCards.forEach(card => {
                card.style.opacity = '0.6';
                card.style.pointerEvents = 'none';
            });
        }

        // Hide loading state
        function hideLoadingState() {
            const metricCards = document.querySelectorAll('.metric-card-modern');
            metricCards.forEach(card => {
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            });
        }

        // Update charts with new data
        function updateAttendanceBreakdownChart(data) {
            if (dashboardCharts.attendanceBreakdown) {
                dashboardCharts.attendanceBreakdown.data.datasets[0].data = [
                    data.present, data.absent, data.late, data.leave
                ];
                dashboardCharts.attendanceBreakdown.update();
            }
        }

        function updateEmployeeStatusChart(data) {
            if (dashboardCharts.employeeStatus) {
                const onTime = data.present - data.late;
                dashboardCharts.employeeStatus.data.datasets[0].data = [
                    onTime, data.late, data.absent, data.leave
                ];
                dashboardCharts.employeeStatus.update();
            }
        }

        // Office filter functionality
        document.getElementById('office-filter').addEventListener('change', function() {
            const selectedOffice = this.value;
            updateMetricsForOffice(selectedOffice);

            // Visual feedback
            this.style.transform = 'scale(1.05)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all charts with dynamic data
            initSpeedometerGauge(dynamicData.overallPercentage);
            initWeeklyTrendChart();
            initAttendanceBreakdownChart();
            initEmployeeStatusChart();

            // Add staggered animation to cards
            const cards = document.querySelectorAll('.metric-card-modern, .dashboard-widget, .office-grid-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });

            console.log('HRMS Dashboard initialized successfully!');
        });

        // Add smooth hover effects
        document.querySelectorAll('.metric-card-modern, .dashboard-widget, .office-grid-card').forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            element.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Prevent chart flickering on window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (dashboardCharts.weeklyTrend) dashboardCharts.weeklyTrend.resize();
                if (dashboardCharts.attendanceBreakdown) dashboardCharts.attendanceBreakdown.resize();
                if (dashboardCharts.employeeStatus) dashboardCharts.employeeStatus.resize();
            }, 100);
        });
    </script>
@endsection
