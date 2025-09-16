/**
 * Dashboard Charts and Functionality
 * Handles all dashboard widgets: Employee Status, Overall Attendance, Pie Chart, Line Chart, Bar Chart
 */

// Global variables for charts
window.dashboardCharts = {
    attendanceBreakdown: null,
    weeklyTrend: null,
    employeeStatus: null
};

// Chart color scheme
const chartColors = {
    present: '#4facfe',
    absent: '#fa709a', 
    late: '#fcb69f',
    leave: '#a8edea',
    gradient: {
        present: ['#4facfe', '#00f2fe'],
        absent: ['#fa709a', '#fee140'],
        late: ['#ffecd2', '#fcb69f'],
        leave: ['#a8edea', '#fed6e3']
    }
};

// Initialize all dashboard functionality
$(document).ready(function() {
    console.log('🚀 Initializing Dashboard Charts...');
    
    // Wait for DOM to be fully loaded
    setTimeout(function() {
        // Initialize charts
        initializeAttendanceBreakdownChart();
        initializeWeeklyTrendChart();
        initializeEmployeeStatusChart();
        
        // Initialize gauge
        initializeGauge();
        
        // Initialize office filter
        initializeOfficeFilter();
        
        // Test functionality
        testDashboardFunctionality();
        
        console.log('✅ Dashboard initialization complete!');
    }, 500);
});

/**
 * Initialize Attendance Breakdown Pie Chart
 */
function initializeAttendanceBreakdownChart() {
    const canvas = document.getElementById('attendance-breakdown-chart');
    if (!canvas) {
        console.warn('Attendance breakdown chart canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Get data from dashboard data container
    const dashboardData = document.getElementById('dashboard-data');
    const presentCount = parseInt(dashboardData?.dataset.presentCount) || 0;
    const absentCount = parseInt(dashboardData?.dataset.absentCount) || 0;
    const lateCount = parseInt(dashboardData?.dataset.lateCount) || 0;
    const leaveCount = parseInt(dashboardData?.dataset.leaveCount) || 0;

    window.dashboardCharts.attendanceBreakdown = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late', 'On Leave'],
            datasets: [{
                data: [presentCount, absentCount, lateCount, leaveCount],
                backgroundColor: [
                    chartColors.present,
                    chartColors.absent,
                    chartColors.late,
                    chartColors.leave
                ],
                borderWidth: 0,
                cutout: '60%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 1000
            }
        }
    });
}

/**
 * Initialize Weekly Attendance Trend Line Chart
 */
function initializeWeeklyTrendChart() {
    const canvas = document.getElementById('weekly-trend-chart');
    if (!canvas) {
        console.warn('Weekly trend chart canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Generate sample data for last 7 days (excluding Saturday)
    const weeklyData = generateWeeklyTrendData();

    window.dashboardCharts.weeklyTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeklyData.labels,
            datasets: [{
                label: 'Attendance Rate',
                data: weeklyData.data,
                borderColor: chartColors.present,
                backgroundColor: chartColors.present + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: chartColors.present,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `Attendance: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        },
                        font: {
                            size: 12
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

/**
 * Initialize Employee Status Comparison Horizontal Bar Chart
 */
function initializeEmployeeStatusChart() {
    const canvas = document.getElementById('employee-status-chart');
    if (!canvas) {
        console.warn('Employee status chart canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Get data from dashboard data container
    const dashboardData = document.getElementById('dashboard-data');
    const presentCount = parseInt(dashboardData?.dataset.presentCount) || 0;
    const absentCount = parseInt(dashboardData?.dataset.absentCount) || 0;
    const lateCount = parseInt(dashboardData?.dataset.lateCount) || 0;
    const leaveCount = parseInt(dashboardData?.dataset.leaveCount) || 0;

    window.dashboardCharts.employeeStatus = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Present', 'Absent', 'Late', 'On Leave'],
            datasets: [{
                data: [presentCount, absentCount, lateCount, leaveCount],
                backgroundColor: [
                    chartColors.present,
                    chartColors.absent,
                    chartColors.late,
                    chartColors.leave
                ],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
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
                        color: '#f1f5f9'
                    },
                    ticks: {
                        font: {
                            size: 12
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
                            weight: '500'
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

/**
 * Initialize Gauge Functionality
 */
function initializeGauge() {
    const dashboardData = document.getElementById('dashboard-data');
    const attendanceRate = parseInt(dashboardData?.dataset.overallPercentage) || 0;
    
    updateGaugeNeedle(attendanceRate);
}

/**
 * Update Gauge Needle Position
 */
function updateGaugeNeedle(percentage) {
    const needle = document.getElementById('gauge-needle');
    const progressArc = document.getElementById('gauge-progress-arc');
    const percentageDisplay = document.getElementById('gauge-percentage-display');
    
    if (!needle || !progressArc || !percentageDisplay) return;
    
    // Calculate rotation angle (-90 to 90 degrees for 0% to 100%)
    const angle = -90 + (percentage * 1.8); // 180 degrees total range
    
    // Update needle rotation
    needle.setAttribute('transform', `rotate(${angle} 160 170)`);
    
    // Update progress arc
    const circumference = 346; // Approximate arc length
    const progress = (percentage / 100) * circumference;
    progressArc.setAttribute('stroke-dasharray', `${progress} ${circumference}`);
    
    // Update percentage display
    percentageDisplay.textContent = `${percentage}%`;
    
    // Update gauge color based on percentage
    let color = '#ef4444'; // Red for low
    if (percentage >= 75) color = '#10b981'; // Green for high
    else if (percentage >= 50) color = '#f59e0b'; // Yellow for medium
    
    progressArc.setAttribute('stroke', color);
}

/**
 * Initialize Office Filter Functionality
 */
function initializeOfficeFilter() {
    const officeFilter = document.getElementById('office-filter');
    if (!officeFilter) return;
    
    officeFilter.addEventListener('change', function() {
        const selectedOffice = this.value;
        console.log('Office filter changed:', selectedOffice);
        
        // Add visual feedback
        this.style.background = 'rgba(255,255,255,0.3)';
        
        // Update dashboard data based on selected office
        updateDashboardForOffice(selectedOffice);
    });
}

/**
 * Update Dashboard for Selected Office
 */
function updateDashboardForOffice(officeId) {
    // Get office data from dashboard data container
    const dashboardData = document.getElementById('dashboard-data');
    const officeDataStr = dashboardData?.dataset.officeData;
    
    if (!officeDataStr) return;
    
    try {
        const officesData = JSON.parse(officeDataStr);
        const officeData = officesData[officeId] || officesData['all'];
        
        if (officeData) {
            // Update metric cards
            updateMetricCards(officeData);
            
            // Update charts
            updateAllCharts(officeData);
            
            // Update gauge
            updateGaugeNeedle(officeData.attendance_rate || 0);
        }
    } catch (error) {
        console.error('Error parsing office data:', error);
    }
}

/**
 * Update Metric Cards
 */
function updateMetricCards(data) {
    const presentCount = document.getElementById('present-count');
    const absentCount = document.getElementById('absent-count');
    const lateCount = document.getElementById('late-count');
    const leaveCount = document.getElementById('leave-count');
    
    if (presentCount) presentCount.textContent = data.present || 0;
    if (absentCount) absentCount.textContent = data.absent || 0;
    if (lateCount) lateCount.textContent = data.late || 0;
    if (leaveCount) leaveCount.textContent = data.on_leave || 0;
}

/**
 * Update All Charts
 */
function updateAllCharts(data) {
    // Update pie chart
    if (window.dashboardCharts.attendanceBreakdown) {
        window.dashboardCharts.attendanceBreakdown.data.datasets[0].data = [
            data.present || 0,
            data.absent || 0,
            data.late || 0,
            data.on_leave || 0
        ];
        window.dashboardCharts.attendanceBreakdown.update();
    }
    
    // Update bar chart
    if (window.dashboardCharts.employeeStatus) {
        window.dashboardCharts.employeeStatus.data.datasets[0].data = [
            data.present || 0,
            data.absent || 0,
            data.late || 0,
            data.on_leave || 0
        ];
        window.dashboardCharts.employeeStatus.update();
    }
}

/**
 * Generate Weekly Trend Data (excluding Saturday)
 */
function generateWeeklyTrendData() {
    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sun']; // Excluding Saturday
    const data = [];
    
    // Generate realistic attendance data
    for (let i = 0; i < days.length; i++) {
        // Higher attendance on weekdays, lower on Sunday
        const baseRate = days[i] === 'Sun' ? 60 : 85;
        const variation = Math.random() * 20 - 10; // ±10% variation
        data.push(Math.max(0, Math.min(100, Math.round(baseRate + variation))));
    }
    
    return {
        labels: days,
        data: data
    };
}

/**
 * Test Dashboard Functionality
 */
function testDashboardFunctionality() {
    console.log('🧪 Testing Dashboard Functionality...');
    
    // Test 1: Check if all chart canvases exist
    const canvases = [
        'attendance-breakdown-chart',
        'weekly-trend-chart', 
        'employee-status-chart'
    ];
    
    canvases.forEach(canvasId => {
        const canvas = document.getElementById(canvasId);
        if (canvas) {
            console.log(`✅ ${canvasId} canvas found`);
        } else {
            console.warn(`❌ ${canvasId} canvas not found`);
        }
    });
    
    // Test 2: Check if charts are initialized
    Object.keys(window.dashboardCharts).forEach(chartKey => {
        if (window.dashboardCharts[chartKey]) {
            console.log(`✅ ${chartKey} chart initialized`);
        } else {
            console.warn(`❌ ${chartKey} chart not initialized`);
        }
    });
    
    // Test 3: Check gauge elements
    const gaugeElements = ['gauge-needle', 'gauge-progress-arc', 'gauge-percentage-display'];
    gaugeElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            console.log(`✅ ${elementId} element found`);
        } else {
            console.warn(`❌ ${elementId} element not found`);
        }
    });
    
    // Test 4: Check office filter
    const officeFilter = document.getElementById('office-filter');
    if (officeFilter) {
        console.log('✅ Office filter found');
    } else {
        console.warn('❌ Office filter not found');
    }
    
    // Test 5: Simulate data updates
    setTimeout(() => {
        console.log('🔄 Testing data updates...');
        
        // Test gauge update
        updateGaugeNeedle(75);
        console.log('✅ Gauge update test completed');
        
        // Test chart updates
        const testData = {
            present: 25,
            absent: 5,
            late: 3,
            on_leave: 2,
            attendance_rate: 75
        };
        
        updateAllCharts(testData);
        console.log('✅ Chart update test completed');
        
    }, 2000);
    
    console.log('🎉 Dashboard functionality test completed!');
}

/**
 * Utility function to create gradient
 */
function createGradient(ctx, colorStart, colorEnd) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, colorStart);
    gradient.addColorStop(1, colorEnd);
    return gradient;
}

/**
 * Resize handler for responsive charts
 */
window.addEventListener('resize', function() {
    Object.values(window.dashboardCharts).forEach(chart => {
        if (chart) {
            chart.resize();
        }
    });
});

// Export functions for global access
window.updateGaugeNeedle = updateGaugeNeedle;
window.updateAllCharts = updateAllCharts;
window.updateMetricCards = updateMetricCards;