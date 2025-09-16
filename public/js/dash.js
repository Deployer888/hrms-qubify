/**
 * Attendance Dashboard JavaScript - Fixed Version
 * Resolves issues with graph fluctuations on hover
 */
 
// Configure Chart.js globally ONCE with consistent settings
// Remove redundant configurations
Chart.defaults.global.animation = false;
Chart.defaults.global.hover.animationDuration = 0;
Chart.defaults.global.responsiveAnimationDuration = 0;
Chart.defaults.global.maintainAspectRatio = false;

// Standardize hover and tooltip behavior
Chart.defaults.global.hover.mode = 'nearest';
Chart.defaults.global.hover.intersect = false;
Chart.defaults.global.tooltips.mode = 'index';
Chart.defaults.global.tooltips.intersect = false;
Chart.defaults.global.tooltips.enabled = true;

// Set consistent styling parameters
Chart.defaults.global.elements.line.tension = 0.3;
Chart.defaults.global.elements.line.borderWidth = 2;
Chart.defaults.global.elements.point.radius = 3;
Chart.defaults.global.elements.point.hoverRadius = 4;

// Add this new function to prevent resize flicker
function preventChartResize() {
    // Store original resize method
    const originalResize = Chart.prototype.resize;
    
    // Override with a debounced version
    Chart.prototype.resize = function() {
        if (this._resizeTimeout) clearTimeout(this._resizeTimeout);
        
        this._resizeTimeout = setTimeout(() => {
            originalResize.apply(this, arguments);
        }, 100);
    };
}

// Fix tooltip positioning to prevent flickering
function stabilizeTooltips() {
    if (Chart.Tooltip && Chart.Tooltip.prototype) {
        const originalGetPosition = Chart.Tooltip.prototype._getPosition;
        
        Chart.Tooltip.prototype._getPosition = function(chart, points) {
            const position = originalGetPosition.call(this, chart, points);
            
            // Round position values to prevent subpixel rendering issues
            position.x = Math.round(position.x);
            position.y = Math.round(position.y);
            
            return position;
        };
    }
}

// Global chart objects to properly manage chart instances
const dashboardCharts = {
    overallGauge: null,
    weeklyTrend: null,
    attendanceBreakdown: null,
    employeeStatus: null,
    officeCharts: {}
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Apply chart stabilization fixes
    preventChartResize();
    stabilizeTooltips();
    
    // Initialize date range picker
    initDateRangePicker();
    
    // Initialize filters
    initFilters();
    
    // Initialize charts and gauges
    initCharts();
    
    // Setup auto-refresh
    setupAutoRefresh();
});

/**
 * Initialize weekly trend chart with proper data handling and stable configuration
 */
function initWeeklyTrendChart(weeklyData) {
    const chartCanvas = document.getElementById('weekly-trend-chart');
    if (!chartCanvas || !chartCanvas.getContext) return;
    
    // Destroy previous chart instance if it exists
    if (dashboardCharts.weeklyTrend) {
        dashboardCharts.weeklyTrend.destroy();
    }
    
    // Get the canvas context
    const ctx = chartCanvas.getContext('2d');
    
    // Make sure we have valid data
    if (!weeklyData || !Array.isArray(weeklyData) || weeklyData.length === 0) {
        ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillStyle = '#718096';
        ctx.fillText('No weekly trend data available', chartCanvas.width / 2, chartCanvas.height / 2);
        return;
    }
    
    // Prepare chart data
    const labels = weeklyData.map(item => item.day || '');
    const presentData = weeklyData.map(item => parseInt(item.present) || 0);
    const absentData = weeklyData.map(item => parseInt(item.absent) || 0);
    const lateData = weeklyData.map(item => parseInt(item.late) || 0);
    const leaveData = weeklyData.map(item => parseInt(item.leave) || 0);
    
    // Create chart with stable configuration
    dashboardCharts.weeklyTrend = new Chart(chartCanvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Present',
                    data: presentData,
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    fill: true
                },
                {
                    label: 'Absent',
                    data: absentData,
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    fill: true
                },
                {
                    label: 'Late',
                    data: lateData,
                    backgroundColor: 'rgba(255, 206, 86, 0.1)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                    fill: true
                },
                {
                    label: 'On Leave',
                    data: leaveData,
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 10
                }
            },
            tooltips: {
                // Use stable tooltip configuration
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        const label = data.datasets[tooltipItem.datasetIndex].label || '';
                        const value = tooltipItem.yLabel;
                        return `${label}: ${value} employees`;
                    }
                }
            },
            hover: {
                // Fixed hover configuration
                animationDuration: 0,
                mode: 'nearest',
                intersect: false
            },
            scales: {
                xAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0,
                        autoSkipPadding: 10,
                        padding: 10
                    }
                }],
                yAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true,
                        stepSize: 100,
                        precision: 0,
                        padding: 10
                    },
                    gridLines: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    }
                }]
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 20
                }
            }
        }
    });
}

/**
 * Initialize attendance breakdown chart (doughnut chart) with stable configuration
 */
function initAttendanceBreakdownChart(data) {
    const chartCanvas = document.getElementById('attendance-breakdown-chart');
    if (!chartCanvas || !chartCanvas.getContext) return;
    
    // Destroy previous chart instance if it exists
    if (dashboardCharts.attendanceBreakdown) {
        dashboardCharts.attendanceBreakdown.destroy();
    }
    
    // Prepare chart data, ensuring values are valid numbers
    const chartData = [
        parseInt(data.present) || 0,
        parseInt(data.absent) || 0,
        parseInt(data.late) || 0,
        parseInt(data.leave) || 0
    ];
    
    // Create chart with stable configuration
    dashboardCharts.attendanceBreakdown = new Chart(chartCanvas, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late', 'On Leave'],
            datasets: [{
                data: chartData,
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
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            cutoutPercentage: 70,
            layout: {
                padding: {
                    left: 0,
                    right: 0,
                    top: 15,
                    bottom: 15
                }
            },
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 20
                }
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const dataset = data.datasets[tooltipItem.datasetIndex];
                        const total = dataset.data.reduce((sum, value) => sum + value, 0);
                        const value = dataset.data[tooltipItem.index];
                        const percentage = Math.round((value / total) * 100);
                        return `${data.labels[tooltipItem.index]}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    });
}

/**
 * Initialize employee status chart (horizontal bar chart) with stable configuration
 */
function initEmployeeStatusChart(data) {
    const chartCanvas = document.getElementById('employee-status-chart');
    if (!chartCanvas || !chartCanvas.getContext) return;
    
    // Destroy previous chart instance if it exists
    if (dashboardCharts.employeeStatus) {
        dashboardCharts.employeeStatus.destroy();
    }
    
    // Calculate on-time count (present minus late)
    const onTime = Math.max(0, (parseInt(data.present) || 0) - (parseInt(data.late) || 0));
    
    // Prepare chart data, ensuring values are valid numbers
    const chartData = [
        onTime,
        parseInt(data.late) || 0,
        parseInt(data.absent) || 0,
        parseInt(data.leave) || 0
    ];
    
    // Create chart with stable configuration
    dashboardCharts.employeeStatus = new Chart(chartCanvas, {
        type: 'horizontalBar',
        data: {
            labels: ['On Time', 'Late', 'Absent', 'On Leave'],
            datasets: [{
                label: 'Number of Employees',
                data: chartData,
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
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            layout: {
                padding: {
                    left: 0,
                    right: 15,
                    top: 15,
                    bottom: 5
                }
            },
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0,
                        padding: 10
                    },
                    gridLines: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        padding: 10
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const value = tooltipItem.xLabel;
                        return `${data.labels[tooltipItem.index]}: ${value} employees`;
                    }
                }
            }
        }
    });
}

/**
 * Initialize office bar chart with stable configuration
 */
function initOfficeBarChart(officeId, officeStats) {
    const chartCanvas = document.getElementById(`office-bar-${officeId}`);
    if (!chartCanvas || !chartCanvas.getContext) return;
    
    // Destroy previous chart instance if it exists
    if (dashboardCharts.officeCharts[`bar-${officeId}`]) {
        dashboardCharts.officeCharts[`bar-${officeId}`].destroy();
    }
    
    // Prepare chart data, ensuring values are valid numbers
    const chartData = [
        parseInt(officeStats.presentCount) || 0,
        parseInt(officeStats.absentCount) || 0,
        parseInt(officeStats.lateCount) || 0
    ];
    
    // Create chart with stable configuration
    dashboardCharts.officeCharts[`bar-${officeId}`] = new Chart(chartCanvas, {
        type: 'bar',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                label: 'Employee Count',
                data: chartData,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(255, 206, 86, 0.6)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            layout: {
                padding: {
                    left: 0,
                    right: 0,
                    top: 5,
                    bottom: 5
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0,
                        maxTicksLimit: 5,
                        padding: 5,
                        fontSize: 10
                    },
                    gridLines: {
                        display: true,
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.03)'
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontSize: 10,
                        padding: 5
                    },
                    gridLines: {
                        display: false
                    }
                }]
            },
            legend: {
                display: false
            }
        }
    });
}

/**
 * Initialize office trend chart with stable configuration
 */
function initOfficeTrendChart(officeId, weeklyData) {
    const chartCanvas = document.getElementById(`office-trend-${officeId}`);
    if (!chartCanvas || !chartCanvas.getContext) return;
    
    // Destroy previous chart instance if it exists
    if (dashboardCharts.officeCharts[`trend-${officeId}`]) {
        dashboardCharts.officeCharts[`trend-${officeId}`].destroy();
    }
    
    // Get the canvas context
    const ctx = chartCanvas.getContext('2d');
    
    // Make sure we have valid data
    if (!weeklyData || !Array.isArray(weeklyData) || weeklyData.length === 0) {
        ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
        ctx.font = '12px Arial';
        ctx.textAlign = 'center';
        ctx.fillStyle = '#718096';
        ctx.fillText('No trend data available', chartCanvas.width / 2, chartCanvas.height / 2);
        return;
    }
    
    // Prepare chart data
    const labels = weeklyData.map(item => item.day || '');
    const presentData = weeklyData.map(item => parseInt(item.present) || 0);
    const absentData = weeklyData.map(item => parseInt(item.absent) || 0);
    const lateData = weeklyData.map(item => parseInt(item.late) || 0);
    
    // Create chart with stable configuration
    dashboardCharts.officeCharts[`trend-${officeId}`] = new Chart(chartCanvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Present',
                    data: presentData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: true
                },
                {
                    label: 'Absent',
                    data: absentData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: true
                },
                {
                    label: 'Late',
                    data: lateData,
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            layout: {
                padding: {
                    left: 5,
                    right: 15,
                    top: 15,
                    bottom: 10
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0,
                        maxTicksLimit: 4,
                        padding: 5,
                        fontSize: 9
                    },
                    gridLines: {
                        color: 'rgba(0, 0, 0, 0.03)',
                        drawBorder: false
                    }
                }],
                xAxes: [{
                    ticks: {
                        maxRotation: 0,
                        padding: 5,
                        fontSize: 9
                    },
                    gridLines: {
                        display: false
                    }
                }]
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    boxWidth: 8,
                    padding: 8,
                    fontSize: 9
                }
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        const label = data.datasets[tooltipItem.datasetIndex].label || '';
                        const value = tooltipItem.yLabel;
                        return `${label}: ${value}`;
                    }
                }
            }
        }
    });
}

/**
 * Window resize handling with proper debounce to prevent chart flickering
 */
let resizeTimer;
$(window).on('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        handleResponsiveLayout();
        
        // Add a small delay to resize charts to prevent flicker
        setTimeout(function() {
            resizeAllCharts();
        }, 50);
    }, 250);
});

/**
 * Force resize all charts to fix rendering
 */
function resizeAllCharts() {
    // Get all chart canvases and cache their dimensions
    const canvases = document.querySelectorAll('canvas');
    const dimensions = {};
    
    // Store original dimensions
    canvases.forEach(canvas => {
        dimensions[canvas.id] = {
            width: canvas.width,
            height: canvas.height
        };
    });
    
    // Main charts
    Object.values(dashboardCharts).forEach(chart => {
        if (chart && typeof chart.resize === 'function') {
            // Lock dimensions during resize to prevent fluctuation
            const canvas = chart.canvas;
            if (canvas && dimensions[canvas.id]) {
                canvas.style.width = dimensions[canvas.id].width + 'px';
                canvas.style.height = dimensions[canvas.id].height + 'px';
            }
            
            chart.resize();
            
            // Reset to responsive after resize
            if (canvas) {
                canvas.style.width = '';
                canvas.style.height = '';
            }
        }
    });
    
    // Office charts
    Object.values(dashboardCharts.officeCharts).forEach(chart => {
        if (chart && typeof chart.resize === 'function') {
            // Lock dimensions during resize to prevent fluctuation
            const canvas = chart.canvas;
            if (canvas && dimensions[canvas.id]) {
                canvas.style.width = dimensions[canvas.id].width + 'px';
                canvas.style.height = dimensions[canvas.id].height + 'px';
            }
            
            chart.resize();
            
            // Reset to responsive after resize
            if (canvas) {
                canvas.style.width = '';
                canvas.style.height = '';
            }
        }
    });
}

/**
 * Add this function to prevent chart hover issues
 * Override Chart.js hover handler to prevent re-rendering on small movements
 */
function preventHoverFluctuations() {
    if (Chart.Controller && Chart.Controller.prototype) {
        const originalHandleHover = Chart.Controller.prototype.handleHover;
        
        Chart.Controller.prototype.handleHover = function(e) {
            // Skip if already hovering at the same position (with small tolerance)
            if (this._lastHoverPosition &&
                Math.abs(this._lastHoverPosition.x - e.x) < 2 &&
                Math.abs(this._lastHoverPosition.y - e.y) < 2) {
                return;
            }
            
            // Store last hover position
            this._lastHoverPosition = { x: e.x, y: e.y };
            
            // Call original handler
            originalHandleHover.call(this, e);
        };
    }
}

// Apply hover fix
$(document).ready(function() {
    preventHoverFluctuations();
});